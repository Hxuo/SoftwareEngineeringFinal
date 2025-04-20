<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Frontend-Admin/ModalTrial.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Simple Modal Example</title>
</head>

<body>
<nav class="navbar">
    <div class="logo">
        <img src="your-logo.png" alt="Logo"> <!-- Change to your actual logo path -->
        APPOINT-IT 
    </div>
    <ul class="nav-links">
    <li>
        <a href="DashboardSched.php">Schedule</a></li>
        <li><a href="DashboardStaff.php">Staff</a></li>
        <li><a href="Appointment.php">Appointment</a></li>
        <li><a href="DashboardHistory.php">History</a></li>
        <li><a href="DashboardServices.php">Services</a></li>
        <li><a href="logout.php" class="logout">Logout</a></li>
    </ul>
</nav>


<!-- Staff Schedule Table -->
<div class="staff-schedule">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Staff ID</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#001</td>
                    <td>Juneil</td>
                    <td>On-duty</td>
                    <td>
                        <button class="delete-btn" onclick="openModal('deleteModal')">DELETE</button>
                        <button class="edit-btn" onclick="openModal('editModal')">EDIT</button>
                    </td>
                </tr>
                <tr>
                    <td>#002</td>
                    <td>Lex</td>
                    <td>Day-off</td>
                    <td>
                        <button class="delete-btn" onclick="openModal('deleteModal')">DELETE</button>
                        <button class="edit-btn" onclick="openModal('editModal')">EDIT</button>
                    </td>
                </tr>
                <tr>
                    <td>#003</td>
                    <td>AwasTzy</td>
                    <td>On-duty</td>
                    <td>
                        <button class="delete-btn" onclick="openModal('deleteModal')">DELETE</button>
                        <button class="edit-btn" onclick="openModal('editModal')">EDIT</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <button class="add-btn" onclick="openModal('addModal')">ADD</button>
</div>

<!-- Edit Modal -->
<div class="modal-overlay" id="editModal">
    <div class="modal">
        <h2>Staff Name</h2>
        <input type="text" placeholder="Edit Staff Name:">
        <br><br>
        <button class="modal-btn confirm-btn">Save</button>
        <button class="modal-btn cancel-btn" onclick="closeModal('editModal')">Cancel</button>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal">
        <h2>Confirm Delete</h2>
        <p>Are you sure you want to remove this staff</p>
        <button class="modal-btn confirm-btn">Yes, Remove</button>
        <button class="modal-btn cancel-btn" onclick="closeModal('deleteModal')">Cancel</button>
    </div>
</div>

<!-- Add Modal -->
<div class="modal-overlay" id="addModal">
    <div class="modal">
        <h2>New Staff</h2>
        <input type="text" placeholder="Enter staff name">
        <br><br>
        <button class="modal-btn confirm-btn">Add</button>
        <button class="modal-btn cancel-btn" onclick="closeModal('addModal')">Cancel</button>
    </div>
</div>

<script src="../Frontend-Admin/ModalTrial.js">

</script>

</body>
</html>


