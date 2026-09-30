</main>

<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="index.php" class="logo"
                    style="color: var(--white); margin-bottom: 1.5rem; display: inline-flex;">
                    <i class="fas fa-shopping-basket"></i>
                    <span>Super Mart Cart</span>
                </a>
                <p style="color: #9ca3af;">Premium grocery delivery at your doorstep. Fresh products, best prices.</p>
            </div>
            <div>
                <h3 style="margin-bottom: 1.5rem;">Quick Links</h3>
                <ul style="color: #9ca3af; display: grid; gap: 0.5rem;">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="products.php">All Products</a></li>
                    <li><a href="cart.php">Shopping Cart</a></li>
                </ul>
            </div>
            <div>
                <h3 style="margin-bottom: 1.5rem;">Categories</h3>
                <ul style="color: #9ca3af; display: grid; gap: 0.5rem;">
                    <li><a href="products.php?category=Vegetables">Vegetables</a></li>
                    <li><a href="products.php?category=Pulses">Pulses</a></li>
                    <li><a href="products.php?category=Seeds">Seeds & Spices</a></li>
                </ul>
            </div>
            <div>
                <h3 style="margin-bottom: 1.5rem;">Contact Us</h3>
                <ul style="color: #9ca3af; display: grid; gap: 1rem;">
                    <li><i class="fas fa-location-dot"></i> 123 Super Mart St, Grocery City</li>
                    <li><i class="fas fa-phone"></i> +1 234 567 890</li>
                    <li><i class="fas fa-envelope"></i> support@supermart.com</li>
                </ul>
            </div>
        </div>
        <div style="border-top: 1px solid #374151; padding-top: 2rem; text-align: center; color: #6b7280;">
            <p>&copy;
                <?php echo date('Y'); ?> Super Mart Cart. All rights reserved.
            </p>
        </div>
    </div>
</footer>

<script src="assets/js/main.js"></script>
</body>

</html>