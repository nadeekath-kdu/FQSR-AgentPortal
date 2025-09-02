<?php
require_once '../config/dbcon.php';

function getUploadedDocuments($passportNo)
{
    $documentsDir = "../uploads/documents/" . $passportNo . "/";
    $documents = array();

    if (file_exists($documentsDir)) {
        $files = scandir($documentsDir);
        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                $type = getDocumentType($file);
                $documents[] = array(
                    'file_name' => $file,
                    'file_path' => $documentsDir . $file,
                    'document_type' => $type,
                    /* 'upload_date' => filemtime($documentsDir . $file) */
                );
            }
        }
    }

    return $documents;
}

function getDocumentType($filename)
{
    // Extract document type from filename pattern
    // Assuming filenames follow a pattern like: passport_123.pdf, transcript_456.pdf etc.
    $parts = explode('_', strtolower($filename));
    return $parts[0];
}

function getDocumentTypeLabel($type)
{
    $types = array(
        'passport' => 'Passport',
        'photo' => 'Photograph',
        'transcript' => 'Academic Transcript',
        'degree' => 'Degree Certificate',
        'birth' => 'Birth Certificate',
        'recommendation' => 'Recommendation Letter',
        'cv' => 'Curriculum Vitae',
        'other' => 'Other Document'
    );

    return isset($types[$type]) ? $types[$type] : ucfirst(str_replace('_', ' ', $type));
}

function recreateDocumentFolder($passportNo)
{
    $documentsDir = "../uploads/documents/" . $passportNo . "/";

    // First backup existing files if directory exists
    $existingFiles = array();
    if (file_exists($documentsDir)) {
        $files = scandir($documentsDir);
        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                $existingFiles[$file] = file_get_contents($documentsDir . $file);
            }
        }

        // Remove existing directory and all its contents
        array_map('unlink', glob($documentsDir . "*.*"));
        rmdir($documentsDir);
    }

    // Create fresh directory
    mkdir($documentsDir, 0777, true);

    // Return backup of files if needed
    return $existingFiles;
}

function removeDocument($passportNo, $filename)
{
    $documentsDir = "../uploads/documents/" . $passportNo . "/";
    $filePath = $documentsDir . basename($filename);

    // Verify file is within the correct directory
    if (strpos(realpath($filePath), realpath($documentsDir)) === 0 && file_exists($filePath)) {
        return unlink($filePath);
    }
    return false;
}

function formatFileSize($bytes)
{
    if ($bytes === 0) return '0 Bytes';
    $k = 1024;
    $sizes = array('Bytes', 'KB', 'MB', 'GB');
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}

function getFileIcon($extension)
{
    $icons = array(
        'pdf' => '<i class="fa fa-file-pdf-o text-danger"></i>',
        'docx' => '<i class="fa fa-file-word-o text-primary"></i>',
        'jpg' => '<i class="fa fa-file-image-o text-success"></i>',
        'jpeg' => '<i class="fa fa-file-image-o text-success"></i>',
        'png' => '<i class="fa fa-file-image-o text-success"></i>',
        'gif' => '<i class="fa fa-file-image-o text-success"></i>',
    );
    return isset($icons[strtolower($extension)]) ? $icons[strtolower($extension)] : '<i class="fa fa-file-o"></i>';
}
