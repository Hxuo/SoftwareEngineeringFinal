<?php
include 'database.php'; // Include database connection

if (isset($_POST['submit'])) {
    $itemsType = "Promo"; // Since this is for adding promos, you can hard-code this or modify if needed
    $itemsCategory = $_POST['promo_category'];
    $itemsName = $_POST['promo_name'];
    $itemsDescription = $_POST['promo_description'];
    $itemsInclusion = $_POST['promo_inclusion'];
    $itemsPrice = $_POST['promo_price'];

    // Directory where the image will be saved
    $target_dir = "../Items-Images/"; // Ensure this folder exists
    $imageName = basename($_FILES["promo_image"]["name"]);
    $target_file = $target_dir . $imageName;

    // Upload the image
    if (move_uploaded_file($_FILES["promo_image"]["tmp_name"], $target_file)) {
        // Insert data into the Items table
        $stmt = $conn->prepare("INSERT INTO Items (Items_Type, Items_Category, Items_Name, Items_Description, Items_Inclusion, Items_Price, Items_Image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $itemsType, $itemsCategory, $itemsName, $itemsDescription, $itemsInclusion, $itemsPrice, $imageName);

        if ($stmt->execute()) {
            echo "<script>
                localStorage.setItem('promoAdded', 'true');
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

