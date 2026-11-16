@extends('layouts.admin')

@section('title', 'Customers')
@section('page-title', 'Customers')

@section('page')
<div class="page-heading-row">
    <div><span class="eyebrow">EVERYONE YOU'VE FITTED</span><h1>Customers<span class="heading-dot">.</span></h1><p class="page-intro">Search, review saved fits, and see how many orders each customer has placed.</p></div>
</div>

<form action="{{ route('customers.index') }}" method="GET" class="customer-search-form" data-customer-search-form>
    <input class="field-control" type="search" name="q" value="{{ $search }}" placeholder="Search by name, phone or email…" data-customer-search-input autocomplete="off">
    <button class="button button-dark" type="submit">Search</button>
    @if ($search !== '')
        <a class="button button-light" href="{{ route('customers.index') }}">Clear</a>
    @endif
</form>

<div data-customer-results>
    @include('tailor.partials.customers-table')
</div>

<script>
    (function () {
        const form = document.querySelector('[data-customer-search-form]');
        const input = document.querySelector('[data-customer-search-input]');
        const results = document.querySelector('[data-customer-results]');
        if (!form || !input || !results) return;

        let timer = null;
        let controller = null;

        const runSearch = () => {
            if (controller) controller.abort();
            controller = new AbortController();

            const url = new URL(form.action);
            url.searchParams.set('q', input.value);

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, signal: controller.signal })
                .then((response) => response.text())
                .then((html) => {
                    results.innerHTML = html;
                    window.history.replaceState({}, '', url);
                })
                .catch((error) => {
                    if (error.name !== 'AbortError') throw error;
                });
        };

        input.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(runSearch, 150);
        });

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            clearTimeout(timer);
            runSearch();
        });
    })();
</script>
@endsection
