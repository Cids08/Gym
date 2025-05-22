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
    $_SESSION['signup_error'] = "Connection failed: " . $conn->connect_error;
    header("Location: signup.php");
    exit();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password']; // Will be hashed before storing
    $email = $conn->real_escape_string($_POST['email']);
    $age = (int)$_POST['age'];
    $gender = $conn->real_escape_string($_POST['gender']);
    $phone = $conn->real_escape_string($_POST['phone']);
    
    // Validate inputs
    $errors = [];
    
    // Validate name fields
    if (empty($first_name) || empty($last_name)) {
        $errors[] = "First name and last name are required";
    }
    
    // Validate username (alphanumeric, 4-20 characters)
    if (empty($username) || !preg_match('/^[a-zA-Z0-9_]{4,20}$/', $username)) {
        $errors[] = "Username must be 4-20 characters and may only contain letters, numbers, and underscores";
    }
    
    // Validate password (at least 8 characters, including one uppercase, one lowercase, one number)
    if (empty($password) || !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
        $errors[] = "Password must be at least 8 characters and include at least one uppercase letter, one lowercase letter, and one number";
    }
    
    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    
    // Validate age
    if ($age < 13 || $age > 120) {
        $errors[] = "Age must be between 13 and 120";
    }
    
    // Validate gender
    $valid_genders = ['male', 'female', 'other', 'prefer_not_to_say'];
    if (empty($gender) || !in_array($gender, $valid_genders)) {
        $errors[] = "Please select a valid gender";
    }
    
    // Validate phone (optional, but if provided should be valid)
    if (!empty($phone) && !preg_match('/^[0-9+\-() ]{7,15}$/', $phone)) {
        $errors[] = "Please enter a valid phone number";
    }
    
    // Check if username or email already exists
    $check_sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $username, $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $user = $check_result->fetch_assoc();
        if ($user['username'] === $username) {
            $errors[] = "Username already exists";
        }
        if ($user['email'] === $email) {
            $errors[] = "Email already registered";
        }
    }
    
    $check_stmt->close();
    
    // If there are errors, redirect back to signup with error messages
    if (!empty($errors)) {
        $_SESSION['signup_errors'] = $errors;
        $_SESSION['signup_data'] = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'username' => $username,
            'email' => $email,
            'age' => $age,
            'gender' => $gender,
            'phone' => $phone
        ];
        header("Location: signup.php");
        exit();
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user into database
    $insert_sql = "INSERT INTO users (first_name, last_name, username, password, email, age, gender, phone, created_at) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("sssssiss", $first_name, $last_name, $username, $hashed_password, $email, $age, $gender, $phone);
    
    if ($insert_stmt->execute()) {
        // Registration successful
        $_SESSION['signup_success'] = "Registration successful! You can now log in.";
        header("Location: login.php");
    } else {
        // Registration failed
        $_SESSION['signup_error'] = "Registration failed: " . $conn->error;
        header("Location: signup.php");
    }
    
    $insert_stmt->close();
}

// Close connection
$conn->close();
?>