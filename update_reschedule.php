<?php
include 'database.php';
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

$appointmentId = $_POST['appointmentId'];
$newDate = $_POST['newDate'];
$newTime = $_POST['newTime'];

if (!$appointmentId || !$newDate || !$newTime) {
    echo json_encode(["success" => false, "message" => "Incomplete data."]);
    exit;
}

// First get customer details before updating
$getCustomerSql = "SELECT Name, Email, Services, Staff_Assigned FROM appointment WHERE Appointment_ID = ?";
$stmt = $conn->prepare($getCustomerSql);
$stmt->bind_param("i", $appointmentId);
$stmt->execute();
$result = $stmt->get_result();
$customerData = $result->fetch_assoc();
$stmt->close();

if (!$customerData) {
    echo json_encode(["success" => false, "message" => "Appointment not found."]);
    exit;
}

$customerName = $customerData['Name'];
$customerEmail = $customerData['Email'];
$serviceName = $customerData['Services'];
$staffAssigned = $customerData['Staff_Assigned'];

// Update the appointment
$sql = "UPDATE appointment SET DATE = ?, TIME = ? WHERE Appointment_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $newDate, $newTime, $appointmentId);

if ($stmt->execute()) {
    // Format date and time for email
    $formattedDate = date("F j, Y", strtotime($newDate));
    $formattedTime = date("g:i A", strtotime($newTime));
    
    // Send email notification
    sendRescheduleEmail($customerName, $customerEmail, $serviceName, $formattedDate, $formattedTime, $appointmentId, $staffAssigned);
    
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Database error."]);
}

$stmt->close();
$conn->close();

function sendRescheduleEmail($name, $email, $service, $date, $time, $appointmentId, $staff) {
    require __DIR__ . "/./vendor/autoload.php";
    
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
$mail->Subject = 'Your Appointment Has Been Rescheduled (ID: '.$appointmentId.')';
$mail->Body = "
    <div style='font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px;'>
        <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); overflow: hidden;'>
            
            <!-- Header -->
            <div style='background-color: #C4A484; color: white; text-align: center; padding: 20px;'>
                <h1 style='margin: 0; font-size: 24px; font-weight: bold;'>Aniah Brow Aesthetics</h1>
            </div>
            
            <!-- Content -->
            <div style='padding: 30px; text-align: center;'>
                <h3 style='font-size: 20px; margin-bottom: 15px; color: #333;'>Dear $name,</h3>
                <p style='font-size: 16px; margin-bottom: 20px; color: #555;'>Your appointment has been successfully rescheduled. Here are your updated appointment details:</p>
                
                <div style='background-color: #f8f8f8; padding: 15px; border-left: 4px solid #3498db; margin-bottom: 15px;'>
                    <div style='margin-bottom: 8px;'>
                        <span style='font-weight: bold; display: inline-block; width: 120px;'>Appointment ID:</span>
                        <span>$appointmentId</span>
                    </div>
                    <div style='margin-bottom: 8px;'>
                        <span style='font-weight: bold; display: inline-block; width: 120px;'>Service:</span>
                        <span>$service</span>
                    </div>
                    <div style='margin-bottom: 8px;'>
                        <span style='font-weight: bold; display: inline-block; width: 120px;'>New Date:</span>
                        <span>$date</span>
                    </div>
                    <div style='margin-bottom: 8px;'>
                        <span style='font-weight: bold; display: inline-block; width: 120px;'>New Time:</span>
                        <span>$time</span>
                    </div>
                    <div style='margin-bottom: 8px;'>
                        <span style='font-weight: bold; display: inline-block; width: 120px;'>Staff Assigned:</span>
                        <span>$staff</span>
                    </div>
                </div>
                
                <p style='font-size: 16px; margin-top: 20px; color: #555;'>Please arrive 10 minutes before your scheduled time.</p>
                <p style='font-size: 16px; margin-top: 20px; color: #555;'>If you have any questions or need to make further changes, please contact us.</p>
            </div>

            <!-- Footer -->
            <div style='background-color: #f1f1f1; padding: 20px; text-align: center; color: #777; font-size: 14px;'>
                <p style='color: #777;'>Best regards,<br>Aniah Brow Aesthetics Team</p>
            </div>
        </div>
    </div>
";

        
        $mail->send();
        error_log("Reschedule email sent to $email for appointment ID $appointmentId");
    } catch (Exception $e) {
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}
?>