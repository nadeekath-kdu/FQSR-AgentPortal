<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION)) {
    session_start();
}

header('Content-Type: application/json');

// Debug logging
error_log('POST data received: ' . print_r($_POST, true));
error_log('FILES data received: ' . print_r($_FILES, true));
require_once '../config/dbcon.php';
require_once '../config/iv_key.php';
require_once '../config/mystore_func.php';
require_once '../config/global.php';

$conn = $con_fqsr;
if (!$conn) {
    http_response_code(500);
    echo json_encode(array(
        'status' => 'error',
        'message' => 'Database connection failed'
    ));
    exit;
}

$formStatus = '';
$response = array(
    'status' => 'error',
    'message' => 'Unknown error occurred',
    'nic' => ''
);

// Get form data
$enc_nic_no = isset($_POST['inputNic']) ? $_POST['inputNic'] : '';
$dec_nic_no = $enc_nic_no; // If encryption is needed, use decryptStr()

if (empty($dec_nic_no)) {
    echo json_encode(array(
        'status' => 'error',
        'message' => 'NIC/Passport number is required'
    ));
    exit;
}

$err_code = 0;
$msg = "";
$app_confirm_status = 0;

// Prepare update data
$updateData = array();
$updateFields = array(
    'stu_fullname' => 'inputFullname',
    'stu_name_initials' => 'inputNameInitials',
    'stu_dob' => 'inputDob',
    'stu_gender' => 'inputGender',
    'civil_status' => 'inputCivilSts',
    'citizenship_type' => 'citizenship_type',
    'country_birth' => 'inputCountryBirth',
    'period_study' => 'periodStudy',
    'stu_permenant_address' => 'addressPermanent',
    'stu_email' => 'inputEmailAddress',
    'elegible_state' => 'elegibleState'
);
$last_id = 0;
$enc_last_id = "";
$media_source_name = "Other";
$sql_personal_data = "";
date_default_timezone_set('Asia/Colombo');

if ((isset($_POST['inputNic'])) && ($_POST['inputNic'] != NULL) && ($_POST['inputNic'] != "") && ($_POST['inputNic'] != " ")) {
    $inputNic = "";
    $inputNic = trim($_POST['inputNic']);
    $inputNic = mysqli_real_escape_string($conn, $inputNic);
    $enc_nic_no = trim($inputNic);

    //$dec_nic_no = decryptStr($enc_nic_no, ENCRYPT_METHOD, WSECRET_KEY, WSECRET_IV);    //local

    $dec_nic_no = $enc_nic_no; //local
    $dec_nic_no = mysqli_real_escape_string($conn, $dec_nic_no);

    // perform a check to see applicant has confirm the application
    $sql_chk = "SELECT * FROM mst_personal_details WHERE nic_no = '$dec_nic_no' AND application_confirm_status = 'Y' ";
    $res_chk = mysqli_query($conn, $sql_chk);
    $result_applicant = mysqli_fetch_array($res_chk);
    $last_id = $result_applicant['applicant_id'];
    $applicant_cnt = mysqli_num_rows($res_chk);
    $oldphoto = $result_applicant['Photo'];
    if ($applicant_cnt > 0) {
        // insert data personal data
        $apply_course_code = "";
        $apply_course = "";
        $intake_yr = "-";
        $stu_title = "";
        $stu_surname = "";
        $stu_givenname = "";
        $stu_initialname = "";
        $stu_dob = "";
        $stu_gender = "";

        $stu_civilstats = "";
        $stu_service_typ = "";
        $stu_rank = "";
        $stu_office_addr = "";
        $stu_home_addr = "";
        $stu_home_tel = "";
        $stu_country_birth = "";
        $stu_email = "";
        $doc_upld_link = "";
        $period_study_abroad = "";
        $eligibility_uni_admision = $_POST['elegibleState'];
        $citizenship_type = "";
        $stu_citizenship = "";
        $citizenship1 = "";
        $citizenship2 = "";
        $country_AL = "";
        $eduAgent = ""; /* 2022-07-20 */
        $nameEduAgent = "";/* 2022-07-20 */
        $Photo = "";

        $apply_course_code = $_POST['inputCourse'];
        $stu_fullname = $_POST['inputFullname'];
        $stu_initialname = $_POST['inputNameInitials'];
        $stu_gender = $_POST['inputGender'];
        $citizenship_type = $_POST['citizenship_type'];
        $stu_civilstats = $_POST['inputCivilSts'];
        $stu_birth_country = $_POST['inputCountryBirth'];
        $stu_permenant_addr = $_POST['addressPermanent'];
        $stu_email = $_POST['inputEmailAddress'];
        //$media_source_name = $_POST['inputMediaSource'];
        $doc_upld_link = $_POST['docupldlink'];
        $period_study_abroad = $_POST['periodStudy'];


        if ($err_code == 1) {
            // redirect back to application form
            header('Location:applicationform.php?errcode=1');
        } else {
            // sanitize inputs
            $stu_dob = $_POST['inputDob'];
            $apply_course_code = mysqli_real_escape_string($conn, $apply_course_code);
            //$AcademicYear = trim($_POST['inputAcademicYear']);
            $intake_yr = trim($_POST['inputIntakeYr']);
            $intake_yr = mysqli_real_escape_string($conn, $intake_yr);
            $stu_title = trim($_POST['inputTitle']);
            $stu_title = mysqli_real_escape_string($conn, $stu_title);
            $stu_fullname = mysqli_real_escape_string($conn, $stu_fullname);
            $stu_birth_country = mysqli_real_escape_string($conn, $stu_birth_country);
            $stu_initialname = mysqli_real_escape_string($conn, $stu_initialname);
            $stu_dob = mysqli_real_escape_string($conn, $stu_dob);
            $stu_gender = mysqli_real_escape_string($conn, $stu_gender);
            $citizenship_type = mysqli_real_escape_string($conn, $citizenship_type);
            $stu_civilstats = mysqli_real_escape_string($conn, $stu_civilstats);
            $stu_permenant_addr = mysqli_real_escape_string($conn, $stu_permenant_addr);
            $stu_email = mysqli_real_escape_string($conn, $stu_email);
            $media_source_name = ""; //mysqli_real_escape_string($conn,$media_source_name);
            $doc_upld_link = mysqli_real_escape_string($conn, $doc_upld_link);
            $period_study_abroad = mysqli_real_escape_string($conn, $period_study_abroad);
            $eligibility_uni_admision = mysqli_real_escape_string($conn, $eligibility_uni_admision);
            $other_qualification = trim($_POST['otherQualifications']);
            $other_qualification = mysqli_real_escape_string($conn, $other_qualification);
            $fund = trim($_POST['fund']);
            $fund = mysqli_real_escape_string($conn, $fund);
            $stu_citizenship = trim($_POST['inputCitizenship']);
            $stu_citizenship = mysqli_real_escape_string($conn, $stu_citizenship);
            $citizenship1 = trim($_POST['inputCitizenship1']);
            $citizenship1 = mysqli_real_escape_string($conn, $citizenship1);
            $citizenship2 = trim($_POST['inputCitizenship2']);
            $citizenship2 = mysqli_real_escape_string($conn, $citizenship2);
            $country_AL = trim($_POST['inputCountryAL']);
            $country_AL = mysqli_real_escape_string($conn, $country_AL);
            $eduAgent = trim($_POST['eduAgent']); /* 2022-07-20 */
            $eduAgent = mysqli_real_escape_string($conn, $eduAgent);
            $nameEduAgent = trim($_POST['nameEduAgent']);
            $nameEduAgent = mysqli_real_escape_string($conn, $nameEduAgent); /* end 2022-07-20 */
            //$Photo=$_FILES["Photo"]["name"]; 

            // Save profile picture
            $uploaddir = dirname(__FILE__) . "/../profile/";
            if (!file_exists($uploaddir)) {
                mkdir($uploaddir, 0777, true);
            }

            // Initialize photo variable
            $Photo = $oldphoto; // Default to existing photo

            // Check if a new photo was uploaded
            if (isset($_FILES["Photo"]) && !empty($_FILES["Photo"]["name"])) {
                // Validate file type
                $allowed_types = array('image/jpeg', 'image/png', 'image/jpg');
                $file_type = $_FILES["Photo"]["type"];

                if (!in_array($file_type, $allowed_types)) {
                    error_log("Invalid file type uploaded. Only JPG and PNG are allowed.");
                    $response = array(
                        'status' => 'error',
                        'message' => 'Invalid file type. Only JPG and PNG images are allowed.'
                    );
                    echo json_encode($response);
                    exit;
                }

                // Validate file size (max 2MB)
                if ($_FILES["Photo"]["size"] > 2 * 1024 * 1024) {
                    error_log("File too large. Maximum size is 2MB.");
                    $response = array(
                        'status' => 'error',
                        'message' => 'File too large. Maximum size is 2MB.'
                    );
                    echo json_encode($response);
                    exit;
                }

                $extension = strtolower(pathinfo($_FILES["Photo"]["name"], PATHINFO_EXTENSION));
                $new_filename = $dec_nic_no . '.' . $extension;
                $uploadfile = $uploaddir . $new_filename;

                // Delete old file if it exists and is different
                if ($oldphoto && $oldphoto != $new_filename && file_exists($uploaddir . $oldphoto)) {
                    unlink($uploaddir . $oldphoto);
                }

                // Try to upload the new file
                if (move_uploaded_file($_FILES["Photo"]["tmp_name"], $uploadfile)) {
                    error_log("File uploaded successfully to: " . $uploadfile);
                    $Photo = $new_filename;
                } else {
                    error_log("Failed to move uploaded file. Error: " . $_FILES["Photo"]["error"]);
                    error_log("Attempted to move to: " . $uploadfile);
                    $response = array(
                        'status' => 'error',
                        'message' => 'Failed to upload photo. Please try again.'
                    );
                    echo json_encode($response);
                    exit;
                }
            } // end photo upload check
            // get apply course name
            $sql_cousr_name = "SELECT degree_name FROM mst_degree_courses WHERE degree_code = '$apply_course_code' ";
            $res_course_name = mysqli_query($conn, $sql_cousr_name);

            $course_name_cnt = mysqli_num_rows($res_course_name);
            if ($course_name_cnt > 0) {
                while ($row_course_name = mysqli_fetch_array($res_course_name)) {
                    $apply_course = $row_course_name['degree_name'];
                }
            }
            // ---------------------
            $cur_dt = date('Y-m-d H:i:s');
            /* 2022-07-20 */
            $sql_personal_data = "UPDATE mst_personal_details SET course_name= '$apply_course',course_code= '$apply_course_code',intake = '$intake_yr',stu_title = '$stu_title',stu_fullname = '$stu_fullname',stu_name_initials = '$stu_initialname',stu_dob = '$stu_dob',stu_gender = '$stu_gender',stu_citizenship = '$stu_citizenship',civil_status = '$stu_civilstats',stu_permenant_address = '$stu_permenant_addr',stu_email = '$stu_email',application_submit_dt = '$cur_dt',media_source_name = '$media_source_name',birth_country = '$stu_birth_country',period_study_abroad = '$period_study_abroad',eligibility_uni_admision = '$eligibility_uni_admision',other_qualification = '$other_qualification',fund = '$fund',citizenship_type = '$citizenship_type',citizenship_1 = '$citizenship1',citizenship_2 = '$citizenship2',AL_sitting_country = '$country_AL',photo = '$Photo' WHERE nic_no = '$dec_nic_no'";
            //$sql_personal_data = "UPDATE mst_personal_details SET course_name= '$apply_course',course_code= '$apply_course_code',intake = '$intake_yr',stu_title = '$stu_title',stu_fullname = '$stu_fullname',stu_name_initials = '$stu_initialname',stu_dob = '$stu_dob',stu_gender = '$stu_gender',stu_citizenship = '$stu_citizenship',civil_status = '$stu_civilstats',stu_permenant_address = '$stu_permenant_addr',stu_email = '$stu_email',application_submit_dt = '$cur_dt',media_source_name = '$media_source_name',doc_upload_link = '$doc_upld_link',birth_country = '$stu_birth_country',period_study_abroad = '$period_study_abroad',eligibility_uni_admision = '$eligibility_uni_admision',other_qualification = '$other_qualification',fund = '$fund',citizenship_type = '$citizenship_type',citizenship_1 = '$citizenship1',citizenship_2 = '$citizenship2',AL_sitting_country = '$country_AL',nameEduAgent = '$nameEduAgent',isEduAgent = '$eduAgent',photo = '$Photo' WHERE nic_no = '$dec_nic_no'";
            $res_personal_data = mysqli_query($conn, $sql_personal_data);

            $test_var = "";

            if ($res_personal_data) {
                //$last_id = mysqli_insert_id($conn);
                //$enc_last_id = encryptStoreStr($last_id,ENCRYPT_METHOD,WSECRET_KEY,WSECRET_IV);
                $enc_last_id = $last_id;
                $edu_counter = $_POST['edurowcnt'];
                $edu_counter2 = $_POST['edurowcnt2'];
                $edu_counter3 = $_POST['edurowcnt3'];

                // Check if A/L section is empty
                $isALEmpty = true;
                for ($ei = 0; $ei <= $edu_counter; $ei++) {
                    if (isset($_POST['subject_AL_' . $ei]) && trim($_POST['subject_AL_' . $ei]) != '') {
                        $isALEmpty = false;
                        break;
                    }
                }

                // Delete A/L records if section is empty or proceed with update
                $sql_educational_dl = "DELETE from mst_educational_qualifications WHERE stu_nic = '$dec_nic_no' AND exm_type = 'A/L'";
                $res_educational_dl = mysqli_query($conn, $sql_educational_dl);

                // Process A/L records regardless, but only insert if not empty
                $exam_name_al = isset($_POST['examNameAL']) ? trim($_POST['examNameAL']) : '';
                $exam_name_al = mysqli_real_escape_string($conn, $exam_name_al);
                for ($ei = 0; $ei <= $edu_counter; $ei++) {
                    // Get A/L details with isset checks
                    $subject_grade = isset($_POST['subject_AL_' . $ei]) ? trim($_POST['subject_AL_' . $ei]) : '';
                    $subject_grade = mysqli_real_escape_string($conn, $subject_grade);
                    $award = isset($_POST['result_AL_' . $ei]) ? trim($_POST['result_AL_' . $ei]) : '';
                    $award = mysqli_real_escape_string($conn, $award);
                    $exam_year_al = isset($_POST['year_AL_' . $ei]) ? trim($_POST['year_AL_' . $ei]) : '';
                    $exam_year_al = mysqli_real_escape_string($conn, $exam_year_al);

                    // insert educational qualifications
                    if ($exam_year_al != "" && $exam_name_al != "") {

                        $sql_educational = "INSERT INTO mst_educational_qualifications (stu_nic,exam_year,exam_name,exm_type,subject_grade,award,stu_id) VALUES ('$dec_nic_no','$exam_year_al','$exam_name_al','A/L','$subject_grade','$award',$last_id)";
                        $res_educational = mysqli_query($conn, $sql_educational);
                        //echo $sql_educational;
                        if ($res_educational) {
                        } else {
                            $err_code = 2;
                        }
                    } // end if
                } // end for educational A/L

                // Check if O/L section is empty
                $isOLEmpty = true;
                for ($ei = 0; $ei <= $edu_counter2; $ei++) {
                    if (isset($_POST['subject_OL_' . $ei]) && trim($_POST['subject_OL_' . $ei]) != '') {
                        $isOLEmpty = false;
                        break;
                    }
                }

                // Delete O/L records if section is empty or proceed with update
                $sql_educational_dl2 = "DELETE from mst_educational_qualifications WHERE stu_nic = '$dec_nic_no' AND exm_type = 'O/L'";
                $res_educational_dl2 = mysqli_query($conn, $sql_educational_dl2);

                // Process O/L records regardless, but only insert if not empty
                $exam_name_ol = isset($_POST['examNameOL']) ? trim($_POST['examNameOL']) : '';
                $exam_name_ol = mysqli_real_escape_string($conn, $exam_name_ol);
                for ($ei = 0; $ei <= $edu_counter2; $ei++) {
                    // Get O/L details with isset checks
                    $subject_grade = isset($_POST['subject_OL_' . $ei]) ? trim($_POST['subject_OL_' . $ei]) : '';
                    $subject_grade = mysqli_real_escape_string($conn, $subject_grade);
                    $award = isset($_POST['result_OL_' . $ei]) ? trim($_POST['result_OL_' . $ei]) : '';
                    $award = mysqli_real_escape_string($conn, $award);
                    $exam_year_ol = isset($_POST['year_OL_' . $ei]) ? trim($_POST['year_OL_' . $ei]) : '';
                    $exam_year_ol = mysqli_real_escape_string($conn, $exam_year_ol);

                    // insert educational qualifications
                    if ($exam_name_ol != "" && $exam_year_ol != "") {

                        $sql_educational = "INSERT INTO mst_educational_qualifications (stu_nic,exam_year,exam_name,exm_type,subject_grade,award,stu_id) VALUES ('$dec_nic_no','$exam_year_ol','$exam_name_ol','O/L','$subject_grade','$award',$last_id)";
                        $res_educational = mysqli_query($conn, $sql_educational);

                        if ($res_educational) {
                        } else {
                            $err_code = 2;
                        }
                    } // end if
                } // end for educational O/L

                //english proficiency
                // Check if all English proficiency sections are empty
                $isAllEnglishEmpty = true;
                $sat_result = trim($_POST['sat_result']);

                // Check SAT results
                if (!empty($sat_result)) {
                    $isAllEnglishEmpty = false;
                }

                // Check other English qualifications
                for ($ei = 0; $ei <= $edu_counter3; $ei++) {
                    if (isset($_POST['name_EP_' . $ei]) && trim($_POST['name_EP_' . $ei]) != '') {
                        $isAllEnglishEmpty = false;
                        break;
                    }
                }

                if ($isAllEnglishEmpty) {
                    // If all sections are empty, delete all English proficiency records
                    $sql_english_dl_all = "DELETE FROM mst_english_proficiency WHERE stu_passport_id = '$dec_nic_no'";
                    mysqli_query($conn, $sql_english_dl_all);
                } else {
                    // Process SAT results if present
                    $sat_passing_year = trim($_POST['sat_passing_year']);
                    $sat_result = mysqli_real_escape_string($conn, $sat_result);
                    $sat_passing_year = mysqli_real_escape_string($conn, $sat_passing_year);

                    // Delete non-SAT records to refresh them
                    $sql_english_dl = "DELETE from mst_english_proficiency WHERE stu_passport_id = '$dec_nic_no' AND qualification_type != 'SAT'";
                    $res_english_dl = mysqli_query($conn, $sql_english_dl);
                }
                for ($ei = 0; $ei <= $edu_counter3; $ei++) {

                    $name_EP = isset($_POST['name_EP_' . $ei]) ? trim($_POST['name_EP_' . $ei]) : '';
                    $name_EP = mysqli_real_escape_string($conn, $name_EP);
                    $result_EP = isset($_POST['result_EP_' . $ei]) ? trim($_POST['result_EP_' . $ei]) : '';
                    $result_EP = mysqli_real_escape_string($conn, $result_EP);
                    $exam_year_EP = isset($_POST['year_EP_' . $ei]) ? trim($_POST['year_EP_' . $ei]) : '';
                    $exam_year_EP = mysqli_real_escape_string($conn, $exam_year_EP);

                    // insert educational qualifications
                    if ($name_EP != "") {

                        $sql_english = "INSERT INTO mst_english_proficiency (stu_passport_id,qualification_type,result,year,al_result,stu_id) VALUES ('$dec_nic_no','$name_EP','$result_EP','$exam_year_EP','',$last_id)";
                        $res_english = mysqli_query($conn, $sql_english);
                    } // end if
                }

                if ($sat_result != "") {
                    $sql_sat = "SELECT * from mst_english_proficiency WHERE stu_passport_id = '$dec_nic_no' AND qualification_type = 'SAT'";
                    $res_sat = mysqli_query($conn, $sql_sat);
                    $sat_cnt = mysqli_num_rows($res_sat);

                    $sql_english = "UPDATE mst_english_proficiency SET result = '$sat_result',year = '$sat_passing_year',al_result = '' WHERE stu_passport_id = '$dec_nic_no' AND qualification_type = 'SAT'";
                    $res_english = mysqli_query($conn, $sql_english);
                    if ($sat_cnt > 0) {
                        $sql_english_sat = "UPDATE mst_english_proficiency SET result = '$sat_result',year = '$sat_passing_year',al_result = '' WHERE stu_passport_id = '$dec_nic_no' AND qualification_type = 'SAT'";
                        $res_english_sat = mysqli_query($conn, $sql_english_sat);
                    } else {
                        $result_sat = trim($_POST['sat_result']);
                        $result_sat = mysqli_real_escape_string($conn, $result_sat);
                        $exam_year_sat = trim($_POST['sat_passing_year']);
                        $exam_year_sat = mysqli_real_escape_string($conn, $exam_year_sat);

                        $sql_englishSat = "INSERT INTO mst_english_proficiency (stu_passport_id,qualification_type,result,year,al_result,stu_id) VALUES ('$dec_nic_no','SAT','$result_sat','$exam_year_sat','',$last_id)";
                        $res_englishSat = mysqli_query($conn, $sql_englishSat);
                    }
                }
                //end of english proficiency

                //family_details
                // father details

                // Check if father details should be removed
                if (isset($_POST['fatherName']) && trim($_POST['fatherName']) == "") {
                    // If father name is empty, delete the record
                    $sql_del_father = "DELETE FROM family_details WHERE stu_passport_id = '$dec_nic_no' AND relationship = 'FATHER'";
                    mysqli_query($conn, $sql_del_father);
                } else if (trim($_POST['fatherName']) != "") {
                    $fatherName = trim($_POST['fatherName']);
                    $fatherName = mysqli_real_escape_string($conn, $fatherName);
                    $fatherJob = trim($_POST['fatherJob']);
                    $fatherJob = mysqli_real_escape_string($conn, $fatherJob);
                    $father_employer = trim($_POST['father_employer']);
                    $father_employer = mysqli_real_escape_string($conn, $father_employer);
                    $fatherEmail = trim($_POST['fatherEmail']);
                    $fatherEmail = mysqli_real_escape_string($conn, $fatherEmail);
                    $fatherFixedPhone = trim($_POST['fatherFixedPhone']);
                    $fatherFixedPhone = mysqli_real_escape_string($conn, $fatherFixedPhone);
                    $fatherMobileNo = trim($_POST['fatherMobileNo']);
                    $fatherMobileNo = mysqli_real_escape_string($conn, $fatherMobileNo);

                    // Check if father record exists
                    $check_father = "SELECT * FROM family_details WHERE stu_passport_id = '$dec_nic_no' AND relationship = 'FATHER'";
                    $res_check_father = mysqli_query($conn, $check_father);

                    if (mysqli_num_rows($res_check_father) > 0) {
                        // Update if record exists
                        $sql_father = "UPDATE family_details SET 
                            name = '$fatherName',
                            job = '$fatherJob',
                            email = '$fatherEmail',
                            fixed_phone = '$fatherFixedPhone',
                            mobile_no = '$fatherMobileNo',
                            employey_details = '$father_employer' 
                            WHERE stu_passport_id = '$dec_nic_no' AND relationship = 'FATHER'";
                    } else {
                        // Insert if no record exists
                        $sql_father = "INSERT INTO family_details 
                            (stu_passport_id, name, job, email, fixed_phone, mobile_no, employey_details, relationship, stu_id) 
                            VALUES 
                            ('$dec_nic_no', '$fatherName', '$fatherJob', '$fatherEmail', '$fatherFixedPhone', '$fatherMobileNo', '$father_employer', 'FATHER', $last_id)";
                    }

                    $res_father = mysqli_query($conn, $sql_father);

                    if (!$res_father) {
                        error_log("Error with father details: " . mysqli_error($conn));
                    }
                }

                // mother details
                // Check if mother details should be removed
                if (isset($_POST['motherName']) && trim($_POST['motherName']) == "") {
                    // If mother name is empty, delete the record
                    $sql_del_mother = "DELETE FROM family_details WHERE stu_passport_id = '$dec_nic_no' AND relationship = 'MOTHER'";
                    mysqli_query($conn, $sql_del_mother);
                } else if (trim($_POST['motherName']) != "") {
                    $motherName = trim($_POST['motherName']);
                    $motherName = mysqli_real_escape_string($conn, $motherName);
                    $motherJob = trim($_POST['motherJob']);
                    $motherJob = mysqli_real_escape_string($conn, $motherJob);
                    $mother_employer = trim($_POST['mother_employer']);
                    $mother_employer = mysqli_real_escape_string($conn, $mother_employer);
                    $motherEmail = trim($_POST['motherEmail']);
                    $motherEmail = mysqli_real_escape_string($conn, $motherEmail);
                    $motherFixelPhone = trim($_POST['motherFixelPhone']);
                    $motherFixelPhone = mysqli_real_escape_string($conn, $motherFixelPhone);
                    $motherMobileNo = trim($_POST['motherMobileNo']);
                    $motherMobileNo = mysqli_real_escape_string($conn, $motherMobileNo);

                    // Check if mother record exists
                    $check_mother = "SELECT * FROM family_details WHERE stu_passport_id = '$dec_nic_no' AND relationship = 'MOTHER'";
                    $res_check_mother = mysqli_query($conn, $check_mother);

                    if (mysqli_num_rows($res_check_mother) > 0) {
                        // Update if record exists
                        $sql_mother = "UPDATE family_details SET 
                            name = '$motherName',
                            job = '$motherJob',
                            email = '$motherEmail',
                            fixed_phone = '$motherFixelPhone',
                            mobile_no = '$motherMobileNo',
                            employey_details = '$mother_employer' 
                            WHERE stu_passport_id = '$dec_nic_no' AND relationship = 'MOTHER'";
                    } else {
                        // Insert if no record exists
                        $sql_mother = "INSERT INTO family_details 
                            (stu_passport_id, name, job, email, fixed_phone, mobile_no, employey_details, relationship, stu_id) 
                            VALUES 
                            ('$dec_nic_no', '$motherName', '$motherJob', '$motherEmail', '$motherFixelPhone', '$motherMobileNo', '$mother_employer', 'MOTHER', $last_id)";
                    }

                    $res_mother = mysqli_query($conn, $sql_mother);

                    if (!$res_mother) {
                        error_log("Error with mother details: " . mysqli_error($conn));
                    }
                }

                // guardian details
                // Check if guardian details should be removed
                if (isset($_POST['guardianName']) && trim($_POST['guardianName']) == "") {
                    // If guardian name is empty, delete the record
                    $sql_del_guardian = "DELETE FROM family_details WHERE stu_passport_id = '$dec_nic_no' AND relationship = 'GUARDIAN'";
                    mysqli_query($conn, $sql_del_guardian);
                } else if (trim($_POST['guardianName']) != "") {
                    $guardianName = trim($_POST['guardianName']);
                    $guardianName = mysqli_real_escape_string($conn, $guardianName);
                    $guardianJob = trim($_POST['guardianJob']);
                    $guardianJob = mysqli_real_escape_string($conn, $guardianJob);
                    $guardian_employer = trim($_POST['guardian_employer']);
                    $guardian_employer = mysqli_real_escape_string($conn, $guardian_employer);
                    $guardianEmail = trim($_POST['guardianEmail']);
                    $guardianEmail = mysqli_real_escape_string($conn, $guardianEmail);
                    $guardianFixelPhone = trim($_POST['guardianFixelPhone']);
                    $guardianFixelPhone = mysqli_real_escape_string($conn, $guardianFixelPhone);
                    $guardianMobileNo = trim($_POST['guardianMobileNo']);
                    $guardianMobileNo = mysqli_real_escape_string($conn, $guardianMobileNo);

                    // First check if guardian record exists
                    $check_guardian = "SELECT * FROM family_details WHERE stu_passport_id = '$dec_nic_no' AND relationship = 'GUARDIAN'";
                    $res_check_guardian = mysqli_query($conn, $check_guardian);

                    if (mysqli_num_rows($res_check_guardian) > 0) {
                        // Update if record exists
                        $sql_guardian = "UPDATE family_details SET 
                            name = '$guardianName',
                            job = '$guardianJob',
                            email = '$guardianEmail',
                            fixed_phone = '$guardianFixelPhone',
                            mobile_no = '$guardianMobileNo',
                            employey_details = '$guardian_employer' 
                            WHERE stu_passport_id = '$dec_nic_no' AND relationship = 'GUARDIAN'";
                    } else {
                        // Insert if no record exists
                        $sql_guardian = "INSERT INTO family_details 
                            (stu_passport_id, name, job, email, fixed_phone, mobile_no, employey_details, relationship, stu_id) 
                            VALUES 
                            ('$dec_nic_no', '$guardianName', '$guardianJob', '$guardianEmail', '$guardianFixelPhone', '$guardianMobileNo', '$guardian_employer', 'GUARDIAN', $last_id)";
                    }

                    $res_guardian = mysqli_query($conn, $sql_guardian);

                    if (!$res_guardian) {
                        error_log("Error with guardian details: " . mysqli_error($conn));
                    }
                }
                // end of family details

                // refrees  
                // First check if all referee fields are empty - if so, we'll delete all records
                $allRefereesEmpty =
                    (isset($_POST['refree1_details']) && trim($_POST['refree1_details']) == "") &&
                    (isset($_POST['refree2_details']) && trim($_POST['refree2_details']) == "") &&
                    (isset($_POST['refree_sl_details']) && trim($_POST['refree_sl_details']) == "");

                if ($allRefereesEmpty) {
                    // Delete all referee records for this student
                    $sql_refree_dl_all = "DELETE FROM refree WHERE stu_passport_id = '$dec_nic_no'";
                    mysqli_query($conn, $sql_refree_dl_all);
                } else {
                    // Otherwise proceed with normal update logic
                    $sql_refree_dl = "DELETE FROM refree WHERE stu_passport_id = '$dec_nic_no' AND type ='FOREIGN'";
                    $res_refree1 = mysqli_query($conn, $sql_refree_dl);
                }

                // Check if referee1 details should be removed
                if (isset($_POST['refree1_details']) && trim($_POST['refree1_details']) == "") {
                    // If referee1 details are empty, delete the record
                    $sql_del_ref1 = "DELETE FROM refree WHERE stu_passport_id = '$dec_nic_no' AND type = 'FOREIGN'";
                    mysqli_query($conn, $sql_del_ref1);
                } else if (trim($_POST['refree1_details']) != "") {
                    $refree1_details = trim($_POST['refree1_details']);
                    $refree1_details = mysqli_real_escape_string($conn, $refree1_details);
                    $refree1_phone = trim($_POST['refree1_phone']);
                    $refree1_phone = mysqli_real_escape_string($conn, $refree1_phone);

                    $sql_refree1 = "INSERT INTO refree (stu_passport_id,refree_details,contact_no,type,stu_id) VALUES ('$dec_nic_no','$refree1_details','$refree1_phone','FOREIGN',$last_id)";
                    $res_refree1 = mysqli_query($conn, $sql_refree1);
                }

                // Check if referee2 details should be removed
                if (isset($_POST['refree2_details']) && trim($_POST['refree2_details']) == "") {
                    // If referee2 details are empty, delete the record
                    $sql_del_ref2 = "DELETE FROM refree WHERE stu_passport_id = '$dec_nic_no' AND type = 'FOREIGN'";
                    mysqli_query($conn, $sql_del_ref2);
                } else if (trim($_POST['refree2_details']) != "" && trim($_POST['refree2_phone']) != "") {
                    $refree2_details = trim($_POST['refree2_details']);
                    $refree2_details = mysqli_real_escape_string($conn, $refree2_details);
                    $refree2_phone = trim($_POST['refree2_phone']);
                    $refree2_phone = mysqli_real_escape_string($conn, $refree2_phone);

                    $sql_refree2 = "INSERT INTO refree (stu_passport_id,refree_details,contact_no,type,stu_id) VALUES ('$dec_nic_no','$refree2_details','$refree2_phone','FOREIGN',$last_id)";
                    $res_refree2 = mysqli_query($conn, $sql_refree2);
                }

                // Check if Sri Lankan referee details should be removed
                if (isset($_POST['refree_sl_details']) && trim($_POST['refree_sl_details']) == "") {
                    // If Sri Lankan referee details are empty, delete the record
                    $sql_del_sl_referee = "DELETE FROM refree WHERE stu_passport_id = '$dec_nic_no' AND type = 'SRILANKA'";
                    mysqli_query($conn, $sql_del_sl_referee);
                } else if (trim($_POST['refree_sl_details']) != "") {
                    $refree_sl_details = trim($_POST['refree_sl_details']);
                    $refree_sl_details = mysqli_real_escape_string($conn, $refree_sl_details);
                    $refree_sl_phone = trim($_POST['refree_sl_phone']);
                    $refree_sl_phone = mysqli_real_escape_string($conn, $refree_sl_phone);

                    // First check if a record exists
                    $check_sl_referee = "SELECT * FROM refree WHERE stu_passport_id = '$dec_nic_no' AND type = 'SRILANKA'";
                    $res_check_sl = mysqli_query($conn, $check_sl_referee);

                    if (mysqli_num_rows($res_check_sl) > 0) {
                        // Update if record exists
                        $sql_refree_sl = "UPDATE refree SET refree_details = '$refree_sl_details', contact_no = '$refree_sl_phone' WHERE stu_passport_id = '$dec_nic_no' AND type = 'SRILANKA'";
                    } else {
                        // Insert if no record exists
                        $sql_refree_sl = "INSERT INTO refree (stu_passport_id, refree_details, contact_no, type, stu_id) VALUES ('$dec_nic_no', '$refree_sl_details', '$refree_sl_phone', 'SRILANKA', $last_id)";
                    }
                    $res_refree_sl = mysqli_query($conn, $sql_refree_sl);

                    if (!$res_refree_sl) {
                        error_log("Error with Sri Lankan referee: " . mysqli_error($conn));
                    }
                }
                // end of refree



                // Validate required fields
                function validateField($value, $fieldName)
                {
                    $trimmed = trim($value);
                    if (empty($trimmed)) {
                        return "$fieldName is required.";
                    }
                    return "";
                }

                // Validate email format
                function validateEmail($email)
                {
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        return "Invalid email format.";
                    }
                    return "";
                }

                // Validate phone number format
                function validatePhone($phone)
                {
                    // Remove any non-digit characters
                    $phone = preg_replace('/[^0-9]/', '', $phone);

                    // Check if the number has a valid length (between 8 and 15 digits)
                    if (strlen($phone) < 8 || strlen($phone) > 15) {
                        return "Phone number must be between 8 and 15 digits.";
                    }
                    return "";
                }

                // Validate photo if uploaded
                function validatePhoto($photo)
                {
                    if (isset($photo['name']) && !empty($photo['name'])) {
                        $allowedTypes = array('image/jpeg', 'image/png');
                        $maxSize = 2 * 1024 * 1024; // 2MB

                        if ($photo['size'] > $maxSize) {
                            return "Photo size should not exceed 2MB.";
                        }

                        if (!in_array($photo['type'], $allowedTypes)) {
                            return "Photo must be in JPG or PNG format.";
                        }
                    }
                    return "";
                }

                if ($err_code == 0) {
                    // Validate required fields
                    $errors = array();

                    $requiredFields = array(
                        'inputFullname' => 'Full Name',
                        'inputNic' => 'NIC/Passport Number',
                        'inputEmailAddress' => 'Email Address',
                        'inputCourse' => 'Course'
                    );

                    foreach ($requiredFields as $field => $label) {
                        if (isset($_POST[$field])) {
                            $error = validateField($_POST[$field], $label);
                            if (!empty($error)) {
                                $errors[] = $error;
                            }
                        } else {
                            $errors[] = "$label is required.";
                        }
                    }

                    // Validate email
                    if (isset($_POST['email'])) {
                        $emailError = validateEmail($_POST['email']);
                        if (!empty($emailError)) {
                            $errors[] = $emailError;
                        }
                    }

                    // Validate phone numbers
                    /* $phoneFields = [
                        'fatherMobileNo' => 'Father\'s mobile number',
                        'motherMobileNo' => 'Mother\'s mobile number',
                        'guardianMobileNo' => 'Guardian\'s mobile number'
                    ];

                    foreach ($phoneFields as $field => $label) {
                        if (!empty($_POST[$field])) {
                            $phoneError = validatePhone($_POST[$field]);
                            if (!empty($phoneError)) {
                                $errors[] = "$label: $phoneError";
                            }
                        }
                    } */

                    // Validate photo if present
                    if (isset($_FILES['Photo'])) {
                        $photoError = validatePhoto($_FILES['Photo']);
                        if (!empty($photoError)) {
                            $errors[] = $photoError;
                        }
                    }

                    if (empty($errors)) {
                        $sql_updt = "UPDATE mst_personal_details SET application_confirm_status = 'Y' , payment_status = 'PENDING' WHERE nic_no = ? ";
                        $stmt = $conn->prepare($sql_updt);
                        $stmt->bind_param("s", $dec_nic_no);
                        $res_updt = $stmt->execute();
                    } else {
                        $err_code = 9; // New error code for validation errors
                        $response = array(
                            'status' => 'error',
                            'message' => implode("\n", $errors)
                        );
                        echo json_encode($response);
                        exit;
                    }
                    if ($res_updt) {
                        $response = array(
                            'status' => 'success',
                            'message' => 'Data updated successfully!',
                            'passport_no' => $dec_nic_no
                        );
                    } else {
                        $err_code = 8;
                        $response = array(
                            'status' => 'error',
                            'message' => 'Failed to update application status. Error code: ' . $err_code
                        );
                    }
                } else {
                    $response = array(
                        'status' => 'error',
                        'message' => 'Error code: ' . $err_code
                    );
                }

                // Send JSON response
                echo json_encode($response);
                exit;
            } else {
                $response = array(
                    'status' => 'error',
                    'message' => 'Failed to update personal details'
                );
                echo json_encode($response);
                exit;
            } // end if($res_personal_data)

        } // end if($err_code == 1)
    }
} else {
    //header('Location:index.php?errcd=1&nic='.$dec_nic_no);
    echo 'nic:' . $enc_nic_no;
}
