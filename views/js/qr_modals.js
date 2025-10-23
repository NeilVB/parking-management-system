/**
 * qr_modals.js - Modal UI Management Only
 */

/* === SCAN MODAL UI CONTROLS === */
const scanModal = document.getElementById("scan-vehicle-modal");
const startScanBtn = document.getElementById("start-scan-btn");
const stopScanBtn = document.getElementById("stop-scan-btn");
const closeScanBtns = scanModal ? scanModal.querySelectorAll(".close-btn") : [];

if (scanModal && startScanBtn && stopScanBtn) {
    // Open scan modal function
    window.openScanModal = function () {
        console.log("[Modal UI] openScanModal called");
        scanModal.style.display = "flex";
        document.getElementById("scan-result").style.display = "none";
        document.getElementById("result-text").textContent = "Processing...";

        stopScanBtn.style.display = "none";
        startScanBtn.style.display = "inline-block";

        requestAnimationFrame(() => {
            const video = document.getElementById("qr-video");
            if (video) {
                video.style.width = "100%";
                video.style.height = "100%";
                video.style.objectFit = "cover";
            }
        });
    };

    // Close scan modal function
    window.closeScanModal = function () {
        console.log("[Modal UI] closeScanModal called");
        scanModal.style.display = "none";
        stopScanBtn.style.display = "none";
        startScanBtn.style.display = "inline-block";
        
        // Let qr_scanner.js handle scanner cleanup
        if (window.stopQrScanner) window.stopQrScanner();
    };

    // Close buttons
    closeScanBtns.forEach(btn => btn.addEventListener("click", () => {
        window.closeScanModal();
    }));

    // Start button UI toggle
    startScanBtn.addEventListener("click", () => {
        startScanBtn.style.display = "none";
        stopScanBtn.style.display = "inline-block";

        const placeholder = document.getElementById("placeholder-canvas");
        if (placeholder) placeholder.style.display = "none";

        // Let qr_scanner.js handle the actual scanning
        if (window.startQrScanner) window.startQrScanner();
    });

    // Stop button UI toggle
    stopScanBtn.addEventListener("click", () => {
        stopScanBtn.style.display = "none";
        startScanBtn.style.display = "inline-block";
        
        // Let qr_scanner.js handle scanner cleanup
        if (window.stopQrScanner) window.stopQrScanner();
    });
}

/* ===  EXIT MODAL UI CONTROLS === */
const exitModal = document.getElementById("exit-vehicle-modal");
const exitSuccessModal = document.getElementById("exit-success-modal");

// Close exit modal function
window.closeExitModal = function() {
    if (exitModal) {
        exitModal.style.display = 'none';
        console.log('[Modal UI] Exit modal closed');
    }
};

// Close exit success modal function
window.closeExitSuccessModal = function() {
    if (exitSuccessModal) {
        exitSuccessModal.style.display = 'none';
        console.log('[Modal UI] Exit success modal closed');
    }
};

/* === VEHICLE INFO MODAL UI CONTROLS === */
// Close vehicle info modal function
window.closeVehicleInfoModal = function() {
    const vehicleInfoModal = document.getElementById('vehicleInfoModal');
    if (!vehicleInfoModal) return;

    console.log('[Modal UI] Closing vehicle info modal');

    if (typeof bootstrap !== 'undefined') {
        const modal = bootstrap.Modal.getInstance(vehicleInfoModal);
        if (modal) {
            modal.hide();
        } else {
            // Fallback if Bootstrap modal instance not found
            vehicleInfoModal.style.display = 'none';
            document.body.classList.remove('modal-open');
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) backdrop.remove();
        }
    } else {
        // Vanilla JS fallback
        vehicleInfoModal.classList.remove('show');
        vehicleInfoModal.style.display = 'none';
        vehicleInfoModal.setAttribute('aria-hidden', 'true');
        vehicleInfoModal.removeAttribute('aria-modal');
        document.body.classList.remove('modal-open');
        
        const backdrop = document.getElementById('vehicle-modal-backdrop');
        if (backdrop) backdrop.remove();
    }
};

/* === ASSIGNMENT MODAL UI CONTROLS === */
// Show assignment success modal
window.showAssignmentSuccessModal = function(data) {
    const modal = document.getElementById('assignment-success-modal');
    if (!modal) {
        console.error('[Modal UI] Assignment success modal not found');
        alert('✅ Vehicle successfully assigned to parking space!');
        location.reload();
        return;
    }

    console.log('[Modal UI] Showing assignment success modal');

    // Populate modal content
    const plateEl = document.getElementById('success-assigned-plate');
    const nameEl = document.getElementById('success-parking-name');
    const timeEl = document.getElementById('success-assigned-time');
    
    if (plateEl) plateEl.textContent = data.plate_number || 'N/A';
    if (nameEl) nameEl.textContent = data.parking_name || 'N/A';
    if (timeEl) timeEl.textContent = data.assigned_time || new Date().toLocaleString();

    // Show modal
    modal.style.display = 'flex';

    // Set up close handlers
    setupModalCloseHandlers(modal, true);
};

// Show assignment error modal
window.showAssignmentErrorModal = function(errorMessage) {
    const modal = document.getElementById('assignment-error-modal');
    if (!modal) {
        console.error('[Modal UI] Assignment error modal not found');
        alert('❌ ' + errorMessage);
        return;
    }

    console.log('[Modal UI] Showing assignment error modal');

    // Populate modal content
    const messageEl = document.getElementById('error-assignment-message');
    if (messageEl) messageEl.textContent = errorMessage;

    // Show modal
    modal.style.display = 'flex';

    // Set up close handlers
    setupModalCloseHandlers(modal, false);
};

/* === EXIT SUCCESS MODAL UI === */
window.showExitSuccessModal = function(data) {
    const modal = document.getElementById('exit-success-modal');
    if (!modal) {
        console.error('[Modal UI] Exit success modal not found');
        alert('✅ Vehicle exited successfully!');
        location.reload();
        return;
    }

    console.log('[Modal UI] Showing exit success modal');

    // Populate success modal fields
    const plateEl = document.getElementById('success-plate-number');
    const timeEl = document.getElementById('success-exit-time');
    const durationEl = document.getElementById('success-total-duration');

    if (plateEl) plateEl.textContent = data.exit_summary?.plate_number || 'N/A';
    if (timeEl) timeEl.textContent = data.exit_summary?.exit_time || new Date().toLocaleString();
    if (durationEl) durationEl.textContent = data.exit_summary?.total_duration || 'N/A';

    // Show modal
    modal.style.display = 'flex';

    // Set up close handlers
    setupModalCloseHandlers(modal, false);
};

/* === HELPER FUNCTIONS === */
function setupModalCloseHandlers(modal, autoRefresh = false) {
    const closeBtns = modal.querySelectorAll('.close-btn, .close-modal');
    
    const closeHandler = () => {
        modal.style.display = 'none';
        if (autoRefresh) {
            location.reload();
        }
    };

    // Remove old listeners by cloning buttons
    closeBtns.forEach(btn => {
        const newBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(newBtn, btn);
        newBtn.addEventListener('click', closeHandler);
    });

    // Close on background click
    modal.onclick = (e) => {
        if (e.target === modal) {
            closeHandler();
        }
    };

    // Auto-refresh for success modals
    if (autoRefresh) {
        setTimeout(() => {
            location.reload();
        }, 3000);
    }
}

console.log("[Modal UI] qr_modals.js initialized - UI controls only");