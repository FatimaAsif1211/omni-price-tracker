<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daraz Product Tracker</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; padding: 20px; background-color: #f5f5f5; }

        .header { text-align: center; margin-bottom: 30px; }

        /* Grid Layout: Min width 180px ensures smaller cards, gap creates distance */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 25px;
            max-width: 1200px;
            margin: auto;
        }

        .card {
            background: white;
            padding: 12px;
            border-radius: 12px;
            text-align: center;
            border: 1px solid #e0e0e0;
            transition: all 0.4s ease;
            cursor: pointer;
            height: 280px; /* Fixed height for consistency */
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 20px rgba(0,0,0,0.15);
            border-color: #f85606;
        }

        .card img {
            max-width: 100%;
            height: 140px;
            object-fit: contain;
            margin-bottom: 10px;
        }

        .name {
            font-size: 0.85em;
            height: 35px;
            overflow: hidden;
            color: #333;
            margin-bottom: 10px;
            line-height: 1.2;
        }

        .price {
            color: #f85606;
            font-weight: bold;
            font-size: 1.1em;
            margin-top: auto;
        }

        button {
            padding: 10px 20px;
            background: #f85606;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }
        button:disabled { background: #bbb; }
    </style>
</head>
<body>

    <div class="header">
        <h2>Daraz Product Tracker</h2>
        <input type="text" id="query" value="perfume" style="padding: 10px; width: 220px; border: 1px solid #ddd; border-radius: 4px;">
        <button id="searchBtn" onclick="checkData()">Search & Sort</button>
    </div>

    <div class="product-grid" id="resultGrid"></div>

    <script>
        async function checkData() {
            const btn = document.getElementById('searchBtn');
            const q = document.getElementById('query').value;
            const grid = document.getElementById('resultGrid');

            btn.disabled = true;
            btn.innerText = 'Searching...';
            grid.innerHTML = '<h3 style="text-align:center; width:100%;">Please Wait...</h3>';

            try {
                const res = await fetch(`/debug-data?query=${encodeURIComponent(q)}`);
                const products = await res.json();

                grid.innerHTML = '';

                if (!products || products.length === 0) {
                    grid.innerHTML = '<h3 style="text-align:center;">No Product Found.</h3>';
                } else {
                    products.forEach(p => {
                        grid.innerHTML += `
                            <a href="${p.link}" target="_blank" style="text-decoration:none; color:inherit;">
                                <div class="card">
                                    <img src="${p.image}" alt="Product">
                                    <div class="name">${p.name.substring(0, 35)}...</div>
                                    <div class="price">${p.priceShow}</div>
                                </div>
                            </a>
                        `;
                    });
                }
            } catch (error) {
                grid.innerHTML = '<h3 style="color:red; text-align:center;">Error loading data!</h3>';
            } finally {
                btn.disabled = false;
                btn.innerText = 'Search & Sort';
            }
        }
    </script>
</body>
</html>