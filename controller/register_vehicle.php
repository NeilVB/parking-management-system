<?php
/**
 * Vehicle Registration Controller
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../models/Vehicle.php';

$error_message = '';
$success_message = '';
$form_data = [];

$vehicle = new Vehicle($conn);

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Generate secure QR token
    $qr_token = bin2hex(random_bytes(16));

    $form_data = [
        'user_id'         => $_SESSION['user_id'] ?? null,
        'plate_number'    => trim($_POST['plate_number'] ?? ''),
        'vehicle_type_id' => $_POST['vehicle_type_id'] ?? '',
        'color'           => trim($_POST['color'] ?? ''),
        'make'            => trim($_POST['make'] ?? ''),
        'model'           => trim($_POST['model'] ?? ''),
        'year'            => $_POST['year'] ?? '',
        'qr_token'        => $qr_token
    ];

    // Check if user is logged in
    if (!$form_data['user_id']) {
        $error_message = 'You must be logged in to register a vehicle.';
    } else {
        $registration_result = $vehicle->register($form_data);

        if ($registration_result['success']) {
            $success_message = $registration_result['message'];
            $form_data = [];
        } else {
            $error_message = $registration_result['message'];
        }
    }
}

header("Location: ../controller/dashboard.php?section=vehicle-registration&success=1");
exit;
