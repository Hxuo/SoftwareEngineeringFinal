<?php
session_start();
include 'database.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__ . "/../vendor/autoload.php";

// Check if logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || 
    !in_array($_SESSION['role'], ['Owner', 'Admin', 'SuperAdmin'])) {
    header("Location: ../index.php");
    exit();
}

// Assign session variables
$fullName = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : '';
$address = isset($_SESSION['address']) ? $_SESSION['address'] : '';
$barangay = isset($_SESSION['barangay']) ? $_SESSION['barangay'] : '';
$city = isset($_SESSION['city']) ? $_SESSION['city'] : '';
$region = isset($_SESSION['region']) ? $_SESSION['region'] : '';
$postalCode = isset($_SESSION['postal_code']) ? $_SESSION['postal_code'] : '';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$phonenumber = isset($_SESSION['phonenumber']) ? $_SESSION['phonenumber'] : '';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';

// Pagination setup
$results_per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

// Fetch staff records for the current page
$result = $conn->query("SELECT * FROM Staff LIMIT $start_from, $results_per_page");

// Fetch total records to calculate total pages
$sql_total = "SELECT COUNT(*) AS total FROM Staff";
$result_total = $conn->query($sql_total);
$total_row = $result_total->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $results_per_page);

function sendRescheduleEmail($appointment, $newStaff, $newTime) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'softwareengineeringfinal@gmail.com';
        $mail->Password = 'pgvy bati jffn pbty'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        $mail->setFrom('softwareengineeringfinal@gmail.com', 'Aniah Brow Aesthetics');
        $mail->addAddress($appointment['Email']);
        $mail->isHTML(true);
        
        $formattedTime = date("g:i A", strtotime($newTime));
        
        $mail->Subject = 'Appointment Reschedule Notification';
        $mail->Body = "
            <p>Dear {$appointment['Name']},</p>
            
            <p>Due to unexpected circumstances, the staff assigned on your appointment (Appointment ID: {$appointment['Appointment_ID']}), 
            Mr/Mrs {$appointment['Staff_Assigned']}, won't be able to accommodate you. Because of this, your appointment will be moved 
            to {$formattedTime} with Mr/Mrs. {$newStaff}.</p>
            
            <p>If you can't attend at the new time, you can reschedule your appointment at your desired date and time. 
            We are sorry for the inconvenience.</p>
            
                <p><a href='http://localhost/SoftEngFinalV9/Backend-User/reschedule_appointment.php?appointment_id={$appointment['Appointment_ID']}'>Click here for rescheduling</a></p>
            
            <p>Best regards,<br>Aniah Brow Aesthetics</p>
        ";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

// Handle staff status change
if (isset($_GET['toggle_status'])) {
    $staff_id = $_GET['toggle_status'];
    
    // Get the current status
    $staff = $conn->query("SELECT Status, Staff_Name FROM Staff WHERE Staff_ID = $staff_id")->fetch_assoc();
    
    // Toggle the status
    $new_status = ($staff['Status'] === 'On-Leave') ? 'On-Duty' : 'On-Leave';
    $conn->query("UPDATE Staff SET Status = '$new_status' WHERE Staff_ID = $staff_id");
    
    // If staff is being set to On-Leave, reassign their appointments
    if ($new_status === 'On-Leave') {
        // Get all active appointments for this staff from today onward
        $today = date('Y-m-d');
        $appointments = $conn->query("
            SELECT * FROM Appointment 
            WHERE Staff_Assigned = '{$staff['Staff_Name']}' 
            AND DATE >= '$today'
            AND Status != 'Cancelled'
        ");
        
        while ($appointment = $appointments->fetch_assoc()) {
            // Find available staff (business hours 1PM-9PM in 24-hour format)
            $businessHours = ['13:00:00', '14:00:00', '15:00:00', '16:00:00', '17:00:00', '18:00:00', '19:00:00', '20:00:00', '21:00:00'];
            
            // Try to find staff available at original time
            $availableStaff = null;
            $onDutyStaff = $conn->query("
                SELECT Staff_ID, Staff_Name 
                FROM Staff 
                WHERE Status = 'On-Duty' AND Staff_ID != $staff_id
            ");
            
            while ($staffMember = $onDutyStaff->fetch_assoc()) {
                $conflict = $conn->query("
                    SELECT COUNT(*) as count 
                    FROM Appointment 
                    WHERE Staff_Assigned = '{$staffMember['Staff_Name']}' 
                    AND DATE = '{$appointment['DATE']}' 
                    AND TIME = '{$appointment['TIME']}'
                    AND Status != 'Cancelled'
                ")->fetch_assoc();
                
                if ($conflict['count'] == 0) {
                    $availableStaff = $staffMember;
                    break;
                }
            }
            
            if ($availableStaff) {
                // Found staff at original time
                $conn->query("
                    UPDATE Appointment 
                    SET Staff_Assigned = '{$availableStaff['Staff_Name']}'
                    WHERE Appointment_ID = {$appointment['Appointment_ID']}
                ");
                
                sendRescheduleEmail($appointment, $availableStaff['Staff_Name'], $appointment['TIME']);
            } else {
                // Find next available time slot
                $currentTimeIndex = array_search($appointment['TIME'], $businessHours);
                if ($currentTimeIndex === false) $currentTimeIndex = 0;
                
                $foundSlot = false;
                for ($i = $currentTimeIndex + 1; $i < count($businessHours); $i++) {
                    $newTime = $businessHours[$i];
                    
                    foreach ($conn->query("SELECT Staff_ID, Staff_Name FROM Staff WHERE Status = 'On-Duty' AND Staff_ID != $staff_id") as $staffMember) {
                        $conflict = $conn->query("
                            SELECT COUNT(*) as count 
                            FROM Appointment 
                            WHERE Staff_Assigned = '{$staffMember['Staff_Name']}' 
                            AND DATE = '{$appointment['DATE']}' 
                            AND TIME = '$newTime'
                            AND Status != 'Cancelled'
                        ")->fetch_assoc();
                        
                        if ($conflict['count'] == 0) {
                            // Update appointment
                            $conn->query("
                                UPDATE Appointment 
                                SET Staff_Assigned = '{$staffMember['Staff_Name']}', 
                                    TIME = '$newTime'
                                WHERE Appointment_ID = {$appointment['Appointment_ID']}
                            ");
                            
                            sendRescheduleEmail($appointment, $staffMember['Staff_Name'], $newTime);
                            $foundSlot = true;
                            break 2;
                        }
                    }
                }
                
                if (!$foundSlot) {
                    // Try next day
                    $nextDate = date('Y-m-d', strtotime($appointment['DATE'] . ' +1 day'));
                    $newTime = $businessHours[0];
                    
                    foreach ($conn->query("SELECT Staff_ID, Staff_Name FROM Staff WHERE Status = 'On-Duty' AND Staff_ID != $staff_id") as $staffMember) {
                        $conflict = $conn->query("
                            SELECT COUNT(*) as count 
                            FROM Appointment 
                            WHERE Staff_Assigned = '{$staffMember['Staff_Name']}' 
                            AND DATE = '$nextDate' 
                            AND TIME = '$newTime'
                            AND Status != 'Cancelled'
                        ")->fetch_assoc();
                        
                        if ($conflict['count'] == 0) {
                            // Update appointment
                            $conn->query("
                                UPDATE Appointment 
                                SET Staff_Assigned = '{$staffMember['Staff_Name']}', 
                                    DATE = '$nextDate',
                                    TIME = '$newTime'
                                WHERE Appointment_ID = {$appointment['Appointment_ID']}
                            ");
                            
                            sendRescheduleEmail($appointment, $staffMember['Staff_Name'], "$nextDate $newTime");
                            break;
                        }
                    }
                }
            }
        }
    }
    
    header("Location: DashboardStaff1.php");
    exit();
}

// Delete staff
if (isset($_GET['delete'])) {
    $staff_id = $_GET['delete'];
    $conn->query("DELETE FROM Staff WHERE Staff_ID = $staff_id");
    header("Location: DashboardStaff1.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="icon" href="../Assests/logonisa-32.png" type="image/png">
<link rel="icon" href="../Assests/logonisa-16.png" type="image/png">
    <title>Staff List</title>
    <link rel="stylesheet" href="../Frontend-Admin/DashboardStaff1.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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

<h2 class="section-title">Staff List</h2>
<div class="table-wrapper">
<div class="button-container">
        <!-- Add Staff Button -->
        <div class="add-staff-container">
            <button onclick="openAddStaffModal()" class="add-staff-btn">Add Staff
                <img src="../Assests/addicon.png" alt="Add Staff" width="50">
            </button>
        </div>

        <!-- Management Password Button -->
        <div class="add-management-password-container">
            <button onclick="openAddManagePasswordModal()" class="add-management-password-btn">Management Password
                <img src="../Assests/addicon.png" alt="Add Staff" width="50">
            </button>
        </div>
    </div>

    <!-- Table -->
<div class="table-container">
    <table border="1">
        <thead>
            <tr>
                <th>Staff Name</th>
                <th>Contact Number</th>
                <th>Staff Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['Staff_Name']); ?></td>
                    <td><?= htmlspecialchars($row['PhoneNumber']); ?></td>
                    <td><?= htmlspecialchars($row['Status']); ?></td>
                    <td>
                        <a href="DashboardStaff1.php?toggle_status=<?= $row['Staff_ID']; ?>" 
                           onclick="return confirm('Change staff status?')" class="status-btn">
                           <?php if ($row['Status'] === 'On-Leave'): ?>
                               <img src="../Assests/dutyicon.png" alt="Put on Duty" title="Put on Duty" style="width: 40px; height: 40px;">
                           <?php else: ?>
                               <img src="../Assests/leaveicon.png" alt="Put on Leave" title="Put on Leave" style="width: 40px; height: 40px;">
                           <?php endif; ?>
                        </a>
                        
                        <a href="DashboardStaff1.php?delete=<?= $row['Staff_ID']; ?>" 
                           onclick="return confirm('Are you sure you want to delete this staff?')">
                            <img src="../Assests/deleteicon.png" alt="Delete" style="width: 40px; height: 40px;">
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination">
        <?php
        // Display "Prev" link
        if ($page > 1) {
            echo "<a href='DashboardStaff1.php?page=" . ($page - 1) . "'>Prev</a>";
        }

        // Display page numbers
        for ($i = 1; $i <= $total_pages; $i++) {
            $current_page = ($i == $page) ? 'class="current-page"' : '';
            echo "<a href='DashboardStaff1.php?page=$i' $current_page>$i</a>";
        }

        // Display "Next" link
        if ($page < $total_pages) {
            echo "<a href='DashboardStaff1.php?page=" . ($page + 1) . "'>Next</a>";
        }
        ?>
    </div>
</div>

<!-- Add Staff Modal -->
<div id="addStaffModal" class="add-staff-modal">
    <div class="add-staff-modal-content">
        <form id="staffForm" method="POST" action="add-staff.php">
            <label for="staffName">Staff Name:</label>
            <input type="text" id="staffName" name="staff_name" placeholder="Enter Staff Name" required>
            
            <label for="phoneNumber">Phone Number:</label>
            <input type="text" id="phoneNumber" name="phone_number" placeholder="Enter 11-digit Phone Number" 
                   maxlength="11" oninput="validatePhoneNumber(this)" required>
            
            <div class="add-staff-modal-buttons">
                <button type="button" onclick="validateAndConfirm()">Add</button>
                <button type="button" onclick="closeAddStaffModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmationModal" class="add-staff-modal">
    <div class="add-staff-modal-content">
        <p>Are you sure you want to add this staff?</p>
        <div class="add-staff-modal-buttons">
            <button type="button" onclick="submitForm()">Yes</button>
            <button type="button" onclick="closeConfirmationModal()">No</button>
        </div>
    </div>
</div>

<!-- Management Password Modal -->
<div id="managePasswordModal" class="management-modal">
    <div class="management-modal-content">
        <span class="management-close-btn" onclick="closeManagePasswordModal()">&times;</span>
        <h2>Management Password</h2>
        
        <div id="manageCurrentPasswordView">
            <div class="current-password-display">
                <label>Current Password:</label>
                <div class="password-display-wrapper">
                    <input type="password" id="currentPasswordDisplay" readonly>
                    <span class="password-toggle" onclick="togglePasswordVisibility('currentPasswordDisplay')">
                        <img src="../Assests/hide.png" alt="Show Password" width="20">
                    </span>
                </div>
            </div>
            <button type="button" class="management-change-pw-btn" onclick="showManagePasswordChangeForm()">Change Password</button>
        </div>
        
        <form id="managePasswordChangeForm" style="display: none;">
            <div class="management-input-group">
                <label for="newManagePassword">New Password:</label>
                <div class="management-password-wrapper">
                    <input type="password" id="newManagePassword" name="newManagePassword" required>
                    <span class="password-toggle" onclick="togglePasswordVisibility('newManagePassword')">
                        <img src="../Assests/show.png" alt="Show Password" width="20">
                    </span>
                </div>
            </div>
            
            <div class="management-input-group">
                <label for="confirmManagePassword">Confirm New Password:</label>
                <div class="management-password-wrapper">
                    <input type="password" id="confirmManagePassword" name="confirmManagePassword" required>
                    <span class="password-toggle" onclick="togglePasswordVisibility('confirmManagePassword')">
                        <img src="../Assests/show.png" alt="Show Password" width="20">
                    </span>
                </div>
            </div>
            
            <div class="management-pw-hint">
                Must be at least 8 characters with 1 uppercase letter and 1 special symbol
            </div>
            
            <div class="management-form-actions">
                <button type="button" class="management-confirm-btn" onclick="updateManagementPassword()">Confirm Changes</button>
                <button type="button" class="management-cancel-btn" onclick="cancelPasswordChange()">Cancel</button>
            </div>
        </form>
    </div>
</div>


<script src="../Frontend-Admin/DashboardStaff1.js"></script>
</body>
</html>