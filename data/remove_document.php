<?php
require_once '../config/dbcon.php';
require_once '../includes/document_functions.php';

header('Content-Type: application/json');

if (!isset($_POST['filePath']) || !isset($_POST['passportNo'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$filePath = $_POST['filePath'];
$passportNo = $_POST['passportNo'];

// Extract filename from path
$filename = basename($filePath);

// Remove file using the new function
if (removeDocument($passportNo, $filename)) {
    echo json_encode(['success' => true, 'message' => 'Document removed successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to remove document']);
}
