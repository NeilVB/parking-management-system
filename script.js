document.addEventListener('DOMContentLoaded', function () {
    const navLinks = document.querySelectorAll('.nav-item a');
    const contentSections = document.querySelectorAll('.content-section');
    let parkingSpacesRendered = false; // Add a flag to track if we've rendered the cards

    // --- Data for Parking Spaces ---
    const parkingSpacesData = [
        {
            name: 'Football Field Front',
            available: 13,
            total: 25,
            imageUrl: 'assets/football-field-front.png'
        },
        {
            name: 'Cafeteria Front',
            available: 9,
            total: 15,
            imageUrl: 'assets/cafeteria-front.png'
        },
        {
            name: 'Football Field Side',
            available: 9,
            total: 15,
            imageUrl: 'assets/football-field-side.png'
        },
        
                {
            name: 'Football Fiel5d Front',
            available: 13,
            total: 25,
            imageUrl: 'assets/football-field-front.png'
        },
        {
            name: 'Cafeter5ia Front',
            available: 9,
            total: 15,
            imageUrl: 'assets/cafeteria-front.png'
        },
        {
            name: 'Footb5all Field Side',
            available: 9,
            total: 15,
            imageUrl: 'assets/football-field-side.png'
        },
        
        
    ];

    // --- Function to Render Parking Spaces ---
    function renderParkingSpaces() {
        const grid = document.querySelector('.parking-grid');
        if (!grid || parkingSpacesRendered) return; // Don't re-render if already done
        grid.innerHTML = ''; // Clear existing content

        parkingSpacesData.forEach(space => {
            const card = document.createElement('div');
            card.className = 'parking-space-card';
            card.style.backgroundImage = `url('${space.imageUrl}')`;

            card.innerHTML = `
                <span class="parking-space-name">${space.name}</span>
                <span class="parking-space-availability">${space.available}/${space.total}</span>
            `;

            grid.appendChild(card);
        });
        parkingSpacesRendered = true; // Set the flag to true
    }

    // --- Initial Page Load ---
    // We remove the initial call from here.
    // renderParkingSpaces(); 

    // --- Navigation Logic ---
    navLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();

            const targetId = this.getAttribute('data-target');

            // --- New logic to render on demand ---
            if (targetId === 'parking-spaces') {
                renderParkingSpaces();
            }

            // Update active state for nav items
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });
            this.parentElement.classList.add('active');

            // Show the target content and hide others
            contentSections.forEach(section => {
                if (section.id === targetId) {
                    section.classList.add('active');
                } else {
                    section.classList.remove('active');
                }
            });
        });
    });
});