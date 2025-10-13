document.addEventListener("DOMContentLoaded", () => {
    /* ===========================
    refresh.js
    ============================ */
    // Function to refresh parking spaces without full page reload
    async function refreshParkingSpaces() {
        try {
            const json = await fetchJson(`${endpoint}?action=list`);
            if (json.success && json.spaces) {
                console.log("Parking spaces data:", json.spaces);
            }
        } catch (err) {
            console.error("Error refreshing parking spaces:", err);
        }
    }
});