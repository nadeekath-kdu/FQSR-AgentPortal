<?php
include '../config/global.php';
$response = array();
$server_url = $url;
$url_admin = $url_admin;

$response = [
    'server_url' => $server_url,
    'url_admin' => $url_admin
];
header('Content-Type: application/json');
echo json_encode($response);
