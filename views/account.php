<?php
// Start session and check authentication
// session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../controller/login.php");
    exit();
}

// Composer autoload for QR code
require_once __DIR__ . '/../vendor/autoload.php';
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

// Fetch session variables
$user_id     = $_SESSION['user_id'];
$firstname   = $_SESSION['firstname'] ?? 'User';
$lastname    = $_SESSION['lastname'] ?? '';
$userType    = $_SESSION['user_type'] ?? 'N/A';
$phoneNumber = $_SESSION['phone_number'] ?? 'N/A';
$email       = $_SESSION['email'] ?? 'N/A';

// Fetch vehicles
require_once __DIR__ . '/../models/Vehicle.php';
$vehicleModel = new Vehicle($conn);
$vehicles = $vehicleModel->getVehiclesByUser($user_id);

// QR generator helper
function generateQrIfMissing($vehicle) {
    $qrPath = __DIR__ . "/../assets/QR/qr-" . $vehicle['plate_number'] . ".png";
    $url = "localhost/parking-management-system/views/qrviewingtest.php?token=" . urlencode($vehicle['qr_token']);

    $builder = new Builder(
        writer: new PngWriter(),
        writerOptions: [],
        validateResult: false,
        data: $url,
        encoding: new Encoding('UTF-8'),
        errorCorrectionLevel: ErrorCorrectionLevel::High,
        size: 300,
        margin: 10,
        roundBlockSizeMode: RoundBlockSizeMode::Margin
    );

    $builder->build()->saveToFile($qrPath);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Account - NUSpace</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../views/dashboardstyle.css">
    <link rel="stylesheet" href="../views/accountstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="dashboard-container">
    <!-- Header -->
    <header class="header">
        <div class="header-logo">
            <h2><a href="dashboard.php"><img src="../assets/NULogo.png" alt="NUSpace Logo" class="nu-logo"> NUSpace</a></h2>
        </div>
        <div class="user-profile">
            <i class="fas fa-bell"></i>
            <a href="../views/account.php" class="user-profile-link">
                <span>Hi, <?= htmlspecialchars($firstname) ?></span>
                <img src="https://i.pravatar.cc/150" alt="User Avatar" class="avatar">
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="account-page-content">
        <section class="account-card">
            <h1 class="account-title">Account</h1>
            <div class="account-header">
                <img src="https://i.pravatar.cc/150" alt="User Profile Picture" class="account-avatar">
                <h2 class="account-name"><?= htmlspecialchars("$firstname $lastname") ?></h2>
            </div>
            <ul class="account-details">
                <li><i class="fas fa-user-tag"></i><div><span class="detail-label">Type</span><span class="detail-value"><?= htmlspecialchars($userType) ?></span></div></li>
                <li><i class="fas fa-phone"></i><div><span class="detail-label">Phone Number</span><span class="detail-value">0<?= htmlspecialchars($phoneNumber) ?></span></div></li>
                <li><i class="fas fa-envelope"></i><div><span class="detail-label">Email</span><span class="detail-value"><?= htmlspecialchars($email) ?></span></div></li>
            </ul>

            <!-- Vehicle Tabs -->
            <div class="vehicle-tabs-container">
                <div class="vehicle-tab-buttons">
                    <button class="vehicle-tab-btn active" data-tab="slot1">Vehicle Slot 1</button>
                    <button class="vehicle-tab-btn" data-tab="slot2">Vehicle Slot 2</button>
                </div>
                <div class="vehicle-tab-content">
                    <?php for ($i = 0; $i < 2; $i++): ?>
                        <?php $slotId = "slot" . ($i + 1); ?>
                        <div id="<?= $slotId ?>" class="vehicle-slot <?= $i === 0 ? 'active' : '' ?>">
                            <?php if (!empty($vehicles[$i])): ?>
                                <?php $vehicle = $vehicles[$i]; generateQrIfMissing($vehicle); ?>
                                <ul class="vehicle-details">
                                    <li><strong>Plate Number:</strong> <?= htmlspecialchars($vehicle['plate_number']) ?></li>
                                    <li><strong>Type:</strong> <?= htmlspecialchars($vehicle['vehicle_type']) ?></li>
                                    <li><strong>Color:</strong> <?= htmlspecialchars($vehicle['color']) ?></li>
                                    <li><strong>Make:</strong> <?= htmlspecialchars($vehicle['make']) ?></li>
                                    <li><strong>Model:</strong> <?= htmlspecialchars($vehicle['model']) ?></li>
                                    <li><strong>Year:</strong> <?= htmlspecialchars($vehicle['year']) ?></li>
                                </ul>
                                <div class="qr-section">
                                    <h3>QR Code</h3>
                                    <img src="../assets/QR/qr-<?= htmlspecialchars($vehicle['plate_number']) ?>.png" alt="QR for <?= htmlspecialchars($vehicle['plate_number']) ?>" class="qr-code-img">
                                    <a href="../assets/QR/qr-<?= htmlspecialchars($vehicle['plate_number']) ?>.png"
                                        class="download-qr-link"
                                        download="QR-<?= htmlspecialchars($vehicle['plate_number']) ?>.png">
                                        Download QR
                                    </a>
                                </div>
                            <?php else: ?>
                                <p class="empty-slot">No vehicle registered in this slot.</p>
                            <?php endif; ?>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="account-footer">
        <span>NUSpace © 2025</span>
        <div class="footer-links">
            <a href="#">Terms And Conditions</a>
            <a href="#">Policy And Regulation</a>
        </div>
    </footer>
</div>
<script src="../views/js/account.js"></script>
</body>
</html>
