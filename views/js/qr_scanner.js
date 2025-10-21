/**
 * QR Scanner Module
 * Handles QR code scanning for vehicle entry and exit
 */

document.addEventListener("DOMContentLoaded", () => {
    // DOM Elements
    const elements = {
        scanModal: document.getElementById("scan-vehicle-modal"),
        resultText: document.getElementById("result-text"),
        resultContainer: document.getElementById("scan-result"),
        videoContainer: document.getElementById("qr-video-container"),
        startBtn: document.getElementById("start-scan-btn"),
        stopBtn: document.getElementById("stop-scan-btn"),
        exitModal: document.getElementById("exit-vehicle-modal"),
        exitSuccessModal: document.getElementById("exit-success-modal"),
        vehicleInfoModal: document.getElementById("vehicleInfoModal")
    };

    // Scanner state
    let qrScanner = null;
    let isScanning = false;
    let isProcessing = false;
    let videoElem = null;
    let currentVehicleData = null;

    // ============================
    // INITIALIZATION
    // ============================

    initializeScannerControls();
    initializeExitModalHandlers();
    initializeParkingSpaceSelection();

    // ============================
    // SCANNER CONTROLS
    // ============================

    function initializeScannerControls() {
        // Open scan modal button
        const openScanBtn = document.getElementById("open-scan-modal-btn");
        if (openScanBtn) {
            openScanBtn.addEventListener("click", (e) => {
                e.preventDefault();
                openScanModal();
            });
        }

        // Close buttons
        const closeBtns = elements.scanModal?.querySelectorAll(".close-btn");
        closeBtns?.forEach(btn => {
            btn.addEventListener("click", closeScanModal);
        });

        // Start/Stop buttons
        elements.startBtn?.addEventListener("click", startScanning);
        elements.stopBtn?.addEventListener("click", stopScanning);

        // Click outside modal to close
        elements.scanModal?.addEventListener("click", (e) => {
            if (e.target === elements.scanModal) {
                closeScanModal();
            }
        });

        // Cleanup on page visibility change
        document.addEventListener("visibilitychange", () => {
            if (document.hidden && isScanning) {
                stopQrScanner();
            }
        });

        window.addEventListener("beforeunload", stopQrScanner);
    }

    function openScanModal() {
        if (!elements.scanModal) return;

        elements.scanModal.style.display = "flex";
        elements.resultContainer.style.display = "none";
        elements.resultText.textContent = "Processing...";
        elements.stopBtn.style.display = "none";
        elements.startBtn.style.display = "inline-block";

        console.log("[QR] Scan modal opened");
    }

    function closeScanModal() {
        if (!elements.scanModal) return;

        elements.scanModal.style.display = "none";
        stopQrScanner();
        console.log("[QR] Scan modal closed");
    }

    function startScanning() {
        elements.startBtn.style.display = "none";
        elements.stopBtn.style.display = "inline-block";

        const placeholder = document.getElementById("placeholder-canvas");
        if (placeholder) placeholder.style.display = "none";

        startQrScanner();
    }

    function stopScanning() {
        elements.stopBtn.style.display = "none";
        elements.startBtn.style.display = "inline-block";
        stopQrScanner();
    }

    // ============================
    // QR SCANNER CORE
    // ============================

    function createVideoElement() {
        const oldVideo = document.getElementById("qr-video");
        if (oldVideo) oldVideo.remove();

        const newVideo = document.createElement("video");
        newVideo.id = "qr-video";
        newVideo.autoplay = true;
        newVideo.playsInline = true;
        newVideo.style.width = "100%";
        newVideo.style.height = "auto";
        elements.videoContainer.appendChild(newVideo);
        return newVideo;
    }

    function startQrScanner() {
        if (isScanning) {
            console.warn("[QR] Scanner already running");
            return;
        }

        console.log("[QR] Starting scanner...");
        isScanning = true;
        isProcessing = false;
        videoElem = createVideoElement();

        import("https://unpkg.com/qr-scanner@1.4.2/qr-scanner.min.js")
            .then(({ default: QrScanner }) => {
                QrScanner.hasCamera().then(hasCamera => {
                    if (!hasCamera) {
                        showError("No camera detected");
                        isScanning = false;
                        return;
                    }

                    qrScanner = new QrScanner(
                        videoElem,
                        result => handleQrCodeDetected(result),
                        {
                            preferredCamera: "environment",
                            highlightScanRegion: true,
                            highlightCodeOutline: true,
                            maxScansPerSecond: 1
                        }
                    );

                    qrScanner.start()
                        .then(() => {
                            console.log("[QR] Scanner started successfully");
                            elements.resultContainer.style.display = "none";
                        })
                        .catch(err => {
                            console.error("[QR] Camera access denied:", err);
                            showError("Unable to access camera. Please allow permissions.");
                            isScanning = false;
                        });
                });
            })
            .catch(err => {
                console.error("[QR] Import failed:", err);
                showError("Failed to load QR scanner");
                isScanning = false;
            });
    }

    function stopQrScanner() {
        if (!isScanning) return;

        console.log("[QR] Stopping scanner...");
        isScanning = false;
        isProcessing = false;

        if (qrScanner) {
            qrScanner.stop();
            qrScanner.destroy();
            qrScanner = null;
        }

        if (videoElem) {
            const stream = videoElem.srcObject;
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
            videoElem.srcObject = null;
            videoElem.remove();
            videoElem = null;
        }
    }

    // ============================
    // QR CODE PROCESSING
    // ============================

    async function handleQrCodeDetected(result) {
        if (isProcessing) {
            console.log("[QR] Already processing, ignoring duplicate scan");
            return;
        }

        isProcessing = true;
        console.log("[QR] QR Code detected:", result.data);

        if (qrScanner) qrScanner.stop();

        elements.resultContainer.style.display = "block";
        elements.resultText.textContent = `Scanned: ${result.data}`;

        try {
            // Extract token from URL or use raw data
            const tokenMatch = result.data.match(/token=([a-zA-Z0-9]+)/);
            const token = tokenMatch ? tokenMatch[1] : result.data;

            const formData = new FormData();
            formData.append("action", "scan");
            formData.append("qr_token", token);

            const response = await fetch("../ajax/scan_vehicle.php", {
                method: "POST",
                body: formData
            });

            // Validate JSON response
            const contentType = response.headers.get("content-type");
            if (!contentType?.includes("application/json")) {
                const text = await response.text();
                console.error("[QR] Non-JSON response:", text);
                throw new Error("Server returned invalid response");
            }

            const data = await response.json();
            console.log("[QR] Server response:", data);

            if (data.success && data.vehicle) {
                await processVehicleData(data.vehicle);
            } else {
                showError(data.message || "Vehicle not found");
                setTimeout(closeScanModal, 2000);
            }

        } catch (error) {
            console.error("[QR] Error processing QR code:", error);
            showError("Server error. Please try again.");
            setTimeout(() => {
                isProcessing = false;
                stopQrScanner();
            }, 2000);
        }
    }

    async function processVehicleData(vehicle) {
        currentVehicleData = vehicle;
        const status = vehicle.status.toLowerCase();

        // Close QR modal
        closeScanModal();

        console.log(`[QR] Vehicle status: ${status}`);

        // Route based on vehicle status
        if (status === 'parked' || status === 'waiting') {
            // EXIT FLOW
            await handleVehicleExit(vehicle);
        } else if (status === 'outside') {
            // ENTRY FLOW
            handleVehicleEntry(vehicle);
        } else {
            showError(`Unknown vehicle status: ${status}`);
            isProcessing = false;
        }
    }

    // ============================
    // VEHICLE EXIT FLOW
    // ============================

    async function handleVehicleExit(vehicle) {
        try {
            const formData = new FormData();
            formData.append('action', 'get_exit_info');
            formData.append('vehicle_id', vehicle.vehicle_id);

            const response = await fetch('../ajax/exit_vehicle.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success && data.exit_info) {
                showExitConfirmationModal(data.exit_info);
            } else {
                alert('❌ ' + (data.message || 'Unable to process exit'));
                isProcessing = false;
            }
        } catch (error) {
            console.error('[QR] Error getting exit info:', error);
            alert('❌ Error processing vehicle exit');
            isProcessing = false;
        }
    }

    function showExitConfirmationModal(exitInfo) {
        // Populate modal fields
        document.getElementById('exit-vehicle-owner').textContent = exitInfo.driver_name || 'N/A';
        document.getElementById('exit-plate-number').textContent = exitInfo.plate_number || 'N/A';
        document.getElementById('exit-vehicle-type').textContent = exitInfo.vehicle_type || 'N/A';
        document.getElementById('exit-parking-space').textContent = exitInfo.parking_space || 'N/A';
        document.getElementById('exit-entry-time').textContent = exitInfo.entry_time || 'N/A';
        document.getElementById('exit-duration').textContent = exitInfo.duration || 'N/A';

        // Store IDs for confirmation
        document.getElementById('exit-vehicle-id').value = exitInfo.vehicle_id || '';
        document.getElementById('exit-parking-transaction-id').value = exitInfo.log_id || '';

        // Store parking_space_id
        const confirmBtn = document.getElementById('confirm-exit-btn');
        if (confirmBtn) {
            confirmBtn.setAttribute('data-parking-space-id', exitInfo.parking_space_id || '');
            confirmBtn.setAttribute('data-log-id', exitInfo.log_id || '');
        }

        // Show exit modal
        if (elements.exitModal) {
            elements.exitModal.style.display = 'flex';
            console.log('[QR] Exit confirmation modal shown');
        }

        isProcessing = false;
    }

    function initializeExitModalHandlers() {
        // Confirm exit button
        const confirmExitBtn = document.getElementById('confirm-exit-btn');
        if (confirmExitBtn) {
            confirmExitBtn.addEventListener('click', confirmVehicleExit);
        }

        // Cancel buttons in exit modal
        const exitCancelBtns = elements.exitModal?.querySelectorAll('.close-btn, .close-modal');
        exitCancelBtns?.forEach(btn => {
            btn.addEventListener('click', closeExitModal);
        });

        // Click outside to close
        elements.exitModal?.addEventListener('click', (e) => {
            if (e.target === elements.exitModal) {
                closeExitModal();
            }
        });

        // Success modal close buttons
        const successCloseBtns = elements.exitSuccessModal?.querySelectorAll('.close-btn, .close-modal');
        successCloseBtns?.forEach(btn => {
            btn.addEventListener('click', closeExitSuccessModal);
        });

        // Click outside success modal to close
        elements.exitSuccessModal?.addEventListener('click', (e) => {
            if (e.target === elements.exitSuccessModal) {
                closeExitSuccessModal();
            }
        });

        console.log('[QR] Exit modal handlers initialized');
    }

    function closeExitModal() {
        if (elements.exitModal) {
            elements.exitModal.style.display = 'none';
            isProcessing = false;
            console.log('[QR] Exit modal closed');
        }
    }

    function closeExitSuccessModal() {
        if (elements.exitSuccessModal) {
            elements.exitSuccessModal.style.display = 'none';
            console.log('[QR] Exit success modal closed');
            
            // Optionally reopen scan modal for next vehicle
            openScanModal();
        }
    }

    async function confirmVehicleExit() {
        const vehicleId = document.getElementById('exit-vehicle-id')?.value;
        const logId = document.getElementById('exit-parking-transaction-id')?.value;
        const confirmBtn = document.getElementById('confirm-exit-btn');
        const parkingSpaceId = confirmBtn?.getAttribute('data-parking-space-id');

        if (!vehicleId || !logId) {
            alert('❌ Missing vehicle information');
            return;
        }

        // Disable button and show loading
        if (confirmBtn) {
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        }

        try {
            const formData = new FormData();
            formData.append('action', 'confirm_exit');
            formData.append('vehicle_id', vehicleId);
            formData.append('log_id', logId);
            formData.append('parking_space_id', parkingSpaceId || '');

            const response = await fetch('../ajax/exit_vehicle.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Close exit confirmation modal
                closeExitModal();

                // Show success modal
                showExitSuccessModal(data);

                console.log('[QR] Vehicle exit successful');
            } else {
                alert('❌ ' + (data.message || 'Failed to process exit'));
                
                // Re-enable button
                if (confirmBtn) {
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = '<i class="fas fa-check-circle"></i> Confirm Exit';
                }
            }
        } catch (error) {
            console.error('[QR] Error confirming exit:', error);
            alert('❌ Error processing exit. Please try again.');
            
            // Re-enable button
            if (confirmBtn) {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<i class="fas fa-check-circle"></i> Confirm Exit';
            }
        }
    }

    function showExitSuccessModal(data) {
        // Populate success modal - FIX: access exit_summary object
        document.getElementById('success-plate-number').textContent = data.exit_summary?.plate_number || 'N/A';
        document.getElementById('success-exit-time').textContent = data.exit_summary?.exit_time || new Date().toLocaleString();
        document.getElementById('success-total-duration').textContent = data.exit_summary?.total_duration || 'N/A';

        // Show success modal
        if (elements.exitSuccessModal) {
            elements.exitSuccessModal.style.display = 'flex';
        }
    }

    // ============================
    // VEHICLE ENTRY FLOW
    // ============================

    function handleVehicleEntry(vehicle) {
        // Populate vehicle information
        document.getElementById("v-owner").textContent = vehicle.driver_name || "N/A";
        document.getElementById("v-plate").textContent = vehicle.plate_number || "N/A";
        document.getElementById("v-user-type").textContent = vehicle.user_type || "N/A";
        document.getElementById("v-type").textContent = vehicle.vehicle_type || "N/A";
        document.getElementById("v-status").textContent = vehicle.status || "N/A";

        // Show vehicle info modal (Bootstrap or vanilla)
        if (typeof bootstrap !== 'undefined') {
            const modal = new bootstrap.Modal(elements.vehicleInfoModal);
            modal.show();
        } else {
            showVanillaModal(elements.vehicleInfoModal);
        }

        // Initialize parking filters
        initializeParkingFilters();

        isProcessing = false;
        console.log('[QR] Vehicle entry modal shown');
    }

    function showVanillaModal(modal) {
        if (!modal) return;

        modal.classList.add('show');
        modal.style.display = 'block';
        modal.setAttribute('aria-modal', 'true');
        modal.removeAttribute('aria-hidden');
        document.body.classList.add('modal-open');

        // Add backdrop
        let backdrop = document.getElementById('vehicle-modal-backdrop');
        if (!backdrop) {
            backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = 'vehicle-modal-backdrop';
            document.body.appendChild(backdrop);
        }
    }

    // ============================
    // PARKING SPACE SELECTION
    // ============================

    function initializeParkingFilters() {
        const searchInput = document.getElementById('modal-space-search');
        const typeFilter = document.getElementById('modal-type-filter');
        const vehicleFilter = document.getElementById('modal-vehicle-filter');
        const spacesCount = document.getElementById('modal-spaces-count');
        const parkingCards = document.querySelectorAll('.modal-parking-card');

        function filterSpaces() {
            const searchTerm = searchInput?.value.toLowerCase() || '';
            const selectedType = typeFilter?.value || 'all';
            const selectedVehicle = vehicleFilter?.value || 'all';

            let visibleCount = 0;
            let availableCount = 0;

            parkingCards.forEach(card => {
                const name = (card.dataset.name || '').toLowerCase();
                const userTypes = (card.dataset.userTypes || '').split(',');
                const vehicles = (card.dataset.vehicles || '').split(',');
                const available = parseInt(card.dataset.available) || 0;

                const matchesSearch = name.includes(searchTerm);
                const matchesType = selectedType === 'all' || userTypes.includes(selectedType);
                const matchesVehicle = selectedVehicle === 'all' || vehicles.includes(selectedVehicle);

                if (matchesSearch && matchesType && matchesVehicle) {
                    card.style.display = 'block';
                    visibleCount++;
                    if (available > 0) availableCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (spacesCount) {
                spacesCount.textContent = `Showing ${visibleCount} spaces (${availableCount} available)`;
            }
        }

        // Attach event listeners
        searchInput?.addEventListener('input', filterSpaces);
        typeFilter?.addEventListener('change', filterSpaces);
        vehicleFilter?.addEventListener('change', filterSpaces);

        // Initial filter
        filterSpaces();
    }

    function initializeParkingSpaceSelection() {
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.select-parking-btn');
            if (!btn) return;

            const parkingId = btn.dataset.id;
            const parkingName = btn.dataset.name;

            if (!currentVehicleData) {
                alert('❌ Vehicle data not found. Please scan again.');
                return;
            }

            if (confirm(`Assign ${currentVehicleData.plate_number} to ${parkingName}?`)) {
                assignVehicleToParking(parkingId, currentVehicleData);
            }
        });
    }

// Debug: Check what's happening when parking button is clicked
document.addEventListener('click', function(e) {
    if (e.target.closest('.select-parking-btn')) {
        console.log('=== PARKING BUTTON CLICK DEBUG ===');
        console.log('Button clicked:', e.target.closest('.select-parking-btn'));
        console.log('Current vehicle data:', window.qrScanner?.currentVehicleData);
        console.log('Stack trace:', new Error().stack);
    }
}, true);

async function assignVehicleToParking(parkingId, vehicleData) {
    console.log('=== assignVehicleToParking CALLED ===');
    console.log('parkingId:', parkingId);
    console.log('vehicleData:', vehicleData);
    
    try {
        const formData = new FormData();
        formData.append('action', 'assign_parking');
        formData.append('parking_id', parkingId);
        formData.append('vehicle_id', vehicleData.id || vehicleData.vehicle_id);
        formData.append('plate_number', vehicleData.plate_number);

        console.log('Sending request to assign_parking.php...');

        const response = await fetch('../ajax/assign_parking.php', {
            method: 'POST',
            body: formData
        });

        console.log('Response status:', response.status);
        const data = await response.json();
        console.log('Response data:', data);

        if (data.success) {
            console.log('Assignment SUCCESS - attempting to show modal');
            
            // Get parking space name
            const parkingBtn = document.querySelector(`.select-parking-btn[data-id="${parkingId}"]`);
            const parkingName = parkingBtn ? parkingBtn.getAttribute('data-name') : 'Parking Space';
            console.log('Parking name:', parkingName);
            
            // DIRECT MODAL SHOW - no function calls
            const successModal = document.getElementById('assignment-success-modal');
            console.log('Success modal element:', successModal);
            
            if (successModal) {
                console.log('Modal found, populating content...');
                
                // Populate content directly
                const plateEl = document.getElementById('success-assigned-plate');
                const nameEl = document.getElementById('success-parking-name');
                const timeEl = document.getElementById('success-assigned-time');
                
                if (plateEl) {
                    plateEl.textContent = vehicleData.plate_number;
                    console.log('Plate element updated');
                }
                if (nameEl) {
                    nameEl.textContent = parkingName;
                    console.log('Name element updated');
                }
                if (timeEl) {
                    timeEl.textContent = new Date().toLocaleString();
                    console.log('Time element updated');
                }
                
                // SHOW THE MODAL
                successModal.style.display = 'flex';
                console.log('Modal display set to flex');
                
                // Add close handlers
                const closeBtn = successModal.querySelector('.close-btn');
                const okBtn = successModal.querySelector('.close-modal');
                
                if (closeBtn && okBtn) {
                    const closeHandler = () => {
                        successModal.style.display = 'none';
                        location.reload();
                    };
                    
                    closeBtn.onclick = closeHandler;
                    okBtn.onclick = closeHandler;
                    console.log('Close handlers attached');
                }
                
                // Auto refresh
                setTimeout(() => {
                    console.log('Auto-refreshing page...');
                    location.reload();
                }, 3000);
                
            } else {
                console.error('Success modal not found in DOM');
                alert('✅ Vehicle successfully assigned to parking space!');
                location.reload();
            }

            // Close vehicle info modal
            console.log('Closing vehicle info modal...');
            const vehicleModal = document.getElementById('vehicleInfoModal');
            if (vehicleModal) {
                vehicleModal.style.display = 'none';
                console.log('Vehicle modal closed');
            }

            currentVehicleData = null;

        } else {
            console.log('Assignment FAILED - showing error');
            alert('❌ ' + (data.message || 'Failed to assign parking space'));
        }
    } catch (error) {
        console.error('[QR] Error assigning parking:', error);
        alert('❌ Error assigning parking space. Please try again.');
    }
}

    // ============================
    // UTILITY FUNCTIONS
    // ============================

    function showError(message) {
        elements.resultText.textContent = `❌ ${message}`;
        elements.resultContainer.style.display = "block";
    }

    // Expose necessary functions globally
    window.openScanModal = openScanModal;
    window.closeScanModal = closeScanModal;
    window.startQrScanner = startQrScanner;
    window.stopQrScanner = stopQrScanner;

    console.log("[QR] Scanner module initialized successfully");
});