<?php
session_start();
require 'database.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $newPassword = $_POST["new_password"];
    
    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    // Update password in database
    $stmt = $conn->prepare("UPDATE accounts SET Password = ? WHERE Email = ?");
    $stmt->bind_param("ss", $hashedPassword, $email);
    
    if ($stmt->execute()) {
        // Get user data
        $stmt = $conn->prepare("SELECT * FROM accounts WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        // Set session variables
        $_SESSION["loggedin"] = true;
        $_SESSION["full_name"] = $user["FullName"];
        $_SESSION["address"] = $user["Address"];
        $_SESSION["barangay"] = $user["Barangay"];
        $_SESSION["city"] = $user["City"];
        $_SESSION["region"] = $user["Region"];
        $_SESSION["email"] = $user["Email"];
        $_SESSION["phonenumber"] = $user["PhoneNumber"];
        $_SESSION["role"] = $user["Role"];
        
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Error updating password"]);
    }
    exit();
}

// Pre-fill email if coming from link
$email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./Assests/logonisa-32.png" type="image/png">
    <link rel="icon" href="./Assests/logonisa-16.png" type="image/png">
    <title>Change Password</title>
    <link rel="stylesheet" href="./Assets/css/styles.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .password-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        input:read-only {
            background-color: #f9f9f9;
            color: #666;
        }
        .error {
            color: #e74c3c;
            font-size: 13px;
            margin-top: 5px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #45a049;
        }
        #successModal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .success-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .success-content h3 {
            color: #4CAF50;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="password-container">
        <h2>Change Your Password</h2>
        <form id="passwordForm">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $email; ?>" required readonly>
            </div>
            
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required oninput="validatePassword()">
                <div id="password-error" class="error"></div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required oninput="validatePassword()">
                <div id="confirm-error" class="error"></div>
            </div>
            
            <button type="button" onclick="submitPasswordChange()">Change Password</button>
        </form>
    </div>

    <!-- Success Modal -->
    <div id="successModal">
        <div class="success-content">
            <h3>Password Changed Successfully!</h3>
            <p>You will be redirected to the homepage.</p>
        </div>
    </div>

    <script>
        function validatePassword() {
            const newPassword = document.getElementById('new_password').value.trim();
            const confirmPassword = document.getElementById('confirm_password').value.trim();
            const passwordError = document.getElementById('password-error');
            const confirmError = document.getElementById('confirm-error');
            
            const regex = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).{8,}$/;
            
            // Validate password format
            if (newPassword && !regex.test(newPassword)) {
                passwordError.innerText = 'Password must be at least 8 characters with 1 uppercase, 1 number, and 1 symbol.';
                return false;
            } else {
                passwordError.innerText = '';
            }
            
            // Validate password match
            if (confirmPassword && newPassword !== confirmPassword) {
                confirmError.innerText = 'Passwords do not match';
                return false;
            } else {
                confirmError.innerText = '';
            }
            
            return true;
        }

        function submitPasswordChange() {
            const email = document.getElementById('email').value.trim();
            const newPassword = document.getElementById('new_password').value.trim();
            const confirmPassword = document.getElementById('confirm_password').value.trim();
            
            if (!validatePassword()) {
                return;
            }
            
            if (!email || !newPassword || !confirmPassword) {
                alert('Please fill in all fields');
                return;
            }
            
            const formData = new FormData();
            formData.append('email', email);
            formData.append('new_password', newPassword);
            
            fetch('change-password.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success modal
                    const modal = document.getElementById('successModal');
                    modal.style.display = 'flex';
                    
                    // Redirect after 2 seconds
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 2000);
                } else {
                    alert(data.message || 'Error changing password');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while changing your password');
            });
        }
    </script>
</body>
</html>