<?php
include '../../../config/dbcon.php';

$sql_degree_list = "SELECT agency_code,fullname,organisation,addressLine1,city,country,email,telephone1,mobile,status_fro, status_dr,status_dvc ,status_vc ,ismailgenerate FROM agency WHERE 1=1"; //ag_code

$result = $con->query($sql_degree_list);

$options = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $options[] = $row;
    }
} else {
    echo "0 results";
}


header('Content-Type: application/json');
echo json_encode($options);
