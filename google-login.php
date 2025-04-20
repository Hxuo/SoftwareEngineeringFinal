<?php
session_start();
require 'database.php';

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $currentPage = $_POST["currentPage"]; 

    $stmt = $conn->prepare("SELECT * FROM accounts WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // If user exists, log them in
        $_SESSION["loggedin"] = true;
        $_SESSION["email"] = $user["Email"];
        $_SESSION["role"] = $user["Role"];
        $_SESSION["full_name"] = $user["FullName"]; 
        $_SESSION["address"] = $user["Address"]; 
        $_SESSION["barangay"] = $user["Barangay"]; 
        $_SESSION["city"] = $user["City"]; 
        $_SESSION["region"] = $user["Region"]; 
        $_SESSION["phonenumber"] = $user["PhoneNumber"]; // Tamang column name
       

        // Determine the redirect URL based on the user's role
        $rolesToRedirect = ["Admin", "Owner", "Staff", "SuperAdmin", "Management"];

        $redirectUrl = in_array($user["Role"], $rolesToRedirect) ? "./Backend-Admin/DashboardSched.php" : $currentPage;


        echo json_encode(["status" => "redirect", "url" => $redirectUrl]);
    } else {
        // If email is not found in DB, create a new user without phone number
        $stmt = $conn->prepare("INSERT INTO accounts (Email, Role, FullName) VALUES (?, 'User', ?)");
        $stmt->bind_param("ss", $email, $name);
        $stmt->execute();

        $_SESSION["loggedin"] = true;
        $_SESSION["email"] = $email;
        $_SESSION["role"] = 'User';
        $_SESSION["full_name"] = $name;

        echo json_encode(["status" => "redirect", "url" => $currentPage]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>
