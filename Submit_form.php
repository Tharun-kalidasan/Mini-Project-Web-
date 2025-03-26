<?php
require_once 'config/database.php';

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $conn->beginTransaction();

        // Validate registration number
        if (!preg_match('/^\d{2}[A-Z]{3}\d{4}$/', $_POST['reg_no'])) {
            throw new Exception('Invalid registration number format.');
        }

        // Insert new student
        $insert_student = $conn->prepare("INSERT INTO students (reg_no, name, block, room_number) VALUES (:reg_no, :name, :block, :room_number)");
        $insert_student->bindParam(':reg_no', $_POST['reg_no']);
        $insert_student->bindParam(':name', $_POST['name']);
        $insert_student->bindParam(':block', $_POST['block']);
        $insert_student->bindParam(':room_number', $_POST['room_number']);
        $insert_student->execute();
        
        $student_id = $conn->lastInsertId();

        // Insert mess preference
        $insert_preference = $conn->prepare("INSERT INTO mess_preferences (student_id, dining_mess, mess_type, meal_type, food_item_suggestion, feasibility_for_mass_production, submission_date) VALUES (:student_id, :dining_mess, :mess_type, :meal_type, :food_item_suggestion, :feasibility, :submission_date)");
        
        $feasibility = isset($_POST['feasibility']) ? 1 : 0;
        $insert_preference->bindParam(':student_id', $student_id);
        $insert_preference->bindParam(':dining_mess', $_POST['dining_mess']);
        $insert_preference->bindParam(':mess_type', $_POST['mess_type']);
        $insert_preference->bindParam(':meal_type', $_POST['meal_type']);
        $insert_preference->bindParam(':food_item_suggestion', $_POST['food_item_suggestion']);
        $insert_preference->bindParam(':feasibility', $feasibility);
        $insert_preference->bindParam(':submission_date', $_POST['submission_date']);
        $insert_preference->execute();

        $conn->commit();
        header("Location: index.php?success=1");
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        header("Location: index.php?error=" . urlencode($e->getMessage()));
        exit();
    }
}
?>