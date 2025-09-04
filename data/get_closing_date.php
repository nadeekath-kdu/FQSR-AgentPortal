<?php
if (!isset($_SESSION)) {
    session_start();
}

// Set timezone
date_default_timezone_set('Asia/Colombo'); // Using Sri Lanka timezone since this is for KDU

try {
    include '../config/dbcon.php';
    include '../config/global.php';
    $closing_date = $application_closing_date;


    if (!isset($closing_date)) {
        throw new Exception('Application closing date is not set.');
    }

    $dateTime = new DateTime($closing_date);
    $closing_date = $dateTime->format('Y-m-d');

    header('Content-Type: application/json');

    $response = array(
        'closing_date' => $closing_date
    );

    echo json_encode($response);
} catch (Exception $e) {
    // Handle exceptions
    $errorJson = array(
        'error' => $e->getMessage()
    );

    echo json_encode($errorJson);
}
