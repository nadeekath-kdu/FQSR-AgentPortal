<?php
if(!isset($_SESSION)) 
{ 
    session_start();
} 
include '../../config/dbcon.php'; 
require_once '../../config/iv_key.php';
require_once '../../config/mystore_func.php'; 

$enc_nic_no = trim($_POST['passportno']);
$dec_nic_no = $enc_nic_no; // decryptStr($enc_nic_no,ENCRYPT_METHOD,WSECRET_KEY,WSECRET_IV);    
//$dec_nic_no = mysqli_real_escape_string($db_connection,$dec_nic_no);
$sql_chk = "SELECT applicant_id FROM mst_personal_details WHERE nic_no = '$dec_nic_no' ";
$res_chk = $con_fqsr->query($sql_chk);

$applicant_cnt = mysqli_num_rows($res_chk);
if($applicant_cnt > 0){
    $app_confirm_status = 'exist';
}else{
    $app_confirm_status = 'notexist';
} 

$options = array();

$options[] = $app_confirm_status;


header('Content-Type: application/json');
echo json_encode($options);
?>
