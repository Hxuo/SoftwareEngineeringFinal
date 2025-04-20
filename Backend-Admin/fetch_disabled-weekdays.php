<?php
include 'database.php';

$disabledWeekdays = [];
$disabledQuery = $conn->query("SELECT Weekday FROM DisabledWeekdays");
while ($row = $disabledQuery->fetch_assoc()) {
    $disabledWeekdays[] = $row['Weekday'];
}

echo json_encode(['disabledWeekdays' => $disabledWeekdays]);
?>