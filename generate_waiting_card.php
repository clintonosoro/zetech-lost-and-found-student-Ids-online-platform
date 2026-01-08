<?php
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

// Include FPDF library
require_once __DIR__ . '/fpdf186/fpdf.php';  // Ensure correct path

// Database connection
$conn = new mysqli('localhost', 'root', '', 'lostfound_zetech');

// Check the connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Fetch student lost ID data from `lost_ids` table
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM lost_ids WHERE user_id = '$user_id' ORDER BY date_reported DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $student_name = $row['student_name'];
    $student_id = $row['student_id'];
    $id_number = $row['id_number'];
    $date_reported = isset($row['reported_at']) ? date("d M Y", strtotime($row['reported_at'])) : 'Not Available';

    $status = isset($row['status']) ? $row['status'] : 'Pending';
} else {
    die("Error: No lost ID data found for user.");
}

// Close database connection
$conn->close();

// Initialize PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// ✅ FIX: Ensure the university logo exists
$logo = __DIR__ . '/zetechlogo.jpg'; 
if (!file_exists($logo)) {
    die("Error: University logo not found. Please upload it.");
}
$pdf->Image($logo, 75, 10, 60);
$pdf->Ln(40);

// Title
$pdf->SetFont('Arial', '', 12);
$pdf->Ln(10);

// Student Info
$pdf->Cell(50, 10, 'Name:', 0, 0);
$pdf->Cell(100, 10, $student_name, 0, 1);
$pdf->Cell(50, 10, 'Student ID:', 0, 0);
$pdf->Cell(100, 10, $student_id, 0, 1);
$pdf->Cell(50, 10, 'ID Number:', 0, 0);
$pdf->Cell(100, 10, $id_number, 0, 1);
$pdf->Cell(50, 10, 'Date Reported:', 0, 0);
$pdf->Cell(100, 10, $date_reported, 0, 1);
$pdf->Cell(50, 10, 'Status:', 0, 0);
$pdf->Cell(100, 10, $status, 0, 1);
$pdf->Ln(10);
$pdf->MultiCell(0, 10, "Use this card to access university properties until your ID is recovered.", 0, 'C');

// ✅ FIX 1: Create 'generated_pdfs' folder if it doesn't exist
$folder = __DIR__ . '/generated_pdfs';
if (!is_dir($folder)) {
    mkdir($folder, 0777, true);
}

// ✅ FIX 2: Remove invalid characters from filename
$clean_student_id = preg_replace('/[^A-Za-z0-9_-]/', '_', $student_id); // Remove slashes and special characters
$file_name = 'Temporary_Access_Card_' . $clean_student_id . '.pdf';
$file_path = $folder . '/' . $file_name;

// ✅ FIX 3: Ensure correct file saving path
$pdf->Output('F', $file_path);

// ✅ FIX 4: Correct download link format
$download_link = 'generated_pdfs/' . $file_name;

// ✅ FIX 5: Use relative URL for the download link (works in localhost)
echo 'Your PDF has been generated. <a href="' . $download_link . '" target="_blank">Click here to download the Temporary Access Card</a>';
?>
