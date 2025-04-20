<?php
include 'database.php';

header('Content-Type: application/json');

try {
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    $query = "SELECT DateClosed FROM storeclosed";
    $result = $conn->query($query);
    $dates = [];

    while ($row = $result->fetch_assoc()) {
        $dates[] = $row['DateClosed'];
    }

    echo json_encode(["dates" => $dates]);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
} finally {
    $conn->close();
}
?>