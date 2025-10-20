<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../controller/config.php';
require_once __DIR__ . '/../models/Vehicle.php';

$token = $_GET['token'] ?? null;

if (!$token) {
    echo json_encode(['status' => 'error', 'message' => 'No token provided']);
    exit;
}

$vehicleModel = new Vehicle($conn);
$data = $vehicleModel->getVehicleWithUserByToken($token);

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or expired token']);
    exit;
}

echo json_encode([
    'status' => 'success',
    'vehicle' => [
        'plate_number' => $data['plate_number'],
        'type' => $data['vehicle_type'],
        'color' => $data['color'],
        'make' => $data['make'],
        'model' => $data['model'],
        'year' => $data['year']
    ],
    'user' => [
        'name' => $data['firstname'] . ' ' . $data['lastname'],
        'email' => $data['email'],
        'phone' => '0' . $data['phone_number'],
        'user_type' => $data['user_type']
    ]
]);
?>


