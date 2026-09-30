<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $order_id]);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin</title>
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

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #b91c1c;
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
                <a href="manage_products.php"><i class="fas fa-box"></i> Products</a>
                <a href="manage_orders.php" class="active"><i class="fas fa-shopping-bag"></i> Orders</a>
                <a href="manage_users.php"><i class="fas fa-users"></i> Customers</a>
                <a href="../logout.php" style="margin-top: 5rem; color: var(--danger);"><i
                        class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </aside>

        <main class="admin-main">
            <h1>Customer Orders</h1>

            <div class="data-card" style="margin-top: 2rem;">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");
                        while ($order = $stmt->fetch()) {
                            // Fetch items for this order
                            $stmt_items = $pdo->prepare("SELECT COUNT(*) FROM order_items WHERE order_id = ?");
                            $stmt_items->execute([$order['id']]);
                            $item_count = $stmt_items->fetchColumn();

                            echo "<tr>
                                    <td>#{$order['id']}</td>
                                    <td>{$order['customer_name']}</td>
                                    <td>{$item_count} items</td>
                                    <td>$" . number_format($order['total_amount'], 2) . "</td>
                                    <td><span class='status-badge status-{$order['status']}'>" . ucfirst($order['status']) . "</span></td>
                                    <td>
                                        <form action='manage_orders.php' method='POST' style='display: flex; gap: 0.5rem;'>
                                            <input type='hidden' name='order_id' value='{$order['id']}'>
                                            <select name='status' class='form-input' style='padding: 0.25rem; width: auto;'>
                                                <option value='pending' " . ($order['status'] == 'pending' ? 'selected' : '') . ">Pending</option>
                                                <option value='completed' " . ($order['status'] == 'completed' ? 'selected' : '') . ">Completed</option>
                                                <option value='cancelled' " . ($order['status'] == 'cancelled' ? 'selected' : '') . ">Cancelled</option>
                                            </select>
                                            <button type='submit' name='update_status' class='btn btn-primary' style='padding: 0.25rem 0.5rem;'><i class='fas fa-check'></i></button>
                                        </form>
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