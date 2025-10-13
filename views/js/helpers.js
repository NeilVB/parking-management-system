document.addEventListener("DOMContentLoaded", () => {
    /* ===========================
    views/js/helpers.js
    ============================ */

    /**
     * Safely fetch JSON from a given URL with error handling.
     * Logs raw response if JSON parsing fails.
     */
    window.fetchJson = async function (url, options = {}) {
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
    };

    /**
     * Global endpoint for parking manager actions
     */
    window.endpoint = "/parking-management-system/controller/parking_manager.php";
});
