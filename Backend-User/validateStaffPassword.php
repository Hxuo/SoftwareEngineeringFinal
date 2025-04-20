<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'database.php'; // Include the MySQLi connection file

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $inputPassword = $_POST['password']; // Get the password from the POST request

    // Prepare and execute the query to fetch the stored password
    $query = "SELECT Password FROM ManagementPassword LIMIT 1";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $storedPassword = $row['Password']; // Get the plain text password from the database

        // Compare the input password with the stored plain text password
        if ($inputPassword === $storedPassword) {
            echo json_encode(["status" => "success"]); // Password is correct
        } else {
            echo json_encode(["status" => "error", "message" => "Wrong password"]); // Password is incorrect
        }
    } else {
        echo json_encode(["status" => "error", "message" => "No password found in database"]); // No password found in the database
    }

    // Close the database connection
    $conn->close();
}
?>