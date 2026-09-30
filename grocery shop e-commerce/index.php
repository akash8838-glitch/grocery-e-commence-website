<?php include 'includes/header.php'; ?>

<section class="hero">
    <div class="container hero-content">
        <div class="hero-text">
            <h1>Fresh Groceries <br><span>Delivered to Your Door</span></h1>
            <p>Shop from our wide range of organic vegetables, pulses, and essential cooking oils at the best prices.
            </p>
            <div style="display: flex; gap: 1rem;">
                <a href="products.php" class="btn btn-primary">Shop Now</a>
                <a href="#categories" class="btn btn-outline">Browse Categories</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&q=80&w=800"
                alt="Groceries" style="width: 100%; border-radius: 30px; box-shadow: var(--shadow-lg);">
        </div>
    </div>
</section>

<section id="categories" class="container" style="padding: 4rem 0;">
    <div style="text-align: center; margin-bottom: 3rem;">
        <h2 style="font-size: 2rem; color: var(--text-dark);">Our Categories</h2>
        <p style="color: var(--text-muted);">Explore our variety of products</p>
    </div>

    <div class="product-grid">
        <?php
        $stmt = $pdo->query("SELECT * FROM categories");
        while ($category = $stmt->fetch()) {
            echo '
            <a href="products.php?category=' . $category['id'] . '" class="product-card" style="text-align: center; padding: 2rem;">
                <div style="font-size: 3rem; color: var(--primary); margin-bottom: 1rem;">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h3>' . $category['name'] . '</h3>
            </a>';
        }
        ?>
    </div>
</section>

<section class="container" style="padding: 4rem 0;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
        <div>
            <h2 style="font-size: 2rem; color: var(--text-dark);">Featured Products</h2>
            <p style="color: var(--text-muted);">Don\'t miss our best deals</p>
        </div>
        <a href="products.php" style="color: var(--primary); font-weight: 600;">View All <i
                class="fas fa-arrow-right"></i></a>
    </div>

    <div class="product-grid">
        <?php
        $stmt = $pdo->query("SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id LIMIT 4");
        if ($stmt->rowCount() > 0) {
            while ($product = $stmt->fetch()) {
                $discount_price = $product['price'] - ($product['price'] * ($product['discount'] / 100));
                echo '
                <div class="product-card">
                    ' . ($product['discount'] > 0 ? '<span class="product-badge">' . $product['discount'] . '% OFF</span>' : '') . '
                    <img src="' . ($product['image'] ?: 'https://via.placeholder.com/200') . '" alt="' . $product['name'] . '" class="product-image">
                    <span style="color: var(--text-muted); font-size: 0.8rem;">' . $product['cat_name'] . '</span>
                    <h3 class="product-name">' . $product['name'] . '</h3>
                    <div style="color: #fbbf24; margin-bottom: 1rem;">
                        <i class="fas fa-star"></i> ' . $product['rating'] . '
                    </div>
                    <div class="product-price">
                        <span class="price-current">$' . number_format($discount_price, 2) . '</span>
                        ' . ($product['discount'] > 0 ? '<span class="price-old">$' . number_format($product['price'], 2) . '</span>' : '') . '
                    </div>
                    <button onclick="addToCart(' . $product['id'] . ')" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-cart-plus"></i> Add to Cart
                    </button>
                </div>';
            }
        } else {
            echo '<p style="grid-column: 1/-1; text-align: center; color: var(--text-muted);">No products found. Start by adding some in the admin panel!</p>';
        }
        ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>