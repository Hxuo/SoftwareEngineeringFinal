<?php
session_start();
require 'database.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Kunin ang user mula sa database kasama ang email verification status
    $stmt = $conn->prepare("SELECT * FROM adminaccount WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // I-verify ang password gamit ang password_verify()
    if ($user && password_verify($password, $user["Password"])) {
        
        // I-check kung verified ang email
        if ($user["is_verified"] == 1) {  // Siguraduhing meron kang `is_verified` column sa `adminaccount` table
            $_SESSION["loggedin"] = true;
            $_SESSION["username"] = $user["Username"];
            header("Location: DashboardSched.php");
            exit();
        } else {
            echo "<script>alert('Please verify your email first before logging in.'); window.location='index.php';</script>";
        }
        
    } else {
        echo "<script>alert('Invalid username or password.'); window.location='index.php';</script>";
    }
}
?>
