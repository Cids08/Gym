<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "muscle_city_gym");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set admin credentials
$first_name = "Kester";
$last_name = "Pogi";
$username = "KesterPogi";
$email = "kesterpogi@example.com";
$password = "Admin123";
$hash = password_hash($password, PASSWORD_DEFAULT);
$role = "admin";
$package = "premium";
$status = "active";

// Insert admin
$stmt = $conn->prepare("
    INSERT INTO users (
        first_name, last_name, username, email, password, role, package, status, created_at, updated_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
");

// Only pass variables to bind_param
$stmt->bind_param(
    "ssssssss",
    $first_name,
    $last_name,
    $username,
    $email,
    $hash,
    $role,
    $package,
    $status
);

if ($stmt->execute()) {
    echo "Admin user created successfully!";
} else {
    echo "Error creating user: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>