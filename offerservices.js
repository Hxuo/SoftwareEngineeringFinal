document.addEventListener("DOMContentLoaded", function () {
    let cities = {};
    let regions = {};
  
    const regionInput = document.getElementById("createregion");
    const cityInput = document.getElementById("createcity");
  
    // Load JSON files
    Promise.all([
        fetch("./JSON/regions.json").then(res => res.json()).then(data => regions = data)
    ]).then(() => {
        console.log("✅ Data loaded successfully");
        populateRegions();  // Populate the regions dropdown
    }).catch(error => console.error("❌ Error loading data:", error));
  
    // Populate Region Dropdown
    function populateRegions() {
        for (const region in regions) {
            let option = document.createElement("option");
            option.value = region;
            option.textContent = region;
            regionInput.appendChild(option);
        }
    }
  
    // Load cities based on the selected region
    function loadCities() {
        const selectedRegion = regionInput.value;
        const cityList = regions[selectedRegion] || [];
  
        // Reset and enable the city dropdown
        cityInput.innerHTML = '<option value="" disabled selected>Select City</option>';  // Reset cities
        cityInput.disabled = false;  // Enable city dropdown
  
        // If there are no cities for the region, disable the city dropdown again
        if (cityList.length === 0) {
            cityInput.disabled = true;
            return;
        }
  
        // Populate the city dropdown with cities for the selected region
        cityList.forEach(city => {
            let option = document.createElement("option");
            option.value = city;
            option.textContent = city;
            cityInput.appendChild(option);
        });
    }
  
    // Add event listener to the region dropdown to load cities when a region is selected
    regionInput.addEventListener("change", loadCities);
  });
  
  
  
  
  //==============================================================================================
  
  //Edit Account to
  document.addEventListener("DOMContentLoaded", function () {
    let cities = {};
    let regions = {};
  
    const cityInput = document.getElementById("editcity");
    const cityList = document.getElementById("edit-city-list");
    const barangayInput = document.getElementById("editbarangay");
    const regionInput = document.getElementById("editregion");
  
    // Set region input to read-only
    regionInput.readOnly = true;
  
    // Load JSON files
    Promise.all([
        fetch("./JSON/cities.json").then(res => res.json()).then(data => cities = data),
        fetch("./JSON/regions.json").then(res => res.json()).then(data => regions = data)
    ]).then(() => {
        console.log("✅ Data loaded successfully");
        initializeLiveSearch();
    }).catch(error => console.error("❌ Error loading data:", error));
  
    function initializeLiveSearch() {
        cityInput.addEventListener("input", function () {
            let query = cityInput.value.toLowerCase().trim();
            cityList.innerHTML = "";
  
            if (query.length === 0) {
                cityList.style.display = "none";
                return;
            }
  
            let matchingCities = [];
            for (const province in cities) {
                matchingCities.push(...cities[province].filter(city => city.toLowerCase().includes(query)));
            }
  
            matchingCities = [...new Set(matchingCities)].slice(0, 10); // Remove duplicates and limit results
  
            if (matchingCities.length === 0) {
                cityList.style.display = "none";
                return;
            }
  
            cityList.style.display = "block";
            matchingCities.forEach(city => {
                let listItem = document.createElement("li");
                listItem.textContent = city;
                listItem.addEventListener("click", function () {
                    cityInput.value = city;
                    cityList.style.display = "none";
  
                    // Find the corresponding region and autofill it
                    for (const region in regions) {
                        if (regions[region].includes(city)) {
                            regionInput.value = region;
                            break;
                        }
                    }
                });
                cityList.appendChild(listItem);
            });
        });
  
        document.addEventListener("click", function (e) {
            if (!cityInput.contains(e.target) && !cityList.contains(e.target)) {
                cityList.style.display = "none";
            }
        });
    }
  });
  
  // ===============================================================================
  

// Function to open the Gluta Modal
function openGlutaModal() {
    const modal = document.getElementById('glutaModal');
    modal.classList.add('show'); // Add 'show' class to make it visible

    // Fetch services from database via PHP
    fetch('fetch_packages.php')
        .then(response => response.json())
        .then(data => {
            displayServicesInModal(data);
        })
        .catch(error => {
            console.error('Error fetching services:', error);
        });
}

// Function to display services inside modal grid
function displayServicesInModal(services) {
    const modalGrid = document.querySelector('#glutaModal .modal-grid');
    modalGrid.innerHTML = ''; // Clear existing content

    services.forEach(service => {
        const serviceCard = document.createElement('div');
        serviceCard.classList.add('modal-card');

        serviceCard.innerHTML = `
            <img src="./Items-Images/${service.Items_Image}" alt="${service.Items_Name}" class="modal-card-img">
            <h3 class="modal-card-title">${service.Items_Name}</h3>
            <p class="modal-card-price">P ${service.Items_Price}</p>
            <button 
                class="modal-card-btn" 
                onclick="showConfirmationDialog('${service.Items_Name}', '${service.Items_Price}')">
                SELECT SERVICE
            </button>
        `;

        modalGrid.appendChild(serviceCard);
    });
}

// Function to close the Modal
function closeModal() {
    const modal = document.getElementById('glutaModal');
    modal.classList.remove('show'); // Remove 'show' class to hide it
}

// Add event listener to close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('glutaModal');
    if (event.target === modal) {
        closeModal();
    }
  });

// Function to open the Promo Modal
function openPromoModal() {
    const modal = document.getElementById('glutaModal');
    modal.classList.add('show'); // Add 'show' class to make it visible

    // Fetch services from database via PHP
    fetch('fetch_promos.php')
        .then(response => response.json())
        .then(data => {
            displayServicesInModal(data);
        })
        .catch(error => {
            console.error('Error fetching services:', error);
        });
}
// Function to display services inside Promo modal grid
function displayServicesInModal(services) {
    const modalGrid = document.querySelector('.modal-grid');
    modalGrid.innerHTML = ''; // Clear existing content

    services.forEach(service => {
        const serviceCard = document.createElement('div');
        serviceCard.classList.add('modal-card');

        serviceCard.innerHTML = `
            <img src="./Items-Images/${service.Items_Image}" alt="${service.Items_Name}" class="modal-card-img">
            <h3 class="modal-card-title">${service.Items_Name}</h3>
            <p class="modal-card-price">P ${service.Items_Price}</p>
            <button 
                class="modal-card-btn" 
                onclick="showConfirmationDialog('${service.Items_Name}', '${service.Items_Price}')">
                SELECT SERVICE
            </button>
        `;

        modalGrid.appendChild(serviceCard);
    });
}

function showConfirmationDialog(serviceName, price) {
    if (!isLoggedIn) {
        openUserModal(); // Open the login modal if not logged in
        return; // Exit the function to prevent further execution
    }

    // Store the service details temporarily for later use
    window.pendingService = { serviceName, price };

    // Create and show the confirmation modal
    const confirmationModal = document.getElementById('confirmationModal');
    document.getElementById('confirmServiceName').textContent = serviceName;
    document.getElementById('confirmServicePrice').textContent = `P ${price}`;
    confirmationModal.classList.add('show');
}

function cancelAddToCart() {
    const modal = document.getElementById('confirmationModal');
    modal.classList.remove('show'); // Remove 'show' class to hide it
}

function confirmAddToCart() {
    const { serviceName, price } = window.pendingService;
    const numericPrice = parseFloat(price) || 0;

    // Check if the user has already selected 3 services
    if (service.length >= 3) {
        document.getElementById('limitModal').classList.add('show');
        document.getElementById('confirmationModal').classList.remove('show');
        return;
    }

    // Check if the service is already in the list
    const isDuplicate = service.some(s => s.serviceName === serviceName);
    if (isDuplicate) {
        document.getElementById('duplicateServiceModal').classList.add('show');
        document.getElementById('confirmationModal').classList.remove('show');
        return;
    }

    // Add the service to the list
    service.push({ serviceName, price: numericPrice });

    cartCount++; 
    updateCartCount();

    // Hide gluta modal temporarily
    const glutaModal = document.getElementById('glutaModal');
    glutaModal.classList.remove('show');

    // Show the success modal
    const successModal = document.getElementById('successModal');
    successModal.style.display = 'flex';

    // After timeout, show gluta modal again if needed
    setTimeout(() => {
        successModal.style.display = 'none';
    }, 2000);

    // Update order summary
    updateOrderSummary();

    // Close the confirmation modal
    document.getElementById('confirmationModal').classList.remove('show');
}


// REMOVE BUTTON SECTION ------------------------------
let services = []; // Array to store added services

function openOrderSummary(serviceName, price) {
    // Add the service to the list
    services.push({ serviceName, price });
    

    // Populate the services table
    const tableBody = document.getElementById('servicesTableBody');
    tableBody.innerHTML = ''; 
    let total = 0;

    services.forEach((service, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${service.serviceName}</td>
            <td>₱${parseFloat(service.price).toLocaleString()}</td>
            <td><button class="remove-btn" onclick="removeService(${index})">
             <img src="./Assests/cancelicon.png" alt="Cancelled" width="50">
            </button></td>
        `;
        tableBody.appendChild(row);
        total += parseFloat(service.price);
    });

    // Update total price
    document.getElementById('totalPrice').innerText = `₱${total.toLocaleString()}`;
    document.getElementById('paymentTotalPrice').innerText = `₱${total.toLocaleString()}`;

    // Show the date & time if available
    const dateInput = document.getElementById("datePickerInput").value;
    const timeSelect = document.getElementById("timeSelect").value;

    document.getElementById('order-date').innerText = dateInput || 'N/A';
    document.getElementById('order-time').innerText = timeSelect || 'N/A';

    // Show the modal
    const modal = document.getElementById('orderSummaryModal');
    modal.classList.add('show');

}


// REMOVE BUTTON SECTION ------------------------------
function closeOrderSummary() {
    const modal = document.getElementById('orderSummaryModal');
    modal.classList.remove('show');
}

function removeService(index) {
    // Remove the service from the array
    services.splice(index, 1);

    cartCount++; 
    updateCartCount(); // Update the cart count in the UI

    // Refresh the modal
    openOrderSummary('', '');
}
// REMOVE BUTTON SECTION ------------------------------


// Function to open the Proceed Payment Modal
function openProceedPaymentModal() {
    console.log("Proceed Payment button clicked");

    const servicesTableBody = document.getElementById("servicesTableBody");

    // Check if the table has any rows
    if (!servicesTableBody || servicesTableBody.children.length === 0) {
        console.warn("No items selected, showing warning modal");
        document.getElementById("warningModal").classList.add("show");
        return;
    }

    // Check if user details are complete
    if (!isAccountComplete(userData)) {
        console.warn("Incomplete account information, opening edit account modal");
        document.getElementById("editmodal").style.display = "flex";
        return;
    }

    // Prepare selected services
    const selectedServices = services.map(service => ({
        name: service.serviceName,
        price: service.price
    }));

    // Store services in sessionStorage (so PHP can access them)
    sessionStorage.setItem("selectedServices", JSON.stringify(selectedServices));

    // Proceed to checkout
    const modal = document.getElementById('proceedPaymentModal');
    if (modal) {
        modal.classList.add('show');
        console.log("Proceed Payment Modal is visible");
    } else {
        console.error("Modal with id 'proceedPaymentModal' not found");
    }
}



// Function to check if user account is complete
function isAccountComplete(userData) {
    return userData.address && userData.barangay && userData.city && 
           userData.region && userData.phonenumber;
}


// Function to close the warning modal
function closeWarningModal() {
    document.getElementById("warningModal").classList.remove("show");
}


function closeProceedPaymentModal() {
    console.log("Close button clicked"); // Debug log
    const modal = document.getElementById('proceedPaymentModal');
    if (modal) {
        modal.classList.remove('show'); // Hide the modal
        console.log("Proceed Payment Modal is hidden"); // Debug log
    } else {
        console.error("Modal with id 'proceedPaymentModal' not found"); // Error log
    }
   
}

//Pang open ng Cart 
function openCartModal() {
    if (!isLoggedIn) {
        openUserModal(); // Open the login modal if not logged in
        return; // Exit the function to prevent further execution
    }

    // Show the order summary modal if logged in
    document.getElementById('orderSummaryModal').classList.add('show');
}

function closeCartModal() {
    document.getElementById('orderSummaryModal').classList.remove('show');
}

document.addEventListener('click', function(event) {
    const modal = document.getElementById('orderSummaryModal');
    if (event.target === modal) {
        closeCartModal();
    }
  });

let service = []; // Array to store added services

let serviceToRemoveIndex = null; // Index of the service to be removed

// Para to sa Filter
document.addEventListener("DOMContentLoaded", function () {
    const categories = document.querySelectorAll(".category");
    const treatmentGrid = document.querySelector(".treatment-grid");

    categories.forEach(category => {
        category.addEventListener("click", function (event) {
            event.preventDefault();

            // Remove active class from all categories
            categories.forEach(cat => cat.classList.remove("active"));
            this.classList.add("active");

            // Get category name or set to "all" if it's the "All Services" button
            const selectedCategory = this.getAttribute("data-category") || "all";

            // Fetch services from PHP script
            fetch(`fetch_services.php?category=${selectedCategory}`)
                .then(response => response.json())
                .then(data => {
                    // Clear the current grid
                    treatmentGrid.innerHTML = "";

                    if (!data || data.length === 0 || data.error) {
                        treatmentGrid.innerHTML = "<p>No services available.</p>";
                        return;
                    }

                    // Loop through services and create cards dynamically
                    data.forEach(service => {
                        const serviceCard = `
                            <div class="treatment-card">
                                <img src="./Items-Images/${service.Service_Image}" alt="${service.Service_Name}" class="treatment-img">
                                <h3 class="treatment-title">${service.Service_Name} - ₱${service.Service_Price}</h3>
                                <button class="select-service-btn" 
                                    onclick="showConfirmationDialog('${service.Service_Name}', ${service.Service_Price})">
                                    SELECT SERVICE
                                </button>
                            </div>
                        `;
                        treatmentGrid.innerHTML += serviceCard;
                    });
                })
                .catch(error => console.error("Error fetching services:", error));
        });
    });
});


function openOrderSummary(serviceName, price) {
    if (!isLoggedIn) {
        openUserModal(); // Open the login modal if not logged in
        return; // Exit the function to prevent further execution
    }

    // Ensure price is a valid number
    const numericPrice = parseFloat(price) || 0;

    // Check if the user has already selected 3 services
    if (service.length >= 3) {
        document.getElementById('limitModal').classList.add('show');
        return;
    }

    // Check if the service is already in the list
    const isDuplicate = service.some(s => s.serviceName === serviceName);
    if (isDuplicate) {
        document.getElementById('duplicateServiceModal').classList.add('show');
        return;
    }

    // Add the service to the list
    service.push({ serviceName, price: numericPrice });

    cartCount++; 
    updateCartCount(); // Update the cart count in the UI

    // Show the success modal
    const successModal = document.getElementById('successModal');
    successModal.style.display = 'flex';

    // Close the success modal after 2 seconds
    setTimeout(() => {
        successModal.style.display = 'none';
    }, 2000);

    // Populate the services table
    const tableBody = document.getElementById('servicesTableBody');
    tableBody.innerHTML = ''; // Clear existing rows
    let total = 0;

    service.forEach((s, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${s.serviceName}</td>
            <td>P ${s.price.toLocaleString()}</td>
            <td>
                <button class="remove-btn" onclick="openRemoveConfirmation(${index})">
                    <img src="./Assests/trash.png" alt="Cancelled" width="20">
                </button>
            </td>
        `;
        tableBody.appendChild(row);

        // Calculate total
        total += s.price;
    });

    // Update total price
    document.getElementById('totalPrice').innerText = `P ${total.toLocaleString()}`;
    document.getElementById('paymentTotalPrice').innerText = `P ${total.toLocaleString()}`;

    // Show the order summary modal (if needed)
    // document.getElementById('orderSummaryModal').classList.add('show');
}

function closeOrderSummary() {
    document.getElementById('orderSummaryModal').classList.remove('show');
}

function openOrderSummary(serviceName, price) {
    if (!isLoggedIn) {
        openUserModal(); // Open the login modal if not logged in
        return; // Exit the function to prevent further execution
    }

    // Ensure price is a valid number
    const numericPrice = parseFloat(price) || 0;

    // Check if the user has already selected 3 services
    if (service.length >= 3) {
        document.getElementById('limitModal').classList.add('show');
        return;
    }

    // Check if the service is already in the list
    const isDuplicate = service.some(s => s.serviceName === serviceName);
    if (isDuplicate) {
        document.getElementById('duplicateServiceModal').classList.add('show');
        return;
    }

    // Add the service to the list
    service.push({ serviceName, price: numericPrice });

    cartCount++; 
    updateCartCount(); // Update the cart count in the UI

    // Show the success modal
    const successModal = document.getElementById('successModal');
    successModal.style.display = 'flex';

    // Close the success modal after 2 seconds
    setTimeout(() => {
        successModal.style.display = 'none';
    }, 2000);

    // Populate the services table
    const tableBody = document.getElementById('servicesTableBody');
    tableBody.innerHTML = ''; // Clear existing rows
    let total = 0;

    service.forEach((s, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${s.serviceName}</td>
            <td>P ${s.price.toLocaleString()}</td>
            <td>
                <button class="remove-btn" onclick="openRemoveConfirmation(${index})">
                    <img src="./Assests/trash.png" alt="Cancelled" width="20">
                </button>
            </td>
        `;
        tableBody.appendChild(row);

        // Calculate total
        total += s.price;
    });

    // Update total price
    document.getElementById('totalPrice').innerText = `P ${total.toLocaleString()}`;
    document.getElementById('paymentTotalPrice').innerText = `P ${total.toLocaleString()}`;

    // Show the order summary modal (if needed)
     //document.getElementById('orderSummaryModal').classList.add('show');
}


function closeLimitModal() {
    document.getElementById('limitModal').classList.remove('show');
}

function closeDuplicateServiceModal() {
    document.getElementById('duplicateServiceModal').classList.remove('show');
}


function closeOrderSummary() {
    const modal = document.getElementById('orderSummaryModal');
    modal.classList.remove('show');
}

function openRemoveConfirmation(index) {
    console.log("Opening confirmation modal for service at index:", index);
    serviceToRemoveIndex = index; // Store the index of the service to be removed
    const modal = document.getElementById('removeConfirmationModal');
    modal.classList.add('show'); // Show the confirmation modal
}

function closeRemoveConfirmation() {
    console.log("Closing confirmation modal...");
    serviceToRemoveIndex = null; // Reset the index
    const modal = document.getElementById('removeConfirmationModal');
    modal.classList.remove('show'); // Hide the confirmation modal
}

function confirmRemoveItem() {
    if (serviceToRemoveIndex !== null) {
        console.log("Removing service at index:", serviceToRemoveIndex);
        
        // Remove the selected service from the array
        service.splice(serviceToRemoveIndex, 1);
        serviceToRemoveIndex = null; // Reset the index

        // Decrement cart count
        cartCount--; 
        updateCartCount(); // Update the cart count in the UI

        // Refresh the order summary
        updateOrderSummary();
    }

    // Close the confirmation modal
    closeRemoveConfirmation();

    // Show the success modal
    const successModal = document.getElementById('removeSuccessModal');
    successModal.classList.add('show'); // Show the success modal
}


function closeSuccessModal() {
    const successModal = document.getElementById('removeSuccessModal');
    if (successModal) {
        successModal.classList.remove('show'); // Hide the success modal
    } else {
        console.error("Success modal not found.");
    }
}


function updateOrderSummary() {
    const tableBody = document.getElementById('servicesTableBody');
    tableBody.innerHTML = ''; // Clear existing rows
    let total = 0;

    service.forEach((s, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${s.serviceName}</td>
            <td>P ${s.price.toLocaleString()}</td>
            <td>
                <button class="remove-btn" onclick="openRemoveConfirmation(${index})">
                    <img src="./Assests/trash.png" alt="Cancelled" width="20">
                </button>
            </td>
        `;
        tableBody.appendChild(row);

        // Calculate total
        total += s.price;
    });

    // Update total price
    document.getElementById('totalPrice').innerText = `P ${total.toLocaleString()}`;
    document.getElementById('paymentTotalPrice').innerText = `P ${total.toLocaleString()}`;
}


//Sa time to
function updateTimeSlots() {
    const selectedDate = document.getElementById("datePickerInput").value;
    if (!selectedDate) return;  // Skip if no date selected yet

    const slotDuration = (service.length >= 2) ? 2 : 1;
    fetch(`fetch_timeslots.php?date=${selectedDate}&duration=${slotDuration}`)
        .then(response => response.json())
        .then(slots => populateTimeDropdown(slots))
        .catch(error => console.error('Error fetching time slots:', error));
}


//Date and Time
function updateDateTime() {
    const now = new Date();
    
    // Format Date (MM/DD/YYYY)
    const date = now.toLocaleDateString("en-US", { 
        month: "2-digit", day: "2-digit", year: "numeric" 
    });

    // Format Time (hh:mm AM/PM)
    const time = now.toLocaleTimeString("en-US", { 
        hour: "2-digit", minute: "2-digit", second: "2-digit", hour12: true 
    });

    document.getElementById("order-date").textContent = date;
    document.getElementById("order-time").textContent = time;
}

// Update time every second
setInterval(updateDateTime, 1000);
updateDateTime(); // Initial call

//Calendar
document.addEventListener("DOMContentLoaded", function () {
    loadCalendar();
});

function openCalendarModal() {
    document.getElementById("calendarModalNew").style.display = "flex";
}

function closeCalendarModal() {
    document.getElementById("calendarModalNew").style.display = "none";
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

    fetch(`fetch_calendar.php?month=${selectedMonth}&year=${selectedYear}`)
        .then(response => response.json())
        .then(data => {
            generateCalendar(data);
        });
}

function generateCalendar(data) {
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
    fetch(`fetch_timeslots.php?date=${date}`)
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
        option.value = slot.value; // 24-hour format for database
        option.textContent = slot.display; // 12-hour format for display
        timeSelect.appendChild(option);
    });
}

//Paymongo
function handlePaymentSubmission(event) {
    event.preventDefault();

    const form = document.querySelector('.appointment-form');
    const nameInput = document.getElementById('nameInput');
    const emailInput = document.getElementById('emailInput'); 
    const dateInput = document.getElementById('datePickerInput');
    const timeSelect = document.getElementById('timeSelect');
    const totalPriceElement = document.getElementById('paymentTotalPrice');

    const totalPrice = parseFloat(totalPriceElement.textContent.replace(/[^\d.]/g, ''));

    if (!form.checkValidity() || isNaN(totalPrice) || totalPrice <= 0) {
        form.reportValidity();
        return;
    }

    const selectedDate = dateInput.value.trim();
    const selectedTime = timeSelect.value;

    // Get selected services
    const servicesTableBody = document.getElementById("servicesTableBody");
    let selectedServices = [];

    if (servicesTableBody) {
        const rows = servicesTableBody.getElementsByTagName("tr");
        for (let row of rows) {
            const serviceName = row.cells[0].textContent;
            const price = row.cells[1].textContent.replace(/[^\d.]/g, '');
            selectedServices.push({ name: serviceName, price: parseFloat(price) });
        }
    }

    if (selectedServices.length === 0) {
        alert("No services selected. Please choose at least one service before proceeding.");
        return;
    }

    const servicesJSON = JSON.stringify(selectedServices);

    // Store details in sessionStorage
    sessionStorage.setItem("selectedDate", selectedDate);
    sessionStorage.setItem("selectedTime", selectedTime);
    sessionStorage.setItem("selectedServices", servicesJSON);

    const formData = new FormData();
    formData.append('name', nameInput.value.trim());
    formData.append('email', emailInput.value.trim());
    formData.append('phonenumber', userData.phonenumber); // Use the phone number from logged-in user's data (from userData)
    formData.append('date', selectedDate);
    formData.append('time', selectedTime);
    formData.append('totalPrice', totalPrice);
    formData.append('services', servicesJSON);

    fetch('create_checkout.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text()) // Change to .text() to get raw response
    .then(data => {
        console.log('Raw Response:', data); // Log the response to check its contents
        try {
            const jsonResponse = JSON.parse(data); // Try parsing the response as JSON
            if (jsonResponse.checkout_url) {
                window.location.href = jsonResponse.checkout_url; 
            } else {
                console.error('Failed to create checkout session:', jsonResponse);
                alert('Failed to initiate payment, please try again.');
            }
        } catch (error) {
            console.error('Error parsing JSON:', error);
            alert('Failed to initiate payment. Server returned an invalid response.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to initiate payment, please try again.');
    });
    
}




//Create Account Modal
function opencreateModal() {
    const modal = document.getElementById('createmodal');
    modal.style.display = 'flex';  
  }
  
  function closecreateModal() {
    document.getElementById('createmodal').style.display = 'none';
    

  // I-clear lahat ng input fields sa modal
  document.getElementById('createfullname').value = '';
  document.getElementById('createemail').value = '';
  document.getElementById('createPhoneNumber').value = '';
  document.getElementById('createAddress').value = '';
  document.getElementById('createbarangay').value = '';
  document.getElementById('createcity').value = '';
  document.getElementById('createregion').value = '';
  document.getElementById('createpassword').value = '';
  document.getElementById('confirmpassword').value = '';

  clearValidationErrors();
  }
  
  function clearValidationErrors() {
    const errorMessages = document.querySelectorAll('.error-message-create');
    errorMessages.forEach(function (element) {
        element.innerText = '';  // I-clear ang error messages
    });
  }

  //Open ng Account Modal
  function openUserModal() {
    const modal = document.getElementById('userModal');
    const modalBody = document.getElementById('modalaccountBody');
  
    if (isLoggedIn) {
        // If logged in, show account info


        modalBody.innerHTML = `
     <h3>Account Information</h3>
         <p><strong>Name:</strong> ${userData.name}</p>
         <p><strong>Address:</strong> ${userData.address}</p>
         <p><strong>Barangay:</strong> ${userData.barangay}</p>
         <p><strong>City:</strong> ${userData.city}</p>
         <p><strong>Region:</strong> ${userData.region}</p>
         <p><strong>Email:</strong> ${userData.email}</p>
         <p><strong>Phone Number:</strong> ${userData.phonenumber}</p>
          <!-- <button onclick="changePassword()">Change Password</button> -->
          <button onclick="editAccount()">Edit Account</button>  
         <button onclick="Logout()">Logout</button>
`;
    } else {
        // If not logged in, show login form
        modalBody.innerHTML = `
     <div class="modal-body-content">
          <div class="form-group">
      <div class="modal-header">
          <h3>Log-in</h3>
      </div>
              <label for="email">Email:</label>
              <input type="email" id="email" class="form-input" placeholder="Enter your email" required>
          </div>
  
          <div class="form-group">
              <label for="password">Password:</label>
              <div class="password-container">
                  <input type="password" id="password" class="form-input" placeholder="Enter your password" required>
                  <img src="./Assests/hide.png" id="togglePassBtn1" class="toggle-icon" onclick="togglePassword('password', 'togglePassBtn1')">
              </div>
          </div>
  
          <div class="button-group">
              <button class="proceed-login-btn" onclick="Loginprocess()">Login</button>
          </div>
  
          <div class="create-account">
              <a href="#" onclick="opencreateModal()">Create an account</a>
          </div>
  
          <div class="divider">
              <span>OR</span>
          </div>
  
          <div class="google-login-group">
              <button class="Login-thru-google" onclick="googleLogin()">Login using Google</button>
          </div>
      </div>
    `;
    
    }
  
    modal.style.display = 'flex';
  }
  

//Login process
function Loginprocess() {
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const currentPage = window.location.pathname; // Get the current page URL
  
    fetch('Login-process.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({email, password, currentPage})
    }).then(response => {
        if (response.redirected) {
            window.location.href = response.url;
        } else {
            response.text().then(alert);
        }
    });
  }
  
  
  //Google SSO
  function googleLogin(name, email) {
    const currentPage = window.location.pathname; // Get the current page URL
  
    fetch('google-login.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({name, email, currentPage})
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'redirect') {
            window.location.href = data.url;
        } else {
            alert("Something went wrong.");
        }
    });
  }

  
  //Create Account Process
function createprocess() {
    validateFullName();
    validateEmail();
    validatePhoneNumber();
    validateAddress();
    validateBarangay();
    validateCity();
    validateRegion();
    validatePassword();
    validateConfirmPassword();
  
    const fullname = document.getElementById('createfullname').value;
    const email = document.getElementById('createemail').value;
    const phonenumber = document.getElementById('createPhoneNumber').value;
    const address = document.getElementById('createAddress').value;
    const barangay = document.getElementById('createbarangay').value;
    const city = document.getElementById('createcity').value;
    const region = document.getElementById('createregion').value;
    const password = document.getElementById('createpassword').value;
    const confirmpassword = document.getElementById('confirmpassword').value;
    const currentPage = window.location.pathname; // Get current page URL
  
    fetch('Registration.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            fullname: fullname,
            email: email,
            phonenumber: phonenumber,
            address: address,
            barangay: barangay,
            city: city,
            region: region,
            password: password,
            confirmpassword: confirmpassword,
            currentPage: currentPage
        })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.status === 'success') {
            clearCreateAccountFields();
            closecreateModal();  // Close the modal on success
        }
    })
    .catch(err => {
        //alert('Error: Please fill in all required fields.');
    });
  }
  
  function clearCreateAccountFields() {
    document.getElementById('fullname').value = '';
  document.getElementById('createusername').value = '';
  document.getElementById('createemail').value = '';
  document.getElementById('createPhoneNumber').value = '';
  document.getElementById('createpassword').value = '';
  document.getElementById('confirmpassword').value = '';
  }
  
  // Clear all fields (optional if you want to reset on close)
function clearCreateAccountFields() {
    document.getElementById('editfullname').value = '';
    document.getElementById('editpassword').value = '';
    document.getElementById('editconfirmpassword').value = '';
    document.getElementById('editAddress').value = '';
    document.getElementById('editPhoneNumber').value = '';
    document.getElementById('editemail').value = '';
    document.getElementById('editbarangay').value = '';
    document.getElementById('editcity').value = '';
    document.getElementById('editregion').value = '';
  }
  
  function changePassword() {
     
  }
  
  function editAccount() {
     
  }
  
  function Logout() {
    const logoutModal = document.getElementById('modalconfirmlogout');
    logoutModal.style.display = 'flex';
  }

  function LogOutNoModal() {
    const logoutModal = document.getElementById('modalconfirmlogout');
    logoutModal.style.display = 'none'; // Just close the modal
  }
  
  function LogOutYesModal() {
    // Store the current page URL
    const currentPage = window.location.href;
    // Redirect to logout.php with a query parameter to return to the current page
    window.location.href = `logout.php?returnUrl=${encodeURIComponent(currentPage)}`;
}  

  function closeUserModal() {
    document.getElementById('userModal').style.display = 'none';
  }
  
  
  // Optional: Close modal when clicking outside content
  window.onclick = function(event) {
      const modal = document.getElementById('userModal');
      if (event.target === modal) {
          closeUserModal();
      }
  };
  
  function validateFullName() {
    const createfullname = document.getElementById('createfullname').value;
    const error = document.getElementById('createfullname-error');
    if (!/^[A-Za-zÑñ\s]+$/.test(createfullname)) {
        error.innerText = 'Full Name should only contain letters and spaces. Numbers and special characters are not allowed.';
    } else {
        error.innerText = '';
    }
  }
  
  function validateEmail() {
    const createemail = document.getElementById('createemail').value;
    const error = document.getElementById('createemail-error');
    if (!createemail.includes('@')) {
        error.innerText = 'Email must contain @ symbol.';
    } else {
        error.innerText = '';
        // Optional: Check if email exists via AJAX
        checkEmailExists(createemail, error);
    }
  }
  
  function validatePassword() {
    const createpassword = document.getElementById('createpassword').value;
    const error = document.getElementById('createpassword-error');
    const regex = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).{8,}$/;
    if (!regex.test(createpassword)) {
        error.innerText = 'Password must be at least 8 characters with 1 uppercase, 1 number, and 1 symbol.';
    } else {
        error.innerText = '';
    }
  }
  
  function validateConfirmPassword() {
    const createpassword = document.getElementById('createpassword').value;
    const confirmPassword = document.getElementById('confirmpassword').value;
    const error = document.getElementById('confirmpassword-error');
    if (createpassword !== confirmPassword) {
        error.innerText = 'Passwords do not match.';
    } else {
        error.innerText = '';
    }
  }
  
  // Validation for Barangay (Allows letters, numbers, "ñ", "-", and "." but no other special characters)
  function validateBarangay() {
    const barangay = document.getElementById('createbarangay').value;
    const error = document.getElementById('createbarangay-error');
    if (/[^A-Za-z0-9ñÑ\-.\s]/.test(barangay)) {
        error.innerText = 'Barangay should only contain letters, numbers, ñ, -, and .';
    } else {
        error.innerText = '';
    }
  }
  
  // Validation for City (No Numbers or Special Characters Allowed)
  function validateCity() {
    const city = document.getElementById('createcity').value;
    const error = document.getElementById('createcity-error');
    if (/[^A-Za-z\s]/.test(city)) {
        error.innerText = 'City should not contain numbers or special characters.';
    } else {
        error.innerText = '';
    }
  }
  
  // Validation for Region (No Special Characters Allowed)
  function validateRegion() {
    const region = document.getElementById('createregion').value;
    const error = document.getElementById('createregion-error');
    if (/[^A-Za-z0-9ñÑ\s\-,]/.test(region)) {
        error.innerText = 'Region should not contain special characters.';
    } else {
        error.innerText = '';
    }
  }
  
  function validatePhoneNumber() {
    const phoneInput = document.getElementById("createPhoneNumber");
    const error = document.getElementById("createPhoneNumber-error");
  
    // Alisin ang non-numeric characters
    phoneInput.value = phoneInput.value.replace(/\D/g, '');
  
    // Limitahan sa 11 digits
    if (phoneInput.value.length > 11) {
        phoneInput.value = phoneInput.value.slice(0, 11);
    }
  
    // Validation message
    if (phoneInput.value.length !== 11) {
        error.innerText = "Phone number must be exactly 11 digits.";
    } else {
        error.innerText = "";
    }
  }
  
  function validateAddress() {
    const addressInput = document.getElementById("createAddress");
    const error = document.getElementById("createAddress-error");
  
    // Regular expression: Letters (uppercase/lowercase), numbers, spaces, commas, and Ñ/ñ only
    const regex = /^[A-Za-z0-9ñÑ\s,]+$/;
  
    if (!regex.test(addressInput.value)) {
        error.innerText = "Address should not contain special characters except commas (,).";
    } else {
        error.innerText = "";
    }
  }
  
  
  // Clear error on input
  document.querySelectorAll('input').forEach(input => {
    input.addEventListener('input', () => {
        const errorSpan = document.getElementById(input.id + '-error');
        if (errorSpan) errorSpan.innerText = '';
    });
  });
  
  
  // OPTIONAL: Check if email exists (AJAX)
  function checkEmailExists(email, errorElement) {
    fetch('CheckEmail.php?email=' + encodeURIComponent(email))
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                errorElement.innerText = 'Email already exists, please use a different email.';
            }
        });
  }


//T&C
function toggleProceedButton() {
    const termsCheckbox = document.getElementById('termsCheckbox');
    const proceedButton = document.getElementById('proceedCreateBtn');
  
    // Enable button only if checkbox is checked
    proceedButton.disabled = !termsCheckbox.checked;
  }
  
  
  function openTermsModal() {
    document.getElementById('termsModal').style.display = 'block';
  }
  
  function closeTermsModal() {
    document.getElementById('termsModal').style.display = 'none';
  }
  
  window.onclick = function(event) {
    if (event.target === document.getElementById('termsModal')) {
        closeTermsModal();
    }
  }


  //EDIT ACCOUNT
// Show the modal and populate fields when clicking "Edit Account"
function editAccount() {
    document.getElementById('editmodal').style.display = 'flex';
    document.getElementById('editemail').value = userData.email;
    document.getElementById('editfullname').value = userData.name;
    document.getElementById('editAddress').value = userData.address;
    document.getElementById('editcity"').value = userData.city;
    document.getElementById('editbarangay').value = userData.barangay;
    document.getElementById('editregion').value = userData.region;
  }
  
  // Close modal
  function closeeditModal() {
    document.getElementById('editmodal').style.display = 'none';
  }
  
  function validateEditFullName() {
    const editfullname = document.getElementById('editfullname').value;
    const error = document.getElementById('editfullname-error');
    if (!/^[A-Za-zÑñ\s]+$/.test(editfullname)) {
        error.innerText = 'Full Name should only contain letters and spaces. Numbers and special characters are not allowed.';
    } else {
        error.innerText = '';
    }
  }
  
  function validateEditEmail() {
    const editemail = document.getElementById('editemail').value;
    const error = document.getElementById('editemail-error');
    if (!editemail.includes('@')) {
        error.innerText = 'Email must contain @ symbol.';
    } else {
        error.innerText = '';
    }
  }
  
  function validateEditPassword() {
    const editpassword = document.getElementById('editpassword').value;
    const error = document.getElementById('editpassword-error');
    const regex = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).{8,}$/;
    if (!regex.test(editpassword)) {
        error.innerText = 'Password must be at least 8 characters with 1 uppercase, 1 number, and 1 symbol.';
    } else {
        error.innerText = '';
    }
  }
  
  function validateEditConfirmPassword() {
    const editpassword = document.getElementById('editpassword').value;
    const editconfirmPassword = document.getElementById('editconfirmpassword').value;
    const error = document.getElementById('editconfirmpassword-error');
    if (editpassword !== editconfirmPassword) {
        error.innerText = 'Passwords do not match.';
    } else {
        error.innerText = '';
    }
  }
  
  // Validation for Barangay (No Special Characters Allowed)
  function validateEditBarangay() {
    const editbarangay = document.getElementById('editbarangay').value;
    const error = document.getElementById('editbarangay-error');
    if (/[^A-Za-z0-9\s]/.test(editbarangay)) {
        error.innerText = 'Barangay should not contain special characters.';
    } else {
        error.innerText = '';
    }
  }
  
  // Validation for City (No Numbers or Special Characters Allowed)
  function validateEditCity() {
    const editcity = document.getElementById('editcity').value;
    const error = document.getElementById('editcity-error');
    if (/[^A-Za-z\s]/.test(editcity)) {
        error.innerText = 'City should not contain numbers or special characters.';
    } else {
        error.innerText = '';
    }
  }
  
  // Validation for Region (No Special Characters Allowed)
  function validateEditRegion() {
    const editregion = document.getElementById('editregion').value;
    const error = document.getElementById('editregion-error');
    if (/[^A-Za-z0-9\s]/.test(editregion)) {
        error.innerText = 'Region should not contain special characters.';
    } else {
        error.innerText = '';
    }
  }
  
  
  function validateEditPhoneNumber() {
    const editphoneInput = document.getElementById("editPhoneNumber");
    const error = document.getElementById("editPhoneNumber-error");
  
    // Alisin ang non-numeric characters
    editphoneInput.value = editphoneInput.value.replace(/\D/g, '');
  
    // Limitahan sa 11 digits
    if (editphoneInput.value.length > 11) {
        editphoneInput.value = editphoneInput.value.slice(0, 11);
    }
  
    // Validation message
    if (editphoneInput.value.length !== 11) {
        error.innerText = "Phone number must be exactly 11 digits.";
    } else {
        error.innerText = "";
    }
  }
  
  function validateEditAddress() {
    const editaddressInput = document.getElementById("editAddress");
    const error = document.getElementById("editAddress-error");
  
    // Regular expression: Letters (uppercase/lowercase), numbers, spaces, commas, and Ñ/ñ only
    const regex = /^[A-Za-z0-9ñÑ\s,]+$/;
  
    if (!regex.test(editaddressInput.value)) {
        error.innerText = "Address should not contain special characters except commas (,).";
    } else {
        error.innerText = "";
    }
  }
  

  function createEditprocess() {
    // Validate inputs
    validateEditFullName();
    validateEditEmail();
    validateEditPhoneNumber();
    validateEditAddress();
    validateEditBarangay();
    validateEditCity();
    validateEditRegion();
    validateEditPassword();
    validateEditConfirmPassword();
  
    const fullname = document.getElementById('editfullname').value;
    const email = document.getElementById('editemail').value;
    const phonenumber = document.getElementById('editPhoneNumber').value;
    const address = document.getElementById('editAddress').value;
    const barangay = document.getElementById('editbarangay').value;
    const city = document.getElementById('editcity').value;
    const region = document.getElementById('editregion').value;
    const password = document.getElementById('editpassword').value;
    const confirmpassword = document.getElementById('editconfirmpassword').value;
  
    if (password !== confirmpassword) {
        alert("Passwords do not match.");
        return;
    }
  
    // Prepare data for AJAX request
    const data = {
        fullname: fullname,
        email: email,
        phonenumber: phonenumber,
        address: address,
        barangay: barangay,
        city: city,
        region: region,
        password: password
    };
  
    // Send AJAX request to edit_account.php
    fetch('edit_account.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Account updated successfully!");
            closeeditModal();
            window.location.reload();
        } else {
            alert("Failed to update account: " + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
  }
  
//Show password
function togglePassword(inputId, iconId) {
    var inputField = document.getElementById(inputId);
    var icon = document.getElementById(iconId);
    
    if (inputField.type === "password") {
        inputField.type = "text";
        icon.src = "./Assests/show.png"; // Change to hide icon
    } else {
        inputField.type = "password";
        icon.src = "./Assests/hide.png"; // Change back to show icon
    }
  }

  function togglePassword1(inputId, iconId) {
    var inputField = document.getElementById(inputId);
    var icon = document.getElementById(iconId);
    
    if (inputField.type === "password") {
        inputField.type = "text";
        icon.src = "./Assests/show.png"; // Change to hide icon
    } else {
        inputField.type = "password";
        icon.src = "./Assests/hide.png"; // Change back to show icon
    }
  }

  function isAccountComplete(userData) {
    return (
        userData.name &&
        userData.email &&
        userData.phonenumber &&
        userData.address &&
        userData.barangay &&
        userData.city &&
        userData.region 
    );
}

//Cart count
let cartCount = 0;  // Variable to keep track of the cart count

// Update cart count function
function updateCartCount() {
    // Update the cart count in the UI
    document.getElementById('cart-count').innerText = cartCount;
}


