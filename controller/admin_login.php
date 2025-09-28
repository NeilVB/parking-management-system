<?php
// controller/admin_login.php
session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../models/Admin.php';

$error_message = '';
$success_message = '';
$email = '';

$admin = new Admin($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['admin_email']);
    $password = $_POST['admin_password'];

    $login_result = $admin->login($email, $password);

    if ($login_result['success']) {
        $_SESSION['admin_id']   = $login_result['admin']['admin_id'];
        $_SESSION['firstname']  = $login_result['admin']['firstname'];
        $_SESSION['lastname']   = $login_result['admin']['lastname'];
        $_SESSION['email']      = $login_result['admin']['admin_email'];
        $_SESSION['user_type']  = 'admin';

        if (!empty($_POST['remember'])) {
            setcookie('remember_admin', $login_result['admin']['admin_id'], time() + (30 * 24 * 60 * 60), '/');
        }

        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error_message = $login_result['message'];
    }
}

if (!isset($_SESSION['admin_id']) && isset($_COOKIE['remember_admin'])) {
    $admin_id = $_COOKIE['remember_admin'];
    $admin_data = $admin->getAdminById($admin_id);

    if ($admin_data) {
        $_SESSION['admin_id']   = $admin_data['admin_id'];
        $_SESSION['firstname']  = $admin_data['firstname'];
        $_SESSION['lastname']   = $admin_data['lastname'];
        $_SESSION['email']      = $admin_data['admin_email'];
        $_SESSION['user_type']  = 'admin';

        header("Location: admin_dashboard.php");
        exit();
    }
}

include __DIR__ . '/../views/admin_login.php';
