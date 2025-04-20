<?php
include 'database.php'; // I-include ang database connection

// Fetch all staff from database
$result = $conn->query("SELECT * FROM Staff");

if(isset($_GET['toggle_status'])) {
    $staff_id = $_GET['toggle_status'];
    
    // Kunin ang kasalukuyang status
    $staff = $conn->query("SELECT Status FROM Staff WHERE Staff_ID = $staff_id")->fetch_assoc();
    
    // Palitan ang status
    $new_status = ($staff['Status'] === 'On-Leave') ? 'On-Duty' : 'On-Leave';
    $conn->query("UPDATE Staff SET Status = '$new_status' WHERE Staff_ID = $staff_id");
    
    header("Location: Staff-List.php");
    exit();
}

if(isset($_GET['delete'])) {
    $staff_id = $_GET['delete'];
    $conn->query("DELETE FROM Staff WHERE Staff_ID = $staff_id");
    header("Location: Staff-List.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Staff List</title>
</head>
<body>
    <h2>Staff List</h2>
    <table border="1">
        <tr>
            <th>Staff Name</th>
            <th>Staff Status</th>
            <th>Action</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['Staff_Name']); ?></td>
            <td><?= htmlspecialchars($row['Status']); ?></td>
            <td>
                <!-- Dynamic Button for Leave/Duty -->
                <a href="Staff-List.php?toggle_status=<?= $row['Staff_ID']; ?>" 
                   onclick="return confirm('Change staff status?')">
                   <?= ($row['Status'] === 'On-Leave') ? 'Put on Duty' : 'Put on Leave'; ?>
                </a> |
                <!-- Delete Button -->
                <a href="Staff-List.php?delete=<?= $row['Staff_ID']; ?>" 
                   onclick="return confirm('Are you sure you want to delete this staff?')">
                   Delete Staff
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <br>
    <button onclick="window.location.href='Add-Staff.php'">Add Staff</button>
</body>
</html>
