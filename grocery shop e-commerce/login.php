<?php
require_once 'includes/db.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header('Location: admin/dashboard.php');
        } else {
            header('Location: index.php');
        }
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Super Mart Cart</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body
    style="background: linear-gradient(135deg, var(--primary-light) 0%, var(--bg-light) 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center;">

    <div class="auth-form-container">
        <div style="text-align: center; margin-bottom: 2rem;">
            <a href="index.php" class="logo" style="justify-content: center; font-size: 2rem; margin-bottom: 1rem;">
                <i class="fas fa-shopping-basket"></i>
                <span>Super Mart Cart</span>
            </a>
            <h2 style="font-size: 1.5rem; color: var(--text-dark);">Welcome Back</h2>
            <p style="color: var(--text-muted);">Please login to your account</p>
        </div>

        <?php if ($error): ?>
            <div
                style="background: #fee2e2; color: #b91c1c; padding: 0.75rem; border-radius: var(--radius); margin-bottom: 1rem; text-align: center;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div
                style="background: #d1fae5; color: #065f46; padding: 0.75rem; border-radius: var(--radius); margin-bottom: 1rem; text-align: center;">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-input" placeholder="john@example.com" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-input" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Login</button>
        </form>

        <p style="text-align: center; margin-top: 1.5rem; color: var(--text-muted);">
            Don't have an account? <a href="register.php" style="color: var(--primary); font-weight: 600;">Register
                here</a>
        </p>
    </div>

</body>

</html>