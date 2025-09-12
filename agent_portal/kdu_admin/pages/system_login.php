<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include '../../../config/dbcon.php';

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM user WHERE userid='$username' AND password='$password'";
    $res_user = mysqli_query($con, $query) or die(mysqli_error($con));
    $row_user = mysqli_fetch_array($res_user);

    if ($row_user) {
        $_SESSION['username'] = $username;
        $_SESSION['user_role'] = $row_user['user_role'];
        $_SESSION['loggedin'] = true;
        header('Location: ../includes/dashboard.php');
        exit();
    } else {
        $errorCode = 1;
        header('Location: ../login.html?code=' . $errorCode);
        exit();
    }
}
