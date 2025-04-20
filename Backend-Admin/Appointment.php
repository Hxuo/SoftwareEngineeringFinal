<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Schedule</title>
    <link rel="stylesheet" href="../Frontend-Admin/Appointment.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <div class="logo">
        <img src="logo.png" alt="Logo">
        <span>APPOINT-IT</span>
    </div>

    <div class="menu-icon">&#9776;</div> <!-- Hamburger Menu -->

    <ul class="nav-links">
    <li><a href="DashboardSched.php">Schedule</a></li>
        <li><a href="DashboardStaff1.php">Staff</a></li>
        <li><a href="Appointment.php">Appointment</a></li>
        <li><a href="DashboardHistory.php">History</a></li>
        <li><a href="DashboardServices.php">Services</a></li>
        <li><a href="logout.php" class="logout">Logout</a></li>
    </ul>
</div>

<!-- Calendar Section -->
<div class="calendar-container">
    <div class="calendar-header">
    </div>

    <!-- Legend -->
    <div class="legend">
        <div><span class="available"></span> Slots Available</div>
        <div><span class="reserved"></span> Fully Reserved</div>
        <div><span class="blocked"></span> Closed for Booking</div>
    </div>

    <!-- Calendar -->
    <div class="calendar-box">
        <table>
            <thead>
                <tr>
                    <th>Sunday</th>
                    <th>Monday</th>
                    <th>Tuesday</th>
                    <th>Wednesday</th>
                    <th>Thursday</th>
                    <th>Friday</th>
                    <th>Saturday</th>
                </tr>
            </thead>
            <tbody id="calendar-body">
                <!-- Dates will be injected here by JavaScript -->
            </tbody>
        </table>
    </div>
</div>

<script src="../Frontend-Admin/Reschedule.js"> </script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
