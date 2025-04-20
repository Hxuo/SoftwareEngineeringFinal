<?php
require 'database.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/../vendor/autoload.php"; // Adjust path if necessary

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve form data
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phonenumber = $_POST['phonenumber'];
    $address = $_POST['address'];
    $barangay = $_POST['barangay'];
    $city = $_POST['city'];
    $region = $_POST['region'];
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword'];
    $currentPage = $_POST['currentPage']; // Get current page URL

    // Validate password match
    if ($password !== $confirmpassword) {
        echo json_encode(["status" => "error", "message" => "Passwords do not match."]);
        exit;
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM accounts WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Email already exists."]);
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Generate verification token
    $token = bin2hex(random_bytes(50));

    // Insert new account into the database
$stmt = $conn->prepare("INSERT INTO accounts (FullName, Address, Barangay, City, Region, Email, PhoneNumber, Password, Token, is_verified, Role) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 'User')");
$stmt->bind_param("sssssssss", $fullname, $address, $barangay, $city, $region, $email, $phonenumber, $hashedPassword, $token);


    if ($stmt->execute()) {
        // Send verification email
        if (sendVerificationEmail($email, $token)) {
            echo json_encode(["status" => "success", "message" => "Registration successful! Please check your email for verification."]);
            exit();
        } else {
            echo json_encode(["status" => "error", "message" => "Error sending verification email."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Registration failed."]);
    }
}

// Function to send verification email
function sendVerificationEmail($email, $token) {
    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'softwareengineeringfinal@gmail.com'; // Your Gmail
        $mail->Password = 'pgvy bati jffn pbty'; // Your Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Sender and recipient
        $mail->setFrom('softwareengineeringfinal@gmail.com', 'Aniah Brow Aesthetics');
        $mail->addAddress($email);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification';
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px;'>
                <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); overflow: hidden;'>
                    <!-- Header -->
               <div style='background-color: #C4A484; color: white; text-align: center; padding: 20px;'>
    <h1 style='margin: 0; font-size: 24px; font-weight: bold;'>Aniah Brow Aesthetics</h1>
</div>


                    <!-- Content -->
                    <div style='padding: 30px; text-align: center;'>
                        <h3 style='font-size: 20px; margin-bottom: 15px; color: #333;'>Your account has been successfully created.</h3>
                        <p style='font-size: 16px; margin-bottom: 20px; color: #555;'>Click the button below to verify your email:</p>
                        <a href='http://localhost/SoftEngFinalV9/Backend-User/verify.php?token=" . urlencode($token) . "' 
                           style='padding: 12px 24px; background-color: #C4A484; color: white; text-decoration: none; border-radius: 6px; font-size: 16px; font-weight: 500;'>
                           Verify Email
                        </a>
                        <p style='font-size: 16px; margin-top: 20px; color: #555;'>If you did not register, ignore this email.</p>
                    </div>

                    <!-- Footer -->
                    <div style='background-color: #f1f1f1; padding: 20px; text-align: center; color: #777; font-size: 14px;'>
                        <!-- Leave this blank as requested -->
                    </div>
                </div>
            </div>
        ";

        // Send email
        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}
?>