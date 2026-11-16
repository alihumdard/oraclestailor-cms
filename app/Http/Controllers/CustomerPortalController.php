<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\MeasurementHistory;
use App\Models\TailorOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CustomerPortalController extends Controller
{
    public function dashboard(): View
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        return view('customer.dashboard', [
            'customer' => $customer,
            'orders' => $customer->orders()->latest()->get(),
        ]);
    }

    public function createOrder(): View
    {
        $customer = Auth::guard('customer')->user();

        return view('customer.order-create', [
            'customer' => $customer,
            'garments' => TailorOrder::GARMENTS,
            'packages' => TailorOrder::PACKAGES,
            'fabrics' => TailorOrder::FABRICS,
            'shirtFields' => TailorOrder::SHIRT_FIELDS,
            'trouserFields' => TailorOrder::TROUSER_FIELDS,
            'vestFields' => TailorOrder::VEST_FIELDS,
            'inchOptions' => TailorOrder::inchOptions(),
            'savedMeasurements' => $customer->measurements ?? [],
        ]);
    }

    public function storeOrder(Request $request): RedirectResponse
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        $data = $request->validate([
            'garment' => ['required', Rule::in(TailorOrder::GARMENTS)],
            'package' => ['required', Rule::in(array_keys(TailorOrder::PACKAGES))],
            'fabric' => ['required', Rule::in(TailorOrder::FABRICS)],
            'standard_size' => ['nullable', Rule::in(['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', 'custom'])],
            'measurements' => ['nullable', 'array'],
            'measurements.*' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'style_notes' => ['nullable', 'string', 'max:1000'],
            'due_date' => ['required', 'date', 'after_or_equal:today', 'before_or_equal:'.today()->addMonths(18)->toDateString()],
        ]);

        $measurements = collect($data['measurements'] ?? [])
            ->filter(fn ($value): bool => $value !== null && $value !== '')
            ->map(fn ($value): float => (float) $value)
            ->all();

        if (empty($measurements) && empty($data['standard_size'])) {
            throw ValidationException::withMessages([
                'standard_size' => 'Please choose a size or enter at least one measurement.',
            ]);
        }

        if ($measurements) {
            $customer->fill([
                'measurements' => $measurements,
                'measurement_unit' => 'in',
                'measured_at' => today(),
            ])->save();
        }

        $order = $customer->orders()->create([
            'garment' => $data['garment'],
            'package' => $data['package'],
            'fabric' => $data['fabric'],
            'standard_size' => $data['standard_size'] ?? null,
            'measurement_unit' => 'in',
            'measurements' => $measurements,
            'style_notes' => $data['style_notes'] ?? null,
            'status' => 'new',
            'due_date' => $data['due_date'],
            'total' => 0,
            'advance' => 0,
        ]);

        if ($measurements) {
            $customer->measurementHistories()->create([
                'tailor_order_id' => $order->id,
                'measurements' => $measurements,
                'measurement_unit' => 'in',
                'measurement_notes' => $data['style_notes'] ?? null,
                'measured_at' => today(),
            ]);
        }

        return redirect()->route('customer.dashboard')
            ->with('success', 'Your order SAIM-'.str_pad((string) $order->id, 5, '0', STR_PAD_LEFT).' has been sent to Saim.');
    }
}
