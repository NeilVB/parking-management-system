<!-- =============================
     USER MANAGEMENT MODAL
============================= -->
<div id="user-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><span id="modal-title">Edit User</span></h2>
            <button type="button" class="close-btn" aria-label="Close">&times;</button>
        </div>
        
        <form id="user-form">
            <input type="hidden" id="user-id" name="user_id">
            
            <!-- User Information -->
            <div class="form-group">
                <label for="firstname">First Name</label>
                <input type="text" id="firstname" name="firstname" required>
            </div>

            <div class="form-group">
                <label for="lastname">Last Name</label>
                <input type="text" id="lastname" name="lastname" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" required>
            </div>

            <div class="form-group">
                <label for="user-type">User Type</label>
                <select id="user-type" name="user_type_id" required>
                    <?php
                    $typeQuery = "SELECT * FROM user_type_table ORDER BY user_type_id";
                    $typeResult = $conn->query($typeQuery);
                    while($type = $typeResult->fetch_assoc()): ?>
                        <option value="<?php echo $type['user_type_id']; ?>">
                            <?php echo htmlspecialchars($type['user_type']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Vehicles Section -->
            <div class="vehicles-section">
                <h3>Registered Vehicles</h3>
                <div id="vehicles-list"></div>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-secondary close-modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- =============================
     VEHICLE MANAGEMENT MODAL
============================= -->
<div id="vehicle-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Vehicle</h2>
            <button type="button" class="close-btn" aria-label="Close">&times;</button>
        </div>
        
        <form id="vehicle-form">
            <input type="hidden" id="vehicle-id" name="vehicle_id">
            <input type="hidden" id="vehicle-user-id" name="user_id">
            
            <div class="form-group">
                <label for="plate_number">Plate Number</label>
                <input type="text" id="plate_number" name="plate_number" required>
            </div>

            <div class="form-group">
                <label for="vehicle_make">Make</label>
                <input type="text" id="vehicle_make" name="make" required>
            </div>

            <div class="form-group">
                <label for="vehicle_model">Model</label>
                <input type="text" id="vehicle_model" name="model" required>
            </div>

            <div class="form-group">
                <label for="vehicle_year">Year</label>
                <input type="number" id="vehicle_year" name="year" required>
            </div>

            <div class="form-group">
                <label for="vehicle_color">Color</label>
                <input type="text" id="vehicle_color" name="color" required>
            </div>

            <div class="form-group">
                <label for="vehicle_type">Vehicle Type</label>
                <select id="vehicle_type" name="vehicle_type_id" required>
                    <?php
                    $vtypeQuery = "SELECT * FROM vehicle_type_table";
                    $vtypeResult = $conn->query($vtypeQuery);
                    while($vtype = $vtypeResult->fetch_assoc()): ?>
                        <option value="<?php echo $vtype['vehicle_type_id']; ?>">
                            <?php echo htmlspecialchars($vtype['vehicle_type']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-secondary close-modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>