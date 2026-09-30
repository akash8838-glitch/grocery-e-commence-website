<?php
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
?>

<div class="container" style="padding: 4rem 0;">
    <h1 style="margin-bottom: 2rem;">My Orders</h1>

    <?php
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);

    if ($stmt->rowCount() > 0):
        while ($order = $stmt->fetch()):
            ?>
            <div
                style="background: white; padding: 2rem; border-radius: var(--radius); shadow: var(--shadow); margin-bottom: 2rem;">
                <div
                    style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #f3f4f6;">
                    <div>
                        <h3 style="margin-bottom: 0.25rem;">Order #
                            <?php echo $order['id']; ?>
                        </h3>
                        <p style="color: var(--text-muted); font-size: 0.9rem;">Placed on
                            <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                        </p>
                    </div>
                    <div>
                        <span class="status-badge status-<?php echo $order['status']; ?>"
                            style="padding: 0.5rem 1.5rem; border-radius: 30px; font-weight: 700;">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </div>
                </div>

                <div style="display: grid; gap: 1rem;">
                    <?php
                    $stmt_items = $pdo->prepare("SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                    $stmt_items->execute([$order['id']]);
                    while ($item = $stmt_items->fetch()):
                        ?>
                        <div style="display: flex; align-items: center; gap: 1.5rem;">
                            <img src="<?php echo $item['image'] ?: 'https://via.placeholder.com/50'; ?>"
                                style="width: 50px; height: 50px; object-fit: contain;">
                            <div style="flex-grow: 1;">
                                <p style="font-weight: 600;">
                                    <?php echo $item['name']; ?>
                                </p>
                                <p style="color: var(--text-muted); font-size: 0.8rem;">Qty:
                                    <?php echo $item['quantity']; ?>
                                </p>
                            </div>
                            <p style="font-weight: 600;">$
                                <?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                            </p>
                        </div>
                    <?php endwhile; ?>
                </div>

                <div
                    style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center;">
                    <p style="color: var(--text-muted); font-size: 0.9rem;">Paid via:
                        <?php echo strtoupper($order['payment_method']); ?>
                    </p>
                    <p style="font-size: 1.25rem; font-weight: 800;">Total: <span style="color: var(--primary);">$
                            <?php echo number_format($order['total_amount'], 2); ?>
                        </span></p>
                </div>
            </div>
        <?php
        endwhile;
    else:
        ?>
        <div style="text-align: center; padding: 4rem 0;">
            <i class="fas fa-box-open" style="font-size: 5rem; color: #e5e7eb; margin-bottom: 2rem;"></i>
            <h2>No orders found</h2>
            <p style="color: var(--text-muted); margin-bottom: 2rem;">You haven't placed any orders yet.</p>
            <a href="products.php" class="btn btn-primary">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>

<style>
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

<?php include 'includes/footer.php'; ?>