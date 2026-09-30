<?php
session_start();
require_once __DIR__ . '/db.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Mart Cart - Fresh Groceries Delivered</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <header class="header">
        <div class="container navbar">
            <a href="index.php" class="logo">
                <i class="fas fa-shopping-basket"></i>
                <span>Super Mart Cart</span>
            </a>

            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="products.php">Products</a>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <a href="admin/dashboard.php">Admin Panel</a>
                <?php endif; ?>
            </nav>

            <div class="nav-actions">
                <div class="search-bar" style="position: relative;">
                    <form action="products.php" method="GET">
                        <input type="text" name="search" placeholder="Search groceries..." class="form-input"
                            style="padding-right: 2.5rem; width: 250px;">
                        <button type="submit"
                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer;">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <a href="cart.php" class="btn btn-outline" style="padding: 0.5rem 1rem;">
                    <i class="fas fa-cart-shopping"></i>
                    <span id="cart-count">
                        <?php
                        $count = 0;
                        if (isset($_SESSION['cart'])) {
                            foreach ($_SESSION['cart'] as $qty)
                                $count += $qty;
                        }
                        echo $count;
                        ?>
                    </span>
                </a>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <a href="logout.php" class="btn btn-primary" style="padding: 0.5rem 1rem;">Logout</a>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary" style="padding: 0.5rem 1rem;">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <main>