<?php
include 'database.php';

header('Content-Type: application/json');

$year = isset($_GET['year']) ? (int)$_GET['year'] : date("Y");
$month = isset($_GET['month']) ? (int)$_GET['month'] : date("m");

try {
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Mapping weekdays to numbers
    $weekdaysMap = [
        "Sunday" => 0, "Monday" => 1, "Tuesday" => 2, "Wednesday" => 3,
        "Thursday" => 4, "Friday" => 5, "Saturday" => 6
    ];

    // Fetch disabled weekdays
    $disabledDays = [];
    $query = "SELECT Weekday FROM DisabledWeekdays";
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        if (isset($weekdaysMap[$row['Weekday']])) {
            $disabledDays[] = $weekdaysMap[$row['Weekday']];
        }
    }

    // Fetch closed dates
    $closedDates = [];
    $query = "SELECT DateClosed FROM storeclosed WHERE YEAR(DateClosed) = ? AND MONTH(DateClosed) = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $year, $month);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $closedDates[] = $row['DateClosed'];
    }

    echo json_encode([
        "disabledDays" => $disabledDays,
        "closedDates" => $closedDates
    ]);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
} finally {
    $conn->close();
}
?>