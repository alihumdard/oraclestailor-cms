@extends('layouts.tailor')

@section('title', 'Saim Tailors · Made to fit you')
@section('body-class', 'booking-shell')

@php
    $shirtFields = \App\Models\TailorOrder::SHIRT_FIELDS;
    $trouserFields = \App\Models\TailorOrder::TROUSER_FIELDS;
    $inchOptions = \App\Models\TailorOrder::inchOptions();
    $genders = \App\Models\TailorOrder::GENDERS;
    $garments = \App\Models\TailorOrder::GARMENTS;
    $packages = \App\Models\TailorOrder::PACKAGES;
    $fabrics = \App\Models\TailorOrder::FABRICS;
    $oldMeasurements = old('measurements', []);
    $hasMeasurements = collect($oldMeasurements)->contains(fn ($value) => $value !== null && $value !== '');
    $hasBookingErrors = $errors->hasAny(['customer_name', 'phone', 'email', 'gender', 'garment', 'package', 'fabric', 'standard_size', 'measurement_unit', 'measurements', 'style_notes', 'due_date']);
@endphp

@section('content')
<div class="tailor-site">
    <header class="site-header">
        <a class="site-brand" href="{{ route('home') }}" aria-label="Saim Tailors home">
            <img src="{{ asset('saim-tailorlogo.webp') }}" alt="Saim Tailors">
            <span class="brand-word">SAIM <small>GENIUS TAILOR</small></span>
        </a>
        <button class="site-menu-toggle" type="button" aria-expanded="false" aria-controls="site-navigation" aria-label="Toggle menu" data-site-menu><x-icon name="menu" class="icon icon-menu-open"/><x-icon name="close" class="icon icon-menu-close"/></button>
        <nav class="site-navigation" id="site-navigation" aria-label="Main navigation">
            <a href="#services">What we make</a>
            <a href="#track">Track an order</a>
            <a href="#faq">FAQ</a>
            <a href="#contact">Talk to Saim</a>
            
            @if(auth()->check())
                <a class="site-nav-login" href="{{ route('dashboard') }}">Workroom</a>
            @elseif ($authCustomer)
                <a class="site-nav-login" href="{{ route('customer.dashboard') }}">My account</a>
            @else
                <a class="site-nav-login" href="{{ route('customer.login') }}">Customer login</a>
                <a class="site-nav-login" href="{{ route('customer.register') }}">Register</a>
                <a class="site-nav-login site-nav-login-admin" href="{{ route('login') }}">Workroom login</a>
            @endif
            
            <a class="site-nav-cta" href="#fitting">Book a fitting <span aria-hidden="true">↗</span></a>
        </nav>
    </header>

    <main>
        <section class="tailor-hero" aria-labelledby="hero-title" style="background-image: url('{{ asset('images/hero-tailor.jpg') }}')">
            <div class="hero-copy">
                <span class="hero-overline">THE BESPOKE TAILOR</span>
                <h1 id="hero-title">Crafting your<br>perfect <em>fit.</em></h1>
                <p>Experience the art of custom tailoring. Thoughtful fabric, precision fit, and a finish made to last — stitched personally by Saim.</p>
                <a class="hero-cta" href="#fitting">Book your consultation <span aria-hidden="true">→</span></a>
                <div class="hero-trust"><x-icon name="sparkle"/> ONE TAILOR. PERSONAL ATTENTION. ALWAYS.</div>
            </div>
            <a class="hero-scroll" href="#services"><span>01</span> A LITTLE ABOUT THE ATELIER <i>↓</i></a>
        </section>

        <section class="services-section" id="services">
            <header class="section-intro">
                <div><span class="eyebrow">MADE FOR THE WAY YOU LIVE</span><h2>One tailor.<br><em>Every detail.</em></h2></div>
                <p>No racks of ready-mades. No strangers measuring you. Just a one-to-one experience, thoughtful fabric, and a fit that feels like it was always yours.</p>
            </header>
            <div class="service-grid">
                <article class="service-card"><span class="service-number">01</span><span class="service-icon" aria-hidden="true"><x-icon name="ruler"/></span><h3>Made-to-measure</h3><p>Shalwar kameez, two- and three-piece suits, waistcoats, sherwanis, and blazers—cut for the way you actually wear them.</p><a href="#fitting">Find your fit <span>→</span></a></article>
                <article class="service-card"><span class="service-number">02</span><span class="service-icon" aria-hidden="true"><x-icon name="sparkle"/></span><h3>Your fabric, your say</h3><p>Choose lawn, cotton, linen, karandi, suiting—or bring your own cloth. Pick the finish and make it distinctly yours.</p><a href="#fitting">Choose your fabric <span>→</span></a></article>
                <article class="service-card"><span class="service-number">03</span><span class="service-icon" aria-hidden="true"><x-icon name="calendar"/></span><h3>A fitting, not a guess</h3><p>Share a standard size or your measurements. Saim follows up personally to confirm the little things before stitching begins.</p><a href="#fitting">Start a conversation <span>→</span></a></article>
            </div>
        </section>

        <section class="fitting-section" id="fitting">
            <div class="fitting-intro"><span class="eyebrow">YOUR VERY OWN SAIM TAILORS FITTING</span><h2>Something lovely<br>starts <em>with you.</em></h2><p>A few thoughtful details are all we need to get started. Your perfect fit is in good hands.</p><span class="fitting-note"><x-icon name="sparkle"/> NO OBLIGATION. SAIM WILL CONFIRM YOUR ORDER PERSONALLY.</span></div>
            <div class="fitting-layout">
                <section class="booking-form-wrap" aria-label="Personal fitting request">
                    @if ($authCustomer)
                        <div class="gate-card">
                            <span class="eyebrow">SIGNED IN AS {{ strtoupper($authCustomer->name) }}</span>
                            <h3>Ready for your next order?</h3>
                            <p>Head to your account to share the garment, fabric and fit — Saim will confirm the rest personally.</p>
                            <a class="button button-dark" href="{{ route('customer.order.create') }}">Request a new order <span aria-hidden="true">→</span></a>
                        </div>
                    @else
                        <div class="gate-card">
                            <span class="eyebrow">ONE ACCOUNT, EVERY FITTING</span>
                            <h3>Sign in to book your fitting.</h3>
                            <p>Create a free account to request fittings, save your measurements, and track every order in one place.</p>
                            <div class="gate-actions">
                                <a class="button button-dark" href="{{ route('customer.register') }}">Create an account <span aria-hidden="true">→</span></a>
                                <a class="button button-light" href="{{ route('customer.login') }}">I already have one</a>
                            </div>
                        </div>
                    @endif
                </section>
                <aside class="booking-aside"><div class="aside-pin" aria-hidden="true"><x-icon name="sparkle"/></div><span class="eyebrow">A NOTE FROM THE WORKROOM</span><blockquote>“The best clothes don't just fit your measurements. They fit <em>your life.</em>”</blockquote><div class="aside-divider"></div><div class="aside-promise"><span>01</span><p><strong>A real person, always.</strong><br>Saim will personally follow up to confirm your order.</p></div><div class="aside-promise"><span>02</span><p><strong>Your measurements stay yours.</strong><br>Only our atelier sees your fitting details.</p></div><div class="aside-promise"><span>03</span><p><strong>Thoughtful from first stitch.</strong><br>We'll help with sizing, fabric and each detail.</p></div><p class="aside-tailoring-note">MADE SLOWLY. WORN JOYFULLY. <x-icon name="sparkle"/></p></aside>
            </div>
        </section>

        <section class="track-section" id="track">
            <div class="track-intro"><span class="eyebrow">A LITTLE UPDATE FROM THE WORKROOM</span><h2>Your order, <em>at a glance.</em></h2><p>Your reference is on the slip Saim shared with you. Enter it with your phone number to see where your garment is in its journey.</p></div>
            <div class="track-card">
                <span class="track-icon" aria-hidden="true"><x-icon name="ruler"/></span>
                <form action="{{ route('orders.track') }}" method="POST" class="track-form">
                    @csrf
                    <label class="field-label">ORDER REFERENCE<input class="field-control" type="text" name="order_reference" value="{{ old('order_reference') }}" placeholder="SAIM-00001" maxlength="20" required></label>
                    <label class="field-label">YOUR PHONE NUMBER<input class="field-control" type="tel" name="phone" value="{{ old('phone') }}" placeholder="The number you booked with" maxlength="30" required></label>
                    <button class="button button-dark" type="submit">Find my order <span>→</span></button>
                </form>
                @if (session('tracking_error'))<p class="tracking-error" role="alert">{{ session('tracking_error') }}</p>@endif
                @if (session('tracked_order'))
                    @php($trackedOrder = session('tracked_order'))
                    <div class="tracking-result" role="status">
                        <div><span class="eyebrow">A GOOD UPDATE</span><h3>{{ $trackedOrder['reference'] }} <span>·</span> {{ $trackedOrder['garment'] }}</h3></div>
                        <x-order-progress :status="$trackedOrder['status']"/>
                        <p>{{ $trackedOrder['status'] === 'delivered' ? 'Your garment has been delivered. Thank you for choosing Saim.' : 'Your next fitting or collection is planned for '.$trackedOrder['due_date'].'. Saim will be in touch if anything changes.' }}</p>
                    </div>
                @endif
            </div>
            <p class="track-privacy">Your order details can only be viewed with both your reference and the phone number on your fitting.</p>
        </section>

        <section class="faq-section" id="faq">
            <header class="faq-intro"><span class="eyebrow">BEFORE YOU BOOK</span><h2>A few things <em>people ask.</em></h2><p>Can't find your answer here? Send Saim a note below.</p></header>
            <div class="faq-list">
                <details class="faq-item" open><summary>How long does a made-to-measure order take?</summary><p>Most shalwar kameez and two-piece orders are ready in 7–14 days. Heavier occasion wear like sherwanis and three-piece suits usually takes 2–4 weeks depending on fabric availability. Saim will confirm your exact fitting date when he reviews your request.</p></details>
                <details class="faq-item"><summary>Can I bring my own fabric?</summary><p>Yes. Choose "My own fabric" when booking and bring it to your fitting. Saim will check the quantity is enough for your garment and talk you through the cut before stitching begins.</p></details>
                <details class="faq-item"><summary>What if I don't know my exact measurements?</summary><p>No problem — pick an approximate size (XS to 3XL) when booking, or leave the measurement fields blank. Saim will take your exact measurements personally at your first fitting.</p></details>
                <details class="faq-item"><summary>How does payment work?</summary><p>Prices are agreed directly with Saim once your garment, fabric and finish are confirmed. You can pay a partial advance to start the order, with the remaining balance due on collection.</p></details>
                <details class="faq-item"><summary>Can I change or cancel my order?</summary><p>Yes, as long as stitching hasn't started. Message Saim through the contact form or call him directly, and he'll confirm the next steps with you.</p></details>
                <details class="faq-item"><summary>Do you stitch for women and kids too?</summary><p>Yes — select the gender when booking and Saim will tailor the cut, sizing and finish accordingly.</p></details>
            </div>
        </section>

        <section class="contact-section" id="contact">
            <div class="contact-copy"><span class="eyebrow">A QUESTION BEFORE YOU BEGIN?</span><h2>Real conversations.<br><em>Real good tailoring.</em></h2><p>Ask Saim about a garment, a fitting, your own fabric, or anything else on your mind. Your note goes straight to the workroom.</p><span class="contact-signature">Warmly, <span>Saim</span> <x-icon name="sparkle"/></span></div>
            <div class="contact-card">
                @if (session('inquiry_sent'))<div class="notice notice-success" role="status">Thanks for reaching out. Your note is in Saim’s workroom; he’ll follow up personally.</div>@endif
                <form action="{{ route('contact.store') }}" method="POST" class="contact-form">
                    @csrf
                    <label class="field-label">YOUR NAME<input class="field-control" name="name" value="{{ old('name') }}" placeholder="The name we’ll know you by" maxlength="120" required>@error('name')<span class="field-error">{{ $message }}</span>@enderror</label>
                    <label class="field-label">PHONE NUMBER<input class="field-control" name="phone" type="tel" value="{{ old('phone') }}" placeholder="+92 300 1234567" maxlength="30" required>@error('phone')<span class="field-error">{{ $message }}</span>@enderror</label>
                    <label class="field-label">WHAT'S ON YOUR MIND?<textarea class="field-control contact-message" name="message" rows="3" maxlength="1500" placeholder="Tell Saim how he can help…" required>{{ old('message') }}</textarea>@error('message')<span class="field-error">{{ $message }}</span>@enderror</label>
                    <button class="button button-dark" type="submit">Send a note to Saim <span>→</span></button>
                </form>
            </div>
        </section>

        <section class="last-word"><div><span class="eyebrow">READY WHEN YOU ARE</span><strong>One good fit can change how a whole day feels.</strong></div><a class="button button-dark" href="#fitting">Place an order <span>→</span></a></section>
    </main>

    <footer class="site-footer">
        <div class="footer-grid">
            <div class="footer-col footer-col-brand">
                <a href="{{ route('home') }}" class="footer-brand"><img src="{{ asset('saim-tailorlogo.webp') }}" alt="Saim Tailors"><span class="brand-word">SAIM <small>GENIUS TAILOR</small></span></a>
                <p class="footer-tagline">One tailor. Every detail. Made-to-measure tailoring, stitched with personal care.</p>
                <p class="footer-address"><x-icon name="pin"/> {{ $businessSetting->full_address }}</p>
            </div>
            <nav class="footer-col" aria-label="Quick links">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="#fitting">Book Appointment</a></li>
                    <li><a href="#track">Track Your Order</a></li>
                    <li><a href="#faq">FAQ</a></li>
                    <li><a href="#contact">Contact Us</a></li>
                </ul>
            </nav>
            <nav class="footer-col" aria-label="Our services">
                <h4>Our Services</h4>
                <ul>
                    @foreach (\App\Models\TailorOrder::GARMENTS as $garment)<li><a href="#fitting">{{ $garment }}</a></li>@endforeach
                </ul>
            </nav>
            <div class="footer-col">
                <h4>Get in Touch</h4>
                <ul class="footer-contact-list">
                    @if ($businessSetting->phone)<li><a href="tel:{{ $businessSetting->phone }}"><x-icon name="phone"/> {{ $businessSetting->phone }}</a></li>@endif
                    @if ($businessSetting->email)<li><a href="mailto:{{ $businessSetting->email }}"><x-icon name="mail"/> {{ $businessSetting->email }}</a></li>@endif
                    <li><span><x-icon name="pin"/> {{ $businessSetting->city }}, {{ $businessSetting->country }}</span></li>
                    @if ($businessSetting->hours)<li><span><x-icon name="clock"/> {{ $businessSetting->hours }}</span></li>@endif
                    <li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
                    <li><a href="{{ route('terms') }}">Terms &amp; Conditions</a></li>
                    <li><a href="{{ route('returns') }}">Return &amp; Exchange Policy</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-meta">
            <span>© {{ now()->year }} SAIM TAILORS · MADE WITH CARE.</span>
            @if(!auth()->check() && !auth('customer')->check())
                <a href="{{ route('login') }}">WORKROOM SIGN IN <span>↗</span></a>
            @endif
        </div>
    </footer>
</div>

@if ($businessSetting->phone)
    <a class="whatsapp-float" id="whatsapp-float" href="https://wa.me/{{ preg_replace('/\D/', '', $businessSetting->phone) }}" target="_blank" rel="noopener" aria-label="Chat with Saim on WhatsApp">
        <x-icon name="whatsapp"/>
    </a>
    <script>
        (function () {
            var btn = document.getElementById('whatsapp-float');
            if (!btn) return;
            var reveal = function () {
                if (window.scrollY > 300) {
                    btn.classList.add('is-visible');
                } else {
                    btn.classList.remove('is-visible');
                }
            };
            window.addEventListener('scroll', reveal, { passive: true });
            reveal();
        })();
    </script>
@endif
@endsection
