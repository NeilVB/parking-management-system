document.addEventListener("DOMContentLoaded", () => {
    /* ===========================
       FORMS
    ============================ */
    const endpoint = "../controller/parking_manager.php"; // Ensure this matches your actual path
    const addModal = document.getElementById("add-space-modal");
    const editModal = document.getElementById("edit-space-modal");

    /**
     * ADD FORM
     */
    const addForm = document.getElementById("add-space-form");
    if (addForm) {
        addForm.addEventListener("submit", async e => {
            e.preventDefault();

            const submitBtn = addForm.querySelector('button[type="submit"]');
            const originalText = submitBtn?.textContent || "";

            try {
                submitBtn.disabled = true;
                submitBtn.textContent = "Adding...";

                const formData = new FormData(addForm);
                formData.append("action", "add");

                const json = await fetchJson(endpoint, {
                    method: "POST",
                    body: formData
                });

                alert(json.message || "Parking space added successfully!");

                if (json.success) {
                    if (addModal) addModal.style.display = "none";
                    addForm.reset();

                    const preview = document.getElementById("add-space-preview");
                    if (preview) preview.src = "";

                    location.reload();
                }
            } catch (err) {
                console.error("Add error:", err);
                alert(`Error adding parking space: ${err.message}`);
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    }

    /**
     * EDIT FORM
     */
    const editForm = document.getElementById("edit-space-form");
    if (editForm) {
        editForm.addEventListener("submit", async e => {
            e.preventDefault();

            const submitBtn = editForm.querySelector('button[type="submit"]');
            const originalText = submitBtn?.textContent || "";

            try {
                submitBtn.disabled = true;
                submitBtn.textContent = "Updating...";

                const formData = new FormData(editForm);
                formData.append("action", "edit");

                const json = await fetchJson(endpoint, {
                    method: "POST",
                    body: formData
                });

                alert(json.message || "Parking space updated successfully!");

                if (json.success) {
                    if (editModal) editModal.style.display = "none";
                    location.reload();
                }
            } catch (err) {
                console.error("Edit error:", err);
                alert(`Error updating parking space: ${err.message}`);
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    }

    /**
     * DELETE BUTTON
     */
    const deleteSpaceBtn = document.getElementById("delete-space-btn");
    if (deleteSpaceBtn) {
        deleteSpaceBtn.addEventListener("click", async () => {
            const idField = document.getElementById("edit-space-id");
            const nameField = document.getElementById("edit-space-name");

            const id = idField?.value;
            const name = nameField?.value || "this parking space";

            if (!id) {
                alert("No parking space selected for deletion.");
                return;
            }

            if (!confirm(`Are you sure you want to delete "${name}"? This action cannot be undone.`)) {
                return;
            }

            const originalText = deleteSpaceBtn.textContent;

            try {
                deleteSpaceBtn.disabled = true;
                deleteSpaceBtn.textContent = "Deleting...";

                const formData = new FormData();
                formData.append("action", "delete");
                formData.append("id", id);

                const json = await fetchJson(endpoint, {
                    method: "POST",
                    body: formData
                });

                alert(json.message || "Parking space deleted successfully!");

                if (json.success) {
                    if (editModal) editModal.style.display = "none";
                    location.reload();
                }
            } catch (err) {
                console.error("Delete error:", err);
                alert(`Error deleting parking space: ${err.message}`);
            } finally {
                deleteSpaceBtn.disabled = false;
                deleteSpaceBtn.textContent = originalText;
            }
        });
    }
});
