@extends('layouts.admin')

@section('title', 'Saim · Workroom')
@section('page-title', 'Dashboard')

@section('page')
        <section class="owner-welcome" id="owner-home"><div><span class="eyebrow"><span class="online-pip"></span> THE WORKROOM · {{ now()->format('l, d F') }}</span><h1>A good day for <em>good work.</em></h1><p>Your orders, your customers, and your craft—all in one place.</p></div><a class="owner-public-link" href="{{ route('home') }}" target="_blank" rel="noopener">See your website <span>↗</span></a></section>

        @if ($dueSoonOrders->isNotEmpty())
            <div class="notice notice-due-soon" role="status">
                <strong>Due soon:</strong>
                {{ $dueSoonOrders->count() }} {{ Str::plural('order', $dueSoonOrders->count()) }} due by {{ $dueSoonOrders->last()->due_date->format('d M') }} —
                @foreach ($dueSoonOrders as $dueOrder)
                    SAIM-{{ str_pad((string) $dueOrder->id, 5, '0', STR_PAD_LEFT) }} ({{ $dueOrder->customer->name }}, {{ $dueOrder->due_date->isToday() ? 'today' : $dueOrder->due_date->format('d M') }}){{ ! $loop->last ? ', ' : '' }}
                @endforeach
            </div>
        @endif

        @if ($eidReminders->isNotEmpty())
            <section class="owner-section compact-owner-section" id="owner-eid-reminders">
                <header class="owner-section-header"><div><span class="eyebrow">EID IS COMING UP</span><h2>Seasonal reminders<span class="heading-dot">.</span></h2><p>These customers ordered around last Eid — a nudge now might bring them back.</p></div><span class="owner-subtle-count">{{ $eidReminders->count() }} {{ Str::plural('customer', $eidReminders->count()) }}</span></header>
                <div class="owner-list-card owner-inquiry-list">
                    @foreach ($eidReminders as $eidCustomer)
                        <article class="owner-inquiry">
                            <div class="inquiry-heading">
                                <span class="client-avatar avatar-{{ ($eidCustomer->id % 5) + 1 }}">{{ mb_strtoupper(mb_substr($eidCustomer->name, 0, 1)) }}</span>
                                <span class="owner-customer-detail"><strong>{{ $eidCustomer->name }}</strong><a href="tel:{{ $eidCustomer->phone }}">{{ $eidCustomer->phone }}</a></span>
                                <a class="inquiry-status" href="https://wa.me/{{ preg_replace('/\D/', '', $eidCustomer->phone) }}?text={{ urlencode('Eid ka time kareeb hai, Saim aapke liye fabric taiyaar rakhe?') }}" target="_blank" rel="noopener">SEND WHATSAPP <x-icon name="whatsapp"/></a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="owner-stats" aria-label="Workroom at a glance">
            <article><span>IN THE WORKROOM</span><strong>{{ number_format($stats['active']) }}</strong><small>Active orders</small></article>
            <article><span>YOUR CUSTOMERS</span><strong>{{ number_format($stats['customers']) }}</strong><small>Lovely people</small></article>
            <article><span>READY TO COLLECT</span><strong>{{ number_format($stats['ready']) }}</strong><small>Finished orders</small></article>
            <article><span>BALANCE TO COLLECT</span><strong class="balance-stat">{{ number_format($stats['due']) }}</strong><small>PKR · open orders</small></article>
        </section>

        <section class="owner-section" id="owner-orders">
            <header class="owner-section-header">
                <div><span class="eyebrow">YOUR LITTLE BLACK BOOK</span><h2>The order book<span class="heading-dot">.</span></h2><p>Every garment in the making, accounted for.</p></div>
                <a class="owner-public-link" href="{{ route('home') }}#fitting">Preview customer booking <span>↗</span></a>
            </header>
            <details class="quick-order">
                <summary class="button button-dark">＋ <span>Write a new order</span></summary>
                <form action="{{ route('orders.store') }}" method="POST" class="quick-order-form" data-order-form>
                    @csrf
                    <div class="quick-order-heading"><div><span class="eyebrow">A FRESH PAGE IN THE ORDER BOOK</span><h3>Let's get the details down.</h3></div><span class="quick-order-count">NEW ORDER</span></div>
                    <div class="quick-order-fields">
                        <label class="field-label quick-span">EXISTING CUSTOMER <span class="field-hint">OR ADD SOMEONE NEW BELOW</span>
                            <select class="field-control" data-customer-picker aria-label="Select an existing customer">
                                <option value="">A new customer</option>
                                @foreach ($customersForSelect as $customer)<option value="{{ $customer->id }}" data-name="{{ $customer->name }}" data-gender="{{ $customer->gender }}" data-phone="{{ $customer->phone }}" data-email="{{ $customer->email }}" data-measurements="{{ json_encode($customer->measurements ?? [], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) }}" data-measurement-unit="{{ $customer->measurement_unit ?? 'in' }}">{{ $customer->name }} · {{ $customer->phone }}</option>@endforeach
                            </select>
                            <input type="hidden" name="customer_id" data-customer-id>
                        </label>
                        <label class="field-label customer-entry-field">CUSTOMER NAME <span class="required-mark">*</span><input class="field-control" data-customer-name name="customer_name" placeholder="Their name" maxlength="120" required></label>
                        <label class="field-label customer-entry-field">PHONE NUMBER <span class="required-mark">*</span><input class="field-control" data-customer-phone name="phone" type="tel" placeholder="+92 300 1234567" maxlength="30" required></label>
                        <label class="field-label customer-entry-field">GENDER <span class="required-mark">*</span><select class="field-control" data-customer-gender name="gender" required>@foreach (\App\Models\TailorOrder::GENDERS as $genderKey => $genderLabel)<option value="{{ $genderKey }}">{{ $genderLabel }}</option>@endforeach</select></label>
                        <label class="field-label">GARMENT <span class="required-mark">*</span><select class="field-control" name="garment" required>@foreach (\App\Models\TailorOrder::GARMENTS as $garment)<option value="{{ $garment }}">{{ $garment }}</option>@endforeach</select></label>
                        <label class="field-label">FABRIC <span class="required-mark">*</span><select class="field-control" name="fabric" required>@foreach (\App\Models\TailorOrder::FABRICS as $fabric)<option value="{{ $fabric }}">{{ $fabric }}</option>@endforeach</select></label>
                        <label class="field-label">FINISH <span class="required-mark">*</span><select class="field-control" name="package" required>@foreach (\App\Models\TailorOrder::PACKAGES as $packageKey => $label)<option value="{{ $packageKey }}">{{ $label }}</option>@endforeach</select></label>
                        <label class="field-label">FITTING / DELIVERY DATE <span class="required-mark">*</span><input class="field-control" type="date" name="due_date" min="{{ today()->toDateString() }}" max="{{ today()->addMonths(18)->toDateString() }}" value="{{ today()->addDays(14)->toDateString() }}" required></label>
                    </div>
                    <fieldset class="booking-fieldset quick-size-field"><legend class="field-legend">SIZE <span class="required-mark">*</span></legend>
                        <div class="size-grid">@foreach (['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL'] as $size)<label class="size-choice"><input type="radio" name="standard_size" value="{{ $size }}" @checked($size === 'M') @if ($loop->first) required @endif><span>{{ $size }}</span></label>@endforeach</div>
                        <label class="custom-size-option"><input type="radio" name="standard_size" value="custom"><span class="custom-check" aria-hidden="true"></span><span><strong>Take or reuse their exact measurements</strong><small>Their last fit is filled in automatically.</small></span></label>
                    </fieldset>
                    <fieldset class="booking-fieldset measurement-fieldset" data-measurements hidden><div class="measurement-heading"><div><legend class="field-legend">MEASUREMENTS</legend></div><label class="unit-switch"><input type="radio" name="measurement_unit" value="in" checked><span>IN</span></label><label class="unit-switch"><input type="radio" name="measurement_unit" value="cm"><span>CM</span></label></div>
                        <div class="measurement-group-label">SHIRT / KAMEEZ</div>
                        <div class="field-grid measurement-grid">@foreach (\App\Models\TailorOrder::SHIRT_FIELDS as $key => $label)<label class="field-label">{{ $label }}<select class="field-control measurement-input" name="measurements[{{ $key }}]" disabled><option value="">—</option>@foreach (\App\Models\TailorOrder::inchOptions() as $inch)<option value="{{ $inch }}">{{ $inch }}"</option>@endforeach</select></label>@endforeach</div>
                        <div class="measurement-group-label">TROUSER / SHALWAR</div>
                        <div class="field-grid measurement-grid">@foreach (\App\Models\TailorOrder::TROUSER_FIELDS as $key => $label)<label class="field-label">{{ $label }}<select class="field-control measurement-input" name="measurements[{{ $key }}]" disabled><option value="">—</option>@foreach (\App\Models\TailorOrder::inchOptions() as $inch)<option value="{{ $inch }}">{{ $inch }}"</option>@endforeach</select></label>@endforeach</div>
                        <div class="measurement-group-label">VEST / WAISTCOAT</div>
                        <div class="field-grid measurement-grid">@foreach (\App\Models\TailorOrder::VEST_FIELDS as $key => $label)<label class="field-label">{{ $label }}<select class="field-control measurement-input" name="measurements[{{ $key }}]" disabled><option value="">—</option>@foreach (\App\Models\TailorOrder::inchOptions() as $inch)<option value="{{ $inch }}">{{ $inch }}"</option>@endforeach</select></label>@endforeach</div>
                    </fieldset>
                    <div class="quick-order-fields quick-order-finish">
                        <label class="field-label">TOTAL PRICE · PKR <span class="required-mark">*</span><input class="field-control" data-order-total type="number" name="total" min="0" max="10000000" value="0" inputmode="numeric" required></label>
                        <label class="field-label">ADVANCE RECEIVED · PKR <span class="required-mark">*</span><input class="field-control" data-order-advance type="number" name="advance" min="0" max="10000000" value="0" inputmode="numeric" required></label>
                        <label class="field-label quick-span">DESIGN NOTES <span class="field-hint">OPTIONAL</span><input class="field-control" name="style_notes" placeholder="A fitting note, or something to remember…" maxlength="1000"></label>
                    </div>
                    <div class="balance-preview"><span>BALANCE REMAINING</span><strong>PKR <span data-order-balance>0</span></strong></div>
                    <div class="quick-order-actions"><button class="button button-light" type="reset">Clear details</button><button class="button button-dark" type="submit">Add to the order book <span>→</span></button></div>
                </form>
            </details>

            <div class="owner-table-card">
                @if ($orders->isEmpty())
                    <div class="owner-empty"><span><x-icon name="sparkle"/></span><h3>Your order book is ready.</h3><p>New requests from the website and orders you add here appear together.</p><a href="{{ route('home') }}#fitting">See the fitting form →</a></div>
                @else
                    <form action="{{ route('orders.bulk-status') }}" method="POST" id="bulk-status-form">@csrf @method('PATCH')</form>
                    <div class="bulk-action-bar" data-bulk-action-bar hidden>
                        <span><strong data-bulk-count>0</strong> selected</span>
                        <select class="field-control" name="status" form="bulk-status-form" aria-label="Status to apply to selected orders" required>
                            <option value="">Update to…</option>
                            @foreach (\App\Models\TailorOrder::STATUSES as $status)<option value="{{ $status }}">{{ \App\Models\TailorOrder::STATUS_LABELS[$status] }}</option>@endforeach
                        </select>
                        <button class="button button-dark" type="submit" form="bulk-status-form">Apply <span aria-hidden="true">→</span></button>
                    </div>
                    <div class="table-scroll"><table class="data-table owner-order-table"><thead><tr><th><input type="checkbox" aria-label="Select all orders" data-select-all></th><th>CLIENT &amp; GARMENT</th><th>FITTING DATE</th><th>PROGRESS</th><th>ASSIGNED</th><th>PRICE / BALANCE</th><th>UPDATE</th></tr></thead><tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td><input type="checkbox" name="order_ids[]" value="{{ $order->id }}" form="bulk-status-form" aria-label="Select order SAIM-{{ str_pad((string) $order->id, 5, '0', STR_PAD_LEFT) }}" data-select-row></td>
                                <td><span class="client-cell"><span class="client-avatar avatar-{{ ($order->customer_id % 5) + 1 }}">{{ mb_strtoupper(mb_substr($order->customer->name, 0, 1)) }}</span><span><strong>{{ $order->customer->name }}</strong><small class="row-subline">{{ $order->customer->phone }} · {{ $order->garment }} / {{ $order->fabric }}</small></span></span>
                                    <details class="measurement-disclosure order-fit-disclosure"><summary>FITTING DETAILS <x-icon name="chevron-down"/></summary><div class="measurement-detail-list"><span><strong>Size</strong><span>{{ $order->standard_size === 'custom' ? 'Custom measurements' : ($order->standard_size ?: 'To confirm') }}</span></span>@if ($order->standard_size === 'custom')@foreach (\App\Models\TailorOrder::MEASUREMENT_FIELDS as $field => $label)@if (isset($order->measurements[$field]))<span><strong>{{ $label }}</strong><span>{{ $order->measurements[$field] }} {{ $order->measurement_unit }}</span></span>@endif @endforeach @endif @if ($order->style_notes)<span class="fit-note-text">{{ $order->style_notes }}</span>@endif</div></details>
                                </td>
                                <td>{{ $order->due_date->format('d M Y') }}</td>
                                <td><x-order-progress :status="$order->status"/></td>
                                <td><form action="{{ route('orders.assign', $order) }}" method="POST">@csrf @method('PATCH')<select name="assigned_to" onchange="this.form.submit()" aria-label="Assign order SAIM-{{ str_pad((string) $order->id, 5, '0', STR_PAD_LEFT) }}"><option value="">Unassigned</option>@foreach ($staff as $member)<option value="{{ $member->id }}" @selected($order->assigned_to === $member->id)>{{ $member->name }}</option>@endforeach</select></form></td>
                                <td>@if ($order->total > 0)<strong>PKR {{ number_format($order->total) }}</strong><small class="row-subline">PKR {{ number_format($order->balance) }} remaining</small>@else<strong class="quote-pending">Price to confirm</strong><small class="row-subline">Waiting for fitting</small>@endif</td>
                                <td><div class="order-actions" style="position: relative; z-index: 999;"><form action="{{ route('orders.status', $order) }}" method="POST" class="status-update-form">@csrf @method('PATCH')<select name="status" autocomplete="off" aria-label="Update status for order SAIM-{{ str_pad((string) $order->id, 5, '0', STR_PAD_LEFT) }}" onchange="this.form.submit()" style="position: relative; z-index: 1000; cursor: pointer;">@foreach (\App\Models\TailorOrder::STATUSES as $status)<option value="{{ $status }}" @selected($order->status === $status)>{{ \App\Models\TailorOrder::STATUS_LABELS[$status] }}</option>@endforeach</select></form><button class="edit-price-button" type="button" data-open-modal="price-modal-{{ $order->id }}" aria-label="Update price for order SAIM-{{ str_pad((string) $order->id, 5, '0', STR_PAD_LEFT) }}"><x-icon name="edit"/></button><a class="edit-price-button" href="{{ route('orders.receipt', $order) }}" target="_blank" aria-label="Receipt for order SAIM-{{ str_pad((string) $order->id, 5, '0', STR_PAD_LEFT) }}"><x-icon name="cart"/></a></div></td>
                            </tr>
                        @endforeach
                    </tbody></table></div>
                @endif
            </div>
        </section>

        <section class="owner-lower-grid">
            <section class="owner-section compact-owner-section" id="owner-customers">
                <header class="owner-section-header"><div><span class="eyebrow">THE PEOPLE YOU MAKE IT FOR</span><h2>Your customers<span class="heading-dot">.</span></h2></div><span class="owner-subtle-count">{{ number_format($stats['customers']) }} total</span></header>
                <div class="owner-list-card">
                    @forelse ($customers as $customer)
                        <details class="owner-customer">
                            <summary><span class="client-avatar avatar-{{ ($customer->id % 5) + 1 }}">{{ mb_strtoupper(mb_substr($customer->name, 0, 1)) }}</span><span class="owner-customer-detail"><strong>{{ $customer->name }}</strong><small>{{ $customer->phone }}@if ($customer->gender) · {{ \App\Models\TailorOrder::GENDERS[$customer->gender] ?? '' }}@endif · {{ $customer->orders_count }} {{ Str::plural('order', $customer->orders_count) }}</small></span><span class="owner-expand">VIEW FIT <x-icon name="chevron-down"/></span></summary>
                            <div class="owner-customer-body">
                                @if ($customer->email)<span><strong>Email</strong>{{ $customer->email }}</span>@endif
                                @if ($customer->measurements)<span><strong>Saved fit · {{ strtoupper($customer->measurement_unit ?? 'in') }}</strong>{{ collect(\App\Models\TailorOrder::MEASUREMENT_FIELDS)->filter(fn ($label, $field) => isset($customer->measurements[$field]))->map(fn ($label, $field) => $label.': '.$customer->measurements[$field])->implode(' · ') }}</span>@else<span><strong>Saved fit</strong>Measurements will show here after the first fitting.</span>@endif
                                @if ($customer->measurement_notes)<span><strong>Notes</strong>{{ $customer->measurement_notes }}</span>@endif
                                @if ($customer->measurementHistories->count() > 1)
                                    <details class="measurement-disclosure fit-history-disclosure"><summary>FIT HISTORY ({{ $customer->measurementHistories->count() }}) <x-icon name="chevron-down"/></summary>
                                        <div class="measurement-detail-list">
                                            @foreach ($customer->measurementHistories as $history)
                                                <span><strong>{{ $history->measured_at?->format('d M Y') ?? $history->created_at->format('d M Y') }} · {{ strtoupper($history->measurement_unit) }}</strong>{{ collect(\App\Models\TailorOrder::MEASUREMENT_FIELDS)->filter(fn ($label, $field) => isset($history->measurements[$field]))->map(fn ($label, $field) => $label.': '.$history->measurements[$field])->implode(' · ') }}</span>
                                            @endforeach
                                        </div>
                                    </details>
                                @endif
                            </div>
                        </details>
                    @empty
                        <div class="owner-list-empty">Your customer book will fill as the requests come in.</div>
                    @endforelse
                </div>
            </section>

            <section class="owner-section compact-owner-section" id="owner-messages">
                <header class="owner-section-header"><div><span class="eyebrow">STRAIGHT FROM YOUR WEBSITE</span><h2>Notes for Saim<span class="heading-dot">.</span></h2></div><span class="owner-subtle-count">{{ $inquiries->where('status', 'new')->count() }} new</span></header>
                <div class="owner-list-card owner-inquiry-list">
                    @forelse ($inquiries as $inquiry)
                        <article class="owner-inquiry {{ $inquiry->status === 'handled' ? 'is-handled' : '' }}">
                            <div class="inquiry-heading"><span class="client-avatar avatar-{{ ($inquiry->id % 5) + 1 }}">{{ mb_strtoupper(mb_substr($inquiry->name, 0, 1)) }}</span><span class="owner-customer-detail"><strong>{{ $inquiry->name }}</strong><a href="tel:{{ $inquiry->phone }}">{{ $inquiry->phone }}</a></span><div class="inquiry-actions"><form action="{{ route('inquiries.update', $inquiry) }}" method="POST">@csrf @method('PATCH')<input type="hidden" name="status" value="{{ $inquiry->status === 'new' ? 'handled' : 'new' }}"><button class="inquiry-status" type="submit">{{ $inquiry->status === 'new' ? 'MARK HANDLED' : 'HANDLED · UNDO' }}</button></form><form action="{{ route('inquiries.destroy', $inquiry) }}" method="POST" onsubmit="return confirm('Delete this note from {{ $inquiry->name }}?');">@csrf @method('DELETE')<button class="edit-price-button" type="submit" aria-label="Delete message from {{ $inquiry->name }}"><x-icon name="trash"/></button></form></div></div>
                            <p>{{ $inquiry->message }}</p><time>{{ $inquiry->created_at->format('d M · g:i a') }}</time>
                        </article>
                    @empty
                        <div class="owner-list-empty">When someone writes to Saim, their note will appear here.</div>
                    @endforelse
                </div>
            </section>
        </section>
        <footer class="owner-footer"><a href="{{ route('home') }}">SAIM TAILORS</a><span>ONE TAILOR. ONE WORKROOM. ALL THE GOOD DETAILS. <x-icon name="sparkle"/></span><a href="#owner-home">BACK TO THE TOP ↑</a></footer>

@foreach ($orders as $order)
    <dialog class="modal-dialog" id="price-modal-{{ $order->id }}" aria-labelledby="price-modal-title-{{ $order->id }}">
        <form action="{{ route('orders.price', $order) }}" method="POST" class="modal-card price-adjustment-form">
            @csrf @method('PATCH')
            <div class="modal-heading"><div><span class="eyebrow">THE FIGURES, JUST SO</span><h2 id="price-modal-title-{{ $order->id }}">Price &amp; payment</h2></div><button class="modal-close" type="button" data-close-modal aria-label="Close dialog">×</button></div>
            <p class="price-modal-customer">{{ $order->customer->name }} · SAIM-{{ str_pad((string) $order->id, 5, '0', STR_PAD_LEFT) }}</p>
            <label class="field-label">AGREED PRICE · PKR <span class="required-mark">*</span><input class="field-control" data-price-total type="number" name="total" min="0" max="10000000" step="1" value="{{ $order->total }}" required></label>
            <label class="field-label">ADVANCE RECEIVED · PKR <span class="required-mark">*</span><input class="field-control" data-price-advance type="number" name="advance" min="0" max="10000000" step="1" value="{{ $order->advance }}" required></label>
            <div class="balance-preview"><span>REMAINING BALANCE</span><strong>PKR <span data-price-balance>{{ number_format($order->balance) }}</span></strong></div>
            <div class="modal-actions"><button class="button button-light" type="button" data-close-modal>Not now</button><button class="button button-dark" type="submit">Save the figures <span>→</span></button></div>
        </form>
    </dialog>
@endforeach
@endsection
