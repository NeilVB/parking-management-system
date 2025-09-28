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
