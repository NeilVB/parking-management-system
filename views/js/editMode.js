document.addEventListener("DOMContentLoaded", () => {
    /* === editMode.js === */

    const editModeBtn = document.getElementById("edit-mode-btn");
    const editButtons = document.querySelectorAll(".edit-btn");
    const parkingCards = document.querySelectorAll(".parking-card:not(.add-card)");

    // Initialize global editMode state
    window.editMode = false;

    /* Toggle edit mode UI and button states */
    function toggleEditMode() {
        window.editMode = !window.editMode;

        if (window.editMode) {
            editModeBtn.innerHTML = '<i class="fas fa-eye"></i> View Mode';
            editModeBtn.classList.remove("btn-primary");
            editModeBtn.classList.add("btn-warning");

            editButtons.forEach(btn => btn.style.display = "block");
            parkingCards.forEach(card => card.classList.add("edit-mode"));
        } else {
            editModeBtn.innerHTML = '<i class="fas fa-edit"></i> Edit Mode';
            editModeBtn.classList.remove("btn-warning");
            editModeBtn.classList.add("btn-primary");

            editButtons.forEach(btn => btn.style.display = "none");
            parkingCards.forEach(card => card.classList.remove("edit-mode"));
        }

        // Update modal delete button visibility if modal is open
        if (typeof window.updateModalDeleteButton === "function") {
            window.updateModalDeleteButton();
        }
    }

    // Bind toggle button
    if (editModeBtn) {
        editModeBtn.addEventListener("click", toggleEditMode);
    }

    // Hide edit buttons on initial load
    editButtons.forEach(btn => btn.style.display = "none");
});