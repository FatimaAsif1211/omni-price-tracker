<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Naheed Search Scraper</title>
    <style>
        /* General Body Styling */
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f8f9fa; margin: 0; padding: 20px; }
        h1 { text-align: center; color: #333; }

        /* Search Bar Styling */
        .search-box { text-align: center; margin-bottom: 30px; }
        .search-box input { padding: 10px; width: 300px; border: 1px solid #ccc; border-radius: 5px; }
        .search-box button { padding: 10px 20px; background: #e44d26; color: white; border: none; border-radius: 5px; cursor: pointer; }

        /* Grid Layout (Cards won't overlap now) */
        .container {
            display: grid;
            /* Auto-fit ensures cards fill space nicely, minmax keeps them from getting too small */
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 25px;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Card Styling */
        .card {
            background: white;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 12px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 350px; /* Fixed height to keep cards aligned */
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
            border-color: #e44d26;
        }

        .card img {
            max-width: 100%;
            height: 180px;
            object-fit: contain;
            margin-bottom: 10px;
        }

        .card a { text-decoration: none; color: #333; display: flex; flex-direction: column; height: 100%; }

        .name { font-size: 0.9em; height: 40px; overflow: hidden; margin-bottom: 10px; font-weight: 500; }

        .price { color: #e44d26; font-weight: bold; font-size: 1.1em; margin-top: auto; }
    </style>
</head>
<body>

    <h1>Naheed Product Scraper</h1>

    <div class="search-box">
        <form action="/naheed" method="GET">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search products...">
            <button type="submit">Search</button>
        </form>
    </div>

    <div class="container">
        @forelse($products as $product)
            <div class="card">
                <a href="{{ $product['link'] }}" target="_blank">
                    <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}">
                    <p class="name">{{ $product['name'] }}</p>
                    <span class="price">{{ $product['price'] }}</span>
                </a>
            </div>
        @empty
            <p style="width:100%; text-align:center; grid-column: 1/-1;">No products found.</p>
        @endforelse
    </div>

</body>
</html>