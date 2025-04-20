<?php
session_start();
include 'database.php';

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phonenumber = $_POST['phonenumber'] ?? '';  
$date = $_POST['date'] ?? '';
$time = $_POST['time'] ?? '';
$time = date("H:i", strtotime($time)); 
$totalPrice = $_POST['totalPrice'] ?? 0;
$servicesJSON = $_POST['services'] ?? '[]';
$paymentMethod = $_POST['paymentMethod'] ?? ''; 
$services = json_decode($servicesJSON, true);

$secretKey = 'sk_test_ZwBwLBqrpTGM5SZ6qPyTmoZ8';

// Store details in session for later use
$_SESSION['appointment_details'] = [
    'name' => $name,
    'email' => $email,
    'phonenumber' => $phonenumber,
    'date' => $date,
    'time' => $time,
    'totalPrice' => $totalPrice,
    'services' => $services,
    'paymentMethod' => $paymentMethod
];

// PayMongo API integration
$lineItems = array_map(fn($s) => [
    'currency' => 'PHP',
    'amount' => (int) ($s['price'] * 100), 
    'name' => $s['name'], 
    'quantity' => 1
], $services);

$description = "Service Booking ($date at $time)";
$payload = json_encode([
    'data' => [
        'attributes' => [
            'billing' => ['name' => $name, 'email' => $email, 'phone' => $phonenumber],
            'line_items' => $lineItems,
            'payment_method_types' => ['gcash', 'paymaya'],
            'description' => $description,
            'success_url' => 'http://localhost/SoftENgFinalV9/Backend-User/payment_success.php',
            'cancel_url' => 'http://localhost/SoftENgFinalV9/Backend-User/payment_cancelled.php'
        ]
    ]
]);

$ch = curl_init('https://api.paymongo.com/v1/checkout_sessions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json', 
    'Authorization: Basic ' . base64_encode($secretKey . ':')
]);
$response = curl_exec($ch);
curl_close($ch);
$responseData = json_decode($response, true);

if (isset($responseData['data']['attributes']['checkout_url'])) {
    echo json_encode(['checkout_url' => $responseData['data']['attributes']['checkout_url']]);
} else {
    echo json_encode(['error' => 'Failed to create checkout session', 'details' => $responseData]);
}
?>
