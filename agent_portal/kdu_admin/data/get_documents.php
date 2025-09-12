<?php
require_once '../../../config/dbcon.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $agency_code = isset($_GET['agency_code']) ? $_GET['agency_code'] : '';

    if (empty($agency_code)) {
        echo json_encode(array(
            'success' => false,
            'message' => 'Agency code is required.'
        ));
        exit;
    }

    // Query to fetch documents for the agency_code
    $query = "SELECT document FROM agency WHERE agency_code = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "s", $agency_code);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Split the comma-separated list of files into an array
        $documents = explode(',', $row['document']);
        echo json_encode(array(
            'success' => true,
            'documents' => $documents
        ));
    } else {
        echo json_encode(array(
            'success' => false,
            'message' => 'No documents found for the specified agency code.'
        ));
    }

    mysqli_stmt_close($stmt);
    mysqli_close($con);
} else {
    echo json_encode(array(
        'success' => false,
        'message' => 'Invalid request method.'
    ));
}
