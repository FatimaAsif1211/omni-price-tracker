{{-- resources/views/home.blade.php --}}
@extends('layout')
@section('title','OmniPrice — Compare Prices from PriceOye, Daraz & Naheed')

@section('content')

{{-- ── HERO ──────────────────────────────────────────────────────────────── --}}
<section style="padding:80px 0 60px; background: radial-gradient(ellipse 80% 60% at 50% 0%, rgba(255,75,43,.18) 0%, transparent 70%);">
    <div class="container text-center">
        <p style="color:var(--brand-primary);font-family:var(--font-head);font-weight:600;letter-spacing:2px;font-size:.8rem;text-transform:uppercase;margin-bottom:16px;">
            PriceOye &nbsp;·&nbsp; Daraz &nbsp;·&nbsp; Naheed
        </p>
        <h1 style="font-size:clamp(2.2rem,5vw,3.8rem);font-weight:800;line-height:1.1;margin-bottom:20px;">
            Find The <span style="background:var(--gradient);-webkit-background-clip:text;-webkit-text-fill-color:transparent">Lowest Price</span><br>Across Pakistan
        </h1>
        <p style="color:var(--brand-muted);font-size:1.05rem;max-width:520px;margin:0 auto 36px;">
            One search. Three stores. Every result sorted from cheapest to most expensive — instantly.
        </p>

        {{-- SEARCH BAR --}}
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <div class="input-group search-wrap">
                    <input type="text" id="heroSearch" class="form-control"
                           placeholder="Search mobiles, perfumes, cosmetics, tea…"
                           onkeydown="if(event.key==='Enter') runSearch()">
                    <button class="btn btn-brand" onclick="runSearch()">
                        <i class="bi bi-search me-1"></i> Search
                    </button>
                </div>
            </div>
        </div>

        {{-- QUICK QUICK-SEARCH CHIPS --}}
        <div class="d-flex flex-wrap gap-2 justify-content-center mt-4">
            <span class="cat-chip" onclick="quickSearch('Samsung Galaxy')"><i class="bi bi-phone"></i> Samsung</span>
            <span class="cat-chip" onclick="quickSearch('iPhone')"><i class="bi bi-apple"></i> iPhone</span>
            <span class="cat-chip" onclick="quickSearch('perfume')"><i class="bi bi-droplet-half"></i> Perfumes</span>
            <span class="cat-chip" onclick="quickSearch('shampoo')"><i class="bi bi-capsule-thumbnail"></i> Skincare</span>
            <span class="cat-chip" onclick="quickSearch('tea')"><i class="bi bi-cart4"></i> Groceries</span>
            <span class="cat-chip" onclick="quickSearch('laptop')"><i class="bi bi-laptop"></i> Laptops</span>
            <span class="cat-chip" onclick="quickSearch('wireless earbuds')"><i class="bi bi-headphones"></i> Earbuds</span>
        </div>
    </div>
</section>

{{-- ── STORE TRUST STRIP ────────────────────────────────────────────────── --}}
<section style="border-top:1px solid var(--brand-border);border-bottom:1px solid var(--brand-border);padding:18px 0;background:var(--brand-surface);">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-center align-items-center gap-4">
            <span style="font-size:.8rem;color:var(--brand-muted);text-transform:uppercase;letter-spacing:1px;">Comparing from</span>
            <span style="font-family:var(--font-head);font-weight:700;color:#4fa3e0"><i class="bi bi-check-circle-fill me-1"></i>PriceOye</span>
            <span style="color:var(--brand-border)">|</span>
            <span style="font-family:var(--font-head);font-weight:700;color:#f05a28"><i class="bi bi-check-circle-fill me-1"></i>Daraz</span>
            <span style="color:var(--brand-border)">|</span>
            <span style="font-family:var(--font-head);font-weight:700;color:#4caf50"><i class="bi bi-check-circle-fill me-1"></i>Naheed</span>
        </div>
    </div>
</section>

{{-- ── RESULTS AREA ─────────────────────────────────────────────────────── --}}
<section class="container" style="min-height:300px;padding:48px 12px;">

    {{-- Status message --}}
    <div id="msg" class="d-none mb-4"></div>

    {{-- Spinner --}}
    <div id="spinner" class="text-center py-5 d-none">
        <div class="spinner-border" style="width:2.5rem;height:2.5rem"></div>
        <p class="mt-3" style="color:var(--brand-muted)">Searching PriceOye, Daraz & Naheed simultaneously…</p>
    </div>

    {{-- Results header --}}
    <div id="resultsHeader" class="d-none mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <span class="section-title" style="font-size:1.2rem" id="resultsTitle"></span>
            <span style="color:var(--brand-muted);font-size:.85rem;margin-left:10px" id="resultsCount"></span>
        </div>
        <div class="d-flex gap-2 flex-wrap" id="storeFilters"></div>
    </div>

    {{-- Grid --}}
    <div class="row g-3" id="results"></div>

    {{-- Empty state --}}
    <div id="emptyState" class="d-none text-center py-5">
        <div style="font-size:3rem;margin-bottom:12px">🔍</div>
        <h5 style="font-family:var(--font-head)">No results found</h5>
        <p style="color:var(--brand-muted)">This product may not be available on PriceOye, Daraz, or Naheed.</p>
    </div>
</section>

{{-- ── MIXED TECH & LIFESTYLE CATEGORIES SECTION ───────────────────────── --}}
<section style="background:var(--brand-surface);padding:60px 0;border-top:1px solid var(--brand-border)">
    <div class="container">
        <h2 class="section-title text-center mb-2">Browse by Category</h2>
        <p class="text-center mb-5" style="color:var(--brand-muted)">Jump straight into what you're looking for</p>
        <div class="row g-3 justify-content-center">
            @php
            $cats = [
                ['icon'=>'bi-phone',            'name'=>'Mobiles',     'query'=>'mobile phone',      'color'=>'#4fa3e0'],
                ['icon'=>'bi-laptop',           'name'=>'Laptops',     'query'=>'laptop',            'color'=>'#a78bfa'],
                ['icon'=>'bi-headphones',       'name'=>'Earbuds',     'query'=>'wireless earbuds',  'color'=>'#f472b6'],
                ['icon'=>'bi-droplet-half',     'name'=>'Perfumes',    'query'=>'perfume',           'color'=>'#fb923c'],
                ['icon'=>'bi-capsule-thumbnail','name'=>'Skincare',    'query'=>'shampoo',           'color'=>'#34d399'],
                ['icon'=>'bi-cart4',            'name'=>'Groceries',   'query'=>'tea',               'color'=>'#fbbf24'],
                ['icon'=>'bi-smartwatch',       'name'=>'Watches',     'query'=>'smart watch',       'color'=>'#60a5fa'],
                ['icon'=>'bi-battery-charging', 'name'=>'Power Banks', 'query'=>'power bank',        'color'=>'#f87171'],
            ];
            @endphp
            @foreach($cats as $cat)
            @php
                // Handle matching slug mappings for the view routing structure
                $targetSlug = strtolower(str_replace(' ', '', $cat['name']));
            @endphp
            <div class="col-6 col-sm-4 col-md-3">
                <div class="product-card text-center p-3" style="cursor:pointer" onclick="window.location.href='/category/{{ $targetSlug }}'">
                    <i class="bi {{ $cat['icon'] }}" style="font-size:2rem;color:{{ $cat['color'] }};margin-bottom:10px;display:block"></i>
                    <span style="font-family:var(--font-head);font-weight:600">{{ $cat['name'] }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── HOW IT WORKS ─────────────────────────────────────────────────────── --}}
<section style="padding:70px 0">
    <div class="container">
        <h2 class="section-title text-center mb-5">How OmniPrice Works</h2>
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div style="width:56px;height:56px;border-radius:50%;background:rgba(255,75,43,.15);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <span style="font-size:1.4rem">🔎</span>
                </div>
                <h5 style="font-family:var(--font-head)">1. Type Your Product</h5>
                <p style="color:var(--brand-muted);font-size:.9rem">Enter any product name in the search bar — electronics, cosmetics, or groceries.</p>
            </div>
            <div class="col-md-4">
                <div style="width:56px;height:56px;border-radius:50%;background:rgba(255,75,43,.15);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <span style="font-size:1.4rem">⚡</span>
                </div>
                <h5 style="font-family:var(--font-head)">2. We Search All 3 Stores</h5>
                <p style="color:var(--brand-muted);font-size:.9rem">PriceOye, Daraz, and Naheed are searched simultaneously in real-time.</p>
            </div>
            <div class="col-md-4">
                <div style="width:56px;height:56px;border-radius:50%;background:rgba(255,75,43,.15);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <span style="font-size:1.4rem">💰</span>
                </div>
                <h5 style="font-family:var(--font-head)">3. Get the Lowest Price</h5>
                <p style="color:var(--brand-muted);font-size:.9rem">Results are merged and sorted cheapest first. Click any card to buy directly.</p>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
let allProducts = [];

async function runSearch() {
    const query = document.getElementById('heroSearch').value.trim();
    if (!query) return;
    await doSearch(query);
}

function quickSearch(term) {
    document.getElementById('heroSearch').value = term;
    doSearch(term);
    window.scrollTo({ top: document.getElementById('results').offsetTop - 80, behavior: 'smooth' });
}

async function doSearch(query) {
    const resultsDiv    = document.getElementById('results');
    const spinner       = document.getElementById('spinner');
    const msg           = document.getElementById('msg');
    const header        = document.getElementById('resultsHeader');
    const emptyState    = document.getElementById('emptyState');

    // Reset view templates
    resultsDiv.innerHTML = '';
    msg.className = 'd-none';
    header.className = 'd-none mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2';
    emptyState.className = 'd-none text-center py-5';
    spinner.className = 'text-center py-5';
    allProducts = [];

    try {
        const res  = await fetch(`/search?query=${encodeURIComponent(query)}`);
        const data = await res.json();
        spinner.className = 'text-center py-5 d-none';

        if (data.status === 'error') {
            showMsg(data.message, 'danger'); return;
        }
        if (data.status === 'unavailable' || !data.results?.length) {
            emptyState.className = 'text-center py-5'; return;
        }

        allProducts = data.results;
        renderProducts(allProducts, query);

    } catch(e) {
        spinner.className = 'text-center py-5 d-none';
        showMsg('Something went wrong. Please try again.', 'danger');
    }
}

function renderProducts(products, query) {
    const resultsDiv = document.getElementById('results');
    const header     = document.getElementById('resultsHeader');

    document.getElementById('resultsTitle').textContent = `Results for "${query}"`;
    document.getElementById('resultsCount').textContent  = `${products.length} products found`;

    const stores = [...new Set(products.map(p => p.store))];
    const filtersDiv = document.getElementById('storeFilters');
    filtersDiv.innerHTML = `<button class="cat-chip active" onclick="filterStore('all',this)">All</button>` +
        stores.map(s => `<button class="cat-chip" onclick="filterStore('${s}',this)">${s}</button>`).join('');

    header.className = 'mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2';

    resultsDiv.innerHTML = products.map((p, i) => {
        const price   = Number(p.price).toLocaleString('en-PK');
        const retail  = p.retail_price ? Number(p.retail_price).toLocaleString('en-PK') : null;
        const pct     = (p.retail_price && p.retail_price > p.price)
                        ? Math.round((1 - p.price / p.retail_price) * 100) : 0;
        const storeClass = 'store-' + p.store.toLowerCase();
        return `
        <div class="col-6 col-md-4 col-lg-3 fade-up" data-store="${p.store}">
            <div class="product-card">
                <div class="card-img-wrap">
                    <img src="${p.image}" alt="${p.name}"
                         onerror="this.src='https://static.priceoye.pk/images/product-placeholder.gif'" loading="lazy">
                </div>
                <div class="card-body">
                    <span class="store-pill ${storeClass}">${p.store}</span>
                    ${pct > 0 ? `<span class="discount float-end">${pct}% OFF</span>` : ''}
                    <h6>${p.name}</h6>
                    ${retail ? `<span class="retail">Rs. ${retail}</span>` : ''}
                    <div class="price">Rs. ${price}</div>
                    <a href="${p.link}" target="_blank" class="btn-view">View Product <i class="bi bi-arrow-up-right"></i></a>
                </div>
            </div>
        </div>`;
    }).join('');
}

function filterStore(store, btn) {
    document.querySelectorAll('#storeFilters .cat-chip').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const cards = document.querySelectorAll('#results [data-store]');
    cards.forEach(c => {
        c.style.display = (store === 'all' || c.dataset.store === store) ? '' : 'none';
    });
}

function showMsg(text, type) {
    const div = document.getElementById('msg');
    div.innerHTML = `<div class="alert alert-${type}">${text}</div>`;
    div.className = 'mb-4';
}
</script>
@endpush