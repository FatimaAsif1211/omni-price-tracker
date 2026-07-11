{{-- resources/views/layout.blade.php (master layout) --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'OmniPrice — Compare Prices Across Pakistan')</title>
    <meta name="description" content="@yield('meta_desc', 'Compare prices from PriceOye, Daraz and Naheed in one place.')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* ── DESIGN TOKENS ───────────────────────────────────────── */
        :root {
            --brand-primary:   #FF4B2B;
            --brand-secondary: #FF416C;
            --brand-dark:      #0D0D0D;
            --brand-surface:   #141414;
            --brand-card:      #1C1C1C;
            --brand-border:    #2A2A2A;
            --brand-text:      #F0F0F0;
            --brand-muted:     #888;
            --brand-accent:    #FFD700;
            --gradient:        linear-gradient(135deg, #FF416C 0%, #FF4B2B 100%);
            --glow:            0 0 40px rgba(255,75,43,.25);
            --radius:          14px;
            --font-head:       'Syne', sans-serif;
            --font-body:       'DM Sans', sans-serif;
        }

        /* ── BASE ────────────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            background: var(--brand-dark);
            color: var(--brand-text);
            font-family: var(--font-body);
            font-size: 15px;
            line-height: 1.6;
            overflow-x: hidden;
        }
        h1,h2,h3,h4,h5,h6 { font-family: var(--font-head); }
        a { color: inherit; text-decoration: none; }
        img { max-width: 100%; }

        /* ── NAVBAR ──────────────────────────────────────────────── */
        .navbar {
            background: rgba(13,13,13,.92);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--brand-border);
            padding: 14px 0;
            position: sticky; top: 0; z-index: 1000;
        }
        .navbar-brand {
            font-family: var(--font-head);
            font-weight: 800;
            font-size: 1.5rem;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .nav-link {
            color: var(--brand-muted) !important;
            font-weight: 500;
            transition: color .2s;
            padding: 6px 14px !important;
            border-radius: 8px;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--brand-text) !important;
            background: var(--brand-border);
        }
        .navbar-toggler { border: 1px solid var(--brand-border); }
        .navbar-toggler-icon { filter: invert(1); }

        /* ── STORE BADGE ─────────────────────────────────────────── */
        .store-priceoye  { background:#1a2636; color:#4fa3e0; }
        .store-daraz     { background:#2a1428; color:#f05a28; }
        .store-naheed    { background:#1a2a1a; color:#4caf50; }

        /* ── BUTTONS ─────────────────────────────────────────────── */
        .btn-brand {
            background: var(--gradient);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-family: var(--font-head);
            font-weight: 600;
            transition: opacity .2s, transform .15s;
        }
        .btn-brand:hover { opacity: .88; transform: translateY(-1px); color:#fff; }
        .btn-outline-brand {
            background: transparent;
            border: 1.5px solid var(--brand-primary);
            color: var(--brand-primary);
            border-radius: 10px;
            font-weight: 500;
            transition: all .2s;
        }
        .btn-outline-brand:hover { background: var(--brand-primary); color: #fff; }

        /* ── CARDS ───────────────────────────────────────────────── */
        .product-card {
            background: var(--brand-card);
            border: 1px solid var(--brand-border);
            border-radius: var(--radius);
            overflow: hidden;
            transition: transform .25s, box-shadow .25s, border-color .25s;
            height: 100%;
            display: flex; flex-direction: column;
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--glow);
            border-color: var(--brand-primary);
        }
        .product-card .card-img-wrap {
            background: #fff;
            height: 200px;
            display: flex; align-items: center; justify-content: center;
            padding: 16px;
        }
        .product-card .card-img-wrap img {
            max-height: 168px;
            object-fit: contain;
            transition: transform .3s;
        }
        .product-card:hover .card-img-wrap img { transform: scale(1.05); }
        .product-card .card-body { padding: 16px; flex:1; display:flex; flex-direction:column; }
        .product-card .store-pill {
            display: inline-block;
            font-size: .7rem;
            font-weight: 600;
            padding: 2px 10px;
            border-radius: 20px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .product-card h6 {
            font-size: .88rem;
            color: var(--brand-text);
            margin-bottom: 8px;
            line-height: 1.4;
            flex:1;
        }
        .product-card .price {
            font-family: var(--font-head);
            font-size: 1.2rem;
            font-weight: 700;
            color: #4ade80;
        }
        .product-card .retail {
            font-size: .78rem;
            color: var(--brand-muted);
            text-decoration: line-through;
        }
        .product-card .discount {
            font-size: .7rem;
            background: rgba(255,75,43,.15);
            color: var(--brand-primary);
            border-radius: 4px;
            padding: 1px 6px;
            font-weight: 600;
        }
        .product-card .btn-view {
            margin-top: 12px;
            background: var(--brand-border);
            color: var(--brand-text);
            border: none;
            border-radius: 8px;
            padding: 8px 14px;
            font-size: .83rem;
            font-weight: 500;
            transition: background .2s;
            text-align: center;
            display: block;
        }
        .product-card .btn-view:hover { background: var(--brand-primary); color:#fff; }

        /* ── FOOTER ──────────────────────────────────────────────── */
        footer {
            background: var(--brand-surface);
            border-top: 1px solid var(--brand-border);
            color: var(--brand-muted);
            padding: 48px 0 24px;
            margin-top: 80px;
        }
        footer h6 { font-family: var(--font-head); color: var(--brand-text); margin-bottom: 14px; }
        footer a { color: var(--brand-muted); transition: color .2s; font-size: .9rem; display:block; margin-bottom:6px; }
        footer a:hover { color: var(--brand-primary); }
        .footer-brand {
            font-family: var(--font-head);
            font-weight: 800;
            font-size: 1.4rem;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* ── SEARCH BAR ──────────────────────────────────────────── */
        .search-wrap { position: relative; }
        .search-wrap input {
            background: var(--brand-card);
            border: 1.5px solid var(--brand-border);
            color: var(--brand-text);
            border-radius: 12px 0 0 12px;
            padding: 14px 20px;
            font-size: 1rem;
            transition: border-color .2s;
        }
        .search-wrap input::placeholder { color: var(--brand-muted); }
        .search-wrap input:focus { outline:none; border-color: var(--brand-primary); background: var(--brand-card); color:var(--brand-text); box-shadow:none; }
        .search-wrap .btn { border-radius: 0 12px 12px 0; padding: 14px 28px; font-size:1rem; }

        /* ── CATEGORY CHIP ───────────────────────────────────────── */
        .cat-chip {
            background: var(--brand-card);
            border: 1px solid var(--brand-border);
            border-radius: 40px;
            padding: 8px 20px;
            font-size: .85rem;
            font-weight: 500;
            cursor: pointer;
            transition: all .2s;
            white-space: nowrap;
            color: var(--brand-muted);
            display: inline-flex; align-items: center; gap: 6px;
        }
        .cat-chip:hover, .cat-chip.active {
            background: var(--brand-primary);
            border-color: var(--brand-primary);
            color: #fff;
        }

        /* ── MISC ────────────────────────────────────────────────── */
        .section-title {
            font-family: var(--font-head);
            font-size: 1.8rem;
            font-weight: 700;
        }
        .divider { border-color: var(--brand-border); opacity:1; }
        .spinner-border { color: var(--brand-primary) !important; }
        .alert-dark-custom {
            background: var(--brand-card);
            border: 1px solid var(--brand-border);
            color: var(--brand-text);
            border-radius: var(--radius);
        }

        /* ── ANIMATE ─────────────────────────────────────────────── */
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(20px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .fade-up { animation: fadeUp .5s ease both; }
        .fade-up:nth-child(1){animation-delay:.05s}
        .fade-up:nth-child(2){animation-delay:.1s}
        .fade-up:nth-child(3){animation-delay:.15s}
        .fade-up:nth-child(4){animation-delay:.2s}
        .fade-up:nth-child(5){animation-delay:.25s}
        .fade-up:nth-child(6){animation-delay:.3s}

        /* ── RESPONSIVE ──────────────────────────────────────────── */
        @media(max-width:576px){
            .product-card .card-img-wrap { height:160px; }
            .search-wrap input { font-size:.9rem; padding:12px 14px; }
        }
    </style>

    @stack('head')
</head>
<body>

{{-- NAVBAR WITH MULTI-STORE CATEGORIES INTEGRATED --}}
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="/">Omni<span style="-webkit-text-fill-color:#fff">Price</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto gap-1">
                <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="/category/mobiles">Mobiles</a></li>
                <li class="nav-item"><a class="nav-link" href="/category/laptops">Laptops</a></li>
                <li class="nav-item"><a class="nav-link" href="/category/earbuds">Earbuds</a></li>
                <li class="nav-item"><a class="nav-link" href="/category/perfumes">Perfumes</a></li>
                <li class="nav-item"><a class="nav-link" href="/category/skincare">Skincare</a></li>
                <li class="nav-item"><a class="nav-link" href="/category/groceries">Groceries</a></li>
            </ul>
        </div>
    </div>
</nav>

{{-- PAGE CONTENT --}}
@yield('content')

{{-- FOOTER WITH SYSTEM LINKS SHIFTED HERE --}}
<footer>
    <div class="container">
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="footer-brand mb-2">OmniPrice</div>
                <p style="font-size:.9rem">Pakistan's unified price comparison engine. Search once, compare everywhere.</p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#" style="font-size:1.3rem"><i class="bi bi-facebook"></i></a>
                    <a href="#" style="font-size:1.3rem"><i class="bi bi-instagram"></i></a>
                    <a href="#" style="font-size:1.3rem"><i class="bi bi-twitter-x"></i></a>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <h6>Browse Tech</h6>
                <a href="/category/mobiles">Mobiles</a>
                <a href="/category/laptops">Laptops</a>
                <a href="/category/earbuds">Earbuds</a>
                <a href="/category/watches">Watches</a>
                <a href="/category/tablets">Tablets</a>
            </div>
            <div class="col-6 col-md-2">
                <h6>Browse Lifestyle</h6>
                <a href="/category/perfumes">Perfumes</a>
                <a href="/category/skincare">Skincare</a>
                <a href="/category/groceries">Groceries</a>
            </div>
            <div class="col-6 col-md-2">
                <h6>Partner Stores</h6>
                <a href="https://priceoye.pk" target="_blank">PriceOye</a>
                <a href="https://daraz.pk" target="_blank">Daraz</a>
                <a href="https://naheed.pk" target="_blank">Naheed</a>
            </div>
            <div class="col-6 col-md-2">
                <h6>Company</h6>
                {{-- SHIFTED LOCATION FOR ABOUT AND CONTACT --}}
                <a href="/about">About Us</a>
                <a href="/contact">Contact Us</a>
                <a href="/privacy">Privacy Policy</a>
            </div>
        </div>
        <hr class="divider">
        <p class="text-center mt-3" style="font-size:.82rem">© {{ date('Y') }} OmniPrice. Built for Pakistan 🇵🇰</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>