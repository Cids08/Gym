<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$conn = new mysqli("localhost", "root", "", "muscle_city_gym");

if ($_GET['action'] === 'update') {
    $id = intval($_POST['id']);
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $package = $conn->real_escape_string($_POST['package']);
    $status = $conn->real_escape_string($_POST['status']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : '';

    if ($password) {
        $sql = "UPDATE users SET first_name='$first_name', last_name='$last_name',
                username='$username', email='$email', package='$package',
                status='$status', password='$password' WHERE id=$id";
    } else {
        $sql = "UPDATE users SET first_name='$first_name', last_name='$last_name',
                username='$username', email='$email', package='$package',
                status='$status' WHERE id=$id";
    }

    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'User updated']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Update failed']);
    }
}
?>