<?php
session_start();
include 'database.php';

// Check if logged in
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// Assign session variables
$fullName = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : '';
$address = isset($_SESSION['address']) ? $_SESSION['address'] : '';
$barangay = isset($_SESSION['barangay']) ? $_SESSION['barangay'] : '';
$city = isset($_SESSION['city']) ? $_SESSION['city'] : '';
$region = isset($_SESSION['region']) ? $_SESSION['region'] : '';
$postalCode = isset($_SESSION['postal_code']) ? $_SESSION['postal_code'] : '';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$phonenumber = isset($_SESSION['phonenumber']) ? $_SESSION['phonenumber'] : '';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : ''; // Get user role
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../Assests/logonisa-32.png" type="image/png">
    <link rel="icon" href="../Assests/logonisa-16.png" type="image/png">
    <title>Schedules</title>
    <link rel="stylesheet" href="../Frontend-Admin/Dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

</head>
<body>

<div class="navbar">
    <div class="logo">
    <img src="../Assests/logorista.png" height="70">
    </div>

    <div class="menu-icon">&#9776;</div> <!-- Hamburger Menu -->

    <ul class="nav-links">
        <li><a href="../index.php">Home</a></li>
        <li><a href="DashboardSched.php">Appointment</a></li>

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

<section class="schedule-container">
    <h2>TODAY</h2>
    <?php if (in_array($role, ['Admin', 'Owner', 'SuperAdmin'])): ?>
            <button class="refund-request-btn" onclick="openRefundRequestModal()">Refund Requests</button>
        <?php endif; ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>APPOINTMENT ID</th>
                    <th>TIME</th>
                    <th>CUSTOMER NAME</th>
                    <th>Contact Number</th>
                    <th>STAFF NAME</th>
                    <th>SERVICE</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody id="schedule-body">
                <!-- Rows will be dynamically inserted here -->
            </tbody>
        </table>
    </div>
</section>
<!-- Pagination for the schedule table -->
<div class="pagination">
    <button id="prevPage">Previous</button>
    <span id="pageIndicator">Page 1</span>
    <button id="nextPage">Next</button>
</div>




<!-- DONE MODAL -->
<div id="doneModal" class="modal-done">
  <div class="modal-done-content">
    <button class="close-done" onclick="closeDoneModal()">✖</button>
    <h3>Service has been given</h3>
    <p>The appointment has been successfully completed.</p>
    <button class="ok-button" onclick="closeDoneModal()">OK</button>
  </div>
</div>

<div id="resched-modal" class="modal-resched">
    <div class="modal-resched-content">
        <span class="close-resched" onclick="closeReschedModal()">&times;</span>
        <h3>Reschedule Appointment</h3>
        <form id="resched-form">
            <input type="hidden" id="appointment_id" name="appointment_id" />
            <div class="form-group">
                <label for="datePickerInput">Select Date:</label>
                <input type="text" id="datePickerInput" placeholder="Select Date" readonly onclick="openNewCalendarModal()" required />
            </div>

            <div class="form-group">
                <label for="timeSelect">Select Time:</label>
                <select id="timeSelect" required>
                    <option value="">Select Time</option>
                </select>
            </div>

            <button type="submit" class="confirm-resched">Confirm</button>
        </form>
    </div>
</div>

<!-- Calendar Modal -->
<div id="calendarModalNew" class="modal-calendar-resched">
    <div class="modal-calendar-content-resched">
        <button class="close-modal" onclick="closeNewCalendarModal()">×</button>
        <div class="calendar-header">
            <button onclick="loadCalendar('current')">This Month</button>
            <button onclick="loadCalendar('next')">Next Month</button>
        </div>
        <div id="calendarContainer"></div>
        <button class="confirm-button" onclick="confirmNewDate()">Confirm</button>
    </div>
</div>

<!-- Success Modal -->
<div id="success-modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeSuccessModal()">&times;</span>
        <h3>Appointment Rescheduled Successfully</h3>
        <p>Your appointment has been updated.</p>
        <button class="confirm" onclick="redirectToDashboard()">OK</button>
    </div>
</div>

   

    <!-- Cancel Confirmation Modal -->
<div id="cancel-confirm-modal" class="modal-cancel-confirm">
    <div class="cancel-confirm-content">
        <h3>Are you sure you want to cancel the appointment?</h3>
        <button class="cancel-yes" onclick="confirmCancellation()">Yes</button>
        <button class="cancel-no" onclick="closeCancelConfirmModal()">No</button>
    </div>
</div>

<!-- Cancel Success Modal -->
<div id="cancel-success-modal" class="modal-cancel-success">
    <div class="cancel-success-content">
        <h3>Appointment Cancelled Successfully</h3>
        <button class="cancel-ok" onclick="closeCancelSuccessModal()">OK</button>
    </div>
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
        <button id="this-month" type="button">This Month</button>
    <button id="next-month" type="button">Next Month</button>
    <button id="disabled-date" type="button" onclick="openDisabledDateModal()">DISABLED DATE</button>
    <button id="disabled-weekday" type="button" onclick="openDisabledWeekdayModal()">DISABLED WEEKDAY</button>
    </div>

    <!-- Calendar -->
    <div class="calendar-box">
    <div class="calendar-header">
    <h2 id="calendar-title"></h2> <!-- This will display the month and year -->
</div>
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

<!-- Selected Date Modal -->
<div id="selectedDate-modal" class="selectedDate-modal">
    <div class="selectedDate-modal-content">
        <span class="close" onclick="closeSelectedDateModal()">&times;</span>
        <h2>Appointments for Selected Day</h2>
        <table>
            <thead>
                <tr>
                    <th>APPOINTMENT ID</th>
                    <th>TIME</th>
                    <th>CUSTOMER NAME</th>
                    <th>Contact Number</th>
                    <th>STAFF NAME</th>
                    <th>SERVICE</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody id="selectedDate-schedule-body">
                <!-- Appointments will be inserted here dynamically -->
            </tbody>
        </table>
        <!-- Pagination for the selected date modal -->
        <div class="pagination">
    <button id="modalPrevPage">Previous</button>
    <span id="modalPageIndicator">Page 1</span>
    <button id="modalNextPage">Next</button>
</div>
    </div>
</div>

<!-- Disabled Date Modal -->
<div id="disabled-date-modal" class="date-modal-container" style="display: none;">
    <div class="date-modal-content">
        <span class="close" onclick="closeModal('disabled-date-modal')">&times;</span>
        <h2>Select Date to Disable</h2>
        <select id="month-select">
            <!-- Options will be populated by JavaScript -->
        </select>
        <div id="modal-calendar" class="calendar-box">
            <div class="calendar-header">
                <h2 id="modal-calendar-title"></h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Sun</th>
                        <th>Mon</th>
                        <th>Tue</th>
                        <th>Wed</th>
                        <th>Thu</th>
                        <th>Fri</th>
                        <th>Sat</th>
                    </tr>
                </thead>
                <tbody id="modal-calendar-body">
                    <!-- Dates will be injected here by JavaScript -->
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
        <button id="confirm-date" onclick="handleConfirmDate()">Confirm</button>
        <button id="remove-date" type="button" onclick="handleRemoveDate()">Remove</button>
        </div>
    </div>
</div>

<!-- Disabled Weekday Modal -->
<div id="disabled-weekday-modal" class="weekday-modal-container" style="display: none;">
    <div class="weekday-modal-content">
    <span class="close" onclick="closeModal('disabled-weekday-modal')">&times;</span>
        <h2>Select Weekday to Disable</h2>
        <div id="weekday-buttons">
        <button data-weekday="Monday">Monday</button>
            <button data-weekday="Tuesday">Tuesday</button>
            <button data-weekday="Wednesday">Wednesday</button>
            <button data-weekday="Thursday">Thursday</button>
            <button data-weekday="Friday">Friday</button>
            <button data-weekday="Saturday">Saturday</button>
            <button data-weekday="Sunday">Sunday</button>
        </div>
        <div class="modal-footer">
        <button id="confirm-weekday" onclick="handleConfirmWeekday()">Confirm</button>
        </div>
    </div>
</div>


<!-- Confirmation Modal -->
<div id="confirm-date-modal" class="confirmation-modal-container" style="display: none;">
    <div class="confirmation-modal-content">
        <h2>updated Successfully</h2>
        <div class="confirmation-buttons">
        <button id="confirm-yes" onclick="closeModal('confirm-date-modal')">Close</button>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirm-date-modal" class="confirmation-modal-container" style="display: none;">
    <div class="confirmation-modal-content">
        <h2>Updated Successfully</h2>
        <div class="confirmation-buttons">
            <button id="confirm-yes" onclick="closeModal('confirm-date-modal')">Close</button>
        </div>
    </div>
</div>

<!-- Refund Request Modal -->
<div id="refundRequestModal" class="refund-request-modal">
    <div class="refund-request-modal-content">
    <button class="close-modal" onclick="closeRefundRequestModal()">×</button>
        <h2>Refund Requests</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Appointment ID</th>
                        <th>Customer Name</th>
                        <th>Service</th>
                        <th>Amount</th>
                        <th>Request Date</th>
                        <th>Request Time</th>
                        <th>Reason</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="refund-request-body">
                    <!-- Refund requests will be populated here -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Reject Reason Modal -->
<div id="rejectReasonModal" class="reject-reason-modal">
    <div class="reject-reason-modal-content">
        <button class="close-modal" onclick="closeRejectReasonModal()">×</button>
        <h2>Reason for Rejection</h2>
        
        <textarea id="rejectReasonText" placeholder="Enter the reason for rejecting this refund request..."></textarea>
        <div class="modal-buttons">
            <button class="confirm-btn" onclick="confirmReject()">Confirm Rejection</button>
            <button class="cancel-btn" onclick="closeRejectReasonModal()">Cancel</button>
           
        </div>
    </div>
</div>


<script src="../Frontend-Admin/Dashboard.js"></script>
<script src="../Frontend-Admin/Reschedule.js"> </script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
