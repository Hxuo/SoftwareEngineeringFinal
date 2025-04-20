<?php
include 'database.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$dates = $data['dates'];

// Debugging: Log received data
error_log("Received dates to remove: " . print_r($dates, true));

try {
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    $conn->begin_transaction();

    foreach ($dates as $date) {
        $stmt = $conn->prepare("DELETE FROM storeclosed WHERE DateClosed = ?");
        $stmt->bind_param("s", $date);
        $stmt->execute();

        // Debugging: Log each date being deleted
        error_log("Removing date: " . $date);
    }

    $conn->commit();
    echo json_encode(["success" => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
} finally {
    $conn->close();
}
?>