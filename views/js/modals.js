document.addEventListener("DOMContentLoaded", () => {
    /* ===========================
       MODALS
    ============================ */

    const addModal = document.getElementById("add-space-modal");
    const editModal = document.getElementById("edit-space-modal");
    const addBtn = document.getElementById("add-space-btn");
    const quickAddCard = document.getElementById("quick-add-card");
    const deleteSpaceBtn = document.getElementById("delete-space-btn");

    /**
     * Show or hide the delete button in the edit modal
     * based on global editMode state.
     */
    function updateModalDeleteButton() {
        if (!deleteSpaceBtn) return;

        if (window.editMode) {
            deleteSpaceBtn.style.display = "inline-block";
        } else {
            deleteSpaceBtn.style.display = "none";
        }
    }

    /**
     * Open the add modal from button or quick-add card
     */
    if (addBtn) {
        addBtn.addEventListener("click", () => {
            if (addModal) addModal.style.display = "flex";
        });
    }

    if (quickAddCard) {
        quickAddCard.addEventListener("click", () => {
            if (addModal) addModal.style.display = "flex";
        });
    }

    /**
     * Bind all edit buttons to open the edit modal
     * and pre-fill form fields with dataset values
     */
    function bindEditButtons() {
        document.querySelectorAll(".edit-btn").forEach(btn => {
            btn.addEventListener("click", e => {
                e.preventDefault();
                e.stopPropagation();

                const {
                    id = "",
                    name = "",
                    max = "",
                    type = "",
                    vehicle = "",
                    image = ""
                } = btn.dataset;

                // Fill form fields
                const editIdField = document.getElementById("edit-space-id");
                const editNameField = document.getElementById("edit-space-name");
                const editMaxField = document.getElementById("edit-space-max");
                const editTypeField = document.getElementById("edit-space-type");
                const editVehicleField = document.getElementById("edit-space-vehicle");

                if (editIdField) editIdField.value = id;
                if (editNameField) editNameField.value = name;
                if (editMaxField) editMaxField.value = max;
                if (editTypeField) editTypeField.value = type;
                if (editVehicleField) editVehicleField.value = vehicle;

                // Preview image
                const imgPreview = document.getElementById("edit-space-preview");
                if (imgPreview) {
                    imgPreview.src = image || "../assets/img/default_parking.jpg";
                }

                updateModalDeleteButton();
                if (editModal) editModal.style.display = "flex";
            });
        });
    }

    // Initial binding of edit buttons
    bindEditButtons();

    /**
     * Close modals via close buttons
     */
    document.querySelectorAll(".close-btn, .close-modal").forEach(el => {
        el.addEventListener("click", () => {
            document.querySelectorAll(".modal").forEach(m => {
                m.style.display = "none";
            });
        });
    });

    /**
     * Close modal when clicking outside of it
     */
    window.addEventListener("click", e => {
        if (e.target.classList.contains("modal")) {
            e.target.style.display = "none";
        }
    });

    // Expose delete button updater globally for editMode.js
    window.updateModalDeleteButton = updateModalDeleteButton;
});
