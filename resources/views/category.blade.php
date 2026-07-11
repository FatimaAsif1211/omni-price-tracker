{{-- resources/views/category.blade.php --}}
@extends('layout')
@section('title', ucfirst($slug) . ' — OmniPrice')
@section('content')
<div style="padding:60px 0 20px">
<div class="container">
    @php
    $catMap = [
        'mobiles'    => ['query'=>'mobile phone',       'icon'=>'📱','desc'=>'Latest smartphones from all brands'],
        'laptops'    => ['query'=>'laptop',             'icon'=>'💻','desc'=>'Laptops for work, gaming and study'],
        'earbuds'    => ['query'=>'wireless earbuds',   'icon'=>'🎧','desc'=>'True wireless earbuds and headphones'],
        'watches'    => ['query'=>'smart watch',        'icon'=>'⌚','desc'=>'Smartwatches and fitness trackers'],
        'tablets'    => ['query'=>'tablet',             'icon'=>'📟','desc'=>'Tablets for productivity and entertainment'],
        'perfumes'   => ['query'=>'perfume',            'icon'=>'✨','desc'=>'Premium fragrances, scents, and body sprays'],
        'skincare'   => ['query'=>'shampoo',            'icon'=>'🧴','desc'=>'Personal care, creams, shampoos and oils'],
        'groceries'  => ['query'=>'tea',                'icon'=>'🛒','desc'=>'Daily essentials, groceries, tea and snacks'],
    ];
    $cat = $catMap[$slug] ?? ['query'=>$slug,'icon'=>'📦','desc'=>'Browse products'];
    @endphp

    <div class="d-flex align-items-center gap-3 mb-2">
        <span style="font-size:2.5rem">{{ $cat['icon'] }}</span>
        <div>
            <h1 class="section-title mb-0">{{ ucfirst($slug) }}</h1>
            <p style="color:var(--brand-muted);margin:0">{{ $cat['desc'] }}</p>
        </div>
    </div>

    <div class="input-group search-wrap my-4" style="max-width:560px">
        <input type="text" id="catSearch" class="form-control" placeholder="Refine search…" value="{{ $cat['query'] }}" onkeydown="if(event.key==='Enter') catSearch()">
        <button class="btn btn-brand" onclick="catSearch()"><i class="bi bi-search"></i> Search</button>
    </div>

    <div id="msg" class="d-none mb-3"></div>
    
    <div id="spinner" class="text-center py-5 d-none">
        <div class="spinner-border"></div>
        <p class="mt-3" style="color:var(--brand-muted)">Searching…</p>
    </div>

    {{-- RESULTS HEADER SECTION WITH FILTER CONTAINER PILLS --}}
    <div id="resultsHeader" class="d-none mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <span class="section-title" style="font-size:1.2rem" id="resultsTitle"></span>
            <span style="color:var(--brand-muted);font-size:.85rem;margin-left:10px" id="resultsCount"></span>
        </div>
        <div class="d-flex gap-2 flex-wrap" id="storeFilters"></div>
    </div>

    <div class="row g-3" id="results"></div>
    
    <div id="emptyState" class="d-none text-center py-5">
        <div style="font-size:3rem">🔍</div>
        <h5 style="font-family:var(--font-head)">No results found</h5>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
let allProducts = [];

async function catSearch() {
    const q = document.getElementById('catSearch').value.trim();
    if (!q) return;
    
    const spinner = document.getElementById('spinner');
    const results = document.getElementById('results');
    const empty   = document.getElementById('emptyState');
    const header  = document.getElementById('resultsHeader');

    // Reset layout view bounds
    results.innerHTML = ''; 
    header.classList.add('d-none');
    empty.className = 'd-none text-center py-5';
    spinner.className = 'text-center py-5';
    allProducts = [];
    
    try {
        const data = await (await fetch(`/search?query=${encodeURIComponent(q)}`)).json();
        spinner.className = 'text-center py-5 d-none';
        
        if (data.status === 'error' || data.status === 'unavailable' || !data.results?.length) { 
            empty.className = 'text-center py-5'; 
            return; 
        }
        
        allProducts = data.results;
        renderProducts(allProducts, q);

    } catch(e) { 
        spinner.className = 'text-center py-5 d-none'; 
        empty.className = 'text-center py-5';
    }
}

function renderProducts(products, query) {
    const resultsDiv = document.getElementById('results');
    const header     = document.getElementById('resultsHeader');

    // Build Results Header Strings
    document.getElementById('resultsTitle').textContent = `Results for "${query}"`;
    document.getElementById('resultsCount').textContent  = `${products.length} products found`;

    // Dynamic generation of individual unique store selection pills
    const stores = [...new Set(products.map(p => p.store))];
    const filtersDiv = document.getElementById('storeFilters');
    filtersDiv.innerHTML = `<button class="cat-chip active" onclick="filterStore('all',this)">All</button>` +
        stores.map(s => `<button class="cat-chip" onclick="filterStore('${s}',this)">${s}</button>`).join('');

    header.className = 'mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2';

    // Build dynamic output grid layout architecture matching home section templates
    resultsDiv.innerHTML = products.map(p => {
        const price = Number(p.price).toLocaleString('en-PK');
        const pct   = p.retail_price && p.retail_price > p.price ? Math.round((1-p.price/p.retail_price)*100) : 0;
        const sc    = 'store-' + p.store.toLowerCase();
        
        return `
        <div class="col-6 col-md-4 col-lg-3 fade-up" data-store="${p.store}">
            <div class="product-card">
                <div class="card-img-wrap">
                    <img src="${p.image}" alt="${p.name}" onerror="this.src='https://static.priceoye.pk/images/product-placeholder.gif'" loading="lazy">
                </div>
                <div class="card-body">
                    <span class="store-pill ${sc}">${p.store}</span>
                    ${pct > 0 ? `<span class="discount float-end">${pct}% OFF</span>` : ''}
                    <h6>${p.name}</h6>
                    <div class="price">Rs. ${price}</div>
                    <a href="${p.link}" target="_blank" class="btn-view">View Product <i class="bi bi-arrow-up-right"></i></a>
                </div>
            </div>
        </div>`;
    }).join('');
}

// Interactive filter method to toggle display bounds by store attributes
function filterStore(store, btn) {
    document.querySelectorAll('#storeFilters .cat-chip').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    
    const cards = document.querySelectorAll('#results [data-store]');
    cards.forEach(c => {
        c.style.display = (store === 'all' || c.dataset.store === store) ? '' : 'none';
    });
}

// Auto-search on page load
window.addEventListener('DOMContentLoaded', catSearch);
</script>
@endpush