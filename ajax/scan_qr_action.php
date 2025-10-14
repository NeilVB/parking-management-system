<?php
// ajax/scan_qr_action.php
session_start();
require_once '../models/Database.php';
require_once '../controllers/ScanController.php';

$database = new Database();
$db = $database->getConnection(); // ✅ FIXED LINE
$controller = new ScanController($db);

$scanned_by = $_SESSION['user_id'] ?? null;
$action = $_POST['action'] ?? null;

if ($action === 'park') {
    $vehicle_id = intval($_POST['vehicle_id']);
    echo json_encode($controller->parkVehicle($vehicle_id, null, $scanned_by));
    exit;
}

$qr_token = $_POST['qr_token'] ?? '';
echo json_encode($controller->scanQRCode($qr_token, $scanned_by));
