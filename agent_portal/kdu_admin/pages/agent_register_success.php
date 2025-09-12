<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: http://students.kdu.ac.lk');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Max-Age: 3600');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../../../config/dbcon.php';
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

$email = 'softwaredev@kdu.ac.lk';
$password = 'IF!m1Z01';

// Set the recipient, subject, and body of the email
$to = 'nadeeka.ieee@gmail.com'; //$_POST['to'];
$subject = 'KDU Agent Portal Notification';
$body = 'KDU Agent Portal Notification.';

$refrence_code = "";
$kdu_client_ref = "";
$ag_code = $_GET['code'];
$fullname = "";
$password_ag = "";
$payment_desc = "";


$tot_deg = 0;
$all_deg_str = "";
$all_deg_arr = array();

if (isset($_GET['rfr_code'])) {
	$refrence_code = $_GET['rfr_code'];
}

function randomPassword()
{
	$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	$pass = array(); //remember to declare $pass as an array
	$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	for ($i = 0; $i < 8; $i++) {
		$n = rand(0, $alphaLength);
		$pass[] = $alphabet[$n];
	}
	return implode($pass); //turn the array into a string
}
if (isset($_GET['code'])) {
	//echo 'AAAA'.$ag_code;
	//$kdu_client_ref = $_GET['code'];
	//$sql_updt = "UPDATE agency SET status = 'APPROVED' WHERE agency_code = '$ag_code' ";
	//$row_updt = mysql_query($sql_updt) or die();

	// get details
	$sql_agency = "SELECT * FROM agency WHERE agency_code = '$ag_code'";
	$res_agency = mysqli_query($con, $sql_agency) or die();
	$row_agency = mysqli_fetch_array($res_agency);

	$fullname = $row_agency['fullname'];
	//$pay_amount = $_GET['kduamt'];
	$to = $row_agency['email'];
	$password_ag = randomPassword(); //'73s7p55w';


	$sql_user = "INSERT INTO user (userid,password,agency_code) VALUES('$to','$password_ag','$ag_code')";
	$res_user = mysqli_query($con, $sql_user) or die();

	$sql_status = "UPDATE agency SET status= 'Approved' WHERE agency_code = '$ag_code'";
	$res_status = mysqli_query($con, $sql_status) or die();
	/* $customer_email = $row_agency['customer_email'];
	$pay_currency = $row_agency['currency']; */
	// -----------
	//echo 'Email:'.$email;
	// Update personal details 


	// --------------------------------

	//header('Location:agency_list.php');
}



$em_txt = '
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Untitled Document</title>
    </head>
   
    <body>
    <img src="https://enlistment.kdu.ac.lk/email-image/band.jpg" />
    <p>
    <b>Credentials for Login to Agent Portal</b>
    </p>
    <p></p>
    <br />
    Dear ' . $fullname . ',
    <br />
    <br />
    Considering your application submitted, it is hereby informed that you have been selected as an Agent to promote KDU degree programmes to enlist Foreign Students and Students with Foreign Qualifications.
	<br />
	<br />
	<br />
	Welcome to Kotelawala Defence University - Agent Web Portal.
	<br />
	<br />
	Please use the link provided to visit the KDU Agent Portal and login details are as follows:
	<br />
	<br />
	URL - https://enlistment.kdu.ac.lk/agent_portal
	<br />
	<br />
	Username - ' . $to . ' 
	<br />
	<br />
	Password - ' . $password_ag . '
	<br />
	<br />
	You are hereby requested to submit the details of applicants through this portal only. Students details submitted without the portal will not be considered as students enlisted through an Agent.
Further, contact following personals for any further information and assistance required.
<br />
<br />
Deputy Registrar 		: +94 710 219 255
<br />
Foreign relations Officer	: +94 710 219 338

	<br />
	<br />
    Thank you.    
    </body>
    </html>
	';

$mail = new PHPMailer;
// Set up SMTP authentication
$mail->isSMTP();
$mail->SMTPAuth = true;
$mail->Username = $email;
$mail->Password = $password;

// Set the mail server and port
$mail->Host = 'smtp-mail.outlook.com';
$mail->Port = 587;

// Set the email properties

$mail->setFrom($email, 'KDU');
$mail->addAddress($to);
$mail->Subject = $subject;
$mail->Body = $body;
$mail->msgHTML($em_txt);

// Send the email and return a response based on the result
/* if ($mail->send()) {
    $response = array('status' => 1, 'message' => 'Message sent successfully');
    echo json_encode($response);
} else {
    $response = array('status' => 0, 'message' => $mail->ErrorInfo);
    echo json_encode($response);
} */
$mailstatus = '';


if (!$mail->send()) {
	echo 'Message could not be sent.';
	//echo 'Mailer Error: ' . $mail->ErrorInfo;
	$mailstatus = 'NotSent: ' . $mail->ErrorInfo . ',' . $email;
	$response['status'] = 0;
} else {
	//echo 'Message has been sent';
	$mailstatus = 'Sent';
	$response['status'] = 1;
}
$sql_mail = "UPDATE agency SET ismailgenerate= '$mailstatus' WHERE agency_code = '$ag_code'";
$res_mail = mysqli_query($con, $sql_mail) or die();
//header('Location:../coall_approved_agency_list.php');
header('Content-Type: application/json');
echo json_encode($response);
