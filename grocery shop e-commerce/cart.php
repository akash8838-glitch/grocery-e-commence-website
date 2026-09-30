<?php include 'includes/header.php'; ?>

<div class="container" style="padding: 4rem 0;">
    <h1 style="margin-bottom: 2rem;">Your Shopping Cart</h1>

    <?php if (empty($_SESSION['cart'])): ?>
        <div style="text-align: center; padding: 4rem 0;">
            <i class="fas fa-cart-arrow-down" style="font-size: 5rem; color: #e5e7eb; margin-bottom: 2rem;"></i>
            <h2>Your cart is empty!</h2>
            <p style="color: var(--text-muted); margin-bottom: 2rem;">Looks like you haven't added anything to your cart
                yet.</p>
            <a href="products.php" class="btn btn-primary">Start Shopping</a>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 3rem;">
            <div class="cart-items">
                <?php
                $total = 0;
                $ids = array_keys($_SESSION['cart']);
                $placeholders = str_repeat('?,', count($ids) - 1) . '?';
                $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
                $stmt->execute($ids);

                while ($product = $stmt->fetch()):
                    $qty = $_SESSION['cart'][$product['id']];
                    $price = $product['price'] - ($product['price'] * ($product['discount'] / 100));
                    $subtotal = $price * $qty;
                    $total += $subtotal;
                    ?>
                    <div
                        style="background: white; padding: 1.5rem; border-radius: var(--radius); box-shadow: var(--shadow); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 2rem;">
                        <img src="<?php echo $product['image'] ?: 'https://via.placeholder.com/100'; ?>"
                            style="width: 100px; height: 100px; object-fit: contain;">
                        <div style="flex-grow: 1;">
                            <h3 style="margin-bottom: 0.5rem;">
                                <?php echo $product['name']; ?>
                            </h3>
                            <p style="color: var(--primary); font-weight: 700;">$
                                <?php echo number_format($price, 2); ?>
                            </p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <input type="number" value="<?php echo $qty; ?>" min="1" max="<?php echo $product['stock']; ?>"
                                onchange="updateCart(<?php echo $product['id']; ?>, this.value)"
                                style="width: 60px; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 8px;">
                            <button onclick="removeFromCart(<?php echo $product['id']; ?>)"
                                style="color: var(--danger); background: none; border: none; cursor: pointer; font-size: 1.2rem;">
                                <i class="fas fa-trash-can"></i>
                            </button>
                        </div>
                        <div style="text-align: right; min-width: 100px;">
                            <p style="font-weight: 700;">$
                                <?php echo number_format($subtotal, 2); ?>
                            </p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <div class="cart-summary">
                <div
                    style="background: white; padding: 2rem; border-radius: var(--radius); box-shadow: var(--shadow); position: sticky; top: 120px;">
                    <h2 style="margin-bottom: 1.5rem;">Order Summary</h2>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                        <span>Subtotal</span>
                        <span>$
                            <?php echo number_format($total, 2); ?>
                        </span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                        <span>Delivery</span>
                        <span style="color: var(--primary);">FREE</span>
                    </div>
                    <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 1.5rem 0;">
                    <div
                        style="display: flex; justify-content: space-between; margin-bottom: 2rem; font-size: 1.25rem; font-weight: 800;">
                        <span>Total</span>
                        <span>$
                            <?php echo number_format($total, 2); ?>
                        </span>
                    </div>
                    <a href="checkout.php" class="btn btn-primary" style="width: 100%;">Proceed to Checkout</a>
                    <a href="products.php"
                        style="display: block; text-align: center; margin-top: 1rem; color: var(--text-muted); font-weight: 600;">Continue
                        Shopping</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>