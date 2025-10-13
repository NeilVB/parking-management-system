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
    const cards = document.querySelectorAll('.parking-card');
    cards.forEach(card => {
        card.style.cursor = 'pointer';
        card.addEventListener('click', handleDeleteClick);
    });
}

function disableDeleteMode() {
    const cards = document.querySelectorAll('.parking-card');
    cards.forEach(card => {
        card.style.cursor = 'default';
        card.removeEventListener('click', handleDeleteClick);
    });
}

function handleDeleteClick(e) {
    const card = e.currentTarget;
    const parkingId = card.dataset.id;
    const parkingName = card.dataset.name;

    if (!parkingId) return;

    if (confirm(`Are you sure you want to delete "${parkingName}"?`)) {
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
                card.remove();
            } else {
                alert(data.message || "Error deleting parking space.");
            }
        })
        .catch(err => {
            console.error("Error:", err);
            alert("Something went wrong while deleting.");
        });
    }
}
