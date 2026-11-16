@extends('layouts.tailor')

@section('body-class', 'studio-shell')

@section('content')
<div class="app-layout">
    <div class="sidebar-scrim" data-close-menu></div>
    <aside class="sidebar" id="studio-sidebar">
        <a class="sidebar-brand" href="{{ route('dashboard') }}">
            <img src="{{ asset('saim-tailorlogo.webp') }}" alt="Saim Tailors">
            <span class="sidebar-wordmark">SAIM<span>WORKROOM</span></span>
        </a>
        <p class="sidebar-caption">ADMIN</p>
        <nav class="sidebar-nav">
            <a class="nav-link @if (request()->routeIs('dashboard')) is-active @endif" href="{{ route('dashboard') }}"><span class="nav-symbol"><x-icon name="grid"/></span> Dashboard</a>
            <a class="nav-link @if (request()->routeIs('customers.*')) is-active @endif" href="{{ route('customers.index') }}"><span class="nav-symbol"><x-icon name="user"/></span> Customers</a>
            <a class="nav-link @if (request()->routeIs('board')) is-active @endif" href="{{ route('board') }}"><span class="nav-symbol"><x-icon name="grid"/></span> Board</a>
            <a class="nav-link @if (request()->routeIs('inventory.*')) is-active @endif" href="{{ route('inventory.index') }}"><span class="nav-symbol"><x-icon name="ruler"/></span> Inventory</a>
            <a class="nav-link @if (request()->routeIs('reports')) is-active @endif" href="{{ route('reports') }}"><span class="nav-symbol"><x-icon name="calendar"/></span> Reports</a>
            <a class="nav-link @if (request()->routeIs('settings')) is-active @endif" href="{{ route('settings') }}"><span class="nav-symbol"><x-icon name="gear"/></span> Business settings</a>
            <a class="nav-link @if (request()->routeIs('users.*')) is-active @endif" href="{{ route('users.index') }}"><span class="nav-symbol"><x-icon name="users"/></span> Team &amp; users</a>
        </nav>
        <div class="sidebar-bottom">
            <div class="stitch-card">“Good work, one stitch at a time.”<span class="stitch-icon" aria-hidden="true"><x-icon name="sparkle"/></span><span class="stitch-note">SAIM TAILORS</span></div>
            <div class="sidebar-profile">
                <span class="profile-avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                <div class="profile-detail"><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->email }}</small></div>
                <form action="{{ route('logout') }}" method="POST">@csrf<button class="logout-button" type="submit" aria-label="Sign out"><x-icon name="logout"/></button></form>
            </div>
        </div>
    </aside>
    <div class="main-content">
        <div class="topbar">
            <div class="breadcrumb"><strong>@yield('page-title', 'Workroom')</strong></div>
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
<nav class="bottom-tabbar" aria-label="Workroom navigation">
    <a class="tab-link @if (request()->routeIs('dashboard')) is-active @endif" href="{{ route('dashboard') }}"><x-icon name="grid"/><span>Dashboard</span></a>
    <a class="tab-link @if (request()->routeIs('customers.*')) is-active @endif" href="{{ route('customers.index') }}"><x-icon name="user"/><span>Customers</span></a>
    <a class="tab-link @if (request()->routeIs('settings')) is-active @endif" href="{{ route('settings') }}"><x-icon name="gear"/><span>Settings</span></a>
    <a class="tab-link @if (request()->routeIs('users.*')) is-active @endif" href="{{ route('users.index') }}"><x-icon name="users"/><span>Team</span></a>
    <a class="tab-link" href="{{ route('home') }}" target="_blank" rel="noopener"><x-icon name="pin"/><span>Site</span></a>
</nav>
@endsection
