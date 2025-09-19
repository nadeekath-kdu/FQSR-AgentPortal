<?php
// Securely serve/download a single applicant document
$root = realpath(__DIR__ . '/../uploads/documents');
if ($root === false) {
    http_response_code(500);
    exit('Uploads directory not found.');
}

$nic = isset($_GET['nic']) ? trim($_GET['nic']) : '';
$file = isset($_GET['file']) ? $_GET['file'] : '';
$download = isset($_GET['download']) ? (int)$_GET['download'] : 0;

if ($nic === '') {
    http_response_code(400);
    exit('Invalid NIC.');
}
// prevent directory traversal in file name
if ($file === '' || strpos($file, '..') !== false || strpos($file, '/') !== false || strpos($file, '\\') !== false) {
    http_response_code(400);
    exit('Invalid file.');
}

// Try direct path then fall back to case-insensitive/normalized match
$candidate = realpath($root . DIRECTORY_SEPARATOR . $nic);
$dir = ($candidate && strpos($candidate, $root) === 0 && is_dir($candidate)) ? $candidate : '';
if ($dir === '') {
    $targetNorm = strtolower(preg_replace('/[^a-z0-9]/i', '', $nic));
    foreach (glob($root . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) as $cand) {
        $base = basename($cand);
        if (strcasecmp($base, $nic) === 0) {
            $dir = realpath($cand);
            break;
        }
        $candNorm = strtolower(preg_replace('/[^a-z0-9]/i', '', $base));
        if ($candNorm === $targetNorm) {
            $dir = realpath($cand);
            break;
        }
    }
}
if ($dir === '' || strpos($dir, $root) !== 0) {
    http_response_code(404);
    exit('Folder not found.');
}

$path = realpath($dir . DIRECTORY_SEPARATOR . $file);
if ($path === false || strpos($path, $dir) !== 0 || !is_file($path)) {
    http_response_code(404);
    exit('File not found.');
}

$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
$mimeMap = array(
    'pdf' => 'application/pdf',
    'png' => 'image/png',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'gif' => 'image/gif',
    'webp' => 'image/webp',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'xls' => 'application/vnd.ms-excel',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'txt' => 'text/plain'
);
$mime = isset($mimeMap[$ext]) ? $mimeMap[$ext] : 'application/octet-stream';

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($path));
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, max-age=60');
if ($download) {
    header('Content-Disposition: attachment; filename="' . basename($path) . '"');
} else {
    header('Content-Disposition: inline; filename="' . basename($path) . '"');
}

// Stream file
readfile($path);
exit;
