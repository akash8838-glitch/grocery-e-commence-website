<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: manage_products.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $discount = $_POST['discount'];
    $stock = $_POST['stock'];
    $description = trim($_POST['description']);
    $image_url = trim($_POST['image_url']);

    $stmt = $pdo->prepare("UPDATE products SET name = ?, category_id = ?, price = ?, discount = ?, stock = ?, description = ?, image = ? WHERE id = ?");
    if ($stmt->execute([$name, $category_id, $price, $discount, $stock, $description, $image_url, $id])) {
        $success = "Product updated successfully!";
        // Refresh product data
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
    } else {
        $error = "Something went wrong!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background: var(--text-dark);
            color: white;
            padding: 2rem;
        }

        .sidebar-logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 3rem;
            display: block;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            color: #9ca3af;
            border-radius: var(--radius);
            transition: all 0.3s;
            margin-bottom: 0.5rem;
        }

        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background: #374151;
            color: white;
        }

        .admin-main {
            flex-grow: 1;
            padding: 2rem;
            background: #f3f4f6;
        }

        .form-card {
            background: white;
            padding: 3rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            max-width: 800px;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <div class="admin-container">
        <aside class="sidebar">
            <a href="../index.php" class="sidebar-logo">
                <i class="fas fa-shopping-basket"></i>
                Super Mart Admin
            </a>
            <nav class="sidebar-nav">
                <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
                <a href="manage_products.php" class="active"><i class="fas fa-box"></i> Products</a>
                <a href="manage_orders.php"><i class="fas fa-shopping-bag"></i> Orders</a>
                <a href="manage_users.php"><i class="fas fa-users"></i> Customers</a>
                <a href="../logout.php" style="margin-top: 5rem; color: var(--danger);"><i
                        class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </aside>

        <main class="admin-main">
            <div style="margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;">
                <a href="manage_products.php" style="color: var(--text-muted);"><i class="fas fa-arrow-left"></i>
                    Back</a>
                <h1>Edit Product:
                    <?php echo $product['name']; ?>
                </h1>
            </div>

            <?php if ($success): ?>
                <div
                    style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: var(--radius); margin-bottom: 2rem; max-width: 800px; margin: 0 auto 2rem;">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <div class="form-card">
                <form action="edit_product.php?id=<?php echo $id; ?>" method="POST">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group">
                            <label>Product Name</label>
                            <input type="text" name="name" class="form-input" required
                                value="<?php echo htmlspecialchars($product['name']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category_id" class="form-input" required>
                                <?php
                                $cats = $pdo->query("SELECT * FROM categories");
                                while ($cat = $cats->fetch()) {
                                    $selected = ($cat['id'] == $product['category_id']) ? 'selected' : '';
                                    echo "<option value='{$cat['id']}' $selected>{$cat['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Price ($)</label>
                            <input type="number" step="0.01" name="price" class="form-input" required
                                value="<?php echo $product['price']; ?>">
                        </div>
                        <div class="form-group">
                            <label>Discount (%)</label>
                            <input type="number" name="discount" class="form-input"
                                value="<?php echo $product['discount']; ?>">
                        </div>
                        <div class="form-group">
                            <label>Stock Quantity</label>
                            <input type="number" name="stock" class="form-input" required
                                value="<?php echo $product['stock']; ?>">
                        </div>
                        <div class="form-group">
                            <label>Image URL</label>
                            <input type="url" name="image_url" class="form-input"
                                value="<?php echo htmlspecialchars($product['image']); ?>">
                        </div>
                    </div>
                    <div class="form-group" style="margin-top: 1.5rem;">
                        <label>Description</label>
                        <textarea name="description" class="form-input"
                            rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 2rem;">Update
                        Product</button>
                </form>
            </div>
        </main>
    </div>
</body>

</html>