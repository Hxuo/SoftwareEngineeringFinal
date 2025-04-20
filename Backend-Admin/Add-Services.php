<?php
include 'database.php'; // I-include ang database connection

if (isset($_POST['submit'])) {
    $itemsType = "Service"; // This is a Service, so we set the type to "Service"
    $itemsCategory = $_POST['service_category'];
    $itemsName = $_POST['service_name'];
    $itemsDescription = $_POST['service_description'];
    $itemsInclusion = ""; // Services don't have inclusions like promos, so leave it empty
    $itemsPrice = $_POST['service_price'];

    // Folder kung saan ise-save ang image
    $target_dir = "../Items-Images/"; // Use the same folder as promos to unify everything
    $imageName = basename($_FILES["service_image"]["name"]);
    $target_file = $target_dir . $imageName;

    // I-save ang image sa folder
    if (move_uploaded_file($_FILES["service_image"]["tmp_name"], $target_file)) {
        // Ipasok ang data sa Items table (instead of Services table)
        $stmt = $conn->prepare("INSERT INTO Items (Items_Type, Items_Category, Items_Name, Items_Description, Items_Inclusion, Items_Price, Items_Image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $itemsType, $itemsCategory, $itemsName, $itemsDescription, $itemsInclusion, $itemsPrice, $imageName);

        if ($stmt->execute()) {
            echo "<script>
                localStorage.setItem('serviceAdded', 'true');
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

