<?php
include '../../../config/dbcon.php';

$query = "SELECT DISTINCT nameEduAgent FROM mst_personal_details";
$result = $con_fqsr->query($query);

/* $agents = [];
while ($row = $result->fetch_assoc()) {
    $agents[] = $row['nameEduAgent'];
} */
$agents = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $agents[] = $row['nameEduAgent'];
    }
} else {
    echo "0 results";
}

header('Content-Type: application/json');
echo json_encode($agents);
