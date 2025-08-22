<?php
if(!isset($_SESSION)) 
{ 
    session_start();
} 
$ag_code = $_SESSION['agent_code']; 
include '../../config/dbcon.php'; 

$sql_degree_list = "SELECT COUNT(*) AS total FROM mst_personal_details WHERE formStatus='UNSUBMITTED' AND nameEduAgent = '$ag_code'";

$result = $con->query($sql_degree_list);

$options = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $options[] = $row;
    }
} else {
    echo "0 results";
}


header('Content-Type: application/json');
echo json_encode($options);
?>
