<?php
require_once '../config/dbcon.php';
require_once '../includes/document_functions.php';

header('Content-Type: application/json');

if (!isset($_FILES['file']) || !isset($_POST['documentType']) || !isset($_POST['passportNo'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$file = $_FILES['file'];
$documentType = $_POST['documentType'];
$passportNo = $_POST['passportNo'];

// Create directory if it doesn't exist
$uploadDir = "../uploads/documents/" . $passportNo . "/";
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Generate unique filename
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = $documentType . '_' . time() . '.' . $extension;
$targetPath = $uploadDir . $filename;

// Check file type
$allowedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
if (!in_array(strtolower($extension), $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type']);
    exit;
}

// Upload file
if (move_uploaded_file($file['tmp_name'], $targetPath)) {
    echo json_encode(['success' => true, 'message' => 'File uploaded successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Upload failed']);
}
