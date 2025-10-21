<?php
header('Content-Type: application/json');

// Suppress errors in production - log them instead
error_reporting(0);
ini_set('display_errors', 0);

try {
    require_once __DIR__ . '/../models/Database.php';
    require_once __DIR__ . '/../models/ParkingSpace.php';

    // Get and validate filter parameters
    $userType = $_GET['userType'] ?? 'all';
    $vehicleType = $_GET['vehicleType'] ?? 'all';

    $validUserTypes = ['all', 'general', 'faculty', 'executive', 'guest'];
    $validVehicleTypes = ['all', 'car', 'motorcycle'];

    if (!in_array($userType, $validUserTypes)) $userType = 'all';
    if (!in_array($vehicleType, $validVehicleTypes)) $vehicleType = 'all';

    // Get data
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception('Database connection failed');
    }
    
    $parkingSpace = new ParkingSpace($db);
    $distribution = $parkingSpace->getSlotDistribution($userType, $vehicleType);
    
    // Success response
    echo json_encode([
        'success' => true,
        'distribution' => $distribution,
        'filters' => [
            'userType' => $userType,
            'vehicleType' => $vehicleType
        ]
    ]);
    
} catch (Exception $e) {
    // Error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'distribution' => []
    ]);
}