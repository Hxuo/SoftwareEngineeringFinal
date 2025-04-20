<?php
require 'database.php';
header("Content-Type: application/json");

$createemail = $_GET['createemail'] ?? '';

// Basic validation
if (empty($createemail) || !filter_var($createemail, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["exists" => false]);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM accounts WHERE Email = ?");
$stmt->bind_param("s", $createemail);
$stmt->execute();
$result = $stmt->get_result();

echo json_encode(["exists" => $result->num_rows > 0]);
?>