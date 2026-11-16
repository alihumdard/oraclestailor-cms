@extends('layouts.customer')

@section('title', 'Request a new order')
@section('page-title', 'New order')

@php
    $oldMeasurements = old('measurements', $savedMeasurements ?? []);
@endphp

@section('page')
        <section class="owner-welcome" id="owner-home"><div><span class="eyebrow">A NEW REQUEST</span><h1>What would you<br><em>like made?</em></h1><p>Share the details below; Saim will confirm the fit and price personally.</p></div></section>

        @if ($errors->any())<div class="notice notice-error" role="alert"><strong>Let's take another look.</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

        <section class="owner-section" id="owner-new-order">
            <div class="owner-table-card" style="padding:22px;">
                <form action="{{ route('customer.orders.store') }}" method="POST" class="order-form new-order-page" data-order-form>
                    @csrf
                    <div class="order-form-section">
                        <div class="order-form-heading"><span class="form-section-number">01</span><div><h2>The garment</h2><p>Pick the piece, fabric and finish.</p></div></div>
                        <div class="order-form-fields">
                            <label class="field-label">GARMENT <span class="required-mark">*</span><select class="field-control" name="garment" required>@foreach ($garments as $garment)<option value="{{ $garment }}" @selected(old('garment') === $garment)>{{ $garment }}</option>@endforeach</select></label>
                            <label class="field-label">FABRIC <span class="required-mark">*</span><select class="field-control" name="fabric" required>@foreach ($fabrics as $fabric)<option value="{{ $fabric }}" @selected(old('fabric') === $fabric)>{{ $fabric }}</option>@endforeach</select></label>
                            <label class="field-label">FINISH <span class="required-mark">*</span><select class="field-control" name="package" required>@foreach ($packages as $packageKey => $label)<option value="{{ $packageKey }}" @selected(old('package', 'everyday') === $packageKey)>{{ $label }}</option>@endforeach</select></label>
                            <label class="field-label">FITTING OR DELIVERY DATE <span class="required-mark">*</span><input class="field-control" type="date" name="due_date" min="{{ today()->toDateString() }}" max="{{ today()->addMonths(18)->toDateString() }}" value="{{ old('due_date', today()->addDays(14)->toDateString()) }}" required></label>
                        </div>
                    </div>
                    <div class="order-form-section">
                        <div class="order-form-heading"><span class="form-section-number">02</span><div><h2>Your fit</h2><p>Choose a size, or share measurements for a more personal cut.</p></div></div>
                        <fieldset class="booking-fieldset order-size-fieldset"><legend class="field-legend">APPROXIMATE SIZE <span class="field-hint">OPTIONAL</span></legend>
                            <div class="size-grid">@foreach (['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL'] as $size)<label class="size-choice"><input type="radio" name="standard_size" value="{{ $size }}" @checked(old('standard_size') === $size)><span>{{ $size }}</span></label>@endforeach</div>
                            @error('standard_size')<span class="field-error">{{ $message }}</span>@enderror
                        </fieldset>
                        <div class="order-measurements">
                            <div class="measurement-group-label">SHIRT / KAMEEZ · INCHES</div>
                            <div class="field-grid measurement-grid">@foreach ($shirtFields as $key => $label)<label class="field-label">{{ $label }}<select class="field-control measurement-input" name="measurements[{{ $key }}]"><option value="">—</option>@foreach ($inchOptions as $inch)<option value="{{ $inch }}" @selected((string) ($oldMeasurements[$key] ?? '') === (string) $inch)>{{ $inch }}"</option>@endforeach</select></label>@endforeach</div>
                            <div class="measurement-group-label">TROUSER / SHALWAR · INCHES</div>
                            <div class="field-grid measurement-grid">@foreach ($trouserFields as $key => $label)<label class="field-label">{{ $label }}<select class="field-control measurement-input" name="measurements[{{ $key }}]"><option value="">—</option>@foreach ($inchOptions as $inch)<option value="{{ $inch }}" @selected((string) ($oldMeasurements[$key] ?? '') === (string) $inch)>{{ $inch }}"</option>@endforeach</select></label>@endforeach</div>
                            <div class="measurement-group-label">VEST / WAISTCOAT · INCHES</div>
                            <div class="field-grid measurement-grid">@foreach ($vestFields as $key => $label)<label class="field-label">{{ $label }}<select class="field-control measurement-input" name="measurements[{{ $key }}]"><option value="">—</option>@foreach ($inchOptions as $inch)<option value="{{ $inch }}" @selected((string) ($oldMeasurements[$key] ?? '') === (string) $inch)>{{ $inch }}"</option>@endforeach</select></label>@endforeach</div>
                            @if (!empty($savedMeasurements) && empty(old('measurements')))
                                <p class="field-hint" style="margin-top:8px;">Loaded from your last fitting on {{ $customer->measured_at?->format('d M Y') ?? 'file' }}. Adjust anything that's changed.</p>
                            @endif
                        </div>
                        <div class="order-fit-fields"><label class="field-label order-field-span">DESIGN NOTES <span class="field-hint">OPTIONAL</span><input class="field-control" type="text" name="style_notes" value="{{ old('style_notes') }}" placeholder="A detail, a preference, a reminder…" maxlength="1000"></label></div>
                    </div>
                    <div class="order-form-actions"><p>Saim will confirm the agreed price and advance with you personally.</p><button class="button button-dark" type="submit">Send my request <span aria-hidden="true">→</span></button></div>
                </form>
            </div>
        </section>
@endsection
