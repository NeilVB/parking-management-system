<?php
// Prevent any output before JSON
error_reporting(0); // Turn off error display for production
ini_set('display_errors', 0);

// Set JSON header first
header('Content-Type: application/json');

try {
    // Include required files
    require_once __DIR__ . '/../models/Database.php';
    require_once __DIR__ . '/../models/ParkingSpace.php';

    // Get filter parameters
    $userType = isset($_GET['userType']) ? $_GET['userType'] : 'all';
    $vehicleType = isset($_GET['vehicleType']) ? $_GET['vehicleType'] : 'all';

    // Validate input
    $validUserTypes = ['all', 'general', 'faculty', 'executive', 'guest'];
    $validVehicleTypes = ['all', 'car', 'motorcycle'];

    if (!in_array($userType, $validUserTypes)) {
        $userType = 'all';
    }

    if (!in_array($vehicleType, $validVehicleTypes)) {
        $vehicleType = 'all';
    }

    // Initialize database connection
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception('Database connection failed');
    }
    
    // Get parking space model
    $parkingSpace = new ParkingSpace($db);
    
    // Get filtered distribution
    $distribution = $parkingSpace->getSlotDistribution($userType, $vehicleType);
    
    // Return JSON response
    echo json_encode([
        'success' => true,
        'distribution' => $distribution,
        'filters' => [
            'userType' => $userType,
            'vehicleType' => $vehicleType
        ]
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    // Return error as JSON
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'distribution' => []
    ], JSON_PRETTY_PRINT);
}
?>