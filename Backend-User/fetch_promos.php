<?php
header('Content-Type: application/json');
include 'database.php';

$sql = "SELECT Items_Name, Items_Price, Items_Image FROM Items WHERE Items_Type = 'Promo'";
$result = $conn->query($sql);

$services = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
}

echo json_encode($services);
$conn->close();
