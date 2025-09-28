// This is for the functionality of the sidebar Tabs
document.addEventListener('DOMContentLoaded', () => {
    const navLinks = document.querySelectorAll('[data-target]');
    const sections = document.querySelectorAll('.content-section');

    function showSection(targetId) {
        // Hide all sections
        sections.forEach(section => {
            section.classList.remove('active');
        });

        // Remove active state from sidebar items
        document.querySelectorAll('.nav-item').forEach(item => {
            item.classList.remove('active');
        });

        // Show the clicked section
        const targetSection = document.getElementById(targetId);
        if (targetSection) {
            targetSection.classList.add('active');
        }

        // Highlight the clicked nav item (if it exists in sidebar)
        const navItem = document.querySelector(`.nav-item a[data-target="${targetId}"]`);
        if (navItem) {
            navItem.parentElement.classList.add('active');
        }
    }

    // Attach click listeners to all links with data-target
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = link.getAttribute('data-target');
            if (targetId) {
                showSection(targetId);
            }
        });
    });

    // On load, make sure Dashboard is visible
    showSection('dashboard');
});

// This is for the functionality of add parking space modals 
document.addEventListener('DOMContentLoaded', () => {
    const addModal = document.getElementById('add-space-modal');
    const editModal = document.getElementById('edit-space-modal');

    const addBtn = document.getElementById('add-space-btn');
    const quickAddCard = document.getElementById('quick-add-card');
    const closeBtns = document.querySelectorAll('.close-btn, .close-modal');

    // Open Add Modal
    if (addBtn) addBtn.addEventListener('click', () => addModal.style.display = 'flex');
    if (quickAddCard) quickAddCard.addEventListener('click', () => addModal.style.display = 'flex');

    // Close modals
    closeBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            addModal.style.display = 'none';
            editModal.style.display = 'none';
        });
    });

    // Close when clicking outside modal
    window.addEventListener('click', (e) => {
        if (e.target === addModal) addModal.style.display = 'none';
        if (e.target === editModal) editModal.style.display = 'none';
    });
});
