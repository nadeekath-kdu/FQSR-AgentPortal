<?php
include '../../../config/global.php';

$server_url = $url;
$url_admin = $url_admin;

$response = [
    'server_url' => $server_url,
    'url_admin' => $url_admin
];
echo json_encode($response);
