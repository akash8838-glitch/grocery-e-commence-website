<?php
require_once 'includes/db.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email already registered!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$name, $email, $hashed_password])) {
                $_SESSION['success'] = "Registration successful! Please login.";
                header('Location: login.php');
                exit();
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Super Mart Cart</title>
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
            <h2 style="font-size: 1.5rem; color: var(--text-dark);">Create an Account</h2>
            <p style="color: var(--text-muted);">Join our grocery family today</p>
        </div>

        <?php if ($error): ?>
            <div
                style="background: #fee2e2; color: #b91c1c; padding: 0.75rem; border-radius: var(--radius); margin-bottom: 1rem; text-align: center;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" id="registerForm">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" name="name" id="name" class="form-input" placeholder="John Doe" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-input" placeholder="john@example.com" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-input" placeholder="••••••••" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-input"
                    placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Register Now</button>
        </form>

        <p style="text-align: center; margin-top: 1.5rem; color: var(--text-muted);">
            Already have an account? <a href="login.php" style="color: var(--primary); font-weight: 600;">Login here</a>
        </p>
    </div>

</body>

</html>