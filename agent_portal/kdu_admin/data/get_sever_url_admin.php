<?php
include '../../../config/global.php';

$serverUrl = $url_admin;

header('Content-Type: application/json');
echo json_encode($serverUrl);
