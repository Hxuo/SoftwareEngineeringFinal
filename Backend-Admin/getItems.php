<?php
// getItemById.php
include 'database.php';

$itemId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($itemId) {
    $sql = "SELECT * FROM items WHERE Items_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $itemId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode($row); // Return the item data as JSON
    } else {
        echo json_encode(null); // Return null if the item is not found
    }
} else {
    echo json_encode(null); // Return null if the ID is invalid or not set
}

?>