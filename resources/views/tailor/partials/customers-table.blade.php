<div class="content-panel orders-panel">
    <div class="panel-topline"><h2>{{ $customers->total() }} {{ Str::plural('customer', $customers->total()) }}</h2></div>
    <div class="table-scroll"><table class="data-table"><thead><tr><th>NAME</th><th>PHONE</th><th>EMAIL</th><th>ORDERS</th><th>SAVED FIT</th><th></th></tr></thead><tbody>
        @forelse ($customers as $customer)
            <tr>
                <td><span class="client-cell"><span class="client-avatar avatar-{{ ($customer->id % 5) + 1 }}">{{ mb_strtoupper(mb_substr($customer->name, 0, 1)) }}</span><strong>{{ $customer->name }}</strong></span></td>
                <td>{{ $customer->phone }}</td>
                <td>{{ $customer->email ?: '—' }}</td>
                <td>{{ $customer->orders_count }}</td>
                <td>
                    @if ($customer->measurements)
                        <details class="measurement-disclosure"><summary>VIEW FIT <x-icon name="chevron-down"/></summary>
                            <div class="measurement-detail-list">
                                {{ collect(\App\Models\TailorOrder::MEASUREMENT_FIELDS)->filter(fn ($label, $field) => isset($customer->measurements[$field]))->map(fn ($label, $field) => $label.': '.$customer->measurements[$field].' '.($customer->measurement_unit ?? 'in'))->implode(' · ') }}
                            </div>
                        </details>
                    @else
                        <span class="quote-pending">Not taken yet</span>
                    @endif
                </td>
                <td><button class="edit-price-button" type="button" data-open-modal="measurements-modal-{{ $customer->id }}" aria-label="Edit measurements for {{ $customer->name }}"><x-icon name="ruler"/></button></td>
            </tr>
        @empty
            <tr><td colspan="6"><div class="owner-list-empty">No customers match that search.</div></td></tr>
        @endforelse
    </tbody></table></div>
    @if ($customers->hasPages())
        <div class="customer-pagination">{{ $customers->links() }}</div>
    @endif
</div>

@foreach ($customers as $customer)
    <dialog class="modal-dialog" id="measurements-modal-{{ $customer->id }}" aria-labelledby="measurements-modal-title-{{ $customer->id }}">
        <form action="{{ route('customers.measurements.update', $customer) }}" method="POST" class="modal-card measurements-edit-form">
            @csrf @method('PATCH')
            <div class="modal-heading"><div><span class="eyebrow">SAVED FIT</span><h2 id="measurements-modal-title-{{ $customer->id }}">{{ $customer->name }}</h2></div><button class="modal-close" type="button" data-close-modal aria-label="Close dialog">×</button></div>

            <fieldset class="booking-fieldset measurement-fieldset">
                <div class="measurement-heading">
                    <div><legend class="field-legend">UNIT</legend></div>
                    <label class="unit-switch"><input type="radio" name="measurement_unit" value="in" @checked(($customer->measurement_unit ?? 'in') === 'in')><span>IN</span></label>
                    <label class="unit-switch"><input type="radio" name="measurement_unit" value="cm" @checked($customer->measurement_unit === 'cm')><span>CM</span></label>
                </div>

                <div class="measurement-group-label">SHIRT / KAMEEZ</div>
                <div class="field-grid measurement-grid">
                    @foreach (\App\Models\TailorOrder::SHIRT_FIELDS as $key => $label)
                        <label class="field-label">{{ $label }}<input class="field-control" type="number" step="0.5" min="0" max="300" name="measurements[{{ $key }}]" value="{{ $customer->measurements[$key] ?? '' }}"></label>
                    @endforeach
                </div>

                <div class="measurement-group-label">TROUSER / SHALWAR</div>
                <div class="field-grid measurement-grid">
                    @foreach (\App\Models\TailorOrder::TROUSER_FIELDS as $key => $label)
                        <label class="field-label">{{ $label }}<input class="field-control" type="number" step="0.5" min="0" max="300" name="measurements[{{ $key }}]" value="{{ $customer->measurements[$key] ?? '' }}"></label>
                    @endforeach
                </div>

                <div class="measurement-group-label">VEST / WAISTCOAT</div>
                <div class="field-grid measurement-grid">
                    @foreach (\App\Models\TailorOrder::VEST_FIELDS as $key => $label)
                        <label class="field-label">{{ $label }}<input class="field-control" type="number" step="0.5" min="0" max="300" name="measurements[{{ $key }}]" value="{{ $customer->measurements[$key] ?? '' }}"></label>
                    @endforeach
                </div>
            </fieldset>

            <label class="field-label">NOTES <span class="field-hint">OPTIONAL</span><input class="field-control" name="measurement_notes" value="{{ $customer->measurement_notes }}" maxlength="1000"></label>

            <div class="modal-actions"><button class="button button-light" type="button" data-close-modal>Not now</button><button class="button button-dark" type="submit">Save measurements <span>→</span></button></div>
        </form>
    </dialog>
@endforeach
