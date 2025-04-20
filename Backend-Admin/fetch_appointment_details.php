<?php
include 'database.php';

$appointmentId = $_GET['appointment_id'];

$sql = "SELECT * FROM appointment WHERE Appointment_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $appointmentId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $appointment = $result->fetch_assoc();
    echo json_encode($appointment);
} else {
    echo json_encode(["error" => "Appointment not found"]);
}

$stmt->close();
$conn->close();
?>