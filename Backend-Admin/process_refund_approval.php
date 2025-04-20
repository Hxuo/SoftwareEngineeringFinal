<?php
include 'database.php';
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if user is admin
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin', 'Owner', 'SuperAdmin'])) {
    die(json_encode(["success" => false, "message" => "Unauthorized access"]));
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);
$appointmentId = $input['appointmentId'] ?? null;
$action = $input['action'] ?? '';
$reason = $input['reason'] ?? '';

if (!$appointmentId || !in_array($action, ['approve', 'reject'])) {
    die(json_encode(["success" => false, "message" => "Invalid request"]));
}

// Begin transaction
$conn->begin_transaction();

try {
    // First, get customer details from refund_request
    $getCustomerSql = "SELECT Name, Email FROM refund_request WHERE Appointment_ID = ?";
    $stmt = $conn->prepare($getCustomerSql);
    $stmt->bind_param("i", $appointmentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $customerData = $result->fetch_assoc();
    $stmt->close();
    
    if (!$customerData) {
        throw new Exception("Customer data not found for this appointment");
    }
    
    $customerName = $customerData['Name'];
    $customerEmail = $customerData['Email'];

    // 1. Update appointment history
    $newStatus = ($action === 'approve') ? 'Refunded' : 'Refund rejected';
    $updateHistorySql = "UPDATE appointment_history SET Status = ? WHERE Appointment_ID = ?";
    $stmt = $conn->prepare($updateHistorySql);
    $stmt->bind_param("si", $newStatus, $appointmentId);
    $stmt->execute();
    $stmt->close();
    
    // If rejecting, add the reason
    if ($action === 'reject') {
        $updateRefundReplySql = "UPDATE appointment_history SET Refund_Reply = ? WHERE Appointment_ID = ?";
        $stmt = $conn->prepare($updateRefundReplySql);
        $stmt->bind_param("si", $reason, $appointmentId);
        $stmt->execute();
        $stmt->close();
    }
    
    // 2. Delete from refund_request table
    $deleteSql = "DELETE FROM refund_request WHERE Appointment_ID = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $appointmentId);
    $stmt->execute();
    $stmt->close();
    
    // Commit transaction
    $conn->commit();
    
    // Send email notification if approved
    if ($action === 'approve') {
        sendRefundApprovalEmail($customerName, $customerEmail);
    } elseif ($action === 'reject') {
        sendRefundRejectionEmail($customerName, $customerEmail, $reason);
    }
    
    echo json_encode(["success" => true, "message" => "Refund request processed successfully"]);
    
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

$conn->close();

function sendRefundApprovalEmail($name, $email) {
    require __DIR__ . "/../vendor/autoload.php";
    
    $mail = new PHPMailer(true);
    
    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'softwareengineeringfinal@gmail.com';
        $mail->Password = 'pgvy bati jffn pbty';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        //Recipients
        $mail->setFrom('softwareengineeringfinal@gmail.com', 'Aniah Brow Aesthetics');
        $mail->addAddress($email, $name);
        
        //Content
        $mail->isHTML(true);
        $mail->isHTML(true);
        $mail->Subject = 'Your Refund Request Has Been Approved';
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px; width: 100%;'>
                <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); overflow: hidden; width: 100%;'>
                    
                    <!-- Header -->
                    <div style='background-color: #C4A484; color: white; text-align: center; padding: 20px;'>
                        <h1 style='margin: 0; font-size: 24px; font-weight: bold;'>Aniah Brow Aesthetics</h1>
                    </div>
                    
                    <!-- Content -->
                    <div style='padding: 30px; text-align: center;'>
                        <h3 style='font-size: 20px; margin-bottom: 15px; color: #333;'>Dear $name,</h3>
                        <p style='font-size: 16px; margin-bottom: 20px; color: #555;'>Your request for refunding your appointment has been <strong>approved</strong>.</p>
                        <p style='font-size: 16px; margin-bottom: 20px; color: #555;'>You'll receive this payment in your bank account within 1-3 Business Days.</p>
                        <p style='font-size: 16px; margin-bottom: 20px; color: #555;'>Feel free to rebook anytime!</p>
                    </div>
            
                    <!-- Footer -->
                    <div style='background-color: #f1f1f1; padding: 20px; text-align: center; color: #777; font-size: 14px;'>
                        <p style='color: #777;'>Best regards,<br>Aniah Brow Aesthetics Team</p>
                    </div>
                </div>
            </div>
        ";
        
        
        
        
        $mail->send();
    } catch (Exception $e) {
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}

function sendRefundRejectionEmail($name, $email, $reason) {
    require __DIR__ . "/../vendor/autoload.php";
    
    $mail = new PHPMailer(true);
    
    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'softwareengineeringfinal@gmail.com';
        $mail->Password = 'pgvy bati jffn pbty';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        //Recipients
        $mail->setFrom('softwareengineeringfinal@gmail.com', 'Aniah Brow Aesthetics');
        $mail->addAddress($email, $name);
        
        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Your Refund Request Has Been Rejected';
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px; width: 100%;'>
                <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); overflow: hidden; width: 100%;'>
                    
                    <!-- Header -->
                    <div style='background-color: #C4A484; color: white; text-align: center; padding: 20px;'>
                        <h1 style='margin: 0; font-size: 24px; font-weight: bold;'>Aniah Brow Aesthetics</h1>
                    </div>
                    
                    <!-- Content -->
                    <div style='padding: 30px; text-align: center;'>
                        <h3 style='font-size: 20px; margin-bottom: 15px; color: #333;'>Dear $name,</h3>
                        <p style='font-size: 16px; margin-bottom: 20px; color: #555;'>We regret to inform you that your refund request has been <strong>rejected</strong>.</p>
                        
                        <div style='background-color: #f8f8f8; padding: 15px; border-left: 4px solid #e74c3c; margin-bottom: 15px;'>
                            <p style='font-size: 16px; color: #555;'><strong>Reason:</strong></p>
                            <p style='font-size: 16px; color: #555;'>$reason</p>
                        </div>
                        
                        <p style='font-size: 16px; margin-top: 20px; color: #555;'>If you have any questions or concerns, please contact our support team.</p>
                    </div>
            
                    <!-- Footer -->
                    <div style='background-color: #f1f1f1; padding: 20px; text-align: center; color: #777; font-size: 14px;'>
                        <p style='color: #777;'>Best regards,<br>Aniah Brow Aesthetics Team</p>
                    </div>
                </div>
            </div>
        ";
        
        
        
        $mail->send();
    } catch (Exception $e) {
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}
?>