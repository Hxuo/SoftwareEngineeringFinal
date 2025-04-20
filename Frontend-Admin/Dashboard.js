document.addEventListener("DOMContentLoaded", function () {
    const menuIcon = document.querySelector(".menu-icon");
    const navLinks = document.querySelector(".nav-links");

    if (menuIcon) {
        menuIcon.addEventListener("click", function () {
            navLinks.classList.toggle("active");
        });
    }

    // Ensure modals are hidden on page load
    const doneModal = document.getElementById("doneModal");
    const reschedModal = document.getElementById("resched-modal");
    const successModal = document.getElementById("success-modal");

    if (doneModal) doneModal.style.display = "none";
    if (reschedModal) reschedModal.style.display = "none";
    if (successModal) successModal.style.display = "none";

    

    // Close modals when clicking outside the content
    window.addEventListener("click", function (event) {
        if (event.target === doneModal) closeDoneModal();
        if (event.target === reschedModal) closeReschedModal();
        if (event.target === successModal) closeSuccessModal();
    });
});


// Function to open the Done Modal
function openDoneModal(appointmentId) {
    // Fetch the appointment details
    fetch(`fetch_appointment_details.php?appointment_id=${appointmentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            // Send the data to the server to move to appointment_history
            fetch('complete_appointment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    // Open the modal
                 const doneModal = document.getElementById('doneModal');
                 doneModal.style.display = 'flex';
                 document.querySelector(".ok-button").onclick = function() {
                    closeDoneModal();
                };
                fetchAppointments();
                } else {
                    alert(result.error);
                }
            });
        });
}

function closeDoneModal() {
    const doneModal = document.getElementById('doneModal');
    doneModal.style.display = 'none';
}



// Function to open the Reschedule Modal
function openReschedModal(appointmentId) {
    const reschedModal = document.getElementById("resched-modal");
    if (reschedModal) {
        // Set the appointment ID in the hidden input field
        document.getElementById("appointment_id").value = appointmentId;
        reschedModal.style.display = "flex";
        closeSelectedDateModal(); // Close the selected date modal if open
    }
}

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
    fetch(`../Backend-User/fetch_calendar.php?month=${selectedMonth}&year=${selectedYear}`)
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
} else if (data.disabledWeekdays.includes(currentDate.toLocaleDateString('en-US', { weekday: 'long' }))) {
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
}


//TIME
function selectDate(date) {
    document.getElementById("datePickerInput").value = date;
    closeNewCalendarModal();

    // Fetch available time slots based on the selected date
    fetch(`../Backend-User/fetch_timeslots.php?date=${date}`)
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
    fetch('../Backend-Admin/update_appointment.php', {
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
            // Close the reschedule modal
            closeReschedModal();

            // Show success modal
                const successModal = document.getElementById("success-modal");
                if (successModal) successModal.style.display = "flex";
            

            // Fetch and populate the updated appointments
            fetchAppointments();
        } else {
            alert('Failed to reschedule appointment.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while rescheduling.');
    });
});




// Function to close the Reschedule Modal
function closeReschedModal() {
    const reschedModal = document.getElementById("resched-modal");
    if (reschedModal) {
        // Clear inputs when closing
        document.getElementById("datePickerInput").value = "";
        document.getElementById("timeSelect").innerHTML = '<option value="">Select Time</option>';
        reschedModal.style.display = "none";
    }
}

// Function to close Success Modal
function closeSuccessModal() {
    const successModal = document.getElementById("success-modal");
    if (successModal) successModal.style.display = "none";
}

function redirectToDashboard() {
    closeSuccessModal(); // Close the success modal first
}

// Function to close the Success Modal
function closeSuccessModal() {
    const successModal = document.getElementById("success-modal");
    if (successModal) {
        successModal.style.display = "none";
    }
}

// Open Cancel Confirmation Modal
function openCancelConfirmModal(appointmentId) {
    document.getElementById('cancel-confirm-modal').style.display = "flex";
    document.querySelector(".cancel-yes").onclick = function() {
        confirmCancellation(appointmentId);
        closeCancelConfirmModal();
    };
}

// Close Cancel Confirmation Modal
function closeCancelConfirmModal() {
    document.getElementById('cancel-confirm-modal').style.display = "none";
}

// Confirm Cancellation & Show Success Modal
function confirmCancellation(appointmentId) {
    // Fetch the appointment details
    fetch(`fetch_appointment_details.php?appointment_id=${appointmentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            // Send the data to the server to move to appointment_history
            fetch('cancel_appointment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    // Open the success modal
                    const cancelSuccessModal = document.getElementById('cancel-success-modal');
                    cancelSuccessModal.style.display = 'flex';
                    document.querySelector(".cancel-ok").onclick = function() {
                        closeCancelSuccessModal();
                        //fetchAppointments(); // Refresh the appointments list
                        closeSelectedDateModal(); // Close the selected date modal if open
                    };
                } else {
                    alert(result.error);
                }
            });
        });
}

// Close Cancel Success Modal
function closeCancelSuccessModal() {
    document.getElementById('cancel-success-modal').style.display = "none";

    // Remove from localStorage to prevent it from showing again after refresh
    localStorage.removeItem("cancelSuccess");
}

// Ensure modals are hidden on page load & check localStorage
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById('cancel-confirm-modal').style.display = "none";
    document.getElementById('cancel-success-modal').style.display = "none";

    // If cancellation was confirmed before refresh, do NOT show success modal
    if (localStorage.getItem("cancelSuccess") === "true") {
        document.getElementById('cancel-success-modal').style.display = "none";
    }
});



document.addEventListener("DOMContentLoaded", function () {
    fetchAppointments(); // Fetch appointments for today when the page loads
    fetchCalendar(currentMonthOffset);

    document.getElementById("selectedDate-modal").style.display = "none"; // Hide modal initially

    document.getElementById("this-month").addEventListener("click", function() {
        if (currentMonthOffset !== 0) {
            currentMonthOffset = 0;
            fetchCalendar(currentMonthOffset);
            this.disabled = true;
            document.getElementById("next-month").disabled = false;
        }
    });

    document.getElementById("next-month").addEventListener("click", function() {
        if (currentMonthOffset !== 1) {
            currentMonthOffset = 1;
            fetchCalendar(currentMonthOffset);
            this.disabled = true;
            document.getElementById("this-month").disabled = false;
        }
    });
});

let currentPage = 1;
let currentMainPage = 1; // For main page appointments
let currentModalPage = 1; // For modal appointments
let isModalOpen = false; // To track if we're in modal view
let selectedDate = '';

// Function to fetch appointments with pagination
function fetchAppointments(page = 1) {
    fetch(`../Backend-Admin/fetch_appointments.php?page=${page}`)
        .then(response => response.json())
        .then(data => {
            populateAppointments('schedule-body', data.data, "No appointments for today");
            updatePagination(data.pagination, false);
            currentMainPage = page;
        })
        .catch(error => console.error('Error fetching appointments:', error));
}

// Function to fetch appointments by date with pagination
function fetchAppointmentsByDate(date, page = 1) {
    selectedDate = date; // Store the selected date
    fetch(`../Backend-Admin/fetch_appointments.php?date=${date}&page=${page}`)
        .then(response => response.json())
        .then(data => {
            populateAppointments('selectedDate-schedule-body', data.data, "No appointments for this day");
            updatePagination(data.pagination, true);
            currentModalPage = page;
            openSelectedDateModal(); // Make sure this is the correct function name
        })
        .catch(error => console.error('Error fetching appointments:', error));
}

// Update pagination controls
function updatePagination(pagination, isModal = false) {
    const pageIndicator = isModal ? document.getElementById('modalPageIndicator') : document.getElementById('pageIndicator');
    const prevButton = isModal ? document.getElementById('modalPrevPage') : document.getElementById('prevPage');
    const nextButton = isModal ? document.getElementById('modalNextPage') : document.getElementById('nextPage');

    if (pageIndicator && prevButton && nextButton) {
        pageIndicator.textContent = `Page ${pagination.current_page} of ${pagination.total_pages}`;
        
        // Disable previous button if on first page
        prevButton.disabled = pagination.current_page <= 1;
        
        // Disable next button if on last page
        nextButton.disabled = pagination.current_page >= pagination.total_pages;
    }
}

// Event listeners for main pagination buttons
document.getElementById('prevPage')?.addEventListener('click', () => {
    if (currentMainPage > 1) {
        fetchAppointments(currentMainPage - 1);
    }
});

document.getElementById('nextPage')?.addEventListener('click', () => {
    fetchAppointments(currentMainPage + 1);
});

// Event listeners for modal pagination buttons
document.getElementById('modalPrevPage')?.addEventListener('click', () => {
    if (currentModalPage > 1) {
        fetchAppointmentsByDate(selectedDate, currentModalPage - 1);
    }
});

document.getElementById('modalNextPage')?.addEventListener('click', () => {
    fetchAppointmentsByDate(selectedDate, currentModalPage + 1);
});

// Initial fetch when page loads
document.addEventListener('DOMContentLoaded', () => {
    fetchAppointments(1);
    
    // Set up modal pagination event listeners
    setupModalPagination();
});

// Optional: Separate function to set up modal pagination
function setupModalPagination() {
    const modalPrev = document.getElementById('modalPrevPage');
    const modalNext = document.getElementById('modalNextPage');
    
    if (modalPrev && modalNext) {
        modalPrev.addEventListener('click', () => {
            if (currentModalPage > 1) {
                fetchAppointmentsByDate(selectedDate, currentModalPage - 1);
            }
        });
        
        modalNext.addEventListener('click', () => {
            fetchAppointmentsByDate(selectedDate, currentModalPage + 1);
        });
    }
}

// Make sure your modal open/close functions are correct
function openSelectedDateModal() {
    document.getElementById('selectedDate-modal').style.display = 'flex';
}

function closeSelectedDateModal() {
    document.getElementById('selectedDate-modal').style.display = 'none';
}

// Initial fetch when page loads
document.addEventListener('DOMContentLoaded', () => {
    fetchAppointments(1);
});

// Populate appointment data into the table
function populateAppointments(tableId, data, noDataMessage) {
    const tableBody = document.getElementById(tableId);
    if (!tableBody) {
        console.error(`Error: Element with ID '${tableId}' not found.`);
        return;
    }

    tableBody.innerHTML = ""; // Clear previous content

    if (!data || data.length === 0 || data.error) {
        tableBody.innerHTML = `<tr><td colspan="7">${noDataMessage}</td></tr>`;
        return;
    }

    data.forEach(appointment => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${appointment.Appointment_ID}</td>
            <td>${formatTime(appointment.TIME)}</td>
            <td>${appointment.Name}</td>
            <td>${appointment.PhoneNumber}</td>
            <td>${appointment['Staff_Assigned']}</td>
            <td>${appointment.Services}</td>
            <td>
                <button class="done" title="Mark as Done" onclick="openDoneModal(${appointment.Appointment_ID})">
                    <img src="../Assests/don.png" alt="Done">
                </button>
                <button class="resched" title="Reschedule Appointment" onclick="openReschedModal(${appointment.Appointment_ID})">
                    <img src="../Assests/reschedicon.png" alt="Reschedule">
                </button>
                <button class="cancel" title="Cancel Appointment" onclick="openCancelConfirmModal(${appointment.Appointment_ID})">
                    <img src="../Assests/cancelicon.png" alt="Cancelled">
                </button>
            </td>
        `;
        tableBody.appendChild(row);
    });
}

// Convert time format (24-hour to 12-hour)
function formatTime(time) {
    if (!time) return "Invalid Time";
    const [hours, minutes] = time.split(':').map(Number);
    const ampm = hours >= 12 ? 'PM' : 'AM';
    const hour = hours % 12 || 12;
    return `${hour}:${String(minutes).padStart(2, '0')} ${ampm}`;
}

// Fetch and generate the calendar
function fetchCalendar(monthOffset) {
    const today = new Date();
    today.setMonth(today.getMonth() + monthOffset);
    const year = today.getFullYear();
    const month = today.getMonth() + 1; // Convert to 1-based month

    // Update the month and year title
    document.getElementById("calendar-title").textContent = `${getMonthName(month)} ${year}`;

    fetch(`../Backend-Admin/fetch_calendar.php?year=${year}&month=${month}`)
        .then(response => response.json())
        .then(data => {
            generateCalendar(year, month, data.disabledDays || [], data.closedDates || []);
        })
        .catch(error => console.error("Error fetching calendar:", error));
}

document.addEventListener("DOMContentLoaded", function() {
    fetchCalendar(0);
    document.getElementById("this-month").disabled = true; // Disable "This Month" initially
})

// Generate the calendar dynamically
function generateCalendar(year, month, disabledDays, closedDates) {
    const firstDay = new Date(year, month - 1, 1).getDay();
    const daysInMonth = new Date(year, month, 0).getDate();
    const calendarBody = document.getElementById("calendar-body");
    calendarBody.innerHTML = "";

    let row = document.createElement("tr");
    for (let i = 0; i < firstDay; i++) {
        row.appendChild(document.createElement("td"));
    }

    for (let day = 1; day <= daysInMonth; day++) {
        const cell = document.createElement("td");
        const dateStr = `${year}-${String(month).padStart(2, "0")}-${String(day).padStart(2, "0")}`;

        if (disabledDays.includes(new Date(dateStr).getDay()) || closedDates.includes(dateStr)) {
            cell.classList.add("blocked");
        } else {
            cell.classList.add("available");
            cell.onclick = () => {
                console.log("Selected Date:", dateStr); // Debugging line
                fetchAppointmentsByDate(dateStr); // Fetch appointments for the selected date
            };
        }

        cell.textContent = day;
        row.appendChild(cell);

        if ((firstDay + day) % 7 === 0) {
            calendarBody.appendChild(row);
            row = document.createElement("tr");
        }
    }
    calendarBody.appendChild(row);
}

function getMonthName(month) {
    const monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    return monthNames[month - 1];
}



// Modal handling functions
function openScheduleModal() {
    console.log("Opening Modal"); // Debugging line
    document.getElementById("selectedDate-modal").style.display = "flex";
}

function closeSelectedDateModal() {
    document.getElementById("selectedDate-modal").style.display = "none";
}

// Track the current month offset
let currentMonthOffset = 0;


//Disable
let selectedDates = [];

function openDisabledDateModal() {
    const modal = document.getElementById('disabled-date-modal');
    modal.style.display = 'flex';
    populateMonthDropdown();
    fetchDisabledDates();
}

// Function to open a modal
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.style.display = 'flex';
}

// Function to close a modal
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.style.display = 'none';
}

function populateMonthDropdown() {
    const monthSelect = document.getElementById('month-select');
    const today = new Date();
    const currentMonth = today.getMonth(); // 0-based month
    const currentYear = today.getFullYear();

    monthSelect.innerHTML = ''; // Clear existing options

    // Loop from the current month to December
    for (let i = currentMonth; i < 12; i++) {
        const date = new Date(currentYear, i, 1);
        const option = document.createElement('option');
        option.value = i; // Use 0-based month value
        option.textContent = getMonthName(i + 1); // Convert to 1-based month for display
        if (i === currentMonth) {
            option.selected = true; // Set the current month as the default selected option
        }
        monthSelect.appendChild(option);
    }

    // Update the modal calendar when the dropdown changes
    monthSelect.onchange = () => {
        const selectedMonth = parseInt(monthSelect.value);
        generateModalCalendar(selectedMonth, currentYear);
    };

    // Generate the modal calendar for the current month
    generateModalCalendar(currentMonth, currentYear);
}

function getMonthName(month) {
    const monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    return monthNames[month - 1]; // Convert 1-based month to 0-based index
}


function generateModalCalendar(month, year) {
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const modalCalendarBody = document.getElementById('modal-calendar-body');
    modalCalendarBody.innerHTML = '';

    let row = document.createElement('tr');
    for (let i = 0; i < firstDay; i++) {
        row.appendChild(document.createElement('td'));
    }

    for (let day = 1; day <= daysInMonth; day++) {
        const cell = document.createElement('td');
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

        cell.textContent = day;
        cell.dataset.date = dateStr;

        if (selectedDates.includes(dateStr)) {
            cell.classList.add('selected');
        }

        cell.onclick = () => {
            if (cell.classList.contains('selected')) {
                cell.classList.remove('selected');
                selectedDates = selectedDates.filter(date => date !== dateStr);
            } else {
                cell.classList.add('selected');
                selectedDates.push(dateStr);
            }
            console.log("Selected Dates after click:", selectedDates); // Debugging
        };

        row.appendChild(cell);

        if ((firstDay + day) % 7 === 0) {
            modalCalendarBody.appendChild(row);
            row = document.createElement('tr');
        }
    }
    modalCalendarBody.appendChild(row);
}

function handleConfirmDate() {
    if (selectedDates.length === 0) {
        alert('Please select at least one date.');
        return;
    }

    fetch('../Backend-Admin/update_disabled_dates.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ dates: selectedDates })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            openModal('confirm-date-modal'); // Open the success modal
            selectedDates = []; // Clear selected dates
            fetchCalendar(currentMonthOffset); // Refresh the calendar
        } else {
            alert('Failed to update disabled dates.');
        }
    })
    .catch(error => console.error('Error:', error));
}


function handleRemoveDate() {

    if (selectedDates.length === 0) {
        alert('Please select at least one date to remove.');
        return;
    }

    fetch('../Backend-Admin/removed_disabled_dates.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ dates: selectedDates })
    })
    .then(response => response.json())
    .then(data => {
        console.log("Response from backend:", data); // Debugging
        if (data.success) {
            openModal('confirm-date-modal'); // Open the success modal
            selectedDates = []; // Clear selected dates
            fetchCalendar(currentMonthOffset); // Refresh the calendar
        } else {
            alert('Failed to remove disabled dates.');
        }
    })
    .catch(error => console.error('Error:', error));
}



function fetchDisabledDates() {
    fetch('../Backend-Admin/fetch_disabled_dates.php')
    .then(response => response.json())
    .then(data => {
        selectedDates = data.dates;
        const monthSelect = document.getElementById('month-select');
        const selectedMonth = parseInt(monthSelect.value);
        const today = new Date();
        generateModalCalendar(selectedMonth, today.getFullYear());
    })
    .catch(error => console.error('Error:', error));
}

//Weekdays
let selectedWeekdays = []; // Array to store selected weekdays

// Function to open the Disabled Weekday Modal
function openDisabledWeekdayModal() {
    console.log("Disabled Weekday button clicked"); // Debugging
    openModal('disabled-weekday-modal'); // Open the modal
    fetchDisabledWeekdays(); // Fetch currently disabled weekdays
}

// Function to fetch currently disabled weekdays
function fetchDisabledWeekdays() {
    fetch('../Backend-Admin/fetch_disabled_weekdays.php')
        .then(response => response.json())
        .then(data => {
            console.log("Fetched Disabled Weekdays:", data.weekdays); // Debugging
            selectedWeekdays = data.weekdays; // Update the selectedWeekdays array
            highlightSelectedWeekdays(); // Highlight the selected weekdays in the modal
        })
        .catch(error => console.error('Error fetching disabled weekdays:', error));
}

// Function to highlight selected weekdays in the modal
function highlightSelectedWeekdays() {
    const weekdayButtons = document.querySelectorAll('#weekday-buttons button');
    weekdayButtons.forEach(button => {
        const weekday = button.getAttribute('data-weekday');
        if (selectedWeekdays.includes(weekday)) {
            button.classList.add('active'); // Highlight the button if the weekday is selected
        } else {
            button.classList.remove('active'); // Remove highlight if the weekday is not selected
        }
    });
}

// Function to handle weekday selection
function setupWeekdayButtons() {
    const weekdayButtons = document.querySelectorAll('#weekday-buttons button');
    weekdayButtons.forEach(button => {
        button.onclick = () => {
            const weekday = button.getAttribute('data-weekday');
            if (selectedWeekdays.includes(weekday)) {
                // If already selected, remove it
                selectedWeekdays = selectedWeekdays.filter(w => w !== weekday);
                button.classList.remove('active');
            } else {
                // If not selected, add it
                selectedWeekdays.push(weekday);
                button.classList.add('active');
            }
            console.log("Selected Weekdays:", selectedWeekdays); // Debugging
        };
    });
}

// Function to handle the Confirm button click
function handleConfirmWeekday() {
    console.log("Confirm Weekday button clicked"); // Debugging
    console.log("Selected Weekdays to Disable:", selectedWeekdays); // Debugging

    if (selectedWeekdays.length === 0) {
        alert('Please select at least one weekday to disable.');
        return;
    }

    fetch('../Backend-Admin/update_disabled_weekdays.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ weekdays: selectedWeekdays })
    })
    .then(response => response.json())
    .then(data => {
        console.log("Response from backend:", data); // Debugging
        if (data.success) {
            openModal('confirm-date-modal'); // Open the success modal
            selectedWeekdays = []; // Clear selected weekdays
            fetchCalendar(currentMonthOffset); // Refresh the calendar
        } else {
            alert('Failed to update disabled weekdays.');
        }
    })
    .catch(error => console.error('Error:', error));
}

// Initialize weekday buttons when the page loads
document.addEventListener('DOMContentLoaded', () => {
    setupWeekdayButtons();
});


//Refund request
let currentRefundRequestId = null;

// Open refund request modal
function openRefundRequestModal() {
    fetchRefundRequests();
    document.getElementById('refundRequestModal').style.display = 'flex';
}

// Close refund request modal
function closeRefundRequestModal() {
    document.getElementById('refundRequestModal').style.display = 'none';
}

// Fetch refund requests from server with proper date/time formatting
function fetchRefundRequests() {
    fetch('../Backend-Admin/fetch_refund_requests.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Received data:', data); // Debug log
            
            const tableBody = document.getElementById('refund-request-body');
            tableBody.innerHTML = '';
            
            if (!data.success) {
                tableBody.innerHTML = `<tr><td colspan="8">${data.error || 'Error fetching requests'}</td></tr>`;
                return;
            }
            
            if (!data.data || data.data.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="8">No pending refund requests found</td></tr>`;
                return;
            }
            
            data.data.forEach(request => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${request.Appointment_ID}</td>
                    <td>${request.Name}</td>
                    <td>${request.Services}</td>
                    <td>₱${number_format(request.Price, 2)}</td>
                    <td>${formatDateYMD(request.Refund_Date)}</td>
                    <td>${formatTime12hr(request.Refund_Time)}</td>
                    <td>${request.Refund_Reason || 'No reason provided'}</td>
                    <td>
                        <button class="approve-btn" onclick="approveRefund(${request.Appointment_ID})">Approve</button>
                        <button class="reject-btn" onclick="openRejectReasonModal(${request.Appointment_ID})">Reject</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Error fetching refund requests:', error);
            document.getElementById('refund-request-body').innerHTML = `
                <tr><td colspan="8">Error loading refund requests: ${error.message}</td></tr>
            `;
        });
}

// Format date as YYYY-MM-DD
function formatDateYMD(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    if (isNaN(date)) return dateString; // Return original if invalid date
    
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    
    return `${year}-${month}-${day}`;
}

// Format time as 12-hour format with AM/PM
function formatTime12hr(timeString) {
    if (!timeString) return 'N/A';
    
    // Handle cases where time might be in format "HH:MM:SS" or "HH:MM"
    const timeParts = timeString.split(':');
    if (timeParts.length < 2) return timeString; // Return original if format is unexpected
    
    let hours = parseInt(timeParts[0]);
    const minutes = timeParts[1];
    const ampm = hours >= 12 ? 'PM' : 'AM';
    
    // Convert to 12-hour format
    hours = hours % 12;
    hours = hours ? hours : 12; // The hour '0' should be '12'
    
    return `${hours}:${minutes} ${ampm}`;
}

// Helper function for number formatting
function number_format(number, decimals) {
    return parseFloat(number).toFixed(decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

// Open reject reason modal
function openRejectReasonModal(appointmentId) {
    currentRefundRequestId = appointmentId;
    document.getElementById('rejectReasonText').value = '';
    document.getElementById('rejectReasonModal').style.display = 'flex';
}

// Close reject reason modal
function closeRejectReasonModal() {
    currentRefundRequestId = null;
    document.getElementById('rejectReasonModal').style.display = 'none';
}

// Approve refund request
function approveRefund(appointmentId) {
    if (!confirm('Are you sure you want to approve this refund request?')) return;
    
    fetch('../Backend-Admin/process_refund_approval.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            appointmentId: appointmentId,
            action: 'approve'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Refund approved successfully!');
            fetchRefundRequests();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while approving the refund.');
    });
}

// Confirm reject with reason
function confirmReject() {
    const reason = document.getElementById('rejectReasonText').value.trim();
    
    if (!reason) {
        alert('Please enter a reason for rejection.');
        return;
    }
    
    fetch('../Backend-Admin/process_refund_approval.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            appointmentId: currentRefundRequestId,
            action: 'reject',
            reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Refund rejected successfully!');
            closeRejectReasonModal();
            fetchRefundRequests();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while rejecting the refund.');
    });
}

//Pagination
