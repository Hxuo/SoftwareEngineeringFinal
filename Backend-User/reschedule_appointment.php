<?php
// reschedule_appointment.php
session_start();
include 'database.php';

// Check if appointment ID is provided
if (!isset($_GET['appointment_id'])) {
    header("Location: index.php");
    exit();
}

$appointment_id = $_GET['appointment_id'];

// Fetch appointment details
$appointment_query = $conn->prepare("SELECT * FROM Appointment WHERE Appointment_ID = ?");
$appointment_query->bind_param("i", $appointment_id);
$appointment_query->execute();
$appointment = $appointment_query->get_result()->fetch_assoc();

if (!$appointment) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reschedule Appointment</title>
    <link rel="stylesheet" href="../Frontend-Admin/DashboardStaff1.css">
    <style>
        /* Reschedule Modal Styling */
        .modal-resched {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-resched-content {
            background:  #F5E6DA;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            text-align: center;
            width: 400px;
            position: relative;
            animation: fadeIn 0.3s ease-in-out;
        }

        /* Modal Header */
        .modal-resched h3 {
            color:  #262129;
            font-size: 22px;
            margin-bottom: 15px;
        }

        /* Close Button */
        .close-resched {
            position: absolute;
            top: 12px;
            right: 15px;
            background: none;
            border: none;
            font-size: 22px;
            cursor: pointer;
            color: #555;
            transition: 0.3s;
        }

        .close-resched:hover {
            color: #555;
            transform: scale(1.2);
        }

        /* Input Fields */
        .modal-resched input, .modal-resched select {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        /* Confirm Button */
        .confirm-resched {
            margin-top: 15px;
            padding: 10px 18px;
            background-color: #4e342e;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        .confirm-resched:hover {
            background-color: #4e342e;
        }

        /* Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }


        /** CALENDAR MODAL **/
        .modal-calendar-resched {
            display: none; /* Hidden by default */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-calendar-content-resched {
            background-color: #f9f9f9;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            width: 650px;
            max-width: 90%;
            padding: 30px;
            position: relative;
            animation: fadeIn 0.3s ease-in-out;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-size: 1.25rem;
            font-weight: bold;
            color: #333;
        }

        .calendar-header button {
            background-color: #4e342e;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background-color 0.2s ease;
        }

        .calendar-header button:hover {
            background-color: #4e342e;
        }

        .calendar-days-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
            margin-top: 10px;
        }

        /* Optional - Day labels (Sun, Mon, etc.) */
        .calendar-weekdays {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            font-weight: bold;
            margin-bottom: 5px;
            text-align: center;
            color: #555;
        }

        .calendar-day, .blank-day {
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.2s ease-in-out;
        }

        .calendar-day:hover {
            background-color: #ececec;
            transform: scale(1.1);
        }

        .calendar-day.available {
            background-color: #4e342e;
            color: white;
        }

        .calendar-day.booked {
            background-color: #f44336;
            color: white;
        }

        .calendar-day.unavailable {
            background-color: #e0e0e0;
            color: #9e9e9e;
            pointer-events: none; /* Disable clicking for unavailable days */
        }

        /* New - Current Date Highlight */
        .calendar-day.today {
            border: 2px solid #4e342e;
            font-weight: bold;
        }

        /* New - Selected Date */
        .calendar-day.selected {
            background-color: #1976d2;
            color: white;
            transform: scale(1.05);
        }

        /* Legend Section */
        .calendar-legend {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
            font-size: 0.9rem;
        }

        .legend-dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .legend-dot.available {
            background-color: #4caf50;
        }

        .legend-dot.booked {
            background-color: #f44336;
        }

        .legend-dot.unavailable {
            background-color: #e0e0e0;
        }

        /* Confirm Button */
        .confirm-button {
            display: block;
            width: 100%;
            padding: 10px 0;
            background-color: #4e342e;
            color: white;
            font-size: 1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.2s ease-in-out;
        }

        .confirm-button:hover {
            background-color: #4e342e;
        }

        /* Close Button */
        .close-modal {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 1.5rem;
            border: none;
            background: none;
            cursor: pointer;
        }

        /* Success Modal */
        .success-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .success-modal-content {
            background: #F5E6DA;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            text-align: center;
            width: 400px;
            position: relative;
            animation: fadeIn 0.3s ease-in-out;
        }

        .success-modal h3 {
            color: #262129;
            font-size: 22px;
            margin-bottom: 15px;
        }

        .success-confirm {
            margin-top: 15px;
            padding: 10px 18px;
            background-color: #4e342e;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        .success-confirm:hover {
            background-color: #4e342e;
        }
    </style>
</head>
<body>

<!-- Reschedule Modal -->
<div id="resched-modal" class="modal-resched" style="display: flex;">
    <div class="modal-resched-content">
        <h3>Reschedule Appointment</h3>
        <form id="resched-form">
            <input type="hidden" id="appointment_id" name="appointment_id" value="<?= $appointment_id ?>" />
            
            <div class="form-group">
                <label>Appointment ID:</label>
                <input type="text" value="<?= $appointment_id ?>" readonly />
            </div>
            
            <div class="form-group">
                <label>Service:</label>
                <input type="text" value="<?= htmlspecialchars($appointment['Services']) ?>" readonly />
            </div>
            
            <div class="form-group">
                <label>Price:</label>
                <input type="text" value="<?= htmlspecialchars($appointment['Price']) ?>" readonly />
            </div>
            
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
<div id="success-modal" class="success-modal">
    <div class="success-modal-content">
        <h3>Appointment Rescheduled Successfully</h3>
        <p>Your appointment has been updated.</p>
        <button class="success-confirm" onclick="redirectToDashboard()">OK</button>
    </div>
</div>

<script>
    let selectedMonth = new Date().getMonth() + 1;
    let selectedYear = new Date().getFullYear();

    function openNewCalendarModal() {
        document.getElementById("calendarModalNew").style.display = "flex";
        loadCalendar('current');
    }

    function closeNewCalendarModal() {
        document.getElementById("calendarModalNew").style.display = "none";
    }

    function loadCalendar(monthType) {
        if (monthType === 'next') {
            selectedMonth = new Date().getMonth() + 2;
            if (selectedMonth > 12) {
                selectedMonth = 1;
                selectedYear++;
            }
        } else {
            selectedMonth = new Date().getMonth() + 1;
            selectedYear = new Date().getFullYear();
        }

        // Fetch the data from the backend
        fetch(`../Backend-User/fetch_calendar_resched.php?month=${selectedMonth}&year=${selectedYear}`)
            .then(response => response.json())
            .then(data => {
                generateCalendar1(data);
            });
    }

    function generateCalendar1(data) {
    let daysInMonth = new Date(selectedYear, selectedMonth, 0).getDate();
    let firstDay = new Date(selectedYear, selectedMonth - 1, 1).getDay();
    let today = new Date();

    // Month & Year Header
    let monthLabel = new Date(selectedYear, selectedMonth - 1).toLocaleString('en-us', { month: 'long', year: 'numeric' });
    let calendarHTML = `<h3 class='calendar-month-label'>${monthLabel}</h3>`;

    // Weekday Labels
    const weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    calendarHTML += "<div class='calendar-weekdays'>";
    weekdays.forEach(day => calendarHTML += `<div class='weekday-label'>${day}</div>`);
    calendarHTML += "</div>";

    calendarHTML += "<div class='calendar-days-grid'>";

    for (let i = 0; i < firstDay; i++) {
        calendarHTML += "<div class='blank-day'></div>";
    }

    for (let day = 1; day <= daysInMonth; day++) {
        let dateStr = `${selectedYear}-${String(selectedMonth).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        let className = "calendar-day";

        let currentDate = new Date(selectedYear, selectedMonth - 1, day);
        let todayDate = new Date();
        todayDate.setHours(0, 0, 0, 0); // Normalize today's date to midnight for accurate comparison

        if (currentDate < todayDate) {
            className += " past-date";
        } else if (data.closedDates.includes(dateStr)) {
            className += " unavailable";
        } else if (data.disabledWeekdays.includes(currentDate.getDay())) { // Changed this line
            className += " unavailable";
        } else if ((data.bookings[dateStr] || 0) >= 10) {
            className += " booked";
        } else {
            className += " available";
        }

        // Ensure today is clickable
        calendarHTML += `<div class="${className}" onclick="${currentDate >= todayDate ? `selectDate('${dateStr}')` : ''}">${day}</div>`;
    }

    calendarHTML += "</div>";
    document.getElementById("calendarContainer").innerHTML = calendarHTML;
}

    function selectDate(date) {
        document.getElementById("datePickerInput").value = date;
        closeNewCalendarModal();

        // Fetch available time slots based on the selected date
        fetch(`../Backend-User/fetch_timeslots_resched.php?date=${date}`)
            .then(response => response.json())
            .then(slots => populateTimeDropdown(slots))
            .catch(error => console.error('Error fetching time slots:', error));
    }

    function populateTimeDropdown(slots) {
        const timeSelect = document.getElementById('timeSelect');
        timeSelect.innerHTML = '<option value="">Select Time</option>'; // Clear old options

        if (slots.length === 0) {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'No available time slots';
            timeSelect.appendChild(option);
            return;
        }

        slots.forEach(slot => {
            const option = document.createElement('option');
            option.value = slot.value;  // 24-hour format for database
            option.textContent = slot.display;  // 12-hour format for display
            timeSelect.appendChild(option);
        });
    }

    document.getElementById('resched-form').addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent default form submission

        const appointmentId = document.getElementById('appointment_id').value;
        const selectedDate = document.getElementById('datePickerInput').value;
        const selectedTime = document.getElementById('timeSelect').value;

        // Check if both date and time are selected
        if (!selectedDate || !selectedTime) {
            alert("Please select both a date and a time.");
            return;
        }

        // Send AJAX request to update the appointment
        fetch('update_appointment_resched.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                appointment_id: appointmentId,
                new_date: selectedDate,
                new_time: selectedTime
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById("datePickerInput").value = "";
                document.getElementById("timeSelect").innerHTML = '<option value="">Select Time</option>';
                
                // Show success modal
                const successModal = document.getElementById("success-modal");
                if (successModal) successModal.style.display = "flex";
            } else {
                alert('Failed to reschedule appointment: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while rescheduling.');
        });
    });

    function closeSuccessModal() {
        const successModal = document.getElementById("success-modal");
        if (successModal) successModal.style.display = "none";
    }

    function redirectToDashboard() {
        window.location.href = "index.php";
    }

    function confirmNewDate() {
        closeNewCalendarModal();
    }
</script>
</body>
</html>