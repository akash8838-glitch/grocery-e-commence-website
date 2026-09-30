<?php
include 'includes/header.php';

if (!isset($_SESSION['order_success'])) {
    header('Location: index.php');
    exit();
}

$order_id = $_SESSION['order_success'];
unset($_SESSION['order_success']);
?>

<div class="container" style="padding: 6rem 0; text-align: center;">
    <div
        style="max-width: 600px; margin: 0 auto; background: white; padding: 4rem; border-radius: var(--radius); box-shadow: var(--shadow-lg);">
        <div
            style="width: 80px; height: 80px; background: var(--primary-light); color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 2rem;">
            <i class="fas fa-check"></i>
        </div>
        <h1 style="margin-bottom: 1rem;">Order Placed Successfully!</h1>
        <p style="color: var(--text-muted); font-size: 1.1rem; margin-bottom: 2rem;">
            Thank you for shopping with us. Your order <strong>#
                <?php echo $order_id; ?>
            </strong> has been received and is being processed.
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center;">
            <a href="index.php" class="btn btn-primary">Go to Home</a>
            <a href="orders.php" class="btn btn-outline">My Orders</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>