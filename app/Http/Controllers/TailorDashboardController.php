<?php

namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use App\Models\Customer;
use App\Models\ContactInquiry;
use App\Models\MeasurementHistory;
use App\Models\TailorOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TailorDashboardController extends Controller
{
    public function index(): View
    {
        $businessSetting = BusinessSetting::current();
        $eidReminders = $this->seasonalEidReminders($businessSetting);

        return view('tailor.dashboard', [
            'orders' => TailorOrder::with(['customer', 'assignedTo'])->latest()->take(8)->get(),
            'staff' => \App\Models\User::orderBy('name')->get(),
            'customers' => Customer::withCount('orders')->with('measurementHistories')->latest()->take(6)->get(),
            'customersForSelect' => Customer::orderBy('name')->get(['id', 'name', 'phone', 'email', 'measurements', 'measurement_unit']),
            'inquiries' => ContactInquiry::latest()->take(5)->get(),
            'dueSoonOrders' => TailorOrder::with('customer')
                ->whereNotIn('status', ['delivered', 'cancelled'])
                ->whereBetween('due_date', [today(), today()->addDays(2)])
                ->orderBy('due_date')
                ->get(),
            'eidReminders' => $eidReminders,
            'businessSetting' => $businessSetting,
            'stats' => [
                'active' => TailorOrder::whereNotIn('status', ['delivered', 'cancelled'])->count(),
                'customers' => Customer::count(),
                'due' => TailorOrder::whereNotIn('status', ['delivered', 'cancelled'])
                    ->selectRaw('COALESCE(SUM(total - advance), 0) as amount')
                    ->value('amount'),
                'ready' => TailorOrder::where('status', 'ready')->count(),
            ],
        ]);
    }

    private function seasonalEidReminders(BusinessSetting $businessSetting): \Illuminate\Support\Collection
    {
        $eidDate = $businessSetting->next_eid_date;

        if (! $eidDate || today()->diffInDays($eidDate, false) < 0 || today()->diffInDays($eidDate, false) > 30) {
            return collect();
        }

        $windowStart = $eidDate->copy()->subYear()->subDays(20);
        $windowEnd = $eidDate->copy()->subYear()->addDays(20);

        return Customer::whereHas('orders', function ($query) use ($windowStart, $windowEnd) {
            $query->whereBetween('due_date', [$windowStart, $windowEnd]);
        })->get();
    }

    public function board(): View
    {
        $orders = TailorOrder::with('customer')
            ->whereNotIn('status', ['cancelled'])
            ->orderBy('due_date')
            ->get()
            ->groupBy('status');

        return view('tailor.board', ['ordersByStatus' => $orders]);
    }

    public function booking(): View
    {
        return view('tailor.booking', [
            'statusLabels' => TailorOrder::STATUS_LABELS,
            'authCustomer' => auth('customer')->user(),
            'businessSetting' => BusinessSetting::current(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['required_without:customer_id', 'nullable', 'string', 'max:120'],
            'phone' => ['required_without:customer_id', 'nullable', 'string', 'max:30'],
            'customer_id' => ['required_without:phone', 'nullable', 'integer', 'exists:customers,id'],
            'email' => ['nullable', 'email', 'max:160'],
            'gender' => ['required_without:customer_id', 'nullable', Rule::in(array_keys(TailorOrder::GENDERS))],
            'garment' => ['required', Rule::in(TailorOrder::GARMENTS)],
            'package' => ['required', Rule::in(array_keys(TailorOrder::PACKAGES))],
            'fabric' => ['required', Rule::in(TailorOrder::FABRICS)],
            'standard_size' => ['nullable', Rule::in(['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', 'custom'])],
            'measurement_unit' => ['required', Rule::in(['in', 'cm'])],
            'measurements' => ['nullable', 'array'],
            'measurements.*' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'style_notes' => ['nullable', 'string', 'max:1000'],
            'due_date' => ['required', 'date', 'after_or_equal:today', 'before_or_equal:'.today()->addMonths(18)->toDateString()],
            'total' => ['required', 'integer', 'min:0', 'max:10000000'],
            'advance' => ['required', 'integer', 'min:0', 'max:10000000'],
        ]);

        $measurements = collect($data['measurements'] ?? [])
            ->filter(fn ($value): bool => $value !== null && $value !== '')
            ->map(fn ($value): float => (float) $value)
            ->all();

        if (empty($measurements) && empty($data['standard_size'])) {
            throw ValidationException::withMessages([
                'standard_size' => 'Please choose a standard size or enter at least one measurement.',
            ]);
        }

        $total = (int) $data['total'];
        $advance = (int) $data['advance'];
        $customerId = $data['customer_id'] ?? null;

        if ($advance > $total) {
            throw ValidationException::withMessages([
                'advance' => 'The advance cannot be more than the total price.',
            ]);
        }

        $order = DB::transaction(function () use ($data, $measurements, $total, $advance, $customerId): TailorOrder {
            $customer = $customerId
                ? Customer::findOrFail($customerId)
                : Customer::firstOrNew(['phone' => trim($data['phone'])]);

            if (! empty($data['customer_name'])) {
                $customer->name = trim($data['customer_name']);
            }

            if (! empty($data['email']) || ! $customer->exists) {
                $customer->email = $data['email'] ?? null;
            }

            if (! empty($data['gender'])) {
                $customer->gender = $data['gender'];
            }

            if ($measurements) {
                $customer->fill([
                    'measurements' => $measurements,
                    'measurement_unit' => $data['measurement_unit'],
                    'measurement_notes' => $data['style_notes'] ?? null,
                    'measured_at' => today(),
                ]);
            }

            $customer->save();

            $order = $customer->orders()->create([
                'garment' => $data['garment'],
                'package' => $data['package'],
                'fabric' => $data['fabric'],
                'standard_size' => $data['standard_size'] ?? null,
                'measurement_unit' => $data['measurement_unit'],
                'measurements' => $measurements,
                'style_notes' => $data['style_notes'] ?? null,
                'status' => 'confirmed',
                'due_date' => $data['due_date'],
                'total' => $total,
                'advance' => $advance,
            ]);

            if ($measurements) {
                $customer->measurementHistories()->create([
                    'tailor_order_id' => $order->id,
                    'measurements' => $measurements,
                    'measurement_unit' => $data['measurement_unit'],
                    'measurement_notes' => $data['style_notes'] ?? null,
                    'measured_at' => today(),
                ]);
            }

            return $order;
        });

        return redirect()->route('dashboard', [], 303)
            ->with('success', 'Order SAIM-'.str_pad((string) $order->id, 5, '0', STR_PAD_LEFT).' has been added.');
    }

    public function settings(): View
    {
        return view('tailor.settings', [
            'setting' => BusinessSetting::current(),
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'business_name' => ['required', 'string', 'max:160'],
            'tagline' => ['nullable', 'string', 'max:160'],
            'address_line' => ['nullable', 'string', 'max:160'],
            'city' => ['nullable', 'string', 'max:100'],
            'region' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:160'],
            'hours' => ['nullable', 'string', 'max:160'],
            'next_eid_date' => ['nullable', 'date'],
        ]);

        BusinessSetting::current()->update($data);

        return back()->with('success', 'Business details updated.');
    }

    public function trackOrder(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'order_reference' => ['required', 'string', 'regex:/^SAIM-\d{5,}$/i', 'max:20'],
            'phone' => ['required', 'string', 'max:30'],
        ]);

        $orderId = (int) substr($data['order_reference'], 5);
        $order = TailorOrder::with('customer')
            ->whereKey($orderId)
            ->whereHas('customer', fn ($query) => $query->where('phone', trim($data['phone'])))
            ->first();

        if (! $order) {
            return redirect()->route('home', [], 303)
                ->with('tracking_error', 'We could not find that order. Check your reference and phone number.');
        }

        return redirect()->route('home', [], 303)
            ->with('tracked_order', [
                'reference' => 'SAIM-'.str_pad((string) $order->id, 5, '0', STR_PAD_LEFT),
                'garment' => $order->garment,
                'status' => $order->status,
                'status_label' => TailorOrder::STATUS_LABELS[$order->status],
                'due_date' => $order->due_date->format('d M Y'),
            ]);
    }

    public function storeInquiry(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:1500'],
        ]);

        ContactInquiry::create($data);

        return redirect()->route('home', [], 303)
            ->with('inquiry_sent', true);
    }

    public function updateInquiry(Request $request, ContactInquiry $inquiry): RedirectResponse
    {
        $inquiry->update($request->validate([
            'status' => ['required', Rule::in(['new', 'handled'])],
        ]));

        return back()->with('success', 'Message marked as '.($inquiry->status === 'handled' ? 'handled.' : 'new.'));
    }

    public function destroyInquiry(ContactInquiry $inquiry): RedirectResponse
    {
        $inquiry->delete();

        return back()->with('success', 'Message deleted.');
    }

    public function reports(): View
    {
        $monthStart = today()->startOfMonth();

        $monthlyEarnings = TailorOrder::where('status', 'delivered')
            ->where('updated_at', '>=', $monthStart)
            ->sum('total');

        $completedThisMonth = TailorOrder::where('status', 'delivered')
            ->where('updated_at', '>=', $monthStart)
            ->count();

        $statusCounts = TailorOrder::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('tailor.reports', [
            'monthlyEarnings' => $monthlyEarnings,
            'completedThisMonth' => $completedThisMonth,
            'statusCounts' => $statusCounts,
        ]);
    }

    public function receipt(TailorOrder $order): View
    {
        return view('tailor.receipt', [
            'order' => $order->load('customer'),
            'businessSetting' => BusinessSetting::current(),
        ]);
    }

    public function updatePrice(Request $request, TailorOrder $order): RedirectResponse
    {
        $data = $request->validate([
            'total' => ['required', 'integer', 'min:0', 'max:10000000'],
            'advance' => ['required', 'integer', 'min:0', 'max:10000000'],
        ]);

        if ($data['advance'] > $data['total']) {
            throw ValidationException::withMessages([
                'advance' => 'The advance cannot be more than the agreed total.',
            ]);
        }

        $order->update($data);

        return back()->with('success', 'Agreed price and remaining balance updated.');
    }

    public function updateStatus(Request $request, TailorOrder $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(TailorOrder::STATUSES)],
        ]);

        $order->update(['status' => $data['status']]);

        return back()->with('success', 'Order status updated to '.TailorOrder::STATUS_LABELS[$data['status']].'.');
    }

    public function assign(Request $request, TailorOrder $order): RedirectResponse
    {
        $data = $request->validate([
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $order->update(['assigned_to' => $data['assigned_to'] ?? null]);

        return back()->with('success', 'Order assignment updated.');
    }

    public function bulkUpdateStatus(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'order_ids' => ['required', 'array', 'min:1'],
            'order_ids.*' => ['integer', 'exists:tailor_orders,id'],
            'status' => ['required', Rule::in(TailorOrder::STATUSES)],
        ]);

        $count = TailorOrder::whereIn('id', $data['order_ids'])->update(['status' => $data['status']]);

        return back()->with('success', $count.' '.Str::plural('order', $count).' updated to '.TailorOrder::STATUS_LABELS[$data['status']].'.');
    }
}
