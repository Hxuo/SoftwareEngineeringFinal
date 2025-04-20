<?php
session_start();
include 'database.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Get the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

// Validate and sanitize input data
$fullname = filter_var($data['fullname'], FILTER_SANITIZE_STRING);
$email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
$phonenumber = filter_var($data['phonenumber'], FILTER_SANITIZE_EMAIL);
$address = filter_var($data['address'], FILTER_SANITIZE_STRING);
$barangay = filter_var($data['barangay'], FILTER_SANITIZE_STRING);
$city = filter_var($data['city'], FILTER_SANITIZE_STRING);
$region = filter_var($data['region'], FILTER_SANITIZE_STRING);
$password = password_hash($data['password'], PASSWORD_DEFAULT);

// Update the user's details in the database
$stmt = $conn->prepare("UPDATE accounts SET FullName = ?, Address = ?, Barangay = ?, City = ?, PhoneNumber = ?, Region = ?, Password = ? WHERE Email = ?");
$stmt->bind_param("ssssssss", $fullname, $address, $barangay, $city, $phonenumber, $region, $password, $email);


if ($stmt->execute()) {
    $_SESSION['full_name'] = $fullname;
    $_SESSION['address'] = $address;
    $_SESSION['barangay'] = $barangay;
    $_SESSION['city'] = $city;
    $_SESSION['region'] = $region;
    $_SESSION['email'] = $email;
    $_SESSION['phonenumber'] = $phonenumber;
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update account']);
}

$stmt->close();
$conn->close();
?>