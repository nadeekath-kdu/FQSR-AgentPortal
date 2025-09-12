<?php
include '../../../config/dbcon.php';

$sql_app_list = "SELECT nic_no,course_name,stu_name_initials,stu_email,citizenship_type,nameEduAgent,intake FROM mst_personal_details WHERE  !(nameEduAgent = 'null' OR nameEduAgent = '')";

$result = $con_fqsr->query($sql_app_list);

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
