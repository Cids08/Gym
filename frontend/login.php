<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Muscle City Fitness Gym</title>
    <link rel="stylesheet" href="../public/css/styles.css">
    <link rel="stylesheet" href="../public/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php include '../partials/header.php'; ?>
<?php include '../partials/navbar.php'; ?>

<div class="login-container">
    <div class="login-content">
        <h1>Login</h1>
        <?php
            // Display error messages if they exist
            if (isset($_SESSION['login_error'])) {
                echo '<div class="error-message">' . $_SESSION['login_error'] . '</div>';
                unset($_SESSION['login_error']);
            }

            // Display success messages
            if (isset($_SESSION['login_success'])) {
                echo '<div class="success-message">' . $_SESSION['login_success'] . '</div>';
                unset($_SESSION['login_success']);
            }

            // Display signup success message (redirected from signup)
            if (isset($_SESSION['signup_success'])) {
                echo '<div class="success-message">' . $_SESSION['signup_success'] . '</div>';
                unset($_SESSION['signup_success']);
            }
        ?>
        <div id="loginForm" class="form-section active">
            <form action="process-login.php" method="POST">
                <input type="text" name="username" placeholder="Username" required><br>
                <input type="password" name="password" placeholder="Password" required><br>
                <button type="submit" class="login-btn">Login</button>
            </form>
            <p><a href="forgot-password.php" class="yellow-link">Forgot Password?</a></p>
            <p>Don't have an account? <a href="signup.php" class="yellow-link">Sign up</a></p>
        </div>
    </div>
</div>

<?php include '../partials/footer.php'; ?>

<script src="../public/js/index.js"></script>
</body>
</html>