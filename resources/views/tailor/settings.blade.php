@extends('layouts.admin')

@section('title', 'Business Settings')
@section('page-title', 'Business settings')

@section('page')
<div class="page-heading-row">
    <div><span class="eyebrow">SHOWN ON YOUR PUBLIC WEBSITE</span><h1>Business details<span class="heading-dot">.</span></h1><p class="page-intro">This address, phone and email appear on your website footer automatically.</p></div>
</div>

<div class="content-panel" style="max-width:640px;padding:26px;">
    <form action="{{ route('settings.update') }}" method="POST" class="form-stack">
        @csrf
        @method('PATCH')
        <label class="field-label">BUSINESS NAME <span class="required-mark">*</span><input class="field-control" name="business_name" value="{{ old('business_name', $setting->business_name) }}" maxlength="160" required>@error('business_name')<span class="field-error">{{ $message }}</span>@enderror</label>
        <label class="field-label">TAGLINE<input class="field-control" name="tagline" value="{{ old('tagline', $setting->tagline) }}" maxlength="160"></label>
        <label class="field-label">ADDRESS LINE<input class="field-control" name="address_line" value="{{ old('address_line', $setting->address_line) }}" placeholder="Shop #, street, market" maxlength="160"></label>
        <div class="field-grid">
            <label class="field-label">CITY<input class="field-control" name="city" value="{{ old('city', $setting->city) }}" maxlength="100"></label>
            <label class="field-label">REGION / PROVINCE<input class="field-control" name="region" value="{{ old('region', $setting->region) }}" maxlength="100"></label>
            <label class="field-label">COUNTRY<input class="field-control" name="country" value="{{ old('country', $setting->country) }}" maxlength="100"></label>
            <label class="field-label">PHONE<input class="field-control" type="tel" name="phone" value="{{ old('phone', $setting->phone) }}" maxlength="30"></label>
        </div>
        <label class="field-label">EMAIL<input class="field-control" type="email" name="email" value="{{ old('email', $setting->email) }}" maxlength="160">@error('email')<span class="field-error">{{ $message }}</span>@enderror</label>
        <label class="field-label">WORKING HOURS<input class="field-control" name="hours" value="{{ old('hours', $setting->hours) }}" placeholder="Mon – Sat, 10am – 8pm" maxlength="160"></label>
        <label class="field-label">NEXT EID DATE <span class="field-hint">FOR SEASONAL REMINDERS</span><input class="field-control" type="date" name="next_eid_date" value="{{ old('next_eid_date', $setting->next_eid_date?->toDateString()) }}">@error('next_eid_date')<span class="field-error">{{ $message }}</span>@enderror</label>
        <button class="button button-dark" type="submit">Save business details <span aria-hidden="true">→</span></button>
    </form>
</div>
@endsection
