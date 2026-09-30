<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$message = '';
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    if ($stmt->execute([$id])) {
        $message = "Product deleted successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin</title>
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

        .data-card {
            background: white;
            padding: 2rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
        }

        th {
            text-align: left;
            padding: 1rem;
            border-bottom: 2px solid #f3f4f6;
            color: var(--text-muted);
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid #f3f4f6;
        }

        .action-btn {
            padding: 0.5rem;
            border-radius: 8px;
            color: white;
            margin-right: 0.5rem;
        }

        .btn-edit {
            background: var(--primary);
        }

        .btn-delete {
            background: var(--danger);
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
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h1>Manage Products</h1>
                <a href="add_product.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Product</a>
            </div>

            <?php if ($message): ?>
                <div
                    style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: var(--radius); margin-bottom: 2rem;">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="data-card">
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC");
                        while ($product = $stmt->fetch()) {
                            echo "<tr>
                                    <td><img src='" . ($product['image'] ?: 'https://via.placeholder.com/50') . "' style='width: 50px; height: 50px; object-fit: contain;'></td>
                                    <td>{$product['name']}</td>
                                    <td>{$product['cat_name']}</td>
                                    <td>$" . number_format($product['price'], 2) . "</td>
                                    <td><span style='color: " . ($product['stock'] <= 5 ? 'var(--danger)' : 'inherit') . "'>{$product['stock']} units</span></td>
                                    <td>
                                        <a href='edit_product.php?id={$product['id']}' class='action-btn btn-edit'><i class='fas fa-edit'></i></a>
                                        <a href='manage_products.php?delete={$product['id']}' class='action-btn btn-delete' onclick='return confirm(\"Are you sure?\")'><i class='fas fa-trash'></i></a>
                                    </td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>

</html>