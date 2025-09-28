// views/admin_dashboard.js
document.addEventListener("DOMContentLoaded", () => {
    /* ===========================
       NAVIGATION (sidebar tabs)
    ============================ */
    const navLinks = document.querySelectorAll("[data-target]");
    const sections = document.querySelectorAll(".content-section");

    function showSection(id) {
        sections.forEach(s => s.classList.remove("active"));
        document.querySelectorAll(".nav-item").forEach(i => i.classList.remove("active"));

        const section = document.getElementById(id);
        if (section) section.classList.add("active");

        const navItem = document.querySelector(`.nav-item a[data-target="${id}"]`);
        if (navItem) navItem.parentElement.classList.add("active");
    }

    navLinks.forEach(link => {
        link.addEventListener("click", e => {
            e.preventDefault();
            showSection(link.getAttribute("data-target"));
        });
    });

    // Default open dashboard
    showSection("dashboard");

    /* ===========================
       MODALS
    ============================ */
    const addModal = document.getElementById("add-space-modal");
    const editModal = document.getElementById("edit-space-modal");
    const addBtn = document.getElementById("add-space-btn");
    const quickAddCard = document.getElementById("quick-add-card");

    // Show/hide delete button in modal based on edit mode
    function updateModalDeleteButton() {
        if (deleteSpaceBtn) {
            if (editMode) {
                deleteSpaceBtn.style.display = "inline-block";
            } else {
                deleteSpaceBtn.style.display = "none";
            }
        }
    }

    // Open add modal from button or quick add card
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

    // Open edit modal with pre-filled data
    function bindEditButtons() {
        document.querySelectorAll(".edit-btn").forEach(btn => {
            btn.addEventListener("click", (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                const id = btn.dataset.id || "";
                const name = btn.dataset.name || "";
                const max = btn.dataset.max || "";
                const type = btn.dataset.type || "";
                const vehicle = btn.dataset.vehicle || "";
                const image = btn.dataset.image || "";

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

                // Preview image if exists
                const imgPreview = document.getElementById("edit-space-preview");
                if (imgPreview) {
                    imgPreview.src = image || "../assets/img/default_parking.jpg";
                }

                // Update delete button visibility based on edit mode
                updateModalDeleteButton();

                if (editModal) editModal.style.display = "flex";
            });
        });
    }

    // Initial bind of edit buttons
    bindEditButtons();

    // Close modals
    document.querySelectorAll(".close-btn, .close-modal").forEach(el => {
        el.addEventListener("click", () => {
            document.querySelectorAll(".modal").forEach(m => {
                m.style.display = "none";
            });
        });
    });

    // Close modal when clicking outside
    window.addEventListener("click", e => {
        if (e.target.classList.contains("modal")) {
            e.target.style.display = "none";
        }
    });

    /* ===========================
       HELPERS
    ============================ */
    async function fetchJson(url, options) {
        try {
            const res = await fetch(url, options);
            const text = await res.text();
            
            if (!res.ok) {
                throw new Error(`HTTP ${res.status}: ${text}`);
            }
            
            try {
                return JSON.parse(text);
            } catch (parseErr) {
                console.error("❌ Server returned non-JSON:", text);
                throw new Error("Invalid server response. Check console for details.");
            }
        } catch (fetchErr) {
            console.error("❌ Fetch error:", fetchErr);
            throw fetchErr;
        }
    }

    const endpoint = "/parking-management-system/controller/parking_manager.php";

    /* ===========================
       FORMS
    ============================ */
    // ADD FORM
    const addForm = document.getElementById("add-space-form");
    if (addForm) {
        addForm.addEventListener("submit", async e => {
            e.preventDefault();
            
            const submitBtn = addForm.querySelector('button[type="submit"]');
            const originalText = submitBtn ? submitBtn.textContent : "";
            
            try {
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = "Adding...";
                }

                const formData = new FormData(addForm);
                formData.append("action", "add");

                const json = await fetchJson(endpoint, { 
                    method: "POST", 
                    body: formData 
                });
                
                alert(json.message || "Parking space added successfully!");
                
                if (json.success) {
                    // Close modal
                    if (addModal) addModal.style.display = "none";
                    
                    // Reset form
                    addForm.reset();
                    
                    // Clear image preview
                    const preview = document.getElementById("add-space-preview");
                    if (preview) preview.src = "";
                    
                    // Reload page to show new space
                    location.reload();
                }
            } catch (err) {
                console.error("Add error:", err);
                alert(`Error adding parking space: ${err.message}`);
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            }
        });
    }

    // EDIT FORM
    const editForm = document.getElementById("edit-space-form");
    if (editForm) {
        editForm.addEventListener("submit", async e => {
            e.preventDefault();
            
            const submitBtn = editForm.querySelector('button[type="submit"]');
            const originalText = submitBtn ? submitBtn.textContent : "";
            
            try {
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = "Updating...";
                }

                const formData = new FormData(editForm);
                formData.append("action", "edit");

                const json = await fetchJson(endpoint, { 
                    method: "POST", 
                    body: formData 
                });
                
                alert(json.message || "Parking space updated successfully!");
                
                if (json.success) {
                    // Close modal
                    if (editModal) editModal.style.display = "none";
                    
                    // Reload page to show updates
                    location.reload();
                }
            } catch (err) {
                console.error("Edit error:", err);
                alert(`Error updating parking space: ${err.message}`);
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            }
        });
    }

    // DELETE BUTTON - Using single declaration at the top
    const deleteSpaceBtn = document.getElementById("delete-space-btn");
    if (deleteSpaceBtn) {
        deleteSpaceBtn.addEventListener("click", async () => {
            const idField = document.getElementById("edit-space-id");
            const nameField = document.getElementById("edit-space-name");
            
            if (!idField) {
                alert("Cannot find parking space ID.");
                return;
            }
            
            const id = idField.value;
            const name = nameField ? nameField.value : "this parking space";
            
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
                    // Close modal
                    if (editModal) editModal.style.display = "none";
                    
                    // Reload page to reflect deletion
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

    /* ===========================
       IMAGE PREVIEWS
    ============================ */
    function bindImagePreview(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);

        if (input && preview) {
            input.addEventListener("change", () => {
                const file = input.files[0];
                if (file) {
                    // Validate file type
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    if (!allowedTypes.includes(file.type)) {
                        alert("Please select a valid image file (JPEG, PNG, GIF, or WebP).");
                        input.value = "";
                        return;
                    }
                    
                    // Validate file size (5MB max)
                    if (file.size > 5 * 1024 * 1024) {
                        alert("File size must be less than 5MB.");
                        input.value = "";
                        return;
                    }
                    
                    const reader = new FileReader();
                    reader.onload = e => {
                        preview.src = e.target.result;
                        preview.style.display = "block";
                    };
                    reader.onerror = () => {
                        alert("Error reading file.");
                        input.value = "";
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.src = "";
                    preview.style.display = "none";
                }
            });
        }
    }

    // Bind image preview handlers
    bindImagePreview("new-space-image", "add-space-preview");
    bindImagePreview("edit-space-image", "edit-space-preview");

    /* ===========================
       DYNAMIC CONTENT REFRESH
    ============================ */
    // Function to refresh parking spaces without full page reload
    async function refreshParkingSpaces() {
        try {
            const json = await fetchJson(`${endpoint}?action=list`);
            if (json.success && json.spaces) {
                // This would require updating the PHP to render just the parking cards
                // For now, we'll use location.reload() in the form handlers
                console.log("Parking spaces data:", json.spaces);
            }
        } catch (err) {
            console.error("Error refreshing parking spaces:", err);
        }
    }

    /* ===========================
       EDIT MODE TOGGLE
    ============================ */
    const editModeBtn = document.getElementById("edit-mode-btn");
    let editMode = false;
    
    if (editModeBtn) {
        editModeBtn.addEventListener("click", () => {
            editMode = !editMode;
            const parkingCards = document.querySelectorAll(".parking-card:not(.add-card)");
            const editButtons = document.querySelectorAll(".edit-btn");
            
            if (editMode) {
                editModeBtn.innerHTML = '<i class="fas fa-eye"></i> View Mode';
                editModeBtn.classList.remove("btn-primary");
                editModeBtn.classList.add("btn-warning");
                
                // Show edit buttons
                editButtons.forEach(btn => {
                    btn.style.display = "block";
                });
                
                parkingCards.forEach(card => {
                    card.classList.add("edit-mode");
                });
            } else {
                editModeBtn.innerHTML = '<i class="fas fa-edit"></i> Edit Mode';
                editModeBtn.classList.remove("btn-warning");
                editModeBtn.classList.add("btn-primary");
                
                // Hide edit buttons
                editButtons.forEach(btn => {
                    btn.style.display = "none";
                });
                
                parkingCards.forEach(card => {
                    card.classList.remove("edit-mode");
                });
            }
            
            // Update modal delete button visibility if modal is open
            updateModalDeleteButton();
        });
        
        // Initialize by hiding edit buttons on page load
        const editButtons = document.querySelectorAll(".edit-btn");
        editButtons.forEach(btn => {
            btn.style.display = "none";
        });
    }

    /* ===========================
       SEARCH AND FILTER
    ============================ */
    const searchInput = document.getElementById("space-search");
    const typeFilter = document.getElementById("type-filter");
    const vehicleFilter = document.getElementById("vehicle-filter");
    let currentSearchTerm = "";
    let currentTypeFilter = "all";
    let currentVehicleFilter = "all";
    
    function filterParkingSpaces() {
        const parkingCards = document.querySelectorAll(".parking-card:not(.add-card)");
        let visibleCount = 0;
        
        parkingCards.forEach(card => {
            const name = card.querySelector("h3")?.textContent.toLowerCase() || "";
            const paragraphs = card.querySelectorAll("p");
            let cardType = "";
            let cardVehicle = "";
            
            // Look for the type and vehicle in the paragraphs
            paragraphs.forEach(p => {
                const text = p.textContent.toLowerCase();
                if (text.includes("type:")) {
                    const typeMatch = text.match(/type:\s*(\w+)/);
                    if (typeMatch) {
                        cardType = typeMatch[1].toLowerCase();
                    }
                }
                if (text.includes("vehicle:")) {
                    const vehicleMatch = text.match(/vehicle:.*?(\w+)$/);
                    if (vehicleMatch) {
                        cardVehicle = vehicleMatch[1].toLowerCase();
                    }
                }
            });
            
            // Debug logging
            console.log(`Card: ${name}, Type: ${cardType}, Vehicle: ${cardVehicle}, Search: ${currentSearchTerm}, TypeFilter: ${currentTypeFilter}, VehicleFilter: ${currentVehicleFilter}`);
            
            // Check search term match
            const matchesSearch = currentSearchTerm === "" || 
                                name.includes(currentSearchTerm) || 
                                cardType.includes(currentSearchTerm) ||
                                cardVehicle.includes(currentSearchTerm);
            
            // Check type filter match
            const matchesType = currentTypeFilter === "all" || cardType === currentTypeFilter;
            
            // Check vehicle filter match
            const matchesVehicle = currentVehicleFilter === "all" || cardVehicle === currentVehicleFilter;
            
            // Show card only if it matches all filters
            if (matchesSearch && matchesType && matchesVehicle) {
                card.style.display = "block";
                visibleCount++;
            } else {
                card.style.display = "none";
            }
        });
        
        // Update visible count
        updateVisibleCount(visibleCount, parkingCards.length);
    }
    
    function updateVisibleCount(visible = null, total = null) {
        if (visible === null || total === null) {
            const visibleCards = document.querySelectorAll(".parking-card:not(.add-card):not([style*='display: none'])");
            const totalCards = document.querySelectorAll(".parking-card:not(.add-card)");
            visible = visibleCards.length;
            total = totalCards.length;
        }
        
        const countElement = document.getElementById("spaces-count");
        if (countElement) {
            countElement.textContent = `${visible} of ${total} spaces`;
        }
    }
    
    // Search input handler
    if (searchInput) {
        searchInput.addEventListener("input", (e) => {
            currentSearchTerm = e.target.value.toLowerCase();
            console.log("Search term changed:", currentSearchTerm);
            filterParkingSpaces();
        });
    }
    
    // Type filter dropdown handler
    if (typeFilter) {
        typeFilter.addEventListener("change", (e) => {
            currentTypeFilter = e.target.value;
            console.log("Type filter changed:", currentTypeFilter);
            filterParkingSpaces();
        });
    }
    
    // Vehicle filter dropdown handler
    if (vehicleFilter) {
        vehicleFilter.addEventListener("change",// views/admin_dashboard.js
    )}
});

document.addEventListener("DOMContentLoaded", () => {
    /* ===========================
       NAVIGATION (sidebar tabs)
    ============================ */
    const navLinks = document.querySelectorAll("[data-target]");
    const sections = document.querySelectorAll(".content-section");

    function showSection(id) {
        sections.forEach(s => s.classList.remove("active"));
        document.querySelectorAll(".nav-item").forEach(i => i.classList.remove("active"));

        const section = document.getElementById(id);
        if (section) section.classList.add("active");

        const navItem = document.querySelector(`.nav-item a[data-target="${id}"]`);
        if (navItem) navItem.parentElement.classList.add("active");
    }

    navLinks.forEach(link => {
        link.addEventListener("click", e => {
            e.preventDefault();
            showSection(link.getAttribute("data-target"));
        });
    });

    // Default open dashboard
    showSection("dashboard");

    /* ===========================
       MODALS
    ============================ */
    const addModal = document.getElementById("add-space-modal");
    const editModal = document.getElementById("edit-space-modal");
    const addBtn = document.getElementById("add-space-btn");
    const quickAddCard = document.getElementById("quick-add-card");

    // Show/hide delete button in modal based on edit mode
    function updateModalDeleteButton() {
        if (deleteSpaceBtn) {
            if (editMode) {
                deleteSpaceBtn.style.display = "inline-block";
            } else {
                deleteSpaceBtn.style.display = "none";
            }
        }
    }

    // Open add modal from button or quick add card
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

    // Open edit modal with pre-filled data
    function bindEditButtons() {
        document.querySelectorAll(".edit-btn").forEach(btn => {
            btn.addEventListener("click", (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                const id = btn.dataset.id || "";
                const name = btn.dataset.name || "";
                const max = btn.dataset.max || "";
                const type = btn.dataset.type || "";
                const vehicle = btn.dataset.vehicle || "";
                const image = btn.dataset.image || "";

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

                // Preview image if exists
                const imgPreview = document.getElementById("edit-space-preview");
                if (imgPreview) {
                    imgPreview.src = image || "../assets/img/default_parking.jpg";
                }

                // Update delete button visibility based on edit mode
                updateModalDeleteButton();

                if (editModal) editModal.style.display = "flex";
            });
        });
    }

    // Initial bind of edit buttons
    bindEditButtons();

    // Close modals
    document.querySelectorAll(".close-btn, .close-modal").forEach(el => {
        el.addEventListener("click", () => {
            document.querySelectorAll(".modal").forEach(m => {
                m.style.display = "none";
            });
        });
    });

    // Close modal when clicking outside
    window.addEventListener("click", e => {
        if (e.target.classList.contains("modal")) {
            e.target.style.display = "none";
        }
    });

    /* ===========================
       HELPERS
    ============================ */
    async function fetchJson(url, options) {
        try {
            const res = await fetch(url, options);
            const text = await res.text();
            
            if (!res.ok) {
                throw new Error(`HTTP ${res.status}: ${text}`);
            }
            
            try {
                return JSON.parse(text);
            } catch (parseErr) {
                console.error("❌ Server returned non-JSON:", text);
                throw new Error("Invalid server response. Check console for details.");
            }
        } catch (fetchErr) {
            console.error("❌ Fetch error:", fetchErr);
            throw fetchErr;
        }
    }

    const endpoint = "/parking-management-system/controller/parking_manager.php";

    /* ===========================
       FORMS
    ============================ */
    // ADD FORM
    const addForm = document.getElementById("add-space-form");
    if (addForm) {
        addForm.addEventListener("submit", async e => {
            e.preventDefault();
            
            const submitBtn = addForm.querySelector('button[type="submit"]');
            const originalText = submitBtn ? submitBtn.textContent : "";
            
            try {
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = "Adding...";
                }

                const formData = new FormData(addForm);
                formData.append("action", "add");

                const json = await fetchJson(endpoint, { 
                    method: "POST", 
                    body: formData 
                });
                
                alert(json.message || "Parking space added successfully!");
                
                if (json.success) {
                    // Close modal
                    if (addModal) addModal.style.display = "none";
                    
                    // Reset form
                    addForm.reset();
                    
                    // Clear image preview
                    const preview = document.getElementById("add-space-preview");
                    if (preview) preview.src = "";
                    
                    // Reload page to show new space
                    location.reload();
                }
            } catch (err) {
                console.error("Add error:", err);
                alert(`Error adding parking space: ${err.message}`);
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            }
        });
    }

    // EDIT FORM
    const editForm = document.getElementById("edit-space-form");
    if (editForm) {
        editForm.addEventListener("submit", async e => {
            e.preventDefault();
            
            const submitBtn = editForm.querySelector('button[type="submit"]');
            const originalText = submitBtn ? submitBtn.textContent : "";
            
            try {
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = "Updating...";
                }

                const formData = new FormData(editForm);
                formData.append("action", "edit");

                const json = await fetchJson(endpoint, { 
                    method: "POST", 
                    body: formData 
                });
                
                alert(json.message || "Parking space updated successfully!");
                
                if (json.success) {
                    // Close modal
                    if (editModal) editModal.style.display = "none";
                    
                    // Reload page to show updates
                    location.reload();
                }
            } catch (err) {
                console.error("Edit error:", err);
                alert(`Error updating parking space: ${err.message}`);
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            }
        });
    }

    // DELETE BUTTON - Using single declaration at the top
    const deleteSpaceBtn = document.getElementById("delete-space-btn");
    if (deleteSpaceBtn) {
        deleteSpaceBtn.addEventListener("click", async () => {
            const idField = document.getElementById("edit-space-id");
            const nameField = document.getElementById("edit-space-name");
            
            if (!idField) {
                alert("Cannot find parking space ID.");
                return;
            }
            
            const id = idField.value;
            const name = nameField ? nameField.value : "this parking space";
            
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
                    // Close modal
                    if (editModal) editModal.style.display = "none";
                    
                    // Reload page to reflect deletion
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

    /* ===========================
       IMAGE PREVIEWS
    ============================ */
    function bindImagePreview(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);

        if (input && preview) {
            input.addEventListener("change", () => {
                const file = input.files[0];
                if (file) {
                    // Validate file type
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    if (!allowedTypes.includes(file.type)) {
                        alert("Please select a valid image file (JPEG, PNG, GIF, or WebP).");
                        input.value = "";
                        return;
                    }
                    
                    // Validate file size (5MB max)
                    if (file.size > 5 * 1024 * 1024) {
                        alert("File size must be less than 5MB.");
                        input.value = "";
                        return;
                    }
                    
                    const reader = new FileReader();
                    reader.onload = e => {
                        preview.src = e.target.result;
                        preview.style.display = "block";
                    };
                    reader.onerror = () => {
                        alert("Error reading file.");
                        input.value = "";
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.src = "";
                    preview.style.display = "none";
                }
            });
        }
    }

    // Bind image preview handlers
    bindImagePreview("new-space-image", "add-space-preview");
    bindImagePreview("edit-space-image", "edit-space-preview");

    /* ===========================
       DYNAMIC CONTENT REFRESH
    ============================ */
    // Function to refresh parking spaces without full page reload
    async function refreshParkingSpaces() {
        try {
            const json = await fetchJson(`${endpoint}?action=list`);
            if (json.success && json.spaces) {
                // This would require updating the PHP to render just the parking cards
                // For now, we'll use location.reload() in the form handlers
                console.log("Parking spaces data:", json.spaces);
            }
        } catch (err) {
            console.error("Error refreshing parking spaces:", err);
        }
    }

    /* ===========================
       EDIT MODE TOGGLE
    ============================ */
    const editModeBtn = document.getElementById("edit-mode-btn");
    let editMode = false;
    
    if (editModeBtn) {
        editModeBtn.addEventListener("click", () => {
            editMode = !editMode;
            const parkingCards = document.querySelectorAll(".parking-card:not(.add-card)");
            const editButtons = document.querySelectorAll(".edit-btn");
            
            if (editMode) {
                editModeBtn.innerHTML = '<i class="fas fa-eye"></i> View Mode';
                editModeBtn.classList.remove("btn-primary");
                editModeBtn.classList.add("btn-warning");
                
                // Show edit buttons
                editButtons.forEach(btn => {
                    btn.style.display = "block";
                });
                
                parkingCards.forEach(card => {
                    card.classList.add("edit-mode");
                });
            } else {
                editModeBtn.innerHTML = '<i class="fas fa-edit"></i> Edit Mode';
                editModeBtn.classList.remove("btn-warning");
                editModeBtn.classList.add("btn-primary");
                
                // Hide edit buttons
                editButtons.forEach(btn => {
                    btn.style.display = "none";
                });
                
                parkingCards.forEach(card => {
                    card.classList.remove("edit-mode");
                });
            }
            
            // Update modal delete button visibility if modal is open
            updateModalDeleteButton();
        });
        
        // Initialize by hiding edit buttons on page load
        const editButtons = document.querySelectorAll(".edit-btn");
        editButtons.forEach(btn => {
            btn.style.display = "none";
        });
    }

    /* ===========================
       SEARCH AND FILTER
    ============================ */
    const searchInput = document.getElementById("space-search");
    const typeFilter = document.getElementById("type-filter");
    let currentSearchTerm = "";
    let currentTypeFilter = "all";
    
    function filterParkingSpaces() {
        const parkingCards = document.querySelectorAll(".parking-card:not(.add-card)");
        let visibleCount = 0;
        
        parkingCards.forEach(card => {
            const name = card.querySelector("h3")?.textContent.toLowerCase() || "";
            const paragraphs = card.querySelectorAll("p");
            let cardType = "";
            
            // Look for the type in the paragraphs
            paragraphs.forEach(p => {
                const text = p.textContent.toLowerCase();
                if (text.includes("type:")) {
                    const typeMatch = text.match(/type:\s*(\w+)/);
                    if (typeMatch) {
                        cardType = typeMatch[1].toLowerCase();
                    }
                }
            });
            
            // Debug logging
            console.log(`Card: ${name}, Type: ${cardType}, Search: ${currentSearchTerm}, Filter: ${currentTypeFilter}`);
            
            // Check search term match
            const matchesSearch = currentSearchTerm === "" || 
                                name.includes(currentSearchTerm) || 
                                cardType.includes(currentSearchTerm);
            
            // Check type filter match
            const matchesType = currentTypeFilter === "all" || cardType === currentTypeFilter;
            
            // Show card only if it matches both search and type filter
            if (matchesSearch && matchesType) {
                card.style.display = "block";
                visibleCount++;
            } else {
                card.style.display = "none";
            }
        });
        
        // Update visible count
        updateVisibleCount(visibleCount, parkingCards.length);
    }
    
    function updateVisibleCount(visible = null, total = null) {
        if (visible === null || total === null) {
            const visibleCards = document.querySelectorAll(".parking-card:not(.add-card):not([style*='display: none'])");
            const totalCards = document.querySelectorAll(".parking-card:not(.add-card)");
            visible = visibleCards.length;
            total = totalCards.length;
        }
        
        const countElement = document.getElementById("spaces-count");
        if (countElement) {
            countElement.textContent = `${visible} of ${total} spaces`;
        }
    }
    
    // Search input handler
    if (searchInput) {
        searchInput.addEventListener("input", (e) => {
            currentSearchTerm = e.target.value.toLowerCase();
            console.log("Search term changed:", currentSearchTerm);
            filterParkingSpaces();
        });
    }
    
    // Type filter dropdown handler - using a proper select element
    if (typeFilter) {
        typeFilter.addEventListener("change", (e) => {
            currentTypeFilter = e.target.value;
            console.log("Type filter changed:", currentTypeFilter);
            filterParkingSpaces();
        });
    }

    /* ===========================
       INITIALIZATION
    ============================ */
    console.log("Admin Dashboard initialized successfully");
    
    // Initial statistics update (if elements exist)
    updateDashboardStats();
    
    // Initialize filter count
    updateVisibleCount();
    
    // Initialize delete button as hidden
    if (deleteSpaceBtn) {
        deleteSpaceBtn.style.display = "none";
    }
    
    function updateDashboardStats() {
        // This would connect to your backend to get real-time stats
        // For now, it's a placeholder
        const availableSpaces = document.querySelector(".card-value.available");
        const takenSpaces = document.querySelector(".card-value.taken");
        
        // You can implement actual stats fetching here
        if (availableSpaces && takenSpaces) {
            // Placeholder values - replace with actual API calls
            // availableSpaces.textContent = "124";
            // takenSpaces.textContent = "64";
        }
    }
});