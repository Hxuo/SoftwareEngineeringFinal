<?php
session_start();
require 'database.php';
require __DIR__ . "/./vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $currentPage = $_POST["currentPage"];

    // Check if there's a failed attempts counter in session
    if (!isset($_SESSION['login_attempts'][$email])) {
        $_SESSION['login_attempts'][$email] = 0;
    }

    $stmt = $conn->prepare("SELECT * FROM accounts WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["Password"])) {
        // Reset failed attempts on successful login
        unset($_SESSION['login_attempts'][$email]);
        
        if ($user["is_verified"] == 1) {
            $_SESSION["loggedin"] = true;
            $_SESSION["full_name"] = $user["FullName"];
            $_SESSION["address"] = $user["Address"];
            $_SESSION["barangay"] = $user["Barangay"];
            $_SESSION["city"] = $user["City"];
            $_SESSION["region"] = $user["Region"];
            $_SESSION["email"] = $user["Email"];
            $_SESSION["phonenumber"] = $user["PhoneNumber"];
            $_SESSION["role"] = $user["Role"];

            $rolesToRedirect = ["Admin", "Owner", "Staff", "SuperAdmin", "Management"];

            if (in_array($user["Role"], $rolesToRedirect)) {
                header("Location: ./Backend-Admin/DashboardSched.php");
            } else {
                header("Location: $currentPage");
            }
            exit();
        } else {
            echo "<script>alert('Please verify your email first.'); window.location='index.php';</script>";
        }
    } else {
        // Increment failed attempts
        $_SESSION['login_attempts'][$email]++;
        
        // Check if reached 3 failed attempts
        if ($_SESSION['login_attempts'][$email] >= 3 && $user) {
            // Send warning email
            sendSecurityAlert($user);
            
            // Reset the counter after sending the email
            $_SESSION['login_attempts'][$email] = 0;
        }
        
        // Always show the same error message regardless of security alert
        echo "<script>alert('Invalid email or password.'); window.location='index.php';</script>";
    }
}

function sendSecurityAlert($user) {
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
        $mail->addAddress($user['Email']);
        $mail->isHTML(true);
        
        $mail->Subject = 'Security Alert: Multiple Failed Login Attempts';
        $mail->Body = "
    <html>
    <body>
        <p>Dear {$user['FullName']},</p>
        <p>Someone has attempted to log in to your account multiple times with incorrect credentials.</p>
        <p>If this was you and you've forgotten your password, please click the link below to reset it:</p>
        <p><a href='http://localhost/SoftEngFinalV9/Backend-User/change-password.php?email={$user['Email']}'>Click to change your password</a></p>
        <p>If this wasn't you, we recommend changing your password immediately.</p>
        <br>
        <p>Best regards,</p>
        <p>Aniah Brow Aesthetics Team</p>
    </body>
    </html>
";
        
        $mail->send();
    } catch (Exception $e) {
        // You might want to log this error instead of showing it to users
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}
?>