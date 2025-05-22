<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$conn = new mysqli("localhost", "root", "", "muscle_city_gym");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $target_dir = "../public/uploads/profile_pictures/";
    $file_name = basename($_FILES["profile_picture"]["name"]);
    $target_file = $target_dir . uniqid() . "_" . $file_name;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is actual image
    $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
    if ($check === false) {
        echo json_encode(['success' => false, 'message' => 'File is not an image']);
        exit();
    }

    // Allow certain file formats
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        echo json_encode(['success' => false, 'message' => 'Only JPG, JPEG, PNG & GIF files allowed']);
        exit();
    }

    // Move uploaded file
    if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
        // Save path in DB
        $conn->query("UPDATE users SET profile_picture = '$target_file' WHERE id = $user_id");
        echo json_encode(['success' => true, 'message' => 'Profile picture updated']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error uploading file']);
    }
}
?>