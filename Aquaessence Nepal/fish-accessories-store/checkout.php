<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Fish & Accessories Store</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🥬</text></svg>">
    <style>
        .checkout-wrapper {
            max-width: 1100px;
            margin: 30px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 24px;
        }

        .checkout-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
            padding: 24px;
        }

        .checkout-card h2,
        .checkout-card h3 {
            color: #1e40af;
            margin-bottom: 16px;
        }

        .checkout-actions {
            max-width: 1100px;
            margin: 25px auto 0;
            padding: 0 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .checkout-actions button,
        .checkout-actions a {
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .home-link {
            background: #1e40af;
        }

        .back-link {
            background: #4b5563;
        }

        .checkout-form {
            display: grid;
            gap: 12px;
        }

        .checkout-form input,
        .checkout-form select,
        .checkout-form textarea {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 11px 12px;
            font-size: 15px;
        }

        .checkout-form textarea {
            resize: vertical;
            min-height: 90px;
        }

        .pay-btn {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 18px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 8px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            color: #374151;
        }

        .cart-item {
            border-bottom: 1px solid #eef2f7;
            padding: 10px 0;
        }

        .cart-item strong {
            color: #111827;
        }

        .empty-state {
            color: #6b7280;
            font-style: italic;
            margin-bottom: 12px;
        }

        .grand-total {
            margin-top: 16px;
            padding-top: 12px;
            border-top: 2px solid #e5e7eb;
            font-weight: 700;
            color: #111827;
            display: flex;
            justify-content: space-between;
        }

        @media (max-width: 900px) {
            .checkout-wrapper {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body style="background: #f8faf8;">
    <header>
        <div class="container" style="padding: 15px 20px;">
            <div class="logo">
                <span class="logo-icon">🥬</span>
                <h1>Fish & Accessories Store</h1>
            </div>
        </div>
    </header>

    <div class="checkout-actions">
        <a class="home-link" href="index.html">Home</a>
        <a class="back-link" href="user-dashboard.php">User Dashboard</a>
    </div>

    <main class="checkout-wrapper">
        <section class="checkout-card">
            <h2>Checkout & Payment</h2>
            <form class="checkout-form" id="paymentForm">
                <input type="text" id="fullName" placeholder="Full Name" required>
                <input type="email" id="email" placeholder="Email Address" required>
                <input type="tel" id="phone" placeholder="Phone Number" required>
                <textarea id="address" placeholder="Delivery Address" required></textarea>
                <select id="paymentMethod" required>
                    <option value="">Select Payment Method</option>
                    <option value="Card">Credit/Debit Card</option>
                    <option value="UPI">UPI</option>
                    <option value="COD">Cash on Delivery</option>
                </select>
                <button type="submit" class="pay-btn">Pay Now</button>
            </form>
        </section>

        <aside class="checkout-card">
            <h3>Order Summary</h3>
            <div id="cartItems"></div>
            <div class="summary-row">
                <span>Subtotal</span>
                <span id="subtotal">$0.00</span>
            </div>
            <div class="summary-row">
                <span>Delivery Fee</span>
                <span id="deliveryFee">$2.00</span>
            </div>
            <div class="grand-total">
                <span>Total</span>
                <span id="grandTotal">$2.00</span>
            </div>
        </aside>
    </main>

    <script>
        function getCart() {
            return JSON.parse(localStorage.getItem('ggmCart') || '[]');
        }

        function saveCart(cart) {
            localStorage.setItem('ggmCart', JSON.stringify(cart));
        }

        function formatCurrency(value) {
            return '$' + value.toFixed(2);
        }

        function renderCartSummary() {
            const cart = getCart();
            const cartItemsContainer = document.getElementById('cartItems');
            const subtotalEl = document.getElementById('subtotal');
            const grandTotalEl = document.getElementById('grandTotal');
            const deliveryFee = 2;

            if (!cart.length) {
                cartItemsContainer.innerHTML = '<p class="empty-state">Your cart is empty. Add products to continue checkout.</p>';
                subtotalEl.textContent = formatCurrency(0);
                grandTotalEl.textContent = formatCurrency(deliveryFee);
                return;
            }

            let subtotal = 0;
            cartItemsContainer.innerHTML = '';

            cart.forEach(item => {
                const lineTotal = Number(item.price) * Number(item.quantity || 1);
                subtotal += lineTotal;

                const itemEl = document.createElement('div');
                itemEl.className = 'cart-item';
                itemEl.innerHTML = '<strong>' + item.name + '</strong><br>' +
                    '<span>' + (item.category || 'General') + ' - Qty: ' + (item.quantity || 1) + '</span><br>' +
                    '<span>' + formatCurrency(lineTotal) + '</span>';

                cartItemsContainer.appendChild(itemEl);
            });

            subtotalEl.textContent = formatCurrency(subtotal);
            grandTotalEl.textContent = formatCurrency(subtotal + deliveryFee);
        }

        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const cart = getCart();
            if (!cart.length) {
                alert('Your cart is empty. Please add products first.');
                window.location.href = 'index.html#products';
                return;
            }

            alert('Payment successful! Your order has been placed. Thank you for shopping with Fish & Accessories Store.');
            saveCart([]);
            window.location.href = 'index.html';
        });

        renderCartSummary();
    </script>
</body>
</html>
