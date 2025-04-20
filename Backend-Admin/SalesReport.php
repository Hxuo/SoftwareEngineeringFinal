<?php
session_start();
include 'database.php';

// Check if logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || 
    !in_array($_SESSION['role'], ['Owner', 'Admin', 'SuperAdmin'])) {
    header("Location: ../index.php");
    exit();
}

// Assign session variables
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$fullName = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : '';
$address = isset($_SESSION['address']) ? $_SESSION['address'] : '';
$barangay = isset($_SESSION['barangay']) ? $_SESSION['barangay'] : '';
$city = isset($_SESSION['city']) ? $_SESSION['city'] : '';
$region = isset($_SESSION['region']) ? $_SESSION['region'] : '';
$postalCode = isset($_SESSION['postal_code']) ? $_SESSION['postal_code'] : '';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$phonenumber = isset($_SESSION['phonenumber']) ? $_SESSION['phonenumber'] : '';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : ''; // Get user role

// Get the current year by default or get selected year from the request
$currentYear = date("Y");
$selectedYear = isset($_GET['year']) ? $_GET['year'] : $currentYear;
$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../Assests/logonisa-32.png" type="image/png">
    <link rel="icon" href="../Assests/logonisa-16.png" type="image/png">
    <title>Sales Report</title>
    <link rel="stylesheet" href="../Frontend-Admin/SalesReport.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="logo">
            <img src="../Assests/logorista.png" height="70">
        </div>

        <div class="menu-icon">&#9776;</div> <!-- Hamburger Menu -->

        <ul class="nav-links">
            <li><a href="../index.php">Home</a></li>
            <li><a href="../Backend-Admin/DashboardSched.php">Appointment</a></li>

            <?php if (in_array($role, ['Admin', 'Owner', 'SuperAdmin'])): ?>
                <li><a href="DashboardStaff1.php">Staff</a></li>
                <li><a href="SalesReport.php">Sales Report</a></li>
                <li><a href="DashboardHistory.php">History</a></li>
            <?php endif; ?>

            <?php if (in_array($role, ['Owner', 'SuperAdmin'])): ?>
                <li><a href="DashboardServices.php">Services</a></li>
            <?php endif; ?>

            <li><a href="logout.php" class="logout">Logout</a></li>
        </ul>
    </div>

    <!-- Add this right before the closing </body> tag -->
<div class="main-content">
<div class="stats-container">
    <!-- First Row -->
    <div class="stat-card">
        <h3>TOTAL APPOINTMENT BOOKED</h3>
        <p id="total-booked">0</p>
    </div>
    <div class="stat-card">
        <h3>CURRENT APPOINTMENT BOOKED</h3>
        <p id="current-booked">0</p>
    </div>
    <div class="stat-card">
        <h3>TOTAL SALES TODAY</h3>
        <p id="today-sales">0</p>
    </div>
    
    <!-- Second Row -->
    <div class="stat-card">
        <h3>COMPLETED</h3>
        <p id="total-completed">0</p>
    </div>
    <div class="stat-card">
        <h3>CANCELLED</h3>
        <p id="total-canceled">0</p>
    </div>
    <div class="stat-card">
        <h3>REFUNDED</h3>
        <p id="total-refunded">0</p>
    </div>
</div>

    <div class="chart-container">
        <div class="chart-header">
            <h2>Summary of sales</h2>
        </div>
        <canvas id="salesChart"></canvas>
    </div>
</div>

<!-- Add Chart.js library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="../Frontend-Admin/SalesReport.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
