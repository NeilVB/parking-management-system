/* ===========================
   warning_modal.js - Warning Modal Handler for Parked Vehicles
============================ */

let warningCallback = null;

/**
 * Show warning modal with parked vehicle information
 * @param {string} action - 'edit' or 'delete'
 * @param {number} spaceId - Parking space ID
 * @param {string} spaceName - Parking space name
 * @param {object} parkedData - Data about parked vehicles
 */
function showWarningModal(action, spaceId, spaceName, parkedData) {
    const modal = document.getElementById('warningModal');
    const content = document.getElementById('warningContent');
    const proceedBtn = document.getElementById('warningProceedBtn');

    if (!modal || !content || !proceedBtn) {
        console.error('Warning modal elements not found');
        return;
    }

    // Build vehicle list HTML
    let vehicleListHTML = '';
    if (parkedData.vehicles && parkedData.vehicles.length > 0) {
        vehicleListHTML = `
            <div class="vehicle-list">
                <strong>Currently Parked Vehicles:</strong>
                ${parkedData.vehicles.map(v => `
                    <div class="vehicle-item">
                        <strong>${v.plate_number}</strong> - ${v.color} ${v.make} ${v.model}<br>
                        <small style="color: #7f8c8d;">Type: ${v.vehicle_type} | Owner: ${v.owner}</small>
                    </div>
                `).join('')}
            </div>
        `;
    }

    // Set warning message based on action
    let warningMessage = '';
    let buttonText = '';
    let buttonColor = '';

    if (action === 'edit') {
        warningMessage = `
            <p><strong>Parking Space:</strong> ${spaceName}</p>
            <p style="color: #e67e22; font-weight: bold;">
                ⚠️ This parking space has ${parkedData.count} parked vehicle(s).
            </p>
            <p>Editing this space may affect the currently parked vehicles. Please proceed with caution.</p>
            ${vehicleListHTML}
        `;
        buttonText = 'Edit Anyway';
        buttonColor = '#f39c12';
    } else if (action === 'delete') {
        warningMessage = `
            <p><strong>Parking Space:</strong> ${spaceName}</p>
            <p style="color: #e74c3c; font-weight: bold;">
                🚨 This parking space has ${parkedData.count} parked vehicle(s).
            </p>
            <p>Deleting this space will affect the currently parked vehicles. They will need to be reassigned or exited.</p>
            ${vehicleListHTML}
            <p style="background: #ffebee; padding: 10px; border-radius: 5px; margin-top: 15px;">
                <strong>Recommendation:</strong> Wait for all vehicles to exit before deleting.
            </p>
        `;
        buttonText = 'Delete Anyway';
        buttonColor = '#e74c3c';
    }

    content.innerHTML = warningMessage;
    proceedBtn.textContent = buttonText;
    proceedBtn.style.background = buttonColor;

    modal.style.display = 'block';
}

/**
 * Close warning modal
 */
function closeWarningModal() {
    const modal = document.getElementById('warningModal');
    if (modal) {
        modal.style.display = 'none';
    }
    warningCallback = null;
}

/**
 * Set callback function to execute when user proceeds
 * @param {function} callback - Function to execute on proceed
 */
function setWarningCallback(callback) {
    warningCallback = callback;
    const proceedBtn = document.getElementById('warningProceedBtn');
    if (proceedBtn) {
        proceedBtn.onclick = () => {
            if (warningCallback) {
                warningCallback();
            }
            closeWarningModal();
        };
    }
}

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const modal = document.getElementById('warningModal');
    if (event.target === modal) {
        closeWarningModal();
    }
});