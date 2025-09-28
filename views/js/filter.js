document.addEventListener("DOMContentLoaded", () => {
    /* ===========================
       SEARCH AND FILTER
    ============================ */

    const searchInput = document.getElementById("space-search");
    const typeFilter = document.getElementById("type-filter");
    const vehicleFilter = document.getElementById("vehicle-filter");

    let currentSearchTerm = "";
    let currentTypeFilter = "all";
    let currentVehicleFilter = "all";

    // Normalize text by removing emojis, symbols, and extra spaces
    function normalize(text) {
        return text
            .toLowerCase()
            .replace(/[^a-z]/gi, "") // keep only letters
            .trim();
    }

    function filterParkingSpaces() {
        const parkingCards = document.querySelectorAll(".parking-card:not(.add-card)");
        let visibleCount = 0;

        parkingCards.forEach(card => {
            const name = card.querySelector("h3")?.textContent.toLowerCase() || "";
            const paragraphs = card.querySelectorAll("p");
            let cardType = "";
            let cardVehicle = "";

            // Extract type and vehicle info
            paragraphs.forEach(p => {
                const text = p.textContent.toLowerCase();

                const typeMatch = text.match(/type:\s*(.+)/i);
                const vehicleMatch = text.match(/vehicle:\s*(.+)/i);

                if (typeMatch) cardType = normalize(typeMatch[1]);
                if (vehicleMatch) cardVehicle = normalize(vehicleMatch[1]);
            });

            const matchesSearch =
                currentSearchTerm === "" ||
                name.includes(currentSearchTerm) ||
                cardType.includes(currentSearchTerm) ||
                cardVehicle.includes(currentSearchTerm);

            const matchesType =
                currentTypeFilter === "all" || cardType === normalize(currentTypeFilter);

            const matchesVehicle =
                currentVehicleFilter === "all" || cardVehicle === normalize(currentVehicleFilter);

            if (matchesSearch && matchesType && matchesVehicle) {
                card.style.display = "block";
                visibleCount++;
            } else {
                card.style.display = "none";
            }
        });

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

    // Event bindings
    if (searchInput) {
        searchInput.addEventListener("input", e => {
            currentSearchTerm = e.target.value.toLowerCase();
            filterParkingSpaces();
        });
    }

    if (typeFilter) {
        typeFilter.addEventListener("change", e => {
            currentTypeFilter = e.target.value;
            filterParkingSpaces();
        });
    }

    if (vehicleFilter) {
        vehicleFilter.addEventListener("change", e => {
            currentVehicleFilter = e.target.value;
            filterParkingSpaces();
        });
    }

    // Initial filter count
    filterParkingSpaces();
});
