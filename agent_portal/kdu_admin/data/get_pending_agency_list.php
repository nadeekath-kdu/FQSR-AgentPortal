<?php
session_start();
include '../../../config/dbcon.php';
//include '../pages/system_login.php';
// Check if user_role is set in the session
if (!isset($_SESSION['user_role'])) {
    //echo json_encode(['error' => 'User role not set in session.']);
    exit();
}

$user_role = $_SESSION['user_role'];

$fro_role = 'FRO';
$dr_role = 'DR';
$dvc_role = 'DVC';
$vc_role = 'VC';

$sql = "SELECT agency_code, fullname, organisation, addressLine1, city, country, email, telephone1, mobile, status, ismailgenerate FROM agency ";

if ($user_role === $fro_role) {
    $query_req = $sql . " WHERE status_fro = '' ORDER BY rec_id";
    $heading1 = 'Pending - Agency List';
} elseif ($user_role === $dr_role) {
    $query_req = $sql . " WHERE status_fro = 'REVIEWED' AND status_dr = '' ORDER BY rec_id";
    $heading1 = 'Verification Pending - Agency List';
} elseif ($user_role === $dvc_role) {
    $query_req = $sql . " WHERE status_dr = 'VERIFIED' AND status_dvc = '' ORDER BY rec_id";
    $heading1 = 'Recommendation Pending - Agency List';
} elseif ($user_role === $vc_role) {
    $query_req = $sql . "  WHERE status_dvc = 'RECOMMENDED' AND status_vc = '' ORDER BY rec_id";
    $heading1 = 'Approval Pending - Agency List';
} else {
    $query_req = '';
    $heading1 = 'No Data Found!';
}

$result = $con->query($query_req);

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
