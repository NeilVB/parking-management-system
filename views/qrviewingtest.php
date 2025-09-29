<?php
require_once __DIR__ . '/../controller/config.php';
require_once __DIR__ . '/../models/Vehicle.php';

$token = $_GET['token'] ?? null;

if (!$token) {
    echo "<h2>No token provided.</h2>";
    exit;
}

$vehicleModel = new Vehicle($conn);
$data = $vehicleModel->getVehicleWithUserByToken($token);

if (!$data) {
    echo "<h2>Invalid or expired token.</h2>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QR Vehicle & User Lookup</title>
    <link rel="stylesheet" href="../views/accountstyle.css">
</head>
<body>
    <div class="vehicle-lookup-container">
        <h1>QR Scan Verified ✅</h1>

        <section class="vehicle-info">
            <h2>Vehicle Details</h2>
            <ul class="vehicle-details">
                <li><strong>Plate Number:</strong> <?= htmlspecialchars($data['plate_number']) ?></li>
                <li><strong>Type:</strong> <?= htmlspecialchars($data['vehicle_type']) ?></li>
                <li><strong>Color:</strong> <?= htmlspecialchars($data['color']) ?></li>
                <li><strong>Make:</strong> <?= htmlspecialchars($data['make']) ?></li>
                <li><strong>Model:</strong> <?= htmlspecialchars($data['model']) ?></li>
                <li><strong>Year:</strong> <?= htmlspecialchars($data['year']) ?></li>
            </ul>
        </section>

        <section class="user-info">
            <h2>User Details</h2>
            <ul class="user-details">
                <li><strong>Name:</strong> <?= htmlspecialchars($data['firstname'] . ' ' . $data['lastname']) ?></li>
                <li><strong>Email:</strong> <?= htmlspecialchars($data['email']) ?></li>
                <li><strong>Phone:</strong> 0<?= htmlspecialchars($data['phone_number']) ?></li>
                <li><strong>User Type:</strong> <?= htmlspecialchars($data['user_type']) ?></li>
            </ul>
        </section>

        <p class="access-message">This vehicle and user are authorized for gate-side access.</p>
    </div>
</body>
</html>
