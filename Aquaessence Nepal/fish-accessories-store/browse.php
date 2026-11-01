<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: login.php?role=user');
    exit();
}

$username = htmlspecialchars($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Fish - Fish & Accessories Store</title>
    <style>
        :root {
            --bg: #f4f8fb;
            --surface: #ffffff;
            --surface-soft: #eef4f9;
            --text: #12232e;
            --muted: #67727a;
            --primary: #0e76d6;
            --primary-soft: #d9e7ff;
            --accent: #ffb703;
            --shadow: 0 24px 60px rgba(14, 118, 214, 0.08);
            --card-radius: 28px;
        }
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            background: radial-gradient(circle at top, #eef6fd 0%, #f8fbff 45%, #f4f8fb 100%);
            color: var(--text);
        }
        a {
            color: inherit;
            text-decoration: none;
        }
        button {
            font: inherit;
        }
        header {
            background: #ffffff;
            border-bottom: 1px solid #e6eef5;
            position: sticky;
            top: 0;
            z-index: 20;
        }
        .topbar {
            max-width: 1180px;
            margin: 0 auto;
            padding: 18px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .brand {
            display: inline-flex;
            align-items: center;
            gap: 14px;
        }
        .brand-icon {
            width: 46px;
            height: 46px;
            border-radius: 16px;
            display: grid;
            place-content: center;
            background: linear-gradient(135deg, #0e76d6, #42a5f5);
            color: white;
            font-size: 1.4rem;
        }
        .brand-text h1 {
            font-size: 1.35rem;
            margin-bottom: 4px;
        }
        .brand-text p {
            margin: 0;
            color: var(--muted);
            font-size: 0.95rem;
        }
        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .header-actions .button {
            padding: 12px 18px;
            border-radius: 999px;
            border: 1px solid rgba(14, 118, 214, 0.15);
            background: #ffffff;
            color: var(--text);
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .header-actions .button:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 24px rgba(14, 118, 214, 0.08);
        }
        .header-actions .logout {
            color: #ffffff;
            background: #0e76d6;
            border-color: transparent;
        }
        .hero {
            max-width: 1180px;
            margin: 0 auto;
            padding: 30px 24px 14px;
        }
        .hero-title {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }
        .hero-title h2 {
            font-size: clamp(2.4rem, 4vw, 3.2rem);
            margin: 0;
            line-height: 1.05;
        }
        .hero-title .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary-soft);
            color: #0e3f82;
            padding: 10px 16px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.98rem;
        }
        .hero-copy {
            max-width: 780px;
            margin-top: 14px;
            color: var(--muted);
            font-size: 1rem;
            line-height: 1.85;
        }
        .filter-row {
            margin-top: 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }
        .tabs {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .tab {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 18px;
            border-radius: 999px;
            background: #ffffff;
            border: 1px solid #d9e7ff;
            color: var(--text);
            cursor: pointer;
            transition: all 0.25s ease;
            min-width: 120px;
        }
        .tab img {
            width: 22px;
            height: 22px;
            object-fit: cover;
            border-radius: 50%;
        }
        .tab span {
            white-space: nowrap;
        }
        .tab.active {
            background: var(--primary);
            color: white;
            border-color: transparent;
            box-shadow: 0 14px 32px rgba(14, 118, 214, 0.14);
        }
        .search-wrap {
            position: relative;
            flex: 1;
            min-width: 240px;
            max-width: 420px;
        }
        .search-wrap input {
            width: 100%;
            border: 1px solid #d9e7ff;
            border-radius: 999px;
            padding: 14px 52px 14px 18px;
            background: #ffffff;
            color: var(--text);
            outline: none;
        }
        .search-wrap .search-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            font-size: 1.1rem;
        }
        .product-grid {
            max-width: 1180px;
            margin: 0 auto;
            padding: 24px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
        }
        .product-card {
            position: relative;
            border-radius: 28px;
            overflow: hidden;
            background: linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
            box-shadow: var(--shadow);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            display: flex;
            flex-direction: column;
        }
        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 30px 70px rgba(14, 118, 214, 0.14);
        }
        .product-image {
            min-height: 220px;
            background-size: cover;
            background-position: center;
        }
        .product-top {
            position: absolute;
            right: 16px;
            top: 16px;
            display: inline-flex;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255,255,255,0.85);
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(14, 118, 214, 0.12);
        }
        .product-top svg {
            width: 20px;
            height: 20px;
            fill: #ff5a74;
        }
        .product-body {
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .product-name {
            font-size: 1.12rem;
            font-weight: 700;
            margin: 0;
        }
        .product-rating {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
            color: var(--muted);
        }
        .rating-stars {
            display: inline-flex;
            gap: 3px;
            color: #ffb703;
        }
        .product-price {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 700;
            color: #0e76d6;
        }
        .product-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .category-pill {
            padding: 8px 14px;
            border-radius: 999px;
            background: #e7f2ff;
            color: #0e4f8f;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .add-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #0e76d6;
            color: white;
            border: none;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        .add-button:hover {
            transform: translateY(-1px);
        }
        .no-products {
            grid-column: 1 / -1;
            text-align: center;
            padding: 48px 24px;
            color: var(--muted);
            font-size: 1.05rem;
        }
        @media (max-width: 780px) {
            .product-body { padding: 20px; }
            .hero-title h2 { font-size: 2.4rem; }
        }
    </style>
</head>
<body>
    <header>
        <div class="topbar">
            <a href="user-dashboard.php" class="brand">
                <span class="brand-icon">🐟</span>
                <span class="brand-text">
                    <h1>Live Fish</h1>
                    <p>Browse the finest species</p>
                </span>
            </a>
            <div class="header-actions">
                <span>Hi, <?= $username ?></span>
                <a href="user-dashboard.php" class="button">Dashboard</a>
                <a href="logout.php" class="button logout">Logout</a>
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="hero-title">
            <h2>Browse Fish</h2>
            <span class="hero-badge" id="speciesCount">Loading...</span>
        </div>
        <p class="hero-copy">
            Discover beautiful aquarium fish with friendly prices, live photos, and quick access to favorites. Use the filters below to find the perfect fish, plants, or aquarium products for your tank.
        </p>
        <div class="filter-row">
            <div class="tabs" id="categoryTabs">
                <button class="tab active" data-category="All"><img src="images/logo.png" alt="All"><span>All</span></button>
                <button class="tab" data-category="Fish"><img src="images/fish-4.jpg" alt="Fish"><span>Fish</span></button>
                <button class="tab" data-category="Plants"><img src="images/plant-1.jpg" alt="Plants"><span>Plants</span></button>
                <button class="tab" data-category="Aquarium"><img src="images/aquarium.png" alt="Aquarium"><span>Aquarium</span></button>
            </div>
            <div class="search-wrap">
                <input type="search" id="searchInput" placeholder="Search fish, plants, or aquariums...">
                <span class="search-icon">🔍</span>
            </div>
        </div>
    </section>

    <main class="product-grid" id="productGrid">
        <div class="no-products" id="emptyState">Loading products…</div>
    </main>

    <script>
        const apiUrl = 'products_api.php';
        const productGrid = document.getElementById('productGrid');
        const speciesCount = document.getElementById('speciesCount');
        const searchInput = document.getElementById('searchInput');
        const tabs = document.querySelectorAll('.tab');
        const emptyState = document.getElementById('emptyState');

        let products = [];
        let activeCategory = 'All';

        function renderStars(count = 5) {
            return Array.from({ length: 5 }, () => '★').join('');
        }

        function categoryMatches(item) {
            const category = (item.category || '').toLowerCase();
            const name = (item.name || '').toLowerCase();

            if (activeCategory === 'All') {
                return true;
            }

            if (activeCategory === 'Fish') {
                return category === 'tropical' || category === 'freshwater' || name.includes('fish') || name.includes('betta') || name.includes('goldfish');
            }

            if (activeCategory === 'Plants') {
                return category.includes('plant') || name.includes('plant') || name.includes('moss') || name.includes('fern');
            }

            if (activeCategory === 'Aquarium') {
                return category.includes('aquarium') || name.includes('aquarium');
            }

            return false;
        }

        function setProductCountLabel(label) {
            speciesCount.textContent = label;
        }

        function showError(message) {
            productGrid.innerHTML = '';
            const errorMessage = document.createElement('div');
            errorMessage.className = 'no-products';
            errorMessage.textContent = message;
            productGrid.appendChild(errorMessage);
            setProductCountLabel('0 species available');
        }

        function renderProducts() {
            const query = searchInput.value.trim().toLowerCase();
            const filtered = products.filter(item => {
                const matchesCategory = categoryMatches(item);
                const matchesSearch = item.name.toLowerCase().includes(query) || item.category.toLowerCase().includes(query);
                return matchesCategory && matchesSearch;
            });

            productGrid.innerHTML = '';

            if (!filtered.length) {
                emptyState.textContent = 'No fish found. Try another filter or search keyword.';
                productGrid.appendChild(emptyState);
                return;
            }

            filtered.forEach(item => {
                const card = document.createElement('article');
                card.className = 'product-card';
                const imageUrl = item.image_url ? item.image_url.replace(/'/g, "\\'") : 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&h=320&fit=crop';
                card.innerHTML = `
                    <div class="product-image" style="background-image: url('${imageUrl}')">
                        <div class="product-top">
                            <svg viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path></svg>
                        </div>
                    </div>
                    <div class="product-body">
                        <div>
                            <h3 class="product-name">${item.name}</h3>
                            <div class="product-rating">
                                <span class="rating-stars">${renderStars()}</span>
                                <span>4.9</span>
                            </div>
                        </div>
                        <div class="product-meta">
                            <span class="category-pill">${item.category || 'Unknown'}</span>
                            <span class="product-price">Rs. ${item.price.toFixed(2)}</span>
                        </div>
                        <button class="add-button" type="button" title="Add to cart">+</button>
                    </div>
                `;
                productGrid.appendChild(card);
            });
        }

        function updateCount() {
            const count = products.length;
            setProductCountLabel(`${count} species available`);
        }

        function loadProducts() {
            setProductCountLabel('Loading...');
            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.success && Array.isArray(data.products)) {
                        products = data.products;
                        updateCount();
                        renderProducts();
                    } else {
                        showError(data.message || 'Unable to load products right now.');
                    }
                })
                .catch(error => {
                    console.error('Product load error:', error);
                    showError('Unable to load products. Check your connection.');
                });
        }

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(item => item.classList.remove('active'));
                tab.classList.add('active');
                activeCategory = tab.dataset.category;
                renderProducts();
            });
        });

        searchInput.addEventListener('input', () => renderProducts());

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', loadProducts);
        } else {
            loadProducts();
        }
    </script>
</body>
</html>
