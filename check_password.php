<?php
session_start();
include "database.php"; // Adjust to your database connection file

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $password = $_POST["password"] ?? "";

    // Query to check the management password
    $stmt = $conn->prepare("SELECT Password FROM ManagementPassword LIMIT 1");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && password_verify($password, $result["Password"])) {
        echo "success";
    } else {
        echo "error";
    }
}
?>
