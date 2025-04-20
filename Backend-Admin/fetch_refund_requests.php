<?php
include 'database.php';
session_start();

header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin', 'Owner', 'SuperAdmin'])) {
    echo json_encode(["error" => "Unauthorized access"]);
    exit;
}

try {
    // Fetch all pending refund requests
    $sql = "SELECT * FROM refund_request WHERE Status = 'Pending'";
    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("Database error: " . $conn->error);
    }

    $requests = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }
    }

    echo json_encode([
        'success' => true,
        'data' => $requests,
        'count' => count($requests)
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    $conn->close();
}
?>