// Main function to open edit modal
function openEditModal(item) {
    const type = item.Items_Type.toLowerCase();
    let modalId;
    
    // Map item types to modal IDs
    if (type === 'service') {
        modalId = 'editServiceModal';
    } else if (type === 'promo') {
        modalId = 'editPromoModal';
    } else if (type === 'package') {
        modalId = 'editPackageModal'; // You'll need to add this modal in your HTML
    } else {
        console.error('Unknown item type:', type);
        return;
    }
    
    const prefix = modalId.replace('Modal', ''); // Gets 'editService' or 'editPromo'
    
    // Set basic form values
    setFormValues(item, prefix);
    
    // Handle image previews
    setupImagePreviews(item, modalId, prefix);
    
    // Show the modal
    document.getElementById(modalId).style.display = 'flex';
}

// Helper function to capitalize first letter
function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

// Set form field values
function setFormValues(item, prefix) {
    document.getElementById(`${prefix}Id`).value = item.Items_ID;
    document.getElementById(`${prefix}Category`).value = item.Items_Category;
    document.getElementById(`${prefix}Name`).value = item.Items_Name;
    document.getElementById(`${prefix}Description`).value = item.Items_Description || '';
    document.getElementById(`${prefix}Price`).value = item.Items_Price;
    
    // Special fields for different types
    if (prefix === 'editPromo') {
        document.getElementById(`${prefix}Inclusion`).value = item.Items_Inclusion || '';
    } else if (prefix === 'editPackage') {
        // Handle package-specific fields
        document.getElementById(`${prefix}Inclusion`).value = item.Items_Inclusion || '';
        // Add any other package-specific fields here
    }
}

// Handle image preview functionality
function setupImagePreviews(item, modalId, prefix) {
    // Current image preview
    const currentPreview = document.getElementById(`${prefix}CurrentImagePreview`);
    if (item.Items_Image) {
        currentPreview.src = `../Items-Images/${item.Items_Image}`;
        currentPreview.style.display = 'block';
    } else {
        currentPreview.style.display = 'none';
    }

    // New image preview handling
    const uploadInput = document.querySelector(`#${modalId} .hidden-upload`);
    const newPreview = document.getElementById(`${prefix}NewImagePreview`);
    
    // Clear any existing file selection
    uploadInput.value = '';
    newPreview.style.display = 'none';
    newPreview.src = '';
    
    // Remove previous event listeners
    const newUploadInput = uploadInput.cloneNode(true);
    uploadInput.parentNode.replaceChild(newUploadInput, uploadInput);
    
    // Add new change listener
    newUploadInput.addEventListener('change', function(e) {
        handleImageUpload(e, newPreview);
    });
}

// Handle image upload and preview
function handleImageUpload(event, previewElement) {
    if (event.target.files && event.target.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewElement.src = e.target.result;
            previewElement.style.display = 'block';
        };
        
        reader.onerror = function() {
            console.error('Error reading image file');
            previewElement.style.display = 'none';
        };
        
        reader.readAsDataURL(event.target.files[0]);
    } else {
        previewElement.style.display = 'none';
    }
}

// Close modal function
function closeEditModal(modalId) {
    // Reset new image preview
    const newPreview = document.querySelector(`#${modalId} .new-image-preview`);
    if (newPreview) {
        newPreview.style.display = 'none';
        newPreview.src = '';
    }
    
    // Reset file input
    const fileInput = document.querySelector(`#${modalId} .hidden-upload`);
    if (fileInput) {
        fileInput.value = '';
    }
    
    // Hide modal
    document.getElementById(modalId).style.display = 'none';
}

// Initialize all modals on page load
function initializeEditModals() {
    // Close buttons
    document.querySelectorAll('.ModalAdd .close-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const modalId = this.closest('.ModalAdd').id;
            closeEditModal(modalId);
        });
    });
    
    // Click outside to close
    document.querySelectorAll('.ModalAdd').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal(this.id);
            }
        });
    });
    
    // Escape key to close
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.ModalAdd').forEach(modal => {
                if (modal.style.display === 'flex') {
                    closeEditModal(modal.id);
                }
            });
        }
    });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeEditModals();
});
//Delete Item
function deleteItem(itemId) {
    if (confirm('Are you sure you want to delete this item?')) {
        fetch(`Delete-Item.php?id=${itemId}`, {
            method: 'DELETE'
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                fetchServices();
            }
        });
    }
}

// Function to close the modal
function closeModal() {
var modal = document.getElementById("myModal");
modal.style.display = "none";
}

// Close modal if the user clicks anywhere outside of it
window.onclick = function (event) {
var modal = document.getElementById("myModal");
if (event.target === modal) {
    modal.style.display = "none";
}
}

//SA SERVICES PAGE 

let currentPage = 1;
let totalPages = 1;
let selectedCategory = ''; // Stores the selected category filter

// Fetch services with filters
function fetchServices() {
    const search = document.getElementById('categorySearch').value;
    const viewCategory = document.querySelector('.category-dropdown').value;

    let categoryQuery = viewCategory === 'all' ? '' : viewCategory; // Remove filter if "All" is selected

    fetch(`search_items.php?page=${currentPage}&search=${search}&category=${categoryQuery}`)
        .then(response => response.json())
        .then(data => {
            totalPages = data.totalPages;
            updateTable(data.data);
            updatePagination();
        });
}


// Update table content
// Update table content
function updateTable(items) {
    const tableBody = document.getElementById('servicesTableBody');
    tableBody.innerHTML = '';
    
    items.forEach(item => {
        const imageSrc = item.Items_Image ? `../Items-Images/${item.Items_Image}` : '../Assests/no-image.png';
        
        tableBody.innerHTML += 
            `<tr>
                <td>${item.Items_ID}</td>
                <td class="image-cell">
                    <img src="${imageSrc}" alt="${item.Items_Name}" class="table-image">
                </td>
                <td>${item.Items_Name}</td>
                <td>${item.Items_Type}</td>
                <td>${item.Items_Category}</td>
                <td>${item.Items_Price}</td>
                <td class="action-cell">
                    <button class="edit-btn" onclick="openEditModal(${JSON.stringify(item).replace(/"/g, '&quot;')})"> 
                        <img src="../Assests/editicon.png" alt="Edit" width="30">
                        <span class="tooltip">Edit</span> <!-- Tooltip text for Edit -->
                    </button>
                    <button class="delete-btn" onclick="deleteItem(${item.Items_ID})"> 
                        <img src="../Assests/deleteicon.png" alt="Delete" width="30">
                        <span class="tooltip">Delete</span> <!-- Tooltip text for Delete -->
                    </button>
                </td>
            </tr>`;
    });
}


// Update pagination buttons
function updatePagination() {
    document.getElementById('pageIndicator').innerText = `Page ${currentPage} of ${totalPages}`;
    document.getElementById('prevPage').disabled = currentPage === 1;
    document.getElementById('nextPage').disabled = currentPage === totalPages;
}

// Change page
function changePage(direction) {
    if ((direction === -1 && currentPage > 1) || (direction === 1 && currentPage < totalPages)) {
        currentPage += direction;
        fetchServices();
    }
}

// Search function (triggers on typing)
function searchCategory() {
    currentPage = 1;
    fetchServices();
}

// Filter by category selection
document.querySelector('.category-dropdown').addEventListener('change', function () {
    selectedCategory = this.value;
    currentPage = 1;
    fetchServices();
});

// Filter by category selection
function filterByCategory() {
    const categoryDropdown = document.querySelector('.category-dropdown');
    selectedCategory = categoryDropdown.value.toLowerCase(); // Ensure lowercase for PHP matching
    currentPage = 1;
    fetchServices();
}


// Initial fetch
document.addEventListener("DOMContentLoaded", fetchServices);

//MODAL SA ADD LOGIC
// Open the modal based on the selected ADD option
document.getElementById('categorySelect').addEventListener('change', function () {
    let selectedValue = this.value;
    openAddModal(selectedValue);
});

// Function to open the correct modal
// Open the modal based on the selected ADD option
document.getElementById('categorySelect').addEventListener('change', function () {
    openAddModal(this.value);
});

function openAddModal(type) {
    document.getElementById('ModalAdd').style.display = 'flex';
    
    document.querySelectorAll('.form-container').forEach(form => {
        form.style.display = 'none';
    });

    if (type === 'services') {
        document.getElementById('addServiceContainer').style.display = 'block';
    } else if (type === 'promo') {
        document.getElementById('addPromoContainer').style.display = 'block';
    } else if (type === 'package') {
        document.getElementById('addPackageContainer').style.display = 'block';
    }
}

function closeAddModal() {
    document.getElementById('ModalAdd').style.display = 'none';
}


document.addEventListener("DOMContentLoaded", function () {
    let promoAdded = localStorage.getItem("promoAdded") === "true";
    let serviceAdded = localStorage.getItem("serviceAdded") === "true";
    let packageAdded = localStorage.getItem("packageAdded") === "true";

    if (promoAdded || serviceAdded || packageAdded) {
        showSuccessModal();

        // Linisin ang localStorage para hindi mag-loop ang modal sa refresh
        localStorage.removeItem("promoAdded");
        localStorage.removeItem("serviceAdded");
        localStorage.removeItem("packageAdded");
    }
});

function showSuccessModal() {
    const successModal = document.getElementById('successModal');
    successModal.style.display = 'flex';

    // Close the success modal after 2 seconds
    setTimeout(() => {
        successModal.style.display = 'none';
    }, 2000);
}


//ADD MODAL IMAGE PREVIEW
// Initialize image preview functionality when the page loads
document.addEventListener('DOMContentLoaded', function() {
    // Service image upload
    const serviceUpload = document.getElementById('serviceImageUpload');
    const servicePreview = document.getElementById('serviceNewImagePreview');
    
    serviceUpload.addEventListener('change', function(e) {
        handleImageUpload(e, servicePreview);
    });

    // Promo image upload
    const promoUpload = document.getElementById('promoImageUpload');
    const promoPreview = document.getElementById('promoNewImagePreview');
    
    promoUpload.addEventListener('change', function(e) {
        handleImageUpload(e, promoPreview);
    });

    // Package image upload
    const packageUpload = document.getElementById('packageImageUpload');
    const packagePreview = document.getElementById('packageNewImagePreview');
    
    packageUpload.addEventListener('change', function(e) {
        handleImageUpload(e, packagePreview);
    });
});

// Handle image upload and preview
function handleImageUpload(event, previewElement) {
    if (event.target.files && event.target.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewElement.src = e.target.result;
            previewElement.style.display = 'block';
        };
        
        reader.onerror = function() {
            console.error('Error reading image file');
            previewElement.style.display = 'none';
        };
        
        reader.readAsDataURL(event.target.files[0]);
    } else {
        previewElement.style.display = 'none';
    }
}

// Function to close the add modal and clear previews
function closeAddModal() {
    document.getElementById('ModalAdd').style.display = 'none';
    
    // Clear all image previews
    const previews = document.querySelectorAll('.new-image-preview');
    previews.forEach(preview => {
        preview.src = '';
        preview.style.display = 'none';
    });
    
    // Reset all file inputs
    const fileInputs = document.querySelectorAll('.hidden-upload');
    fileInputs.forEach(input => {
        input.value = '';
    });
    
    // Hide all form containers
    document.getElementById('addServiceContainer').style.display = 'none';
    document.getElementById('addPromoContainer').style.display = 'none';
    document.getElementById('addPackageContainer').style.display = 'none';
}