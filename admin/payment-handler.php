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
    die(json_encode(['error' => 'DB connection failed']));
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'read':
        $stmt = $pdo->query("SELECT * FROM payments ORDER BY id ASC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_NUMERIC_CHECK);
        break;

    case 'read_one':
        $id = intval($_GET['id']);
        $stmt = $pdo->prepare("SELECT * FROM payments WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC), JSON_NUMERIC_CHECK);
        break;

    case 'create':
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare("INSERT INTO payments (member, amount, date, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['member'], $data['amount'], $data['date'], $data['status']]);
        echo json_encode(['success' => true]);
        break;

    case 'update':
        $id = intval($_GET['id']);
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare("UPDATE payments SET member = ?, amount = ?, date = ?, status = ? WHERE id = ?");
        $stmt->execute([$data['member'], $data['amount'], $data['date'], $data['status'], $id]);
        echo json_encode(['success' => true]);
        break;

    case 'delete':
        $id = intval($_GET['id']);
        $stmt = $pdo->prepare("DELETE FROM payments WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
}
?>