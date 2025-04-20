<?php
include 'database.php';

header('Content-Type: application/json');

// Assuming $conn is your MySQLi connection from database.php
$query = "SELECT Password FROM managementpassword WHERE Password_ID = 1";
$stmt = $conn->prepare($query);

if ($stmt === false) {
    echo json_encode([
        'success' => false,
        'message' => 'Prepare failed: ' . $conn->error
    ]);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if ($data) {
    echo json_encode([
        'success' => true,
        'password' => $data['Password']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Password not found'
    ]);
}

$stmt->close();
?>