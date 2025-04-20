<?php
include 'database.php';

// Get form data
$staffName = $_POST['staff_name'];
$phoneNumber = $_POST['phone_number'];

// Validate phone number (server-side validation)
if (!preg_match('/^\d{11}$/', $phoneNumber)) {
    die("Invalid phone number format. Please enter exactly 11 digits.");
}

// Insert into database using prepared statements
$sql = "INSERT INTO Staff (Staff_Name, PhoneNumber, Status) VALUES (?, ?, 'On-Duty')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $staffName, $phoneNumber);

if ($stmt->execute()) {
    echo "New staff added successfully!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();

// Redirect back to the dashboard
header("Location: DashboardStaff1.php");
exit();
?>