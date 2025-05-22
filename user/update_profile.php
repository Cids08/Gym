<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

$conn = new mysqli("localhost", "root", "", "muscle_city_gym");
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// Sanitize input
$first_name = $conn->real_escape_string(trim($_POST['first_name']));
$last_name = $conn->real_escape_string(trim($_POST['last_name']));
$username = $conn->real_escape_string(trim($_POST['username']));
$email = $conn->real_escape_string(trim($_POST['email']));
$age = isset($_POST['age']) ? intval($_POST['age']) : null;
$gender = in_array($_POST['gender'], ['male', 'female', 'other', 'prefer_not_to_say']) ? $_POST['gender'] : null;
$phone = $conn->real_escape_string(trim($_POST['phone']));

// Check if username or email is already taken by someone else
$stmt = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
$stmt->bind_param("ssi", $username, $email, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Username or Email already taken']);
    exit();
}

// Update query
$stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, username = ?, email = ?, age = ?, gender = ?, phone = ? WHERE id = ?");
$stmt->bind_param("ssssisssi", $first_name, $last_name, $username, $email, $age, $gender, $phone, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating profile']);
}
?>