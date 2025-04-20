<?php
session_start();
include 'database.php';

// Check if logged in
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

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
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Navbar Only</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Frontend-User/offerservices.css">

    <!-- Firebase SDK -->
    <script type="module">
  import { initializeApp } from "https://www.gstatic.com/firebasejs/9.6.1/firebase-app.js";
  import { getAuth, signInWithPopup, GoogleAuthProvider } from "https://www.gstatic.com/firebasejs/9.6.1/firebase-auth.js";

  // Firebase Configuration
  const firebaseConfig = {
      apiKey: "AIzaSyAPIelxIv7Hri58fdyTy6Fj1ZN7O8FiCsQ",
      authDomain: "softengfinal-7a580.firebaseapp.com",
      projectId: "softengfinal-7a580",
      storageBucket: "softengfinal-7a580.appspot.com",
      messagingSenderId: "615237496560",
      appId: "1:615237496560:web:332c22d9f2040ea4d7dc35",
      measurementId: "G-J8YRKWJHYQ"
  };

  // Initialize Firebase
  const app = initializeApp(firebaseConfig);
  const auth = getAuth(app);
  const provider = new GoogleAuthProvider();

  // Function to handle Google login
  window.googleLogin = async () => {
    try {
        const result = await signInWithPopup(auth, provider);
        const user = result.user;
        const name = user.displayName; // Full name from Google
        const email = user.email;
        const currentPage = window.location.pathname; // Get the current page URL

        // Send the user data to your backend
        const response = await fetch('../Backend-User/google-login.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({name, email, currentPage}) // Include the full name and current page
        });

        const data = await response.json();
        if (data.status === 'redirect') {
            window.location.href = data.url; // Redirect to the current page
        } else {
            alert("Something went wrong.");
        }
    } catch (error) {
        console.error(error);
        alert("Failed to login with Google.");
    }
};
</script>
</head>
  <body>
    
  
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <a class="navbar-brand" href="#">
      <img src="../Assests/logorista.png" height="70">
    </a> <!-- Closed the anchor tag here -->

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="index.php">Home</a>
            </li>
          <li class="nav-item">
            <a class="nav-link" href="#offers">Services</a>
          </li>
          <li class="nav-item">
          <a class="nav-link" href="#" onclick="openUserModal()">Account</a>
          </li>
          <li class="nav-item">
           <a  onclick="openCartModal()">
          <img src="../Assests/cart.png" alt="Cart" class="cart-icon">
           </a>
          </li>

          
        </ul>
      </div>
    </div>
  </nav>

  <!--Account Modal -->
  <div class="modal-account" id="userModal">
    <div class="modal-account-content">
        <button class="close-account" onclick="closeUserModal()">x</button>
        <div id="modalaccountBody">
            <!-- Dynamic content will be placed here -->
        </div>
    </div>
</div>
  
    <!-- Modal for Create Account -->
<div id="createmodal" class="modal-create">
    <div class="modal-create-content">
        <!-- Close Button -->
        <button class="close-create" onclick="closecreateModal()">&times;</button>
        
        <h3>Create an Account</h3>
        
        <label for="createfullname">Full Name:</label>
        <input type="text" id="createfullname" placeholder="Enter your Full Name" required onblur="validateFullName()">
        <span class="error-message-create" id="createfullname-error"></span>
        
        <!-- Email and Phone Number in one row -->
        <div class="inline-group">
            <div>
                <label for="createemail">Email:</label>
                <input type="text" id="createemail" placeholder="Enter your email" required onblur="validateEmail()">
                <span class="error-message-create" id="createemail-error"></span>
            </div>
            <div>
                <label for="createPhoneNumber">Phone Number:</label>
                <input type="tel" id="createPhoneNumber" placeholder="Enter your Phone Number" required 
                       oninput="validatePhoneNumber()" onblur="validatePhoneNumber()">
                <span class="error-message-create" id="createPhoneNumber-error"></span>
            </div>
        </div>
        
        <!-- Address (Separate Row) -->
        <label for="createAddress">Address:</label>
          <input type="text" id="createAddress" placeholder="Enter your Address" required onblur="validateAddress()">
          <span class="error-message-create" id="createAddress-error"></span>

        
        <!-- Barangay, City, Region, and Postal Code in one row -->
        <div class="inline-group">
            <div>
                <label for="createcity">City:</label>
                <input type="text" id="createcity" placeholder="Enter City" autocomplete="off" onblur="validateCity()">
                <ul id="city-list" class="suggestion-list"></ul> <!-- Added suggestion list -->
                <span class="error-message-create" id="createcity-error"></span>
            </div>
            <div>
                <label for="createbarangay">Barangay:</label>
                <input type="text" id="createbarangay" placeholder="Enter Barangay" autocomplete="off" onblur="validateBarangay()">
                <ul id="barangay-list" class="suggestion-list"></ul> <!-- Added suggestion list -->
                <span class="error-message-create" id="createbarangay-error"></span>
            </div>
            <div>
                <label for="createregion">Region:</label>
                <input type="text" id="createregion" placeholder="Enter Region" autocomplete="off" onblur="validateRegion()">
                <ul id="region-list" class="suggestion-list"></ul> <!-- Added suggestion list -->
                <span class="error-message-create" id="createregion-error"></span>
            </div>
            <div>
                <label for="createpostalcode">Postal Code:</label>
                <input type="text" id="createpostalcode" placeholder="Enter Postal Code" autocomplete="off" onblur="validatePostalCode()">
                <span class="error-message-create" id="createpostalcode-error"></span>
            </div>
        </div>
        
        <label for="createpassword">Password:</label>
<div class="password-container-1">
    <input type="password" id="createpassword" placeholder="Enter your password" required onblur="validatePassword()">
    <img src="../Assests/hide.png" id="togglePassBtn3" class="toggle-icon" onclick="togglePassword('createpassword', 'togglePassBtn3')">
</div>
<span class="error-message-create" id="createpassword-error"></span>

<label for="confirmpassword">Confirm Password:</label>
<div class="password-container-1">
    <input type="password" id="confirmpassword" placeholder="Confirm your password" required onblur="validateConfirmPassword()">
    <img src="../Assests/hide.png" id="togglePassBtn4" class="toggle-icon" onclick="togglePassword('confirmpassword', 'togglePassBtn4')">
</div>
<span class="error-message-create" id="confirmpassword-error"></span>
        
        <div class="terms-container">
            <label for="termsCheckbox">
                I Agree to the terms and conditions of Aniah Brow Aesthetic 
                <a href="#" onclick="openTermsModal()">read here</a>
            </label>
        </div>
        
        <button class="proceed-create-btn" id="proceedCreateBtn" onclick="createprocess()" disabled>Create your Account</button>
    </div>
</div>

  <!-- Terms and Condition and Privacy Policy -->
  <div id="termsModal" class="modalTNC">
    <div class="modal-content-TNC">
        <button class="close-create" onclick="closeTermsModal()">×</button>
        <h3>Terms and Conditions - Aniah Brow Aesthetic</h3>
        <p>Welcome to Aniah Brow Aesthetic! To ensure the best experience for all our clients, please carefully read and understand our Terms and Conditions before booking an appointment or availing of any services.</p>

<p><strong>1. Booking and Appointments</strong><br>
All appointments must be booked through our online appointment system.<br>
To secure your preferred time slot, full payment is required upon booking.<br>
We accept GCash, credit cards, debit cards, and other available payment options.<br>
Your appointment will only be confirmed after payment is successfully processed.<br>
Clients are encouraged to arrive at least 10 minutes before their scheduled appointment.<br>
Late arrivals of more than 15 minutes may result in automatic cancellation or rescheduling, subject to availability.</p>

<p><strong>2. Payment and Fees</strong><br>
All service fees are listed on our website and may change without prior notice.<br>
Payments made via our online system are non-refundable, except under certain conditions outlined in the cancellation policy.<br>
Any additional services requested during the appointment must be paid in-store after the session.</p>

<p><strong>3. Cancellations, Rescheduling, and Refunds</strong><br>
Clients may reschedule or cancel their appointment at least 24 hours before their scheduled time by contacting us directly.<br>
Cancellations made within 24 hours of the appointment will result in forfeiture of the payment.<br>
No-shows will also result in forfeiture of the payment.<br>
Refunds are only granted if the clinic cancels the appointment due to unforeseen circumstances (e.g., staff unavailability, power outages, or emergencies).<br>
One-time rescheduling is allowed without extra charge, provided the request is made at least 24 hours before the original appointment.</p>

<p><strong>4. Health and Safety</strong><br>
Clients must disclose any medical conditions, allergies, or sensitivities before any treatment.<br>
We reserve the right to refuse service if the treatment is unsafe for the client’s condition.<br>
All tools and equipment are sanitized following strict health protocols.</p>

<p><strong>5. Service Results</strong><br>
Results may vary per individual depending on skin type, aftercare, and lifestyle.<br>
We do not offer guaranteed results as aesthetic treatments react differently to each person.<br>
Clients must follow recommended aftercare procedures provided by our staff to achieve the best possible results.</p>

<p><strong>6. Client Conduct</strong><br>
We expect respectful and professional behavior from all clients.<br>
Any rude, aggressive, or inappropriate behavior towards our staff or other clients will not be tolerated and may result in immediate termination of services without refund.</p>

<p><strong>7. Liability Waiver</strong><br>
By booking and availing of our services, you acknowledge and accept that there are inherent risks involved in aesthetic procedures.<br>
Aniah Brow Aesthetic and its staff shall not be held liable for any adverse reactions, side effects, or complications if proper pre-treatment disclosure and aftercare instructions were not followed.</p>

<p><strong>8. Privacy Policy</strong><br>
Effective Date: [Date]<br>
At Aniah Brow Aesthetic, we are committed to protecting your privacy. This Privacy Policy outlines how we collect, use, and safeguard your information when you book appointments and avail of our services.<br>
<br>
<strong>Information We Collect</strong><br>
- Full Name<br>
- Contact Number<br>
- Email Address<br>
- Payment Information (processed securely via third-party payment providers)<br>
- Medical History and Treatment Preferences (if applicable)<br>
<br>
<strong>How We Use Your Information</strong><br>
Your information is collected and used for the following purposes:<br>
- To process and confirm your appointment<br>
- To communicate important updates related to your booking<br>
- To keep accurate client records for future treatments<br>
- To improve our services and customer experience<br>
- To comply with legal or regulatory requirements<br>
<br>
<strong>Payment Security</strong><br>
All online payments are processed through secure third-party payment gateways (e.g., GCash, credit/debit card processors).<br>
Aniah Brow Aesthetic does not store or have direct access to your payment card details.<br>
<br>
<strong>Data Privacy and Confidentiality</strong><br>
All personal information provided to us is kept strictly confidential.<br>
We will not share, sell, or rent your information to any third parties unless required by law.<br>
<br>
<strong>Client Rights</strong><br>
You have the right to:<br>
- Access and review the personal information we hold about you<br>
- Request correction of any inaccurate information<br>
- Request deletion of your information, subject to applicable laws<br>
<br>
By booking an appointment and availing of our services, you confirm that you have read, understood, and agreed to our Terms and Conditions and Privacy Policy.</p>
<input type="checkbox" id="termsCheckbox" onchange="toggleProceedButton()">I Agree </input>    
</div>
</div>


  <!-- GLOW SKIN / TAAS NG WEB -->
  <<div class="container-glow">
        <div class="image-wrapper">
            <img src="../Assests/aniahlogo.png" alt="Placeholder Image">
        </div>
        <div class="text">Glow From Within: <span>Unlock Your Best Skin Yet</span></div>
        <div class="subtext">Discover the secrets to radiant and healthy skin.</div>
    </div>
<!-- Offer Section -->

<section id="packages" class="offer-section container mt-5">
  <h1 class="text-center fw-bold">Packages</h1>
</div>

<section id = "offers" class="offer-section container mt-5">
    <div class="row g-4">
      <!-- First Card -->
      <div class="col-md-6 col-lg-4">
        <div class="offer-card">
          <img src="../Assests/Pic1.jpg" alt="Gluta Package" class="offer-img rectangular">
          <div class="offer-overlay">
            <h3 class="offer-title">View our PACKAGE</h3>
            <p class="offer-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor.</p>

            <!-- BUTTON PARA SA MODAL-->
            <button class="offer-btn" onclick="openGlutaModal()">See Offer</button>
          </div>
        </div>
      </div>

      <!-- Second Card -->
      <div class="col-md-6 col-lg-4">
        <div class="offer-card">
          <img src="../Assests/Pic2.jpg" alt="Promo Offer" class="offer-img">
          <div class="offer-overlay">
            <h3 class="offer-title">View our Promos</h3>
            <!-- BUTTON PARA SA MODAL-->
            <button class="offer-btn" onclick="openPromoModal()">See Offer</button>
          </div>
        </div>
      </div>

      <!-- Third Card -->
      <div class="col-md-6 col-lg-4">
        <div class="offer-card">
          <img src="../Assests/Pic3.jpg" alt="Promo Offer" class="offer-img">
          <div class="offer-overlay">
            <h3 class="offer-title">10% OFF LIMITED PROMO</h3>
         <!-- BUTTON PARA SA MODAL-->
         <button class="offer-btn" onclick="openGlutaModal()">See Offer</button>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Modal Structure For Gluta Package -->
  <div id="glutaModal" class="modal-gluta">
    <div class="modal-gluta-content">
        <!-- Close Button -->
        <button class="close-modal" onclick="closeModal()">✖</button>
        <h2 class="modal-title">AVAIL OUR PACKAGE</h2>

        <!-- Modal Grid (Initially Empty - Filled by JS) -->
        <div class="modal-grid">
            <!-- Services will be inserted here dynamically -->
        </div>
    </div>
</div>

<div id="glutaModal" class="modal-gluta">
    <div class="modal-gluta-content">
        <!-- Close Button -->
        <button class="close-modal" onclick="closeModal()">✖</button>
        <h2 class="modal-title">AVAIL OUR PACKAGE</h2>

        <!-- Modal Grid (Initially Empty - Filled by JS) -->
        <div class="modal-grid">
            <!-- Services will be inserted here dynamically -->
        </div>
    </div>
</div>

<div id="PromoModal" class="modal-gluta">
    <div class="modal-gluta-content">
        <!-- Close Button -->
        <button class="close-modal" onclick="closeModal()">✖</button>
        <h2 class="modal-title">AVAIL OUR PACKAGE</h2>

        <!-- Modal Grid (Initially Empty - Filled by JS) -->
        <div class="modal-grid">
            <!-- Services will be inserted here dynamically -->
        </div>
    </div>
</div>


  <!-- Facial Treatments Section -->
  <section class="facial-treatments container">
    <h2 class="section-title">Facial Treatments</h2>
    <!-- Category Tabs -->
    <div class="categories">
    <a href="#" class="category active" data-category="all">All Services</a>
    <a href="#" class="category" data-category="Facial">Facial</a>
    <a href="#" class="category" data-category="Eyebrow">Eyebrow</a>
    <a href="#" class="category" data-category="Eyelash">Eyelash</a>
    <a href="#" class="category" data-category="Nail">Nail</a>
    <a href="#" class="category" data-category="Skin">Skin</a>
    <a href="#" class="category" data-category="Body">Body</a>
    <a href="#" class="category" data-category="Makeup">Makeup</a>
    </div>

    <div class="treatment-grid">
       
    </div>

     <!-- Limiter -->
    <div id="limitModal" class="modal">
    <div class="modal-content">
        <p>Up to 3 services per transaction only.</p>
        <button onclick="closeLimitModal()">OK</button>
    </div>
</div>

<!-- Duplicate -->
<div id="duplicateServiceModal" class="modal">
    <div class="modal-content">
        <p>Service is already on the list.</p>
        <button onclick="closeDuplicateServiceModal()">OK</button>
    </div>
</div>

        <!-- Modal Structure -->
<div id="orderSummaryModal" class="modal">
    <div class="modal-content">
        <!-- Close Button -->
        <button class="close-modal" onclick="closeOrderSummary()">×</button>

        <!-- Modal Title -->
        <h2 class="modal-title">ORDER SUMMARY</h2>

        <!-- Order Details -->
        <div class="order-details">
    <div class="order-row">
        <span class="order-label">Date</span>
        <span class="order-value" id="order-date"></span>
    </div>
    <div class="order-row">
        <span class="order-label">Time</span>
        <span class="order-value" id="order-time"></span>
    </div>
    </div>

        <!-- Services Table -->
        <h3 class="services-title">Services</h3>
        <table class="services-table">
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Price</th>
                    <th>Remove Items</th>
                </tr>
            </thead>
            <tbody id="servicesTableBody">
                <!-- Dynamically Populated Rows -->
            </tbody>
        </table>

        <!-- Total Row -->
        <div class="total-row">
            <span>Total</span>
            <span id="totalPrice">0.00</span>
        </div>

        <!-- Proceed Button -->
        <button class="proceed-btn" onclick="openProceedPaymentModal()">Proceed Payment</button>
    </div>
</div>


<!-- Remove Confirmation Modal -->
<div id="removeConfirmationModal" class="modal">
    <div class="modal-content">
        <p>Are you sure you want to delete this service?</p>
        <div class="modal-actions">
            <button class="confirm-btn" onclick="confirmRemoveItem()">Yes</button>
            <button class="cancel-btn" onclick="closeRemoveConfirmation()">Cancel</button>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="removeSuccessModal" class="modal">
    <div class="modal-content">
        <p>Your service has been Deleted</p>
        <button class="confirm-btn" onclick="closeSuccessModal()">Confirm</button>
    </div>
</div>



<!-- Proceed Payment Modal -->
<div id="proceedPaymentModal" class="modal">
    <div class="modal-content appoint-now-section">
        <!-- Close Button -->
        <button id="close-modal-payment" class="close-modal-payment" onclick="closeProceedPaymentModal()">×</button>

        <!-- Modal Content -->
        <div class="image-container"></div>

        <div class="form-container">
            <h2 class="appoint-title">PROCEED PAYMENT</h2>
            <p class="appoint-description">
                Complete your payment by filling in the required details below. Ensure all fields are accurate to avoid delays.
            </p>
            <form class="appointment-form" onsubmit="handlePaymentSubmission(event)">
                
                <div class="form-group">
                    <label for="nameInput">Full Name:</label>
                    <input type="text" id="nameInput" value="<?php echo htmlspecialchars($fullName); ?>" 
                        minlength="6" maxlength="25" required readonly />
                </div>

                <div class="form-group">
                    <label for="emailInput">Email:</label>
                    <input type="email" id="emailInput" value="<?php echo htmlspecialchars($email); ?>" 
                        required readonly />
                </div>

                <div class="form-group">
                    <label for="phoneInput">Phone Number:</label>
                    <input type="text" id="phoneInput" value="<?php echo htmlspecialchars($phonenumber); ?>" 
                        required readonly />
                </div>

                <div class="form-group">
                    <label for="datePickerInput">Select Date:</label>
                    <input type="text" id="datePickerInput" placeholder="Select Date" readonly onclick="openCalendarModal()" required />
                </div>

                <div class="form-group">
                    <label for="timeSelect">Select Time:</label>
                    <select id="timeSelect" required>
                        <option value="">Select Time</option>
                    </select>
                </div>

                <div class="total-payment-container">
                    <strong>Total Price:</strong> 
                    <span id="paymentTotalPrice">₱0.00</span>
                </div>

                <button type="submit" class="confirm-button">Proceed Payment</button>
            </form>
        </div>
    </div>
</div>


<!-- Warning Modal -->
<div id="warningModal" class="modal">
    <div class="modal-content">
        <p>Select an item before proceeding to the payment</p>
        <button onclick="closeWarningModal()">OK</button>
    </div>
</div>


 <!-- Calendar Modal -->
 <div id="calendarModalNew" class="modal-calendar">
    <div class="modal-calendar-content">
        <button class="close-modal" onclick="closeNewCalendarModal()">×</button>

        <div class="calendar-header">
            <button onclick="loadCalendar('current')">This Month</button>
            <button onclick="loadCalendar('next')">Next Month</button>
        </div>

        <div id="calendarContainer"></div>

        <button class="confirm-button" onclick="confirmNewDate()">Confirm</button>
    </div>
</div>

    <!-- Confirmation Popup Modal -->
    <div id="confirmationPopupModal" class="modal">
        <div class="modal-content">
            <button class="close-modal" onclick="closeConfirmationModal()">×</button>
            <h2>Confirmation</h2>
            <p>Your date has been successfully selected. Please proceed with the next steps.</p>
            <button class="confirm-button" onclick="closeConfirmationModal()">OK</button>
        </div>
    </div>

    <!-- Added to card -->
    <div id="confirmationPopupModal" class="modal">
        <div class="modal-content">
            <button class="close-modal" onclick="closeConfirmationModal()">×</button>
            <h2>Confirmation</h2>
            <p>Your date has been successfully selected. Please proceed with the next steps.</p>
            <button class="confirm-button" onclick="closeConfirmationModal()">OK</button>
        </div>
    </div>

    <!-- Success Modal -->
<div id="successModal" class="modal-cartAdded">
    <div class="modal-cartAdded-content">
        <p>Service Added Successfully</p>
    </div>
</div>

 <!-- Logout Confirmation Modal -->
<div class="modal-confirm-logout" id="modalconfirmlogout">
        <div class="modal-logout-confirm-content">

            <!-- <button class="close-modal-logout-confirm" onclick="closeConfirmationModal()">×</button> -->
            <p>Are you sure you want to log out?</p>
            <button class="confirm-logout-button" onclick="LogOutYesModal()">Yes</button>
            <button class="deny-logout-button" onclick="LogOutNoModal()">No</button>
        </div>
</div>

<!-- Modal for Edit Account -->
<div id="editmodal" class="modal-create">
    <div class="modal-create-content">
        <!-- Close Button -->
        <button class="close-create" onclick="closeeditModal()">&times;</button>
        
        <h3>Edit your Account</h3>
        
        <label for="editfullname">Full Name:</label>
<input type="text" id="editfullname" value="<?php echo htmlspecialchars($fullName); ?>" required onblur="validateEditFullName()">
<span class="error-message-edit" id="editfullname-error"></span>
        
        <!-- Email and Phone Number in one row -->
        <div class="inline-group">
        <div>
    <label for="editemail">Email:</label>
    <input type="text" id="editemail" value="<?php echo htmlspecialchars($email); ?>" readonly>
    <span class="error-message-edit" id="editemail-error"></span>
</div>

            <div>
                <label for="editPhoneNumber">Phone Number:</label>
                <input type="tel" id="editPhoneNumber" placeholder="Enter your Phone Number" required 
                       oninput="validateEditPhoneNumber()" onblur="validateEditPhoneNumber()">
                <span class="error-message-edit" id="editPhoneNumber-error"></span>
            </div>
        </div>
        
        <!-- Address (Separate Row) -->
        <label for="editAddress">Address:</label>
        <input type="text" id="editAddress" placeholder="Enter your Address" required onblur="validateEditAddress()">
        <span class="error-message-edit" id="editAddress-error"></span>

        <!-- Barangay, City, Region, and Postal Code in one row -->
        <div class="inline-group">
            <div>
                <label for="editcity">City:</label>
                <input type="text" id="editcity" placeholder="Enter City" autocomplete="off" onblur="validateEditCity()">
                <ul id="edit-city-list" class="suggestion-list"></ul> <!-- Added suggestion list -->
                <span class="error-message-edit" id="editcity-error"></span>
            </div>
            <div>
                <label for="editbarangay">Barangay:</label>
                <input type="text" id="editbarangay" placeholder="Enter Barangay" autocomplete="off" onblur="validateEditBarangay()">
                <ul id="edit-barangay-list" class="suggestion-list"></ul> <!-- Added suggestion list -->
                <span class="error-message-edit" id="editbarangay-error"></span>
            </div>
            <div>
                <label for="editregion">Region:</label>
                <input type="text" id="editregion" placeholder="Enter Region" autocomplete="off" onblur="validateEditRegion()">
                <ul id="region-list" class="suggestion-list"></ul> <!-- Added suggestion list -->
                <span class="error-message-edit" id="editregion-error"></span>
            </div>
            <div>
                <label for="editpostalcode">Postal Code:</label>
                <input type="text" id="editpostalcode" placeholder="Enter Postal Code" autocomplete="off" onblur="validateEditPostalCode()">
                <span class="error-message-edit" id="editpostalcode-error"></span>
            </div>
        </div>

        <label for="editpassword">Password:</label>
<div class="password-container-1">
    <input type="password" id="editpassword" placeholder="Enter your password" required onblur="validateEditPassword()">
    <img src="../Assests/hide.png" id="togglePassBtn3" class="toggle-icon" onclick="togglePassword('editpassword', 'togglePassBtn3')">
</div>
<span class="error-message-edit" id="editpassword-error"></span>

<label for="editconfirmpassword">Confirm Password:</label>
<div class="password-container-1">
    <input type="password" id="editconfirmpassword" placeholder="Confirm your password" required onblur="validateEditConfirmPassword()">
    <img src="../Assests/hide.png" id="togglePassBtn4" class="toggle-icon" onclick="togglePassword('editconfirmpassword', 'togglePassBtn4')">
</div>
<span class="error-message-edit" id="editconfirmpassword-error"></span>
        
        <button class="proceed-create-btn" id="proceedCreateBtn" onclick="createEditprocess()">Edit your Account</button>
    </div>
</div>

<script>
      const isLoggedIn = <?php echo json_encode($isLoggedIn); ?>;
    const userData = {
        name: <?php echo json_encode($fullName); ?>, 
        email: <?php echo json_encode($email); ?>,
        phonenumber: <?php echo json_encode($phonenumber); ?>,
        address: <?php echo json_encode($address); ?>,
        barangay: <?php echo json_encode($barangay); ?>,
        city: <?php echo json_encode($city); ?>,
        region: <?php echo json_encode($region); ?>,
        postalCode: <?php echo json_encode($postalCode); ?>
    }
</script>

    
  <script src="../Frontend-User/offerservices.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
