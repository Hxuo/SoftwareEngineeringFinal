<?php
session_start();
include 'database.php';

// Clear any pending appointment data
if (isset($_SESSION['appointment_details'])) {
    unset($_SESSION['appointment_details']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./Assests/logonisa-32.png" type="image/png">
    <link rel="icon" href="./Assests/logonisa-16.png" type="image/png">
    <title>Appointment Not Scheduled</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            color: #d9534f;
        }
        p {
            color: #333;
        }
        .btn {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #d9534f;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn:hover {
            background-color: #c9302c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Your Appointment isn't scheduled</h2>
        <p>Please try again or contact support.</p>
        <a href="index.php" class="btn">Proceed to Home</a>
    </div>
</body>
</html>
