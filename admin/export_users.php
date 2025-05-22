<?php
session_start();
$conn = new mysqli("localhost", "root", "", "muscle_city_gym");

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="members.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Name', 'Email', 'Membership Type', 'Status', 'Last Login']);

$sql = "SELECT * FROM users WHERE role = 'user'";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['id'],
        $row['first_name'] . ' ' . $row['last_name'],
        $row['email'],
        ucfirst($row['package']),
        ucfirst($row['status']),
        $row['last_login']
    ]);
}

fclose($output);
?>