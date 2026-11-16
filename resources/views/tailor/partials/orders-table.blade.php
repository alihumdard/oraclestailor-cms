@if ($orders->isEmpty())
    <div class="empty-state compact-empty"><span class="empty-symbol">⌁</span><h3>{{ $emptyTitle }}</h3><p>{{ $emptyBody }}</p></div>
@else
    <div class="table-scroll"><table class="data-table"><thead><tr><th>CLIENT & ORDER</th><th>GARMENT</th><th>DELIVERY</th><th>PROGRESS</th><th>PRICE / DUE</th><th>UPDATE</th></tr></thead><tbody>
        @foreach ($orders as $order)
            <tr>
                <td><span class="client-cell"><span class="client-avatar avatar-{{ ($order->customer_id % 5) + 1 }}">{{ mb_strtoupper(mb_substr($order->customer->name, 0, 1)) }}</span><span><strong>{{ $order->customer->name }}</strong><small class="row-subline">SAIM-{{ str_pad((string) $order->id, 5, '0', STR_PAD_LEFT) }} · {{ $order->customer->phone }}</small></span></span></td>
                <td><strong class="garment-name">{{ $order->garment }}</strong><small class="row-subline">{{ \App\Models\TailorOrder::PACKAGES[$order->package] ?? $order->package }} · {{ $order->fabric }}</small>
                    <details class="measurement-disclosure order-fit-disclosure"><summary>FIT &amp; DESIGN NOTES <span aria-hidden="true">⌄</span></summary>
                        <div class="measurement-detail-list"><span><strong>Size</strong><span>{{ $order->standard_size === 'custom' ? 'Custom measurements' : ($order->standard_size ?: 'To confirm') }}</span></span>
                            @if ($order->standard_size === 'custom')@foreach (\App\Models\TailorOrder::MEASUREMENT_FIELDS as $field => $label)@if (isset($order->measurements[$field]))<span><strong>{{ $label }}</strong><span>{{ $order->measurements[$field] }} {{ $order->measurement_unit }}</span></span>@endif @endforeach @endif
                            @if ($order->style_notes)<span class="fit-note-text">{{ $order->style_notes }}</span>@endif
                        </div>
                    </details>
                </td>
                <td><strong class="{{ $order->due_date->isPast() && ! $order->due_date->isToday() && ! in_array($order->status, ['delivered', 'cancelled']) ? 'overdue-text' : '' }}">{{ $order->due_date->format('d M Y') }}</strong><small class="row-subline">{{ $order->due_date->isToday() ? 'Due today' : $order->due_date->diffForHumans() }}</small></td>
                <td><span class="status-badge status-{{ $order->status }}"><i></i>{{ \App\Models\TailorOrder::STATUS_LABELS[$order->status] ?? ucfirst($order->status) }}</span></td>
                <td>@if ($order->total > 0)<strong>PKR {{ number_format($order->total) }}</strong><small class="row-subline">PKR {{ number_format($order->balance) }} remaining</small>@else<strong class="quote-pending">Price to confirm</strong><small class="row-subline">Not yet quoted</small>@endif</td>
                <td><div class="order-actions"><form action="{{ route('orders.status', $order) }}" method="POST" class="status-update-form">@csrf @method('PATCH')<select name="status" aria-label="Update status for order SAIM-{{ str_pad((string) $order->id, 5, '0', STR_PAD_LEFT) }}" onchange="this.form.submit()">@foreach (\App\Models\TailorOrder::STATUSES as $status)<option value="{{ $status }}" @selected($order->status === $status)>{{ \App\Models\TailorOrder::STATUS_LABELS[$status] }}</option>@endforeach</select></form><button class="edit-price-button" type="button" data-open-modal="price-modal-{{ $order->id }}" aria-label="Set price for order SAIM-{{ str_pad((string) $order->id, 5, '0', STR_PAD_LEFT) }}">↗</button></div></td>
            </tr>
        @endforeach
    </tbody></table></div>
    @if (method_exists($orders, 'links'))<div class="pagination-wrap">{{ $orders->links() }}</div>@endif
@endif
@if ($orders->isNotEmpty())
    @foreach ($orders as $order)
        <dialog class="modal-dialog" id="price-modal-{{ $order->id }}" aria-labelledby="price-modal-title-{{ $order->id }}">
            <form action="{{ route('orders.price', $order) }}" method="POST" class="modal-card price-adjustment-form">
                @csrf
                @method('PATCH')
                <div class="modal-heading"><div><span class="eyebrow">THE FIGURES, JUST SO</span><h2 id="price-modal-title-{{ $order->id }}">Price &amp; payment</h2></div><button class="modal-close" type="button" data-close-modal aria-label="Close dialog">×</button></div>
                <p class="price-modal-customer">{{ $order->customer->name }} <span>·</span> SAIM-{{ str_pad((string) $order->id, 5, '0', STR_PAD_LEFT) }} · {{ $order->garment }}</p>
                <label class="field-label">AGREED PRICE · PKR <span class="required-mark">*</span><input class="field-control" data-price-total type="number" name="total" min="0" max="10000000" step="1" value="{{ $order->total }}" inputmode="numeric" required>@error('total')<span class="field-error">{{ $message }}</span>@enderror</label>
                <label class="field-label">ADVANCE RECEIVED · PKR <span class="required-mark">*</span><input class="field-control" data-price-advance type="number" name="advance" min="0" max="10000000" step="1" value="{{ $order->advance }}" inputmode="numeric" required>@error('advance')<span class="field-error">{{ $message }}</span>@enderror</label>
                <div class="balance-preview"><span>REMAINING BALANCE</span><strong>PKR <span data-price-balance>{{ number_format($order->balance) }}</span></strong></div>
                <div class="modal-actions"><button class="button button-light" type="button" data-close-modal>Not now</button><button class="button button-dark" type="submit">Save agreed price <span>→</span></button></div>
            </form>
        </dialog>
    @endforeach
@endif
