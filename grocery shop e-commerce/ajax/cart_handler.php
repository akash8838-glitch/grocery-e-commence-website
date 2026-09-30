<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $response = ['status' => 'error', 'message' => 'Invalid action'];

    switch ($action) {
        case 'add':
            // Check stock
            $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();

            if ($product) {
                $current_in_cart = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id] : 0;
                if ($product['stock'] > $current_in_cart) {
                    if (isset($_SESSION['cart'][$product_id])) {
                        $_SESSION['cart'][$product_id]++;
                    } else {
                        $_SESSION['cart'][$product_id] = 1;
                    }
                    $response = [
                        'status' => 'success',
                        'cart_count' => array_sum($_SESSION['cart'])
                    ];
                } else {
                    $response = ['status' => 'error', 'message' => 'Not enough stock available'];
                }
            } else {
                $response = ['status' => 'error', 'message' => 'Product not found'];
            }
            break;

        case 'update':
            $quantity = (int) $_POST['quantity'];
            if ($quantity > 0) {
                $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
                $stmt->execute([$product_id]);
                $product = $stmt->fetch();

                if ($product && $product['stock'] >= $quantity) {
                    $_SESSION['cart'][$product_id] = $quantity;
                    $response = ['status' => 'success'];
                } else {
                    $response = ['status' => 'error', 'message' => 'Not enough stock'];
                }
            } else {
                unset($_SESSION['cart'][$product_id]);
                $response = ['status' => 'success'];
            }
            break;

        case 'remove':
            if (isset($_SESSION['cart'][$product_id])) {
                unset($_SESSION['cart'][$product_id]);
                $response = ['status' => 'success'];
            }
            break;
    }

    echo json_encode($response);
    exit();
}
?>