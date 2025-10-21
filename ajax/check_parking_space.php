<?php
session_start();
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in JSON response

try {
    // Check if space_id parameter exists
    if (!isset($_GET['space_id'])) {
        throw new Exception('Parking space ID is required');
    }

    $spaceId = intval($_GET['space_id']);

    if ($spaceId <= 0) {
        throw new Exception('Invalid parking space ID');
    }

    // Include required files
    require_once '../models/Database.php';
    require_once '../models/ParkingSpace.php';

    // Create database connection
    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        throw new Exception('Database connection failed');
    }

    // Create ParkingSpace instance
    $parkingSpace = new ParkingSpace($db);

    // Check for parked vehicles
    $result = $parkingSpace->hasParkedVehicles($spaceId);

    // Return success response
    echo json_encode([
        'success' => true,
        'has_parked' => $result['has_parked'],
        'count' => $result['count'],
        'vehicles' => $result['vehicles']
    ]);

} catch (Exception $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error' => true
    ]);
}
?>