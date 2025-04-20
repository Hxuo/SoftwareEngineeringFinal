function validatePhoneNumber(input) {
    // Remove any non-digit characters
    input.value = input.value.replace(/\D/g, '');
    
    // Limit to 11 digits
    if (input.value.length > 11) {
        input.value = input.value.slice(0, 11);
    }
}


function openAddStaffModal() {
    document.getElementById('addStaffModal').style.display = 'flex';
}

function closeAddStaffModal() {
    document.getElementById('addStaffModal').style.display = 'none';
}

document.addEventListener('click', function(event) {
    const modal = document.getElementById('addStaffModal');
    if (event.target === modal) {
        closeAddStaffModal();
    }
  });

function openConfirmationModal() {
    document.getElementById('confirmationModal').style.display = 'flex';
}

function closeConfirmationModal() {
    document.getElementById('confirmationModal').style.display = 'none';
}

function validateAndConfirm() {
    var staffName = document.getElementById('staffName').value.trim();
    var phoneNumber = document.getElementById('phoneNumber').value.trim();
    
    if (staffName === "") {
        alert("Please enter a staff name before proceeding.");
        return;
    }
    
    if (phoneNumber === "" || phoneNumber.length !== 11) {
        alert("Please enter a valid 11-digit phone number.");
        return;
    }

    // Open confirmation modal
    closeAddStaffModal();
    openConfirmationModal();
}


function submitForm() {
    // Submit the form
    document.getElementById('staffForm').submit();
    closeConfirmationModal();
}

//Management Password
document.addEventListener('DOMContentLoaded', function() {
    // Load current password when page loads
    loadCurrentPassword();
});

function openAddManagePasswordModal() {
    document.getElementById('managePasswordModal').style.display = 'flex';
}

function closeManagePasswordModal() {
    document.getElementById('managePasswordModal').style.display = 'none';
    // Reset form and hide it when closing modal
    document.getElementById('managePasswordChangeForm').style.display = 'none';
    document.getElementById('manageCurrentPasswordView').style.display = 'flex';
    document.getElementById('newManagePassword').value = '';
    document.getElementById('confirmManagePassword').value = '';
}

document.addEventListener('click', function(event) {
    const modal = document.getElementById('managePasswordModal');
    if (event.target === modal) {
        closeManagePasswordModal();
    }
  });


function loadCurrentPassword() {
    // Fetch current password from server
    fetch('../Backend-Admin/getManagementPassword.php')
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                document.getElementById('currentPasswordDisplay').value = data.password;
            } else {
                alert('Error loading current password: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading current password');
        });
}

function showManagePasswordChangeForm() {
    document.getElementById('manageCurrentPasswordView').style.display = 'none';
    document.getElementById('managePasswordChangeForm').style.display = 'flex';
}

function cancelPasswordChange() {
    document.getElementById('managePasswordChangeForm').style.display = 'none';
    document.getElementById('manageCurrentPasswordView').style.display = 'flex';
    document.getElementById('newManagePassword').value = '';
    document.getElementById('confirmManagePassword').value = '';
}

function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling.querySelector('img'); // Get the img inside the toggle span
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.src = '../Assests/show.png'; // Show password (eye open)
        icon.alt = "Hide Password";
    } else {
        input.type = 'password';
        icon.src = '../Assests/hide.png'; // Hide password (eye closed)
        icon.alt = "Show Password";
    }
}

function updateManagementPassword() {
    const newPassword = document.getElementById('newManagePassword').value;
    const confirmPassword = document.getElementById('confirmManagePassword').value;

    // Validate password
    if (!validatePassword(newPassword)) {
        alert('Password must be at least 8 characters with 1 uppercase letter and 1 special symbol');
        return;
    }

    if (newPassword !== confirmPassword) {
        alert('Passwords do not match');
        return;
    }

    // Send new password to server
    const formData = new FormData();
    formData.append('newPassword', newPassword);

    fetch('../Backend-Admin/updateManagementPassword.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Password updated successfully');
            // Update current password display
            document.getElementById('currentPasswordDisplay').value = newPassword;
            // Reset form
            cancelPasswordChange();
        } else {
            alert('Error updating password: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating password');
    });
}

function validatePassword(password) {
    // At least 8 characters, 1 uppercase letter, and 1 special symbol
    const regex = /^(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}$/;
    return regex.test(password);
}