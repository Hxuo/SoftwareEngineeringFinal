<?php
include 'database.php';

// Get the logged-in user's email from the session
session_start();
$userEmail = isset($_SESSION['email']) ? $_SESSION['email'] : '';

if (empty($userEmail)) {
    die(json_encode(["error" => "Email is required"])); // If no email in session, return an error
}

// Prepare the SQL query to fetch appointment history for the logged-in user
$sql = "SELECT * FROM appointment_history WHERE email = ? ORDER BY DATE DESC";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode(["error" => "SQL Error: " . $conn->error])); // If there's an SQL error, return it
}

$stmt->bind_param("s", $userEmail); // Use the email from the session
$stmt->execute();
$result = $stmt->get_result();

$appointments = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row; // Add each row of appointments to the array
    }
    echo json_encode($appointments); // Return appointments as a JSON response
} else {
    echo json_encode(["error" => "No appointments found for this user"]); // Return an error if no appointments are found
}

$stmt->close();
$conn->close();
?>
