<?php
// Show all errors
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log', '../error.log');

// Try/catch for older PHP versions
try {
    require_once('../config/dbcon.php');
} catch (Exception $e) {
    die('Could not load dbcon.php');
}

try {
    require_once('../config/global.php');
} catch (Exception $e) {
    die('Could not load global.php');
}

// Set headers for older PHP versions
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Simple response array
$response = array();

// Check database connection first
if (!isset($con_fqsr)) {
    $response['error'] = 'Database connection not available';
    echo json_encode($response);
    exit;
}

// Try to use existing academic_year
if (isset($academic_year) && !empty($academic_year)) {
    $response['success'] = true;
    $response['academic_year'] = $academic_year;
    echo json_encode($response);
    exit;
}

// If no academic_year, query database
$sql = "SELECT year FROM intake ORDER BY id DESC LIMIT 1";
$result = @mysqli_query($con_fqsr, $sql);

if ($result) {
    $row = @mysqli_fetch_assoc($result);
    if ($row && isset($row['year'])) {
        $response['success'] = true;
        $response['academic_year'] = $row['year'];
    } else {
        $response['error'] = 'No academic year found in database';
    }
} else {
    $response['error'] = 'Could not query database';
    if (function_exists('mysqli_error')) {
        $response['debug'] = mysqli_error($con_fqsr);
    }
}

echo json_encode($response);
