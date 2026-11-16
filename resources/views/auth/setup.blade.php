@extends('layouts.tailor')

@section('title', 'Set up your studio')
@section('body-class', 'auth-shell')

@section('content')
<main class="auth-wrap">
    <a class="auth-brand" href="{{ route('setup') }}">
        <img src="{{ asset('saim-tailorlogo.webp') }}" alt="Saim Tailors logo">
        <span>THE ATELIER <span>·</span> EST. WITH CARE</span>
    </a>
    <section class="auth-card">
        <span class="eyebrow">A FRESHLY PRESSED START</span>
        <h1>Make it yours.</h1>
        <p>Set up the private owner account for your Saim Tailors workroom.</p>
        <form action="{{ route('setup.store') }}" method="POST" class="form-stack">
            @csrf
            <label class="field-label">Your name<input class="field-control" type="text" name="name" value="{{ old('name') }}" placeholder="Saim" autocomplete="name" required autofocus>@error('name')<span class="field-error">{{ $message }}</span>@enderror</label>
            <label class="field-label">Email address<input class="field-control" type="email" name="email" value="{{ old('email') }}" placeholder="you@youratelier.com" autocomplete="email" required>@error('email')<span class="field-error">{{ $message }}</span>@enderror</label>
            <label class="field-label">Password <span class="field-hint">At least 12 characters</span><span class="password-input-wrap"><input class="field-control" type="password" name="password" placeholder="Make it a good one" autocomplete="new-password" minlength="12" required><button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false" data-password-toggle><x-icon name="eye" class="icon icon-eye-closed"/><x-icon name="eye-off" class="icon icon-eye-open"/></button></span>@error('password')<span class="field-error">{{ $message }}</span>@enderror</label>
            <label class="field-label">Confirm password<span class="password-input-wrap"><input class="field-control" type="password" name="password_confirmation" placeholder="One more time" autocomplete="new-password" minlength="12" required><button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false" data-password-toggle><x-icon name="eye" class="icon icon-eye-closed"/><x-icon name="eye-off" class="icon icon-eye-open"/></button></span></label>
            <button class="button button-dark button-wide" type="submit">Open my workroom <span aria-hidden="true">→</span></button>
        </form>
        <p class="auth-security">Your dashboard and customer records stay behind your sign-in.</p>
    </section>
    <p class="auth-foot">A little more personal, by design. <x-icon name="sparkle"/> Saim Tailors</p>
</main>
@endsection
