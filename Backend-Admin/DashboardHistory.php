<?php
session_start();
include 'database.php';

// Initialize filter variables
$selectedMonth = isset($_GET['month']) ? (int)$_GET['month'] : '';
$selectedDay = isset($_GET['day']) ? (int)$_GET['day'] : '';
$selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : '';
$searchId = isset($_GET['search_id']) ? $conn->real_escape_string($_GET['search_id']) : '';

// Get unique months, days, years from the database for dropdowns
$sql_dates = "SELECT 
                DISTINCT 
                MONTH(DATE) as month, 
                DAY(DATE) as day, 
                YEAR(DATE) as year 
              FROM appointment_history 
              ORDER BY year DESC, month DESC, day DESC";
$result_dates = $conn->query($sql_dates);

$months = [];
$days = [];
$years = [];

while ($row = $result_dates->fetch_assoc()) {
    if (!in_array($row['month'], $months)) {
        $months[] = $row['month'];
    }
    if (!in_array($row['day'], $days)) {
        $days[] = $row['day'];
    }
    if (!in_array($row['year'], $years)) {
        $years[] = $row['year'];
    }
}

// Sort the arrays
sort($months, SORT_NUMERIC);
sort($days, SORT_NUMERIC);
rsort($years, SORT_NUMERIC); // Years should be descending

// Build WHERE conditions for filters
$conditions = [];
if (!empty($selectedMonth)) {
    $conditions[] = "MONTH(DATE) = " . $conn->real_escape_string($selectedMonth);
}
if (!empty($selectedDay)) {
    $conditions[] = "DAY(DATE) = " . $conn->real_escape_string($selectedDay);
}
if (!empty($selectedYear)) {
    $conditions[] = "YEAR(DATE) = " . $conn->real_escape_string($selectedYear);
}
if (!empty($searchId)) {
    $conditions[] = "Appointment_ID LIKE '%$searchId%'";
}

$whereClause = '';
if (!empty($conditions)) {
    $whereClause = "WHERE " . implode(' AND ', $conditions);
}
// Check if logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || 
    !in_array($_SESSION['role'], ['Owner', 'Admin', 'SuperAdmin'])) {
    header("Location: ../index.php");
    exit();
}


// Pagination setup
$results_per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

// Fetch user info from session
$fullName = $_SESSION['full_name'] ?? '';
$address = $_SESSION['address'] ?? '';
$barangay = $_SESSION['barangay'] ?? '';
$city = $_SESSION['city'] ?? '';
$region = $_SESSION['region'] ?? '';
$postalCode = $_SESSION['postal_code'] ?? '';
$email = $_SESSION['email'] ?? '';
$phonenumber = $_SESSION['phonenumber'] ?? '';
$role = $_SESSION['role'] ?? ''; // Get user role

// Query for the appointment history (with pagination)
$sql_history = "SELECT Appointment_ID, Name, DATE, TIME, Staff_Assigned, PhoneNumber, Services, Price, Status 
                FROM appointment_history 
                $whereClause
                ORDER BY DATE DESC, TIME DESC 
                LIMIT $start_from, $results_per_page";
$result_history = $conn->query($sql_history);

// Fetch total records to calculate the number of pages (with filters)
$sql_total = "SELECT COUNT(*) AS total FROM appointment_history $whereClause";
$result_total = $conn->query($sql_total);
$total_row = $result_total->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $results_per_page);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../Assests/logonisa-32.png" type="image/png">
    <link rel="icon" href="../Assests/logonisa-16.png" type="image/png">
    <title>Appointment Schedule</title>
    <link rel="stylesheet" href="../Frontend-Admin/DashboardHistory.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
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

<!-- Appointment History -->
<div class="schedule-container">
    <h2>Appointment Schedule History</h2>

     <!-- Filter Form -->
     <div class="filters mb-4">
        <form method="get" action="DashboardHistory.php" class="row g-3" id="filterForm">
            <div class="col-md-2">
                <label for="month" class="form-label">Select Month</label>
                <select class="form-select" id="month" name="month">
                    <option value="">Months</option>
                    <?php foreach ($months as $month): 
                        $monthName = date("F", mktime(0, 0, 0, $month, 10));
                    ?>
                        <option value="<?php echo $month; ?>" <?php echo ($selectedMonth == $month) ? 'selected' : ''; ?>>
                            <?php echo $monthName; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="day" class="form-label">Select Day</label>
                <select class="form-select" id="day" name="day">
                    <option value="">Days</option>
                    <?php foreach ($days as $day): ?>
                        <option value="<?php echo $day; ?>" <?php echo ($selectedDay == $day) ? 'selected' : ''; ?>>
                            <?php echo $day; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="year" class="form-label">Select Year</label>
                <select class="form-select" id="year" name="year">
                    <option value="">Years</option>
                    <?php foreach ($years as $year): ?>
                        <option value="<?php echo $year; ?>" <?php echo ($selectedYear == $year) ? 'selected' : ''; ?>>
                            <?php echo $year; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="search_id" class="form-label">Search Appointment ID</label>
                <input type="text" class="form-control" id="search_id" name="search_id" 
                       placeholder="Enter Appointment ID" value="<?php echo htmlspecialchars($searchId); ?>">
            </div>
            
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Apply Filters</button>
                <?php if (!empty($selectedMonth) || !empty($selectedDay) || !empty($selectedYear) || !empty($searchId)): ?>
                    <a href="DashboardHistory.php" class="btn btn-secondary">Clear Filters</a>
                <?php endif; ?>
            </div>
            
            <input type="hidden" name="page" value="1" id="pageInput">
        </form>
    </div>

    
    <table class="appointment-table">
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>Date</th>
                <th>Time</th>
                <th>Customer Name</th>
                <th>Contact Number</th>
                <th>Service</th>
                <th>Price</th>
                <th>Staff Name</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody id="history-body">
        <?php
        if ($result_history->num_rows > 0) {
            while($row = $result_history->fetch_assoc()) {
                // Format the date and time
                $time = date("g:i A", strtotime($row["TIME"]));
                $formatted_date = date("F d, Y", strtotime($row["DATE"]));

                echo '<tr>';
                echo '<td class="transaction-id">' . $row["Appointment_ID"] . '</td>';
                echo '<td>' . $formatted_date . '</td>';
                echo '<td>' . $time . '</td>';
                echo '<td>' . $row["Name"] . '</td>';
                echo '<td>' . $row["PhoneNumber"] . '</td>';
                echo '<td>' . $row["Services"] . '</td>';
                echo '<td>₱' . number_format($row["Price"], 2) . '</td>';
                echo '<td>' . $row["Staff_Assigned"] . '</td>';
                echo '<td>' . $row["Status"] . '</td>';
                echo '</tr>';
            }
        } else {
            echo "<tr><td colspan='9'>No appointments found</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php
        // Pagination links
        if ($page > 1) {
            echo "<a href='DashboardHistory.php?page=" . ($page - 1) . 
                 (!empty($selectedMonth) ? "&month=$selectedMonth" : "") .
                 (!empty($selectedDay) ? "&day=$selectedDay" : "") .
                 (!empty($selectedYear) ? "&year=$selectedYear" : "") .
                 (!empty($searchId) ? "&search_id=$searchId" : "") .
                 "'>Prev</a>";
        }

        for ($i = 1; $i <= $total_pages; $i++) {
            $current_page = ($i == $page) ? 'class="current-page"' : '';
            echo "<a href='DashboardHistory.php?page=$i" .
                 (!empty($selectedMonth) ? "&month=$selectedMonth" : "") .
                 (!empty($selectedDay) ? "&day=$selectedDay" : "") .
                 (!empty($selectedYear) ? "&year=$selectedYear" : "") .
                 (!empty($searchId) ? "&search_id=$searchId" : "") .
                 "' $current_page>$i</a>";
        }

        if ($page < $total_pages) {
            echo "<a href='DashboardHistory.php?page=" . ($page + 1) .
                 (!empty($selectedMonth) ? "&month=$selectedMonth" : "") .
                 (!empty($selectedDay) ? "&day=$selectedDay" : "") .
                 (!empty($selectedYear) ? "&year=$selectedYear" : "") .
                 (!empty($searchId) ? "&search_id=$searchId" : "") .
                 "'>Next</a>";
        }
        ?>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Get form elements
    const filterForm = document.getElementById('filterForm');
    const searchInput = document.getElementById('search_id');
    const monthSelect = document.getElementById('month');
    const daySelect = document.getElementById('day');
    const yearSelect = document.getElementById('year');
    const pageInput = document.getElementById('pageInput');
    
    // Function to submit form with AJAX
    function updateTable() {
        const formData = new FormData(filterForm);
        
        // Reset to page 1 when filters change
        pageInput.value = 1;
        
        fetch('DashboardHistory.php?' + new URLSearchParams(formData).toString())
            .then(response => response.text())
            .then(html => {
                // Create a temporary DOM to parse the response
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Update the table body
                document.getElementById('history-body').innerHTML = 
                    doc.getElementById('history-body').innerHTML;
                
                // Update the pagination
                const pagination = doc.querySelector('.pagination');
                if (pagination) {
                    document.querySelector('.pagination').innerHTML = pagination.innerHTML;
                }
            })
            .catch(error => console.error('Error:', error));
    }
    
    // Add event listeners for real-time filtering
    searchInput.addEventListener('input', function() {
        updateTable();
    });
    
    // Add event listeners for dropdown changes
    monthSelect.addEventListener('change', updateTable);
    daySelect.addEventListener('change', updateTable);
    yearSelect.addEventListener('change', updateTable);
    
    // Handle pagination clicks (prevent default and use AJAX)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            const url = new URL(e.target.href);
            pageInput.value = url.searchParams.get('page') || 1;
            
            // Update other filter values from the URL
            if (url.searchParams.has('month')) {
                monthSelect.value = url.searchParams.get('month');
            }
            if (url.searchParams.has('day')) {
                daySelect.value = url.searchParams.get('day');
            }
            if (url.searchParams.has('year')) {
                yearSelect.value = url.searchParams.get('year');
            }
            if (url.searchParams.has('search_id')) {
                searchInput.value = url.searchParams.get('search_id');
            }
            
            updateTable();
        }
    });
});
</script>
</body>
</html>