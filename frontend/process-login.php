<?php
session_start();

// Database connection
$db_host = "localhost";
$db_user = "root"; 
$db_pass = ""; 
$db_name = "muscle_city_gym";

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    $_SESSION['login_error'] = "Connection failed: " . $conn->connect_error;
    header("Location: ../frontend/login.php");
    exit();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // Validate inputs
    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = "Username and password are required";
        header("Location: ../frontend/login.php");
        exit();
    }

    // Prepare SQL statement to prevent SQL injection
    $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password is correct, start a new session
            session_regenerate_id();
            
            // Store data in session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; // store user role
            
            // Set last login time
            $updateSql = "UPDATE users SET last_login = NOW() WHERE id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("i", $user['id']);
            $updateStmt->execute();
            $updateStmt->close();
            
            // Redirect based on role
            if ($user['role'] === 'admin') {
                $stmt->close();
                header("Location: ../admin/AdminDashboard.php");
                exit(); // always good to add after redirect
            } else {
                $stmt->close();
                header("Location: ../user/UserDashboard.php");
                exit(); // always good to add after redirect
            }
        } else {
            // Password is not correct
            $stmt->close();
            $_SESSION['login_error'] = "Invalid username or password";
            header("Location: ../frontend/login.php");
            exit();
        }
    } else {
        // User does not exist
        $stmt->close();
        $_SESSION['login_error'] = "Invalid username or password";
        header("Location: ../frontend/login.php");
        exit();
    }
}

// Close connection
$conn->close();
?>