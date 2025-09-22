document.addEventListener('DOMContentLoaded', function () {
    const navLinks = document.querySelectorAll('.nav-item a');
    const contentSections = document.querySelectorAll('.content-section');

    // --- Modal Elements (Add) ---
    const addSpaceModal = document.getElementById('add-space-modal');
    const addSpaceForm = document.getElementById('add-space-form');

    function openAddSpaceModal() {
        if (addSpaceModal) {
            addSpaceModal.style.display = 'flex';
            addSpaceModal.style.zIndex = '9999';
        }
    }
    function closeAddSpaceModal() {
        if (addSpaceModal) addSpaceModal.style.display = 'none';
    }

    // Close add modal
    document.querySelectorAll('#add-space-modal .close-btn').forEach(btn => {
        btn.addEventListener('click', closeAddSpaceModal);
    });
    window.addEventListener('click', (event) => {
        if (event.target === addSpaceModal) {
            closeAddSpaceModal();
        }
    });

    // --- Modal Elements (Edit) ---
    const editSpaceModal = document.getElementById('edit-space-modal');
    const editSpaceForm = document.getElementById('edit-space-form');
    const deleteSpaceBtn = document.getElementById('delete-space-btn');

    function openEditSpaceModal(space) {
        document.getElementById('edit-space-id').value = space.id;
        document.getElementById('edit-space-name').value = space.name;
        document.getElementById('edit-space-max').value = space.max_space;
        editSpaceModal.style.display = 'flex';
    }
    function closeEditSpaceModal() {
        editSpaceModal.style.display = 'none';
    }
    document.querySelectorAll('#edit-space-modal .close-btn').forEach(btn => {
        btn.addEventListener('click', closeEditSpaceModal);
    });

    // --- Fetch spaces from backend ---
    async function loadParkingSpaces() {
        try {
            const response = await fetch('get_spaces.php');
            const data = await response.json();

            // 0 handle both array OR {spaces, summary}
            const spaces = Array.isArray(data) ? data : data.spaces;
            const summary = data.summary || null;

            renderParkingSpaces(spaces);
            if (summary) updateDashboardSummary(summary);

        } catch (err) {
            console.error("Error loading parking spaces:", err);
        }
    }

    // --- Render spaces into grid ---
    function renderParkingSpaces(spaces) {
        const grid = document.querySelector('.parking-grid');
        if (!grid) return;
        grid.innerHTML = '';

        spaces.forEach(space => {
            const available = space.max_space - space.used_space;
            const card = document.createElement('div');
            card.className = 'parking-space-card';

            card.innerHTML = `
                <span class="parking-space-name">${space.name}</span>
                <span class="parking-space-availability">${available}/${space.max_space}</span>
                <button class="btn-edit" data-id="${space.id}"><i class="fas fa-edit"></i></button>
            `;

            grid.appendChild(card);
        });

        // Bind edit button events
        grid.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                const space = spaces.find(s => s.id == id);
                if (space) openEditSpaceModal(space);
            });
        });

        // Re-create the "Add Parking Space" card
        const addCard = document.createElement('div');
        addCard.className = 'add-space-card';
        addCard.innerHTML = `
            <div class="add-space-content">
                <i class="fas fa-plus"></i>
                <span>Add Parking Space</span>
            </div>
        `;
        addCard.addEventListener('click', openAddSpaceModal);
        grid.appendChild(addCard);
    }

    // --- Update Dashboard Counters ---
    function updateDashboardSummary(summary) {
        const availableEl = document.querySelector('.card-value.available');
        const takenEl = document.querySelector('.card-value.taken');
        const reservedEl = document.querySelector('.card-value.reserved');

        if (availableEl) availableEl.textContent = summary.available;
        if (takenEl) takenEl.textContent = summary.taken;
        if (reservedEl) reservedEl.textContent = summary.reserved;
    }

    // --- Handle Add form submission ---
    if (addSpaceForm) {
        addSpaceForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(addSpaceForm);
            try {
                const response = await fetch('add_space.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                alert(result.message);
                if (result.success) {
                    addSpaceForm.reset();
                    closeAddSpaceModal();
                    loadParkingSpaces();
                }
            } catch (err) {
                console.error("Error adding parking space:", err);
                alert("Error adding parking space. Check console for details.");
            }
        });
    }

    // --- Handle Edit form submission ---
    if (editSpaceForm) {
        editSpaceForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(editSpaceForm);
            const response = await fetch('edit_space.php', { method: 'POST', body: formData });
            const result = await response.json();
            alert(result.message);
            if (result.success) {
                closeEditSpaceModal();
                loadParkingSpaces();
            }
        });
    }
    // --- Handle Delete button ---
    if (deleteSpaceBtn) {
        deleteSpaceBtn.addEventListener('click', async () => {
            if (!confirm("Are you sure you want to delete this parking space?")) return;
            const id = document.getElementById('edit-space-id').value;
            const response = await fetch('delete_space.php', {
                method: 'POST',
                body: new URLSearchParams({ id })
            });
            const result = await response.json();
            alert(result.message);
            if (result.success) {
                closeEditSpaceModal();
                loadParkingSpaces();
            }
        });
    }
    // --- Navigation Logic ---
    navLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target');

            if (targetId === 'parking-spaces' || targetId === 'dashboard') {
                loadParkingSpaces(); // refresh both spaces + summary
            }

            // Update active state
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });
            this.parentElement.classList.add('active');

            // Show target content
            contentSections.forEach(section => {
                section.classList.toggle('active', section.id === targetId);
            });
        });
    });

    // load data on first page load
    loadParkingSpaces();

    const footerLinks = document.querySelectorAll('.footer-links a[data-target]');
    console.log('Footer links found:', footerLinks);

    footerLinks.forEach(link => {
        console.log('Adding listener to:', link);
        link.addEventListener('click', function (e) {
            console.log('Footer link clicked:', this.getAttribute('data-target'));
            e.preventDefault();

            const targetId = this.getAttribute('data-target');
            console.log('Target ID:', targetId);

            const allSections = document.querySelectorAll('.content-section');
            allSections.forEach(section => {
                section.classList.remove('active');
            });

            const targetSection = document.getElementById(targetId);
            console.log('Target section found:', targetSection);
            if (targetSection) {
                targetSection.classList.add('active');
                console.log('Added active class to target section');
            }

            const allNavItems = document.querySelectorAll('.nav-item');
            allNavItems.forEach(item => {
                item.classList.remove('active');
            });
        });
    });


});
