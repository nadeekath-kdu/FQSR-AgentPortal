<?php
session_start();
header('Content-Type: application/json');

// Example user authentication check
if (!isset($_SESSION['loggedin']) || !isset($_SESSION['username'])) {

    $response = array('success' => false, 'message' => 'Unauthorized access');
    echo json_encode($response);
    exit;
}

require_once '../../config/dbcon.php';

$userId = $_SESSION['username'];
$currentPassword = $_POST['currentPassword'];
$newPassword = $_POST['newPassword'];


$query = "SELECT password FROM user WHERE userid = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "s", $userId);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);



if (mysqli_stmt_num_rows($stmt) === 0) {
    $response = array('success' => false, 'message' => 'User not found');
    echo json_encode($response);
    exit;
}


mysqli_stmt_bind_result($stmt, $hashedPassword);
mysqli_stmt_fetch($stmt);


if ($currentPassword !== $hashedPassword) {
    $response = array('success' => false, 'message' => 'Current password is incorrect');
    echo json_encode($response);
    exit;
}


//$newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);



$updateQuery = "UPDATE user SET password = ? WHERE userid = ?";
$updateStmt = mysqli_prepare($con, $updateQuery);
mysqli_stmt_bind_param($updateStmt, "ss", $newPassword, $userId);

/* $response = array('success' => false, 'message' => 'User not found66');
echo json_encode($response);
exit; */
if (mysqli_stmt_execute($updateStmt)) {
    session_unset();     // Clear session data
    session_destroy();
    $response = array('success' => true,  'message' => 'Password changed. Please log in again.', 'redirect' => '../index.php');
    echo json_encode($response);
    exit;
} else {

    $response = array('success' => false, 'message' => 'Failed to update password');
    echo json_encode($response);
    exit;
}
