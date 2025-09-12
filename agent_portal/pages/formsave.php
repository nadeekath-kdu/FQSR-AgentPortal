<?php
if(!isset($_SESSION)) 
{ 
    session_start();
} 
$ag_code = $_SESSION['agent_code']; 
header('Content-Type: application/json');
require_once '../config/iv_key.php';
require_once '../config/mystore_func.php'; 
require_once '../config/dbcon.php'; 
require_once '../config/global.php'; 
	



$db_connection = $con;
$form_action = 'save';
$formStatus='UNSUBMITTED';
$domain = $url;
$passportno = "";
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

//if( (isset($_POST['passportno'])) && ($_POST['passportno'] != NULL) && ($_POST['passportno'] != "") && ($_POST['passportno'] != " ") ){
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    /* if($app_confirm_status == 1){
        header('Location:applicationstatus.php?idn='.$enc_nic_no); 
    }else{ */
        // insert data personal data
       /*  $apply_course_code = "";
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
        $period_study_abroad ="";
        $eligibility_uni_admision ="";
        $citizenship_type = "";
        $stu_citizenship = "";
        $citizenship1 = "";
        $citizenship2 = "";
        $country_AL = "";
        $eduAgent = ""; 
        $nameEduAgent = "";
        $Photo = "";
 */

        
        /* if($err_code == 1){
            header('Location:applicationform.php?errcode=1');
        }else{ */
            // sanitize inputs
            $dec_nic_no = $_POST['passportno'];
            $apply_course_code = $_POST['inputCourse'];
            //$apply_course =
            $intake_yr = $intake;
            $stu_title = trim($_POST['inputTitle']);
            $stu_fullname = $_POST['inputFullname'];
            $stu_birth_country = $_POST['inputCountryBirth'];
            $stu_initialname = $_POST['inputInitials'];
            $stu_dob = $_POST['inputDob'];
            $stu_gender = $_POST['inputGender'];
            $citizenship_type = $_POST['citizenship_type'];
            $stu_civilstats = $_POST['inputCivilSts'];
            $stu_permenant_addr = $_POST['addressPermanent'];
            $stu_email = $_POST['inputEmailAddress'];
            //$media_source_name = $_POST['inputMediaSource'];
            $media_source_name = "";
            $doc_upld_link = $_POST['docupldlink'];
            $period_study_abroad = $_POST['periodStudy'];
            $eligibility_uni_admision =trim($_POST['elegibleState']);
            $other_qualification = trim($_POST['otherQualifications']);
            $fund = trim($_POST['fund']);
            $stu_citizenship = trim($_POST['inputCitizenship']);
            $citizenship1 = trim($_POST['inputCitizenship1']);
            $citizenship2 = trim($_POST['inputCitizenship2']);
            $country_AL = trim($_POST['countryAL']);

            $eduAgent = "Yes"; 
            $nameEduAgent = trim($ag_code); 
            
            $uploaddir = "../profile/";
            $fileName = basename($_FILES["inputPhoto"]["name"]);
            $extension = pathinfo($targetFilePath, PATHINFO_EXTENSION);
            $Photo=$dec_nic_no. '.' . $extension; 
            $targetFilePath = $uploaddir.$dec_nic_no.$fileName;
           
            move_uploaded_file($_FILES["inputPhoto"]["tmp_name"], $targetFilePath);
           /*  if (move_uploaded_file($_FILES["inputPhoto"]["tmp_name"], $targetFilePath)) {
            }else{
                $response = [
                    'status' => 'error',
                    'message' => 'Sorry, there was an error uploading your file.'
                ];
            } */
            //save profile picture abooutus floder          
            
           /*  $temp = explode(".", $_FILES["inputPhoto"]["name"]);
           echo 'extension:' .$extension = pathinfo($_FILES["inputPhoto"]["name"], PATHINFO_EXTENSION);
            $uploadfile = $uploaddir.$dec_nic_no. '.' .$extension; */
            
        

            
            /* if (move_uploaded_file($_FILES["inputPhoto"]["tmp_name"], $uploadfile)) {
              echo "File is valid, and was successfully uploaded.\n";
            } else {
               echo "Photo Upload failed";
            } */ 
            $sql_cousr_name = "SELECT degree_name FROM mst_degree_courses WHERE degree_code = '$apply_course_code' ";
            $res_course_name = mysqli_query($db_connection,$sql_cousr_name);

            $course_name_cnt = mysqli_num_rows($res_course_name);
            if($course_name_cnt > 0){
                while($row_course_name = mysqli_fetch_array($res_course_name)){
                    $apply_course = $row_course_name['degree_name'];
                }
            }
            // ---------------------
            $cur_dt = date('Y-m-d H:i:s'); 
            if (isset($_POST['submit'])) {
                $sql_personal_data = "INSERT INTO mst_personal_details (nic_no,course_name,course_code,intake,stu_title,stu_fullname,stu_name_initials,stu_dob,stu_gender,stu_citizenship,civil_status,stu_permenant_address,stu_email,application_submit_dt,media_source_name,doc_upload_link,birth_country,period_study_abroad,eligibility_uni_admision,other_qualification,fund,citizenship_type,citizenship_1,citizenship_2,AL_sitting_country,photo,isEduAgent,nameEduAgent,application_status)VALUES ('$dec_nic_no','$apply_course','$apply_course_code','$intake_yr','$stu_title','$stu_fullname','$stu_initialname','$stu_dob','$stu_gender','$stu_citizenship','$stu_civilstats','$stu_permenant_addr','$stu_email','$cur_dt','$media_source_name','$doc_upld_link','$stu_birth_country','$period_study_abroad','$eligibility_uni_admision','$other_qualification','$fund','$citizenship_type','$citizenship1','$citizenship2','$country_AL','$Photo','$eduAgent','$nameEduAgent','$formStatus')";
            }else{
                $sql_personal_data = "INSERT INTO mst_personal_details (nic_no,course_name,course_code,intake,stu_title,stu_fullname,stu_name_initials,stu_dob,stu_gender,stu_citizenship,civil_status,stu_permenant_address,stu_email,application_submit_dt,media_source_name,doc_upload_link,birth_country,period_study_abroad,eligibility_uni_admision,other_qualification,fund,citizenship_type,citizenship_1,citizenship_2,AL_sitting_country,photo,isEduAgent,nameEduAgent,formStatus)VALUES ('$dec_nic_no','$apply_course','$apply_course_code','$intake_yr','$stu_title','$stu_fullname','$stu_initialname','$stu_dob','$stu_gender','$stu_citizenship','$stu_civilstats','$stu_permenant_addr','$stu_email','$cur_dt','$media_source_name','$doc_upld_link','$stu_birth_country','$period_study_abroad','$eligibility_uni_admision','$other_qualification','$fund','$citizenship_type','$citizenship1','$citizenship2','$country_AL','$Photo','$eduAgent','$nameEduAgent','$formStatus')";            
            }
            $res_personal_data = mysqli_query($db_connection,$sql_personal_data);

            $test_var = "";
            if($res_personal_data){
                $last_id = mysqli_insert_id($db_connection);
                //$enc_last_id = encryptStoreStr($last_id,ENCRYPT_METHOD,WSECRET_KEY,WSECRET_IV);
                $enc_last_id =$last_id;
                $edu_counter = $_POST['edurowcnt'];
                $edu_counter2 = $_POST['edurowcnt2'];
                $edu_counter3 = $_POST['edurowcnt3'];
                
                
                $exam_name_al = trim($_POST['examNameAL']);
                $exam_name_al = mysqli_real_escape_string($db_connection,$exam_name_al);
                for($ei=1; $ei <= $edu_counter; $ei++){
                    
                    $subject_grade = trim($_POST['subject_AL_'.$ei]);
                    $subject_grade = mysqli_real_escape_string($db_connection,$subject_grade);
                    $award = trim($_POST['result_AL_'.$ei]);
                    $award = mysqli_real_escape_string($db_connection,$award);
                    $exam_year_al = trim($_POST['year_AL_'.$ei]);
                    $exam_year_al = mysqli_real_escape_string($db_connection,$exam_year_al);
                    
                    // insert educational qualifications
                    if($subject_grade != "" && $exam_name_al != ""){
                        
                        $sql_educational = "INSERT INTO mst_educational_qualifications (stu_nic,exam_year,exam_name,exm_type,subject_grade,award,stu_id) VALUES ('$dec_nic_no','$exam_year_al','$exam_name_al','A/L','$subject_grade','$award',$last_id)";
                        $res_educational = mysqli_query($db_connection,$sql_educational);
                        
                        if($res_educational){}else{$err_code = 2;}
                        
                    } // end if
                } // end for educational A/L

                
                $exam_name_ol = trim($_POST['examNameOL']);
                $exam_name_ol = mysqli_real_escape_string($db_connection,$exam_name_ol);
                for($ei=1; $ei <= $edu_counter2; $ei++){
                    //console.log($edu_counter2);
                    $subject_grade = trim($_POST['subject_OL_'.$ei]);
                    $subject_grade = mysqli_real_escape_string($db_connection,$subject_grade);
                    $award = trim($_POST['result_OL_'.$ei]);
                    $award = mysqli_real_escape_string($db_connection,$award);
                    $exam_year_ol = trim($_POST['year_OL_'.$ei]);
                    $exam_year_ol = mysqli_real_escape_string($db_connection,$exam_year_ol);
                    
                    // insert educational qualifications
                    if($exam_name_ol != "" && $subject_grade != ""){
                        
                        $sql_educational = "INSERT INTO mst_educational_qualifications (stu_nic,exam_year,exam_name,exm_type,subject_grade,award,stu_id) VALUES ('$dec_nic_no','$exam_year_ol','$exam_name_ol','O/L','$subject_grade','$award',$last_id)";
                        $res_educational = mysqli_query($db_connection,$sql_educational);
                        
                        if($res_educational){}else{$err_code = 2;}
                        
                    } // end if
                } // end for educational O/L

                //english proficiency
                $sat_result = trim($_POST['sat_result']);
                $sat_passing_year = trim($_POST['sat_passing_year']);
                $sat_result = mysqli_real_escape_string($db_connection,$sat_result);
                $sat_passing_year = mysqli_real_escape_string($db_connection,$sat_passing_year);
                
                for($ei=1; $ei <= $edu_counter3; $ei++){
                    
                    $name_EP = trim($_POST['name_EP_'.$ei]);
                    $name_EP = mysqli_real_escape_string($db_connection,$name_EP);
                    $result_EP = trim($_POST['result_EP_'.$ei]);
                    $result_EP = mysqli_real_escape_string($db_connection,$result_EP);
                    $exam_year_EP = trim($_POST['year_EP_'.$ei]);
                    $exam_year_EP = mysqli_real_escape_string($db_connection,$exam_year_EP);
                    
                    // insert educational qualifications
                    if($name_EP != ""){
                        
                        $sql_english = "INSERT INTO mst_english_proficiency (stu_passport_id,qualification_type,result,year,al_result,stu_id) VALUES ('$dec_nic_no','$name_EP','$result_EP','$exam_year_EP','',$last_id)";
                        $res_english = mysqli_query($db_connection,$sql_english);
                        
                    } // end if
                }

                if($sat_result != ""){
                   
                    $sql_english = "INSERT INTO mst_english_proficiency (stu_passport_id,qualification_type,result,year,al_result,stu_id) VALUES ('$dec_nic_no','SAT','$sat_result','$sat_passing_year','',$last_id)";
                    $res_english = mysqli_query($db_connection,$sql_english);
                    
                }
                //end of english proficiency

                //family_details
                // father details


                if(trim($_POST['fatherName'] != "")){
                    $fatherName = trim($_POST['fatherName']);
                    $fatherName = mysqli_real_escape_string($db_connection,$fatherName);
                    $fatherJob = trim($_POST['fatherJob']);
                    $fatherJob = mysqli_real_escape_string($db_connection,$fatherJob);
                    $father_employer = trim($_POST['father_employer']);
                    $father_employer = mysqli_real_escape_string($db_connection,$father_employer);
                    $fatherEmail = trim($_POST['fatherEmail']);
                    $fatherEmail = mysqli_real_escape_string($db_connection,$fatherEmail);
                    $fatherFixedPhone = trim($_POST['fatherFixedPhone']);
                    $fatherFixedPhone = mysqli_real_escape_string($db_connection,$fatherFixedPhone);
                    $fatherMobileNo = trim($_POST['fatherMobileNo']);
                    $fatherMobileNo = mysqli_real_escape_string($db_connection,$fatherMobileNo);

                    $sql_father = "INSERT INTO family_details (stu_passport_id,relationship,name,job,email,fixed_phone,mobile_no,employey_details,stu_id) VALUES ('$dec_nic_no','FATHER','$fatherName','$fatherJob','$fatherEmail','$fatherFixedPhone','$fatherMobileNo','$father_employer',$last_id)";
                    $res_father = mysqli_query($db_connection,$sql_father);
                }

                // mother details
                if(trim($_POST['motherName']  != "")){
                    $motherName = trim($_POST['motherName']);
                    $motherName = mysqli_real_escape_string($db_connection,$motherName);
                    $motherJob = trim($_POST['motherJob']);
                    $motherJob = mysqli_real_escape_string($db_connection,$motherJob);
                    $mother_employer = trim($_POST['mother_employer']);
                    $mother_employer = mysqli_real_escape_string($db_connection,$mother_employer);
                    $motherEmail = trim($_POST['motherEmail']);
                    $motherEmail = mysqli_real_escape_string($db_connection,$motherEmail);
                    $motherFixelPhone = trim($_POST['motherFixelPhone']);
                    $motherFixelPhone = mysqli_real_escape_string($db_connection,$motherFixelPhone);
                    $motherMobileNo = trim($_POST['motherMobileNo']);
                    $motherMobileNo = mysqli_real_escape_string($db_connection,$motherMobileNo);

                    $sql_mother = "INSERT INTO family_details (stu_passport_id,relationship,name,job,email,fixed_phone,mobile_no,employey_details,stu_id) VALUES ('$dec_nic_no','MOTHER','$motherName','$motherJob','$motherEmail','$motherFixelPhone','$motherMobileNo','$mother_employer',$last_id)";
                    $res_mother = mysqli_query($db_connection,$sql_mother);
                }

                // guardian details
                if(trim($_POST['guardianName']  != "")){
                    $guardianName = trim($_POST['guardianName']);
                    $guardianName = mysqli_real_escape_string($db_connection,$guardianName);
                    $guardianJob = trim($_POST['guardianJob']);
                    $guardianJob = mysqli_real_escape_string($db_connection,$guardianJob);
                    $guardian_employer = trim($_POST['guardian_employer']);
                    $guardian_employer = mysqli_real_escape_string($db_connection,$guardian_employer);
                    $guardianEmail = trim($_POST['guardianEmail']);
                    $guardianEmail = mysqli_real_escape_string($db_connection,$guardianEmail);
                    $guardianFixelPhone = trim($_POST['guardianFixelPhone']);
                    $guardianFixelPhone = mysqli_real_escape_string($db_connection,$guardianFixelPhone);
                    $guardianMobileNo = trim($_POST['guardianMobileNo']);
                    $guardianMobileNo = mysqli_real_escape_string($db_connection,$guardianMobileNo);

                    $sql_guardian = "INSERT INTO family_details (stu_passport_id,relationship,name,job,email,fixed_phone,mobile_no,employey_details,stu_id) VALUES ('$dec_nic_no','GUARDIAN','$guardianName','$guardianJob','$guardianEmail','$guardianFixelPhone','$guardianMobileNo','$guardian_employer',$last_id)";
                    $res_guardian = mysqli_query($db_connection,$sql_guardian);
                }
                // end of family details

                // refrees  
                if(trim($_POST['refree1_details'] != "")){
                    $refree1_details = trim($_POST['refree1_details']);
                    $refree1_details = mysqli_real_escape_string($db_connection,$refree1_details);
                    $refree1_phone = trim($_POST['refree1_phone']);
                    $refree1_phone = mysqli_real_escape_string($db_connection,$refree1_phone);
                    
                    $sql_refree1 = "INSERT INTO refree (stu_passport_id,refree_details,contact_no,type,stu_id) VALUES ('$dec_nic_no','$refree1_details','$refree1_phone','FOREIGN',$last_id)";
                    $res_refree1 = mysqli_query($db_connection,$sql_refree1);
                }

                if(trim($_POST['refree2_details'] != "") && trim($_POST['refree2_phone'] != "")){
                    $refree2_details = trim($_POST['refree2_details']);
                    $refree2_details = mysqli_real_escape_string($db_connection,$refree2_details);
                    $refree2_phone = trim($_POST['refree2_phone']);
                    $refree2_phone = mysqli_real_escape_string($db_connection,$refree2_phone);
                    

                    $sql_refree2 = "INSERT INTO refree (stu_passport_id,refree_details,contact_no,type,stu_id) VALUES ('$dec_nic_no','$refree2_details','$refree2_phone','FOREIGN',$last_id)";
                    $res_refree2 = mysqli_query($db_connection,$sql_refree2);
                }

                if(trim($_POST['refree_sl_details'] != "")){
                    $refree_sl_details = trim($_POST['refree_sl_details']);
                    $refree_sl_details = mysqli_real_escape_string($db_connection,$refree_sl_details);
                    $refree_sl_phone = trim($_POST['refree_sl_phone']);
                    $refree_sl_phone = mysqli_real_escape_string($db_connection,$refree_sl_phone);
                    
                    $test_var = $refree_sl_details; 
                    $sql_refree_sl = "INSERT INTO refree (stu_passport_id,refree_details,contact_no,type,stu_id) VALUES ('$dec_nic_no','$refree_sl_details','$refree_sl_phone','SRILANKA',$last_id)";
                    $res_refree_sl = mysqli_query($db_connection,$sql_refree_sl);
                }
                // end of refree

                

                if($err_code == 0){
                    $sql_updt = "UPDATE mst_personal_details SET application_confirm_status = 'Y' , payment_status = 'PENDING' WHERE nic_no = '$dec_nic_no' ";
                   
                    $res_updt = mysqli_query($db_connection, $sql_updt);
                    if($res_updt){}else{$err_code = 8; }

                    $response = [
                        'status' => 'success',
                        'message' => 'Data saved!'.$apply_course_code
                      ]; 
                }else{
                    $response = [
                        'status' => 'error',
                        'message' => 'error code: ' .$err_code
                      ];
                }

                

            }else{
                $response = [
                    'status' => 'error',
                    'message' => 'Personal details not saved: '
                  ];
            } // end -->if($res_personal_data)

       
    
      // Send the response back as JSON
      echo json_encode($response);

}else{
    $db_connection->close();
  // Handle non-POST requests (optional)
  $response = [
    'status' => 'error',
    'message' => 'Invalid request method'
  ];

  // Send the response back as JSON
  echo json_encode($response);
}
?>
