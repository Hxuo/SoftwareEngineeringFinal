<?php
session_start();

$response = [];

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    $response = [
        "fullname" => $_SESSION["FullName"] ?? "",
        "address" => $_SESSION["Address"] ?? "",
        "barangay" => $_SESSION["Barangay"] ?? "",
        "city" => $_SESSION["City"] ?? "",
        "region" => $_SESSION["Region"] ?? "",
        "email" => $_SESSION["Email"] ?? "",
        "phonenumber" => $_SESSION["PhoneNumber"] ?? "",
    ];
}

header("Content-Type: application/json");
echo json_encode($response);
?>
