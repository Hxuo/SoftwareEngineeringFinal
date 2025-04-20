<?php
include 'database.php'; // Include your database connection

if (isset($_POST['submit'])) {
    $itemsType = "Package"; // This is a Promo
    $itemsCategory = $_POST['package_category'];
    $itemsName = $_POST['package_name'];
    $itemsDescription = $_POST['package_description'];
    $itemsInclusion = $_POST['package_inclusion']; // Promos have inclusions (list of services included)
    $itemsPrice = $_POST['package_price'];

    // Folder kung saan ise-save ang image
    $target_dir = "../Items-Images/"; // Same folder used for both services and promos
    $imageName = basename($_FILES["package_image"]["name"]);
    $target_file = $target_dir . $imageName;

    // Upload the image
    if (move_uploaded_file($_FILES["package_image"]["tmp_name"], $target_file)) {
        // Insert into the Items table
        $stmt = $conn->prepare("INSERT INTO Items (Items_Type, Items_Category, Items_Name, Items_Description, Items_Inclusion, Items_Price, Items_Image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $itemsType, $itemsCategory, $itemsName, $itemsDescription, $itemsInclusion, $itemsPrice, $imageName);
        if ($stmt->execute()) {
            echo "<script>
                localStorage.setItem('packageAdded', 'true');
                window.location.href = 'DashboardServices.php';
            </script>";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Failed to upload image.";
    }
}

$conn->close();
?>


