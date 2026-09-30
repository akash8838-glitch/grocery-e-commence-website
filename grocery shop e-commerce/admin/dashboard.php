<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Stats
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status='completed'")->fetchColumn() ?: 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Super Mart Cart</title>
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: white;
            padding: 2rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .stat-card h3 {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .stat-card p {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-dark);
        }

        .recent-orders {
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
                <a href="dashboard.php" class="active"><i class="fas fa-chart-line"></i> Dashboard</a>
                <a href="manage_products.php"><i class="fas fa-box"></i> Products</a>
                <a href="manage_orders.php"><i class="fas fa-shopping-bag"></i> Orders</a>
                <a href="manage_users.php"><i class="fas fa-users"></i> Customers</a>
                <a href="../logout.php" style="margin-top: 5rem; color: var(--danger);"><i
                        class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </aside>

        <main class="admin-main">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h1>Dashboard Overview</h1>
                <p>Welcome back, Admin!</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Revenue</h3>
                    <p>$
                        <?php echo number_format($total_revenue, 2); ?>
                    </p>
                </div>
                <div class="stat-card">
                    <h3>Total Orders</h3>
                    <p>
                        <?php echo $total_orders; ?>
                    </p>
                </div>
                <div class="stat-card">
                    <h3>Active Products</h3>
                    <p>
                        <?php echo $total_products; ?>
                    </p>
                </div>
                <div class="stat-card">
                    <h3>Customers</h3>
                    <p>
                        <?php echo $total_users; ?>
                    </p>
                </div>
            </div>

            <div class="recent-orders">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h2>Recent Orders</h2>
                    <a href="manage_orders.php" style="color: var(--primary); font-weight: 600;">View All</a>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5");
                        while ($order = $stmt->fetch()) {
                            echo "<tr>
                                    <td>#{$order['id']}</td>
                                    <td>{$order['customer_name']}</td>
                                    <td>$" . number_format($order['total_amount'], 2) . "</td>
                                    <td><span class='status-badge status-{$order['status']}'>" . ucfirst($order['status']) . "</span></td>
                                    <td>" . date('M d, Y', strtotime($order['created_at'])) . "</td>
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