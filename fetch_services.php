<?php
include 'database.php'; // Make sure this is your correct database connection file

if (isset($_GET['category'])) {
    $category = $_GET['category'];

    if ($category === "all" || $category === "") {
        // Get all items where Items_Type is 'Service'
        $query = "SELECT Items_Name AS Service_Name, Items_Price AS Service_Price, Items_Image AS Service_Image 
                  FROM Items 
                  WHERE Items_Type = 'Service'";
        $stmt = $conn->prepare($query);
    } else {
        // Filtered by category, only get items where Items_Type is 'Service'
        $query = "SELECT Items_Name AS Service_Name, Items_Price AS Service_Price, Items_Image AS Service_Image 
                  FROM Items 
                  WHERE Items_Type = 'Service' AND Items_Category = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $category);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $services = [];
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }

    echo json_encode($services);
} else {
    echo json_encode(["error" => "Category not provided"]);
}
