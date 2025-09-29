<?php
/**
 * controller/dashboard.php
 * Protected page that requires user authentication
 */

// Start session to check user authentication
session_start();

// Include database configuration and User model
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../models/Vehicle.php';

// Initialize variables for the view
$error_message = '';
$success_message = '';
$email = '';

// // Check if user is logged in, if not redirect to login page
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }

// Get user information from session
$user_id = $_SESSION['user_id'];
$firstname = $_SESSION['firstname'] ?? 'User';
$lastname = $_SESSION['lastname'] ?? '';
$email = $_SESSION['email'] ?? '';
$user_type = $_SESSION['user_type'] ?? 'Guest';
$phone_number = $_SESSION['phone_number'] ?? '';

$vehicleModel = new Vehicle($conn);
$vehicles = $vehicleModel->getVehiclesByUser($user_id);

// Handle logout request
if (isset($_GET['logout'])) {
    // Destroy all session data
    session_unset();
    session_destroy();
    
    // Remove remember me cookie if it exists
    if (isset($_COOKIE['remember_user'])) {
        setcookie('remember_user', '', time() - 3600, '/');
    }
    
    // Redirect to login page with logout message
    header("Location: login.php?logout=success");
    exit();
}

// Include the dashboard view
include __DIR__ . '/../views/account.php';
?>