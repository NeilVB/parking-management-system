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

function handleDeleteClick(e) {
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

    // Get the delete modal elements
    const deleteModal = document.getElementById('delete-space-modal');
    const deleteSpaceIdInput = document.getElementById('delete-space-id');
    const deleteSpaceNameSpan = document.getElementById('delete-space-name');

    if (!deleteModal || !deleteSpaceIdInput || !deleteSpaceNameSpan) {
        console.error('Delete modal elements not found');
        // Fallback to confirm dialog
        if (confirm(`Are you sure you want to delete "${parkingName}"?`)) {
            performDelete(parkingId, card);
        }
        return;
    }

    // Open the delete confirmation modal
    deleteSpaceIdInput.value = parkingId;
    deleteSpaceNameSpan.textContent = parkingName;
    deleteModal.style.display = 'flex';
    
    console.log('✅ Delete modal opened from delete mode');
}

// Separate function to perform the actual delete
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