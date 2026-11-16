@extends('layouts.admin')

@section('title', 'Board')
@section('page-title', 'Board')

@section('page')
<div class="page-heading-row">
    <div><span class="eyebrow">ORDER WORKFLOW</span><h1>Board<span class="heading-dot">.</span></h1><p class="page-intro">Each order's current step. Move it on from here.</p></div>
</div>

<div class="kanban-board">
    @foreach (\App\Models\TailorOrder::STATUSES as $status)
        @continue($status === 'cancelled')
        <div class="kanban-column">
            <div class="kanban-column-head">{{ \App\Models\TailorOrder::STATUS_LABELS[$status] }} <span>{{ ($ordersByStatus[$status] ?? collect())->count() }}</span></div>
            @forelse ($ordersByStatus[$status] ?? [] as $order)
                <div class="kanban-card">
                    <strong>{{ $order->customer->name }}</strong>
                    <small>{{ $order->garment }} · SAIM-{{ str_pad((string) $order->id, 5, '0', STR_PAD_LEFT) }}</small>
                    <small>Due {{ $order->due_date->format('d M Y') }}</small>
                    <form action="{{ route('orders.status', $order) }}" method="POST">
                        @csrf @method('PATCH')
                        <select name="status" onchange="this.form.submit()">
                            @foreach (\App\Models\TailorOrder::STATUSES as $option)
                                <option value="{{ $option }}" @selected($order->status === $option)>{{ \App\Models\TailorOrder::STATUS_LABELS[$option] }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            @empty
                <div class="kanban-empty">No orders</div>
            @endforelse
        </div>
    @endforeach
</div>
@endsection
