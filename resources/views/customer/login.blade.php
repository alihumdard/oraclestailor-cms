@extends('layouts.tailor')

@section('title', 'Sign in to your account')
@section('body-class', 'auth-shell')

@section('content')
<main class="auth-wrap">
    <a class="auth-brand" href="{{ route('home') }}">
        <img src="{{ asset('saim-tailorlogo.webp') }}" alt="Saim Tailors logo">
        <span>SAIM TAILORS <span>·</span> MADE FOR YOU</span>
    </a>
    <section class="auth-card">
        <span class="eyebrow">YOUR ACCOUNT</span>
        <h1>Welcome back.</h1>
        <p>Sign in to track your orders or book a new fitting.</p>
        @if (session('success'))<div class="notice notice-success" role="status">{{ session('success') }}</div>@endif
        <form action="{{ route('customer.login.store') }}" method="POST" class="form-stack">
            @csrf
            <label class="field-label">Email address or phone number<input class="field-control" type="text" name="login_identifier" value="{{ old('login_identifier') }}" placeholder="you@example.com or +1234567890" required autofocus>@error('login_identifier')<span class="field-error">{{ $message }}</span>@enderror</label>
            <label class="field-label">Password<span class="password-input-wrap"><input class="field-control" type="password" name="password" placeholder="Your password" autocomplete="current-password" required><button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false" data-password-toggle><x-icon name="eye" class="icon icon-eye-closed"/><x-icon name="eye-off" class="icon icon-eye-open"/></button></span>@error('password')<span class="field-error">{{ $message }}</span>@enderror</label>
            <label class="remember-label"><input type="checkbox" name="remember" value="1"> Keep me signed in</label>
            <button class="button button-dark button-wide" type="submit">Sign in <span aria-hidden="true">→</span></button>
        </form>
        <div class="account-setup-prompt"><span>NEW HERE?</span><a href="{{ route('customer.register') }}">Create an account <span aria-hidden="true">→</span></a></div>
    </section>
    <p class="auth-foot">A little more personal, by design. <x-icon name="sparkle"/> Saim Tailors</p>
</main>
@endsection
