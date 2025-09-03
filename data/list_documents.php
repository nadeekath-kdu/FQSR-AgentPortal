<?php
// list_documents.php - Returns a JSON array of all files in a user's document folder
header('Content-Type: application/json');
$nic = isset($_GET['nic']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['nic']) : '';
$dir = __DIR__ . "/../uploads/documents/" . $nic . "/";
$files = [];
if ($nic && is_dir($dir)) {
    foreach (scandir($dir) as $file) {
        if ($file !== '.' && $file !== '..' && is_file($dir . $file)) {
            $files[] = $file;
        }
    }
}
echo json_encode($files);
