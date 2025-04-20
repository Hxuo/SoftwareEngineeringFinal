<?php
include 'database.php';

$data = json_decode(file_get_contents('php://input'), true);
$dates = $data['dates'];

// Clear existing closed dates
$conn->query("DELETE FROM StoreClosed");

// Insert new closed dates
if (!empty($dates)) {
    $stmt = $conn->prepare("INSERT INTO StoreClosed (DateClosed) VALUES (?)");
    foreach ($dates as $date) {
        $stmt->bind_param('s', $date);
        $stmt->execute();
    }
}

echo json_encode(['success' => true]);
?>