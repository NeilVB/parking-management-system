document.addEventListener("DOMContentLoaded", () => {
    const resultElem = document.getElementById("result-text");
    const resultContainer = document.getElementById("scan-result");
    const modal = document.getElementById("scan-vehicle-modal");

    let qrScanner = null;
    let isScanning = false;
    let isProcessing = false;
    let videoElem = null;
    let currentVehicleData = null; // Store vehicle data for parking assignment

    function createVideoElement() {
        const oldVideo = document.getElementById("qr-video");
        if (oldVideo) oldVideo.remove();

        const newVideo = document.createElement("video");
        newVideo.id = "qr-video";
        newVideo.autoplay = true;
        newVideo.playsInline = true;
        newVideo.style.width = "100%";
        newVideo.style.height = "auto";
        document.getElementById("qr-video-container").appendChild(newVideo);
        return newVideo;
    }

    window.startQrScanner = function () {
        if (isScanning) {
            console.warn("[QR] Scanner already running.");
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
                        resultElem.textContent = "❌ No camera detected.";
                        resultContainer.style.display = "block";
                        isScanning = false;
                        return;
                    }

                    qrScanner = new QrScanner(
                        videoElem,
                        async result => {
                            if (isProcessing) {
                                console.log("[QR] Already processing, ignoring duplicate scan");
                                return;
                            }

                            isProcessing = true;
                            console.log("[QR] Detected:", result.data);

                            if (qrScanner) {
                                qrScanner.stop();
                            }

                            resultContainer.style.display = "block";
                            resultElem.textContent = `Scanned Code: ${result.data}`;

                            try {
                                const scannedUrl = result.data;
                                const tokenMatch = scannedUrl.match(/token=([a-zA-Z0-9]+)/);
                                const token = tokenMatch ? tokenMatch[1] : scannedUrl;

                                const formData = new FormData();
                                formData.append("action", "scan");
                                formData.append("qr_token", token);

                                const response = await fetch("../ajax/scan_vehicle.php", {
                                    method: "POST",
                                    body: formData
                                });

                                const contentType = response.headers.get("content-type");
                                if (!contentType || !contentType.includes("application/json")) {
                                    const responseText = await response.text();
                                    console.error("[QR] Server returned non-JSON:", responseText);
                                    throw new Error("Server did not return JSON");
                                }

                                const data = await response.json();
                                console.log("[QR] Response:", data);

                                // Scanning successful
                                if (data.success && data.vehicle) {
                                    console.log("[QR] Displaying vehicle info:", data.vehicle);
                                    
                                    // Store vehicle data globally for later use
                                    currentVehicleData = data.vehicle;
                                    
                                    // Fill vehicle info with CORRECT IDs
                                    document.getElementById("v-owner").textContent = data.vehicle.driver_name || "N/A";
                                    document.getElementById("v-plate").textContent = data.vehicle.plate_number || "N/A";
                                    document.getElementById("v-type").textContent = data.vehicle.vehicle_type || "N/A";
                                    document.getElementById("v-status").textContent = data.vehicle.status || "N/A";

                                    // Stop scanner and close QR modal
                                    window.stopQrScanner();
                                    if (modal) modal.style.display = "none";

                                    // Show vehicle info modal (vanilla JS)
                                    const vehicleModal = document.getElementById('vehicleInfoModal');
                                    if (vehicleModal) {
                                        vehicleModal.classList.add('show');
                                        vehicleModal.style.display = 'block';
                                        document.body.classList.add('modal-open');
                                        
                                        // Add backdrop
                                        const backdrop = document.createElement('div');
                                        backdrop.className = 'modal-backdrop fade show';
                                        backdrop.id = 'vehicle-modal-backdrop';
                                        document.body.appendChild(backdrop);
                                    }

                                    // Reset processing flag
                                    isProcessing = false;
                                }
                                else {
                                    resultElem.textContent = `❌ ${data.message}`;
                                    setTimeout(() => {
                                        window.stopQrScanner();
                                        if (modal) modal.style.display = "none";
                                    }, 2000);
                                }
                            } catch (err) {
                                console.error("[QR] AJAX error:", err);
                                resultElem.textContent = "❌ Server error. Check console for details.";

                                isProcessing = false;

                                setTimeout(() => {
                                    window.stopQrScanner();
                                }, 2000);
                            }
                        },
                        {
                            preferredCamera: "environment",
                            highlightScanRegion: true,
                            highlightCodeOutline: true,
                            maxScansPerSecond: 1
                        }
                    );

                    qrScanner.start().then(() => {
                        console.log("[QR] Scanner started.");
                        resultContainer.style.display = "none";
                    }).catch(err => {
                        console.error("[QR] Camera access denied:", err);
                        resultElem.textContent = "❌ Unable to access camera. Please allow permissions.";
                        resultContainer.style.display = "block";
                        isScanning = false;
                    });
                });
            })
            .catch(err => {
                console.error("[QR] Import failed:", err);
                isScanning = false;
            });
    };

    window.stopQrScanner = function () {
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
    };

    window.closeScanModal = function() {
        console.log("[QR] closeScanModal called");
        window.stopQrScanner();
        if (modal) {
            modal.style.display = "none";
        }
    };

    // Handle "Proceed to Parking" button
    const proceedBtn = document.getElementById("proceed-to-parking-btn");
    if (proceedBtn) {
        proceedBtn.addEventListener("click", async () => {
            console.log("[QR] Proceeding to parking assignment...");

            if (!currentVehicleData) {
                alert("❌ No vehicle data available.");
                return;
            }

            try {
                // Fetch available parking spaces
                const response = await fetch("../ajax/get_parking_spaces.php", {
                    method: "GET"
                });

                const responseText = await response.text();
                console.log("[QR] Raw response:", responseText);
                
                const data = JSON.parse(responseText);
                console.log("[QR] Available spaces:", data);

                if (data.success && data.spaces.length > 0) {
                    // Close vehicle info modal (vanilla JS)
                    const vehicleModal = document.getElementById('vehicleInfoModal');
                    if (vehicleModal) {
                        vehicleModal.classList.remove('show');
                        vehicleModal.style.display = 'none';
                        document.body.classList.remove('modal-open');
                        
                        const backdrop = document.getElementById('vehicle-modal-backdrop');
                        if (backdrop) backdrop.remove();
                    }

                    // Display parking spaces
                    displayParkingSpaces(data.spaces, currentVehicleData);

                } else {
                    alert("⚠️ No available parking spaces.");
                }
            } catch (err) {
                console.error("[QR] Failed to fetch parking spaces:", err);
                alert("❌ Could not load parking spaces.");
            }
        });
    }

    function displayParkingSpaces(spaces, vehicleData) {
        // Create a simple modal/section to show parking spaces
        // This is a basic implementation - adjust based on your UI needs
        const parkingHtml = `
            <div class="modal fade" id="parkingSpacesModal" tabindex="-1">
                <div class="modal-dialog modal-lg" style="display: flex; justify-content: center; align-items: center;">
                    <div class="modal-content" style="display: flex; justify-content: center; align-items: center;">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title"><i class="fas fa-parking"></i> Select Parking Space</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <h6>Vehicle: ${vehicleData.plate_number} (${vehicleData.vehicle_type})</h6>
                            <hr>
                            <div class="row g-3">
                                ${spaces.map(space => `
                                    <div class="col-md-4">
                                        <div class="card parking-space-card" style="cursor: pointer;" data-space-id="${space.id}">
                                            <div class="card-body text-center">
                                                <h5 class="card-title">${space.name}</h5>
                                                <p class="card-text">
                                                    <i class="fas fa-car"></i> ${space.vehicle_type}<br>
                                                    <span class="badge bg-success">Available</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing parking modal if any
        const existingModal = document.getElementById('parkingSpacesModal');
        if (existingModal) existingModal.remove();

        // Append new modal
        document.body.insertAdjacentHTML('beforeend', parkingHtml);

        // Show modal (vanilla JS)
        const parkingModalElem = document.getElementById('parkingSpacesModal');
        parkingModalElem.classList.add('show');
        parkingModalElem.style.display = 'block';
        document.body.classList.add('modal-open');
        
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.id = 'parking-modal-backdrop';
        document.body.appendChild(backdrop);

        // Add click handlers for parking spaces
        document.querySelectorAll('.parking-space-card').forEach(card => {
            card.addEventListener('click', async () => {
                const spaceId = card.getAttribute('data-space-id');
                console.log("[QR] Assigning vehicle to space:", spaceId);

                const formData = new FormData();
                formData.append("action", "assign");
                formData.append("vehicle_id", vehicleData.vehicle_id);
                formData.append("space_id", spaceId);

                try {
                    const assignResponse = await fetch("../ajax/assign_parking.php", {
                        method: "POST",
                        body: formData
                    });

                    const assignData = await assignResponse.json();
                    console.log("[QR] Assign result:", assignData);

                    if (assignData.success) {
                        alert("✅ Vehicle successfully parked!");
                        
                        // Close parking modal (vanilla JS)
                        parkingModalElem.classList.remove('show');
                        parkingModalElem.style.display = 'none';
                        document.body.classList.remove('modal-open');
                        
                        const backdrop = document.getElementById('parking-modal-backdrop');
                        if (backdrop) backdrop.remove();
                        
                        parkingModalElem.remove();
                        window.location.reload();
                    } else {
                        alert("❌ " + assignData.message);
                    }
                } catch (err) {
                    console.error("[QR] Assignment error:", err);
                    alert("❌ Failed to assign parking space.");
                }
            });
        });
    }

    // Cleanup on page visibility change
    document.addEventListener("visibilitychange", () => {
        if (document.hidden && isScanning) {
            console.log("[QR] Tab hidden -> stop scanner");
            window.stopQrScanner();
        }
    });

    window.addEventListener("beforeunload", () => {
        console.log("[QR] Unloading -> stop scanner");
        window.stopQrScanner();
    });

    // Modal close handlers
    if (modal) {
        const closeBtn = modal.querySelector(".close-btn");
        if (closeBtn) {
            closeBtn.addEventListener("click", () => {
                console.log("[QR] Modal closed -> stop scanner");
                window.closeScanModal();
            });
        }
        modal.addEventListener("click", e => {
            if (e.target === modal) {
                console.log("[QR] Modal background clicked -> stop scanner");
                window.closeScanModal();
            }
        });
    }
});