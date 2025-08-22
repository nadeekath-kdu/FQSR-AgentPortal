<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION)) {
    session_start();
}

header('Content-Type: application/json');

require_once '../config/dbcon.php';
require_once '../config/iv_key.php';
require_once '../config/mystore_func.php';
require_once '../config/global.php';

// Debug logging
error_log('POST data received: ' . print_r($_POST, true));
error_log('FILES data received: ' . print_r($_FILES, true));

function validateFormData($data, $files) {
    $errors = array();
    
    // Required fields
    $requiredFields = array(
        'inputFullname' => 'Full Name',
        'inputNic' => 'NIC/Passport Number',
        'inputEmailAddress' => 'Email Address',
        'inputCourse' => 'Course'
    );

    foreach ($requiredFields as $field => $label) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            $errors[] = "$label is required.";
        }
    }

    // Email validation
    if (isset($data['inputEmailAddress']) && !empty($data['inputEmailAddress'])) {
        if (!filter_var($data['inputEmailAddress'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }
    }

    // Photo validation
    if (isset($files['Photo']) && !empty($files['Photo']['name'])) {
        $allowedTypes = array('image/jpeg', 'image/png');
        $maxSize = 2 * 1024 * 1024; // 2MB

        if ($files['Photo']['size'] > $maxSize) {
            $errors[] = "Photo size should not exceed 2MB.";
        }
        if (!in_array($files['Photo']['type'], $allowedTypes)) {
            $errors[] = "Photo must be in JPG or PNG format.";
        }
    }

    return $errors;
}

// Main process
$conn = $con;
if (!$conn) {
    echo json_encode(array(
        'status' => 'error',
        'message' => 'Database connection failed'
    ));
    exit;
}

// Get and validate NIC/Passport
$enc_nic_no = isset($_POST['inputNic']) ? $_POST['inputNic'] : '';
$dec_nic_no = $enc_nic_no; // If encryption is needed, use decryptStr()

if (empty($dec_nic_no)) {
    echo json_encode(array(
        'status' => 'error',
        'message' => 'NIC/Passport number is required'
    ));
    exit;
}

// Initialize variables
$last_id = 0;
$media_source_name = "Other";
$sql_personal_data = "";
date_default_timezone_set('Asia/Colombo');

function validateForm($conn, $dec_nic_no) {
    $errors = array();

    // Validate required fields
    $requiredFields = array(
        'inputFullname' => 'Full Name',
        'inputNic' => 'NIC/Passport Number',
        'inputEmailAddress' => 'Email Address',
        'inputCourse' => 'Course'
    );

    foreach ($requiredFields as $field => $label) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            $errors[] = "$label is required.";
        }
    }

    // Validate email
    if (isset($_POST['inputEmailAddress']) && !empty($_POST['inputEmailAddress'])) {
        if (!filter_var($_POST['inputEmailAddress'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }
    }

    // Validate photo if present
    if (isset($_FILES['Photo']) && !empty($_FILES['Photo']['name'])) {
        $allowedTypes = array('image/jpeg', 'image/png');
        $maxSize = 2 * 1024 * 1024; // 2MB

        if ($_FILES['Photo']['size'] > $maxSize) {
            $errors[] = "Photo size should not exceed 2MB.";
        }
        if (!in_array($_FILES['Photo']['type'], $allowedTypes)) {
            $errors[] = "Photo must be in JPG or PNG format.";
        }
    }

    return $errors;
}

// Main process starts here
if (!empty($_POST['inputNic'])) {
    $inputNic = trim($_POST['inputNic']);
    $inputNic = mysqli_real_escape_string($conn, $inputNic);
    $enc_nic_no = $inputNic;
    $dec_nic_no = $enc_nic_no;
    $dec_nic_no = mysqli_real_escape_string($conn, $dec_nic_no);

    // Check if applicant exists
    $sql_chk = "SELECT * FROM mst_personal_details WHERE nic_no = ? AND application_confirm_status = 'Y'";
    $stmt = $conn->prepare($sql_chk);
    $stmt->bind_param("s", $dec_nic_no);
    $stmt->execute();
    $result = $stmt->get_result();
    $applicant_cnt = $result->num_rows;

    if ($applicant_cnt > 0) {
        $result_applicant = $result->fetch_array();
        $last_id = $result_applicant['applicant_id'];
        $oldphoto = $result_applicant['Photo'];
        // Initialize variables
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

            //save profile picture abooutus folder          
            $uploaddir = "../profile/";
            $Photo = $oldphoto; // Default to existing photo
            
            // Check if a new photo was uploaded
            if (isset($_FILES["Photo"]) && !empty($_FILES["Photo"]["name"]) && $_FILES["Photo"]["error"] == 0) {
                $extension = pathinfo($_FILES["Photo"]["name"], PATHINFO_EXTENSION);
                $uploadfile = $uploaddir . $dec_nic_no . '.' . $extension;
                
                // Only attempt to move file if upload was successful
                if (move_uploaded_file($_FILES["Photo"]["tmp_name"], $uploadfile)) {
                    $Photo = $dec_nic_no . '.' . $extension;
                    error_log("File uploaded successfully");
                } else {
                    error_log("Photo Upload failed");
                }
            }
            // end save profile picture
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
            
            // Update personal details
            $sql_personal_data = "UPDATE mst_personal_details SET 
                course_name = '$apply_course',
                course_code = '$apply_course_code',
                intake = '$intake_yr',
                stu_title = '$stu_title',
                stu_fullname = '$stu_fullname',
                stu_name_initials = '$stu_initialname',
                stu_dob = '$stu_dob',
                stu_gender = '$stu_gender',
                stu_citizenship = '$stu_citizenship',
                civil_status = '$stu_civilstats',
                stu_permenant_address = '$stu_permenant_addr',
                stu_email = '$stu_email',
                application_submit_dt = '$cur_dt',
                media_source_name = '$media_source_name',
                doc_upload_link = '$doc_upld_link',
                birth_country = '$stu_birth_country',
                period_study_abroad = '$period_study_abroad',
                eligibility_uni_admision = '$eligibility_uni_admision',
                other_qualification = '$other_qualification',
                fund = '$fund',
                citizenship_type = '$citizenship_type',
                citizenship_1 = '$citizenship1',
                citizenship_2 = '$citizenship2',
                AL_sitting_country = '$country_AL',
                nameEduAgent = '$nameEduAgent',
                isEduAgent = '$eduAgent',
                photo = '$Photo' 
                WHERE nic_no = '$dec_nic_no'";
                
            if (!mysqli_query($conn, $sql_personal_data)) {
                error_log("Failed to update personal details: " . mysqli_error($conn));
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'Failed to update personal details'
                ));
                exit;
            }

            $test_var = "";

            if (!$res_personal_data) {
                $response = array(
                    'status' => 'error',
                    'message' => 'Failed to update personal details'
                );
                echo json_encode($response);
                exit;
            }

            // If personal data update was successful, continue with other updates
            $enc_last_id = $last_id;
            $edu_counter = isset($_POST['edurowcnt']) ? $_POST['edurowcnt'] : 0;
            $edu_counter2 = isset($_POST['edurowcnt2']) ? $_POST['edurowcnt2'] : 0;
            $edu_counter3 = isset($_POST['edurowcnt3']) ? $_POST['edurowcnt3'] : 0;

                $sql_educational_dl = "DELETE from mst_educational_qualifications WHERE stu_nic = '$dec_nic_no' AND exm_type = 'A/L'";
                $res_educational_dl = mysqli_query($conn, $sql_educational_dl);
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
                    if ($subject_grade != "" || $award != "" || $exam_year_al != "") {
                        // If any of the fields are filled, insert the record
                        $sql_educational = "INSERT INTO mst_educational_qualifications (stu_nic,exam_year,exam_name,exm_type,subject_grade,award,stu_id) VALUES ('$dec_nic_no','$exam_year_al','$exam_name_al','A/L','$subject_grade','$award',$last_id)";
                        $res_educational = mysqli_query($conn, $sql_educational);
                        error_log("Inserting A/L record: " . $sql_educational);
                        if (!$res_educational) {
                            error_log("Failed to insert A/L record: " . mysqli_error($conn));
                            $err_code = 2;
                        }
                    } // end if
                } // end for educational A/L

                $sql_educational_dl2 = "DELETE from mst_educational_qualifications WHERE stu_nic = '$dec_nic_no' AND exm_type = 'O/L'";
                $res_educational_dl2 = mysqli_query($conn, $sql_educational_dl2);
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
                    if ($subject_grade != "" || $award != "" || $exam_year_ol != "") {
                        // If any of the fields are filled, insert the record
                        $sql_educational = "INSERT INTO mst_educational_qualifications (stu_nic,exam_year,exam_name,exm_type,subject_grade,award,stu_id) VALUES ('$dec_nic_no','$exam_year_ol','$exam_name_ol','O/L','$subject_grade','$award',$last_id)";
                        $res_educational = mysqli_query($conn, $sql_educational);
                        error_log("Inserting O/L record: " . $sql_educational);
                        if (!$res_educational) {
                            error_log("Failed to insert O/L record: " . mysqli_error($conn));
                            $err_code = 2;
                        }
                    } // end if
                } // end for educational O/L

                //english proficiency
                $sat_result = trim($_POST['sat_result']);
                $sat_passing_year = trim($_POST['sat_passing_year']);
                $sat_result = mysqli_real_escape_string($conn, $sat_result);
                $sat_passing_year = mysqli_real_escape_string($conn, $sat_passing_year);
                $sql_english_dl = "DELETE from mst_english_proficiency WHERE stu_passport_id = '$dec_nic_no' AND qualification_type != 'SAT'";
                $res_english_dl = mysqli_query($conn, $sql_english_dl);
                for ($ei = 0; $ei <= $edu_counter3; $ei++) {

                    $name_EP = isset($_POST['name_EP_' . $ei]) ? trim($_POST['name_EP_' . $ei]) : '';
                    $name_EP = mysqli_real_escape_string($conn, $name_EP);
                    $result_EP = isset($_POST['result_EP_' . $ei]) ? trim($_POST['result_EP_' . $ei]) : '';
                    $result_EP = mysqli_real_escape_string($conn, $result_EP);
                    $exam_year_EP = isset($_POST['year_EP_' . $ei]) ? trim($_POST['year_EP_' . $ei]) : '';
                    $exam_year_EP = mysqli_real_escape_string($conn, $exam_year_EP);

                    // insert educational qualifications
                    if ($name_EP != "" || $result_EP != "" || $exam_year_EP != "") {
                        // Check if record exists
                        $check_sql = "SELECT * FROM mst_english_proficiency WHERE stu_passport_id = '$dec_nic_no' AND qualification_type = '$name_EP'";
                        $check_result = mysqli_query($conn, $check_sql);
                        
                        if (mysqli_num_rows($check_result) > 0) {
                            // Update existing record
                            $sql_english = "UPDATE mst_english_proficiency SET result = '$result_EP', year = '$exam_year_EP' WHERE stu_passport_id = '$dec_nic_no' AND qualification_type = '$name_EP'";
                        } else {
                            // Insert new record
                            $sql_english = "INSERT INTO mst_english_proficiency (stu_passport_id,qualification_type,result,year,al_result,stu_id) VALUES ('$dec_nic_no','$name_EP','$result_EP','$exam_year_EP','',$last_id)";
                        }
                        
                        $res_english = mysqli_query($conn, $sql_english);
                        if (!$res_english) {
                            error_log("Failed to save English proficiency record: " . mysqli_error($conn));
                        }
                    } // end if
                }

                // Handle SAT scores
                if (isset($_POST['sat_result']) || isset($_POST['sat_passing_year'])) {
                    $sat_result = isset($_POST['sat_result']) ? trim($_POST['sat_result']) : '';
                    $sat_passing_year = isset($_POST['sat_passing_year']) ? trim($_POST['sat_passing_year']) : '';
                    
                    if ($sat_result != "" || $sat_passing_year != "") {
                        $sat_result = mysqli_real_escape_string($conn, $sat_result);
                        $sat_passing_year = mysqli_real_escape_string($conn, $sat_passing_year);
                        
                        // Check if SAT record exists
                        $sql_sat = "SELECT * FROM mst_english_proficiency WHERE stu_passport_id = '$dec_nic_no' AND qualification_type = 'SAT'";
                        $res_sat = mysqli_query($conn, $sql_sat);
                        
                        if (mysqli_num_rows($res_sat) > 0) {
                            // Update existing SAT record
                            $sql_english_sat = "UPDATE mst_english_proficiency SET result = '$sat_result', year = '$sat_passing_year', al_result = '' WHERE stu_passport_id = '$dec_nic_no' AND qualification_type = 'SAT'";
                        } else {
                            // Insert new SAT record
                            $sql_english_sat = "INSERT INTO mst_english_proficiency (stu_passport_id, qualification_type, result, year, al_result, stu_id) VALUES ('$dec_nic_no', 'SAT', '$sat_result', '$sat_passing_year', '', $last_id)";
                        }
                        
                        $res_english_sat = mysqli_query($conn, $sql_english_sat);
                        if (!$res_english_sat) {
                            error_log("Failed to save SAT score: " . mysqli_error($conn));
                        }
                    }
                }

                                // Process all form data updates first
                if (!$res_personal_data || !$res_educational || !$res_english) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Failed to update one or more sections'
                    ]);
                    exit;
                }

                // Final validation
                $errors = validateFormData($_POST, $_FILES);
                if (!empty($errors)) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => implode("\n", $errors)
                    ]);
                    exit;
                }

                // Update application status
                $sql_updt = "UPDATE mst_personal_details SET application_confirm_status = 'Y', payment_status = 'PENDING' WHERE nic_no = ?";
                $stmt = $conn->prepare($sql_updt);
                $stmt->bind_param("s", $dec_nic_no);
                $res_updt = $stmt->execute();

                if (!$res_updt) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Failed to update application status'
                    ]);
                    exit;
                }

                echo json_encode([
                    'status' => 'success',
                    'message' => 'Data updated successfully!',
                    'passport_no' => $dec_nic_no
                ]);
                exit;
                //end of validation and status update

                //family_details
                // father details


                if (isset($_POST['fatherName']) && trim($_POST['fatherName']) != "") {
                    $fatherName = trim($_POST['fatherName']);
                    $fatherName = mysqli_real_escape_string($conn, $fatherName);
                    $fatherJob = isset($_POST['fatherJob']) ? trim($_POST['fatherJob']) : '';
                    $fatherJob = mysqli_real_escape_string($conn, $fatherJob);
                    $father_employer = isset($_POST['father_employer']) ? trim($_POST['father_employer']) : '';
                    $father_employer = mysqli_real_escape_string($conn, $father_employer);
                    $fatherEmail = isset($_POST['fatherEmail']) ? trim($_POST['fatherEmail']) : '';
                    $fatherEmail = mysqli_real_escape_string($conn, $fatherEmail);
                    $fatherFixedPhone = isset($_POST['fatherFixedPhone']) ? trim($_POST['fatherFixedPhone']) : '';
                    $fatherFixedPhone = mysqli_real_escape_string($conn, $fatherFixedPhone);
                    $fatherMobileNo = isset($_POST['fatherMobileNo']) ? trim($_POST['fatherMobileNo']) : '';
                    $fatherMobileNo = mysqli_real_escape_string($conn, $fatherMobileNo);

                    // Check if record exists
                    $check_sql = "SELECT * FROM family_details WHERE stu_passport_id = '$dec_nic_no' AND relationship = 'FATHER'";
                    $check_result = mysqli_query($conn, $check_sql);
                    
                    if (mysqli_num_rows($check_result) > 0) {
                        // Update existing record
                        $sql_father = "UPDATE family_details SET name = '$fatherName',job = '$fatherJob',email = '$fatherEmail',fixed_phone = '$fatherFixedPhone',mobile_no = '$fatherMobileNo',employey_details = '$father_employer' WHERE stu_passport_id = '$dec_nic_no' AND relationship = 'FATHER'";
                    } else {
                        // Insert new record
                        $sql_father = "INSERT INTO family_details (stu_passport_id, relationship, name, job, email, fixed_phone, mobile_no, employey_details) VALUES ('$dec_nic_no', 'FATHER', '$fatherName', '$fatherJob', '$fatherEmail', '$fatherFixedPhone', '$fatherMobileNo', '$father_employer')";
                    }
                    
                    $res_father = mysqli_query($conn, $sql_father);
                    if (!$res_father) {
                        error_log("Failed to save father's details: " . mysqli_error($conn));
                    }
                }

                // mother details
                if (isset($_POST['motherName']) && trim($_POST['motherName']) != "") {
                    $motherName = trim($_POST['motherName']);
                    $motherName = mysqli_real_escape_string($conn, $motherName);
                    $motherJob = isset($_POST['motherJob']) ? trim($_POST['motherJob']) : '';
                    $motherJob = mysqli_real_escape_string($conn, $motherJob);
                    $mother_employer = isset($_POST['mother_employer']) ? trim($_POST['mother_employer']) : '';
                    $mother_employer = mysqli_real_escape_string($conn, $mother_employer);
                    $motherEmail = isset($_POST['motherEmail']) ? trim($_POST['motherEmail']) : '';
                    $motherEmail = mysqli_real_escape_string($conn, $motherEmail);
                    $motherFixelPhone = isset($_POST['motherFixelPhone']) ? trim($_POST['motherFixelPhone']) : '';
                    $motherFixelPhone = mysqli_real_escape_string($conn, $motherFixelPhone);
                    $motherMobileNo = isset($_POST['motherMobileNo']) ? trim($_POST['motherMobileNo']) : '';
                    $motherMobileNo = mysqli_real_escape_string($conn, $motherMobileNo);

                    // Check if record exists
                    $check_sql = "SELECT * FROM family_details WHERE stu_passport_id = '$dec_nic_no' AND relationship = 'MOTHER'";
                    $check_result = mysqli_query($conn, $check_sql);
                    
                    if (mysqli_num_rows($check_result) > 0) {
                        // Update existing record
                        $sql_mother = "UPDATE family_details SET name = '$motherName',job = '$motherJob',email = '$motherEmail',fixed_phone = '$motherFixelPhone',mobile_no = '$motherMobileNo',employey_details = '$mother_employer' WHERE stu_passport_id = '$dec_nic_no' AND relationship = 'MOTHER'";
                    } else {
                        // Insert new record
                        $sql_mother = "INSERT INTO family_details (stu_passport_id, relationship, name, job, email, fixed_phone, mobile_no, employey_details) VALUES ('$dec_nic_no', 'MOTHER', '$motherName', '$motherJob', '$motherEmail', '$motherFixelPhone', '$motherMobileNo', '$mother_employer')";
                    }
                    
                    $res_mother = mysqli_query($conn, $sql_mother);
                    if (!$res_mother) {
                        error_log("Failed to save mother's details: " . mysqli_error($conn));
                    }
                }

                // guardian details
                if (isset($_POST['guardianName']) && trim($_POST['guardianName']) != "") {
                    $guardianName = trim($_POST['guardianName']);
                    $guardianName = mysqli_real_escape_string($conn, $guardianName);
                    $guardianJob = isset($_POST['guardianJob']) ? trim($_POST['guardianJob']) : '';
                    $guardianJob = mysqli_real_escape_string($conn, $guardianJob);
                    $guardian_employer = isset($_POST['guardian_employer']) ? trim($_POST['guardian_employer']) : '';
                    $guardian_employer = mysqli_real_escape_string($conn, $guardian_employer);
                    $guardianEmail = isset($_POST['guardianEmail']) ? trim($_POST['guardianEmail']) : '';
                    $guardianEmail = mysqli_real_escape_string($conn, $guardianEmail);
                    $guardianFixelPhone = isset($_POST['guardianFixelPhone']) ? trim($_POST['guardianFixelPhone']) : '';
                    $guardianFixelPhone = mysqli_real_escape_string($conn, $guardianFixelPhone);
                    $guardianMobileNo = isset($_POST['guardianMobileNo']) ? trim($_POST['guardianMobileNo']) : '';
                    $guardianMobileNo = mysqli_real_escape_string($conn, $guardianMobileNo);

                    // Check if record exists
                    $check_sql = "SELECT * FROM family_details WHERE stu_passport_id = '$dec_nic_no' AND relationship = 'GUARDIAN'";
                    $check_result = mysqli_query($conn, $check_sql);
                    
                    if (mysqli_num_rows($check_result) > 0) {
                        // Update existing record
                        $sql_guardian = "UPDATE family_details SET name = '$guardianName',job = '$guardianJob',email = '$guardianEmail',fixed_phone = '$guardianFixelPhone',mobile_no = '$guardianMobileNo',employey_details = '$guardian_employer' WHERE stu_passport_id = '$dec_nic_no' AND relationship = 'GUARDIAN'";
                    } else {
                        // Insert new record
                        $sql_guardian = "INSERT INTO family_details (stu_passport_id, relationship, name, job, email, fixed_phone, mobile_no, employey_details) VALUES ('$dec_nic_no', 'GUARDIAN', '$guardianName', '$guardianJob', '$guardianEmail', '$guardianFixelPhone', '$guardianMobileNo', '$guardian_employer')";
                    }
                    
                    $res_guardian = mysqli_query($conn, $sql_guardian);
                    if (!$res_guardian) {
                        error_log("Failed to save guardian's details: " . mysqli_error($conn));
                    }
                }
                // end of family details

                // refrees  
                $sql_refree_dl = "DELETE FROM refree WHERE stu_passport_id = '$dec_nic_no' AND type ='FOREIGN'";
                $res_refree1 = mysqli_query($conn, $sql_refree_dl);
                if (trim($_POST['refree1_details'] != "")) {
                    $refree1_details = trim($_POST['refree1_details']);
                    $refree1_details = mysqli_real_escape_string($conn, $refree1_details);
                    $refree1_phone = trim($_POST['refree1_phone']);
                    $refree1_phone = mysqli_real_escape_string($conn, $refree1_phone);

                    $sql_refree1 = "INSERT INTO refree (stu_passport_id,refree_details,contact_no,type,stu_id) VALUES ('$dec_nic_no','$refree1_details','$refree1_phone','FOREIGN',$last_id)";
                    $res_refree1 = mysqli_query($conn, $sql_refree1);
                }

                if (trim($_POST['refree2_details'] != "") && trim($_POST['refree2_phone'] != "")) {
                    $refree2_details = trim($_POST['refree2_details']);
                    $refree2_details = mysqli_real_escape_string($conn, $refree2_details);
                    $refree2_phone = trim($_POST['refree2_phone']);
                    $refree2_phone = mysqli_real_escape_string($conn, $refree2_phone);


                    $sql_refree2 = "INSERT INTO refree (stu_passport_id,refree_details,contact_no,type,stu_id) VALUES ('$dec_nic_no','$refree2_details','$refree2_phone','FOREIGN',$last_id)";
                    $res_refree2 = mysqli_query($conn, $sql_refree2);
                }

                if (trim($_POST['refree_sl_details'] != "")) {
                    $refree_sl_details = trim($_POST['refree_sl_details']);
                    $refree_sl_details = mysqli_real_escape_string($conn, $refree_sl_details);
                    $refree_sl_phone = trim($_POST['refree_sl_phone']);
                    $refree_sl_phone = mysqli_real_escape_string($conn, $refree_sl_phone);

                    $test_var = $refree_sl_details;
                    $sql_refree_sl = "UPDATE refree SET refree_details = '$refree_sl_details',contact_no = '$refree_sl_phone' WHERE stu_passport_id = '$dec_nic_no' AND type = 'SRILANKA'";
                    $res_refree_sl = mysqli_query($conn, $sql_refree_sl);
                }
                // end of refree



                // Perform final validation
                $errors = array();
                $requiredFields = array(
                    'inputFullname' => 'Full Name',
                    'inputNic' => 'NIC/Passport Number',
                    'inputEmailAddress' => 'Email Address',
                    'inputCourse' => 'Course'
                );

                foreach ($requiredFields as $field => $label) {
                    if (isset($_POST[$field])) {
                        if (empty(trim($_POST[$field]))) {
                            $errors[] = "$label is required.";
                        }
                    } else {
                        $errors[] = "$label is required.";
                    }
                }

                // Validate email
                if (isset($_POST['inputEmailAddress'])) {
                    if (!filter_var($_POST['inputEmailAddress'], FILTER_VALIDATE_EMAIL)) {
                        $errors[] = "Invalid email format.";
                    }
                }

                // Validate photo if present
                // Final validation of all data
                $errors = array();
                
                // Required fields validation
                $requiredFields = array(
                    'inputFullname' => 'Full Name',
                    'inputNic' => 'NIC/Passport Number',
                    'inputEmailAddress' => 'Email Address',
                    'inputCourse' => 'Course'
                );

                foreach ($requiredFields as $field => $label) {
                    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
                        $errors[] = "$label is required.";
                    }
                }

                // Email validation
                if (isset($_POST['inputEmailAddress']) && !empty($_POST['inputEmailAddress'])) {
                    if (!filter_var($_POST['inputEmailAddress'], FILTER_VALIDATE_EMAIL)) {
                        $errors[] = "Invalid email format.";
                    }
                }

                // Photo validation if present
                if (isset($_FILES['Photo']) && !empty($_FILES['Photo']['name'])) {
                    $allowedTypes = array('image/jpeg', 'image/png');
                    $maxSize = 2 * 1024 * 1024; // 2MB

                    if ($_FILES['Photo']['size'] > $maxSize) {
                        $errors[] = "Photo size should not exceed 2MB.";
                    }
                    if (!in_array($_FILES['Photo']['type'], $allowedTypes)) {
                        $errors[] = "Photo must be in JPG or PNG format.";
                    }
                }

                // Process validation results
                if (!empty($errors)) {
                    echo json_encode(array(
                        'status' => 'error',
                        'message' => implode("\n", $errors)
                    ));
                    exit;
                }

                // Update application status after successful validation
                $sql_updt = "UPDATE mst_personal_details SET application_confirm_status = 'Y', payment_status = 'PENDING' WHERE nic_no = ?";
                $stmt = $conn->prepare($sql_updt);
                $stmt->bind_param("s", $dec_nic_no);
                $res_updt = $stmt->execute();

                if ($res_updt) {
                    echo json_encode(array(
                        'status' => 'success',
                        'message' => 'Data updated successfully!',
                        'passport_no' => $dec_nic_no
                    ));
                } else {
                    echo json_encode(array(
                        'status' => 'error',
                        'message' => 'Failed to update application status'
                    ));
                }
                exit;
            }
        }
    }
}