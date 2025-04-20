<?php
include 'database.php'; // Include your database connection file

header('Content-Type: application/json'); // Set response header to JSON

try {
    // Create a new database connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check for connection errors
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Fetch all disabled weekdays from the DisabledWeekdays table
    $query = "SELECT Weekday FROM DisabledWeekdays";
    $result = $conn->query($query);

    // Check if the query was successful
    if (!$result) {
        throw new Exception("Error fetching disabled weekdays: " . $conn->error);
    }

    // Store the weekdays in an array
    $weekdays = [];
    while ($row = $result->fetch_assoc()) {
        $weekdays[] = $row['Weekday'];
    }

    // Return the weekdays as a JSON response
    echo json_encode(["weekdays" => $weekdays]);
} catch (Exception $e) {
    // Handle errors and return an error message
    echo json_encode(["error" => $e->getMessage()]);
} finally {
    // Close the database connection
    if (isset($conn)) {
        $conn->close();
    }
}
?>