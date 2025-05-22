<?php
session_start();

// Check authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "muscle_city_gym");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = [];
    $success_messages = [];
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // Get admin user ID
        $admin_id = intval($_SESSION['user_id']);
        
        // Sanitize and validate input data
        $gym_name = trim($_POST['gym_name'] ?? '');
        $gym_location = trim($_POST['gym_location'] ?? '');
        $gym_contact = trim($_POST['gym_contact'] ?? '');
        $payment_gateway = trim($_POST['payment_gateway'] ?? '');
        
        $admin_username = trim($_POST['admin_username'] ?? '');
        $admin_name = trim($_POST['admin_name'] ?? '');
        $admin_email = trim($_POST['admin_email'] ?? '');
        $admin_password = trim($_POST['admin_password'] ?? '');
        
        // Validate required fields
        if (empty($gym_name)) $errors[] = "Gym name is required";
        if (empty($gym_location)) $errors[] = "Gym location is required";
        if (empty($gym_contact)) $errors[] = "Gym contact is required";
        if (empty($admin_username)) $errors[] = "Admin username is required";
        if (empty($admin_email)) $errors[] = "Admin email is required";
        
        // Validate email format
        if (!empty($admin_email) && !filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }
        
        // Check if username already exists (excluding current user)
        $username_check = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $username_check->bind_param("si", $admin_username, $admin_id);
        $username_check->execute();
        if ($username_check->get_result()->num_rows > 0) {
            $errors[] = "Username already exists";
        }
        
        // Check if email already exists (excluding current user)
        $email_check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $email_check->bind_param("si", $admin_email, $admin_id);
        $email_check->execute();
        if ($email_check->get_result()->num_rows > 0) {
            $errors[] = "Email already exists";
        }
        
        // If there are validation errors, stop processing
        if (!empty($errors)) {
            throw new Exception(implode(", ", $errors));
        }
        
        // Update or Insert Gym Settings
        $gym_check = $conn->query("SELECT id FROM gym_settings LIMIT 1");
        
        if ($gym_check->num_rows > 0) {
            // Update existing settings
            $gym_stmt = $conn->prepare("UPDATE gym_settings SET 
                gym_name = ?, 
                gym_location = ?, 
                gym_contact = ?, 
                payment_gateway_key = ?, 
                updated_at = NOW() 
                WHERE id = (SELECT id FROM gym_settings LIMIT 1)");
            $gym_stmt->bind_param("ssss", $gym_name, $gym_location, $gym_contact, $payment_gateway);
        } else {
            // Insert new settings
            $gym_stmt = $conn->prepare("INSERT INTO gym_settings 
                (gym_name, gym_location, gym_contact, payment_gateway_key, created_at, updated_at) 
                VALUES (?, ?, ?, ?, NOW(), NOW())");
            $gym_stmt->bind_param("ssss", $gym_name, $gym_location, $gym_contact, $payment_gateway);
        }
        
        if (!$gym_stmt->execute()) {
            throw new Exception("Failed to update gym settings: " . $gym_stmt->error);
        }
        $success_messages[] = "Gym information updated successfully";
        
        // Update Admin Profile
        if (!empty($admin_password)) {
            // Validate password strength
            if (strlen($admin_password) < 8) {
                throw new Exception("Password must be at least 8 characters long");
            }
            
            // Update with new password
            $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
            $user_stmt = $conn->prepare("UPDATE users SET 
                username = ?, 
                name = ?, 
                email = ?, 
                password = ?, 
                updated_at = NOW() 
                WHERE id = ?");
            $user_stmt->bind_param("ssssi", $admin_username, $admin_name, $admin_email, $hashed_password, $admin_id);
            $success_messages[] = "Admin profile and password updated successfully";
        } else {
            // Update without changing password
            $user_stmt = $conn->prepare("UPDATE users SET 
                username = ?, 
                name = ?, 
                email = ?, 
                updated_at = NOW() 
                WHERE id = ?");
            $user_stmt->bind_param("sssi", $admin_username, $admin_name, $admin_email, $admin_id);
            $success_messages[] = "Admin profile updated successfully";
        }
        
        if (!$user_stmt->execute()) {
            throw new Exception("Failed to update admin profile: " . $user_stmt->error);
        }
        
        // Update session username if it was changed
        if ($_SESSION['username'] !== $admin_username) {
            $_SESSION['username'] = $admin_username;
        }
        
        // Commit transaction
        $conn->commit();
        
        // Set success message
        $_SESSION['settings_saved'] = implode(" and ", $success_messages) . "!";
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        
        // Set error message
        $_SESSION['settings_error'] = "Error: " . $e->getMessage();
    }
    
    // Close prepared statements
    if (isset($gym_stmt)) $gym_stmt->close();
    if (isset($user_stmt)) $user_stmt->close();
    if (isset($username_check)) $username_check->close();
    if (isset($email_check)) $email_check->close();
    
    // Close connection
    $conn->close();
    
    // Redirect back to settings page
    header("Location: settings.php");
    exit();
}

// If not POST request, redirect to settings
header("Location: settings.php");
exit();
?>