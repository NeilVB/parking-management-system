// exit_modal_handler.js - Handle Exit Confirmation and Success Modals

document.addEventListener('DOMContentLoaded', function() {
    console.log('[EXIT] Exit modal handler initialized');

    // Get modal elements
    const exitConfirmModal = document.getElementById('exit-vehicle-modal');
    const exitSuccessModal = document.getElementById('exit-success-modal');
    const confirmExitBtn = document.getElementById('confirm-exit-btn');

    // Exit Confirmation Modal - Close buttons
    if (exitConfirmModal) {
        const closeBtns = exitConfirmModal.querySelectorAll('.close-btn, .close-modal');
        closeBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                exitConfirmModal.style.display = 'none';
                console.log('[EXIT] Exit confirmation modal closed');
            });
        });

        // Close on background click
        exitConfirmModal.addEventListener('click', function(e) {
            if (e.target === exitConfirmModal) {
                exitConfirmModal.style.display = 'none';
                console.log('[EXIT] Exit confirmation modal closed (background)');
            }
        });
    }

    // Exit Success Modal - Close buttons
    if (exitSuccessModal) {
        const closeBtns = exitSuccessModal.querySelectorAll('.close-btn, .close-modal');
        closeBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                exitSuccessModal.style.display = 'none';
                console.log('[EXIT] Exit success modal closed');
                
                // Refresh page to update counts
                location.reload();
            });
        });

        // Close on background click
        exitSuccessModal.addEventListener('click', function(e) {
            if (e.target === exitSuccessModal) {
                exitSuccessModal.style.display = 'none';
                console.log('[EXIT] Exit success modal closed (background)');
                
                // Refresh page to update counts
                location.reload();
            }
        });
    }

    // Handle Confirm Exit button click
    if (confirmExitBtn) {
        confirmExitBtn.addEventListener('click', async function() {
            console.log('[EXIT] Confirm exit button clicked');

            // Disable button to prevent double clicks
            confirmExitBtn.disabled = true;
            const originalHTML = confirmExitBtn.innerHTML;
            confirmExitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

            try {
                // Get data from hidden fields
                const vehicleId = document.getElementById('exit-vehicle-id').value;
                const logId = document.getElementById('exit-parking-transaction-id').value;
                const parkingSpaceId = confirmExitBtn.getAttribute('data-parking-space-id') || '';

                console.log('[EXIT] Exit data:', { vehicleId, logId, parkingSpaceId });

                if (!vehicleId || !logId) {
                    throw new Error('Missing required exit data');
                }

                // Send exit confirmation to server
                const formData = new FormData();
                formData.append('action', 'confirm_exit');
                formData.append('log_id', logId);
                formData.append('vehicle_id', vehicleId);
                formData.append('parking_space_id', parkingSpaceId);

                const response = await fetch('../ajax/exit_vehicle.php', {
                    method: 'POST',
                    body: formData
                });

                const contentType = response.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    const responseText = await response.text();
                    console.error('[EXIT] Server returned non-JSON:', responseText);
                    throw new Error('Server did not return JSON');
                }

                const data = await response.json();
                console.log('[EXIT] Server response:', data);

                if (data.success && data.exit_summary) {
                    // Close confirmation modal
                    exitConfirmModal.style.display = 'none';

                    // Populate success modal
                    document.getElementById('success-plate-number').textContent = data.exit_summary.plate_number;
                    document.getElementById('success-exit-time').textContent = data.exit_summary.exit_time;
                    document.getElementById('success-total-duration').textContent = data.exit_summary.total_duration;

                    // Show success modal
                    exitSuccessModal.style.display = 'flex';

                    console.log('[EXIT] Exit completed successfully');
                } else {
                    // Show error message
                    alert('❌ Exit failed: ' + (data.message || 'Unknown error'));
                    
                    // Re-enable button
                    confirmExitBtn.disabled = false;
                    confirmExitBtn.innerHTML = originalHTML;
                }

            } catch (error) {
                console.error('[EXIT] Error during exit:', error);
                alert('❌ Error processing vehicle exit: ' + error.message);
                
                // Re-enable button
                confirmExitBtn.disabled = false;
                confirmExitBtn.innerHTML = originalHTML;
            }
        });
    }

    // Keyboard event - ESC to close modals
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (exitConfirmModal && exitConfirmModal.style.display === 'flex') {
                exitConfirmModal.style.display = 'none';
                console.log('[EXIT] Exit confirmation modal closed (ESC key)');
            }
            if (exitSuccessModal && exitSuccessModal.style.display === 'flex') {
                exitSuccessModal.style.display = 'none';
                console.log('[EXIT] Exit success modal closed (ESC key)');
                location.reload();
            }
        }
    });
});

// Global function to manually show exit confirmation modal (if needed)
window.showExitModal = function(exitInfo) {
    console.log('[EXIT] Showing exit modal manually:', exitInfo);
    
    // Populate modal fields
    document.getElementById('exit-vehicle-owner').textContent = exitInfo.driver_name || 'N/A';
    document.getElementById('exit-plate-number').textContent = exitInfo.plate_number || 'N/A';
    document.getElementById('exit-vehicle-type').textContent = exitInfo.vehicle_type || 'N/A';
    document.getElementById('exit-parking-space').textContent = exitInfo.parking_space || 'Not assigned';
    document.getElementById('exit-entry-time').textContent = exitInfo.entry_time || 'N/A';
    document.getElementById('exit-duration').textContent = exitInfo.duration || 'N/A';

    // Store IDs in hidden fields
    document.getElementById('exit-vehicle-id').value = exitInfo.vehicle_id || '';
    document.getElementById('exit-parking-transaction-id').value = exitInfo.log_id || '';

    // Store parking_space_id as data attribute
    const confirmBtn = document.getElementById('confirm-exit-btn');
    if (confirmBtn) {
        confirmBtn.setAttribute('data-parking-space-id', exitInfo.parking_space_id || '');
    }

    // Show modal
    const exitModal = document.getElementById('exit-vehicle-modal');
    if (exitModal) {
        exitModal.style.display = 'flex';
    }
};