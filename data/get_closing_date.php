<?php
if (!isset($_SESSION)) {
    session_start();
}

try {
    include '../config/dbcon.php';
    include '../config/global.php';
    $closing_date = $application_closing_date;


    if (!isset($closing_date)) {
        throw new Exception('Application closing date is not set.');
    }

    $dateTime = new DateTime($closing_date);
    $closing_date = $dateTime->format('Y-m-d');

    /* $day = $dateTime->format('j'); 
    $month = $dateTime->format('M'); 
    $year = $dateTime->format('Y'); 
    
    if ($day % 10 == 1 && $day != 11) {
        $daySuffix = 'st';
    } elseif ($day % 10 == 2 && $day != 12) {
        $daySuffix = 'nd';
    } elseif ($day % 10 == 3 && $day != 13) {
        $daySuffix = 'rd';
    } else {
        $daySuffix = 'th';
    }
 */

    //$closing_date = $day . '<sup>' . htmlspecialchars($daySuffix) . '</sup> ' . $month . ' ' . $year;


    $json = array(
        'closing_date' => $closing_date
    );

    echo json_encode($json);
} catch (Exception $e) {
    // Handle exceptions
    $errorJson = array(
        'error' => $e->getMessage()
    );

    echo json_encode($errorJson);
}
