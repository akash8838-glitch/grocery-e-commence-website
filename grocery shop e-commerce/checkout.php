<?php
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['success'] = "Please login to place an order.";
    header('Location: login.php');
    exit();
}

if (empty($_SESSION['cart'])) {
    header('Location: products.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address']);
    $payment_method = $_POST['payment_method'];
    $user_id = $_SESSION['user_id'];

    try {
        $pdo->beginTransaction();

        // Calculate total
        $total = 0;
        $items = [];
        foreach ($_SESSION['cart'] as $pid => $qty) {
            $stmt = $pdo->prepare("SELECT price, discount, stock FROM products WHERE id = ?");
            $stmt->execute([$pid]);
            $product = $stmt->fetch();

            if ($product['stock'] < $qty) {
                throw new Exception("Not enough stock for one of the items.");
            }

            $price = $product['price'] - ($product['price'] * ($product['discount'] / 100));
            $total += $price * $qty;
            $items[] = [
                'id' => $pid,
                'qty' => $qty,
                'price' => $price
            ];
        }

        // Create order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, address, payment_method) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $total, $address, $payment_method]);
        $order_id = $pdo->lastInsertId();

        // Add items and reduce stock
        $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt_stock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

        foreach ($items as $item) {
            $stmt_item->execute([$order_id, $item['id'], $item['qty'], $item['price']]);
            $stmt_stock->execute([$item['qty'], $item['id']]);
        }

        $pdo->commit();
        $_SESSION['cart'] = [];
        $_SESSION['order_success'] = $order_id;
        header('Location: order_success.php');
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        $error = $e->getMessage();
    }
}
?>

<div class="container" style="padding: 4rem 0;">
    <div style="max-width: 800px; margin: 0 auto;">
        <h1 style="margin-bottom: 2rem;">Checkout</h1>

        <?php if ($error): ?>
            <div
                style="background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: var(--radius); margin-bottom: 2rem;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="checkout.php" method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;">
            <div class="checkout-form">
                <div style="background: white; padding: 2rem; border-radius: var(--radius); box-shadow: var(--shadow);">
                    <h2 style="margin-bottom: 1.5rem;">Shipping details</h2>
                    <div class="form-group">
                        <label>Delivery Address</label>
                        <textarea name="address" class="form-input" rows="4" required
                            placeholder="Enter your full address here..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Payment Method</label>
                        <select name="payment_method" class="form-input" required>
                            <option value="cod">Cash on Delivery</option>
                            <option value="card">Credit/Debit Card (Simple Demo)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="order-preview">
                <div style="background: white; padding: 2rem; border-radius: var(--radius); box-shadow: var(--shadow);">
                    <h2 style="margin-bottom: 1.5rem;">Your Order</h2>
                    <?php
                    $total = 0;
                    foreach ($_SESSION['cart'] as $pid => $qty):
                        $stmt = $pdo->prepare("SELECT name, price, discount FROM products WHERE id = ?");
                        $stmt->execute([$pid]);
                        $product = $stmt->fetch();
                        $price = $product['price'] - ($product['price'] * ($product['discount'] / 100));
                        $subtotal = $price * $qty;
                        $total += $subtotal;
                        ?>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                            <span style="color: var(--text-muted);">
                                <?php echo $product['name']; ?> x
                                <?php echo $qty; ?>
                            </span>
                            <span style="font-weight: 600;">$
                                <?php echo number_format($subtotal, 2); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                    <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 1.5rem 0;">
                    <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 800;">
                        <span>Total</span>
                        <span>$
                            <?php echo number_format($total, 2); ?>
                        </span>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 2rem;">Place
                        Order</button>
                    <p style="text-align: center; margin-top: 1rem; font-size: 0.8rem; color: var(--text-muted);">
                        By placing an order, you agree to our terms and conditions.
                    </p>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>