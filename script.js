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
            name: 'Football Field Side',
            available: 15,
            total: 20,
            imageUrl: 'assets/football-field-side.png'
        },
        {
            name: 'Cafeteria Front',
            available: 5,
            total: 30,
            imageUrl: 'assets/cafeteria-front.png'
        }

        
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

        // **This is the new part:**
        // Now, create and append the "Add Parking Space" card at the end
        const addCardHTML = `
            <div class="add-space-card">
                <div class="add-space-content">
                    <i class="fas fa-plus"></i>
                    <span>Add Parking Space</span>
                </div>
            </div>
        `;
        grid.insertAdjacentHTML('beforeend', addCardHTML);

        // Add event listener for the newly created "Add" card
        const addSpaceBtn = grid.querySelector('.add-space-card');
        if (addSpaceBtn) {
            addSpaceBtn.addEventListener('click', openAddSpaceModal);
        }

        parkingSpacesRendered = true; // Set the flag to true
    }

    // --- Modal Functionality ---
    const addSpaceModal = document.getElementById('add-space-modal');
    const closeModalBtn = document.querySelector('.close-btn');
    const addSpaceForm = document.getElementById('add-space-form');

    function openAddSpaceModal() {
        if (addSpaceModal) {
            addSpaceModal.style.display = 'flex';
        }
    }

    function closeAddSpaceModal() {
        if (addSpaceModal) {
            addSpaceModal.style.display = 'none';
        }
    }

    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeAddSpaceModal);
    }

    window.addEventListener('click', (event) => {
        if (event.target == addSpaceModal) {
            closeAddSpaceModal();
        }
    });

    if (addSpaceForm) {
        addSpaceForm.addEventListener('submit', (event) => {
            event.preventDefault(); // Prevent actual form submission
            
            const spaceName = document.getElementById('new-space-name').value;
            const studentSlots = document.getElementById('student-visitor-slots').value;
            const facultySlots = document.getElementById('faculty-slots').value;
            const executiveSlots = document.getElementById('executive-slots').value;

            console.log('New Parking Area:', {
                name: spaceName,
                student: studentSlots,
                faculty: facultySlots,
                executives: executiveSlots
            });

            alert(`Parking area "${spaceName}" would be created! Check the console for details.`);
            addSpaceForm.reset();
            closeAddSpaceModal();
        });
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