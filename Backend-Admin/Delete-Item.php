<?php
include 'database.php';

$id = $_GET['id'];

$sql = "DELETE FROM items WHERE Items_ID=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

echo json_encode(["success" => true]);
?>