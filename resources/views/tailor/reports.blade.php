@extends('layouts.admin')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('page')
<div class="page-heading-row">
    <div><span class="eyebrow">THIS MONTH</span><h1>Reports<span class="heading-dot">.</span></h1></div>
</div>

<section class="owner-stats" aria-label="Monthly summary">
    <article><span>EARNED THIS MONTH</span><strong>{{ number_format($monthlyEarnings) }}</strong><small>PKR · delivered orders</small></article>
    <article><span>DELIVERED THIS MONTH</span><strong>{{ number_format($completedThisMonth) }}</strong><small>orders</small></article>
</section>

<div class="content-panel orders-panel">
    <div class="panel-topline"><h2>Orders by status</h2></div>
    <table class="data-table"><tbody>
        @foreach (\App\Models\TailorOrder::STATUS_LABELS as $status => $label)
            <tr><td>{{ $label }}</td><td>{{ $statusCounts[$status] ?? 0 }}</td></tr>
        @endforeach
    </tbody></table>
</div>
@endsection
