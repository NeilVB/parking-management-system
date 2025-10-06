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
$user_id = $_SESSION['user_id'];
$firstname = $_SESSION['firstname'] ?? 'User';
$lastname = $_SESSION['lastname'] ?? '';
$userType = $_SESSION['user_type'] ?? 'N/A';
$phoneNumber = $_SESSION['phone_number'] ?? 'N/A';
$email = $_SESSION['email'] ?? 'N/A';

// Fetch vehicles
require_once __DIR__ . '/../models/Vehicle.php';
$vehicleModel = new Vehicle($conn);
$vehicles = $vehicleModel->getVehiclesByUser($user_id);

// QR generator helper
function generateQrIfMissing($vehicle)
{
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

    <!-- JS -->
    <script src="../views/js/navigation.js"></script>

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
                <h2><a href="dashboard.php"><img src="../assets/NULogo.png" alt="NUSpace Logo" class="nu-logo">
                        NUSpace</a></h2>
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
                    <li><i class="fas fa-user-tag"></i>
                        <div><span class="detail-label">Type</span><span
                                class="detail-value"><?= htmlspecialchars($userType) ?></span></div>
                    </li>
                    <li><i class="fas fa-phone"></i>
                        <div><span class="detail-label">Phone Number</span><span
                                class="detail-value">0<?= htmlspecialchars($phoneNumber) ?></span></div>
                    </li>
                    <li><i class="fas fa-envelope"></i>
                        <div><span class="detail-label">Email</span><span
                                class="detail-value"><?= htmlspecialchars($email) ?></span></div>
                    </li>
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
                                    <?php $vehicle = $vehicles[$i];
                                    generateQrIfMissing($vehicle); ?>
                                    <ul class="vehicle-details">
                                        <li><strong>Plate Number:</strong> <?= htmlspecialchars($vehicle['plate_number']) ?>
                                        </li>
                                        <li><strong>Type:</strong> <?= htmlspecialchars($vehicle['vehicle_type']) ?></li>
                                        <li><strong>Color:</strong> <?= htmlspecialchars($vehicle['color']) ?></li>
                                        <li><strong>Make:</strong> <?= htmlspecialchars($vehicle['make']) ?></li>
                                        <li><strong>Model:</strong> <?= htmlspecialchars($vehicle['model']) ?></li>
                                        <li><strong>Year:</strong> <?= htmlspecialchars($vehicle['year']) ?></li>
                                    </ul>
                                    <div class="qr-section">
                                        <h3>QR Code</h3>
                                        <img src="../assets/QR/qr-<?= htmlspecialchars($vehicle['plate_number']) ?>.png"
                                            alt="QR for <?= htmlspecialchars($vehicle['plate_number']) ?>" class="qr-code-img">
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
        <!-- Terms and Conditions Content -->
        <div id="terms-container" class="content-section">
            <h3>Parking Terms and Conditions</h3>
            <p class="last-updated">Last Updated: September 2025</p>
            <p>By registering your vehicle and using the University Parking Management System (PMS), you agree
                to the following terms and conditions:</p>

            <ol class="terms-list">
                <li>
                    <strong>Registration Requirements</strong>
                    <ul>
                        <li>You must provide accurate details, including your plate number, University ID, and
                            active phone number.</li>
                        <li>Your phone number may be used for emergency purposes or to contact you if issues
                            arise
                            (e.g., incorrect parking, overstaying, blocking another vehicle).</li>
                    </ul>
                </li>
                <li>
                    <strong>Guard Verification</strong>
                    <ul>
                        <li>Security guards are authorized to check plate numbers and scan QR/barcodes.</li>
                        <li>You must comply with guard instructions at all times.</li>
                    </ul>
                </li>
                <li>
                    <strong>Proper Use of Parking Spaces</strong>
                    <ul>
                        <li>Only registered vehicles are allowed to park.</li>
                        <li>You must park in the assigned area (student or staff) based on your status.</li>
                        <li>Overstaying, double-parking, or parking without notice/permission is prohibited.
                        </li>
                    </ul>
                </li>
                <li>
                    <strong>Violations & Consequences</strong>
                    <ul>
                        <li><strong>Minor Violations:</strong> Causing delays for other drivers, or incomplete
                            registration may result in warnings.</li>
                        <li><strong>Major Violations (Troll Parking):</strong> Parking without notifying a
                            guard,
                            parking in the wrong area, using fake details, or repeated violations will result in
                            disciplinary action or loss of parking privileges.</li>
                    </ul>
                </li>
                <li>
                    <strong>Liability & Responsibility</strong>
                    <ul>
                        <li>The University is not liable for loss, theft, or damage to vehicles while inside the
                            premises.</li>
                        <li>Users are responsible for their own vehicles and must ensure they are parked safely.
                        </li>
                    </ul>
                </li>
                <li>
                    <strong>Agreement</strong>
                    <ul>
                        <li>By proceeding with registration, you confirm that you have read, understood, and
                            agree
                            to abide by these terms and conditions.</li>
                    </ul>
                </li>
            </ol>

            <div class="terms-footer">
                <a href="dashboard.php" class="btn-back">← Close</a>
            </div>
        </div>

        <!-- Policy and Regulations Content -->
        <div id="policy-container" class="content-section">
            <h3>Policy and Regulations</h3>
            <p class="last-updated">Last Updated: September 2025</p>

            <div class="policy-section">
                <h4>A. General Policy</h4>
                <p>The University provides regulated parking facilities for students, faculty, and staff. To
                    ensure safety, fairness, and orderliness, all members of the University community who wish
                    to avail of parking privileges must comply with the following policies and regulations.</p>
            </div>

            <div class="policy-section">
                <h4>B. Registration and Access</h4>
                <ul>
                    <li>All vehicle owners must register their vehicle(s) through the University Parking
                        Management System (PMS).</li>
                    <li>Registration requires submission of accurate information, including name, plate number,
                        college/department, and emergency contact number.</li>
                    <li>Each registered user shall be issued a unique QR code which must be presented to the
                        gate guard upon entry.</li>
                    <li>Parking privileges are non-transferable. Lending or falsifying a parking QR code is
                        subject to disciplinary action.</li>
                </ul>
            </div>

            <div class="policy-section">
                <h4>C. Role of Gate Guards</h4>
                <ul>
                    <li>Gate guards shall verify the driver's identity and QR code.</li>
                    <li>Guards will assign parking areas (student, faculty, staff, visitors) based on
                        availability.</li>
                    <li>Guards may issue reminders, warnings, or report violations directly to the Discipline
                        Office (DO).</li>
                </ul>
            </div>

            <div class="policy-section">
                <h4>D. Parking Rules and Regulations</h4>
                <ul>
                    <li>Vehicles must be parked only in the designated area assigned at entry.</li>
                    <li>Unauthorized use of faculty/staff parking areas by students, or vice versa, is a
                        violation.</li>
                    <li>Waiting in parking areas is prohibited, a waiting zone is provided outside the field.
                    </li>
                    <li>Vehicles must be properly aligned and must not obstruct other vehicles or pedestrian
                        pathways.</li>
                    <li>Users must comply with all guard instructions at all times.</li>
                </ul>
            </div>

            <div class="policy-section">
                <h4>E. Violations and Consequences</h4>
                <div class="violation-type">
                    <h5>Minor Violations (first/occasional offenses):</h5>
                    <ul>
                        <li>Improper parking, failure to follow guard instructions.</li>
                        <li><strong>Sanction:</strong> Warning or violation slip.</li>
                    </ul>
                </div>

                <div class="violation-type">
                    <h5>Major Violations (habitual or deliberate misconduct):</h5>
                    <ul>
                        <li>"Troll parking" (parking without notifying the guard, blocking other cars, repeated
                            abuse of the system).</li>
                        <li>Using falsified or borrowed QR codes.</li>
                        <li>Parking in restricted or reserved zones.</li>
                        <li><strong>Sanction:</strong> Suspension of parking privileges, disciplinary referral
                            to DO, and possible fines for damages.</li>
                    </ul>
                </div>
            </div>

            <div class="policy-section">
                <h4>F. Emergency and Accountability</h4>
                <ul>
                    <li>In case of emergencies, the University may contact the registered phone number of the
                        vehicle owner.</li>
                    <li>The University is not liable for loss, theft, or damage to vehicles or personal property
                        within the parking premises.</li>
                </ul>
            </div>

            <div class="policy-section">
                <h4>G. Acceptance of Terms</h4>
                <p>By registering a vehicle and using the University's parking facilities, the student/faculty
                    member agrees to abide by these policies and regulations and acknowledges the authority of
                    the University to impose sanctions for violations.</p>
            </div>

            <div class="terms-footer">
                <a href="dashboard.php" class="btn-back">← Close</a>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <div class="footer-content">
                <div class="copyright">
                    <p>NUSpace &copy; 2025</p>
                </div>
                <div class="footer-links">
                    <a href="#" data-target="terms-container">Terms and Conditions</a>
                    <a href="#" data-target="policy-container">Policy and Regulations</a>
                </div>
            </div>
        </footer>
    </div>
    <script src="../views/js/account.js"></script>
</body>

</html>