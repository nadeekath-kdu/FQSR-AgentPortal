<?php
include '../config/dbcon.php';

$sql_degree_list = "SELECT degree_code,degree_name FROM mst_degree_courses WHERE active_status = 'Y' ";

$result = $con_fqsr->query($sql_degree_list);

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
