<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Schedule</title>
    <link rel="stylesheet" href="../Frontend-Admin/DashboardAppointment.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <div class="logo">
        <img src="logo.png" alt="Logo">
        <span>APPOINT-IT</span>
    </div>
    <div class="menu-icon">&#9776;</div>
    <ul class="nav-links">
    <li>
        <a href="DashboardSched.php">Schedule</a></li>
        <li><a href="DashboardStaff1.php">Staff</a></li>
        <li><a href="Appointment.php">Appointment</a></li>
        <li><a href="DashboardHistory.php">History</a></li>
        <li><a href="DashboardServices.php">Services</a></li>
        <li><a href="logout.php" class="logout">Logout</a></li>
    </ul>
</div>
<!-- Appointment Schedule Table -->
<div class="container mt-5">
    <h2 class="text-center">February 14, 2025</h2>
    <table class="table table-bordered text-center appointment-table">
        <thead>
            <tr>
                <th>TIME</th>
                <th>CUSTOMER NAME</th>
                <th>STAFF NAME</th>
                <th>SERVICE</th>
                <th>ACTION</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1:00 PM - 2:00 PM</td>
                <td>ROBERTO BATUNBAKAL</td>
                <td>BERTA</td>
                <td>GLUTA DRIP</td>
                <td>
                    <button class="btn btn-success btn-sm">RESCHEDULE</button>
                    <button class="btn btn-danger btn-sm">CANCEL BOOK</button>
                </td>
            </tr>
            <tr>
                <td>1:00 PM - 2:00 PM</td>
                <td>ROBERTO BATUNBAKAL</td>
                <td>JORRAT</td>
                <td>MESO LIPO</td>
                <td>
                    <button class="btn btn-success btn-sm">RESCHEDULE</button>
                    <button class="btn btn-danger btn-sm">CANCEL BOOK</button>
                </td>
            </tr>
            <tr>
                <td>3:00 PM - 4:00 PM</td>
                <td>ROSA DIMAKULANGAN</td>
                <td>BOGART</td>
                <td>FACIAL</td>
                <td>
                    <button class="btn btn-success btn-sm">RESCHEDULE</button>
                    <button class="btn btn-danger btn-sm">CANCEL BOOK</button>
                </td>
            </tr>
            <tr>
                <td>4:00 PM - 5:00 PM</td>
                <td>ROBERT CUMIN</td>
                <td>KOYKOY</td>
                <td>MANICURE</td>
                <td>
                    <button class="btn btn-success btn-sm">RESCHEDULE</button>
                    <button class="btn btn-danger btn-sm">CANCEL BOOK</button>
                </td>
            </tr>
            <tr>
                <td>6:00 PM - 7:00 PM</td>
                <td>JENNY THALIA</td>
                <td>BUGOY</td>
                <td>PEDICURE</td>
                <td>
                    <button class="btn btn-success btn-sm">RESCHEDULE</button>
                    <button class="btn btn-danger btn-sm">CANCEL BOOK</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
