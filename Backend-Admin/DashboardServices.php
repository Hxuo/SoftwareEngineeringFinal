<?php
session_start();
include 'database.php';

// Check if logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || 
    !in_array($_SESSION['role'], ['Owner', 'Admin', 'SuperAdmin'])) {
    header("Location: ../index.php");
    exit();
}

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
$role = isset($_SESSION['role']) ? $_SESSION['role'] : ''; // Get user role
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../Assests/logonisa-32.png" type="image/png">
    <link rel="icon" href="../Assests/logonisa-16.png" type="image/png">
    <title>Appointment Schedule</title>
    <link rel="stylesheet" href="../Frontend-Admin/DashboardServices.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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

<div class="container">
    <h2 class="section-title">Services</h2>

<!-- Category Section -->
<div class="category-wrapper">
        <div class="category-container">
            <div class="category-box">
                CATEGORY
                <input type="text" id="categorySearch" class="category-search" placeholder="Search category..." onkeyup="searchCategory()">
            </div>
            <div class="category-box">
    VIEW
    <select class="category-dropdown" onchange="filterByCategory()">
        <option value="" selected disabled hidden>Select an option</option>
        <option value="all">ALL</option>
        <option value="services">SERVICES</option>
        <option value="promo">PROMO</option>
        <option value="package">PACKAGE</option>
    </select>
</div>

        <!-- ADD Category Box (In-line with others) -->
        <div class="category-box">
    ADD
    <select class="category-dropdown" id="categorySelect">
        <option value="" selected disabled hidden>Select an option</option>
        <option value="services">SERVICES</option>
        <option value="promo">PROMO</option>
        <option value="package">PACKAGE</option>
    </select>
</div>
</div>
    </div>

        




    <!-- Services Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Items ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="servicesTableBody">
                <!-- Data is dynamically inserted here -->
            </tbody>
        </table>
    </div>

    
    <!-- Pagination -->
    <div class="pagination">
        <button id="prevPage" onclick="changePage(-1)">Previous</button>
        <span id="pageIndicator">Page 1</span>
        <button id="nextPage" onclick="changePage(1)">Next</button>
    </div>


<!-- Modal -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle">Edit Item</h2>

        <!-- Modal content for edit -->
        <div id="editModalContent" style="display: none;">
            <form id="editForm">
                <label for="editName">Name:</label>
                <input type="text" id="editName" name="editName" required><br><br>

                <label for="editCategory">Category:</label>
                <input type="text" id="editCategory" name="editCategory" required><br><br>

                <label for="editPrice">Price:</label>
                <input type="number" id="editPrice" name="editPrice" required><br><br>

                <button type="submit" id="editConfirmBtn" class="confirm-btn">Confirm Edit</button>
                <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
            </form>
        </div>

        <!-- Modal content for delete -->
        <div id="deleteModalContent" style="display: none;">
            <p id="deleteDescription">Are you sure you want to delete this item?</p>
            <button class="confirm-btn" onclick="deleteItem()">Confirm Delete</button>
            <button class="cancel-btn" onclick="closeModal()">Cancel</button>
        </div>

    </div>
</div>

<!-- ADD MODAL (Hidden by default) -->
<div id="ModalAdd" class="ModalAdd">
    <div class="Modal-content-add">
        <button class="close-btn" onclick="closeAddModal()">✖</button>

        <!-- ADD SERVICE FORM -->
        <div id="addServiceContainer" class="form-container" style="display: none;">
            <h2 class="form-title">Add New Service</h2>
            <form action="Add-Services.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Select Category:</label>
                    <select name="service_category" required>
                        <option value="" selected disabled hidden>Select Category</option>
                        <option value="Facial">Facial</option>
                        <option value="Eyebrow">Eyebrow</option>
                        <option value="Eyelash">Eyelash</option>
                        <option value="Nail">Nail</option>
                        <option value="Skin">Skin</option>
                        <option value="Body">Body</option>
                        <option value="Makeup">Makeup</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Service Name:</label>
                    <input type="text" name="service_name" placeholder="Enter Name" required>
                </div>
                <div class="form-group">
                    <label>Service Description:</label>
                    <textarea name="service_description" placeholder="Enter Description" required></textarea>
                </div>
                <div class="form-group">
                    <label>Service Price:</label>
                    <input type="text" name="service_price" required inputmode="decimal" pattern="[0-9]+(\.[0-9]{1,2})?" placeholder="Enter Price">
                </div>
                <div class="image-upload-group">
                <label>Upload Image:</label>
                    <div class="centered-image-upload">
                        <div class="image-preview-container">
                            <img id="serviceNewImagePreview" class="new-image-preview" style="display: none;">
                        </div>
                        <div class="upload-btn-wrapper">
                            <input type="file" name="service_image" id="serviceImageUpload" accept="image/*" required class="hidden-upload">
                            <label for="serviceImageUpload" class="custom-upload-btn">Choose Image</label>
                        </div>
                    </div>
                </div>
                <button type="submit" name="submit" class="submit-btn">Confirm</button>
            </form>
        </div>

        <!-- ADD PROMO FORM -->
        <div id="addPromoContainer" class="form-container" style="display: none;">
            <h2 class="form-title">Add New Promo</h2>
            <form action="Add-Promo.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Select Category:</label>
                    <select name="promo_category" required>
                        <option value="" selected disabled hidden>Select a Category</option>
                        <option value="Facial">Facial</option>
                        <option value="Eyebrow">Eyebrow</option>
                        <option value="Eyelash">Eyelash</option>
                        <option value="Nail">Nail</option>
                        <option value="Skin">Skin</option>
                        <option value="Body">Body</option>
                        <option value="Makeup">Makeup</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Promo Name:</label>
                    <input type="text" name="promo_name" placeholder="Enter Name" required>
                </div>
                <div class="form-group">
                    <label>Promo Description:</label>
                    <textarea name="promo_description" placeholder="Enter Description" required></textarea>
                </div>
                <div class="form-group">
                    <label>Promo Inclusion:</label>
                    <input type="text" name="promo_inclusion" placeholder="e.g. Facial, Derma, Cleaning" required>
                </div>
                <div class="form-group">
                    <label>Promo Price:</label>
                    <input type="text" name="promo_price" required inputmode="decimal" pattern="[0-9]+(\.[0-9]{1,2})?" placeholder="Enter price">
                </div>
                <div class="image-upload-group">
                <label>Upload Image:</label>
                    <div class="centered-image-upload">
                        <div class="image-preview-container">
                            <img id="promoNewImagePreview" class="new-image-preview" style="display: none;">
                        </div>
                        <div class="upload-btn-wrapper">
                            <input type="file" name="promo_image" id="promoImageUpload" accept="image/*" required class="hidden-upload">
                            <label for="promoImageUpload" class="custom-upload-btn">Choose Image</label>
                        </div>
                    </div>
                </div>
                <button type="submit" name="submit" class="submit-btn">Add Promo</button>
            </form>
        </div>

        <!-- ADD PACKAGE FORM -->
        <div id="addPackageContainer" class="form-container" style="display: none;">
            <h2 class="form-title">Add New Package</h2>
            <form action="Add-Package.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Select Category:</label>
                    <select name="package_category" required>
                        <option value="" selected disabled hidden>Select a Category</option>
                        <option value="Facial">Facial</option>
                        <option value="Eyebrow">Eyebrow</option>
                        <option value="Eyelash">Eyelash</option>
                        <option value="Nail">Nail</option>
                        <option value="Skin">Skin</option>
                        <option value="Body">Body</option>
                        <option value="Makeup">Makeup</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Package Name:</label>
                    <input type="text" name="package_name" placeholder="Enter Name" required>
                </div>
                <div class="form-group">
                    <label>Package Description:</label>
                    <textarea name="package_description" placeholder="Enter Description" required></textarea>
                </div>
                <div class="form-group">
                    <label>Package Inclusion:</label>
                    <input type="text" name="package_inclusion" placeholder="e.g. Facial, Derma, Nail" required>
                </div>
                <div class="form-group">
                    <label>Package Price:</label>
                    <input type="text" name="package_price" required inputmode="decimal" pattern="[0-9]+(\.[0-9]{1,2})?" placeholder="Enter Price">
                </div>
                <div class="image-upload-group">
                <label>Upload Image:</label>
                    <div class="centered-image-upload">
                        <div class="image-preview-container">
                            <img id="packageNewImagePreview" class="new-image-preview" style="display: none;">
                        </div>
                        <div class="upload-btn-wrapper">
                            <input type="file" name="package_image" id="packageImageUpload" accept="image/*" required class="hidden-upload">
                            <label for="packageImageUpload" class="custom-upload-btn">Choose Image</label>
                        </div>
                    </div>
                </div>
                <button type="submit" name="submit" class="submit-btn">Add Package</button>
            </form>
        </div>
    </div>
</div>

<div id="successModal" class="modal-cartAdded">
    <div class="modal-cartAdded-content">
        <p>Added Successfully</p>
    </div>
</div>

<!-- Edit Service Modal -->
<div id="editServiceModal" class="ModalAdd">
    <div class="Modal-content-add">
        <button class="close-btn" onclick="closeEditModal('editServiceModal')">✖</button>
        <h2 class="form-title">Edit Service</h2>
        <form action="Edit-Services.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="service_id" id="editServiceId">
            <input type="hidden" name="current_image" id="editServiceCurrentImage">
            <div class="form-group">
                <label>Select Category:</label>
                <select name="service_category" id="editServiceCategory" required>
                    <option value="Facial">Facial</option>
                    <option value="Eyebrow">Eyebrow</option>
                    <option value="Eyelash">Eyelash</option>
                    <option value="Nail">Nail</option>
                    <option value="Skin">Skin</option>
                    <option value="Body">Body</option>
                    <option value="Makeup">Makeup</option>
                </select>
            </div>
            <div class="form-group">
                <label>Service Name:</label>
                <input type="text" name="service_name" id="editServiceName" placeholder="Enter Name" required>
            </div>
            <div class="form-group">
                <label>Service Description:</label>
                <textarea name="service_description" id="editServiceDescription" placeholder="Enter Description" required></textarea>
            </div>
            <div class="form-group">
                <label>Service Price:</label>
                <input type="text" name="service_price" id="editServicePrice" required inputmode="decimal" pattern="[0-9]+(\.[0-9]{1,2})?" placeholder="Enter Price">
            </div>
            <div class="form-group image-upload-group">
                <div class="image-upload-container">
                    <!-- Current Image -->
                    <div class="image-section">
                        <label>Current Image</label>
                        <div class="image-preview-container">
                            <img id="editServiceCurrentImagePreview" src="" alt="Current Image" class="current-image-preview">
                        </div>
                    </div>
                    
                    <!-- Upload Button -->
                    <div class="image-section">
                        <label>Upload Image</label>
                        <div class="upload-btn-container">
                            <input type="file" name="service_image" id="editServiceImageUpload" accept="image/*" class="hidden-upload">
                            <label for="editServiceImageUpload" class="custom-upload-btn">Choose File</label>
                        </div>
                    </div>
                    
                    <!-- New Image Preview -->
                    <div class="image-section">
                        <label>New Preview</label>
                        <div class="image-preview-container">
                            <img id="editServiceNewImagePreview" src="" alt="New Image Preview" class="new-image-preview" style="display: none;">
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" name="submit" class="submit-btn">Update Service</button>
        </form>
    </div>
</div>

<!-- Edit Promo Modal -->
<div id="editPromoModal" class="ModalAdd">
    <div class="Modal-content-add">
        <button class="close-btn" onclick="closeEditModal('editPromoModal')">✖</button>
        <h2 class="form-title">Edit Promo</h2>
        <form action="Edit-Promo.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="promo_id" id="editPromoId">
            <input type="hidden" name="current_image" id="editPromoCurrentImage">
            <div class="form-group">
                <label>Select Category:</label>
                <select name="promo_category" id="editPromoCategory" required>
                    <option value="Facial">Facial</option>
                    <option value="Eyebrow">Eyebrow</option>
                    <option value="Eyelash">Eyelash</option>
                    <option value="Nail">Nail</option>
                    <option value="Skin">Skin</option>
                    <option value="Body">Body</option>
                    <option value="Makeup">Makeup</option>
                </select>
            </div>
            <div class="form-group">
                <label>Promo Name:</label>
                <input type="text" name="promo_name" id="editPromoName" placeholder="Enter Name" required>
            </div>
            <div class="form-group">
                <label>Promo Description:</label>
                <textarea name="promo_description" id="editPromoDescription" placeholder="Enter Description" required></textarea>
            </div>
            <div class="form-group">
                <label>Promo Inclusion:</label>
                <input type="text" name="promo_inclusion" id="editPromoInclusion" placeholder="e.g. Facial, Derma, Cleaning" required>
            </div>
            <div class="form-group">
                <label>Promo Price:</label>
                <input type="text" name="promo_price" id="editPromoPrice" required inputmode="decimal" pattern="[0-9]+(\.[0-9]{1,2})?" placeholder="Enter price">
            </div>
            <div class="form-group image-upload-group">
                <div class="image-upload-container">
                    <!-- Current Image -->
                    <div class="image-section">
                        <label>Current Image</label>
                        <div class="image-preview-container">
                            <img id="editPromoCurrentImagePreview" src="" alt="Current Image" class="current-image-preview">
                        </div>
                    </div>
                    
                    <!-- Upload Button -->
                    <div class="image-section">
                        <label>Upload Image</label>
                        <div class="upload-btn-container">
                            <input type="file" name="promo_image" id="editPromoImageUpload" accept="image/*" class="hidden-upload">
                            <label for="editPromoImageUpload" class="custom-upload-btn">Choose File</label>
                        </div>
                    </div>
                    
                    <!-- New Image Preview -->
                    <div class="image-section">
                        <label>New Preview</label>
                        <div class="image-preview-container">
                            <img id="editPromoNewImagePreview" src="" alt="New Image Preview" class="new-image-preview" style="display: none;">
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" name="submit" class="submit-btn">Update Promo</button>
        </form>
    </div>
</div>

<!-- Edit Package Modal -->
<div id="editPackageModal" class="ModalAdd">
    <div class="Modal-content-add">
        <button class="close-btn" onclick="closeEditModal('editPackageModal')">✖</button>
        <h2 class="form-title">Edit Package</h2>
        <form action="Edit-Package.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="package_id" id="editPackageId">
            <input type="hidden" name="current_image" id="editPackageCurrentImage">
            <div class="form-group">
                <label>Select Category:</label>
                <select name="package_category" id="editPackageCategory" required>
                    <option value="Facial">Facial</option>
                    <option value="Eyebrow">Eyebrow</option>
                    <option value="Eyelash">Eyelash</option>
                    <option value="Nail">Nail</option>
                    <option value="Skin">Skin</option>
                    <option value="Body">Body</option>
                    <option value="Makeup">Makeup</option>
                </select>
            </div>
            <div class="form-group">
                <label>Package Name:</label>
                <input type="text" name="package_name" id="editPackageName" placeholder="Enter Name" required>
            </div>
            <div class="form-group">
                <label>Package Description:</label>
                <textarea name="package_description" id="editPackageDescription" placeholder="Enter Description" required></textarea>
            </div>
            <div class="form-group">
                <label>Package Inclusion:</label>
                <input type="text" name="package_inclusion" id="editPackageInclusion" placeholder="e.g. Facial, Derma, Cleaning" required>
            </div>
            <div class="form-group">
                <label>Package Price:</label>
                <input type="text" name="package_price" id="editPackagePrice" required inputmode="decimal" pattern="[0-9]+(\.[0-9]{1,2})?" placeholder="Enter price">
            </div>
            <div class="form-group image-upload-group">
                <div class="image-upload-container">
                    <!-- Current Image -->
                    <div class="image-section">
                        <label>Current Image</label>
                        <div class="image-preview-container">
                            <img id="editPackageCurrentImagePreview" src="" alt="Current Image" class="current-image-preview">
                        </div>
                    </div>
                    
                    <!-- Upload Button -->
                    <div class="image-section">
                        <label>Upload Image</label>
                        <div class="upload-btn-container">
                            <input type="file" name="package_image" id="editPackageImageUpload" accept="image/*" class="hidden-upload">
                            <label for="editPackageImageUpload" class="custom-upload-btn">Choose File</label>
                        </div>
                    </div>
                    
                    <!-- New Image Preview -->
                    <div class="image-section">
                        <label>New Preview</label>
                        <div class="image-preview-container">
                            <img id="editPackageNewImagePreview" src="" alt="New Image Preview" class="new-image-preview" style="display: none;">
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" name="submit" class="submit-btn">Update Package</button>
        </form>
    </div>
</div>

<!-- Edit Package Modal -->
<div id="editPackageModal" class="ModalAdd">
    <div class="Modal-content-add">
        <button class="close-btn" onclick="closeEditModal('editPackageModal')">✖</button>
        <h2 class="form-title">Edit Package</h2>
        <form action="Edit-Package.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="package_id" id="editPackageId">
            <input type="hidden" name="current_image" id="editPackageCurrentImage">
            <div class="form-group">
                <label>Select Category:</label>
                <select name="package_category" id="editPackageCategory" required>
                    <option value="Facial">Facial</option>
                    <option value="Eyebrow">Eyebrow</option>
                    <option value="Eyelash">Eyelash</option>
                    <option value="Nail">Nail</option>
                    <option value="Skin">Skin</option>
                    <option value="Body">Body</option>
                    <option value="Makeup">Makeup</option>
                </select>
            </div>
            <div class="form-group">
                <label>Package Name:</label>
                <input type="text" name="package_name" id="editPackageName" placeholder="Enter Name" required>
            </div>
            <div class="form-group">
                <label>Package Description:</label>
                <textarea name="package_description" id="editPackageDescription" placeholder="Enter Description" required></textarea>
            </div>
            <div class="form-group">
                <label>Package Inclusion:</label>
                <input type="text" name="package_inclusion" id="editPackageInclusion" placeholder="e.g. Facial, Derma, Nail" required>
            </div>
            <div class="form-group">
                <label>Package Price:</label>
                <input type="text" name="package_price" id="editPackagePrice" required inputmode="decimal" pattern="[0-9]+(\.[0-9]{1,2})?" placeholder="Enter Price">
            </div>
            <div class="form-group image-upload-group">
            <div class="form-group image-upload-group">
            <div class="form-group image-upload-group">
    <div class="image-upload-container">
        <!-- Current Image -->
        <div class="image-section">
            <label>Current Image</label>
            <div class="image-preview-container">
                <img id="editServiceCurrentImagePreview" src="" alt="Current Image" class="current-image-preview">
            </div>
        </div>
        
        <!-- Upload Button -->
        <div class="image-section">
            <label>Upload Image</label>
            <div class="upload-btn-container">
                <input type="file" name="service_image" id="serviceImageUpload" accept="image/*" class="hidden-upload">
                <label for="serviceImageUpload" class="custom-upload-btn">Choose File</label>
            </div>
        </div>
        
        <!-- New Image Preview -->
        <div class="image-section">
            <label>New Preview</label>
            <div class="image-preview-container">
                <img id="editServiceNewImagePreview" src="" alt="New Image Preview" class="new-image-preview" style="display: none;">
            </div>
        </div>
    </div>
</div>
</div>
</div>
            <button type="submit" name="submit" class="submit-btn">Update Package</button>
        </form>
    </div>
</div>


 
<script src="../Frontend-Admin/DashboardServices.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
