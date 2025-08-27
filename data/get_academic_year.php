<?php
require_once('../config/dbcon.php');
require_once('../config/global.php');

header('Content-Type: application/json');

$response = array(
    'academic_year' => $academic_year
);

echo json_encode($response);
