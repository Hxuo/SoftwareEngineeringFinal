<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Schedule</title>
    <link rel="stylesheet" href="../Frontend-Admin/Reschedule.css">
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
        <li><a href="#">Schedule</a></li>
        <li><a href="#">Staff</a></li>
        <li><a href="#">Appointment</a></li>
        <li><a href="#">History</a></li>
        <li><a href="#">Services</a></li>
        <li><a href="#" class="logout">Logout</a></li>
    </ul>
</div>

<!-- Calendar Section -->
<div class="calendar-container">
    <div class="calendar-header">
        <span id="selected-date">SELECTED DATE:</span>
        <span>
            SELECT TIME:
            <select class="time-dropdown">
                <option value="9:00 AM">9:00 AM</option>
                <option value="10:00 AM">10:00 AM</option>
                <option value="11:00 AM">11:00 AM</option>
                <option value="1:00 PM">1:00 PM</option>
                <option value="2:00 PM">2:00 PM</option>
            </select>
        </span>
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
