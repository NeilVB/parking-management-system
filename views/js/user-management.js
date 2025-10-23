document.addEventListener('DOMContentLoaded', function() {
    // Modal & Form References
    const userModal = document.getElementById('user-modal');
    const userForm = document.getElementById('user-form');
    const modalTitle = document.getElementById('modal-title');
    const vehicleModal = document.getElementById('vehicle-modal');
    const vehicleForm = document.getElementById('vehicle-form');

    // USER MANAGEMENT

    // Handle Edit Button Click
    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', async (e) => {
            const userId = e.currentTarget.dataset.id;
            modalTitle.textContent = 'Edit User';
            await loadUserData(userId);
            userModal.style.display = 'flex';
        });
    });

    // Handle Delete Button Click
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', async (e) => {
            if (confirm('Are you sure you want to delete this user?')) {
                const userId = e.currentTarget.dataset.id;
                await deleteUser(userId);
            }
        });
    });

    // Close Modals
    document.querySelectorAll('.close-modal, .close-btn').forEach(button => {
        button.addEventListener('click', () => {
            userModal.style.display = 'none';
            vehicleModal.style.display = 'none';
        });
    });

    // Handle User Form Submit
    userForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(userForm);
        await saveUser(formData);
    });

    // Load User Data
    async function loadUserData(userId) {
        try {
            const response = await fetch(`../controller/user_actions.php?action=get_user&id=${userId}`);
            const data = await response.json();

            if (data.success) {
                populateForm(data.user);
                console.log("Loading user vehicles for ID:", userId);
                await loadUserVehicles(userId);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    // Populate User Form
    function populateForm(user) {
        document.getElementById('user-id').value = user.user_id;
        document.getElementById('firstname').value = user.firstname;
        document.getElementById('lastname').value = user.lastname;
        document.getElementById('email').value = user.email;
        document.getElementById('phone').value = user.phone_number;
        document.getElementById('user-type').value = user.user_type_id;
    }

    // Save User
    async function saveUser(formData) {
        try {
            const response = await fetch('../controller/user_actions.php?action=update_user', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                alert('User updated successfully!');
                location.reload();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    // Delete User
    async function deleteUser(userId) {
        try {
            const response = await fetch('../controller/user_actions.php?action=delete_user', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user_id: userId })
            });
            const data = await response.json();

            if (data.success) {
                alert('User deleted successfully!');
                location.reload();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    // === VEHICLE MANAGEMENT ===

    // Handle Vehicle Form Submit
    vehicleForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(vehicleForm);
        await updateVehicle(formData);
    });

    // Load User Vehicles
    async function loadUserVehicles(userId) {
        try {
            const response = await fetch(`../controller/user_actions.php?action=get_vehicles&user_id=${userId}`);
            const data = await response.json();

            if (data.success) {
                displayVehicles(data.vehicles);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    // Display Vehicles
    function displayVehicles(vehicles) {
        const vehiclesList = document.getElementById('vehicles-list');
        console.log("Displaying vehicles:", vehicles);

        if (!vehicles || vehicles.length === 0) {
            vehiclesList.innerHTML = '<p>No vehicles registered</p>';
            return;
        }

        vehiclesList.innerHTML = vehicles.map(vehicle => `
            <div class="vehicle-item">
                <div class="vehicle-info">
                    <strong>${vehicle.plate_number}</strong><br>
                    ${vehicle.make} ${vehicle.model} (${vehicle.year})<br>
                    <small>${vehicle.vehicle_type} • ${vehicle.color}</small>
                </div>
                <div class="vehicle-actions">
                    <button type="button" class="btn btn-warning btn-sm" onclick="editVehicle('${vehicle.vehicle_id}')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteVehicle('${vehicle.vehicle_id}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }

    // Edit Vehicle
    window.editVehicle = async function(vehicleId) {
        try {
            const response = await fetch(`../controller/user_actions.php?action=get_vehicle&id=${vehicleId}`);
            const data = await response.json();

            if (data.success) {
                const vehicle = data.vehicle;
                document.getElementById('vehicle-id').value = vehicle.vehicle_id;
                document.getElementById('vehicle-user-id').value = vehicle.user_id;
                document.getElementById('plate_number').value = vehicle.plate_number;
                document.getElementById('vehicle_make').value = vehicle.make;
                document.getElementById('vehicle_model').value = vehicle.model;
                document.getElementById('vehicle_year').value = vehicle.year;
                document.getElementById('vehicle_color').value = vehicle.color;
                document.getElementById('vehicle_type').value = vehicle.vehicle_type_id;

                vehicleModal.style.display = 'flex';
            }
        } catch (error) {
            console.error('Error loading vehicle:', error);
        }
    };

    // Delete Vehicle
    window.deleteVehicle = async function(vehicleId) {
        if (confirm('Are you sure you want to delete this vehicle?')) {
            try {
                const response = await fetch('../controller/user_actions.php?action=delete_vehicle', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ vehicle_id: vehicleId })
                });
                const data = await response.json();

                if (data.success) {
                    alert('Vehicle deleted successfully!');
                    await loadUserVehicles(document.getElementById('user-id').value);
                }
            } catch (error) {
                console.error('Error deleting vehicle:', error);
            }
        }
    };

    // Update Vehicle
    async function updateVehicle(formData) {
        try {
            const response = await fetch('../controller/user_actions.php?action=update_vehicle', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                alert('Vehicle updated successfully!');
                vehicleModal.style.display = 'none';
                await loadUserVehicles(document.getElementById('user-id').value);
            }
        } catch (error) {
            console.error('Error updating vehicle:', error);
        }
    }
});
