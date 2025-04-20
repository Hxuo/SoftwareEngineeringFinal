<?php
require 'database.php';
header("Content-Type: application/json");

$username = $_GET['username'] ?? '';

$stmt = $conn->prepare("SELECT * FROM accounts WHERE Username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

echo json_encode(["exists" => $result->num_rows > 0]);
?>
