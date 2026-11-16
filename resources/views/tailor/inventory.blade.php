@extends('layouts.admin')

@section('title', 'Inventory')
@section('page-title', 'Inventory')

@section('page')
<div class="page-heading-row">
    <div><span class="eyebrow">FABRIC &amp; SUPPLIES</span><h1>Inventory<span class="heading-dot">.</span></h1><p class="page-intro">Track what's in stock and what's running low.</p></div>
</div>

<div class="dashboard-grid">
    <div class="content-panel orders-panel">
        <div class="panel-topline"><h2>{{ $items->count() }} {{ Str::plural('item', $items->count()) }}</h2></div>
        <div class="table-scroll"><table class="data-table"><thead><tr><th>ITEM</th><th>QUANTITY</th><th>LOW STOCK AT</th><th></th></tr></thead><tbody>
            @forelse ($items as $item)
                <tr>
                    <td><strong>{{ $item->name }}</strong> @if ($item->is_low)<span class="quote-pending">LOW</span>@endif</td>
                    <td>
                        <form action="{{ route('inventory.update', $item) }}" method="POST" class="inventory-inline-form">
                            @csrf @method('PATCH')
                            <input type="number" step="0.01" min="0" name="quantity" value="{{ $item->quantity }}">
                            {{ $item->unit }}
                            <input type="hidden" name="low_stock_threshold" value="{{ $item->low_stock_threshold }}">
                            <button class="button button-light" type="submit">Save</button>
                        </form>
                    </td>
                    <td>{{ $item->low_stock_threshold }} {{ $item->unit }}</td>
                    <td>
                        <form action="{{ route('inventory.destroy', $item) }}" method="POST" onsubmit="return confirm('Remove {{ $item->name }}?');">
                            @csrf @method('DELETE')
                            <button class="edit-price-button" type="submit" aria-label="Remove {{ $item->name }}"><x-icon name="trash"/></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4"><div class="owner-list-empty">No inventory items yet.</div></td></tr>
            @endforelse
        </tbody></table></div>
    </div>

    <div class="side-stack">
        <div class="schedule-card">
            <h2>Add an item</h2>
            <form action="{{ route('inventory.store') }}" method="POST" class="form-stack">
                @csrf
                <label class="field-label">NAME <span class="required-mark">*</span><input class="field-control" name="name" value="{{ old('name') }}" maxlength="120" required></label>
                <label class="field-label">UNIT <span class="required-mark">*</span><input class="field-control" name="unit" value="{{ old('unit', 'meters') }}" maxlength="20" required></label>
                <label class="field-label">QUANTITY <span class="required-mark">*</span><input class="field-control" type="number" step="0.01" min="0" name="quantity" value="{{ old('quantity', 0) }}" required></label>
                <label class="field-label">LOW STOCK ALERT AT<input class="field-control" type="number" step="0.01" min="0" name="low_stock_threshold" value="{{ old('low_stock_threshold', 0) }}"></label>
                <button class="button button-dark button-wide" type="submit">Add item <span aria-hidden="true">→</span></button>
            </form>
        </div>
    </div>
</div>
@endsection
