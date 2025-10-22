document.addEventListener("DOMContentLoaded", () => {
    /* ===========================
    views/js/modals.js
    ============================ */
    window.openFeedbackModal = function (title, message, type = "success", context = "register") {
        const modal = document.getElementById("feedback-modal");
        const modalContent = modal?.querySelector(".modal-content");
        const titleEl = document.getElementById("feedback-title");
        const messageEl = document.getElementById("feedback-message");
        const buttonEl = document.getElementById("feedback-btn");

        if (!modal || !modalContent || !titleEl || !messageEl || !buttonEl) {
            console.error("Feedback modal elements missing");
            return;
        }

        titleEl.textContent = title;
        messageEl.textContent = message;

        modalContent.classList.remove("success", "error");
        modalContent.classList.add(type);
        modal.style.display = "flex";

        if (type === "error") {
            buttonEl.textContent = "OK";
            buttonEl.onclick = () => {
                modal.style.display = "none";
                document.getElementById("Email")?.focus();
            };
        } else if (type === "success") {
            if (context === "register") {
                buttonEl.textContent = "Login Now";
                buttonEl.onclick = () => (window.location.href = "login.php");
            } else if (context === "login") {
                buttonEl.textContent = "Go to Dashboard";
                buttonEl.onclick = () => (window.location.href = "dashboard.php");
            } else {
                buttonEl.textContent = "Close";
                buttonEl.onclick = () => (modal.style.display = "none");
            }
        } else {
            buttonEl.textContent = "Close";
            buttonEl.onclick = () => (modal.style.display = "none");
        }
    };

    /* ===========================
       ADD SPACE MODAL
    ============================ */
    const addSpaceBtn = document.getElementById("add-space-btn");
    const addModal = document.getElementById("add-space-modal");
    const addCloseBtns = addModal ? addModal.querySelectorAll(".close-btn, .close-modal") : [];

    const addSupportsCar = document.getElementById("add-supports-car");
    const addSupportsMotorcycle = document.getElementById("add-supports-motorcycle");
    const addAllocationsContainer = document.getElementById("add-allocations-container");
    const addCarAllocations = document.getElementById("add-car-allocations");
    const addMotorcycleAllocations = document.getElementById("add-motorcycle-allocations");

    const addPreview = document.getElementById("add-space-preview");
    const addImageInput = document.getElementById("add-space-image");
    const addForm = document.getElementById("add-space-form");

    if (addSpaceBtn && addModal) {
        addSpaceBtn.addEventListener("click", () => {
            addModal.style.display = "flex";
            addForm.reset();

            addAllocationsContainer.style.display = "none";
            addCarAllocations.style.display = "none";
            addMotorcycleAllocations.style.display = "none";

            addPreview.style.display = "none";
            addPreview.src = "";

            // Reset allocation inputs to 0
            document.querySelectorAll('#add-car-allocations .allocation-input, #add-motorcycle-allocations .allocation-input')
                .forEach(input => input.value = 0);

            updateTotals("add");
        });

        addCloseBtns.forEach(btn => btn.addEventListener("click", () => {
            addModal.style.display = "none";
        }));

        // Image preview
        addImageInput?.addEventListener("change", e => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    addPreview.src = e.target.result;
                    addPreview.style.display = "block";
                };
                reader.readAsDataURL(file);
            }
        });

        // Toggle allocations
        const toggleAddAllocations = () => {
            const carChecked = addSupportsCar.checked;
            const motoChecked = addSupportsMotorcycle.checked;

            addAllocationsContainer.style.display = (carChecked || motoChecked) ? "block" : "none";
            addCarAllocations.style.display = carChecked ? "block" : "none";
            addMotorcycleAllocations.style.display = motoChecked ? "block" : "none";

            // Required validation for inputs
            document.querySelectorAll('#add-car-allocations .allocation-input').forEach(input => {
                input.required = carChecked;
                if (!carChecked) input.value = 0;
            });
            document.querySelectorAll('#add-motorcycle-allocations .allocation-input').forEach(input => {
                input.required = motoChecked;
                if (!motoChecked) input.value = 0;
            });

            updateTotals("add");
        };

        [addSupportsCar, addSupportsMotorcycle].forEach(cb => cb.addEventListener("change", toggleAddAllocations));

        // Update totals on input change
        document.querySelectorAll('#add-car-allocations .allocation-input, #add-motorcycle-allocations .allocation-input')
            .forEach(input => input.addEventListener("input", () => updateTotals("add")));
    }

    /* ===========================
       EDIT SPACE MODAL
    ============================ */
    const editModal = document.getElementById("edit-space-modal");
    const editCloseBtns = editModal ? editModal.querySelectorAll(".close-btn, .close-modal") : [];
    const editSupportsCar = document.getElementById("edit-supports-car");
    const editSupportsMotorcycle = document.getElementById("edit-supports-motorcycle");
    const editAllocationsContainer = document.getElementById("edit-allocations-container");
    const editCarAllocations = document.getElementById("edit-car-allocations");
    const editMotorcycleAllocations = document.getElementById("edit-motorcycle-allocations");

    const editPreview = document.getElementById("edit-space-preview");
    const editImageInput = document.getElementById("edit-space-image");

    const editButtons = document.querySelectorAll(".edit-space-btn");

    const deleteBtn = document.getElementById("delete-space-btn");

    if (editModal) {
        editCloseBtns.forEach(btn => btn.addEventListener("click", () => {
            editModal.style.display = "none";
        }));

        const toggleEditAllocations = () => {
            const carChecked = editSupportsCar.checked;
            const motoChecked = editSupportsMotorcycle.checked;

            editAllocationsContainer.style.display = (carChecked || motoChecked) ? "block" : "none";
            editCarAllocations.style.display = carChecked ? "block" : "none";
            editMotorcycleAllocations.style.display = motoChecked ? "block" : "none";

            // Required validation for inputs
            document.querySelectorAll('#edit-car-allocations .allocation-input').forEach(input => {
                input.required = carChecked;
                if (!carChecked) input.value = 0;
            });
            document.querySelectorAll('#edit-motorcycle-allocations .allocation-input').forEach(input => {
                input.required = motoChecked;
                if (!motoChecked) input.value = 0;
            });

            updateTotals("edit");
        };

        [editSupportsCar, editSupportsMotorcycle].forEach(cb => cb.addEventListener("change", toggleEditAllocations));

        // Image preview
        editImageInput.addEventListener("change", e => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    editPreview.src = e.target.result;
                    editPreview.style.display = "block";
                };
                reader.readAsDataURL(file);
            }
        });

        // Populate edit modal with parked vehicle warning
        editButtons.forEach(button => {
            button.addEventListener("click", async () => {
                const id = button.dataset.id;
                const name = button.dataset.name;

                // 🆕 Check for parked vehicles first
                try {
                    const checkResponse = await fetch(`../ajax/check_parking_space.php?space_id=${id}`);
                    const checkData = await checkResponse.json();

                    if (checkData.success && checkData.has_parked) {
                        // Show warning modal
                        showWarningModal('edit', id, name, checkData);
                        
                        // Set callback to proceed with edit
                        setWarningCallback(() => {
                            openEditModalContent(id, button);
                        });
                        return; // Stop here, wait for user decision
                    }
                } catch (error) {
                    console.error('Error checking parking space:', error);
                    // If check fails, proceed normally
                }

                // If no parked vehicles or check failed, open directly
                openEditModalContent(id, button);
            });
        });

        // 🆕 Extracted function to open edit modal content
        async function openEditModalContent(id, button) {
            editModal.style.display = "flex";

            document.getElementById("edit-space-id").value = id;
            document.getElementById("edit-space-name").value = button.dataset.name;

            editSupportsCar.checked = button.dataset.supportsCar === "1";
            editSupportsMotorcycle.checked = button.dataset.supportsMotorcycle === "1";

            const imagePath = button.dataset.image;
            document.getElementById("edit-current-image").value = imagePath || "";
            if (imagePath) {
                editPreview.src = imagePath;
                editPreview.style.display = "block";
            } else {
                editPreview.style.display = "none";
            }

            // Fetch allocations
            try {
                const response = await fetch(`../controller/get_allocations.php?parking_space_id=${id}`);
                const data = await response.json();

                // Reset inputs
                document.querySelectorAll('#edit-car-allocations .allocation-input, #edit-motorcycle-allocations .allocation-input')
                    .forEach(input => input.value = 0);

                if (data.success && data.allocations) {
                    const userTypeMap = {1:'general',2:'faculty',3:'executive',4:'guest'};
                    data.allocations.forEach(a => {
                        const type = a.vehicle_type_id === 1 ? 'car' : 'motorcycle';
                        const user = userTypeMap[a.user_type_id];
                        const input = document.getElementById(`edit-${type}-${user}`);
                        if (input) input.value = a.allocated_slots;
                    });
                }
            } catch (err) {
                console.error("Error fetching allocations:", err);
            }

            toggleEditAllocations();
        }
    }

    /* ===========================
    DELETE CONFIRMATION MODAL
    ============================ */
    const deleteModal = document.getElementById('delete-space-modal');
    const deleteSpaceIdInput = document.getElementById('delete-space-id');
    const deleteSpaceNameSpan = document.getElementById('delete-space-name');
    const confirmDeleteBtn = document.getElementById('confirm-delete-btn');

    // Open delete modal from edit modal
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function(e) {
            e.preventDefault(); // CRITICAL: Prevent form submission
            e.stopPropagation(); // Stop event bubbling
            
            const spaceId = document.getElementById('edit-space-id').value;
            const spaceName = document.getElementById('edit-space-name').value;
            
            console.log('🗑️ Delete button clicked!');
            console.log('Space ID:', spaceId);
            console.log('Space Name:', spaceName);
            console.log('Delete modal element:', deleteModal);
            
            // Close edit modal
            if (editModal) {
                editModal.style.display = 'none';
            }
            
            // Open delete confirmation modal
            if (deleteModal && deleteSpaceIdInput && deleteSpaceNameSpan) {
                deleteSpaceIdInput.value = spaceId;
                deleteSpaceNameSpan.textContent = spaceName;
                deleteModal.style.display = 'flex';
                console.log('✅ Delete modal should now be visible');
            } else {
                console.error('❌ Missing elements:', {
                    deleteModal: !!deleteModal,
                    deleteSpaceIdInput: !!deleteSpaceIdInput,
                    deleteSpaceNameSpan: !!deleteSpaceNameSpan
                });
            }
        });
    } else {
        console.error('❌ Delete button (#delete-space-btn) not found in DOM!');
    }

    // Confirm delete action
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', async function() {
            const spaceId = deleteSpaceIdInput.value;
            
            console.log('🔴 Yes Delete button clicked!', spaceId);
            
            if (!spaceId) {
                alert('Error: No parking space selected');
                return;
            }

            // Disable button during processing
            confirmDeleteBtn.disabled = true;
            confirmDeleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';

            try {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', spaceId);

                console.log('Sending delete request for ID:', spaceId);

                // Try parking_manager.php first (used by delete mode)
                let response = await fetch('../controller/parking_manager.php', {
                    method: 'POST',
                    body: formData
                });

                console.log('Response status:', response.status);

                const data = await response.json();
                console.log('Response data:', data);

                if (data.success) {
                    // Success message
                    alert('✅ Parking space deleted successfully!');
                    
                    // Close modal
                    deleteModal.style.display = 'none';
                    
                    // Remove the card from UI if it exists
                    const card = document.querySelector(`.parking-card[data-id="${spaceId}"]`);
                    if (card) {
                        card.remove();
                        console.log('Card removed from UI');
                    }
                    
                    // Refresh page after a short delay
                    setTimeout(() => location.reload(), 500);
                } else {
                    alert('❌ Error: ' + (data.message || 'Failed to delete parking space'));
                    confirmDeleteBtn.disabled = false;
                    confirmDeleteBtn.innerHTML = '<i class="fas fa-trash"></i> Yes, Delete';
                }
            } catch (error) {
                console.error('Delete error:', error);
                alert('❌ Error deleting parking space. Please try again.');
                confirmDeleteBtn.disabled = false;
                confirmDeleteBtn.innerHTML = '<i class="fas fa-trash"></i> Yes, Delete';
            }
        });
    } else {
        console.error('❌ Confirm delete button not found!');
    }

    // Close modal handlers
    if (deleteModal) {
        // Close button
        const closeBtn = deleteModal.querySelector('.close-btn');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                deleteModal.style.display = 'none';
                console.log('Delete modal closed via close button');
            });
        }

        // Cancel button
        const cancelBtn = deleteModal.querySelector('.close-modal');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                deleteModal.style.display = 'none';
                console.log('Delete modal closed via cancel button');
            });
        }

        // Click outside modal
        deleteModal.addEventListener('click', (e) => {
            if (e.target === deleteModal) {
                deleteModal.style.display = 'none';
                console.log('Delete modal closed by clicking outside');
            }
        });
    }

    /* ===========================
       Totals helper (shared)
    ============================ */
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
    

    
});