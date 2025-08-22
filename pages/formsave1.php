<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../config/dbcon.php';
require_once '../config/global.php';
$response = array();

// Check database connection
if (!$con || $con->connect_error) {
    $response = array(
        'status' => 'error',
        'message' => 'Database connection failed: ' . ($con ? $con->connect_error : 'No connection object')
    );
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $test_sql = "INSERT INTO `agent_portal`.`mst_personal_details` 
    (`nic_no`, `course_name`, `course_code`, `intake`, `stu_title`, `stu_fullname`, `stu_name_initials`, `stu_dob`, `stu_gender`, `stu_citizenship`, `civil_status`, `stu_permenant_address`, `stu_email`, `application_confirm_status`, `application_submit_dt`, `media_source_name`, `doc_upload_link`, `birth_country`, `period_study_abroad`, `eligibility_uni_admision`, `other_qualification`, `fund`, `citizenship_type`, `citizenship_1`, `citizenship_2`, `AL_sitting_country`, `Photo`, `payment_status`, `isEduAgent`, `nameEduAgent`, `formStatus`, `isRequestHostel`) 
    VALUES 
    ('3334', '-', '-', '-', '-', '-', '-', NULL, '-', '-', '-', NULL, '', 'N', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', '', '', '', '', NULL, NULL, '', NULL)";

    $test_res = $con->query($test_sql);

    if ($test_res) {
        $response = array(
            'status' => 'success',
            'message' => 'Test insert successful'
        );
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'Test insert failed: ' . mysqli_error($con)
        );
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
