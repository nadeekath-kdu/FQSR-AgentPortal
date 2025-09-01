<?php
require_once '../config/dbcon.php';
require_once '../includes/document_functions.php';

header('Content-Type: application/json');

if (!isset($_POST['passportNo'])) {
    echo json_encode(['success' => false, 'message' => 'Missing passport number']);
    exit;
}

$passportNo = $_POST['passportNo'];
$uploadDir = "../uploads/documents/" . $passportNo . "/";

// Function to recursively remove directory
function removeDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }

    if (!is_dir($dir)) {
        return unlink($dir);
    }

    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }

        if (!removeDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }

    return rmdir($dir);
}

// Clear the directory
if (removeDirectory($uploadDir)) {
    // Recreate empty directory
    mkdir($uploadDir, 0777, true);
    echo json_encode(['success' => true, 'message' => 'Documents cleared successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to clear documents']);
}
