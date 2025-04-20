<?php
include 'database.php'; // Include database connection

$data = json_decode(file_get_contents("php://input"), true);
$appointmentID = $data['Appointment_ID'];

if ($appointmentID) {
    // Fetch appointment details
    $query = "SELECT * FROM appointment WHERE Appointment_ID = '$appointmentID'";
    $result = mysqli_query($conn, $query);
    $appointment = mysqli_fetch_assoc($result);

    if ($appointment) {
        // Insert into appointment_history
        $insertQuery = "INSERT INTO appointment_history (Appointment_ID, Name, DATE, TIME, Staff_Assigned, Services, Price, Email, PhoneNumber, Status, PaymentMethod) 
                        VALUES ('{$appointment['Appointment_ID']}', '{$appointment['Name']}', '{$appointment['DATE']}', '{$appointment['TIME']}', '{$appointment['Staff_Assigned']}', 
                                '{$appointment['Services']}', '{$appointment['Price']}', '{$appointment['Email']}', '{$appointment['PhoneNumber']}', 'Completed', '{$appointment['PaymentMethod']}')";
        mysqli_query($conn, $insertQuery);

        // Delete from appointment table
        $deleteQuery = "DELETE FROM appointment WHERE Appointment_ID = '$appointmentID'";
        mysqli_query($conn, $deleteQuery);

        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Appointment not found"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Invalid Appointment ID"]);
}
?>
