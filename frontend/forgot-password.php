<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Muscle City Fitness Gym</title>
    <link rel="stylesheet" href="../public/css/styles.css">
    <link rel="stylesheet" href="../public/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php include '../partials/header.php'; ?>
<?php include '../partials/navbar2.php'; ?>

<div class="login-container">
    <div class="login-content">
        <h1>Reset Password</h1>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message">
                <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        
        <div id="forgotPasswordForm" class="form-section active">
            <form action="process-forgot-password.php" method="POST">
                <input type="email" name="email" placeholder="Enter your email" required><br>
                <button type="submit" class="login-btn">Send Reset Link</button>
            </form>
            <p><a href="login.php" class="yellow-link">Back to Login</a></p>
        </div>
    </div>
</div>