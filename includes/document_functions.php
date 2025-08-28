<?php
function getUploadedDocuments($passportNo) {
    $documentsDir = "../uploads/documents/" . $passportNo . "/";
    $documents = array();
    
    if (file_exists($documentsDir)) {
        $files = scandir($documentsDir);
        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                $documents[] = array(
                    'name' => $file,
                    'path' => $documentsDir . $file,
                    'size' => filesize($documentsDir . $file),
                    'type' => pathinfo($documentsDir . $file, PATHINFO_EXTENSION)
                );
            }
        }
    }
    
    return $documents;
}

function formatFileSize($bytes) {
    if ($bytes === 0) return '0 Bytes';
    $k = 1024;
    $sizes = array('Bytes', 'KB', 'MB', 'GB');
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}

function getFileIcon($extension) {
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
?>
