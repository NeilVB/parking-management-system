<?php
// views/scan_qr.php
require_once '../models/Database.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QR Code Scanner | Parking Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../publicstyle.css">
    <script src="https://unpkg.com/html5-qrcode"></script> <!-- QR scanner library -->
    <style>
        body { font-family: Arial, sans-serif; background: #f6f6f6; text-align: center; }
        #reader { width: 350px; margin: 20px auto; }
        .info-card {
            background: white; border-radius: 10px; padding: 15px; width: 400px;
            margin: 20px auto; box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }
        .info-card h3 { margin-bottom: 10px; }
        .btn-action {
            padding: 10px 20px; border: none; border-radius: 5px;
            background: #007bff; color: white; cursor: pointer; margin: 5px;
        }
        .btn-action:hover { background: #0056b3; }
        #status { margin-top: 15px; font-weight: bold; }
    </style>
</head>
<body>
    <h2><i class="fas fa-qrcode"></i> Scan Vehicle QR Code</h2>

    <div id="reader"></div>
    <div id="result"></div>

    <div id="vehicle-info" class="info-card" style="display:none;">
        <h3>Vehicle Information</h3>
        <p><strong>Plate No:</strong> <span id="plate"></span></p>
        <p><strong>Owner:</strong> <span id="owner"></span></p>
        <p><strong>Status:</strong> <span id="current-status"></span></p>

        <button class="btn-action" id="btn-entered">Mark Entered</button>
        <button class="btn-action" id="btn-parked">Mark Parked</button>
        <button class="btn-action" id="btn-exiting">Mark Exiting</button>
        <button class="btn-action" id="btn-outside">Mark Outside</button>

        <div id="status"></div>
    </div>

    <script>
        const html5QrCode = new Html5Qrcode("reader");
        const qrConfig = { fps: 10, qrbox: 250 };

        function startScanner() {
            html5QrCode.start({ facingMode: "environment" }, qrConfig, qrCodeMessage => {
                html5QrCode.stop();
                handleScan(qrCodeMessage);
            }).catch(err => console.error("Camera Error: ", err));
        }

        function handleScan(qr_token) {
            fetch('../ajax/scan_vehicle.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=scan&qr_token=' + encodeURIComponent(qr_token)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById("vehicle-info").style.display = "block";
                    document.getElementById("plate").textContent = data.vehicle.plate_no;
                    document.getElementById("owner").textContent = data.vehicle.owner_name;
                    document.getElementById("current-status").textContent = data.status;
                    document.getElementById("status").textContent = data.message;
                } else {
                    alert(data.message);
                    html5QrCode.start({ facingMode: "environment" }, qrConfig, qrCodeMessage => {
                        html5QrCode.stop();
                        handleScan(qrCodeMessage);
                    });
                }
            });
        }

        // Button actions
        document.getElementById("btn-entered").addEventListener("click", () => updateStatus("entered"));
        document.getElementById("btn-parked").addEventListener("click", () => updateStatus("parked"));
        document.getElementById("btn-exiting").addEventListener("click", () => updateStatus("exiting"));
        document.getElementById("btn-outside").addEventListener("click", () => updateStatus("outside"));

        function updateStatus(status) {
            const plate = document.getElementById("plate").textContent;

            fetch('../ajax/scan_vehicle.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=update&status=${status}&plate_no=${encodeURIComponent(plate)}`
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById("status").textContent = data.message;
            });
        }

        startScanner();
    </script>
</body>
</html>
