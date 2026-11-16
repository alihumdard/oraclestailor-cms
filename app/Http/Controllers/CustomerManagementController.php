<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerManagementController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $customers = Customer::withCount('orders')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        if ($request->ajax()) {
            return view('tailor.partials.customers-table', ['customers' => $customers]);
        }

        return view('tailor.customers', [
            'customers' => $customers,
            'search' => $search,
        ]);
    }

    public function updateMeasurements(Request $request, Customer $customer): RedirectResponse
    {
        $data = $request->validate([
            'measurement_unit' => ['required', Rule::in(['in', 'cm'])],
            'measurements' => ['nullable', 'array'],
            'measurements.*' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'measurement_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $measurements = collect($data['measurements'] ?? [])
            ->filter(fn ($value): bool => $value !== null && $value !== '')
            ->map(fn ($value): float => (float) $value)
            ->all();

        $customer->fill([
            'measurements' => $measurements,
            'measurement_unit' => $data['measurement_unit'],
            'measurement_notes' => $data['measurement_notes'] ?? null,
            'measured_at' => today(),
        ])->save();

        if ($measurements) {
            $customer->measurementHistories()->create([
                'measurements' => $measurements,
                'measurement_unit' => $data['measurement_unit'],
                'measurement_notes' => $data['measurement_notes'] ?? null,
                'measured_at' => today(),
            ]);
        }

        return back()->with('success', "Saved {$customer->name}'s measurements.");
    }
}
