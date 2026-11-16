@extends('layouts.tailor')

@section('body-class', 'studio-shell')

@section('content')
<div class="app-layout">
    <div class="sidebar-scrim" data-close-menu></div>
    <aside class="sidebar" id="studio-sidebar">
        <a class="sidebar-brand" href="{{ route('customer.dashboard') }}">
            <img src="{{ asset('saim-tailorlogo.webp') }}" alt="Saim Tailors">
            <span class="sidebar-wordmark">SAIM<span>MY ACCOUNT</span></span>
        </a>
        <p class="sidebar-caption">CUSTOMER</p>
        <nav class="sidebar-nav">
            <a class="nav-link @if (request()->routeIs('customer.dashboard')) is-active @endif" href="{{ route('customer.dashboard') }}"><span class="nav-symbol"><x-icon name="grid"/></span> Dashboard</a>
            <a class="nav-link @if (request()->routeIs('customer.order.create')) is-active @endif" href="{{ route('customer.order.create') }}"><span class="nav-symbol"><x-icon name="shirt"/></span> New order</a>
            <a class="nav-link" href="{{ route('home') }}"><span class="nav-symbol"><x-icon name="pin"/></span> Visit the website</a>
        </nav>
        <div class="sidebar-bottom">
            <div class="stitch-card">“Your fittings, your pace.”<span class="stitch-icon" aria-hidden="true"><x-icon name="sparkle"/></span><span class="stitch-note">SAIM TAILORS</span></div>
            <div class="sidebar-profile">
                <span class="profile-avatar">{{ mb_strtoupper(mb_substr(auth('customer')->user()->name, 0, 1)) }}</span>
                <div class="profile-detail"><strong>{{ auth('customer')->user()->name }}</strong><small>{{ auth('customer')->user()->phone }}</small></div>
                <form action="{{ route('customer.logout') }}" method="POST">@csrf<button class="logout-button" type="submit" aria-label="Sign out"><x-icon name="logout"/></button></form>
            </div>
        </div>
    </aside>
    <div class="main-content">
        <div class="topbar">
            <div class="breadcrumb"><strong>@yield('page-title', 'My account')</strong></div>
            <div class="topbar-right">
                <button class="mobile-menu" type="button" data-mobile-menu aria-label="Open menu" aria-expanded="false"><x-icon name="menu"/></button>
                <span class="today-date">{{ now()->format('d M, Y') }}</span>
                <a class="online-dot" href="{{ route('home') }}" target="_blank" rel="noopener"><i></i> VIEW SITE</a>
            </div>
        </div>
        <div class="page-content">
            @if (session('success'))<div class="notice notice-success" role="status">{{ session('success') }}<button type="button" data-dismiss aria-label="Dismiss">×</button></div>@endif
            @if (session('error'))<div class="notice notice-error" role="alert">{{ session('error') }}<button type="button" data-dismiss aria-label="Dismiss">×</button></div>@endif
            @yield('page')
        </div>
    </div>
</div>
<nav class="bottom-tabbar" aria-label="Account navigation">
    <a class="tab-link @if (request()->routeIs('customer.dashboard')) is-active @endif" href="{{ route('customer.dashboard') }}"><x-icon name="grid"/><span>Dashboard</span></a>
    <a class="tab-link @if (request()->routeIs('customer.order.create')) is-active @endif" href="{{ route('customer.order.create') }}"><x-icon name="shirt"/><span>New order</span></a>
    <a class="tab-link" href="{{ route('home') }}" target="_blank" rel="noopener"><x-icon name="pin"/><span>Site</span></a>
</nav>
@endsection
