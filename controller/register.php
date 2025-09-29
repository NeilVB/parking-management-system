<?php
/**
 * Registration Controller
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../models/User.php';

$error_message = '';
$success_message = '';
$form_data = [];

$user = new User($conn);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Normalize keys to match User.php expectations
    $form_data = [
        'FirstName'   => trim($_POST['FirstName'] ?? ''),
        'LastName'    => trim($_POST['LastName'] ?? ''),
        // 'PlateNumber' => trim($_POST['PlateNumber'] ?? ''),
        'PhoneNumber' => trim($_POST['PhoneNumber'] ?? ''),
        'Type'        => $_POST['Type'] ?? '',
        // 'VehicleType' => $_POST['VehicleType'] ?? '',
        'Email'       => trim($_POST['Email'] ?? ''),
        'password'    => $_POST['password'] ?? ''
    ];

    $registration_result = $user->register($form_data);

    if ($registration_result['success']) {
        $success_message = $registration_result['message'];
        $form_data = [];
    } else {
        $error_message = $registration_result['message'];
    }
}

// Load the view
include __DIR__ . '/../views/register.php';
