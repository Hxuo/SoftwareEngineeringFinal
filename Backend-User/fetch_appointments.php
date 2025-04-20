<?php
// fetch_timeslots.php
require 'database.php'; // your DB connection file

$date = $_GET['date'] ?? '';
$duration = intval($_GET['duration'] ?? 1); // 1 hour or 2 hours

$storeOpen = strtotime("13:00"); // 1:00 PM
$storeClose = strtotime("21:00"); // 10:00 PM

// Fetch booked times
$sql = "SELECT TIME FROM Appointment WHERE DATE = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

$bookedTimes = [];
while ($row = $result->fetch_assoc()) {
    $bookedTimes[] = $row['TIME'];
}

// Generate all possible slots
$availableSlots = [];
$currentTime = $storeOpen;

while ($currentTime + ($duration * 3600) <= $storeClose) {
    $endTime = $currentTime + ($duration * 3600);
    $slotStart = date("h:i A", $currentTime);
    $slotEnd = date("h:i A", $endTime);

    $slot = "$slotStart - $slotEnd";

    // Check if this slot overlaps with any booked time
    $conflict = false;

    foreach ($bookedTimes as $bookedTime) {
        $bookedStart = strtotime($bookedTime);
        $bookedEnd = $bookedStart + 3600; // Assume each appointment is 1 hour in DB

        if (
            ($currentTime >= $bookedStart && $currentTime < $bookedEnd) || 
            ($endTime > $bookedStart && $endTime <= $bookedEnd) ||
            ($currentTime <= $bookedStart && $endTime >= $bookedEnd)
        ) {
            $conflict = true;
            break;
        }
    }

    if (!$conflict) {
        $availableSlots[] = $slot;
    }

    $currentTime += ($duration * 3600); // Move to next slot
}

header('Content-Type: application/json');
echo json_encode($availableSlots);
