<?php
include 'database.php';

// Kunin ang form data
$id = $_POST['service_id'];
$category = $_POST['service_category'];
$name = $_POST['service_name'];
$description = $_POST['service_description'];
$price = $_POST['service_price'];

// Folder kung saan ise-save ang image
$target_dir = "../Items-Images/";
$imageName = basename($_FILES["service_image"]["name"]);
$target_file = $target_dir . $imageName;

// Kunin ang lumang larawan mula sa database
$sql = "SELECT Items_Image FROM items WHERE Items_ID=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$oldImage = $result->fetch_assoc()['Items_Image'];

// Kung may bagong larawan na na-upload
if (!empty($_FILES["service_image"]["name"])) {
    // Tanggalin ang lumang larawan kung ito ay umiiral
    if (!empty($oldImage) && file_exists($target_dir . $oldImage)) {
        unlink($target_dir . $oldImage);
    }

    // I-upload ang bagong larawan
    if (move_uploaded_file($_FILES["service_image"]["tmp_name"], $target_file)) {
        // Image uploaded successfully, update the database with the new image
        $sql = "UPDATE items SET Items_Category=?, Items_Name=?, Items_Description=?, Items_Price=?, Items_Image=? WHERE Items_ID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdsi", $category, $name, $description, $price, $imageName, $id);
    } else {
        // Error in uploading image
        echo "Sorry, there was an error uploading your file.";
        exit();
    }
} else {
    // Kung walang bagong larawan, i-update lang ang ibang details
    $sql = "UPDATE items SET Items_Category=?, Items_Name=?, Items_Description=?, Items_Price=? WHERE Items_ID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdi", $category, $name, $description, $price, $id);
}

if ($stmt->execute()) {
    // Successfully updated
    header("Location: DashboardServices.php");
} else {
    // Error in update
    echo "Error: " . $stmt->error;
}
?>