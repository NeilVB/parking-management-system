<?php
// ajax/scan_vehicle.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../models/Database.php';
require_once '../controller/scan_controller.php';

// ✅ Initialize database connection properly
$database = new Database();
$db = $database->getConnection();

// ✅ Initialize ScanController
$controller = new ScanController($db);

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

if ($action === 'scan') {
    $qr_token = $_POST['qr_token'] ?? '';
    if (empty($qr_token)) {
        echo json_encode(['success' => false, 'message' => 'Missing QR token.']);
        exit;
    }

    // TODO: Replace 1 with actual logged-in guard's user_id
    $result = $controller->scanQRCode($qr_token, 1);
    echo json_encode($result);
    exit;
}

if ($action === 'update') {
    $status = $_POST['status'] ?? '';
    $plate_no = $_POST['plate_no'] ?? '';

    if (empty($status) || empty($plate_no)) {
        echo json_encode(['success' => false, 'message' => 'Missing status or plate number.']);
        exit;
    }

    // ✅ Fetch vehicle ID by plate number
    $query = $db->prepare("SELECT vehicle_id FROM vehicles_table WHERE plate_number = ?");
    $query->bind_param("s", $plate_no);
    $query->execute();
    $vehicle = $query->get_result()->fetch_assoc();
    $query->close();

    if (!$vehicle) {
        echo json_encode(['success' => false, 'message' => 'Vehicle not found.']);
        exit;
    }

    $vehicle_id = $vehicle['vehicle_id'];
    $user_id = 1; // TODO: Replace with session user_id of guard

    switch ($status) {
        case 'entered':
            $result = $controller->markEntered($vehicle_id, $user_id);
            break;
        case 'parked':
            $result = $controller->parkVehicle($vehicle_id, null, $user_id);
            break;
        case 'exiting':
            $result = $controller->markExiting($vehicle_id, $user_id);
            break;
        case 'outside':
            $result = $controller->markOutside($vehicle_id, $user_id);
            break;
        default:
            $result = ['success' => false, 'message' => 'Invalid status.'];
    }

    echo json_encode($result);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);
?>
