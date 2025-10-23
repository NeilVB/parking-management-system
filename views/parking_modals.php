<!-- === ADD PARKING SPACE MODAL === -->
<div id="add-space-modal" class="modal" role="dialog" aria-modal="true">
    <div class="modal-content add-space-modal">
        <div class="modal-header">
            <h2>Add Parking Space</h2>
            <button type="button" class="close-btn" aria-label="Close">&times;</button>
        </div>

        <form id="add-space-form" enctype="multipart/form-data">
            <!-- Space Name -->
            <div class="form-section">
                <label for="add-space-name">Parking Space Name</label>
                <input type="text" id="add-space-name" name="name" placeholder="Enter parking space name" required>
            </div>

            <!-- Vehicle Types -->
            <div class="form-section">
                <label>Select Vehicle Types</label>
                <div class="vehicle-checkboxes">
                    <label><input type="checkbox" id="add-supports-car" name="supports_car" value="1"> Car</label>
                    <label><input type="checkbox" id="add-supports-motorcycle" name="supports_motorcycle" value="1"> Motorcycle</label>
                </div>
            </div>

            <!-- Allocations -->
            <div id="add-allocations-container" class="slot-config" style="display:none;">
                <!-- Car Allocations -->
                <div id="add-car-allocations" class="slot-section" style="display:none;">
                    <h3>Car Parking Slots</h3>
                    <div class="slot-inputs">
                        <label>General <input type="number" id="add-car-general" class="allocation-input" value="0" min="0"></label>
                        <label>Faculty <input type="number" id="add-car-faculty" class="allocation-input" value="0" min="0"></label>
                        <label>Executive <input type="number" id="add-car-executive" class="allocation-input" value="0" min="0"></label>
                        <label>Guest <input type="number" id="add-car-guest" class="allocation-input" value="0" min="0"></label>
                    </div>
                    <div class="slot-total">Total Car Slots: <span id="add-car-total">0</span></div>
                </div>

                <!-- Motorcycle Allocations -->
                <div id="add-motorcycle-allocations" class="slot-section" style="display:none;">
                    <h3>Motorcycle Parking Slots</h3>
                    <div class="slot-inputs">
                        <label>General <input type="number" id="add-motorcycle-general" class="allocation-input" value="0" min="0"></label>
                        <label>Faculty <input type="number" id="add-motorcycle-faculty" class="allocation-input" value="0" min="0"></label>
                        <label>Executive <input type="number" id="add-motorcycle-executive" class="allocation-input" value="0" min="0"></label>
                        <label>Guest <input type="number" id="add-motorcycle-guest" class="allocation-input" value="0" min="0"></label>
                    </div>
                    <div class="slot-total">Total Motorcycle Slots: <span id="add-motorcycle-total">0</span></div>
                </div>
            </div>

            <!-- Upload Image -->
            <div class="form-section upload-section">
                <label for="add-space-image">Upload Parking Image</label>
                <input type="file" id="add-space-image" name="image" accept="image/*">
                <img id="add-space-preview" alt="Image preview" style="max-width:200px; display:none; margin-top:10px;">
            </div>

            <!-- Buttons -->
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary close-modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Parking Space</button>
            </div>
        </form>
    </div>
</div>


<!-- === DIT PARKING SPACE MODAL === -->
<div id="edit-space-modal" class="modal" role="dialog" aria-modal="true">
    <div class="modal-content edit-space-modal">
        <div class="modal-header">
            <h2>Edit Parking Space</h2>
            <button type="button" class="close-btn" aria-label="Close">&times;</button>
        </div>

        <form id="edit-space-form" enctype="multipart/form-data">
            <input type="hidden" id="edit-space-id" name="id">
            <input type="hidden" id="edit-current-image" name="current_image">

            <!-- Space Name -->
            <div class="form-section">
                <label for="edit-space-name">Parking Space Name</label>
                <input type="text" id="edit-space-name" name="name" required>
            </div>

            <!-- Vehicle Types -->
            <div class="form-section">
                <label>Supported Vehicle Types</label>
                <div class="vehicle-checkboxes">
                    <label><input type="checkbox" id="edit-supports-car" name="supports_car" value="1"> Car</label>
                    <label><input type="checkbox" id="edit-supports-motorcycle" name="supports_motorcycle" value="1"> Motorcycle</label>
                </div>
            </div>

            <!-- Allocations -->
            <div id="edit-allocations-container" class="slot-config" style="display:none;">
                <!-- Car Allocations -->
                <div id="edit-car-allocations" class="slot-section" style="display:none;">
                    <h3>Car Parking Slots</h3>
                    <div class="slot-inputs">
                        <label>General <input type="number" id="edit-car-general" class="allocation-input" value="0" min="0"></label>
                        <label>Faculty <input type="number" id="edit-car-faculty" class="allocation-input" value="0" min="0"></label>
                        <label>Executive <input type="number" id="edit-car-executive" class="allocation-input" value="0" min="0"></label>
                        <label>Guest <input type="number" id="edit-car-guest" class="allocation-input" value="0" min="0"></label>
                    </div>
                    <div class="slot-total">Total Car Slots: <span id="edit-car-total">0</span></div>
                </div>

                <!-- Motorcycle Allocations -->
                <div id="edit-motorcycle-allocations" class="slot-section" style="display:none;">
                    <h3>Motorcycle Parking Slots</h3>
                    <div class="slot-inputs">
                        <label>General <input type="number" id="edit-motorcycle-general" class="allocation-input" value="0" min="0"></label>
                        <label>Faculty <input type="number" id="edit-motorcycle-faculty" class="allocation-input" value="0" min="0"></label>
                        <label>Executive <input type="number" id="edit-motorcycle-executive" class="allocation-input" value="0" min="0"></label>
                        <label>Guest <input type="number" id="edit-motorcycle-guest" class="allocation-input" value="0" min="0"></label>
                    </div>
                    <div class="slot-total">Total Motorcycle Slots: <span id="edit-motorcycle-total">0</span></div>
                </div>
            </div>

            <!-- Upload Image -->
            <div class="form-section upload-section">
                <label for="edit-space-image">Update Image (Optional)</label>
                <input type="file" id="edit-space-image" name="image" accept="image/*">
                <img id="edit-space-preview" alt="Image preview" style="max-width:200px; margin-top:10px; display:none;">
            </div>

            <!-- Buttons -->
            <div class="modal-actions">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <button type="button" id="delete-space-btn" class="btn btn-danger">Delete</button>
                <button type="button" class="btn btn-secondary close-modal">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- === DELETE CONFIRMATION MODAL === -->
<div id="delete-space-modal" class="modal" role="dialog" aria-modal="true">
    <div class="modal-content delete-modal">
        <div class="modal-header delete-header">
            <h2><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h2>
            <button type="button" class="close-btn" aria-label="Close">&times;</button>
        </div>

        <div class="modal-body">
            <div class="warning-icon">
                <i class="fas fa-trash-alt"></i>
            </div>
            
            <p class="warning-text">Are you sure you want to delete this parking space?</p>
            
            <div class="parking-info">
                <strong>Parking Space:</strong> <span id="delete-space-name"></span>
            </div>
            
            <div class="warning-message">
                <i class="fas fa-info-circle"></i>
                <span>This action cannot be undone. All associated data will be permanently deleted.</span>
            </div>
        </div>

        <div class="modal-actions">
            <button type="button" class="btn btn-secondary close-modal">Cancel</button>
            <button type="button" id="confirm-delete-btn" class="btn btn-danger">
                <i class="fas fa-trash"></i> Yes, Delete
            </button>
        </div>

        <input type="hidden" id="delete-space-id" value="">
    </div>
</div>

<!-- === WARNING MODAL === -->
<div id="warningModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 500px;">
        <span class="close" onclick="closeWarningModal()">&times;</span>
        <h2 style="color: #f39c12; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-exclamation-triangle"></i>
            Warning
        </h2>
        <div id="warningContent" style="margin: 20px 0;">
        </div>
        <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
            <button onclick="closeWarningModal()" 
                    style="padding: 10px 20px; background: #95a5a6; color: white; border: none; border-radius: 5px; cursor: pointer;">
                Cancel
            </button>
            <button id="warningProceedBtn" 
                    style="padding: 10px 20px; background: #f39c12; color: white; border: none; border-radius: 5px; cursor: pointer;">
                Proceed Anyway
            </button>
        </div>
    </div>
</div>