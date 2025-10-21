<?php
session_start();
header('Content-Type: application/json');

require_once '../models/Database.php';
require_once '../models/ParkingSpace.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    $parkingSpace = new ParkingSpace($db);

    // Get filter parameters
    $userType = isset($_GET['user_type']) ? $_GET['user_type'] : 'all';
    $vehicleType = isset($_GET['vehicle_type']) ? $_GET['vehicle_type'] : 'all';

    // Get filtered distribution
    $distribution = $parkingSpace->getSlotDistribution($userType, $vehicleType);

    echo json_encode([
        'success' => true,
        'distribution' => $distribution
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>