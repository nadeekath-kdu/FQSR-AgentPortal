<?php
require_once '../config/iv_key.php';
//require_once '../config/mystore_func.php';
require_once '../config/global.php';
require_once '../config/dbcon.php';
$db_connection = $con;
if (isset($_POST['submit'])) {
    $db_connection = $con;
    //$db_connection = $$con_fqsr;
    $form_action = 'submit';
    $formStatus = 'SUBMITTED';
} elseif (isset($_POST['save'])) {
    $db_connection = $con;
    $form_action = 'save';
    $formStatus = 'UNSUBMITTED';
}
date_default_timezone_set('Asia/Colombo');

$enc_nic_no = "";
$dec_nic_no = "";
$err_code = 0;
$display_message = "";
$academic_year = $row_academicYear['year'];
$intake = $row_academicYear['intake'];
$application_closing_date = $row_academicYear['application_closing_date'];
//echo "AA:".$_GET['idn'];
if ((isset($_GET['nic'])) && ($_GET['nic'] != NULL) && ($_GET['nic'] != "") && ($_GET['nic'] != " ")) {
    $enc_nic_no = $_GET['nic'];
    //$dec_nic_no = decryptStr($enc_nic_no, ENCRYPT_METHOD, WSECRET_KEY, WSECRET_IV); //local
    $dec_nic_no = $enc_nic_no; //local
}
if (isset($_POST['submit1'])) {
    header('Location:formupdate.php?idn=' . $enc_nic_no);
}
if (isset($_POST['submit2'])) {
    header('Location:../unsubmitted_list.php');
    //header('Location:intermediate_pg_request.php?idn='.$enc_nic_no.'&lsidn='.$enc_last_id);
}
if (isset($_POST['submit3'])) {
    header('Location:https://enlistment.kdu.ac.lk/fqsr/formsave.php?idn=' . $enc_nic_no);
    //header('Location:intermediate_pg_request.php?idn='.$enc_nic_no.'&lsidn='.$enc_last_id);
}

//get personal details
$sql_get_personal = "SELECT * FROM mst_personal_details WHERE nic_no ='$dec_nic_no'    ";
$res_get_personal = mysqli_query($db_connection, $sql_get_personal) or die(mysqli_error($db_connection));
$row_get_personal = mysqli_fetch_array($res_get_personal);

// ---------------
$sql_eng_prof_sat = "SELECT * FROM mst_english_proficiency WHERE stu_passport_id = '$dec_nic_no' AND qualification_type = 'SAT'";
$res_eng_prof_sat = mysqli_query($db_connection, $sql_eng_prof_sat) or die(mysqli_error($db_connection));
$row_eng_prof_sat = mysqli_fetch_array($res_eng_prof_sat);
// ---------------
$sql_family_father = "SELECT * FROM family_details WHERE stu_passport_id = '$dec_nic_no' AND relationship = 'FATHER'";
$res_family_father = mysqli_query($db_connection, $sql_family_father) or die(mysqli_error($db_connection));
$row_family_father = mysqli_fetch_array($res_family_father);
//$family_row_cnt_father = mysqli_num_rows($res_family_father);
// ---------------
$sql_family_mother = "SELECT * FROM family_details WHERE stu_passport_id = '$dec_nic_no' AND relationship = 'MOTHER'";
$res_family_mother = mysqli_query($db_connection, $sql_family_mother) or die(mysqli_error($db_connection));
$row_family_mother = mysqli_fetch_array($res_family_mother);
//$family_row_cnt_mother = mysqli_num_rows($res_family_mother);
// ---------------
$sql_family_guardian = "SELECT * FROM family_details WHERE stu_passport_id = '$dec_nic_no' AND relationship = 'GUARDIAN'";
$res_family_guardian = mysqli_query($db_connection, $sql_family_guardian) or die(mysqli_error($db_connection));
$row_family_guardian = mysqli_fetch_array($res_family_guardian);

// ---------------
$sql_refree_sl = "SELECT * FROM refree WHERE stu_passport_id = '$dec_nic_no' AND type = 'SRILANKA'";
$res_refree_sl = mysqli_query($db_connection, $sql_refree_sl) or die(mysqli_error($db_connection));
$row_refree_sl = mysqli_fetch_array($res_refree_sl);

// get degrees
$sql_degree_list = "SELECT * FROM mst_degree_courses WHERE active_status = 'Y' ";
$res_degree_list = mysqli_query($db_connection, $sql_degree_list) or die(mysqli_error($db_connection));

$degree_list_cnt = mysqli_num_rows($res_degree_list);
//--------------
