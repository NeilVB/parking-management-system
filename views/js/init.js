document.addEventListener("DOMContentLoaded", () => {
    /* ===========================
    init.js
    ============================ */
    console.log("Admin Dashboard initialized successfully");

    // Shared global state
    window.editMode = false;

    // Initial statistics update (if elements exist)
    updateDashboardStats();

    // Initialize filter count (function from filter.js)
    if (typeof updateVisibleCount === "function") {
        updateVisibleCount();
    }

    // Initialize delete button as hidden
    const deleteSpaceBtn = document.getElementById("delete-space-btn");
    if (deleteSpaceBtn) {
        deleteSpaceBtn.style.display = "none"; // ✅ Only if not in editMode
    }

    function updateDashboardStats() {
        // Placeholder for future backend integration
        const availableSpaces = document.querySelector(".card-value.available");
        const takenSpaces = document.querySelector(".card-value.taken");

        if (availableSpaces && takenSpaces) {
            // Example: availableSpaces.textContent = "124";
            // Example: takenSpaces.textContent = "64";
        }
    }
});
