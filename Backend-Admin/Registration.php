<?php
require 'database.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/../vendor/autoload.php"; // Adjust path if necessary

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Password validation
    if (!preg_match('/^(?=.*[A-Z])(?=.*[\W_]).{8,}$/', $password)) {
        echo "<script>alert('Password must be at least 8 characters long, contain at least one uppercase letter, and one symbol.'); window.location='Registration.php';</script>";
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Secure password hashing
    $token = bin2hex(random_bytes(32)); // Generate a secure random token

    // Insert user into database
    $stmt = $conn->prepare("INSERT INTO adminaccount (Username, Password, Email, Token) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $hashed_password, $email, $token);

    if ($stmt->execute()) {
        // Send verification email
        if (sendVerificationEmail($email, $token)) {
            echo "<script>alert('Registration successful! Please check your email for verification.'); window.location='login2.php';</script>";
        } else {
            echo "<script>alert('Error sending verification email.'); window.location='Registration.php';</script>";
        }
    } else {
        echo "<script>alert('Registration failed.'); window.location='Registration.php';</script>";
    }
}

// Function to send verification email
function sendVerificationEmail($email, $token) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'softwareengineeringfinal@gmail.com'; // Your Gmail
        $mail->Password = 'pgvy bati jffn pbty'; // Your Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('softwareengineeringfinal@gmail.com', 'PhpMailer');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Email Verification';
        $mail->Body = "
            <h3>Your account has been successfully created.</h3>
            <p>Click the button below to verify your email:</p>
            <a href='http://localhost/SoftEngFinalV1/Backend-Admin/verify.php?token=" . urlencode($token) . "' 
               style='padding:10px 20px; background-color:blue; color:white; text-decoration:none;'>
               Verify Email
            </a>
            <p>If you did not register, ignore this email.</p>
        ";

        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <style>
       body {
            font-family: Arial, sans-serif;
            background-color: #4D3B30; /* Beige */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 320px;
            text-align: center;
        }
        h2 {
            color: #6b4226; /* Dark Brown */
        }
        label {
            display: block;
            text-align: left;
            margin-top: 10px;
            color: #6b4226;
        }
        input {
            width: calc(100% - 16px);
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            display: block;
        }
        .error {
            color: red;
            font-size: 12px;
        }
        button {
            background-color: #a67b5b; /* Light Brown */
            color: white;
            border: none;
            padding: 10px;
            margin-top: 15px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #8c6239;
        }
    </style>
    <script>
        function validatePassword() {
            let password = document.getElementById("password").value;
            let error = document.getElementById("passwordError");
            let regex = /^(?=.*[A-Z])(?=.*[\W_]).{8,}$/; 

            if (!regex.test(password)) {
                error.textContent = "Password must be at least 8 characters, contain an uppercase letter, and a symbol.";
                return false;
            } else {
                error.textContent = "";
                return true;
            }
        }

        function validateForm() {
            return validatePassword();
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form method="post" onsubmit="return validateForm()">
            <label>Enter Username:</label>
            <input type="text" name="username" required>

            <label>Enter Password:</label>
            <input type="password" id="password" name="password" required onkeyup="validatePassword()">
            <span id="passwordError" class="error"></span>

            <label>Enter Email:</label>
            <input type="email" name="email" required>
            <span class="error"><?php echo isset($error) ? $error : ''; ?></span>

            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>