<?php
require_once 'dbcon.php';
//$conn = mysqli_connect(DB_HOST,DB_USERNAME,DB_PWD,DB_TBL);
$sql_intake = "SELECT * FROM intake ORDER BY id DESC LIMIT 1";
$res_intake = mysqli_query($con_fqsr, $sql_intake);
$row_academicYear = mysqli_fetch_array($res_intake);

$academic_year = $row_academicYear['year'];
$intake = $row_academicYear['intake'];
$application_closing_date = $row_academicYear['application_closing_date'];
/* $sql_agency = "SELECT * FROM user WHERE ";
$res_agency = mysqli_query($con,$sql_agency);
$row_acency = mysqli_fetch_array($res_agency); */

//$row_academicYear = '2022';

$url = "https://enlistment.kdu.ac.lk/agent_portal";
$url_admin = "https://enlistment.kdu.ac.lk/agent_portal/kdu_admin";
