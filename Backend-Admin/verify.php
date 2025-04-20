<?php
session_start(); // Start the session
require 'database.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if token exists
    $stmt = $conn->prepare("SELECT Username, Email FROM adminaccount WHERE Token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch user data
        $user = $result->fetch_assoc();
        $username = $user['Username'];
        $email = $user['Email'];

        // Verify the account (set Token to NULL)
        $stmt = $conn->prepare("UPDATE adminaccount SET is_verified = 1 WHERE Token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();

        // Set session variables
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $_SESSION['loggedin'] = true;

        // Redirect to dashboard
        echo "<script>alert('Your email has been verified. Redirecting to your dashboard...'); window.location='DashboardSched.php';</script>";
        exit();
    } else {
        echo "<script>alert('Invalid verification token.'); window.location='Registration.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('No verification token provided.'); window.location='Registration.php';</script>";
    exit();
}
?>
