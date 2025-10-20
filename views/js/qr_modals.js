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
// Handle "Proceed to Parking" button click
document.addEventListener('DOMContentLoaded', function() {
    const proceedBtn = document.getElementById('proceed-to-parking-btn');
    
    if (proceedBtn) {
        proceedBtn.addEventListener('click', function() {
            // Close the vehicle info modal
            const vehicleModal = bootstrap.Modal.getInstance(document.getElementById('vehicleInfoModal'));
            if (vehicleModal) {
                vehicleModal.hide();
            }
            
            // Open the parking selection modal
            const parkingModal = new bootstrap.Modal(document.getElementById('parkingSelectionModal'));
            parkingModal.show();
            
            // Initialize filters and search for the parking modal
            initializeModalFilters();
        });
    }
    
    // Handle parking space selection
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('select-parking-btn') || 
            e.target.closest('.select-parking-btn')) {
            
            const btn = e.target.classList.contains('select-parking-btn') ? 
                        e.target : e.target.closest('.select-parking-btn');
            
            const parkingId = btn.getAttribute('data-id');
            const parkingName = btn.getAttribute('data-name');
            
            // Get vehicle information from the previous modal
            const vehiclePlate = document.getElementById('v-plate').textContent;
            
            // Confirm selection
            if (confirm(`Assign vehicle ${vehiclePlate} to ${parkingName}?`)) {
                assignVehicleToParking(parkingId, vehiclePlate);
            }
        }
    });
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