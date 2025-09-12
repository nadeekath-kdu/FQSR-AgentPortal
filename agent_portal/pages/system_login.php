<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include '../../config/dbcon.php';

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Join user table with agency table to get agent details
    $query = "SELECT u.*, a.fullname as agent_fullname, a.organisation 
              FROM user u 
              LEFT JOIN agency a ON u.agency_code = a.agency_code 
              WHERE u.userid='$username' AND u.password='$password'";
    $res_user = mysqli_query($con, $query) or die(mysqli_error($con));
    $row_user = mysqli_fetch_array($res_user);

    if ($row_user) {
        $_SESSION['username'] = $username;
        $_SESSION['agent_code'] = $row_user['agency_code'];
        // Use fullname from agency table, fallback to user table if not available
        $_SESSION['agent_name'] = !empty($row_user['agent_fullname']) ? $row_user['agent_fullname'] : 'Agent';
        $_SESSION['organisation'] = $row_user['organisation'];
        $_SESSION['loggedin'] = true;
        header('Location: ../includes/dashboard.php');
        exit();
    } else {
        $errorCode = 1;
        header('Location: login.php?code=' . $errorCode);
        exit();
    }
}
