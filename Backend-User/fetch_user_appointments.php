<?php
include 'database.php';

date_default_timezone_set('Asia/Manila');

// Start the session to access session variables
session_start();

// Get the logged-in user's email from the session
$userEmail = isset($_SESSION['email']) ? $_SESSION['email'] : '';

if (empty($userEmail)) {
    die(json_encode(["error" => "Email is required"])); // Return an error if no email in session
}

// Prepare the SQL query to fetch scheduled appointments for the logged-in user
$sql = "SELECT Appointment_ID, DATE, TIME, Services, Staff_Assigned, Price FROM appointment WHERE email = ? AND DATE(DATE) >= CURDATE()";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode(["error" => "SQL Error: " . $conn->error])); // Handle SQL errors
}

$stmt->bind_param("s", $userEmail); // Use the email from the session
$stmt->execute();
$result = $stmt->get_result();

$appointments = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row; // Add each scheduled appointment to the array
    }
    echo json_encode($appointments); // Return the scheduled appointments as a JSON response
} else {
    echo json_encode(["error" => "No scheduled appointments for today"]); // Return error if no appointments are found
}

$stmt->close();
$conn->close();
?>
