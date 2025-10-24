/**
 * qr_scanner.js - QR Scanner & Business Logic
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

    // === INITIALIZATION ===

    initializeScanButton();
    initializeExitModalHandlers();
    initializeParkingSpaceSelection();
    setupCleanupHandlers();

    // === SCAN BUTTON INITIALIZATION ===

    function initializeScanButton() {
        const openScanBtn = document.getElementById("open-scan-modal-btn");
        
        if (openScanBtn) {
            openScanBtn.addEventListener("click", (e) => {
                e.preventDefault();
                console.log("[Scanner] Scan button clicked");
                
                // Use modal UI function from qr_modals.js
                if (typeof window.openScanModal === "function") {
                    window.openScanModal();
                }
            });
            console.log("[Scanner] Scan button listener attached");
        } else {
            console.warn("[Scanner] open-scan-modal-btn not found in DOM");
        }
    }

    // === CLEANUP HANDLERS ===

    function setupCleanupHandlers() {
        // Cleanup on page visibility change
        document.addEventListener("visibilitychange", () => {
            if (document.hidden && isScanning) {
                stopQrScanner();
            }
        });

        // Cleanup on page unload
        window.addEventListener("beforeunload", stopQrScanner);
    }

    // === QR SCANNER CORE ===

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
            console.warn("[Scanner] Scanner already running");
            return;
        }

        console.log("[Scanner] Starting scanner...");
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
                            console.log("[Scanner] Scanner started successfully");
                            elements.resultContainer.style.display = "none";
                        })
                        .catch(err => {
                            console.error("[Scanner] Camera access denied:", err);
                            showError("Unable to access camera. Please allow permissions.");
                            isScanning = false;
                        });
                });
            })
            .catch(err => {
                console.error("[Scanner] Import failed:", err);
                showError("Failed to load QR scanner");
                isScanning = false;
            });
    }

    function stopQrScanner() {
        if (!isScanning) return;

        console.log("[Scanner] Stopping scanner...");
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

    // Expose scanner control functions globally
    window.startQrScanner = startQrScanner;
    window.stopQrScanner = stopQrScanner;

    // === QR CODE PROCESSING ===

    async function handleQrCodeDetected(result) {
        if (isProcessing) {
            console.log("[Scanner] Already processing, ignoring duplicate scan");
            return;
        }

        isProcessing = true;
        console.log("[Scanner] QR Code detected:", result.data);

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
                console.error("[Scanner] Non-JSON response:", text);
                throw new Error("Server returned invalid response");
            }

            const data = await response.json();
            console.log("[Scanner] Server response:", data);

            if (data.success && data.vehicle) {
                await processVehicleData(data.vehicle);
            } else {
                showError(data.message || "Vehicle not found");
                setTimeout(() => {
                    if (window.closeScanModal) window.closeScanModal();
                }, 2000);
            }

        } catch (error) {
            console.error("[Scanner] Error processing QR code:", error);
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

        // Close QR modal using UI function
        if (window.closeScanModal) window.closeScanModal();

        console.log(`[Scanner] Vehicle status: ${status}`);

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

    // === VEHICLE EXIT FLOW ===

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
            console.error('[Scanner] Error getting exit info:', error);
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
            console.log('[Scanner] Exit confirmation modal shown');
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
            btn.addEventListener('click', () => {
                if (window.closeExitModal) window.closeExitModal();
                isProcessing = false;
            });
        });

        // Click outside to close
        elements.exitModal?.addEventListener('click', (e) => {
            if (e.target === elements.exitModal) {
                if (window.closeExitModal) window.closeExitModal();
                isProcessing = false;
            }
        });

        // Success modal close buttons
        const successCloseBtns = elements.exitSuccessModal?.querySelectorAll('.close-btn, .close-modal');
        successCloseBtns?.forEach(btn => {
            btn.addEventListener('click', () => {
                if (window.closeExitSuccessModal) window.closeExitSuccessModal();
                // Optionally reopen scan modal
                if (window.openScanModal) window.openScanModal();
            });
        });

        // Click outside success modal to close
        elements.exitSuccessModal?.addEventListener('click', (e) => {
            if (e.target === elements.exitSuccessModal) {
                if (window.closeExitSuccessModal) window.closeExitSuccessModal();
                if (window.openScanModal) window.openScanModal();
            }
        });

        console.log('[Scanner] Exit modal handlers initialized');
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
                // Close exit confirmation modal using UI function
                if (window.closeExitModal) window.closeExitModal();

                // Show success modal using UI function
                if (window.showExitSuccessModal) {
                    window.showExitSuccessModal(data);
                }

                console.log('[Scanner] Vehicle exit successful');
            } else {
                alert('❌ ' + (data.message || 'Failed to process exit'));
                
                // Re-enable button
                if (confirmBtn) {
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = '<i class="fas fa-check-circle"></i> Confirm Exit';
                }
            }
        } catch (error) {
            console.error('[Scanner] Error confirming exit:', error);
            alert('❌ Error processing exit. Please try again.');
            
            // Re-enable button
            if (confirmBtn) {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<i class="fas fa-check-circle"></i> Confirm Exit';
            }
        }
    }

    // === VEHICLE ENTRY FLOW ===

    function handleVehicleEntry(vehicle) {
        // Populate vehicle information
        document.getElementById("v-owner").textContent = vehicle.driver_name || "N/A";
        document.getElementById("v-plate").textContent = vehicle.plate_number || "N/A";
        document.getElementById("v-user-type").textContent = vehicle.user_type || "N/A";
        document.getElementById("v-type").textContent = vehicle.vehicle_type || "N/A";
        document.getElementById("v-status").textContent = vehicle.status || "N/A";

        // Show vehicle info modal
        if (typeof bootstrap !== 'undefined') {
            const modal = new bootstrap.Modal(elements.vehicleInfoModal);
            modal.show();
        } else {
            showVanillaModal(elements.vehicleInfoModal);
        }

        // Initialize parking filters
        initializeParkingFilters();

        isProcessing = false;
        console.log('[Scanner] Vehicle entry modal shown');
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

    // === PARKING SPACE FILTERS ===

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
                spacesCount.textContent = `${visibleCount} of ${availableCount} spaces`;
            }
        }

        // Attach event listeners
        searchInput?.addEventListener('input', filterSpaces);
        typeFilter?.addEventListener('change', filterSpaces);
        vehicleFilter?.addEventListener('change', filterSpaces);

        // Initial filter
        filterSpaces();
    }

    // === PARKING SPACE SELECTION ===

    function initializeParkingSpaceSelection() {
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.select-parking-btn');
            if (!btn) return;

            // Prevent duplicate processing
            e.stopImmediatePropagation();
            e.preventDefault();

            if (btn.hasAttribute('data-processing')) {
                console.log('[Scanner] Button already being processed');
                return;
            }

            const parkingId = btn.dataset.id;
            const parkingName = btn.dataset.name;

            if (!currentVehicleData) {
                alert('❌ Vehicle data not found. Please scan again.');
                return;
            }

            console.log('[Scanner] Assigning vehicle to parking:', parkingName);

            // Mark as processing
            btn.setAttribute('data-processing', 'true');

            // Assign immediately (no confirmation as per original qr_modals.js)
            assignVehicleToParking(parkingId, parkingName, currentVehicleData);

            // Remove processing flag after delay
            setTimeout(() => {
                btn.removeAttribute('data-processing');
            }, 1000);
        });
    }

    // === PARKING ASSIGNMENT ===

    async function assignVehicleToParking(parkingId, parkingName, vehicleData) {
        console.log('[Scanner] Starting assignment:', { parkingId, parkingName, vehicleData });
        
        // Show loading state
        const btn = document.querySelector(`.select-parking-btn[data-id="${parkingId}"]`);
        const originalText = btn?.innerHTML;
        if (btn) {
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Assigning...';
            btn.disabled = true;
        }

        try {
            // Use JSON format like original qr_modals.js
            const response = await fetch('../controller/assign_parking.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    parking_space_id: parkingId,
                    plate_number: vehicleData.plate_number
                })
            });

            console.log('[Scanner] Response status:', response.status);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('[Scanner] Assignment response:', data);

            if (data.success) {
                console.log('[Scanner] Assignment successful');
                
                // Close vehicle info modal using UI function
                if (window.closeVehicleInfoModal) {
                    window.closeVehicleInfoModal();
                }

                // Close parking selection modal if using Bootstrap
                const parkingModal = bootstrap.Modal.getInstance(document.getElementById('parkingSelectionModal'));
                if (parkingModal) {
                    parkingModal.hide();
                }

                // Show success modal using UI function
                if (window.showAssignmentSuccessModal) {
                    window.showAssignmentSuccessModal({
                        plate_number: vehicleData.plate_number,
                        parking_name: parkingName,
                        assigned_time: new Date().toLocaleString('en-US', {
                            month: 'short',
                            day: 'numeric',
                            year: 'numeric',
                            hour: 'numeric',
                            minute: '2-digit',
                            hour12: true
                        })
                    });
                }

                currentVehicleData = null;

            } else {
                console.log('[Scanner] Assignment failed:', data.message);
                
                // Show error using alert like original
                alert('Error: ' + (data.message || 'Failed to assign vehicle'));
                
                // Restore button
                if (btn) {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            }
        } catch (error) {
            console.error('[Scanner] Assignment error:', error);
            
            // Show error using alert like original
            alert('An error occurred while assigning the vehicle');

            // Restore button
            if (btn) {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }
    }

    // === UTILITY FUNCTIONS ===

    function showError(message) {
        elements.resultText.textContent = `❌ ${message}`;
        elements.resultContainer.style.display = "block";
    }

    // === INITIALIZATION COMPLETE ===

    console.log("[Scanner] QR Scanner module initialized successfully");
});