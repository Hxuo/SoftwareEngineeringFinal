
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
      // Clear existing options first (keeping only the first "Select Region" option)
      regionInput.innerHTML = '<option value="" disabled selected>Select Region</option>';
      
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

  const regionInput = document.getElementById("editregion");
  const cityInput = document.getElementById("editcity");

  // Load JSON files
  Promise.all([
      fetch("./JSON/regions.json").then(res => res.json()).then(data => regions = data)
  ]).then(() => {
      console.log("✅ Data loaded successfully");
      populateRegions();  // Populate the regions dropdown
  }).catch(error => console.error("❌ Error loading data:", error));

  // Populate Region Dropdown
  function populateRegions() {
      // Clear existing options first (keeping only the first "Select Region" option)
      regionInput.innerHTML = '<option value="" disabled selected>Select Region</option>';
      
      for (const region in regions) {
          let option = document.createElement("option");
          option.value = region;
          option.textContent = region;
          regionInput.appendChild(option);
      }
  }

  // Load cities based on the selected region
  function loadCities() {
    return new Promise((resolve) => {
        const selectedRegion = document.getElementById('editregion').value;
        const citySelect = document.getElementById('editcity');
        const cityList = regions[selectedRegion] || [];
        
        citySelect.innerHTML = '<option value="" disabled selected>Select City</option>';
        citySelect.disabled = false;
        
        cityList.forEach(city => {
            let option = document.createElement('option');
            option.value = city;
            option.textContent = city;
            citySelect.appendChild(option);
        });
        
        resolve();
    });
}

  // Add event listener to the region dropdown to load cities when a region is selected
  regionInput.addEventListener("change", loadCities);

  // Function to open edit modal with current values
  window.editAccount = function() {
    // Show modal
    document.getElementById('editmodal').style.display = 'flex';
    
    // Set all field values
    document.getElementById('editfullname').value = userData.name || '';
    document.getElementById('editemail').value = userData.email || '';
    document.getElementById('editPhoneNumber').value = userData.phonenumber || '';
    document.getElementById('editAddress').value = userData.address || '';
    document.getElementById('editbarangay').value = userData.barangay || '';
    document.getElementById('editregion').value = userData.region || '';
    
    // Special handling for City dropdown
    if (userData.region) {
        loadCities().then(() => {
            const citySelect = document.getElementById('editcity');
            if (userData.city) {
                citySelect.value = userData.city;
                // Lock the city field if it has a value
                citySelect.disabled = true;
                citySelect.classList.add('locked-field');
            }
        });
    }

    // Lock all non-phone fields that have values
    const fieldsToLock = ['editfullname', 'editemail', 'editAddress', 'editbarangay', 'editregion'];
    fieldsToLock.forEach(field => {
        const element = document.getElementById(field);
        const shouldLock = element.value && element.value.trim() !== '';
        element.readOnly = shouldLock;
        element.disabled = shouldLock && element.tagName === 'SELECT';
        element.classList.toggle('locked-field', shouldLock);
    });
    
    // Always keep phone number editable
    const phoneInput = document.getElementById('editPhoneNumber');
    phoneInput.readOnly = false;
    phoneInput.disabled = false;
    phoneInput.classList.remove('locked-field');
};

// Improved loadCities() with error handling
function loadCities() {
    return new Promise((resolve) => {
        const region = document.getElementById('editregion').value;
        const citySelect = document.getElementById('editcity');
        
        citySelect.innerHTML = '<option value="" disabled selected>Select City</option>';
        
        if (regions[region]) {
            regions[region].forEach(city => {
                const option = document.createElement('option');
                option.value = city;
                option.textContent = city;
                citySelect.appendChild(option);
            });
            
            // Set existing city value if available
            if (userData.city) {
                setTimeout(() => {
                    citySelect.value = userData.city;
                    resolve();
                }, 0);
            } else {
                resolve();
            }
        } else {
            citySelect.disabled = true;
            resolve();
        }
    });
}
});

// ===============================================================================

document.addEventListener("DOMContentLoaded", () => {
  toggleProceedButton();
  const cardContainer = document.getElementById("card-container");
  const prevBtn = document.getElementById("prev-btn");
  const nextBtn = document.getElementById("next-btn");

  // Calculate card width (including margin)
  const cardWidth = document.querySelector(".card").offsetWidth + 10;
  const cards = [...document.querySelectorAll(".card")];
  const totalCards = cards.length;

  // Disable prev button initially (since we start at first card)
  prevBtn.disabled = true;

  // Scroll smoothly + disable buttons when reaching ends
  const scrollSmoothly = (direction) => {
    if (direction === "next") {
      cardContainer.scrollBy({ left: cardWidth, behavior: "smooth" });
    } else {
      cardContainer.scrollBy({ left: -cardWidth, behavior: "smooth" });
    }

    // Check scroll position after animation
    setTimeout(() => {
      const currentScroll = cardContainer.scrollLeft;
      const maxScroll = (totalCards - 1) * cardWidth;

      // Disable next button if at last card
      nextBtn.disabled = currentScroll >= maxScroll;
      
      // Disable prev button if at first card
      prevBtn.disabled = currentScroll <= 0;
    }, 300);
  };

  nextBtn.addEventListener("click", () => scrollSmoothly("next"));
  prevBtn.addEventListener("click", () => scrollSmoothly("prev"));
});




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
  <div class="modal-header">
    <h3>Account Information</h3>
  </div>
  
  <div class="account-info-grid">
    <div class="account-info-item">
      <div class="account-info-label">Name</div>
      <div class="account-info-value">${userData.name}</div>
    </div>
    
    <div class="account-info-item">
      <div class="account-info-label">Email</div>
      <div class="account-info-value">${userData.email}</div>
    </div>
    
    <div class="account-info-item">
      <div class="account-info-label">Phone</div>
      <div class="account-info-value">${userData.phonenumber}</div>
    </div>
    
    <div class="account-info-item">
      <div class="account-info-label">Address</div>
      <div class="account-info-value">${userData.address}</div>
    </div>
    
    <div class="account-info-item">
      <div class="account-info-label">Barangay</div>
      <div class="account-info-value">${userData.barangay}</div>
    </div>
    
    <div class="account-info-item">
      <div class="account-info-label">City/Region</div>
      <div class="account-info-value">${userData.city}, ${userData.region}</div>
    </div>
  </div>
  
  <div class="account-actions">
    <button class="account-action-btn edit-account-btn" onclick="editAccount()">Edit Account</button>
    <button class="account-action-btn logout-btn" onclick="Logout()">Logout</button>
  </div>
  
`;
  } else {
      // If not logged in, show login form
      modalBody.innerHTML = `
      

       <button class="close-account" onclick="closeUserModal()">x</button>
          <div class="form-group">
      <div class="modal-header">
          <h3>Login</h3>
      </div>
              <label for="email">Email</label>
              <input type="email" id="email" class="form-input" placeholder="Enter your email" required>
          </div>
  
          <div class="form-group">
              <label for="password">Password</label>
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
      
  `;
  }

  modal.style.display = 'flex';
}

// Login Error Modal elements
const loginErrorModal = document.getElementById('loginErrorModal');
const loginErrorText = document.getElementById('loginErrorText');

// Function to show login error modal with auto-close
function showLoginErrorModal(message) {
    loginErrorText.textContent = message;
    loginErrorModal.style.display = 'flex'; // Changed to flex for centering
    
    // Auto-close after 1 second
    setTimeout(() => {
        loginErrorModal.style.display = 'none';
    }, 2000);
}


//Login process
// Modified Loginprocess function with email
function Loginprocess() {
  const email = document.getElementById('email').value;
  const password = document.getElementById('password').value;
  const currentPage = window.location.pathname;

  fetch('Login-process.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: new URLSearchParams({email, password, currentPage})
  }).then(response => {
      if (response.redirected) {
          window.location.href = response.url;
      } else {
          return response.text();
      }
  }).then(text => {
      if (text.includes('Invalid email or password')) {
          showLoginErrorModal('Invalid email or password.');
      } else if (text.includes('Please verify your email')) {
          showLoginErrorModal('Please verify your email first.');
      }
  }).catch(error => {
      showLoginErrorModal('Login error. Please try again.');
  });
}


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

  const errors = document.querySelectorAll('.error-message-create');
  let hasErrors = false;
  
  errors.forEach(error => {
    if (error.innerText !== '') {
      hasErrors = true;
    }
  });

  if (hasErrors) {
    alert('Please fill all inputs before submitting.');
    return;
  }

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
  document.getElementById('createpassword').value = '';
  document.getElementById('confirmpassword').value = '';
}



function changePassword() {
   
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

document.addEventListener('click', function(event) {
  const modal = document.getElementById('userModal');
  if (event.target === modal) {
    closeUserModal();
  }
});

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
  const createemail = document.getElementById('createemail').value.trim();
  const error = document.getElementById('createemail-error');
  
  // Clear previous error
  error.innerText = '';
  
  // First check if email is empty
  if (!createemail) {
    error.innerText = 'Email is required.';
    return false;
  }
  
  // Basic email validation
  if (!createemail.includes('@')) {
    error.innerText = 'Email must contain @ symbol.';
    return false;
  }
  
  // Only check for existence if format is valid and not empty
  checkEmailExists(createemail, error);
  return true;
}

function validatePassword() {
  const createpassword = document.getElementById('createpassword').value.trim();
  const error = document.getElementById('createpassword-error');
  const regex = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).{8,}$/;
  if (!regex.test(createpassword)) {
      error.innerText = 'Password must be at least 8 characters with 1 uppercase, 1 number, and 1 special character.';
  } else {
      error.innerText = '';
  }
}

function validateConfirmPassword() {
  const createpassword = document.getElementById('createpassword').value.trim();
  const confirmPassword = document.getElementById('confirmpassword').value.trim();
  const error = document.getElementById('confirmpassword-error');
  if (createpassword !== confirmPassword) {
      error.innerText = 'Passwords do not match.';
  } else {
      error.innerText = '';
  }
}

// Validation for Barangay (Allows letters, numbers, "ñ", "-", and "." but no other special characters)
function validateBarangay() {
  const barangay = document.getElementById('createbarangay').value.trim();
  const error = document.getElementById('createbarangay-error');
  
  if (!barangay) {
      error.innerText = 'Barangay is required.';
      return false;
  }
  
  if (/[^A-Za-z0-9ñÑ\-.\s]/.test(barangay)) {
      error.innerText = 'Barangay should only contain letters, numbers, ñ, -, and .';
      return false;
  }
  
  error.innerText = '';
  return true;
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
  // Don't check if email is empty
  if (!email || !email.includes('@')) return;
  
  fetch(`CheckEmail.php?createemail=${encodeURIComponent(email)}`)
    .then(response => response.json())
    .then(data => {
      if (data.exists) {
        errorElement.innerText = 'Email already exists.';
      }
    })
    .catch(error => {
      console.error('Error checking email:', error);
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
  document.getElementById('termsModal').style.display = 'flex';
}

function openTNCModal() {
  document.getElementById('TNCModal').style.display = 'flex';
}

function closeTermsModal() {
  document.getElementById('termsModal').style.display = 'none';
}

function closeTNCModal() {
  document.getElementById('TNCModal').style.display = 'none';
}

function closeTNCModal() {
  document.getElementById('TNCModal').style.display = 'none';
}

// Add event listener to close modal when clicking outside
document.addEventListener('click', function(event) {
  const modal = document.getElementById('TNCModal');
  if (event.target === modal) {
    closeTNCModal();
  }
});

window.onclick = function(event) {
  if (event.target === document.getElementById('termsModal')) {
      closeTermsModal();
  }
}

//EDIT ACCOUNT
// Show the modal and populate fields when clicking "Edit Account"


// Close modal
function closeeditModal() {
  document.getElementById('editmodal').style.display = 'none';
}

document.addEventListener('click', function(event) {
  const modal = document.getElementById('editmodal');
  if (event.target === modal) {
    closeeditModal();
  }
});

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
      error.innerText = 'Password must be at least 8 characters with 1 uppercase, 1 number, and 1 special character.';
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
  if (/[^A-Za-z0-9ñÑ\s\-,]/.test(editregion)) {
      error.innerText = 'Region should not contain special characters.';
  } else {
      error.innerText = '';
  }
}



function validateEditPhoneNumber() {
  const editphoneInput = document.getElementById("editPhoneNumber");
  const error = document.getElementById("editPhoneNumber-error");

  // Remove non-numeric characters
  editphoneInput.value = editphoneInput.value.replace(/\D/g, '');

  // Limit to 11 digits
  if (editphoneInput.value.length > 11) {
      editphoneInput.value = editphoneInput.value.slice(0, 11);
  }

  // Validation message
  if (editphoneInput.value.length === 0) {
      error.innerText = "Phone number is required.";
      return false; // Return false when validation fails
  } else if (editphoneInput.value.length !== 11) {
      error.innerText = "Phone number must be exactly 11 digits.";
      return false; // Return false when validation fails
  } else {
      error.innerText = "";
      return true; // Return true when validation passes
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
  // Run all validations and collect results
  const validations = [
    validateEditFullName(),
    validateEditEmail(),
    validateEditPhoneNumber(), // This now returns true/false
    validateEditAddress(),
    validateEditBarangay(),
    validateEditCity(),
    validateEditRegion(),
    validateEditPassword(),
    validateEditConfirmPassword()
  ];

  // Check if any validation failed
  if (validations.includes(false)) {
    alert("Please fix all errors before submitting.");
    return;
  }

  // Check password match
  const password = document.getElementById('editpassword').value;
  const confirmpassword = document.getElementById('editconfirmpassword').value;
  
  if (password !== confirmpassword) {
    document.getElementById('editconfirmpassword-error').innerText = "Passwords do not match.";
    alert("Passwords do not match.");
    return;
  }

  // Prepare and send data
  const data = {
    fullname: document.getElementById('editfullname').value,
    email: document.getElementById('editemail').value,
    phonenumber: document.getElementById('editPhoneNumber').value,
    address: document.getElementById('editAddress').value,
    barangay: document.getElementById('editbarangay').value,
    city: document.getElementById('editcity').value,
    region: document.getElementById('editregion').value,
    password: password
  };

  fetch('edit_account.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
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
    alert("An error occurred. Please try again.");
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

//Show password
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




// Click listener to open management
document.addEventListener('DOMContentLoaded', function() {
  // Function to open management modal
  function openstaffmanagement() {
    document.getElementById('staffpass1').style.display = 'flex';
  }
  
  // Function to close management modal
  function closeManagementModal() {
    document.getElementById('staffpass1').style.display = 'none';
  }
  
  // Copyright click listener
  const copyrightParagraph = document.querySelector('.footer-bottom p');
  let clickCount = 0;
  
  copyrightParagraph.addEventListener('click', function() {
    clickCount++;
    
    if (clickCount === 5) {
      openstaffmanagement();
      clickCount = 0; // Reset counter after opening
    }
    
    // Reset counter if user takes too long between clicks
    setTimeout(() => {
      clickCount = 0;
    }, 3000); // 3 seconds timeout
  });

  // Make these functions available globally
  window.openstaffmanagement = openstaffmanagement;
  window.closeManagementModal = closeManagementModal;
});

document.addEventListener('click', function(event) {
  const modal = document.getElementById('staffpass1');
  if (event.target === modal) {
    closeManagementModal();
  }
});


// Toggle password visibility
function togglePassword(inputId, iconId) {
  const passwordInput = document.getElementById(inputId);
  const icon = document.getElementById(iconId);
  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    icon.src = './Assests/show.png'; // Path to show icon
  } else {
    passwordInput.type = 'password';
    icon.src = './Assests/hide.png'; // Path to hide icon
  }
}

// Staff login process
function staffprocess() {
  const passwordInput = document.getElementById('staffpassword').value;
  const errorMessage = document.getElementById('staffpassword-error');

  // Clear previous error message
  errorMessage.textContent = "";

  // Validate password input
  if (!passwordInput) {
    errorMessage.textContent = "Password is required";
    errorMessage.style.color = "red";
    return; // Stop the function if password is empty
  }

  // Send password to the backend for validation
  fetch('validateStaffPassword.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ password: passwordInput })
  })
    .then(response => response.json())
    .then(data => {
      if (data.status === 'success') {
        window.location.href = './Backend-Admin/DashboardSched.php'; // Redirect on success
      } else {
        errorMessage.textContent = data.message || "Wrong password"; // Show error message
        errorMessage.style.color = "red";
      }
    })
    .catch(error => {
      console.error('Error:', error);
      errorMessage.textContent = "Something went wrong. Try again.";
      errorMessage.style.color = "red";
    });
}


// Global pagination variables
let currentPageSchedule = 1;
let currentPageHistory = 1;
const appointmentsPerPage = 5;

// Function to open the user appointment modal
function openUserAppointmentModal() {
    const modal = document.getElementById('userAppointmentModal');
    modal.style.display = 'flex';  // Show the modal when clicked

    // Fetch scheduled appointments for the logged-in user
    fetchUserAppointments();

    // Fetch appointment history for the logged-in user
    fetchUserAppointmentHistory();
}

// Function to close the modal
function closeUserAppointmentModal() {
    const modal = document.getElementById('userAppointmentModal');
    modal.style.display = 'none';  // Hide the modal
}

document.addEventListener('click', function(event) {
  const modal = document.getElementById('userAppointmentModal');
  if (event.target === modal) {
    closeUserAppointmentModal();
  }
});

// Function to fetch scheduled appointments for the logged-in user
function fetchUserAppointments() {
    const userEmail = '<?php echo $email; ?>'; // Use the session email from PHP
    fetch(`fetch_user_appointments.php?email=${userEmail}`)
        .then(response => response.json())
        .then(data => populateUserAppointments('user-schedule-body', data, "No appointments for today"))
        .catch(error => console.error('Error fetching appointments:', error));
}

// Function to fetch appointment history for the logged-in user
function fetchUserAppointmentHistory() {
    const userEmail = '<?php echo $email; ?>'; // Use the session email from PHP
    fetch(`fetch_user_appointment_history.php?email=${userEmail}`)
        .then(response => response.json())
        .then(data => populateUserHistory('user-history-body', data, "No history available"))
        .catch(error => console.error('Error fetching appointment history:', error));
}

// Function to paginate the scheduled appointments
function paginateAppointments(data, page) {
    const start = (page - 1) * appointmentsPerPage;
    const end = page * appointmentsPerPage;
    return data.slice(start, end);
}

// Function to paginate the appointment history
function paginateHistory(data, page) {
    const start = (page - 1) * appointmentsPerPage;
    const end = page * appointmentsPerPage;
    return data.slice(start, end);
}

// Function to render pagination buttons
function renderPagination(totalItems, page, type) {
    const totalPages = Math.ceil(totalItems / appointmentsPerPage);
    let paginationHtml = '';

    for (let i = 1; i <= totalPages; i++) {
        paginationHtml += `<button class="page-btn" onclick="changePage(${i}, '${type}')">${i}</button>`;
    }

    const paginationContainer = document.getElementById(`${type}-pagination`);
    if (paginationContainer) {
        paginationContainer.innerHTML = paginationHtml;
    }
}

// Function to change pages
function changePage(page, type) {
    if (type === 'schedule') {
        currentPageSchedule = page;
        fetchUserAppointments();
    } else if (type === 'history') {
        currentPageHistory = page;
        fetchUserAppointmentHistory();
    }
}

// Function to populate scheduled appointments in the table
function populateUserAppointments(tableId, data, noDataMessage) {
  const tableBody = document.getElementById(tableId);
  if (!tableBody) {
      console.error(`Error: Element with ID '${tableId}' not found.`);
      return;
  }

  tableBody.innerHTML = ""; // Clear previous content

  const paginatedData = paginateAppointments(data, currentPageSchedule);

  if (!paginatedData || paginatedData.length === 0 || data.error) {
      tableBody.innerHTML = `<tr><td colspan="7">${noDataMessage}</td></tr>`;
      return;
  }

  paginatedData.forEach(appointment => {
      const row = document.createElement('tr');
      row.innerHTML = `
          <td>${appointment.Appointment_ID}</td>
          <td>${formatDate(appointment.DATE)}</td>
          <td>${formatTime(appointment.TIME)}</td>
          <td>${appointment.Services}</td>
          <td>${appointment['Staff_Assigned']}</td>
          <td>₱${number_format(appointment.Price, 2)}</td>
          
          <td>
              <div class="action-buttons">
                 <button class="resched" title="Reschedule Appointment" onclick="openReschedModal(${appointment.Appointment_ID})">
                    <img src="./Assests/reschedicon.png" alt="Reschedule" width="50">
                 </button>
                  <button class="cancel" title="Refund Appointment" onclick="openRefundModal(${appointment.Appointment_ID})">
                      <img src="./Assests/cancelicon.png" alt="Refund" width="50">
                  </button>
              </div>
          </td>
      `;
      tableBody.appendChild(row);
  });

  renderPagination(data.length, currentPageSchedule, 'schedule');
}


// Function to populate appointment history in the table
function populateUserHistory(tableId, data, noDataMessage) {
    const tableBody = document.getElementById(tableId);
    if (!tableBody) {
        console.error(`Error: Element with ID '${tableId}' not found.`);
        return;
    }

    tableBody.innerHTML = ""; // Clear previous content

    const paginatedData = paginateHistory(data, currentPageHistory);

    if (!paginatedData || paginatedData.length === 0 || data.error) {
        tableBody.innerHTML = `<tr><td colspan="6">${noDataMessage}</td></tr>`;
        return;
    }

    paginatedData.forEach(appointment => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${appointment.Appointment_ID}</td>
            <td>${formatDate(appointment.DATE)}</td>
            <td>${formatTime(appointment.TIME)}</td>
            <td>${appointment.Services}</td>
            <td>${appointment['Staff_Assigned']}</td>
            <td>₱${number_format(appointment.Price, 2)}</td>
            <td>${appointment.Status}</td> 
            <td>${appointment.Refund_Reply}</td> 
        `;
        tableBody.appendChild(row);
    });

    renderPagination(data.length, currentPageHistory, 'history');
}

// Function to format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
}

// Function to format time
function formatTime(timeString) {
    const time = new Date(`1970-01-01T${timeString}`);
    return time.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
}

// Function to format numbers
function number_format(number, decimals) {
    return parseFloat(number).toFixed(decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,' );
}

// Event listener for closing the modal
document.querySelector('.user-appointment-close').addEventListener('click', closeUserAppointmentModal);

//Reschedule
// Open reschedule modal with appointment ID
function openReschedModal(appointmentId) {
  document.getElementById("reschedAppointmentId").value = appointmentId;
  document.getElementById("rescheduleModal").classList.add("show");
}

// Close reschedule modal
function closeRescheduleModal() {
  document.getElementById("rescheduleModal").classList.remove("show");
}

// Open reschedule calendar modal
function openReschedCalendar() {
  document.getElementById("calendarModalResched").style.display = "flex";
  loadReschedCalendar('current');
}

// Close reschedule calendar modal
function closeReschedCalendar() {
  document.getElementById("calendarModalResched").style.display = "none";
}

// Load calendar for reschedule
let reschedSelectedMonth = new Date().getMonth() + 1;
let reschedSelectedYear = new Date().getFullYear();

function loadReschedCalendar(type) {
  const currentDate = new Date();
  const currentMonth = currentDate.getMonth() + 1;
  const currentYear = currentDate.getFullYear();
  const nextMonth = currentMonth === 12 ? 1 : currentMonth + 1;
  const nextYear = currentMonth === 12 ? currentYear + 1 : currentYear;

  if (type === 'next') {
      // Only allow going to next month if not already there
      if (!(reschedSelectedMonth === nextMonth && reschedSelectedYear === nextYear)) {
          reschedSelectedMonth = nextMonth;
          reschedSelectedYear = nextYear;
      } else {
          return; // Already showing next month, don't proceed
      }
  } else {
      // Reset to current month
      reschedSelectedMonth = currentMonth;
      reschedSelectedYear = currentYear;
  }

  fetch(`fetch_calendar.php?month=${reschedSelectedMonth}&year=${reschedSelectedYear}`)
      .then(response => response.json())
      .then(data => generateReschedCalendar(data));
}

// Generate calendar HTML for reschedule
function generateReschedCalendar(data) {
  let daysInMonth = new Date(reschedSelectedYear, reschedSelectedMonth, 0).getDate();
  let firstDay = new Date(reschedSelectedYear, reschedSelectedMonth - 1, 1).getDay();
  let today = new Date();
  today.setHours(0, 0, 0, 0);

  let monthLabel = new Date(reschedSelectedYear, reschedSelectedMonth - 1).toLocaleString('en-us', { month: 'long', year: 'numeric' });

  // Calculate current and next month for navigation control
  const currentDate = new Date();
  const currentMonth = currentDate.getMonth() + 1;
  const currentYear = currentDate.getFullYear();
  const nextMonth = currentMonth === 12 ? 1 : currentMonth + 1;
  const nextYear = currentMonth === 12 ? currentYear + 1 : currentYear;
  const isNextMonth = reschedSelectedMonth === nextMonth && reschedSelectedYear === nextYear;

  let html = `<div class="calendar-nav">
    <h3 class='calendar-month-label'>${monthLabel}</h3>
  </div>`;
  
  html += "<div class='calendar-weekdays'>";
  ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].forEach(day => {
      html += `<div class='weekday-label'>${day}</div>`;
  });
  html += "</div><div class='calendar-days-grid'>";

  for (let i = 0; i < firstDay; i++) {
      html += "<div class='blank-day'></div>";
  }

  for (let day = 1; day <= daysInMonth; day++) {
      let dateStr = `${reschedSelectedYear}-${String(reschedSelectedMonth).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
      let className = "calendar-day";
      let current = new Date(reschedSelectedYear, reschedSelectedMonth - 1, day);

      if (current < today) {
          className += " past-date";
      } else if (data.closedDates.includes(dateStr) || data.disabledWeekdays.includes(current.toLocaleDateString('en-US', { weekday: 'long' }))) {
          className += " unavailable";
      } else if ((data.bookings[dateStr] || 0) >= 10) {
          className += " booked";
      } else {
          className += " available";
      }

      html += `<div class="${className}" onclick="${current >= today ? `selectReschedDate('${dateStr}')` : ''}">${day}</div>`;
  }

  html += "</div>";
  document.getElementById("calendarContainerResched").innerHTML = html;
}

// Select reschedule date and fetch available times
function selectReschedDate(date) {
  document.getElementById("reschedDateInput").value = date;
  closeReschedCalendar();

  fetch(`fetch_timeslots.php?date=${date}`)
      .then(response => response.json())
      .then(slots => {
          const timeSelect = document.getElementById('reschedTimeSelect');
          timeSelect.innerHTML = '<option value="">Select Time</option>';

          if (slots.length === 0) {
              timeSelect.innerHTML = '<option>No available time slots</option>';
          } else {
              slots.forEach(slot => {
                  const option = document.createElement('option');
                  option.value = slot.value;
                  option.textContent = slot.display;
                  timeSelect.appendChild(option);
              });
          }
      });
}

document.addEventListener('DOMContentLoaded', function() {
  loadReschedCalendar();
});

// Submit reschedule form
function submitReschedule(event) {
  event.preventDefault();
  
  const appointmentId = document.getElementById("reschedAppointmentId").value;
  const newDate = document.getElementById("reschedDateInput").value;
  const newTime = document.getElementById("reschedTimeSelect").value;

  if (!appointmentId || !newDate || !newTime) {
      alert("Please complete all fields.");
      return;
  }

  const formData = new FormData();
  formData.append("appointmentId", appointmentId);
  formData.append("newDate", newDate);
  formData.append("newTime", newTime);

  fetch("update_reschedule.php", {
      method: "POST",
      body: formData
  }).then(response => response.json())
    .then(data => {
      if (data.success) {
          alert("Appointment rescheduled successfully.");
          closeRescheduleModal();
          // Optional: Refresh appointments table
          location.reload();
      } else {
          alert("Failed to reschedule. Please try again.");
      }
  });
}


//Refund 
let currentAppointmentIdForRefund = null;

// Function to open the refund modal and store the appointment ID
function openRefundModal(appointmentId) {
    currentAppointmentIdForRefund = appointmentId;
    document.getElementById('refundModal').style.display = 'flex';
}

// Function to close the refund modal
function closeRefundModal() {
    document.getElementById('refundModal').style.display = 'none';
    currentAppointmentIdForRefund = null;
    document.getElementById('refundReason').value = '';
}

// Function to process the refund
function processRefund() {
    const reason = document.getElementById('refundReason').value.trim();
    
    if (!reason) {
        alert('Please specify a reason for cancellation.');
        return;
    }

    if (!currentAppointmentIdForRefund) {
        alert('No appointment selected for refund.');
        return;
    }

    // Get current date and time in 24-hour format
    const now = new Date();
    const refundDate = now.toISOString().split('T')[0]; // YYYY-MM-DD
    const refundTime = now.toTimeString().split(' ')[0]; // HH:MM:SS

    // Prepare the data to send
    const refundData = {
        appointmentId: currentAppointmentIdForRefund,
        reason: reason,
        refundDate: refundDate,
        refundTime: refundTime
    };

    // Send the refund request to the server
    fetch('process_refund.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(refundData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Refund request submitted successfully!');
            closeRefundModal();
            fetchUserAppointments(); // Refresh the appointments list
            fetchUserAppointmentHistory(); // Refresh the history list
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while processing your request.');
    });
}








