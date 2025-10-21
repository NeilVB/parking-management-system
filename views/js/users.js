document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('user-search');
    const typeFilter = document.getElementById('user-type-filter');
    const tbody = document.getElementById('users-table-body');

    function filterUsers() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedType = typeFilter.value; // This can be "" for "All User Types"
        const rows = tbody.getElementsByTagName('tr');

        Array.from(rows).forEach(row => {
            const name = row.cells[1].textContent.toLowerCase();
            const email = row.cells[2].textContent.toLowerCase();
            const type = row.cells[4].textContent.toLowerCase();
            const typeId = row.getAttribute('data-type-id');

            // Search match
            const matchesSearch = !searchTerm || 
                                name.includes(searchTerm) || 
                                email.includes(searchTerm) || 
                                type.includes(searchTerm);
            
            // Type filter match - show all if no type selected ("") or matches selected type
            const matchesType = selectedType === "" || typeId === selectedType;

            // Show row only if both conditions are met
            row.style.display = (matchesSearch && matchesType) ? '' : 'none';
        });
    }

    // Add event listeners
    searchInput.addEventListener('input', filterUsers);
    typeFilter.addEventListener('change', filterUsers);

    // Initial filter
    filterUsers();
});