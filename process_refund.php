<?php
include 'database.php';

// Start the session to access session variables
session_start();

// Get the logged-in user's email from the session
$userEmail = isset($_SESSION['email']) ? $_SESSION['email'] : '';

if (empty($userEmail)) {
    die(json_encode(["success" => false, "message" => "User not logged in"]));
}

// Get the input data
$input = json_decode(file_get_contents('php://input'), true);
$appointmentId = $input['appointmentId'] ?? null;
$reason = $input['reason'] ?? '';
$refundDate = $input['refundDate'] ?? '';
$refundTime = $input['refundTime'] ?? '';

if (!$appointmentId) {
    die(json_encode(["success" => false, "message" => "Appointment ID is required"]));
}

// Begin transaction
$conn->begin_transaction();

try {
    // 1. Get the appointment details
    $getAppointmentSql = "SELECT * FROM appointment WHERE Appointment_ID = ? AND email = ?";
    $stmt = $conn->prepare($getAppointmentSql);
    $stmt->bind_param("is", $appointmentId, $userEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Appointment not found or not owned by user");
    }
    
    $appointment = $result->fetch_assoc();
    $stmt->close();
    
    // Prepare variables for binding
    $statusPending = 'Pending';
    $statusPendingRefund = 'Pending for refund';
    
    // 2. Insert into refund_request table
    $insertRefundSql = "INSERT INTO refund_request (
        Appointment_ID, Name, DATE, TIME, Staff_Assigned, Services, Price, 
        Email, PhoneNumber, Status, PaymentMethod, Refund_Date, Refund_Time, Refund_Reason
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($insertRefundSql);
    $stmt->bind_param(
        "isssssisssssss", 
        $appointment['Appointment_ID'],
        $appointment['Name'],
        $appointment['DATE'],
        $appointment['TIME'],
        $appointment['Staff_Assigned'],
        $appointment['Services'],
        $appointment['Price'],
        $appointment['Email'],
        $appointment['PhoneNumber'],
        $statusPending, // Status
        $appointment['PaymentMethod'],
        $refundDate,
        $refundTime,
        $reason
    );
    $stmt->execute();
    $stmt->close();
    
    // 3. Insert into appointment_history table
    $insertHistorySql = "INSERT INTO appointment_history (
        Appointment_ID, Name, DATE, TIME, Staff_Assigned, Services, Price, 
        Email, PhoneNumber, Status, PaymentMethod
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($insertHistorySql);
    $stmt->bind_param(
        "isssssissss", 
        $appointment['Appointment_ID'],
        $appointment['Name'],
        $appointment['DATE'],
        $appointment['TIME'],
        $appointment['Staff_Assigned'],
        $appointment['Services'],
        $appointment['Price'],
        $appointment['Email'],
        $appointment['PhoneNumber'],
        $statusPendingRefund, // Status
        $appointment['PaymentMethod']
    );
    $stmt->execute();
    $stmt->close();
    
    // 4. Delete from appointment table
    $deleteSql = "DELETE FROM appointment WHERE Appointment_ID = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $appointmentId);
    $stmt->execute();
    $stmt->close();
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode(["success" => true, "message" => "Refund request processed successfully"]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

$conn->close();
?>