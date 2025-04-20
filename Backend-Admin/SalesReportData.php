<?php
include 'database.php';

date_default_timezone_set('Asia/Manila');

// Function to get sales data for all years
function getSalesData() {
    global $conn;
    
    $data = array();
    $months = array(
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    );
    
    // Get all years with data
    $sql = "SELECT DISTINCT YEAR(date) as year FROM appointment_history ORDER BY year";
    $result = $conn->query($sql);
    $years = array();
    while ($row = $result->fetch_assoc()) {
        $years[] = $row['year'];
    }
    
    foreach ($years as $year) {
        foreach ($months as $month) {
            $monthNum = date('m', strtotime($month));
            $startDate = "$year-$monthNum-01";
            $endDate = "$year-$monthNum-" . date('t', strtotime($startDate));
            
            $sql = "SELECT SUM(Price) as total FROM appointment_history 
                    WHERE status = 'completed' AND date BETWEEN '$startDate' AND '$endDate'";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            
            $data[$year][$month] = $row['total'] ? $row['total'] : 0;
        }
    }
    
    return $data;
}

// Get data for all years
$salesData = getSalesData();
$availableYears = array_keys($salesData);

// Today's sales
$today = date('Y-m-d');
$sql = "SELECT SUM(Price) as total FROM appointment_history 
        WHERE LOWER(status) = 'completed' 
        AND DATE(date) = DATE('$today')";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$todaySales = $row['total'] ?? 0;

// Get appointment stats (same as before)
$sql = "SELECT 
        ((SELECT COUNT(*) FROM appointment) + 
        (SELECT COUNT(*) FROM appointment_history WHERE appointment_id NOT IN (SELECT appointment_id FROM appointment))) as total_booked,
        (SELECT COUNT(*) FROM appointment) as current_booked,
        (SELECT COUNT(*) FROM appointment_history WHERE LOWER(status) = 'completed') as total_completed,
        (SELECT COUNT(*) FROM appointment_history WHERE LOWER(status) = 'cancelled') as total_canceled,
        (SELECT COUNT(*) FROM appointment_history WHERE LOWER(status) LIKE '%refun%') as total_refunded";
$result = $conn->query($sql);
$stats = $result->fetch_assoc();

header('Content-Type: application/json');
echo json_encode([
    'salesData' => $salesData,
    'availableYears' => $availableYears,
    'todaySales' => $todaySales,
    'stats' => $stats
]);
?>