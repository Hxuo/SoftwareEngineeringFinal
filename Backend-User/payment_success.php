<?php
session_start();
include 'database.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__ . "/../vendor/autoload.php";

if (!isset($_SESSION['appointment_details'])) {
    // If no appointment details, show styled error message
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="../Assests/logonisa-32.png" type="image/png">
    <link rel="icon" href="../Assests/logonisa-16.png" type="image/png">
        <title>Appointment Scheduled</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f8f8f8;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .container {
                background: white;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                text-align: center;
            }
            h2 {
                color:  #4CAF50;
            }
            p {
                color: #333;
            }
            .btn {
                display: inline-block;
                margin-top: 15px;
                padding: 10px 20px;
                background-color: #4CAF50;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                font-weight: bold;
            }
            .btn:hover {
                background-color: #45a049;
            }
        </style>
    </head>
    <body>
        <div class="container">
        <h2>Your Appointment has been scheduled successfully!</h2>
        <p>Check your email for more information.</p>
            <a href="../Backend-User/index.php" class="btn">Return to Home</a>
        </div>
    </body>
    </html>
    <?php
    exit();
}

$appointmentDetails = $_SESSION['appointment_details'];
$name = $appointmentDetails['name'];
$email = $appointmentDetails['email'];
$phonenumber = $appointmentDetails['phonenumber'];
$date = $appointmentDetails['date'];
$time = $appointmentDetails['time'];
$totalPrice = $appointmentDetails['totalPrice'];
$services = $appointmentDetails['services'];
$paymentMethod = $appointmentDetails['paymentMethod'];

// Staff assignment using round-robin
function getNextAvailableStaff($conn) {
    $query = "SELECT Staff_ID, Staff_Name FROM Staff WHERE Status = 'On-Duty' ORDER BY Staff_ID ASC";
    $result = $conn->query($query);
    $staffList = [];

    while ($row = $result->fetch_assoc()) {
        $staffList[] = $row;
    }

    if (count($staffList) == 0) {
        return null; // No available staff
    }

    $appointmentCountQuery = "SELECT COUNT(*) as count FROM Appointment";
    $appointmentCountResult = $conn->query($appointmentCountQuery);
    $appointmentCount = ($appointmentCountResult && $row = $appointmentCountResult->fetch_assoc()) ? $row['count'] : 0;

    $nextIndex = $appointmentCount % count($staffList);
    return [
        'id' => $staffList[$nextIndex]['Staff_ID'],
        'name' => $staffList[$nextIndex]['Staff_Name']
    ];
}

$staff = getNextAvailableStaff($conn);
$staffName = $staff ? $staff['name'] : "No available staff";

// Generate unique Appointment_ID
function generateAppointmentID($conn) {
    do {
        $randomID = str_pad(mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
        $stmt = $conn->prepare("SELECT COUNT(*) FROM Appointment WHERE Appointment_ID = ?");
        $stmt->bind_param("i", $randomID);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
    } while ($count > 0);
    return $randomID;
}

$appointmentID = generateAppointmentID($conn);
$servicesList = implode(", ", array_map(fn($s) => $s['name'], $services));

$query = "INSERT INTO Appointment (Appointment_ID, Name, Email, DATE, TIME, Staff_Assigned, Services, Price, PaymentMethod, PhoneNumber, Status) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Scheduled')";
$stmt = $conn->prepare($query);
$stmt->bind_param("issssssdss", $appointmentID, $name, $email, $date, $time, $staffName, $servicesList, $totalPrice, $paymentMethod, $phonenumber);
$stmt->execute();
$stmt->close();

$formattedTime = date("h:i A", strtotime($time));

// Send email confirmation
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
    $mail->addAddress($email, $name);
    $mail->isHTML(true);
    $mail->Subject = "Your Appointment Confirmation - PTN_$appointmentID";
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
                    <p style='font-size: 16px; margin-bottom: 20px; color: #555;'>Here is your booking information:</p>
                    <p style='font-size: 16px; margin-bottom: 10px; color: #555;'><strong>Appointment ID:</strong> $appointmentID</p>
                    <p style='font-size: 16px; margin-bottom: 10px; color: #555;'><strong>Date:</strong> $date</p>
                    <p style='font-size: 16px; margin-bottom: 10px; color: #555;'><strong>Time:</strong> $formattedTime</p>
                    <p style='font-size: 16px; margin-bottom: 10px; color: #555;'><strong>Services:</strong> $servicesList</p>
                    <p style='font-size: 16px; margin-bottom: 10px; color: #555;'><strong>Staff Assigned:</strong> $staffName</p>
                    <p style='font-size: 16px; margin-bottom: 20px; color: #555;'><strong>Price:</strong> PHP $totalPrice</p>
                    <br>
                    <p style='font-size: 16px; margin-top: 20px; color: #555;'>Please arrive on time.</p>
                    <p style='font-size: 16px; margin-top: 20px; color: #555;'>Thank you!</p>
                </div>
    
                <!-- Footer -->
                <div style='background-color: #f1f1f1; padding: 20px; text-align: center; color: #777; font-size: 14px;'>
                    <!-- Leave this blank as requested -->
                </div>
            </div>
        </div>
    ";
    
    $mail->send();
} catch (Exception $e) {
    error_log("Mail Exception: " . $e->getMessage());
}

// Do not unset session here, keep it until the page has rendered
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../Assests/logonisa-32.png" type="image/png">
    <link rel="icon" href="../Assests/logonisa-16.png" type="image/png">
    <title>Appointment Scheduled</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            color: #4CAF50;
        }
        p {
            color: #333;
        }
        .btn {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Your Appointment has been scheduled successfully!</h2>
        <p>Check your email for more information.</p>
        <a href="../Backend-User/index.php" class="btn">Proceed to Home</a>
    </div>
</body>
</html>

<?php
// Finally, after rendering, clear the session data if needed
unset($_SESSION['appointment_details']);
?>
