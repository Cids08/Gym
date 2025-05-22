<?php
session_start();

// Check if token is provided
if (!isset($_GET['token']) || empty($_GET['token'])) {
    $_SESSION['reset_error'] = "Invalid or missing token";
    header("Location: forgot-password.php");
    exit();
}

$token = $_GET['token'];

// Database connection
$db_host = "localhost";
$db_user = "your_db_username"; // Change to your database username
$db_pass = "your_db_password"; // Change to your database password
$db_name = "muscle_city_fitness";

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    $_SESSION['reset_error'] = "Connection failed: " . $conn->connect_error;
    header("Location: forgot-password.php");
    exit();
}

// Verify token and check if it's expired
$current_time = date('Y-m-d H:i:s');
$sql = "SELECT id, username FROM users WHERE reset_token = ? AND reset_token_expires > ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $token, $current_time);
$stmt->execute();
$result = $stmt->get_result();

// If token is valid, show password reset form
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $_SESSION['reset_user_id'] = $user['id'];
    $_SESSION['reset_token'] = $token;
} else {
    $_SESSION['reset_error'] = "Invalid or expired token. Please request a new password reset link.";
    header("Location: forgot-password.php");
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Muscle City Fitness Gym</title>
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
        
        <?php if (isset($_SESSION['reset_error'])): ?>
            <div class="error-message">
                <?php echo $_SESSION['reset_error']; ?>
                <?php unset($_SESSION['reset_error']); ?>
            </div>
        <?php endif; ?>
        
        <div id="resetPasswordForm" class="form-section active">
            <form action="process-reset-password.php" method="POST">
                <input type="hidden" name="token" value="<?php echo $token; ?>">
                <input type="password" name="new_password" placeholder="New Password" required><br>
                <input type="password" name="confirm_password" placeholder="Confirm New Password" required><br>
                <button type="submit" class="login-btn">Reset Password</button>
            </form>
            <p>Password must be at least 8 characters and include at least one uppercase letter, one lowercase letter, and one number.</p>
            <p><a href="login.php" class="yellow-link">Back to Login</a></p>
        </div>
    </div>
</div>

<?php include '../partials/footer.php'; ?>

<script src="../public/js/index.js"></script>
</body>
</html>