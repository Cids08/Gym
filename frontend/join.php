<?php
session_start();

// Check login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../frontend/login.php");
    exit();
}

// Database config
$host = 'localhost';
$db = 'muscle_city_gym';
$user = 'root';
$pass = '';

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Get form data
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$age = $_POST['age'];
$gender = $_POST['gender'];
$email = $_POST['email'];
$package = $_POST['package'];
$payment_method = $_POST['payment_method'];
$phone_number = $_POST['phone_number'];
$pin_code = $_POST['pin_code'];

// Optional: Prevent multiple subscriptions
$check_sql = "SELECT * FROM memberships WHERE user_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $user_id);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    echo "<script>
        alert('You are already subscribed.');
        window.location.href = '../user/UserDashboard.php';
        </script>";
    exit();
}
$check_stmt->close();

// Insert into memberships table with user_id
$stmt = $conn->prepare("INSERT INTO memberships (user_id, first_name, last_name, age, gender, email, package_name, payment_method, phone_number, pin_code, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')");
$stmt->bind_param("ississssss", $user_id, $first_name, $last_name, $age, $gender, $email, $package, $payment_method, $phone_number, $pin_code);

if ($stmt->execute()) {
    echo "<script>
        alert('Membership successfully submitted!');
        window.location.href = '../user/UserDashboard.php';
    </script>";
} else {
    echo "<script>
        alert('Error: " . addslashes($stmt->error) . "');
        window.history.back();
    </script>";
}

$stmt->close();
$conn->close();
?>