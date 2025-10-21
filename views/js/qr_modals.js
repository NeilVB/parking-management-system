// qr_modals.js - JavaScript for QR and Parking Modals

/* ===========================
    QR SCANNER MODAL
    =========================== */
const scanModal = document.getElementById("scan-vehicle-modal");
const startScanBtn = document.getElementById("start-scan-btn");
const stopScanBtn = document.getElementById("stop-scan-btn");
const closeScanBtns = scanModal ? scanModal.querySelectorAll(".close-btn") : [];

if (scanModal && startScanBtn && stopScanBtn) {
    // Open modal when needed (you can trigger this from a button elsewhere)
    window.openScanModal = function () {
        console.log("[QR] openScanModal called");
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

    // Close modal function
    window.closeScanModal = function () {
        console.log("[QR] closeScanModal called");
        scanModal.style.display = "none";
        stopScanBtn.style.display = "none";
        startScanBtn.style.display = "inline-block";
        if (window.stopQrScanner) window.stopQrScanner();
    };

    // Close modal
    closeScanBtns.forEach(btn => btn.addEventListener("click", () => {
        window.closeScanModal();
    }));

    // Start/Stop scanning buttons
    startScanBtn.addEventListener("click", () => {
        startScanBtn.style.display = "none";
        stopScanBtn.style.display = "inline-block";

        const placeholder = document.getElementById("placeholder-canvas");
        if (placeholder) placeholder.style.display = "none";

        if (window.startQrScanner) window.startQrScanner();
        
    });

    stopScanBtn.addEventListener("click", () => {
        stopScanBtn.style.display = "none";
        startScanBtn.style.display = "inline-block";
        if (window.stopQrScanner) window.stopQrScanner();
    });
}

/* ===========================
    SCAN BUTTON - attach once
    =========================== */
document.addEventListener('DOMContentLoaded', function() {
    console.log("[QR] DOM Content Loaded - Setting up scan button");
    
    const openScanBtn = document.getElementById("open-scan-modal-btn");
    const scanModalEl = document.getElementById("scan-vehicle-modal");

    console.log("[QR] openScanBtn:", openScanBtn);
    console.log("[QR] scanModalEl:", scanModalEl);

    if (openScanBtn) {
        openScanBtn.addEventListener("click", (e) => {
            e.preventDefault();
            console.log("[QR] open-scan-modal-btn clicked");

            if (typeof window.openScanModal === "function") {
                window.openScanModal();
                console.log("[QR] Modal opened via openScanModal()");
            } else if (scanModalEl) {
                scanModalEl.style.display = "flex";
                document.getElementById("scan-result").style.display = "none";
                document.getElementById("result-text").textContent = "Processing...";
                console.log("[QR] Modal opened (fallback)");
            }
        });
        console.log("[QR] Click listener attached successfully");
    } else {
        console.warn("[QR] open-scan-modal-btn not found in DOM.");
    }
});

/* ===========================
    VEHICLE INFO & PARKING SELECTION
    =========================== */
// Handle parking space selection - NO CONFIRMATION NEEDED
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('select-parking-btn') || 
        e.target.closest('.select-parking-btn')) {
        
        // STOP event from bubbling to other listeners
        e.stopImmediatePropagation();
        e.preventDefault();
        
        const btn = e.target.classList.contains('select-parking-btn') ? 
                    e.target : e.target.closest('.select-parking-btn');
        
        // Check if button is already being processed
        if (btn.hasAttribute('data-processing')) {
            console.log('Button already being processed, ignoring click');
            return;
        }
        
        // Mark as processing
        btn.setAttribute('data-processing', 'true');
        
        const parkingId = btn.getAttribute('data-id');
        const parkingName = btn.getAttribute('data-name');
        
        // Get vehicle information from the previous modal
        const vehiclePlate = document.getElementById('v-plate').textContent;
        
        // NO CONFIRMATION - assign immediately
        assignVehicleToParking(parkingId, vehiclePlate);
        
        // Remove processing flag after a delay
        setTimeout(() => {
            btn.removeAttribute('data-processing');
        }, 1000);
    }
});

// Initialize filters for the parking modal
function initializeModalFilters() {
    const searchInput = document.getElementById('modal-space-search');
    const typeFilter = document.getElementById('modal-type-filter');
    const vehicleFilter = document.getElementById('modal-vehicle-filter');
    const spacesCount = document.getElementById('modal-spaces-count');
    const cards = document.querySelectorAll('.modal-parking-card');

    function applyFilters() {
        const searchTerm = searchInput.value.toLowerCase();
        const typeValue = typeFilter.value;
        const vehicleValue = vehicleFilter.value;
        let visibleCount = 0;

        cards.forEach(card => {
            const name = card.getAttribute('data-name').toLowerCase();
            const userTypes = card.getAttribute('data-user-types');
            const vehicles = card.getAttribute('data-vehicles');

            let matchSearch = name.includes(searchTerm);
            let matchType = typeValue === 'all' || userTypes.includes(typeValue);
            let matchVehicle = vehicleValue === 'all' || vehicles.includes(vehicleValue);

            if (matchSearch && matchType && matchVehicle) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        spacesCount.textContent = `Showing ${visibleCount} of ${cards.length} spaces`;
    }

    // Add event listeners
    searchInput.addEventListener('input', applyFilters);
    typeFilter.addEventListener('change', applyFilters);
    vehicleFilter.addEventListener('change', applyFilters);

    // Initial count
    applyFilters();
}

// Assign vehicle to parking space
function assignVehicleToParking(parkingId, vehiclePlate) {
    // Show loading state
    const btn = document.querySelector(`.select-parking-btn[data-id="${parkingId}"]`);
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Assigning...';
    btn.disabled = true;

    // Make AJAX call to assign vehicle
    fetch('../controller/assign_parking.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            parking_space_id: parkingId,
            plate_number: vehiclePlate
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Vehicle assigned successfully!');
            
            // Close the parking selection modal
            const parkingModal = bootstrap.Modal.getInstance(document.getElementById('parkingSelectionModal'));
            if (parkingModal) {
                parkingModal.hide();
            }
            
            // Refresh the page to update parking space counts
            setTimeout(() => {
                location.reload();
            }, 500);
            
        } else {
            alert('Error: ' + (data.message || 'Failed to assign vehicle'));
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while assigning the vehicle');
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

// Function to display vehicle information in modal
window.displayVehicleInfo = function(plateNumber) {
    console.log("[QR] Fetching vehicle info for:", plateNumber);
    
    // Fetch vehicle information
    fetch(`../ajax/get_vehicle_info.php?plate_number=${encodeURIComponent(plateNumber)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const vehicle = data.vehicle;
                
                // Populate the vehicle info modal
                document.getElementById('v-plate').textContent = vehicle.plate_number;
                document.getElementById('v-owner').textContent = vehicle.owner;
                document.getElementById('v-type').textContent = vehicle.vehicle_type;
                
                // Display status with appropriate styling
                const statusBadge = vehicle.status === 'parked' ? 
                    '<span class="badge bg-success">Parked</span>' : 
                    '<span class="badge bg-warning">Outside</span>';
                document.getElementById('v-status').innerHTML = statusBadge;
                
                // Close QR scanner modal if using Bootstrap
                const scanModal = document.getElementById('scan-vehicle-modal');
                if (scanModal) {
                    scanModal.style.display = 'none';
                }
                
                // Open vehicle info modal
                const vehicleModal = new bootstrap.Modal(document.getElementById('vehicleInfoModal'));
                vehicleModal.show();
                
                console.log("[QR] Vehicle info displayed successfully");
            } else {
                alert('Error: ' + (data.message || 'Failed to fetch vehicle information'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while fetching vehicle information');
        });
}

// Assignment Modal Functions
function showAssignmentSuccessModal(plateNumber, parkingName) {
    const modal = document.getElementById('assignment-success-modal');
    if (!modal) {
        console.error('Assignment success modal not found');
        alert('✅ Vehicle successfully assigned to parking space!');
        location.reload();
        return;
    }

    console.log('Showing assignment success modal for:', plateNumber, parkingName);

    // Populate modal content
    const plateEl = document.getElementById('success-assigned-plate');
    const nameEl = document.getElementById('success-parking-name');
    const timeEl = document.getElementById('success-assigned-time');
    
    if (plateEl) plateEl.textContent = plateNumber;
    if (nameEl) nameEl.textContent = parkingName;
    if (timeEl) timeEl.textContent = new Date().toLocaleString();

    // Show modal
    modal.style.display = 'flex';

    // Set up close handlers
    const closeBtn = modal.querySelector('.close-btn');
    const okBtn = modal.querySelector('.close-modal');
    
    const closeModal = () => {
        modal.style.display = 'none';
        location.reload();
    };
    
    // Remove existing listeners and add new ones
    closeBtn.replaceWith(closeBtn.cloneNode(true));
    okBtn.replaceWith(okBtn.cloneNode(true));
    
    modal.querySelector('.close-btn').addEventListener('click', closeModal);
    modal.querySelector('.close-modal').addEventListener('click', closeModal);
    
    // Auto-refresh after 3 seconds
    setTimeout(() => {
        location.reload();
    }, 3000);
}

function showAssignmentErrorModal(errorMessage) {
    const modal = document.getElementById('assignment-error-modal');
    if (!modal) {
        console.error('Assignment error modal not found');
        alert('❌ ' + errorMessage);
        return;
    }

    console.log('Showing assignment error modal:', errorMessage);

    // Populate modal content
    const messageEl = document.getElementById('error-assignment-message');
    if (messageEl) messageEl.textContent = errorMessage;

    // Show modal
    modal.style.display = 'flex';

    // Set up close handlers
    const closeBtn = modal.querySelector('.close-btn');
    const cancelBtn = modal.querySelector('.close-modal');
    const retryBtn = document.getElementById('retry-assignment-btn');
    
    const closeModal = () => {
        modal.style.display = 'none';
    };
    
    // Remove existing listeners and add new ones
    closeBtn.replaceWith(closeBtn.cloneNode(true));
    cancelBtn.replaceWith(cancelBtn.cloneNode(true));
    
    modal.querySelector('.close-btn').addEventListener('click', closeModal);
    modal.querySelector('.close-modal').addEventListener('click', closeModal);
    
    // Retry button
    if (retryBtn) {
        retryBtn.replaceWith(retryBtn.cloneNode(true));
        modal.querySelector('#retry-assignment-btn').addEventListener('click', closeModal);
    }
}

function closeVehicleInfoModal() {
    const vehicleInfoModal = document.getElementById('vehicleInfoModal');
    if (!vehicleInfoModal) return;

    console.log('Closing vehicle info modal');

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
        vehicleInfoModal.style.display = 'none';
        document.body.classList.remove('modal-open');
        const backdrop = document.getElementById('vehicle-modal-backdrop');
        if (backdrop) backdrop.remove();
    }
}

// Function to close vehicle info modal
function closeVehicleInfoModal() {
    const vehicleInfoModal = document.getElementById('vehicleInfoModal');
    if (!vehicleInfoModal) return;

    if (typeof bootstrap !== 'undefined') {
        const modal = bootstrap.Modal.getInstance(vehicleInfoModal);
        modal?.hide();
    } else {
        vehicleInfoModal.classList.remove('show');
        vehicleInfoModal.style.display = 'none';
        document.body.classList.remove('modal-open');
        const backdrop = document.getElementById('vehicle-modal-backdrop');
        if (backdrop) backdrop.remove();
    }
}