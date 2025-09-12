<?php
session_start();
include '../../../config/dbcon.php'; //include('../content/viewagency.php');
$fro_role = 'FRO';
$dr_role = 'DR';
$dvc_role = 'DVC';
$vc_role = 'VC';

$success = true; // Default to success

try {
	$code = $_POST['code'];
	$user_role = $_POST['user_role'];
	//echo $user_role;
	if ($user_role == $fro_role) {

		if (isset($_POST['review'])) {
			$review_status = $_POST['review'];
			$remark_fro = $_POST['review_comments'];
			if ($review_status == 'REVIEWED') {
				//header('Location:agent_register_success.php?code='.$code);// send email to agency
				//$sql_status = "UPDATE agency SET status= 'Verify' WHERE agency_code = '$code'";
				$sql_status = "UPDATE agency SET status_fro= 'REVIEWED', remark_fro = '' WHERE agency_code = '$code'";
				$res_status = mysqli_query($con, $sql_status) or die();
				//header('Location:../content/viewpendinglist.html');
			} else if ($review_status == 'Hold') {
				$sql_status = "UPDATE agency SET status_fro= '$review_status', remark_fro = '$remark_fro' WHERE agency_code = '$code'";
				$res_status = mysqli_query($con, $sql_status) or die();
				//header('Location:agent_hold.php?code='.$code.'&reason='.$_POST['reCallingReason']);
			} else {
				//echo $verification_status.','.$remark_fro;
				$sql_status = "UPDATE agency SET status_fro= '$review_status', remark_fro = '$remark_fro' WHERE agency_code = '$code'";
				$res_status = mysqli_query($con, $sql_status) or die();
				//header('Location:agent_reject.php?code='.$code.'&reason='.$_POST['rejectReason']);
				//header('Location:../content/viewpendinglist.html');
			}
			$status = $review_status;
		}
	} elseif ($user_role == $dr_role) {
		$remark_dr = $_POST['remark_dr'];
		$verification_status = $_POST['verification'];
		$sql_status = "UPDATE agency SET status_dr= '$verification_status', remark_dr = '$remark_dr' WHERE agency_code = '$code'";
		$res_status = mysqli_query($con, $sql_status) or die();
		$status = $verification_status;
		//header('Location:../content/viewpendinglist.html');
	} elseif ($user_role == $dvc_role) {
		$remark_dvc = $_POST['remark_dvc'];
		$recommendation_status = $_POST['recommendation'];
		$sql_status = "UPDATE agency SET status_dvc= '$recommendation_status', remark_dvc = '$remark_dvc' WHERE agency_code = '$code'";
		$res_status = mysqli_query($con, $sql_status) or die();
		$status = $recommendation_status;
		//header('Location:../content/viewpendinglist.html');
	} elseif ($user_role == $vc_role) {
		$remark_vc = $_POST['remark_vc'];
		$approval_status = $_POST['approval'];
		$sql_status = "UPDATE agency SET status_vc= '$approval_status', remark_vc = '$remark_vc' WHERE agency_code = '$code'";
		$res_status = mysqli_query($con, $sql_status) or die();
		$status = $approval_status;
		//header('Location:../content/viewpendinglist.html');
	}
} catch (Exception $e) {
	$success = false;
}

$data = array(
	"success" => $success,
	"status" => $status,
	"code" => $code,
	"message" => "Data fetched successfully",
	"data" => array(
		"code" => $code
	)
);

header('Content-Type: application/json');
echo json_encode($data);
//}
