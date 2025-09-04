<?php
// Basic version info
$version_info = array(
    'php_version' => PHP_VERSION,
    'mysql_version' => function_exists('mysqli_get_client_info') ? mysqli_get_client_info() : 'MySQL not available',
    'system' => php_uname(),
    'extensions' => get_loaded_extensions()
);

// If it's an AJAX request, return JSON
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode($version_info);
    exit;
}

// Otherwise show formatted info
echo '<h2>PHP Environment Information</h2>';
echo '<pre>';
echo 'PHP Version: ' . PHP_VERSION . "\n";
echo 'MySQL Version: ' . (function_exists('mysqli_get_client_info') ? mysqli_get_client_info() : 'MySQL not available') . "\n";
echo 'System: ' . php_uname() . "\n";
echo "\nLoaded Extensions:\n";
echo implode(", ", get_loaded_extensions());
echo '</pre>';

// Uncomment the line below to see full phpinfo
// phpinfo();
