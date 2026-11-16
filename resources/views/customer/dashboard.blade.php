@extends('layouts.customer')

@section('title', 'My Account')
@section('page-title', 'Dashboard')

@section('page')
        <section class="owner-welcome" id="owner-home"><div><span class="eyebrow"><span class="online-pip"></span> {{ now()->format('l, d F') }}</span><h1>Hello, <em>{{ explode(' ', $customer->name)[0] }}.</em></h1><p>Track every order and start a new one whenever you're ready.</p></div><a class="owner-public-link" href="{{ route('customer.order.create') }}">Request a new order <span>↗</span></a></section>

        <section class="owner-stats" aria-label="Your orders at a glance">
            <article><span>TOTAL ORDERS</span><strong>{{ $orders->count() }}</strong><small>All time</small></article>
            <article><span>IN PROGRESS</span><strong>{{ $orders->whereNotIn('status', ['delivered', 'cancelled'])->count() }}</strong><small>Being made</small></article>
            <article><span>READY TO COLLECT</span><strong>{{ $orders->where('status', 'ready')->count() }}</strong><small>Waiting for you</small></article>
            <article><span>DELIVERED</span><strong>{{ $orders->where('status', 'delivered')->count() }}</strong><small>Completed</small></article>
        </section>

        <section class="owner-section" id="owner-orders">
            <header class="owner-section-header">
                <div><span class="eyebrow">YOUR ORDER HISTORY</span><h2>Every fitting<span class="heading-dot">.</span></h2><p>Every garment you've requested, with its live status.</p></div>
                <a class="owner-public-link" href="{{ route('customer.order.create') }}">Request a new order <span>↗</span></a>
            </header>
            <div class="owner-table-card">
                @if ($orders->isEmpty())
                    <div class="owner-empty"><span><x-icon name="sparkle"/></span><h3>No orders yet.</h3><p>When you request a fitting, it will appear here with its live status.</p><a href="{{ route('customer.order.create') }}">Request your first order →</a></div>
                @else
                    <div class="table-scroll"><table class="data-table owner-order-table"><thead><tr><th>GARMENT</th><th>FITTING DATE</th><th>PROGRESS</th><th>PRICE / BALANCE</th></tr></thead><tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td><strong>{{ $order->garment }}</strong><small class="row-subline">SAIM-{{ str_pad((string) $order->id, 5, '0', STR_PAD_LEFT) }} · {{ $order->fabric }}</small></td>
                                <td>{{ $order->due_date->format('d M Y') }}</td>
                                <td><x-order-progress :status="$order->status"/></td>
                                <td>@if ($order->total > 0)<strong>PKR {{ number_format($order->total) }}</strong><small class="row-subline">PKR {{ number_format($order->balance) }} remaining</small>@else<strong class="quote-pending">Price to confirm</strong>@endif</td>
                            </tr>
                        @endforeach
                    </tbody></table></div>
                @endif
            </div>
        </section>

        <section class="owner-section compact-owner-section" id="owner-profile">
            <header class="owner-section-header"><div><span class="eyebrow">ON FILE WITH SAIM</span><h2>Your saved fit<span class="heading-dot">.</span></h2></div></header>
            <div class="owner-list-card">
                <div class="owner-customer-body" style="padding:18px;">
                    <span><strong>Phone</strong>{{ $customer->phone }}</span>
                    @if ($customer->email)<span><strong>Email</strong>{{ $customer->email }}</span>@endif
                    @if ($customer->measurements)<span><strong>Saved measurements · {{ strtoupper($customer->measurement_unit ?? 'in') }}</strong>{{ collect(\App\Models\TailorOrder::MEASUREMENT_FIELDS)->filter(fn ($label, $field) => isset($customer->measurements[$field]))->map(fn ($label, $field) => $label.': '.$customer->measurements[$field])->implode(' · ') }}</span>@else<span><strong>Saved measurements</strong>Will appear here after your first fitting.</span>@endif
                </div>
            </div>
        </section>

        <footer class="owner-footer"><a href="{{ route('home') }}">SAIM TAILORS</a><span>YOUR FITTINGS, YOUR PACE. <x-icon name="sparkle"/></span><a href="#owner-home">BACK TO THE TOP ↑</a></footer>
@endsection
