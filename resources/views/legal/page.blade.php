@extends('layouts.tailor')

@section('title', $title)
@section('body-class', 'booking-shell')

@section('content')
<div class="tailor-site">
    <header class="site-header">
        <a class="site-brand" href="{{ route('home') }}" aria-label="Saim Tailors home">
            <img src="{{ asset('saim-tailorlogo.webp') }}" alt="Saim Tailors">
            <span class="brand-word">SAIM <small>GENIUS TAILOR</small></span>
        </a>
        <nav class="site-navigation" id="site-navigation" aria-label="Main navigation">
            <a href="{{ route('home') }}">Back to home</a>
        </nav>
    </header>
    <main>
        <section class="legal-page">
            <a class="back-link" href="{{ route('home') }}">← Back to home</a>
            {!! $content !!}
        </section>
    </main>
    <footer class="site-footer">
        <div class="footer-meta"><span>© {{ now()->year }} {{ $businessSetting->business_name }} · MADE WITH CARE.</span>@if(!auth()->check())<a href="{{ route('login') }}">WORKROOM SIGN IN <span>↗</span></a>@endif</div>
    </footer>
</div>
@endsection
