<?php
// list_documents.php - Returns a JSON array of all files in a user's document folder, with URLs
header('Content-Type: application/json');
$nic = isset($_GET['nic']) ? trim($_GET['nic']) : '';
if ($nic === '') {
    echo json_encode(array('success' => false, 'error' => 'NIC is required.'));
    exit;
}
$nicSafe = preg_replace('/[^a-zA-Z0-9_-]/', '', $nic);
$dir = __DIR__ . "/../uploads/documents/" . $nicSafe . "/";
$files = array();
if (is_dir($dir)) {
    foreach (scandir($dir) as $file) {
        if ($file !== '.' && $file !== '..' && is_file($dir . $file)) {
            $files[] = array(
                'name' => $file,
                'url' => "../uploads/documents/" . $nicSafe . "/" . rawurlencode($file)
            );
        }
    }
}
echo json_encode(array('success' => true, 'documents' => $files));
