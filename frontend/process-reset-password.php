<?php
session_start();

// Check if user is properly authenticated for password reset
if (!isset($_SESSION['reset_user_id']) || !isset($_SESSION['reset_token']) || !isset($_POST['token'])) {
    $_SESSION['reset_error'] = "Invalid request";
    header("Location: forgot-password.php");
    exit();
}

// Verify that the token from the form matches the one in session
if ($_SESSION['reset_token'] !== $_POST['token']) {
    $_SESSION['reset_error'] = "Token mismatch";
    header("Location: forgot-password.php");
    exit();
}

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
    header("Location: reset-password.php?token=" . $_POST['token']);
    exit();
}

// Get and validate passwords
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

// Check if passwords match
if ($new_password !== $confirm_password) {
    $_SESSION['reset_error'] = "Passwords do not match";
    header("Location: reset-password.php?token=" . $_POST['token']);
    exit();
}

// Validate password strength
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $new_password)) {
    $_SESSION['reset_error'] = "Password must be at least 8 characters and include at least one uppercase letter, one lowercase letter, and one number";
    header("Location: reset-password.php?token=" . $_POST['token']);
    exit();
}

// Hash the new password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update password and clear reset token
$user_id = $_SESSION['reset_user_id'];
$sql = "UPDATE users SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $hashed_password, $user_id);

if ($stmt->execute()) {
    // Clear reset session variables
    unset($_SESSION['reset_user_id']);
    unset($_SESSION['reset_token']);
    
    // Set success message
    $_SESSION['login_success'] = "Password has been reset successfully. You can now log in with your new password.";
    header("Location: login.php");
} else {
    $_SESSION['reset_error'] = "Password reset failed. Please try again.";
    header("Location: reset-password.php?token=" . $_POST['token']);
}

$stmt->close();
$conn->close();
?>