document.addEventListener("DOMContentLoaded", function () {
    // Open "ADD" Modal
    document.querySelector(".add-btn").addEventListener("click", function () {
        document.getElementById("addModal").style.display = "flex";
    });

    // Open "EDIT" Modal
    document.querySelectorAll(".edit-btn").forEach(button => {
        button.addEventListener("click", function () {
            document.getElementById("editModal").style.display = "flex";
        });
    });

    // Open "DELETE" Modal
    document.querySelectorAll(".delete-btn").forEach(button => {
        button.addEventListener("click", function () {
            document.getElementById("deleteModal").style.display = "flex";
        });
    });

    // Close Modal Function (Only needs to be defined ONCE)
    window.closeModal = function (modalID) {
        document.getElementById(modalID).style.display = "none";
    };
});

//CRUD
