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
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
    if ($stmt->execute([$id])) {
        $message = "User deleted successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customers - Admin</title>
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
                <a href="manage_orders.php"><i class="fas fa-shopping-bag"></i> Orders</a>
                <a href="manage_users.php" class="active"><i class="fas fa-users"></i> Customers</a>
                <a href="../logout.php" style="margin-top: 5rem; color: var(--danger);"><i
                        class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </aside>

        <main class="admin-main">
            <h1>Customer Management</h1>
            <p>Manage registered customers</p>

            <?php if ($message): ?>
                <div
                    style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: var(--radius); margin-top: 2rem;">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="data-card" style="margin-top: 2rem;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Registered On</th>
                            <th>Orders</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT u.*, COUNT(o.id) as order_count 
                                              FROM users u 
                                              LEFT JOIN orders o ON u.id = o.user_id 
                                              WHERE u.role = 'user' 
                                              GROUP BY u.id 
                                              ORDER BY u.created_at DESC");
                        while ($user = $stmt->fetch()) {
                            echo "<tr>
                                    <td>#{$user['id']}</td>
                                    <td>{$user['name']}</td>
                                    <td>{$user['email']}</td>
                                    <td>" . date('M d, Y', strtotime($user['created_at'])) . "</td>
                                    <td>{$user['order_count']} orders</td>
                                    <td>
                                        <a href='manage_users.php?delete={$user['id']}' class='btn btn-outline' style='padding: 0.25rem 0.5rem; color: var(--danger); border-color: var(--danger);' onclick='return confirm(\"Are you sure you want to delete this customer? This will NOT delete their orders.\")'>
                                            <i class='fas fa-trash'></i>
                                        </a>
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