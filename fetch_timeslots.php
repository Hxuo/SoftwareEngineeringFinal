<?php
session_start();
include 'database.php';

date_default_timezone_set('Asia/Manila');

// Ensure the date parameter is provided
if (!isset($_GET['date'])) {
    die(json_encode(["error" => "Date parameter is missing."]));
}

$date = $_GET['date'];
$currentTime = date('H:i:s'); // Get the current time in 24-hour format

// Fetch the number of available staff
$staffCountQuery = "SELECT COUNT(*) as staffCount FROM Staff WHERE Status = 'On-Duty'";
$staffCountResult = $conn->query($staffCountQuery);

if (!$staffCountResult) {
    die(json_encode(["error" => "Error fetching staff count: " . $conn->error]));
}

$staffCount = $staffCountResult->fetch_assoc()['staffCount'];

// Fetch the number of appointments for each time slot on the given date
$appointmentCountQuery = "SELECT TIME, COUNT(*) as appointmentCount FROM Appointment WHERE DATE = ? GROUP BY TIME";
$stmt = $conn->prepare($appointmentCountQuery);

if (!$stmt) {
    die(json_encode(["error" => "Error preparing appointment count query: " . $conn->error]));
}

$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

$appointmentCounts = [];
while ($row = $result->fetch_assoc()) {
    $appointmentCounts[$row['TIME']] = $row['appointmentCount'];
}

$stmt->close();

// Define the time slots
$timeSlots = [];
$startTime = strtotime('13:00:00'); // 1:00 PM
$endTime = strtotime('21:00:00');  // 10:00 PM

while ($startTime <= $endTime) {
    $time24hr = date('H:i:s', $startTime); // 24-hour format for database
    $time12hr = date('h:i A', $startTime); // 12-hour format for display
    $timeSlots[] = [
        'display' => $time12hr, // For dropdown display
        'value' => $time24hr   // For database storage
    ];
    $startTime += 3600; // Add 1 hour
}

// Filter out time slots that are fully booked or in the past
$availableSlots = [];
foreach ($timeSlots as $slot) {
    $appointmentCount = $appointmentCounts[$slot['value']] ?? 0;

    // Check if the slot is available
    if ($appointmentCount < $staffCount && ($date > date('Y-m-d') || ($date == date('Y-m-d') && $slot['value'] >= $currentTime))) {
        $availableSlots[] = $slot;
    }
}

// Debugging: Log the available slots
error_log("Available Slots: " . print_r($availableSlots, true));

// Return the available slots as JSON
header('Content-Type: application/json');
echo json_encode($availableSlots);
?>