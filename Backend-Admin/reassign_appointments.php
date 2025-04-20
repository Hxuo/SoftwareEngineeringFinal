<?php
// reassign_appointments.php
include 'database.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__ . "/../vendor/autoload.php";

function sendRescheduleEmail($appointment, $newStaff, $newTime) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'softwareengineeringfinal@gmail.com';
        $mail->Password = 'pgvy bati jffn pbty'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        $mail->setFrom('softwareengineeringfinal@gmail.com', 'Aniah Brow Aesthetics');
        $mail->addAddress($appointment['Email']);
        $mail->isHTML(true);
        
        $mail->Subject = 'Appointment Reschedule Notification';
        $mail->Body = "
            <p>Dear {$appointment['Name']},</p>
            
            <p>Due to unexpected circumstances, the staff assigned on your appointment (Appointment ID: {$appointment['Appointment_ID']}), 
            Mr/Mrs {$appointment['Staff_Assigned']}, won't be able to accommodate you. Because of this, your appointment will be moved 
            to {$newTime} with Mr/Mrs. {$newStaff}.</p>
            
            <p>If you can't attend at the new time, you can reschedule your appointment at your desired date and time. 
            We are sorry for the inconvenience.</p>
            
            <p><a href='#'>Click here for rescheduling</a></p>
            
            <p>Best regards,<br>Aniah Brow Aesthetics</p>
        ";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

function findAvailableStaff($conn, $originalDate, $originalTime, $excludeStaffId) {
    // Convert original date and time to datetime format
    $originalDateTime = $originalDate . ' ' . $originalTime;
    
    // Find all staff who are on-duty (excluding the one going on leave)
    $onDutyStaff = $conn->query("
        SELECT Staff_ID, Staff_Name 
        FROM Staff 
        WHERE Status = 'On-Duty' AND Staff_ID != $excludeStaffId
    ");
    
    // Check each staff's availability
    while ($staff = $onDutyStaff->fetch_assoc()) {
        $staffId = $staff['Staff_ID'];
        
        // Check if staff has any appointments at the original time
        $conflictingAppointments = $conn->query("
            SELECT COUNT(*) as count 
            FROM Appointment 
            WHERE Staff_Assigned = '{$staff['Staff_Name']}' 
            AND DATE = '$originalDate' 
            AND TIME = '$originalTime'
            AND Status != 'Cancelled'
        ");
        
        $conflict = $conflictingAppointments->fetch_assoc();
        if ($conflict['count'] == 0) {
            return $staff; // Found available staff at original time
        }
    }
    
    // If no staff available at original time, find next available time slot
    return findNextAvailableSlot($conn, $originalDate, $originalTime, $excludeStaffId);
}

function findNextAvailableSlot($conn, $originalDate, $originalTime, $excludeStaffId) {
    // Get business hours (adjust these according to your business hours)
    $businessHours = ['09:00:00', '10:00:00', '11:00:00', '12:00:00', '13:00:00', '14:00:00', '15:00:00', '16:00:00'];
    
    // Find current time index
    $currentIndex = array_search($originalTime, $businessHours);
    if ($currentIndex === false) {
        $currentIndex = 0;
    }
    
    // Check subsequent time slots
    for ($i = $currentIndex + 1; $i < count($businessHours); $i++) {
        $newTime = $businessHours[$i];
        
        // Check all on-duty staff for availability at this new time
        $onDutyStaff = $conn->query("
            SELECT Staff_ID, Staff_Name 
            FROM Staff 
            WHERE Status = 'On-Duty' AND Staff_ID != $excludeStaffId
        ");
        
        while ($staff = $onDutyStaff->fetch_assoc()) {
            $conflictingAppointments = $conn->query("
                SELECT COUNT(*) as count 
                FROM Appointment 
                WHERE Staff_Assigned = '{$staff['Staff_Name']}' 
                AND DATE = '$originalDate' 
                AND TIME = '$newTime'
                AND Status != 'Cancelled'
            ");
            
            $conflict = $conflictingAppointments->fetch_assoc();
            if ($conflict['count'] == 0) {
                return [
                    'staff' => $staff,
                    'newTime' => $newTime
                ];
            }
        }
    }
    
    // If no slots found today, try next day (simplified - in real app you'd want a more robust solution)
    $nextDate = date('Y-m-d', strtotime($originalDate . ' +1 day'));
    return findNextAvailableSlot($conn, $nextDate, $businessHours[0], $excludeStaffId);
}

function reassignAppointments($conn, $staffId) {
    // Get staff name
    $staffResult = $conn->query("SELECT Staff_Name FROM Staff WHERE Staff_ID = $staffId");
    if ($staffResult->num_rows == 0) return false;
    
    $staff = $staffResult->fetch_assoc();
    $staffName = $staff['Staff_Name'];
    
    // Get today's date
    $today = date('Y-m-d');
    
    // Get all active appointments for this staff today
    $appointments = $conn->query("
        SELECT * FROM Appointment 
        WHERE Staff_Assigned = '$staffName' 
        AND DATE >= '$today'
        AND Status != 'Cancelled'
    ");
    
    while ($appointment = $appointments->fetch_assoc()) {
        // Find available staff
        $availability = findAvailableStaff($conn, $appointment['DATE'], $appointment['TIME'], $staffId);
        
        if (isset($availability['newTime'])) {
            // Found a new time slot
            $newStaff = $availability['staff'];
            $newTime = $availability['newTime'];
            
            // Update appointment
            $conn->query("
                UPDATE Appointment 
                SET Staff_Assigned = '{$newStaff['Staff_Name']}', 
                    TIME = '$newTime'
                WHERE Appointment_ID = {$appointment['Appointment_ID']}
            ");
            
            // Send email notification
            sendRescheduleEmail($appointment, $newStaff['Staff_Name'], $newTime);
        } else {
            // Found staff available at original time
            $newStaff = $availability;
            
            // Update appointment
            $conn->query("
                UPDATE Appointment 
                SET Staff_Assigned = '{$newStaff['Staff_Name']}'
                WHERE Appointment_ID = {$appointment['Appointment_ID']}
            ");
            
            // Send email notification
            sendRescheduleEmail($appointment, $newStaff['Staff_Name'], $appointment['TIME']);
        }
    }
    
    return true;
}


?>