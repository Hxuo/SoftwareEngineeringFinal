<?php
include 'database.php';

$limit = 5; // Items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';

// Normalize category filter values
$categoryMap = [
    'services' => 'Service',
    'promo' => 'Promo',
    'package' => 'Package'
];

$categoryFilter = strtolower($categoryFilter);
$categoryFilter = isset($categoryMap[$categoryFilter]) ? $categoryMap[$categoryFilter] : '';

// Base query (always filter by search terms)
$sql = "SELECT * FROM items WHERE (Items_Name LIKE ? OR Items_Category LIKE ?)";

// Apply category filter only if not "All"
if (!empty($categoryFilter)) {
    $sql .= " AND Items_Type = ?";
}

$sql .= " LIMIT ?, ?";
$stmt = $conn->prepare($sql);

// Bind parameters dynamically
$searchParam = "%$search%";

if (!empty($categoryFilter)) {
    $stmt->bind_param("ssssi", $searchParam, $searchParam, $categoryFilter, $offset, $limit);
} else {
    $stmt->bind_param("ssii", $searchParam, $searchParam, $offset, $limit);
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Get total count for pagination
$countQuery = "SELECT COUNT(*) as total FROM items WHERE (Items_Name LIKE ? OR Items_Category LIKE ?)";
if (!empty($categoryFilter)) {
    $countQuery .= " AND Items_Type = ?";
}

$countStmt = $conn->prepare($countQuery);

if (!empty($categoryFilter)) {
    $countStmt->bind_param("sss", $searchParam, $searchParam, $categoryFilter);
} else {
    $countStmt->bind_param("ss", $searchParam, $searchParam);
}

$countStmt->execute();
$countResult = $countStmt->get_result();
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

echo json_encode(["data" => $data, "totalPages" => $totalPages]);
?>
