<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/./vendor/autoload.php";



function sendVerificationEmail($email, $token) {
    $mail = new PHPMailer(true);

    try {
        // SMTP Server Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'softwareengineeringfinal@gmail.com'; // GMAIL mo
        $mail->Password = 'pgvy bati jffn pbty'; // App Password ng Gmail mo
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Sender & Recipient
        $mail->setFrom('softwareengineeringfinal@gmail.com', 'PhpMailer');
        $mail->addAddress($email);

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification';
        $mail->Body = "
            <h3>Your account has been successfully created.</h3>
            <p>Click the button below to verify your email:</p>
            <a href='http://localhost/SoftEngFinalV4/Backend-User/verify.php?token=$token'  
               style='padding:10px 20px; background-color:blue; color:white; text-decoration:none;'>
               Verify Email
            </a>
            <p>If you did not register, ignore this email.</p>
        ";

        // Send email
        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}
?>
