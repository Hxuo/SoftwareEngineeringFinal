<?php
include 'database.php'; // Siguraduhin na may connection ka sa database

$month = isset($_GET['month']) ? $_GET['month'] : date('m');
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Get closed dates
$closedDates = [];
$closedQuery = $conn->query("SELECT DateClosed FROM StoreClosed");
while ($row = $closedQuery->fetch_assoc()) {
    $closedDates[] = $row['DateClosed'];
}

// Get disabled weekdays
$disabledWeekdays = [];
$disabledQuery = $conn->query("SELECT Weekday FROM DisabledWeekdays");
while ($row = $disabledQuery->fetch_assoc()) {
    $disabledWeekdays[] = $row['Weekday'];
}

// Get bookings count per day
$bookings = [];
$bookingQuery = $conn->query("SELECT DATE, COUNT(*) as total FROM Appointment GROUP BY DATE");
while ($row = $bookingQuery->fetch_assoc()) {
    $bookings[$row['DATE']] = $row['total'];
}

// Adjust closed weekdays if previous week had 20+ bookings
$adjustedDisabledWeekdays = $disabledWeekdays;
$startDate = date("Y-m-d", strtotime("first day of $year-$month"));
$endDate = date("Y-m-d", strtotime("last day of $year-$month"));

$weekBookings = [];
$checkWeekQuery = $conn->query("SELECT DATE, COUNT(*) as total FROM Appointment WHERE DATE BETWEEN '$startDate' AND '$endDate' GROUP BY WEEK(DATE)");
while ($row = $checkWeekQuery->fetch_assoc()) {
    if ($row['total'] > 20) {
        // Open the weekday in the next week
        $weekDay = date('l', strtotime($row['DATE']));
        $adjustedDisabledWeekdays = array_diff($adjustedDisabledWeekdays, [$weekDay]);
    }
}

// Return JSON response
echo json_encode([
    'closedDates' => $closedDates,
    'disabledWeekdays' => $adjustedDisabledWeekdays,
    'bookings' => $bookings
]);
?>
