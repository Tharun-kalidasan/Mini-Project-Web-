<?php
require_once '../config/database.php';
require_once '../fpdf/fpdf.php';

$database = new Database();
$conn = $database->getConnection();

$start_date = $_GET['start_date'] ?? date('Y-m-d');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

$query = "SELECT s.reg_no, s.name, s.block, s.room_number, 
          mp.dining_mess, mp.mess_type, mp.meal_type, 
          mp.food_item_suggestion, mp.submission_date 
          FROM students s 
          JOIN mess_preferences mp ON s.id = mp.student_id 
          WHERE mp.submission_date BETWEEN :start_date AND :end_date";

$stmt = $conn->prepare($query);
$stmt->bindParam(':start_date', $start_date);
$stmt->bindParam(':end_date', $end_date);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pdf = new FPDF();
$pdf->AddPage('L');
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Mess Menu Preferences Report', 0, 1, 'C');
$pdf->Ln(5);

// Headers
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(25, 10, 'Reg No', 1);
$pdf->Cell(35, 10, 'Name', 1);
$pdf->Cell(15, 10, 'Block', 1);
$pdf->Cell(20, 10, 'Room', 1);
$pdf->Cell(30, 10, 'Dining Mess', 1);
$pdf->Cell(25, 10, 'Mess Type', 1);
$pdf->Cell(25, 10, 'Meal Type', 1);
$pdf->Cell(70, 10, 'Food Suggestion', 1);
$pdf->Cell(25, 10, 'Date', 1);
$pdf->Ln();

// Data rows
$pdf->SetFont('Arial', '', 9);
foreach($results as $row) {
    $pdf->Cell(25, 10, $row['reg_no'], 1);
    $pdf->Cell(35, 10, $row['name'], 1);
    $pdf->Cell(15, 10, $row['block'], 1);
    $pdf->Cell(20, 10, $row['room_number'], 1);
    $pdf->Cell(30, 10, $row['dining_mess'], 1);
    $pdf->Cell(25, 10, $row['mess_type'], 1);
    $pdf->Cell(25, 10, $row['meal_type'], 1);
    $pdf->Cell(70, 10, $row['food_item_suggestion'], 1);
    $pdf->Cell(25, 10, $row['submission_date'], 1);
    $pdf->Ln();
}

$pdf->Output('D', 'mess_preferences.pdf');
?>