document.addEventListener('DOMContentLoaded', function() {
    const messMenuForm = document.getElementById('messMenuForm');
    
    if (messMenuForm) {
        messMenuForm.addEventListener('submit', function(event) {
            let isValid = true;
            
            // Registration number validation (format: 2 digits, 2 letters, 4 digits)
            const regNo = document.getElementById('reg_no').value;
            if (!/^\d{2}[A-Z]{3}\d{4}$/.test(regNo)) {
                alert('Registration number must be in the format: 2 digits, 2 letters, 4 digits (e.g., 21BC1234)');
                isValid = false;
            }
            
            // Name validation (letters and spaces)
            const name = document.getElementById('name').value;
            if (!/^[a-zA-Z\s]+$/.test(name)) {
                alert('Name should contain only letters and spaces');
                isValid = false;
            }

            // Block validation
            const block = document.getElementById('block').value;
            if (!block.trim()) {
                alert('Please enter a valid block');
                isValid = false;
            }

            // Room number validation (digits only)
            const roomNumber = document.getElementById('room_number').value;
            if (!/^\d+$/.test(roomNumber)) {
                alert('Room number should contain only digits');
                isValid = false;
            }

            // Dining mess validation
            const diningMess = document.getElementById('dining_mess').value;
            if (!diningMess.trim()) {
                alert('Please enter a valid dining mess name');
                isValid = false;
            }

            // Mess type validation
            const messType = document.getElementById('mess_type').value;
            if (!messType) {
                alert('Please select a mess type');
                isValid = false;
            }

            // Meal type validation
            const mealType = document.getElementById('meal_type').value;
            if (!mealType) {
                alert('Please select a meal type');
                isValid = false;
            }
            
            // Date validation
            const submissionDate = document.getElementById('submission_date').value;
            if (!submissionDate) {
                alert('Please select a date');
                isValid = false;
            } else {
                const today = new Date();
                const selectedDate = new Date(submissionDate);
                if (selectedDate < today) {
                    alert('Please select today or a future date');
                    isValid = false;
                }
            }
            
            if (!isValid) {
                event.preventDefault();
            }
        });
    }
});