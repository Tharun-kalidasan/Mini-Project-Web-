<?php include 'includes/header.php'; ?>


<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card form-container">
            <h2 class="text-center mb-4">Mess Menu Preference Form</h2>
            
            <form id="messMenuForm" action="Submit_form.php" method="post">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo bin2hex(random_bytes(32)); ?>">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="reg_no" class="form-label">Registration Number</label>
                        <input type="text" class="form-control" id="reg_no" name="reg_no" 
                               pattern="\d{2}[A-Z]{3}\d{4}" aria-label="Registration Number" 
                               title="Enter registration number in format: 2 digits, 3 letters, 4 digits (e.g., 21BCE1234)" required>
                    </div>
                    <div class="col-md-6">
                        <label for="name" class="form-label">Name of the Student</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               pattern="[A-Za-z\s]+" aria-label="Name of the Student" 
                               title="Enter a valid name (letters and spaces only)" required>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="block" class="form-label">Block</label>
                        <input type="text" class="form-control" id="block" name="block" 
                               aria-label="Block" required>
                    </div>
                    <div class="col-md-6">
                        <label for="room_number" class="form-label">Room Number</label>
                        <input type="text" class="form-control" id="room_number" name="room_number" 
                               pattern="\d+" aria-label="Room Number" 
                               title="Enter a valid room number (digits only)" required>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="dining_mess" class="form-label">Name of the Dining Mess</label>
                        <input type="text" class="form-control" id="dining_mess" name="dining_mess" 
                               aria-label="Name of the Dining Mess" required>
                    </div>
                    <div class="col-md-6">
                        <label for="mess_type" class="form-label">Type of Mess</label>
                        <select class="form-select" id="mess_type" name="mess_type" 
                                aria-label="Type of Mess" required>
                            <option value="" disabled selected>Select Mess Type</option>
                            <option value="Veg">Veg</option>
                            <option value="Non-Veg">Non-Veg</option>
                            <option value="Special">Special</option>
                            <option value="Night mess">Night Mess</option>
                        </select>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="meal_type" class="form-label">Type of Meal</label>
                        <select class="form-select" id="meal_type" name="meal_type" 
                                aria-label="Type of Meal" required>
                            <option value="" disabled selected>Select Meal Type</option>
                            <option value="Breakfast">Breakfast</option>
                            <option value="Lunch">Lunch</option>
                            <option value="Snacks">Snacks</option>
                            <option value="Dinner">Dinner</option>
                            <option value="Night mess">Night Mess</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="submission_date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="submission_date" name="submission_date" 
                               min="<?php echo date('Y-m-d'); ?>" aria-label="Submission Date" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="food_item_suggestion" class="form-label">Food Item Suggestion</label>
                    <textarea class="form-control" id="food_item_suggestion" name="food_item_suggestion" 
                              rows="3" aria-label="Food Item Suggestion"></textarea>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="feasibility" name="feasibility" 
                           value="1" aria-label="Feasibility for Mass Production">
                    <label class="form-check-label" for="feasibility">Feasibility for Mass Production</label>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Submit Preference</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add JavaScript file link before closing body -->
<script src="Script.js"></script>

<?php include 'includes/footer.php'; ?>