document.addEventListener("DOMContentLoaded", () => {
    /* === views/js/forms.js (Updated) === */
    const endpoint = "../controller/parking_manager.php";
    const addForm = document.getElementById("add-space-form");
    const editForm = document.getElementById("edit-space-form");

    /* Collect allocation data from form inputs */
    function collectAllocations(prefix) {
        const allocations = [];
        const userTypeMap = { 'general': 1, 'faculty': 2, 'executive': 3, 'guest': 4 };
        const userTypes = ['general', 'faculty', 'executive', 'guest'];

        // Car allocations
        const carCheckbox = document.getElementById(`${prefix}-supports-car`);
        if (carCheckbox && carCheckbox.checked) {
            userTypes.forEach(type => {
                const input = document.getElementById(`${prefix}-car-${type}`);
                if (input) {
                    allocations.push({
                        vehicle_type_id: 1,
                        user_type_id: userTypeMap[type],
                        allocated_slots: parseInt(input.value) || 0
                    });
                }
            });
        }

        // Motorcycle allocations
        const motoCheckbox = document.getElementById(`${prefix}-supports-motorcycle`);
        if (motoCheckbox && motoCheckbox.checked) {
            userTypes.forEach(type => {
                const input = document.getElementById(`${prefix}-motorcycle-${type}`);
                if (input) {
                    allocations.push({
                        vehicle_type_id: 2,
                        user_type_id: userTypeMap[type],
                        allocated_slots: parseInt(input.value) || 0
                    });
                }
            });
        }

        return allocations;
    }

    /**
     * Validate that each selected vehicle type has at least one slot > 0
     */
    function validateAllocations(prefix) {
        const errors = [];
        const carCheckbox = document.getElementById(`${prefix}-supports-car`);
        const motoCheckbox = document.getElementById(`${prefix}-supports-motorcycle`);

        if (carCheckbox && carCheckbox.checked) {
            let carTotal = 0;
            document.querySelectorAll(`#${prefix}-car-allocations .allocation-input`).forEach(input => {
                carTotal += parseInt(input.value) || 0;
            });
            if (carTotal === 0) errors.push("Car");
        }

        if (motoCheckbox && motoCheckbox.checked) {
            let motoTotal = 0;
            document.querySelectorAll(`#${prefix}-motorcycle-allocations .allocation-input`).forEach(input => {
                motoTotal += parseInt(input.value) || 0;
            });
            if (motoTotal === 0) errors.push("Motorcycle");
        }

        if (errors.length > 0) {
            alert(`Please specify at least one slot for: ${errors.join(", ")}`);
            return false;
        }

        return true;
    }

    /**
     * Update totals dynamically
     */
    function updateTotals(prefix) {
        let carTotal = 0;
        document.querySelectorAll(`#${prefix}-car-allocations .allocation-input`).forEach(input => {
            carTotal += parseInt(input.value) || 0;
        });
        const carTotalEl = document.getElementById(`${prefix}-car-total`);
        if (carTotalEl) carTotalEl.textContent = carTotal;

        let motoTotal = 0;
        document.querySelectorAll(`#${prefix}-motorcycle-allocations .allocation-input`).forEach(input => {
            motoTotal += parseInt(input.value) || 0;
        });
        const motoTotalEl = document.getElementById(`${prefix}-motorcycle-total`);
        if (motoTotalEl) motoTotalEl.textContent = motoTotal;
    }

    /* Handle Add Form Submission */
    if (addForm) {
        addForm.addEventListener("submit", async e => {
            e.preventDefault();

            if (!validateAllocations("add")) return; // Stop if invalid

            const submitBtn = addForm.querySelector('button[type="submit"]');
            const originalText = submitBtn?.textContent || "";

            try {
                submitBtn.disabled = true;
                submitBtn.textContent = "Adding...";

                const formData = new FormData(addForm);
                formData.append("action", "add");
                formData.append("allocations", JSON.stringify(collectAllocations("add")));

                const response = await fetch(endpoint, { method: "POST", body: formData });
                const json = await response.json();

                alert(json.message || "Parking space added successfully!");

                if (json.success) {
                    addForm.reset();
                    const preview = document.getElementById("add-space-preview");
                    if (preview) {
                        preview.src = "";
                        preview.style.display = "none";
                    }
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

        // Update totals on input change
        document.querySelectorAll('#add-car-allocations .allocation-input, #add-motorcycle-allocations .allocation-input')
            .forEach(input => input.addEventListener('input', () => updateTotals('add')));
    }

    /* Handle Edit Form Submission */
    if (editForm) {
        editForm.addEventListener("submit", async e => {
            e.preventDefault();

            if (!validateAllocations("edit")) return; // Stop if invalid

            const submitBtn = editForm.querySelector('button[type="submit"]');
            const originalText = submitBtn?.textContent || "";

            try {
                submitBtn.disabled = true;
                submitBtn.textContent = "Updating...";

                const formData = new FormData(editForm);
                formData.append("action", "edit");
                formData.append("allocations", JSON.stringify(collectAllocations("edit")));

                const response = await fetch(endpoint, { method: "POST", body: formData });
                const json = await response.json();

                alert(json.message || "Parking space updated successfully!");

                if (json.success) {
                    editForm.reset();
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

        // Update totals on input change
        document.querySelectorAll('#edit-car-allocations .allocation-input, #edit-motorcycle-allocations .allocation-input')
            .forEach(input => input.addEventListener('input', () => updateTotals('edit')));
    }
});
