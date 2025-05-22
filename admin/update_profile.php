<?php
session_start();
require_once 'db_connect.php'; // Your database connection file

// Check if user is logged in and request is POST
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$errors = [];

// Validate and sanitize input data
$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
$first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
$last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
$age = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 120]]);
$gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);

// Validate required fields
if (empty($username) || empty($email)) {
    $errors[] = "Username and email are required fields.";
}

if ($age === false) {
    $errors[] = "Please enter a valid age between 1 and 120.";
}

// Handle file upload if present
$profile_pic = null;
if (!empty($_FILES['profile_picture']['name'])) {
    $target_dir = "uploads/profile_pics/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true); // More secure permissions
    }
    
    // Get file info
    $file_type = $_FILES['profile_picture']['type'];
    $file_size = $_FILES['profile_picture']['size'];
    $file_tmp = $_FILES['profile_picture']['tmp_name'];
    $file_ext = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
    
    // Validate image
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $max_file_size = 2 * 1024 * 1024; // 2MB
    
    if (!in_array($file_type, $allowed_types) || !in_array($file_ext, $allowed_extensions)) {
        $errors[] = "Only JPG, JPEG, PNG & GIF files are allowed.";
    } elseif ($file_size > $max_file_size) {
        $errors[] = "File size must be less than 2MB.";
    } else {
        // Generate unique filename
        $filename = 'user_' . $user_id . '_' . bin2hex(random_bytes(8)) . '.' . $file_ext;
        $target_file = $target_dir . $filename;
        
        if (move_uploaded_file($file_tmp, $target_file)) {
            $profile_pic = $target_file;
            
            // Delete old profile picture if it exists
            if (!empty($_SESSION['profile_pic']) && file_exists($_SESSION['profile_pic'])) {
                unlink($_SESSION['profile_pic']);
            }
        } else {
            $errors[] = "There was an error uploading your file.";
        }
    }
}

// If there are errors, redirect back with errors
if (!empty($errors)) {
    $_SESSION['update_errors'] = $errors;
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

try {
    // Build the SQL query dynamically based on whether we have a new profile picture
    $sql = "UPDATE users SET 
            username = ?, 
            first_name = ?, 
            last_name = ?, 
            age = ?, 
            gender = ?, 
            email = ?, 
            phone = ?";
    
    $params = [
        $username, 
        $first_name, 
        $last_name, 
        $age, 
        $gender, 
        $email, 
        $phone
    ];
    
    if ($profile_pic !== null) {
        $sql .= ", profile_pic = ?";
        $params[] = $profile_pic;
    }
    
    $sql .= " WHERE id = ?";
    $params[] = $user_id;
    
    // Prepare and execute the statement
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    
    // Update session variables
    $_SESSION['username'] = $username;
    $_SESSION['first_name'] = $first_name;
    $_SESSION['last_name'] = $last_name;
    $_SESSION['age'] = $age;
    $_SESSION['gender'] = $gender;
    $_SESSION['email'] = $email;
    $_SESSION['phone'] = $phone;
    
    if ($profile_pic !== null) {
        $_SESSION['profile_pic'] = $profile_pic;
    }
    
    $_SESSION['update_success'] = "Profile updated successfully!";
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $_SESSION['update_errors'] = ["An error occurred while updating your profile. Please try again."];
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}