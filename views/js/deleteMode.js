/* deleteMode.js - Delete Mode with Warning Integration */
let deleteModeActive = false;

document.addEventListener('DOMContentLoaded', () => {
    const deleteModeBtn = document.getElementById('delete-mode-btn');

    if (deleteModeBtn) {
        deleteModeBtn.addEventListener('click', () => {
            deleteModeActive = !deleteModeActive;
            deleteModeBtn.classList.toggle('active', deleteModeActive);

            if (deleteModeActive) {
                deleteModeBtn.textContent = "Selecting Parking to DELETE...";
                enableDeleteMode();
            } else {
                deleteModeBtn.textContent = "Delete Mode";
                disableDeleteMode();
            }
        });
    }
});

function enableDeleteMode() {
    document.body.classList.add('delete-mode');

    const cards = document.querySelectorAll('.parking-card');
    cards.forEach(card => {
        card.style.cursor = 'pointer';
        card.addEventListener('click', handleDeleteClick);
    });
}

function disableDeleteMode() {
    document.body.classList.remove('delete-mode');

    const cards = document.querySelectorAll('.parking-card');
    cards.forEach(card => {
        card.style.cursor = '';
        card.removeEventListener('click', handleDeleteClick);
    });
}

/* Handle delete click - checks for parked vehicles first */
async function handleDeleteClick(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const card = e.currentTarget;
    const parkingId = card.dataset.id;
    const parkingName = card.dataset.name;

    console.log('🗑️ Delete Mode - Card clicked:', parkingId, parkingName);

    if (!parkingId) {
        console.error('No parking ID found');
        return;
    }

    try {
        // Check if space has parked vehicles
        const response = await fetch(`../ajax/check_parking_space.php?space_id=${parkingId}`);
        const data = await response.json();

        if (data.success && data.has_parked) {
            // Show warning modal if there are parked vehicles
            showWarningModal('delete', parkingId, parkingName, data);
            
            // Set callback to proceed with delete confirmation
            setWarningCallback(() => {
                openDeleteConfirmationModal(parkingId, parkingName, card);
            });
        } else {
            // No parked vehicles, proceed directly to confirmation
            openDeleteConfirmationModal(parkingId, parkingName, card);
        }

    } catch (error) {
        console.error('Error checking parking space:', error);
        // If check fails, proceed with normal flow
        openDeleteConfirmationModal(parkingId, parkingName, card);
    }
}

/* Open delete confirmation modal */
function openDeleteConfirmationModal(parkingId, parkingName, cardElement) {
    const deleteModal = document.getElementById('delete-space-modal');
    const deleteSpaceIdInput = document.getElementById('delete-space-id');
    const deleteSpaceNameSpan = document.getElementById('delete-space-name');

    if (!deleteModal || !deleteSpaceIdInput || !deleteSpaceNameSpan) {
        console.error('Delete modal elements not found');
        // Fallback to confirm dialog
        if (confirm(`Are you sure you want to delete "${parkingName}"?`)) {
            performDelete(parkingId, cardElement);
        }
        return;
    }

    // Open the delete confirmation modal
    deleteSpaceIdInput.value = parkingId;
    deleteSpaceNameSpan.textContent = parkingName;
    
    // Store card element for later use
    deleteModal.dataset.cardElement = cardElement ? cardElement.dataset.id : '';
    
    deleteModal.style.display = 'flex';
    
    console.log('✅ Delete modal opened');
}

/* Perform the actual delete operation */
function performDelete(parkingId, cardElement) {
    const formData = new FormData();
    formData.append("action", "delete");
    formData.append("id", parkingId);

    fetch("../controller/parking_manager.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message || "Parking space deleted successfully.");
            if (cardElement) {
                cardElement.remove();
            } else {
                location.reload();
            }
        } else {
            alert(data.message || "Error deleting parking space.");
        }
    })
    .catch(err => {
        console.error("Error:", err);
        alert("Something went wrong while deleting.");
    });
}

/* Global function to handle final delete confirmation from the delete modal */
window.confirmDeleteFromModal = function() {
    const deleteModal = document.getElementById('delete-space-modal');
    const parkingId = document.getElementById('delete-space-id').value;
    const cardId = deleteModal.dataset.cardElement;
    
    // Find the card element
    const cardElement = cardId ? document.querySelector(`.parking-card[data-id="${cardId}"]`) : null;
    
    // Close modal
    deleteModal.style.display = 'none';
    
    // Perform delete
    performDelete(parkingId, cardElement);
}