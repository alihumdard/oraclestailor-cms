@extends('layouts.tailor')

@section('title', 'Create your account')
@section('body-class', 'auth-shell')

@section('content')
<main class="auth-wrap">
    <a class="auth-brand" href="{{ route('home') }}">
        <img src="{{ asset('saim-tailorlogo.webp') }}" alt="Saim Tailors logo">
        <span>SAIM TAILORS <span>·</span> MADE FOR YOU</span>
    </a>
    <section class="auth-card">
        <span class="eyebrow">JOIN THE WORKROOM</span>
        <h1>Create your account.</h1>
        <p>One account to book fittings and track every order.</p>
        <form action="{{ route('customer.register.store') }}" method="POST" class="form-stack">
            @csrf
            <label class="field-label">Your name<input class="field-control" type="text" name="name" value="{{ old('name') }}" placeholder="The name we'll know you by" autocomplete="name" required autofocus>@error('name')<span class="field-error">{{ $message }}</span>@enderror</label>
            <fieldset class="booking-fieldset gender-fieldset"><legend class="field-legend">WHO IS THIS FOR? <span class="required-mark">*</span></legend>
                <div class="gender-grid">@foreach ($genders as $genderKey => $genderLabel)<label class="size-choice"><input type="radio" name="gender" value="{{ $genderKey }}" @checked(old('gender', 'male') === $genderKey) required><span>{{ $genderLabel }}</span></label>@endforeach</div>
                @error('gender')<span class="field-error">{{ $message }}</span>@enderror
            </fieldset>
            <label class="field-label">Phone number<input class="field-control" type="tel" name="phone" value="{{ old('phone') }}" placeholder="+92 300 1234567" autocomplete="tel" required>@error('phone')<span class="field-error">{{ $message }}</span>@enderror</label>
            <label class="field-label">Email <span class="field-hint">OPTIONAL</span><input class="field-control" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email">@error('email')<span class="field-error">{{ $message }}</span>@enderror</label>
            <label class="field-label">Password<span class="password-input-wrap"><input class="field-control" type="password" name="password" placeholder="At least 8 characters" autocomplete="new-password" minlength="8" required><button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false" data-password-toggle><x-icon name="eye" class="icon icon-eye-closed"/><x-icon name="eye-off" class="icon icon-eye-open"/></button></span>@error('password')<span class="field-error">{{ $message }}</span>@enderror</label>
            <label class="field-label">Confirm password<span class="password-input-wrap"><input class="field-control" type="password" name="password_confirmation" placeholder="Type it again" autocomplete="new-password" minlength="8" required><button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false" data-password-toggle><x-icon name="eye" class="icon icon-eye-closed"/><x-icon name="eye-off" class="icon icon-eye-open"/></button></span></label>
            <button class="button button-dark button-wide" type="submit">Create my account <span aria-hidden="true">→</span></button>
        </form>
        <div class="account-setup-prompt"><span>ALREADY HAVE AN ACCOUNT?</span><a href="{{ route('customer.login') }}">Sign in <span aria-hidden="true">→</span></a></div>
    </section>
    <p class="auth-foot">A little more personal, by design. <x-icon name="sparkle"/> Saim Tailors</p>
</main>
@endsection
