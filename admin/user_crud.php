<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Log incoming request
file_put_contents('debug.log', "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n", FILE_APPEND);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    file_put_contents('debug.log', "POST DATA: " . print_r($_POST, true) . "\n", FILE_APPEND);
}

// Create a debug log function
function debug_log($message) {
    $timestamp = date('[Y-m-d H:i:s] ');
    error_log($timestamp . "USER_CRUD DEBUG: " . $message . "\n", 3, "debug.log");
}

debug_log("Script started - Request method: " . $_SERVER["REQUEST_METHOD"]);

// Database connection with error handling
$conn = new mysqli("localhost", "root", "", "muscle_city_gym");

if ($conn->connect_error) {
    debug_log("Database connection failed: " . $conn->connect_error);
    die("Connection failed: " . $conn->connect_error);
}

debug_log("Database connected successfully");

// Log all POST data for debugging
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    debug_log("POST data received: " . print_r($_POST, true));
}

// Handle GET operations (delete, suspend, renew)
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    debug_log("Attempting to delete user ID: " . $id);
    
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'user'");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        debug_log("User deleted successfully");
        $_SESSION['success'] = "Member deleted successfully!";
    } else {
        debug_log("Delete failed: " . $stmt->error);
        $_SESSION['error'] = "Error deleting member: " . $stmt->error;
    }
    
    $stmt->close();
    header("Location: users.php");
    exit();
}

if (isset($_GET['suspend'])) {
    $id = intval($_GET['suspend']);
    debug_log("Attempting to suspend user ID: " . $id);
    
    $stmt = $conn->prepare("UPDATE users SET status = 'expired' WHERE id = ? AND role = 'user'");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        debug_log("User suspended successfully");
        $_SESSION['success'] = "Member suspended successfully!";
    } else {
        debug_log("Suspend failed: " . $stmt->error);
        $_SESSION['error'] = "Error suspending member: " . $stmt->error;
    }
    
    $stmt->close();
    header("Location: users.php");
    exit();
}

if (isset($_GET['renew'])) {
    $id = intval($_GET['renew']);
    debug_log("Attempting to renew user ID: " . $id);
    
    $stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE id = ? AND role = 'user'");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        debug_log("User renewed successfully");
        $_SESSION['success'] = "Member renewed successfully!";
    } else {
        debug_log("Renew failed: " . $stmt->error);
        $_SESSION['error'] = "Error renewing member: " . $stmt->error;
    }
    
    $stmt->close();
    header("Location: users.php");
    exit();
}

// Handle POST operations
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // CREATE USER
    if (isset($_POST['create'])) {
        debug_log("Processing CREATE request");
        
        // Validate required fields
        $required_fields = ['first_name', 'last_name', 'username', 'email', 'password', 'package', 'status'];
        $missing_fields = [];
        
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                $missing_fields[] = $field;
            }
        }
        
        if (!empty($missing_fields)) {
            debug_log("Missing required fields: " . implode(', ', $missing_fields));
            $_SESSION['error'] = "Missing required fields: " . implode(', ', $missing_fields);
            header("Location: users.php");
            exit();
        }
        
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $package = $_POST['package'];
        $status = $_POST['status'];
        
        debug_log("Sanitized data - Name: $first_name $last_name, Username: $username, Email: $email, Package: $package, Status: $status");

        // Check if username or email already exists
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        if (!$check_stmt) {
            debug_log("Prepare failed for duplicate check: " . $conn->error);
            $_SESSION['error'] = "Database error occurred";
            header("Location: users.php");
            exit();
        }
        
        $check_stmt->bind_param("ss", $username, $email);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            debug_log("Duplicate username or email found");
            $_SESSION['error'] = "Username or email already exists!";
            $check_stmt->close();
            header("Location: users.php");
            exit();
        }
        $check_stmt->close();

        // Insert new user
        $insert_sql = "INSERT INTO users (first_name, last_name, username, email, password, package, status, role, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'user', NOW())";
        debug_log("Preparing INSERT statement: " . $insert_sql);
        
        $stmt = $conn->prepare($insert_sql);
        if (!$stmt) {
            debug_log("Prepare failed for INSERT: " . $conn->error);
            $_SESSION['error'] = "Database prepare error: " . $conn->error;
            header("Location: users.php");
            exit();
        }
        
        $stmt->bind_param("sssssss", $first_name, $last_name, $username, $email, $password, $package, $status);
        
        debug_log("Executing INSERT with bound parameters");
        
        if ($stmt->execute()) {
            $new_id = $conn->insert_id;
            debug_log("User created successfully with ID: " . $new_id);
            $_SESSION['success'] = "Member added successfully!";
        } else {
            debug_log("INSERT execution failed: " . $stmt->error);
            $_SESSION['error'] = "Error adding member: " . $stmt->error;
        }
        $stmt->close();
    }

    // UPDATE USER
    if (isset($_POST['update'])) {
        debug_log("Processing UPDATE request");
        
        if (empty($_POST['id'])) {
            debug_log("No ID provided for update");
            $_SESSION['error'] = "No user ID provided for update";
            header("Location: users.php");
            exit();
        }
        
        $id = intval($_POST['id']);
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $package = $_POST['package'];
        $status = $_POST['status'];

        debug_log("Update data - ID: $id, Name: $first_name $last_name, Username: $username, Email: $email, Package: $package, Status: $status");

        // Check if username or email already exists for other users
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
        if (!$check_stmt) {
            debug_log("Prepare failed for duplicate check in update: " . $conn->error);
            $_SESSION['error'] = "Database error occurred";
            header("Location: users.php");
            exit();
        }
        
        $check_stmt->bind_param("ssi", $username, $email, $id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            debug_log("Duplicate username or email found for update");
            $_SESSION['error'] = "Username or email already exists for another user!";
            $check_stmt->close();
            header("Location: users.php");
            exit();
        }
        $check_stmt->close();

        // Update user
        if (!empty($_POST['password'])) {
            debug_log("Updating user with new password");
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $update_sql = "UPDATE users SET first_name=?, last_name=?, username=?, email=?, package=?, status=?, password=? WHERE id=? AND role='user'";
            $stmt = $conn->prepare($update_sql);
            if (!$stmt) {
                debug_log("Prepare failed for UPDATE with password: " . $conn->error);
                $_SESSION['error'] = "Database prepare error: " . $conn->error;
                header("Location: users.php");
                exit();
            }
            $stmt->bind_param("sssssssi", $first_name, $last_name, $username, $email, $package, $status, $password, $id);
        } else {
            debug_log("Updating user without password change");
            $update_sql = "UPDATE users SET first_name=?, last_name=?, username=?, email=?, package=?, status=? WHERE id=? AND role='user'";
            $stmt = $conn->prepare($update_sql);
            if (!$stmt) {
                debug_log("Prepare failed for UPDATE without password: " . $conn->error);
                $_SESSION['error'] = "Database prepare error: " . $conn->error;
                header("Location: users.php");
                exit();
            }
            $stmt->bind_param("ssssssi", $first_name, $last_name, $username, $email, $package, $status, $id);
        }
        
        if ($stmt->execute()) {
            debug_log("User updated successfully");
            $_SESSION['success'] = "Member updated successfully!";
        } else {
            debug_log("UPDATE execution failed: " . $stmt->error);
            $_SESSION['error'] = "Error updating member: " . $stmt->error;
        }
        $stmt->close();
    }

    debug_log("Redirecting to users.php");
    header("Location: users.php");
    exit();
}

$conn->close();
debug_log("Script ended");
?>