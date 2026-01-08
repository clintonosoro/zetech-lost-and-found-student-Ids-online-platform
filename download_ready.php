<?php
$file = __DIR__ . '/Waiting_Card.pdf';
if (file_exists($file)) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="Waiting_Card.pdf"');
    readfile($file);
    unlink($file); // Delete file after download
    exit;
} else {
    die("File not found.");
}
?>
