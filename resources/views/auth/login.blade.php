@extends('layouts.tailor')

@section('title', 'Sign in')
@section('body-class', 'auth-shell')

@section('content')
<main class="auth-wrap">
    <a class="auth-brand" href="{{ route('login') }}">
        <img src="{{ asset('saim-tailorlogo.webp') }}" alt="Saim Tailors logo">
        <span>THE ATELIER <span>·</span> EST. WITH CARE</span>
    </a>
    <section class="auth-card">
        <span class="eyebrow">YOUR WORKROOM</span>
        <h1>Welcome back.</h1>
        <p>Sign in to pick up where you left off.</p>
        @if (session('success'))<div class="notice notice-success" role="status">{{ session('success') }}</div>@endif
        <form action="{{ route('login.store') }}" method="POST" class="form-stack">
            @csrf
            <label class="field-label">Email address<input class="field-control" type="email" name="email" value="{{ old('email') }}" placeholder="you@youratelier.com" autocomplete="email" required autofocus>@error('email')<span class="field-error">{{ $message }}</span>@enderror</label>
            <label class="field-label">Password<span class="password-input-wrap"><input class="field-control" type="password" name="password" placeholder="Your password" autocomplete="current-password" required><button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false" data-password-toggle><x-icon name="eye" class="icon icon-eye-closed"/><x-icon name="eye-off" class="icon icon-eye-open"/></button></span>@error('password')<span class="field-error">{{ $message }}</span>@enderror</label>
            <label class="remember-label"><input type="checkbox" name="remember" value="1"> Keep me signed in</label>
            <button class="button button-dark button-wide" type="submit">Enter the studio <span aria-hidden="true">→</span></button>
        </form>
        @unless (\App\Models\User::exists())
            <div class="account-setup-prompt"><span>FIRST TIME HERE?</span><a href="{{ route('setup') }}">Set up your studio <span aria-hidden="true">→</span></a></div>
        @endunless
    </section>
    <p class="auth-foot">A little more personal, by design. <x-icon name="sparkle"/> Saim Tailors</p>
</main>
@endsection
