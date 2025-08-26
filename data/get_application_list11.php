<?php
if (!isset($_SESSION)) {
    session_start();
}

include '../config/dbcon.php';
$ag_code = $_SESSION['agent_code'];
$sql_degree_list = "SELECT nic_no,course_name,stu_name_initials,stu_email,formStatus,nameEduAgent FROM mst_personal_details WHERE nameEduAgent = '$ag_code'"; //WHERE formStatus = 'UNSUBMITTED' 

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
