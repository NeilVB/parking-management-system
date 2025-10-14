document.addEventListener("DOMContentLoaded", () => {
    const resultElem = document.getElementById("result-text");
    const resultContainer = document.getElementById("scan-result");
    const modal = document.getElementById("scan-vehicle-modal");

    let qrScanner = null;
    let isScanning = false;
    let isProcessing = false; // ✅ ADD: Prevent duplicate scans
    let videoElem = null;

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
        isProcessing = false; // ✅ Reset processing flag
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
                            // ✅ FIX: Prevent duplicate processing
                            if (isProcessing) {
                                console.log("[QR] Already processing, ignoring duplicate scan");
                                return;
                            }

                            isProcessing = true;
                            console.log("[QR] Detected:", result.data);
                            
                            // ✅ OPTIONAL: Pause scanner during processing
                            if (qrScanner) {
                                qrScanner.stop();
                            }

                            resultContainer.style.display = "block";
                            resultElem.textContent = `Scanned Code: ${result.data}`;

                            try {
                                // ✅ Extract token from full URL
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
                                    // ✅ IMPROVED: Log response for debugging
                                    const responseText = await response.text();
                                    console.error("[QR] Server returned non-JSON:", responseText);
                                    throw new Error("Server did not return JSON");
                                }

                                const data = await response.json();
                                resultElem.textContent = data.success
                                    ? `✅ ${data.message}`
                                    : `❌ ${data.message}`;

                                setTimeout(() => {
                                    window.stopQrScanner();
                                    // ✅ Close modal if needed
                                    if (modal) {
                                        modal.style.display = "none";
                                    }
                                }, 2000);
                            } catch (err) {
                                console.error("[QR] AJAX error:", err);
                                resultElem.textContent = "❌ Server error. Check console for details.";
                                
                                // ✅ Reset processing flag on error
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
                            maxScansPerSecond: 1 // ✅ ADD: Limit scan rate
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
        isProcessing = false; // ✅ Reset processing flag

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

    // ✅ ADD: Global function for modal close button
    window.closeScanModal = function() {
        console.log("[QR] closeScanModal called");
        window.stopQrScanner();
        if (modal) {
            modal.style.display = "none";
        }
    };

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