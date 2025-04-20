<?php
include 'database.php';

date_default_timezone_set('Asia/Manila');

// Get parameters
$selectedDate = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 5; // Number of items per page
$offset = ($page - 1) * $perPage;

// First query to get total count
$countSql = "SELECT COUNT(*) as total FROM appointment WHERE DATE(DATE) = ?";
$countStmt = $conn->prepare($countSql);
$countStmt->bind_param("s", $selectedDate);
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $perPage);

// Second query to get paginated data
$sql = "SELECT Appointment_ID, TIME, Name, Staff_Assigned, Services, PhoneNumber 
        FROM appointment 
        WHERE DATE(DATE) = ? 
        ORDER BY DATE DESC, TIME ASC
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die(json_encode(["error" => "SQL Error: " . $conn->error]));
}

$stmt->bind_param("sii", $selectedDate, $perPage, $offset);
$stmt->execute();
$result = $stmt->get_result();

$totalPages = max(1, ceil($totalRows / $perPage));

$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}

// Ensure at least one page is shown even if there are no appointments
$totalPages = max(1, ceil($totalRows / $perPage));

echo json_encode([
    'data' => $appointments,
    'pagination' => [
        'total' => $totalRows,
        'per_page' => $perPage,
        'current_page' => $page,
        'total_pages' => $totalPages
    ]
]);


$stmt->close();
$countStmt->close();
$conn->close();
?>