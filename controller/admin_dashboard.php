<?php
// controller/admin_dashboard.php
session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../models/Admin.php';

$error_message = '';
$success_message = '';
$email = '';

// Access control
if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: admin_login.php?access=denied");
    exit();
}

$admin_id  = $_SESSION['admin_id'];
$firstname = $_SESSION['firstname'] ?? 'Admin';
$lastname  = $_SESSION['lastname'] ?? '';
$email     = $_SESSION['email'] ?? '';
$user_type = $_SESSION['user_type'];

// Logout handler
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();

    if (isset($_COOKIE['remember_admin'])) {
        setcookie('remember_admin', '', time() - 3600, '/');
    }

    header("Location: admin_login.php?logout=success");
    exit();
}

include __DIR__ . '../views/admin_dashboard.php';
