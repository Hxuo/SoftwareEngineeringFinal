<?php
session_start();
require 'database.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $conn->prepare("SELECT FullName, Address, Barangay, City, Region, Email, PhoneNumber, Role FROM accounts WHERE Token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Update account to verified
        $stmt = $conn->prepare("UPDATE accounts SET is_verified = 1, Token = NULL WHERE Token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();

        // Set session variables properly
        $_SESSION['full_name'] = $user['FullName'];
        $_SESSION['address'] = $user['Address'];
        $_SESSION['barangay'] = $user['Barangay'];
        $_SESSION['city'] = $user['City'];
        $_SESSION['region'] = $user['Region'];
        $_SESSION['email'] = $user['Email'];
        $_SESSION['phonenumber'] = $user['PhoneNumber'];
        $_SESSION['role'] = $user['Role'];
        $_SESSION['loggedin'] = true;

        $redirectPage = ($user['Role'] === 'Admin') ? 'DashboardSched.php' : 'index.php';
    
        echo "<script>
                alert('Your email has been verified. Redirecting to your dashboard...');
                window.location.href = '$redirectPage';
              </script>";
        exit();
    }
    else {
        // Invalid token alert
        echo "<script>
                alert('Invalid verification token.');
                window.location.href = 'Registration.php';
              </script>";
        exit();
    }
} else {
    // No token provided alert
    echo "<script>
            alert('No verification token provided.');
            window.location.href = 'Registration.php';
          </script>";
    exit();
}
?>