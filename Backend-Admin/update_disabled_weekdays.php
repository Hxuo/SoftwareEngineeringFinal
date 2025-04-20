<?php
include 'database.php'; // Include your database connection file

header('Content-Type: application/json'); // Set response header to JSON

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);
$weekdays = $data['weekdays']; // Extract the weekdays from the request

try {
    // Create a new database connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check for connection errors
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Start a transaction to ensure data consistency
    $conn->begin_transaction();

    // Clear all existing disabled weekdays from the table
    $clearQuery = "DELETE FROM DisabledWeekdays";
    if (!$conn->query($clearQuery)) {
        throw new Exception("Error clearing disabled weekdays: " . $conn->error);
    }

    // Insert the new disabled weekdays into the table
    foreach ($weekdays as $weekday) {
        $stmt = $conn->prepare("INSERT INTO DisabledWeekdays (Weekday) VALUES (?)");
        $stmt->bind_param("s", $weekday);
        if (!$stmt->execute()) {
            throw new Exception("Error inserting disabled weekday: " . $stmt->error);
        }
    }

    // Commit the transaction
    $conn->commit();

    // Return a success response
    echo json_encode(["success" => true]);
} catch (Exception $e) {
    // Rollback the transaction in case of errors
    if (isset($conn)) {
        $conn->rollback();
    }

    // Return an error message
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
} finally {
    // Close the database connection
    if (isset($conn)) {
        $conn->close();
    }
}
?>