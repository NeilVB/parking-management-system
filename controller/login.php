<?php
/**
 * Login Controller
 * Handles login logic and includes the view
 */

session_start();

// Include database configuration and User model
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../models/User.php';

// Initialize variables for the view
$error_message = '';
$success_message = '';
$email = '';

// // If user is already logged in, redirect to dashboard
// if (isset($_SESSION['user_id'])) {
//     header("Location: /../dashboard.php");
//     exit();
// }

// Create User model instance
$user = new User($conn);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['Email']);
    $password = $_POST['password'];

    // Attempt login
    $login_result = $user->login($email, $password);

    if ($login_result['success']) {
        // Save user data in session
        $_SESSION['user_id']      = $login_result['user']['user_id'];
        $_SESSION['firstname']    = $login_result['user']['firstname'];
        $_SESSION['lastname']     = $login_result['user']['lastname'];
        $_SESSION['email']        = $login_result['user']['email'];
        $_SESSION['user_type']    = $login_result['user']['user_type'];
        $_SESSION['phone_number'] = $login_result['user']['phone_number'];  // ✅ newly added
        $_SESSION['plate_number'] = $login_result['user']['plate_number'];  // ✅ newly added


        // Remember Me cookie
        if (isset($_POST['remember']) && $_POST['remember'] == 'on') {
            setcookie('remember_user', $login_result['user']['user_id'], time() + (30 * 24 * 60 * 60), '/');
        }

        // Redirect to dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        $error_message = $login_result['message'];
    }
}

// Check "Remember Me" cookie
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_user'])) {
    $user_id   = $_COOKIE['remember_user'];
    $user_data = $user->getUserById($user_id);

    if ($user_data) {
        $_SESSION['user_id']      = $user_data['user_id'];
        $_SESSION['firstname']    = $user_data['firstname'];
        $_SESSION['lastname']     = $user_data['lastname'];
        $_SESSION['email']        = $user_data['email'];
        $_SESSION['user_type']    = $user_data['user_type'];
        $_SESSION['phone_number'] = $user_data['phone_number'];  // ✅ newly added
        $_SESSION['plate_number'] = $user_data['plate_number'];  // ✅ newly added


        header("Location: dashboard.php");
        exit();
    }
}

// Include the login view
include __DIR__ . '/../views/login.php';
?>