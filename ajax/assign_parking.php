<?php
require_once __DIR__ . '/../controller/config.php';
require_once __DIR__ . '/../models/ParkingSpace.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$vehicle_id = $_POST['vehicle_id'] ?? null;
$space_id = $_POST['space_id'] ?? null;

if (!$vehicle_id || !$space_id) {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit;
}

try {
    $parking = new ParkingSpace($conn);
    $result = $parking->assignVehicleToSpace($vehicle_id, $space_id);

    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
