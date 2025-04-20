<?php
include 'database.php';

$data = json_decode(file_get_contents('php://input'), true);

$appointmentId = $data['Appointment_ID'];
$name = $data['Name'];
$date = $data['DATE'];
$time = $data['TIME'];
$staffAssigned = $data['Staff_Assigned'];
$services = $data['Services'];
$price = $data['Price'];
$email = $data['Email'];
$phoneNumber = $data['PhoneNumber'];
$status = "Completed";
$paymentMethod = $data['PaymentMethod'];

// Insert into appointment_history
$sql = "INSERT INTO appointment_history (Appointment_ID, Name, DATE, TIME, Staff_Assigned, Services, Price, Email, PhoneNumber, Status, PaymentMethod) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isssssdssss", $appointmentId, $name, $date, $time, $staffAssigned, $services, $price, $email, $phoneNumber, $status, $paymentMethod);

if ($stmt->execute()) {
    // Delete from appointment
    $sql = "DELETE FROM appointment WHERE Appointment_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appointmentId);
    $stmt->execute();

    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Failed to move appointment to history"]);
}

$stmt->close();
$conn->close();
?>