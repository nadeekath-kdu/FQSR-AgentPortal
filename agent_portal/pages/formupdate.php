<?php
require_once '../config/dbcon.php';
require_once 'config/iv_key.php';
require_once 'config/mystore_func.php'; //local

$conn = $con;
$formStatus = '';
$db_connection = ''; // edit 2022-08-15 change conn --> db_connection

$enc_nic_no = "";
$dec_nic_no = "";
$err_code = 0;
$msg = "";
$app_confirm_status = 0;
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

    $dec_nic_no = decryptStr($enc_nic_no, ENCRYPT_METHOD, WSECRET_KEY, WSECRET_IV);    //local

    //$dec_nic_no = $enc_nic_no; //local
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

            //save profile picture abooutus floder          
            $uploaddir = "profile/";
            $temp = explode(".", $_FILES["file"]["name"]);
            //$uploadfile = $uploaddir. basename($_FILES["Photo"]["name"]);
            $extension = pathinfo($_FILES["Photo"]["name"], PATHINFO_EXTENSION);
            $uploadfile = $uploaddir . $dec_nic_no . '.' . $extension;
            if ($_FILES['Photo']['error'] != 0) {

                $Photo = $oldphoto;
            } else {

                $Photo = $dec_nic_no . '.' . $extension;
            }

            if (move_uploaded_file($_FILES["Photo"]["tmp_name"], $uploadfile)) {
                echo "File is valid, and was successfully uploaded.\n";
            } else {
                echo "Photo Upload failed";
            } // end save profile picture
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
            $sql_personal_data = "UPDATE mst_personal_details SET course_name= '$apply_course',course_code= '$apply_course_code',intake = '$intake_yr',stu_title = '$stu_title',stu_fullname = '$stu_fullname',stu_name_initials = '$stu_initialname',stu_dob = '$stu_dob',stu_gender = '$stu_gender',stu_citizenship = '$stu_citizenship',civil_status = '$stu_civilstats',stu_permenant_address = '$stu_permenant_addr',stu_email = '$stu_email',application_submit_dt = '$cur_dt',media_source_name = '$media_source_name',doc_upload_link = '$doc_upld_link',birth_country = '$stu_birth_country',period_study_abroad = '$period_study_abroad',eligibility_uni_admision = '$eligibility_uni_admision',other_qualification = '$other_qualification',fund = '$fund',citizenship_type = '$citizenship_type',citizenship_1 = '$citizenship1',citizenship_2 = '$citizenship2',AL_sitting_country = '$country_AL',nameEduAgent = '$nameEduAgent',isEduAgent = '$eduAgent',photo = '$Photo' WHERE nic_no = '$dec_nic_no'";
            $res_personal_data = mysqli_query($conn, $sql_personal_data);

            $test_var = "";

            if ($res_personal_data) {
                //$last_id = mysqli_insert_id($conn);
                //$enc_last_id = encryptStoreStr($last_id,ENCRYPT_METHOD,WSECRET_KEY,WSECRET_IV);
                $enc_last_id = $last_id;
                $edu_counter = $_POST['edurowcnt'];
                $edu_counter2 = $_POST['edurowcnt2'];
                $edu_counter3 = $_POST['edurowcnt3'];

                $sql_educational_dl = "DELETE from mst_educational_qualifications WHERE stu_nic = '$dec_nic_no' AND exm_type = 'A/L'";
                $res_educational_dl = mysqli_query($conn, $sql_educational_dl);
                $exam_name_al = trim($_POST['examNameAL']);
                $exam_name_al = mysqli_real_escape_string($conn, $exam_name_al);
                for ($ei = 0; $ei <= $edu_counter; $ei++) {

                    $subject_grade = trim($_POST['subject_AL_' . $ei]);
                    $subject_grade = mysqli_real_escape_string($conn, $subject_grade);
                    $award = trim($_POST['result_AL_' . $ei]);
                    $award = mysqli_real_escape_string($conn, $award);
                    $exam_year_al = trim($_POST['year_AL_' . $ei]);
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

                $sql_educational_dl2 = "DELETE from mst_educational_qualifications WHERE stu_nic = '$dec_nic_no' AND exm_type = 'O/L'";
                $res_educational_dl2 = mysqli_query($conn, $sql_educational_dl2);
                $exam_name_ol = trim($_POST['examNameOL']);
                $exam_name_ol = mysqli_real_escape_string($conn, $exam_name_ol);
                for ($ei = 0; $ei <= $edu_counter2; $ei++) {

                    $subject_grade = trim($_POST['subject_OL_' . $ei]);
                    $subject_grade = mysqli_real_escape_string($conn, $subject_grade);
                    $award = trim($_POST['result_OL_' . $ei]);
                    $award = mysqli_real_escape_string($conn, $award);
                    $exam_year_ol = trim($_POST['year_OL_' . $ei]);
                    $exam_year_ol = mysqli_real_escape_string($conn, $exam_year_ol);

                    // insert educational qualifications
                    if ($exam_name_ol != "" && $exam_year_ol != "") {

                        $sql_educational = "INSERT INTO mst_educational_qualifications (stu_nic,exam_year,exam_name,exm_type,subject_grade,award,stu_id) VALUES ('$dec_nic_no','$exam_year_ol','$exam_name_ol','O/L','$subject_grade','$award',$last_id)";
                        $res_educational = mysqli_query($conn, $sql_educational);
                        echo $sql_educational;
                        if ($res_educational) {
                        } else {
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

                    $name_EP = trim($_POST['name_EP_' . $ei]);
                    $name_EP = mysqli_real_escape_string($conn, $name_EP);
                    $result_EP = trim($_POST['result_EP_' . $ei]);
                    $result_EP = mysqli_real_escape_string($conn, $result_EP);
                    $exam_year_EP = trim($_POST['year_EP_' . $ei]);
                    $exam_year_EP = mysqli_real_escape_string($conn, $exam_year_EP);

                    // insert educational qualifications
                    if ($name_EP != "") {

                        $sql_english = "INSERT INTO mst_english_proficiency (stu_passport_id,qualification_type,result,year,al_result,stu_id) VALUES ('$dec_nic_no','$name_EP','$result_EP','$exam_year_EP','',$last_id)";
                        $res_english = mysqli_query($conn, $sql_english);
                        echo $sql_english;
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


                if (trim($_POST['fatherName'] != "")) {
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

                    $sql_father = "UPDATE family_details SET name = '$fatherName',job = '$fatherJob',email = '$fatherEmail',fixed_phone = '$fatherFixedPhone',mobile_no = '$fatherMobileNo',employey_details = '$father_employer' WHERE  stu_passport_id= '$dec_nic_no' AND relationship = 'FATHER'";
                    $res_father = mysqli_query($conn, $sql_father);
                }

                // mother details
                if (trim($_POST['motherName']  != "")) {
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

                    $sql_mother = "UPDATE family_details SET name = '$motherName',job = '$motherJob',email = '$motherEmail',fixed_phone = '$motherFixelPhone',mobile_no = '$motherMobileNo',employey_details = '$mother_employer' WHERE  stu_passport_id= '$dec_nic_no' AND relationship = 'MOTHER'";
                    $res_mother = mysqli_query($conn, $sql_mother);
                }

                // guardian details
                if (trim($_POST['guardianName']  != "")) {
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

                    $sql_guardian = "UPDATE family_details SET name = '$guardianName',job = '$guardianJob',email = '$guardianEmail',fixed_phone = '$guardianFixelPhone',mobile_no = '$guardianMobileNo',employey_details = '$guardian_employer' WHERE stu_passport_id = '$dec_nic_no' AND relationship = 'GUARDIAN'";
                    $res_guardian = mysqli_query($conn, $sql_guardian);
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



                if ($err_code == 0) {
                    $sql_updt = "UPDATE mst_personal_details SET application_confirm_status = 'Y' , payment_status = 'PENDING' WHERE nic_no = '$dec_nic_no' ";
                    //$sql_updt = "UPDATE mst_personal_details SET application_confirm_status = 'Y' WHERE nic_no = '$dec_nic_no' ";
                    $res_updt = mysqli_query($conn, $sql_updt);
                    if ($res_updt) {
                    } else {
                        $err_code = 8;
                    }
                }

                if ($err_code == 0) {
                    //mysqli -> rollback();
                    //header('Location:applicationcnfm.php?idn='.$enc_nic_no.'&lsidn='.$enc_last_id.'&errCode='.$err_code);
                    //echo $Photo;
                    //header('Location:view_unsubmitted_application.php?idn='.$enc_nic_no.'&lsidn='.$enc_last_id);
                    //header('Location:intermediate_pg_request.php?idn='.$enc_nic_no.'&lsidn='.$enc_last_id);
                    //header('Location:../includes/content/viewapplicationslist.html');

                } else {
                    //header('Location:applicationform.php?errcode='.$err_code);
                }
            } else {
                //header('Location:applicationform.php?errcode=2');
            } // end if($res_personal_data)

        } // end if($err_code == 1)
    }
} else {
    //header('Location:index.php?errcd=1&nic='.$dec_nic_no);
    echo 'nic:' . $enc_nic_no;
}
?>
<script src="../assets/js/formupdate.js"></script>