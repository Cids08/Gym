<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");

$host = "localhost";
$dbname = "muscle_city_gym"; 
$user = "root";          
$pass = "";      

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'read':
        $stmt = $pdo->query("SELECT * FROM membership_plans ORDER BY id ASC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_NUMERIC_CHECK);
        break;

    case 'read_one':
        $id = intval($_GET['id']);
        $stmt = $pdo->prepare("SELECT * FROM membership_plans WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC), JSON_NUMERIC_CHECK);
        break;

    case 'create':
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare("INSERT INTO membership_plans (name, duration, price) VALUES (?, ?, ?)");
        $stmt->execute([$data['name'], $data['duration'], $data['price']]);
        echo json_encode(['success' => true]);
        break;

    case 'update':
        $id = intval($_GET['id']);
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare("UPDATE membership_plans SET name = ?, duration = ?, price = ? WHERE id = ?");
        $stmt->execute([$data['name'], $data['duration'], $data['price'], $id]);
        echo json_encode(['success' => true]);
        break;

    case 'delete':
        $id = intval($_GET['id']);
        $stmt = $pdo->prepare("DELETE FROM membership_plans WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
}
?>