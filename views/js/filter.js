document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("space-search");
    const typeFilter = document.getElementById("type-filter");
    const vehicleFilter = document.getElementById("vehicle-filter");

    let currentSearchTerm = "";
    let currentTypeFilter = "all";
    let currentVehicleFilter = "all";

    function normalize(text) {
        return (text || "").toLowerCase().trim();
    }

    function filterParkingSpaces() {
        const parkingCards = document.querySelectorAll(".parking-container .parking-card:not(.add-card)");
        let visibleCount = 0;

        parkingCards.forEach(card => {
            const name = normalize(card.dataset.name);
            const userTypes = (card.dataset.userTypes || "")
                .split(",")
                .map(normalize)
                .filter(v => v !== "");
            const vehicles = (card.dataset.vehicles || "")
                .split(",")
                .map(normalize)
                .filter(v => v !== "");

            const matchesSearch =
                currentSearchTerm === "" ||
                name.includes(currentSearchTerm);

            const matchesType =
                currentTypeFilter === "all" ||
                userTypes.includes(normalize(currentTypeFilter));

            const matchesVehicle =
                currentVehicleFilter === "all" ||
                vehicles.includes(normalize(currentVehicleFilter));

            if (matchesSearch && matchesType && matchesVehicle) {
                card.style.display = "";
                visibleCount++;
            } else {
                card.style.display = "none";
            }
        });

        updateVisibleCount(visibleCount, parkingCards.length);
    }

    function updateVisibleCount(visible, total) {
        const countElement = document.getElementById("spaces-count");
        if (countElement) {
            countElement.textContent = `${visible} of ${total} spaces`;
        }
    }

    // 🧠 Event bindings
    if (searchInput) {
        searchInput.addEventListener("input", e => {
            currentSearchTerm = normalize(e.target.value);
            filterParkingSpaces();
        });
    }

    if (typeFilter) {
        typeFilter.addEventListener("change", e => {
            currentTypeFilter = normalize(e.target.value);
            filterParkingSpaces();
        });
    }

    if (vehicleFilter) {
        vehicleFilter.addEventListener("change", e => {
            currentVehicleFilter = normalize(e.target.value);
            filterParkingSpaces();
        });
    }

    // Run once after everything is rendered
    setTimeout(filterParkingSpaces, 300);
});
