<?php
/**
 * Registration Controller
 * Handles registration logic and includes the view
 */

// Include database configuration and User model
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../models/User.php';

// Initialize variables for the view
$error_message = '';
$success_message = '';
$form_data = [];

// Create User model instance
$user = new User($conn);

// Check if form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Store form data for repopulation
    $form_data = [
        'FirstName' => trim($_POST['FirstName']),
        'LastName' => trim($_POST['LastName']),
        'PlateNumber' => trim($_POST['PlateNumber']),
        'PhoneNumber' => trim($_POST['PhoneNumber']),
        'Type' => $_POST['Type'],
        'VehicleType' => $_POST['VehicleType'],
        'Email' => trim($_POST['Email']),
        'password' => $_POST['password']
    ];
    
    // Attempt to register user using the model
    $registration_result = $user->register($_POST);
    
    if ($registration_result['success']) {
        // Registration successful
        $success_message = $registration_result['message'];
        // Clear form data after successful registration
        $form_data = [];
    } else {
        // Registration failed, show error message
        $error_message = $registration_result['message'];
    }
}

// Include the registration view
include __DIR__ . '/../views/register.php';
?>
