<?php
session_start();
include '../../../config/dbcon.php';

// Simple query to get total count of all agencies
$sql = "SELECT COUNT(*) as total FROM agency";
$result = $con->query($sql);

$response = array();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $response = array(
        'total' => (int)$row['total'],
        'success' => true
    );
} else {
    $response = array(
        'total' => 0,
        'success' => false,
        'error' => 'Failed to get total count'
    );
}

header('Content-Type: application/json');
echo json_encode($response);

$con->close();
