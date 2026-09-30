<?php include 'includes/header.php'; ?>

<div class="container" style="padding: 3rem 0;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 2.5rem;">Our Products</h1>
            <p style="color: var(--text-muted);">Browse through our fresh and organic collection</p>
        </div>

        <div style="display: flex; gap: 1rem;">
            <form action="products.php" method="GET" id="filterForm">
                <select name="category" onchange="this.form.submit()" class="form-input" style="width: auto;">
                    <option value="">All Categories</option>
                    <?php
                    $cats = $pdo->query("SELECT * FROM categories");
                    while ($cat = $cats->fetch()) {
                        $selected = (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : '';
                        echo '<option value="' . $cat['id'] . '" ' . $selected . '>' . $cat['name'] . '</option>';
                    }
                    ?>
                </select>
                <?php if (isset($_GET['search'])): ?>
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($_GET['search']); ?>">
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="product-grid">
        <?php
        $where = [];
        $params = [];

        if (isset($_GET['category']) && !empty($_GET['category'])) {
            $where[] = "category_id = ?";
            $params[] = $_GET['category'];
        }

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $where[] = "p.name LIKE ?";
            $params[] = "%" . $_GET['search'] . "%";
        }

        $sql = "SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id";
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

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
                    </div>';

                if ($product['stock'] > 0) {
                    echo '<button onclick="addToCart(' . $product['id'] . ')" class="btn btn-primary" style="width: 100%;">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                          </button>';
                } else {
                    echo '<button class="btn" style="width: 100%; background: #e5e7eb; color: #9ca3af; cursor: not-allowed;">
                            Out of Stock
                          </button>';
                }
                echo '</div>';
            }
        } else {
            echo '<div style="grid-column: 1/-1; text-align: center; padding: 4rem 0;">
                    <i class="fas fa-search" style="font-size: 4rem; color: #e5e7eb; margin-bottom: 1.5rem;"></i>
                    <h2 style="color: var(--text-muted);">No products found matching your criteria.</h2>
                    <a href="products.php" style="color: var(--primary); font-weight: 600;">Clear all filters</a>
                  </div>';
        }
        ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>