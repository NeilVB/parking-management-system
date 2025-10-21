<!-- qr_modals.php -->

<!-- QR Scanner Modal -->
<div id="scan-vehicle-modal" class="modal-overlay">
    <div class="modal-content qr-modal">
        <span class="close-btn" onclick="closeScanModal()">&times;</span>
        <h2>Scan Vehicle QR Code</h2>

        <div id="qr-video-container" style="display: flex; justify-content: center; align-items: center;">

        </div>

        <div class="scan-controls" style="margin-top: 10px;">
            <button id="start-scan-btn" class="btn btn-primary">Start Scan</button>
            <button id="stop-scan-btn" class="btn btn-secondary" style="display:none;">Stop Scan</button>
        </div>

        <div id="scan-result" style="display:none; margin-top: 15px;">
            <p id="result-text">Processing...</p>
        </div>
    </div>
</div>


<!-- =======================================
     MERGED: Vehicle Info + Parking Selection Modal
======================================= -->
<div class="modal fade" id="vehicleInfoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-car"></i> Vehicle Information & Parking Selection</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <!-- Vehicle Information Section -->
                <div class="vehicle-info-section mb-4">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-info-circle"></i> Vehicle Details</h6>
                    <table class="table table-bordered table-sm">
                        <tbody>
                            <tr>
                                <th style="width: 150px;">Plate Number</th>
                                <td id="v-plate"></td>
                                <th style="width: 150px;">Owner</th>
                                <td id="v-owner"></td>
                            </tr>
                            <tr>
                                <th>User Type</th>
                                <td id="v-user-type"></td>
                                <th>Vehicle Type</th>
                                <td id="v-type"></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td id="v-status" colspan="3"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Parking Selection Section -->
                <div class="parking-selection-section">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-parking"></i> Select Parking Space</h6>
                    
                    <!-- Search and Filter Bar -->
                    <div class="filter-bar" style="display: flex; gap: 15px; margin-bottom: 20px; align-items: center; flex-wrap: wrap;">
                        <!-- Search Input -->
                        <div class="search-container" style="flex: 1; min-width: 250px;">
                            <input type="text" id="modal-space-search" placeholder="Search parking spaces..." 
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                        </div>
                        
                        <!-- Filter Dropdown -->
                        <div class="filter-container">
                            <select id="modal-type-filter" class="filter-select" 
                                    style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; cursor: pointer; background: white;">
                                <option value="all">All Types</option>
                                <option value="general">General</option>
                                <option value="faculty">Faculty</option>
                                <option value="executive">Executive</option>
                                <option value="guest">Guest</option>
                            </select>
                        </div>
                        
                        <!-- Vehicle Type Filter -->
                        <div class="filter-container">
                            <select id="modal-vehicle-filter" class="filter-select" 
                                    style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; cursor: pointer; background: white;">
                                <option value="all">All Vehicles</option>
                                <option value="car">Car Only</option>
                                <option value="motorcycle">Motorcycle Only</option>
                            </select>
                        </div>
                        
                        <!-- Count Display -->
                        <div class="count-display">
                            <span id="modal-spaces-count" style="color: #666; font-size: 14px;">Loading...</span>
                        </div>
                    </div>

                    <!-- Parking Spaces Container -->
                    <div id="modal-parking-container" class="parking-container" style="max-height: 400px; overflow-y: auto;">
                        <?php
                        require_once __DIR__ . '/../controller/config.php';
                        require_once __DIR__ . '/../models/ParkingSpace.php';

                        $parkingModel = new ParkingSpace($conn);
                        $query = "SELECT * FROM parking_spaces ORDER BY id DESC";
                        $result = $conn->query($query);

                        while ($row = $result->fetch_assoc()):
                            // Fix image path
                            $imagePath = !empty($row['image_path']) ? '../' . ltrim($row['image_path'], '/.') : '../assets/img/default_parking.jpg';

                            // Get allocations for this parking space
                            $allocations = $parkingModel->getAllocations($row['id']);

                            // Compute slots and user types
                            $carSlots = 0;
                            $motorcycleSlots = 0;
                            $userTypesSet = [];
                            $allocationDetails = [];

                            foreach ($allocations as $alloc) {
                                if ($alloc['vehicle_type_id'] == 1) {
                                    $carSlots += (int)$alloc['allocated_slots'];
                                } else {
                                    $motorcycleSlots += (int)$alloc['allocated_slots'];
                                }

                                if ((int)$alloc['allocated_slots'] > 0) {
                                    $ut = strtolower(trim($alloc['user_type']));
                                    if (strpos($ut, 'student') !== false || strpos($ut, 'visitor') !== false) {
                                        $userTypesSet['general'] = true;
                                    } elseif (strpos($ut, 'faculty') !== false) {
                                        $userTypesSet['faculty'] = true;
                                    } elseif (strpos($ut, 'executive') !== false) {
                                        $userTypesSet['executive'] = true;
                                    } elseif (strpos($ut, 'guest') !== false) {
                                        $userTypesSet['guest'] = true;
                                    }
                                    $allocationDetails[] = $alloc['user_type'] . ': ' . (int)$alloc['allocated_slots'];
                                }
                            }

                            // Prepare data attributes
                            $userTypes = !empty($userTypesSet) ? implode(',', array_keys($userTypesSet)) : '';
                            $vehicles = [];
                            if ($row['supports_car']) $vehicles[] = 'car';
                            if ($row['supports_motorcycle']) $vehicles[] = 'motorcycle';
                            $vehiclesAttr = implode(',', $vehicles);

                            // Calculate totals
                            $totalSlots = $carSlots + $motorcycleSlots;
                            $parkedCount = (int)$row['parked_car_count'] + (int)$row['parked_motorcycle_count'];
                            $available = $totalSlots - $parkedCount;
                            $status = $available > 0 ? 'Available' : 'Full';

                            // image path safe output
                            $imgSafe = htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8');
                            $nameSafe = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
                        ?>
                            <div class="parking-card modal-parking-card"
                                data-id="<?php echo (int)$row['id']; ?>"
                                data-name="<?php echo $nameSafe; ?>"
                                data-user-types="<?php echo htmlspecialchars($userTypes, ENT_QUOTES, 'UTF-8'); ?>"
                                data-vehicles="<?php echo htmlspecialchars($vehiclesAttr, ENT_QUOTES, 'UTF-8'); ?>"
                                data-available="<?php echo $available; ?>"
                                style="background-image: url('<?php echo $imgSafe; ?>');">
                                <div class="overlay">
                                    <h3><?php echo $nameSafe; ?></h3>

                                    <p><strong>Vehicle Types:</strong>
                                        <?php
                                        $types = [];
                                        if ($row['supports_car']) $types[] = 'Car (' . $carSlots . ' slots)';
                                        if ($row['supports_motorcycle']) $types[] = 'Motorcycle (' . $motorcycleSlots . ' slots)';
                                        echo implode(', ', $types);
                                        ?>
                                    </p>

                                    <p><strong>User Type Allocations:</strong><br>
                                        <?php
                                        if (!empty($allocationDetails)) {
                                            echo '<small>' . implode('<br>', array_slice($allocationDetails, 0, 4)) . '</small>';
                                            if (count($allocationDetails) > 4) {
                                                echo '<br><small>+' . (count($allocationDetails) - 4) . ' more...</small>';
                                            }
                                        } else {
                                            echo '<small>No allocations</small>';
                                        }
                                        ?>
                                    </p>

                                    <p><strong>Occupancy:</strong>
                                        <?php echo $parkedCount; ?> / <?php echo $totalSlots; ?>
                                        <span class="status-badge <?php echo strtolower($status); ?>">
                                            <?php echo htmlspecialchars($status); ?>
                                        </span>
                                    </p>

                                    <?php if ($available > 0): ?>
                                    <button class="btn btn-success select-parking-btn"
                                        data-id="<?php echo (int)$row['id']; ?>"
                                        data-name="<?php echo $nameSafe; ?>">
                                        <i class="fas fa-check-circle"></i> Select This Space
                                    </button>
                                    <?php else: ?>
                                    <button class="btn btn-secondary" disabled>
                                        <i class="fas fa-ban"></i> Full
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php
                        endwhile;
                        ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- =============================
     ASSIGNMENT SUCCESS MODAL
============================= -->
<div id="assignment-success-modal" class="modal" role="dialog" aria-modal="true" style="display: none;">
    <div class="modal-content success-modal">
        <div class="modal-header success-header">
            <h2><i class="fas fa-check-circle"></i> Assignment Successful</h2>
            <button type="button" class="close-btn" aria-label="Close">&times;</button>
        </div>

        <div class="modal-body">
            <div class="success-icon">
                <i class="fas fa-car-side"></i> <i class="fas fa-arrow-right"></i> <i class="fas fa-parking"></i>
            </div>
            
            <h3>Vehicle Assigned to Parking Space</h3>
            
            <!-- Assignment Summary -->
            <div class="assignment-summary-card">
                <div class="info-row">
                    <strong>Plate Number:</strong>
                    <span id="success-assigned-plate">-</span>
                </div>
                <div class="info-row">
                    <strong>Parking Space:</strong>
                    <span id="success-parking-name">-</span>
                </div>
                <div class="info-row">
                    <strong>Assigned At:</strong>
                    <span id="success-assigned-time">-</span>
                </div>
            </div>
            
            <div class="auto-refresh-notice">
                <i class="fas fa-sync-alt fa-spin"></i>
                <span>Page will refresh automatically...</span>
            </div>
        </div>

        <div class="modal-actions">
            <button type="button" class="btn btn-primary close-modal">
                <i class="fas fa-check"></i> OK
            </button>
        </div>
    </div>
</div>

<!-- =============================
     ASSIGNMENT ERROR MODAL
============================= -->
<div id="assignment-error-modal" class="modal" role="dialog" aria-modal="true" style="display: none;">
    <div class="modal-content error-modal">
        <div class="modal-header error-header">
            <h2><i class="fas fa-exclamation-triangle"></i> Assignment Failed</h2>
            <button type="button" class="close-btn" aria-label="Close">&times;</button>
        </div>

        <div class="modal-body">
            <div class="error-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            
            <h3>Could Not Assign Parking Space</h3>
            
            <!-- Error Message -->
            <div class="error-message-card">
                <p id="error-assignment-message">An error occurred</p>
            </div>
            
            <div class="error-help">
                <i class="fas fa-info-circle"></i>
                <span>Please try again or contact support if the problem persists.</span>
            </div>
        </div>

        <div class="modal-actions">
            <button type="button" class="btn btn-secondary close-modal">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button type="button" class="btn btn-primary" id="retry-assignment-btn">
                <i class="fas fa-redo"></i> Try Again
            </button>
        </div>
    </div>
</div>

<!-- =============================
     EXIT VEHICLE CONFIRMATION MODAL
============================= -->
<div id="exit-vehicle-modal" class="modal" role="dialog" aria-modal="true">
    <div class="modal-content exit-modal">
        <div class="modal-header exit-header">
            <h2><i class="fas fa-sign-out-alt"></i> Confirm Vehicle Exit</h2>
            <button type="button" class="close-btn" aria-label="Close">&times;</button>
        </div>

        <div class="modal-body">
            <div class="exit-icon">
                <i class="fas fa-car"></i>
            </div>
            
            <p class="exit-text">Confirm vehicle exit from parking?</p>
            
            <!-- Vehicle Information -->
            <div class="vehicle-info-card">
                <div class="info-row">
                    <strong>Vehicle Owner:</strong>
                    <span id="exit-vehicle-owner"></span>
                </div>
                <div class="info-row">
                    <strong>Plate Number:</strong>
                    <span id="exit-plate-number"></span>
                </div>
                <div class="info-row">
                    <strong>Vehicle Type:</strong>
                    <span id="exit-vehicle-type"></span>
                </div>
                <div class="info-row">
                    <strong>Parking Space:</strong>
                    <span id="exit-parking-space"></span>
                </div>
                <div class="info-row">
                    <strong>Entry Time:</strong>
                    <span id="exit-entry-time"></span>
                </div>
                <div class="info-row highlight">
                    <strong>Duration:</strong>
                    <span id="exit-duration"></span>
                </div>
            </div>
            
            <div class="info-message">
                <i class="fas fa-info-circle"></i>
                <span>This will mark the vehicle as exited and free up the parking slot.</span>
            </div>
        </div>

        <div class="modal-actions">
            <button type="button" class="btn btn-secondary close-modal">Cancel</button>
            <button type="button" id="confirm-exit-btn" class="btn btn-success">
                <i class="fas fa-check-circle"></i> Confirm Exit
            </button>
        </div>

        <input type="hidden" id="exit-vehicle-id" value="">
        <input type="hidden" id="exit-parking-transaction-id" value="">
    </div>
</div>

<!-- =============================
     EXIT SUCCESS MODAL
============================= -->
<div id="exit-success-modal" class="modal" role="dialog" aria-modal="true">
    <div class="modal-content success-modal">
        <div class="modal-header success-header">
            <h2><i class="fas fa-check-circle"></i> Exit Successful</h2>
            <button type="button" class="close-btn" aria-label="Close">&times;</button>
        </div>

        <div class="modal-body">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <h3>Vehicle Successfully Exited</h3>
            
            <!-- Exit Summary -->
            <div class="exit-summary-card">
                <div class="info-row">
                    <strong>Plate Number:</strong>
                    <span id="success-plate-number"></span>
                </div>
                <div class="info-row">
                    <strong>Exit Time:</strong>
                    <span id="success-exit-time"></span>
                </div>
                <div class="info-row highlight">
                    <strong>Total Duration:</strong>
                    <span id="success-total-duration"></span>
                </div>
            </div>
            
            <p class="success-message">The parking slot has been freed and is now available.</p>
        </div>

        <div class="modal-actions">
            <button type="button" class="btn btn-primary close-modal">
                <i class="fas fa-qrcode"></i> Scan Next Vehicle
            </button>
        </div>
    </div>
</div>