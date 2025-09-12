<?php
session_start();
//include '../../config/dbcon.php';

if (isset($_SESSION['user_role'])) {
    $user = $_SESSION['user_role'];
} else {
    $response = array('error' => 'User role not set in session');
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$response = array('user' => $user); 

header('Content-Type: application/json');
echo json_encode($response);
?>
