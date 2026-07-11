{{-- resources/views/about.blade.php --}}
@extends('layout')
@section('title','About Us — OmniPrice')
@section('content')
<div style="background: radial-gradient(ellipse 60% 40% at 50% 0%, rgba(255,75,43,.15) 0%, transparent 60%);padding:70px 0 20px">
<div class="container" style="max-width:800px">
    <h1 class="section-title mb-2">About <span style="background:var(--gradient);-webkit-background-clip:text;-webkit-text-fill-color:transparent">OmniPrice</span></h1>
    <p style="color:var(--brand-muted);margin-bottom:48px">Pakistan's unified price comparison platform</p>

    <div class="product-card p-4 mb-4">
        <h4 style="font-family:var(--font-head);margin-bottom:12px">Our Mission</h4>
        <p style="color:var(--brand-muted)">OmniPrice was built to save Pakistani shoppers time and money. Instead of checking PriceOye, Daraz, and Naheed separately, we search all three simultaneously and show you every result sorted from cheapest to most expensive in one unified view.</p>
    </div>

    <div class="product-card p-4 mb-4">
        <h4 style="font-family:var(--font-head);margin-bottom:16px">What We Compare</h4>
        <div class="row g-3">
            <div class="col-md-4">
                <div style="background:var(--brand-border);border-radius:10px;padding:16px;text-align:center">
                    <div style="font-family:var(--font-head);font-weight:700;color:#4fa3e0;font-size:1.1rem">PriceOye</div>
                    <div style="color:var(--brand-muted);font-size:.85rem;margin-top:6px">Electronics & Gadgets</div>
                </div>
            </div>
            <div class="col-md-4">
                <div style="background:var(--brand-border);border-radius:10px;padding:16px;text-align:center">
                    <div style="font-family:var(--font-head);font-weight:700;color:#f05a28;font-size:1.1rem">Daraz</div>
                    <div style="color:var(--brand-muted);font-size:.85rem;margin-top:6px">Everything</div>
                </div>
            </div>
            <div class="col-md-4">
                <div style="background:var(--brand-border);border-radius:10px;padding:16px;text-align:center">
                    <div style="font-family:var(--font-head);font-weight:700;color:#4caf50;font-size:1.1rem">Naheed</div>
                    <div style="color:var(--brand-muted);font-size:.85rem;margin-top:6px">Electronics & Home</div>
                </div>
            </div>
        </div>
    </div>

    <div class="product-card p-4 mb-4">
        <h4 style="font-family:var(--font-head);margin-bottom:12px">How Prices Stay Accurate</h4>
        <p style="color:var(--brand-muted)">Every search triggers a live API call to each store in real-time. Prices are never cached for more than a session, so what you see is what the store currently charges — not outdated data.</p>
    </div>

    <div class="text-center mt-5">
        <a href="/" class="btn btn-brand px-5 py-3">Start Comparing Prices</a>
    </div>
</div>
</div>
@endsection