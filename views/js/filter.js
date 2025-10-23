document.addEventListener("DOMContentLoaded", () => {
    // Parking space filter
    const searchInput = document.getElementById("space-search");
    const typeFilter = document.getElementById("type-filter");
    const vehicleFilter = document.getElementById("vehicle-filter");

    let currentSearchTerm = "";
    let currentTypeFilter = "all";
    let currentVehicleFilter = "all";

    function normalize(text) {
        return (text || "").toLowerCase().trim();
    }

    function updateVisibleCount(visible, total) {
        const countElement = document.getElementById("spaces-count");
        if (countElement) {
            countElement.textContent = `${visible} of ${total} spaces`;
        }
    }

    function filterParkingSpaces() {
        const parkingCards = document.querySelectorAll(".parking-container .parking-card:not(.add-card)");
        const visibleIds = new Set();
        const totalIds = new Set();

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

            const matchesSearch = currentSearchTerm === "" || name.includes(currentSearchTerm);
            const matchesType = currentTypeFilter === "all" || userTypes.includes(normalize(currentTypeFilter));
            const matchesVehicle = currentVehicleFilter === "all" || vehicles.includes(normalize(currentVehicleFilter));

            totalIds.add(card.dataset.id);

            if (matchesSearch && matchesType && matchesVehicle) {
                card.style.display = "";
                visibleIds.add(card.dataset.id);
            } else {
                card.style.display = "none";
            }
        });

        updateVisibleCount(visibleIds.size, totalIds.size);
    }

    // Event bindings for parking spaces filter
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

    // Distribution filter (ajax)
    const distributionTypeFilter = document.getElementById("distribution-type-filter");
    const distributionVehicleFilter = document.getElementById("distribution-vehicle-filter");

    let currentDistributionType = "all";
    let currentDistributionVehicle = "all";

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    async function updateDistribution() {
        const distributionContainer = document.getElementById("distribution-container");
        
        if (!distributionContainer) return;

        try {
            distributionContainer.innerHTML = '<p style="text-align: center; color: #666;">Loading...</p>';
            
            const response = await fetch(`/parking-management-system/ajax/get_distribution.php?userType=${currentDistributionType}&vehicleType=${currentDistributionVehicle}`);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            
            if (data.success && data.distribution.length > 0) {
                let html = '';
                data.distribution.forEach(slot => {
                    html += `
                        <div class="slot-item">
                            <span class="slot-count">${slot.slots}</span>
                            <span class="slot-name">${escapeHtml(slot.location)}</span>
                        </div>
                    `;
                });
                distributionContainer.innerHTML = html;
            } else {
                distributionContainer.innerHTML = '<p>No parking areas found.</p>';
            }
        } catch (error) {
            console.error('[Filter] Error fetching distribution:', error);
            distributionContainer.innerHTML = '<p style="color: #d9534f;">Error loading data. Please try again.</p>';
        }
    }

    // Event listeners for distribution filters
    if (distributionTypeFilter) {
        distributionTypeFilter.addEventListener("change", (e) => {
            currentDistributionType = e.target.value;
            updateDistribution();
        });
    }

    if (distributionVehicleFilter) {
        distributionVehicleFilter.addEventListener("change", (e) => {
            currentDistributionVehicle = e.target.value;
            updateDistribution();
        });
    }
});