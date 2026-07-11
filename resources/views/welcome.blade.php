<!DOCTYPE html>
<html>
<head>
    <title>Omni Price Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-img-top { height: 220px; object-fit: contain; padding: 10px; background: #fff; }
        .retail-price { text-decoration: line-through; color: #999; font-size: 0.9rem; }
        .discount-badge { font-size: 0.75rem; }
        #spinner { display: none; }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5">

    <h1 class="text-center mb-4">Omni Price Tracker</h1>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="input-group">
                <input type="text"
                       id="searchInput"
                       class="form-control form-control-lg"
                       placeholder="Search product (e.g. Samsung Galaxy S25)"
                       onkeydown="if(event.key==='Enter') searchProduct()">
                <button class="btn btn-primary btn-lg" onclick="searchProduct()">
                    Search
                </button>
            </div>
        </div>
    </div>

    <!-- Status / error message -->
    <div class="row justify-content-center mt-3">
        <div class="col-md-8">
            <div id="message" class="alert d-none"></div>
        </div>
    </div>

    <!-- Spinner -->
    <div class="text-center mt-4" id="spinner">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="mt-2 text-muted">Fetching prices from PriceOye...</p>
    </div>

    <!-- Results grid -->
    <div class="row mt-4" id="results"></div>

</div>

<script>
async function searchProduct() {
    const query = document.getElementById('searchInput').value.trim();
    const resultsDiv = document.getElementById('results');
    const messageDiv = document.getElementById('message');
    const spinner    = document.getElementById('spinner');

    // Reset
    resultsDiv.innerHTML = '';
    messageDiv.className = 'alert d-none';
    messageDiv.textContent = '';

    if (!query) {
        showMessage('Please enter a product name.', 'warning');
        return;
    }

    // Show spinner
    spinner.style.display = 'block';

    try {
        const response = await fetch(`/search?query=${encodeURIComponent(query)}`);
        const data     = await response.json();

        spinner.style.display = 'none';

        // ── The controller returns { status, count, results: [...] }
        // ── We must read data.results — NOT data directly
        if (data.status === 'error' || data.status === 'unavailable') {
            showMessage(data.message || 'No products found.', 'warning');
            return;
        }

        const products = data.results;

        if (!products || products.length === 0) {
            showMessage('No products found for "' + query + '".', 'warning');
            return;
        }

        showMessage(`Found ${products.length} products — sorted lowest to highest price.`, 'success');

        products.forEach(product => {
            // Format price with commas  e.g. 287999 → 287,999
            const formattedPrice  = Number(product.price).toLocaleString('en-PK');
            const formattedRetail = product.retail_price
                ? Number(product.retail_price).toLocaleString('en-PK')
                : null;

            // Calculate discount %
            let discountBadge = '';
            if (product.retail_price && product.retail_price > product.price) {
                const pct = Math.round((1 - product.price / product.retail_price) * 100);
                discountBadge = `<span class="badge bg-danger discount-badge">${pct}% OFF</span>`;
            }

            const retailLine = formattedRetail
                ? `<p class="retail-price mb-1">Rs. ${formattedRetail}</p>`
                : '';

            resultsDiv.innerHTML += `
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="${product.image}"
                         class="card-img-top"
                         onerror="this.src='https://static.priceoye.pk/images/product-placeholder.gif'">
                    <div class="card-body d-flex flex-column">
                        <div class="mb-2">${discountBadge}</div>
                        <h6 class="card-title">${product.name}</h6>
                        ${retailLine}
                        <h5 class="text-success fw-bold">Rs. ${formattedPrice}</h5>
                        <a href="${product.link}"
                           target="_blank"
                           class="btn btn-dark mt-auto">
                            View on PriceOye ↗
                        </a>
                    </div>
                </div>
            </div>`;
        });

    } catch (err) {
        spinner.style.display = 'none';
        showMessage('Something went wrong: ' + err.message, 'danger');
        console.error(err);
    }
}

function showMessage(text, type) {
    const div = document.getElementById('message');
    div.textContent = text;
    div.className = `alert alert-${type}`;
}
</script>

</body>
</html>