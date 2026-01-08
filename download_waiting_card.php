<?php
session_start();
require "connect.php";

if (!isset($_SESSION["user_id"])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION["user_id"];

// Fetch the latest lost ID report for this student
$sql = "SELECT student_name, student_id, id_number, date_lost FROM lost_ids WHERE user_id = ? ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
$conn->close();

if ($result->num_rows === 0) {
    die("No lost ID report found.");
}

$data = $result->fetch_assoc();
$student_name = $data['student_name'];
$student_id = $data['student_id'];
$id_number = $data['id_number'];
$date_lost = $data['date_lost'];

// Generate the Waiting Card PDF
require "vendor/autoload.php"; // Ensure TCPDF is installed

use TCPDF;

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont("Helvetica", "", 12);

// Add university details
$pdf->Cell(0, 10, "Zetech University", 0, 1, "C");
$pdf->Cell(0, 10, "Waiting Card", 0, 1, "C");
$pdf->Ln(10);

// Add student details
$pdf->Cell(0, 10, "Student Name: $student_name", 0, 1);
$pdf->Cell(0, 10, "Student ID: $student_id", 0, 1);
$pdf->Cell(0, 10, "ID Number: $id_number", 0, 1);
$pdf->Cell(0, 10, "Date Lost: $date_lost", 0, 1);
$pdf->Ln(10);

// Output the PDF for download
$pdf->Output("Waiting_Card.pdf", "D"); // Forces download
?>
