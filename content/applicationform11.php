<?php
require_once './config/dbcon.php';
require_once 'config/iv_key.php';
require_once 'config/global.php';
require_once 'config/mystore_func.php'; //local 

$conn = mysqli_connect(DB_HOST,DB_USERNAME,DB_PWD,DB_TBL);

date_default_timezone_set('Asia/Colombo');

$cur_dts = date('Y-m-d');
$academic_year = $row_academicYear['year'];
$intake = $row_academicYear['intake'];
//$application_closing_date = $row_academicYear['application_closing_date'];
// redirect to index.php
/* if($cur_dts > $application_closing_date){
    header('Location:index.php');
} */

$enc_nic_no = "";
$dec_nic_no = "";
$err_code = 0;
$display_message = "";

/* $enc_nic_no = "";
$dec_nic_no = "";
$err_code = 0;
$display_message = "";
$academic_year = '2022';//$row_academicYear['year'];
$intake = '39';//$row_academicYear['intake'];
$application_closing_date = '2022-10-01';//$row_academicYear['application_closing_date']; */
//echo "AA:".$_GET['idn'];
if( (isset($_GET['idn'])) && ($_GET['idn'] != NULL) && ($_GET['idn'] != "") && ($_GET['idn'] != " ") ){
    $enc_nic_no = $_GET['idn'];
    $dec_nic_no = decryptStr($enc_nic_no,ENCRYPT_METHOD,WSECRET_KEY,WSECRET_IV); //local
    //$dec_nic_no=$enc_nic_no; //local
}

// perform a check to see applicant has confirm the application
$sql_chk = "SELECT applicant_id FROM mst_personal_details WHERE nic_no = '$dec_nic_no' AND application_confirm_status = 'Y' ";
$res_chk = mysqli_query($conn,$sql_chk);

$applicant_cnt = mysqli_num_rows($res_chk);
if($applicant_cnt > 0){
    // already confirmed
    $app_confirm_status = 1;
    header('Location:applicationstatus.php?idn='.$enc_nic_no);
}else{
    $app_confirm_status = 0;
} //end if

// get degrees
$sql_degree_list = "SELECT * FROM mst_degree_courses WHERE active_status = 'Y' ";
$res_degree_list = mysqli_query($conn,$sql_degree_list);

$degree_list_cnt = mysqli_num_rows($res_degree_list);
//--------------

// error handling
if( (isset($_GET['errcode'])) &&
    ($_GET['errcode'] != NULL) && ($_GET['errcode'] != "") && ($_GET['errcode'] != " ") ){
    $err_code = $_GET['errcode'];
    
    //echo $sql_educational;
        switch($err_code){
            case 1:
                $display_message = "Error! Mandatory field is missing";
                break;
            case 2:
                $display_message = "Error! error occured while saving educational qualifications. Please try again.";
                break;
            case 4:
                $display_message = "Error! error occured while saving membership of professional institutions details. Please try again.";
                break;
            case 5:
                $display_message = "Error! error occured while saving present employment details. Please try again.";
                break;
            case 6:
                $display_message = "Error! error occured while saving previous employment details. Please try again.";
                break;
            case 7:
                $display_message = "Error! error occured while saving application details. Please try again.";
                break;
            case 8:
                $display_message = "Error! failed to update application status. Please try again.";
                break;
            default:
                $display_message = "";
                break;
        }
    //}
    //header('Location:formsave.php');
}
?>
<!DOCTYPE html>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.14.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-duration-format/1.3.0/moment-duration-format.min.js"></script>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>KDU-FSR</title>
        <link rel="shortcut icon" href="localhost/agent_portal/images/Kdufav.png" />
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>-->
        <link href="dist/css/styles.css" rel="stylesheet" />
        <script src="dist/js/all-min.js"></script>
       <style type="text/css">
            /*Background Image*/
            .imagebg {
                background-image: url("DSC_2723.JPG");
                background-repeat: no-repeat;
                background-position: center center;
                background-size: cover;
                background-attachment: fixed;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                -webkit-filter: blur(3px);
              filter: blur(3px);
              opacity: 0.6;
              filter: alpha(opacity=60);
            }/* end Background Image*/
           /*Profile Pic Start*/
            .picture-container{
                position: relative;
                cursor: pointer;
                text-align: center;
            }
            .picture{
                width: 126px;
                height: 126px;
                background-color: #999999;
                border: 4px solid #CCCCCC;
                color: #FFFFFF;
                /*border-radius: 50%;*/
                margin: 0px auto;
                overflow: hidden;
                transition: all 0.2s;
                -webkit-transition: all 0.2s;
            }
            .picture:hover{
                border-color: #2ca8ff;
            }
            .content.ct-wizard-green .picture:hover{
                border-color: #05ae0e;
            }
            .content.ct-wizard-blue .picture:hover{
                border-color: #3472f7;
            }
            .content.ct-wizard-orange .picture:hover{
                border-color: #ff9500;
            }
            .content.ct-wizard-red .picture:hover{
                border-color: #ff3b30;
            }
            .picture input[type="file"] {
                cursor: pointer;
                display: block;
                height: 100%;
                left: 0;
                opacity: 0 !important;
                position: absolute;
                top: 0;
                width: 100%;
            }

            .picture-src{
                width: 100%;
                
            }
            /*Profile Pic End*/
            
       </style>
    </head>
    <body class="bg-primary">
         <div class="imagebg"></div>
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="card shadow-lg border-0 rounded-lg mt-5">

                                    <div class="card-header">
                                        <center><img border="0"  src="logo.png" ></center>
                                        <h4 class="text-center font-weight-light my-4" >
                                            <b>Application for Admission of Students with Foreign Qualification for the Academic Year  <?php echo $academic_year ?></b>
                                        </h4>
                                    </div>
                                    <div class="card-body">
                                            <?php
                                            if($err_code == 1 || $err_code == 2 || $err_code == 4 || $err_code == 5 || $err_code == 6 || $err_code == 7 || $err_code == 8 ){
                                            ?>
                                                <div class="form-row">
                                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                                        <div class="alert alert-danger" role="alert">
                                                        <i class="fa fa-warning"></i>&nbsp;<?php echo $display_message; ?>
                                                        </div>
                                                    </div>                                                
                                                </div>
                                            <?php
                                            }
                                            ?>
                                        <form name="my-form" onsubmit="return validateForm()" action="formsave.php" method="post" enctype="multipart/form-data">
                                            <!-- <form name="my-form"  method="post" onsubmit="return validateForm()"> -->
                                            <div class="form-row">
                                                <div class="col-lg- col-md-8 col-sm-12">
                                                    <h5>Applicant Passport No : <?php echo $dec_nic_no; ?></h5>
                                                </div>
                                                <div class="col-lg- col-md-4 col-sm-12">
                                                    <div class="container">
                                                        <div class="picture-container">
                                                            <div class="picture">
                                                                <img  class="picture-src" id="wizardPicturePreview" title="">
                                                               <input type="file" id="Photo" name = "Photo" class="form-control" accept="image/jpeg" placeholder="Choose Image">
                                                               <label class="small mb-1" for="img" style="color:blue">Choose Image</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="form-row">
                                                <div class="col-lg- col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="inputCourse" >Choose a course intending to follow <span class="error" style="color: #FF0000;">*</span></label>
                                                        <select class="form-control" name="inputCourse" id="inputCourse">
                                                            <option value="">Please Select</option>
                                                            <?php
                                                            if($degree_list_cnt > 0){
                                                                while($row_degree = mysqli_fetch_array($res_degree_list)){
                                                            ?>
                                                                <option value="<?php echo $row_degree['degree_code']; ?>"><?php echo $row_degree['degree_name']; ?></option>
                                                            <?php

                                                                }
                                                            }
                                                            ?>                                                                                                                      
                                                        </select>                                                            
                                                    </div>
                                                </div> 
                                                <div class="col-lg-3 col-md-3 col-sm-12">
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-12">
                                                    <div class="form-group" style="display: none;">
                                                        <label class="small mb-1" for="inputIntakeYr">Intake year</label>
                                                        <input class="form-control py-4" id="inputAcademicYear" name="inputAcademicYear" type="text" value="<?php echo $academic_year; ?>" />
                                                        <input class="form-control py-4" id="inputIntakeYr" name="inputIntakeYr" type="text" value="<?php echo $intake; ?>" />                                                            
                                                        <input class="form-control py-4" id="inputNic" name="inputNic" type="hidden" required value="<?php echo $enc_nic_no; ?>" />
                                                        <input type="hidden" id="closingDate" name="closingDate" value="<?php echo $application_closing_date; ?>">
                                                    </div>
                                                </div>                                                 
                                            </div>
                                            <br><br>
                                            <h5 style="color: #007300; font-size: x-large;">Personal Details</h5>
                                            <hr>
                                            <div class="form-row">
                                                <div class="col-lg-2 col-md-2 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="inputTitle">Title</label>                                                        
                                                        <select class="form-control" id="inputTitle" name="inputTitle" >
                                                            <option value="">Please Select</option>
                                                            <option value="Mr">Mr</option>
                                                            <option value="Mrs">Mrs</option>
                                                            <option value="Miss">Miss</option>                                                                                                                 
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-7 col-md-7 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="inputFullname">Full Name <span class="error" style="color: #FF0000;">*</span></label>
                                                        <input class="form-control" id="inputFullname" name="inputFullname" required type="text" placeholder="Enter your Full Name" />
                                                    </div>
                                                </div>
                                            
                                                <div class="col-lg-3 col-md-3 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="inputNameInitials">Name with initials <span class="error" style="color: #FF0000;">*</span></label>
                                                        <input class="form-control" id="inputNameInitials" name="inputNameInitials" type="text" required placeholder="Enter name with initials" />
                                                    </div>
                                                </div>                                               
                                            </div>

                                            <div class="form-row">
                                                <div class="col-lg-3 col-md-3 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="inputDob">Date of birth <span class="error" style="color: #FF0000;">*</span></label> 
                                                        <input class="form-control" id="inputDob" name="inputDob" type="date"  required placeholder="Enter your Date of birth" />
                                                        <!-- <input class="form-control" id="inputDob" name="inputDob" type="date" onchange="validateDate(this.value)" required placeholder="Enter your Date of birth" /> -->
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="inputGender">Gender <span class="error" style="color: #FF0000;">*</span></label>
                                                        <select class="form-control" id="inputGender" name="inputGender" >
                                                            <option value="">Please Select</option>
                                                            <option value="Male">Male</option>
                                                            <option value="Female">Female</option>                                                                                                                                                                                  
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="inputCivilSts">Civil Status <span class="error" style="color: #FF0000;">*</span>
                                                        </label>                                                        
                                                        <select class="form-control" id="inputCivilSts" name="inputCivilSts" >
                                                            <option value="">Please Select</option>
                                                            <option value="Single"  >Single</option>
                                                            <option value="Married">Married</option>                                                                                                                                                                                  
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-12">
                                                </div>
                                            </div>
                                            &nbsp;
                                            <div class="form-row">
                                               <div class="col-lg-3">
                                                    <label class="small mb-1" for="Citizenship" name="citizenship_t">Citizenship </label>
                                                    <span class="error" style="color: #FF0000;">*</span>
                                                </div>
                                                <div class="col-lg-3">
                                                        <input class="small mb-1" type="radio" id="sriLanakan" name="citizenship_type" required value="Sri Lankan Citizenship Only"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label class="small mb-1" for="sriLanakan" name="citizenship_ty">Sri Lankan Citizenship Only</label>
                                                    
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <input class="small mb-1" type="radio" id="foreign" name="citizenship_type" value="Foreign Citizenship"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label class="small mb-1" for="foreign" name="citizenship_ty">Foreign Citizenship</label>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <input class="small mb-1" type="radio" id="dual" name="citizenship_type" value="Dual Citizenship"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label class="small mb-1" for="dual" name="citizenship_ty">Dual Citizenship</label>
                                                    </div> 
                                            </div>  
                                            &nbsp; 
                                                                                      
                                            <div class="form-row" id = "section1" style="display: none;">
                                                <label class="small mb-1" for="inputCitizenship">Country of Citizenship </label>
                                                <input class="form-control py-4" id="inputCitizenship" name="inputCitizenship" type="text" placeholder="Enter your Country of Citizenship" />
                                            </div>
                                            &nbsp;
                                            <div class="form-row" id = "section2" style="display: none;">
                                                <label class="small mb-1" for="inputCitizenship1">Mention Your Country of Citizenship 1  </label>
                                                <input class="form-control" id="inputCitizenship1" name="inputCitizenship1" type="text" placeholder="Enter your 1st Country" />
                                            </div>
                                            &nbsp;
                                            <div class="form-row" id = "section3" style="display: none;">
                                                <label class="small mb-1" for="inputCitizenship2">Mention Your Country of Citizenship 2</label>
                                                <input class="form-control" id="inputCitizenship2" name="inputCitizenship2" type="text" placeholder="Enter your 2nd Country" />
                                            </div> 
                                            &nbsp;
                                            <div class="form-group" >
                                                <label class="small mb-1" for="inputCountryAL">What is the country that you have appeared for Advanced Level examination/ High School Diploma</label>
                                                <input class="form-control" id="inputCountryAL" name="inputCountryAL" type="text" placeholder="Enter your Answer" />
                                            </div>                                         
                                            <div class="form-row">
                                                <div class="col-lg-4 col-md-3 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="inputCountryBirth">Country of Birth<span class="error" style="color: #FF0000;">*</span></label>
                                                        <input class="form-control" id="inputCountryBirth" name="inputCountryBirth" type="text" required placeholder="Enter your Country of Birth" />
                                                    </div>                                                    
                                                </div>
                                                <div class="col-lg-4 col-md-3 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="periodStudy">Period of Study apart from Sri Lanka <span class="error" style="color: #FF0000;">*</span></label>
                                                        <input class="form-control" id="periodStudy" name="periodStudy" type="text" required placeholder="Enter your Period of Study Abroad" />
                                                    </div>                                                    
                                                </div>
                                                <div class="col-lg-4 col-md-3 col-sm-12">
                                                    <label class="small mb-1"><span style="color:blue">(Sri Lanakan expatriates should have studied abroad for a period of not less than three academic years immediately prior to sitting the qualifying examination) </span>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="addressPermanent">Permanent Address to which correspondence should be sent <span class="error" style="color: #FF0000; font-size:large;">*</span></label>
                                                        <textarea class="form-control" id="addressPermanent" name="addressPermanent" rows="3" required></textarea>
                                                    </div>
                                                </div>                                                                                                                 
                                            </div>

                                            <div class="form-group">
                                                <label class="small mb-1" for="inputEmailAddress">Email address <span class="error" style="color: #FF0000;">*</span></label>
                                                <input class="form-control" id="inputEmailAddress" name="inputEmailAddress" type="email" aria-describedby="emailHelp" required placeholder="Enter email address" />
                                            </div>
                                            <br><br>
                                            <h5 style="color: #007300; font-size: x-large;">Educational Qualifications</h5>
                                            <hr>
                                            <h6>Examination equivalent to Advanced Level/ High School</h6>
                                            
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="examNameAL">Name of Examination</label>
                                                        <input class="form-control" id="examNameAL" name="examNameAL"/>
                                                    </div>
                                                    <!-- <div class="form-group">
                                                        <label class="small mb-1" for="examYearAL">Year of Examination</label>
                                                        <input class="form-control py-4" id="examYearAL" name="examYearAL"/>
                                                    </div> -->
                                                
                                                <div class="form-row">
                                                    <button type="button" class="btn btn-warning" onClick="addtoEducational();"><i class="fa fa-plus"></i></button>&nbsp;<button type="button" onClick="remfromEducational();" class="btn btn-danger"><i class="fa fa-minus"></i></button>
                                                 </div>

                                            <div class="form-row">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                <table class="table" id="edutbl">
                                                    <thead>
                                                        <tr>
                                                        <th scope="col">Subject</th>
                                                        <th scope="col">Grade</th>
                                                        <th scope="col">Year</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>                                                        
                                                            <td><input class="form-control" id="subject_AL_1" type="text" name="subject_AL_1"  placeholder="Enter Subject" /></td>
                                                            <td><input class="form-control" id="result_AL_1" type="text" name="result_AL_1"  placeholder="Enter Result" /></td>
                                                            <td><input class="form-control" id="year_AL_1" type="text" name="year_AL_1"  placeholder="Enter Year" /></td>
                                                        </tr>
                                                        
                                                    </tbody>
                                                </table>
                                                </div>                                                                                                                                                
                                            </div>
                                               
                                            
                                             
                                            <input class="form-control" id="edurowcnt" type="hidden" name="edurowcnt"  value="1" />
                                             <hr>
                                            <h6>Examination equivalent to Ordinary Level/ Secondary Education</h6>
 
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="examNameOL">Name of Examination</label>
                                                        <input class="form-control" id="examNameOL" name="examNameOL"/>
                                                    </div>
                                                    <!-- <div class="form-group">
                                                        <label class="small mb-1" for="examYearOL">Year of Examination</label>
                                                        <input class="form-control py-4" id="examYearOL" name="examYearOL"/>
                                                    </div> -->
                                                
                                                <div class="form-row">
                                                    <button type="button" class="btn btn-warning" onClick="addtoEducational_ol();"><i class="fa fa-plus"></i></button>&nbsp;<button type="button" onClick="remfromEducational_ol();" class="btn btn-danger"><i class="fa fa-minus"></i></button>
                                            
                                                </div>
                                            <div class="form-row">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                <table class="table" id="edutbl2">
                                                    <thead>
                                                        <tr>
                                                        <th scope="col">Subject</th>
                                                        <th scope="col">Grade</th>
                                                        <th scope="col">Year</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>                                                        
                                                            <td><input class="form-control" id="subject_OL_1" type="text" name="subject_OL_1"  placeholder="Enter Subject" /></td>
                                                            <td><input class="form-control" id="result_OL_1" type="text" name="result_OL_1"  placeholder="Enter Result" /></td>
                                                            <td><input class="form-control" id="year_OL_1" type="text" name="year_OL_1"  placeholder="Enter Year" /></td>
                                                        </tr>
                                                        
                                                    </tbody>
                                                </table>
                                                </div>                                                                                                                                                
                                            </div>
                                                
                                           
                                            <input class="form-control py-4" id="edurowcnt2" type="hidden" name="edurowcnt2"  value="1" />
                                             <hr>
                                            <div class="form-row">
                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="elegibleState1">State whether you are eligible for the admission to a state University in your country <span class="error" style="color: #FF0000; font-size: x-large;">*</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 col-md-2 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="elegibleState">   </label>                                                  
                                                        <select class="form-control" id="elegibleState" name="elegibleState" >
                                                            <option value="">Please Select</option>
                                                            <option value="Yes">Yes</option>
                                                            <option value="No">No</option>                                                                                                                                                                                  
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <label class="small mb-1" for="ele"></label>
                                            </div>
                                            <div class="form-row" style="display: none;"> <!-- 2022-07-20 -->
                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="eduAgent">Have you been assisted by an education agent <span class="error" style="color: #FF0000; font-size: x-large;">*</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 col-md-2 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="eduAgent">   </label>                                                  
                                                        <select class="form-control" id="eduAgent" name="eduAgent" >
                                                            <option value="">Please Select</option>
                                                            <option value="Yes">Yes</option>
                                                            <option value="No">No</option>                                                                                                                                                                                  
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                </div>
                                            </div>
                                            <div class="form-group" id = "section4" style="display: none;">
                                                        <label class="small mb-1" for="nameEduAgent">Name of Agent</label>
                                                        <input class="form-control" id="nameEduAgent" name="nameEduAgent"/>
                                                    </div> <!-- end 2022-07-20 -->
                                            <br><br>
                                            <h5 style="color: #007300; font-size: x-large;">English Language Proficiency </h5>
                                            <hr>
                                            <h6>
                                                
                                                    Applicants whose primary language is not English or whose previous education has not been in English must provide evidence of proficiency in English (achieve a minimum score of 79 on the TOFEL or achieve a minimum score of 6.5 on IELTS)
                                               
                                            </h6>
                                            <label class="small mb-1" for="el">
                                                Please list down your English Language Qualifications with results obtained
                                            </label><br><br>
                                                <div class="form-row">
                                                    <button type="button" class="btn btn-warning" onClick="addtoEnglish_qualification();"><i class="fa fa-plus"></i></button>&nbsp;<button type="button" onClick="remfromEnglish_qualification();" class="btn btn-danger"><i class="fa fa-minus"></i></button>
                                               </div>
                                            <div class="form-row">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                <table class="table" id="ep_tbl">
                                                    <thead>
                                                        <tr>
                                                        <th scope="col">English Qualifications </th>
                                                        <th scope="col">Results/Score</th>
                                                        <th scope="col">Passing Year</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>                                                        
                                                            <td><input class="form-control" id="name_EP_1" type="text" name="name_EP_1"  placeholder="Enter Qualification Type" /></td>
                                                            <td><input class="form-control" id="result_EP_1" type="text" name="result_EP_1"  placeholder="Enter Result" /></td>
                                                            <td><input class="form-control" id="year_EP_1" type="text" name="year_EP_1"  placeholder="Enter Year" /></td>
                                                        </tr>
                                                        
                                                    </tbody>
                                                </table>
                                                </div>                                                                                                                                                
                                            </div>
                                                
                                           
                                            <input class="form-control py-4" id="edurowcnt3" type="hidden" name="edurowcnt3"  value="1" />
                                             <hr>
                                             <h6>
                                                For Candidates with High School Diploma
                                            </h6>
                                             <label class="small mb-1" for="elegi">
                                                Candidates with High School Diploma should have passed the scholastic Aptitude Test (SAT)
                                            </label><br><br>
                                            <div class="form-row">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                <table class="table" id="edutbl2">
                                                    <thead>
                                                        <tr>
                                                        <th scope="col">Score of the Scholastic Aptitude Test</th>
                                                        <th scope="col">Passing Year </th>
                                                       
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>                                                        
                                                            <td><input class="form-control" id="sat_result" type="text" name="sat_result"  placeholder="Enter Result" /></td>
                                                            <td><input class="form-control" id="sat_passing_year" type="text" name="sat_passing_year"  placeholder="Enter Year" /></td>
                                                        </tr>
                                                        
                                                    </tbody>
                                                </table>
                                                </div>                                                                                                              
                                            </div>                                               

                                            <br><br>
                                            <h5 style="color: #007300; font-size: x-large;">Other Qualifications (If Any)</h5>
                                            <hr>

                                            <div class="form-row">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="otherQualifications">Enter any other qualification details in below area</label>
                                                        <textarea class="form-control" id="otherQualifications" name="otherQualifications" rows="4" maxlength="250"></textarea>
                                                    </div>
                                                </div>                                                                                                                                               
                                            </div>

                                            <br><br>
                                            <h5 style="color: #007300; font-size: x-large;">Parent Details </h5>
                                            <hr>
                                            Father's Details <span class="error" style="color: #FF0000;">*</span>

                                            <div class="form-row">
                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="fatherName">Name</label>
                                                        <input class="form-control" id="fatherName" name="fatherName" type="text" required  placeholder="Enter Name" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="fatherJob">Occupation</label>
                                                        <input class="form-control" id="fatherJob" name="fatherJob" type="text"  placeholder="Enter Occupation" />
                                                    </div>
                                                </div>   
                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="father_employer">Employer Address</label>
                                                        <input class="form-control" id="father_employer" name="father_employer" type="text"  placeholder="Enter Employer Details" />
                                                    </div>
                                                </div>                                                                                                                          
                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="fatherEmail">Email</label>
                                                        <input class="form-control" id="fatherEmail" name="fatherEmail" type="text" type="email" aria-describedby="emailHelp"  placeholder="Enter Email" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="fatherFixedPhone">Tel.(Local)</label>
                                                        <input class="form-control" id="fatherFixedPhone" name="fatherFixedPhone" type="text"  placeholder="Enter Tel.No." />
                                                    </div>
                                                </div>   
                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="fatherMobileNo">Mobile No</label>
                                                        <input class="form-control" id="fatherMobileNo" name="fatherMobileNo" type="text"  placeholder="Enter Mobile No" />
                                                    </div>
                                                </div>                 
                                            </div>

                                            <hr>
                                            Mother's Details <span class="error" style="color: #FF0000;">*</span>

                                            <div class="form-row">
                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="motherName">Name</label>
                                                        <input class="form-control" id="motherName" name="motherName" type="text" required  placeholder="Enter Name" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="motherJob">Occupation</label>
                                                        <input class="form-control" id="motherJob" name="motherJob" type="text"  placeholder="Enter Occupation" />
                                                    </div>
                                                </div>   
                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="mother_employer">Employer Address</label>
                                                        <input class="form-control" id="mother_employer" name="mother_employer" type="text"  placeholder="Enter Employer Details" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="motherEmail">Email</label>
                                                        <input class="form-control" id="motherEmail" name="motherEmail" type="text" type="email" aria-describedby="emailHelp"  placeholder="Enter Email" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="motherFixelPhone">Tel.(Local)</label>
                                                        <input class="form-control" id="motherFixelPhone" name="motherFixelPhone" type="text"  placeholder="Enter Tel.No" />
                                                    </div>
                                                </div>   
                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="motherMobileNo">Mobile No</label>
                                                        <input class="form-control" id="motherMobileNo" name="motherMobileNo" type="text"  placeholder="Enter Mobile No" />
                                                    </div>
                                                </div>                    
                                            </div>

                                             <hr>
                                            Guardian's Details

                                            <div class="form-row">
                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="guardianName">Name</label>
                                                        <input class="form-control" id="guardianName" name="guardianName" type="text"   placeholder="Enter Name" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="guardianJob">Occupation</label>
                                                        <input class="form-control" id="guardianJob" name="guardianJob" type="text"  placeholder="Enter Occupation" />
                                                    </div>
                                                </div>   
                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="guardian_employer">Employer Address</label>
                                                        <input class="form-control" id="guardian_employer" name="guardian_employer" type="text"  placeholder="Enter Employer Details" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="guardianEmail">Email</label>
                                                        <input class="form-control" id="guardianEmail" name="guardianEmail" type="text" type="email" aria-describedby="emailHelp"  placeholder="Enter Email" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="guardianFixelPhone">Tel.(Local)</label>
                                                        <input class="form-control" id="guardianFixelPhone" name="guardianFixelPhone" type="text"  placeholder="Enter Tel.No" />
                                                    </div>
                                                </div>   
                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="guardianMobileNo">Mobile No</label>
                                                        <input class="form-control" id="guardianMobileNo" name="guardianMobileNo" type="text"  placeholder="Enter Mobile No" />
                                                    </div>
                                                </div>                    
                                            </div>
                                            <br><br>
                                            <h5 style="color: #007300; font-size: x-large;">Non-Related Refrees <span class="error" style="color: #FF0000;">*</span></h5>
                                            <hr>
                                            <label class="small mb-1" for="refree1">Give the Name & Address of two non-related persons of good standing in your own country who could, from their personal knowledge, attest your character, academic background and capacity to undertake the study</label>
                                            <div class="form-row">
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <div class="form-group">   
                                                        <textarea class="form-control" id="refree1_details" name="refree1_details" rows="3"></textarea>
                                                        <div class="form-row">
                                                            <label class="small mb-1" for="refree1_phone"> Contact No</label>
                                                            <input class="form-control" id="refree1_phone" name="refree1_phone" type="text"  placeholder="" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <div class="form-group">    
                                                        <textarea class="form-control" id="refree2_details" name="refree2_details" rows="3"></textarea>
                                                        <div class="form-row">
                                                            <label class="small mb-1" for="refree2_phone"> Contact No</label>
                                                            <input class="form-control" id="refree2_phone" name="refree2_phone" type="text"  placeholder="" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <div class="form-group">   
                                                        <label class="small mb-1" for="addressOffice">If you are a non-Sri Lankan and know of any Sri Lanakan citizen permanently residing in Sri Lanaka who could act as your refree, Mention the Nama & Address</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <div class="form-group">    
                                                        <textarea class="form-control" id="refree_sl_details" name="refree_sl_details" rows="3"></textarea>
                                                        <div class="form-row">
                                                            <label class="small mb-1" for="refree_sl_phone"> Contact No</label>
                                                            <input class="form-control" id="refree_sl_phone" name="refree_sl_phone" type="text"  placeholder="" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <br><br>
                                            <hr>
                                            
                                            <div class="form-row">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="fund">Specify how much fund would be available to you whilst in Sri Lanaka, and the sourse of such funds.</label>
                                                        <input class="form-control" id="fund" type="text" name="fund"   />                                                        
                                                    </div>
                                                </div>                                                                                                                                               
                                            </div>

                                            <div class="form-row">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="docupldlink">Copy the link of your uploaded documents <span class="error" style="color: #FF0000; font-size: x-large;">*</span> (Upload scanned copies of your educational,employment certificates to any storage like google drive, dropbox under a folder named by your pasport number.Then get the publicly downloadable link and paste it here.)</label>                                                       
                                                        <input class="form-control" id="docupldlink" type="text" name="docupldlink" required  placeholder="Paste the downloadable link of your uploaded documents folder" />
                                                    </div>
                                                </div>                                                                                                                                               
                                            </div>

                                            <hr>
                                            <!-- <input type="hidden" id="closing_date" name="closing_date" value="<?php if(isset($_POST['application_closing_date'])) echo $_POST['application_closing_date']; ?>"> -->
                                            <!-- <input type="hidden" id="minDate" name="minDate" value="<?php if(isset($_POST['minDate'])) echo $_POST['minDate']; ?>">
                                            <input type="hidden" id="dob" name="dob" value="<?php if(isset($_POST['dob'])) echo $_POST['dob']; ?>"> -->
                                            <!-- <div class="form-row" style="background-color: #ccc;">
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <div class="form-group">
                                                        <label class="small mb-1" for="inputMediaSource">How you came to know about us ?</label>
                                                        <select class="form-control" id="inputMediaSource" name="inputMediaSource" >
                                                            <option value="">Please Select</option>
                                                            <option value="Newspaper">Newspaper</option>
                                                            <option value="Social media">Social media</option>
                                                            <option value="TV advertisement">TV advertisement</option>  
                                                            <option value="Other">Other</option>                                                                                                                                                                                
                                                        </select>
                                                    </div>
                                                </div>                                                
                                            </div> -->

                                            <div class="form-row"> <!-- edit 2022-08-15  -->
                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                    <input type="submit" class="btn btn-primary btn-block" name="save" value="Save Application" />                                                                                                                                           
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                    <a href="application_formpdf.php?lsidn=<?php echo $enc_last_id; ?>&idn=<?php echo $enc_nic_no; ?>"><input type="submit" class="btn btn-primary btn-block" name="submit" value="Submit Application" /></a>                                                                                                                                                
                                                </div>
                                            
                                        </form>
                                    </div>
                                    <br>
                                    <div class="card-footer text-center">
                                        <div class="small"><a href="#">Application step (2/2) </a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
            <div id="layoutAuthentication_footer">
                <footer class="py-4 bg-light mt-2">
                    <div class="container-fluid">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; <?php echo date('Y');?> General Sir John Kotelawala Defence University</div>
                            <div>
                                <a href="#">Developed by CITS&DS</a>
                                &middot;
                                
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <!--<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>-->
        <script src="dist/js/jquery-3-5-min.js"></script>
        <!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>-->
        <script src="dist/js/bootstrap-bundle.js" crossorigin="anonymous"></script>
        <script src="dist/js/scripts.js"></script>
        <script src="dist/js/managerows.js"></script>
    </body>
</html>
<script type="text/javascript">
    // disable right click
    document.addEventListener('contextmenu', event => event.preventDefault());
 
    document.onkeydown = function (e) {
 
        // disable F12 key
        if(e.keyCode == 123) {
            return false;
        }
 
        // disable I key
        if(e.ctrlKey && e.shiftKey && e.keyCode == 73){
            return false;
        }
 
        // disable J key
        if(e.ctrlKey && e.shiftKey && e.keyCode == 74) {
            return false;
        }
 
        // disable U key
        if(e.ctrlKey && e.keyCode == 85) {
            return false;
        }
    }//end disable right click
    document.onbeforeunload = function() { return "Your work will be lost."; };

    function validateForm(){
        //alert("radio");     
    
    if(document.forms["my-form"]["inputCourse"].value == "" ){
               Swal.fire({
              icon: 'warning',
              title: 'Missing Data',
              text: 'Please Select Course!',
             onAfterClose: () => {
                document.forms["my-form"]["inputCourse"].focus();
             }
            })
        return false;
    }
    if(document.forms["my-form"]["inputDob"].value == "" ){
               Swal.fire({
              icon: 'warning',
              title: 'Missing Data',
              text: 'Please Enter Birth Day!',
             onAfterClose: () => {
                document.forms["my-form"]["inputDob"].focus();
             }
            })
        return false;
    }if(document.forms["my-form"]["citizenship_type"].value == "" ){
               Swal.fire({
              icon: 'warning',
              title: 'Missing Data',
              text: 'Please Select citizenship Type!',
             onAfterClose: () => {
                document.forms["my-form"]["citizenship_type"].focus();
             }
            })
        return false;
    }
    if(document.forms["my-form"]["inputGender"].value == "" ){
               Swal.fire({
              icon: 'warning',
              title: 'Missing Data',
              text: 'Please Select Gender!',
             onAfterClose: () => {
                document.forms["my-form"]["inputGender"].focus();
             }
            })
        return false;
    }if(document.forms["my-form"]["inputCivilSts"].value == "" ){
               Swal.fire({
              icon: 'warning',
              title: 'Missing Data',
              text: 'Please Select Status!',
             onAfterClose: () => {
                document.forms["my-form"]["inputCivilSts"].focus();
             }
            })
        return false;
    }if(document.forms["my-form"]["refree1_details"].value == "" ){
               Swal.fire({
              icon: 'warning',
              title: 'Missing Data',
              text: 'Please Enter Refree Details!',
             onAfterClose: () => {
                document.forms["my-form"]["refree1_details"].focus();
             }
            })
        return false;
    }
    if(document.forms["my-form"]["refree1_phone"].value == "" ){
               Swal.fire({
              icon: 'warning',
              title: 'Missing Data',
              text: 'Please Enter Refree Contact No!',
             onAfterClose: () => {
                document.forms["my-form"]["refree1_phone"].focus();
             }
            })
        return false;
    }
    if(document.forms["my-form"]["citizenship_type"].value == "Foreign Citizenship" ){
        if(document.forms["my-form"]["inputCitizenship"].value == "" ){
               Swal.fire({
              icon: 'warning',
              title: 'Missing Data',
              text: 'Please Enter Citizenship!',
             onAfterClose: () => {
                document.forms["my-form"]["inputCitizenship"].focus();
             }
            })
        return false;
    }
    }if(document.forms["my-form"]["citizenship_type"].value == "Dual Citizenship" ){
        if(document.forms["my-form"]["inputCitizenship1"].value == "" ){
               Swal.fire({
              icon: 'warning',
              title: 'Missing Data',
              text: 'Please Enter Dual Citizenship!',
             onAfterClose: () => {
                document.forms["my-form"]["inputCitizenship1"].focus();
             }
            })
            return false;
        }if(document.forms["my-form"]["inputCitizenship2"].value == "" ){
                   Swal.fire({
                  icon: 'warning',
                  title: 'Missing Data',
                  text: 'Please Enter Dual Citizenship!',
                 onAfterClose: () => {
                    document.forms["my-form"]["inputCitizenship2"].focus();
                 }
                })
            return false;
        }
    }
    

    /* if(document.forms["my-form"]["eduAgent"].value == "" ){////2022-07-20
        Swal.fire({
                icon: 'warning',
                title: 'Missing Data',
                text: 'Please Enter Education Agent!',
                onAfterClose: () => {
                    document.forms["my-form"]["eduAgent"].focus();
                }
                })
            return false;
    }else{
        if(document.forms["my-form"]["eduAgent"].value == "Yes" ){
            if(document.forms["my-form"]["nameEduAgent"].value == "" ){
                Swal.fire({
                icon: 'warning',
                title: 'Missing Data',
                text: 'Please Enter Name of education agent!',
                onAfterClose: () => {
                    document.forms["my-form"]["nameEduAgent"].focus();
                }
                })
            return false;
        }
    }
    } */

    var closingDate = document.forms["my-form"]["closingDate"].value;
    //alert('closing date:'+closingDate);
    var dob1 = document.forms["my-form"]["inputDob"].value;
    //alert('dob'+dob1);
    var dob = moment(dob1, 'YYYY-MM-DD').format('MM/DD/YYYY');
    var endDate = moment(closingDate, 'YYYY-MM-DD').format('MM/DD/YYYY');
    //console.log(dob);

    var year25 =  moment(endDate, 'MM/DD/YYYY').subtract(30, 'years').format('MM/DD/YYYY');

    var year17 =  moment(endDate, 'MM/DD/YYYY').subtract(17, 'years').format('MM/DD/YYYY');
       //console.log('25 year :' + year25);
        //console.log('17 year :' + year17);
    var d_dob = dob.split("/");
    var d_25 = year25.split("/");
    var d_17 = year17.split("/");


    var bday = new Date(d_dob[2], parseInt(d_dob[0])-1, d_dob[1]);  // -1 because months are from 0 to 11
    var date25   = new Date(d_25[2], parseInt(d_25[0])-1, d_25[1]);
    var date17 = new Date(d_17[2], parseInt(d_17[0])-1, d_17[1]);
    //console.log((bday >= date25) && (bday <= date17)) ;
    if(date17 < bday ){
        Swal.fire({
              icon: 'warning',
              title: 'Age Limit',
              text: 'You are younger than 17!',
             onAfterClose: () => {
                document.forms["my-form"]["inputDob"].focus();
             }
            })
         return false;
    }if(bday < date25){
        Swal.fire({
              icon: 'warning',
              title: 'Age Limit',
              text: 'You are older than 30!',
             onAfterClose: () => {
                document.forms["my-form"]["inputDob"].focus();
             }
            })
         return false;
    }
    $("#minDate").val(date25);
    $("#maxDate").val(date17);
    $("#dob").val(bday);
    console.log('17: '+date17);
    console.log('25:'+date25 );
    console.log('17 > age: '+date17 > bday );
    console.log('age > 25: '+bday > date25 );


    /*var minDate = document.forms["my-form"]["minDate"].value;
    var maxDate = document.forms["my-form"]["maxDate"].value;
    var dob = document.forms["my-form"]["dob"].value;
    if(maxDate < dob ){
        Swal.fire({
              icon: 'warning',
              title: 'Age Limit',
              text: 'You are younger than 17!',
             onAfterClose: () => {
                document.forms["my-form"]["inputDob"].focus();
             }
            })
        return false;
    }if(dob < minDate){
        Swal.fire({
              icon: 'warning',
              title: 'Age Limit',
              text: 'You are older than 25!',
             onAfterClose: () => {
                document.forms["my-form"]["inputDob"].focus();
             }
            })
        return false;
    }
*/
   }

   function validateDate(val) {
    //console.log(val);
    var dob = moment(val, 'YYYY-MM-DD').format('MM/DD/YYYY');
    var closingDate = document.forms["my-form"]["closingDate"].value;
    //alert('closing date:'+closingDate);
    var endDate = moment(closingDate, 'YYYY-MM-DD').format('MM/DD/YYYY');
    //console.log(dob);

    var year25 =  moment(endDate, 'MM/DD/YYYY').subtract(30, 'years').format('MM/DD/YYYY');

    var year17 =  moment(endDate, 'MM/DD/YYYY').subtract(17, 'years').format('MM/DD/YYYY');
        //console.log('17 year :' + year17);
    var d_dob = dob.split("/");
    var d_25 = year25.split("/");
    var d_17 = year17.split("/");


    var bday = new Date(d_dob[2], parseInt(d_dob[0])-1, d_dob[1]);  // -1 because months are from 0 to 11
    var date25   = new Date(d_25[2], parseInt(d_25[0])-1, d_25[1]);
    var date17 = new Date(d_17[2], parseInt(d_17[0])-1, d_17[1]);
    //console.log((bday >= date25) && (bday <= date17)) ;
    if(date17 < bday ){
        Swal.fire({
              icon: 'warning',
              title: 'Age Limit',
              text: 'You are younger than 17!',
             onAfterClose: () => {
                document.forms["my-form"]["inputDob"].focus();
             }
            })
    }if(bday < date25){
        Swal.fire({
              icon: 'warning',
              title: 'Age Limit',
              text: 'You are older than 30!',
             onAfterClose: () => {
                document.forms["my-form"]["inputDob"].focus();
             }
            })
    }
    $("#minDate").val(date25);
    $("#maxDate").val(date17);
    $("#dob").val(bday);
    console.log('17: '+date17);
    console.log('25:'+date25 );
    console.log('17 > age: '+date17 > bday );
    console.log('age > 25: '+bday > date25 );
    

    }
    $(document).ready(function(){
// Prepare the preview for profile picture
    $("#Photo").change(function(){
        readURL(this);
    });
});
function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#wizardPicturePreview').attr('src', e.target.result).fadeIn('slow');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
$(document).ready(function(){

    /*$('#edurowcnt').val(0);
    $('#edurowcnt2').val(0);
    $('#edurowcnt3').val(0);*/

//alert(document.forms["my-form"]["inputIntakeYr"].value)
  $("#foreign").click(function(){
    //alert(document.forms["my-form"]["citizenship_type"].value);
    document.getElementById("section1").style.display = 'flex';
    document.getElementById("section2").style.display = 'none';
    document.getElementById("section3").style.display = 'none';
    $("#inputCitizenship1").val("");
    $("#inputCitizenship2").val("");
    
  });
  $("#dual").click(function(){
    //alert("A");
    document.getElementById("section1").style.display = 'none';
    document.getElementById("section2").style.display = 'flex';
    document.getElementById("section3").style.display = 'flex';
    $("#inputCitizenship").val("");
    
  });
  $("#sriLanakan").click(function(){
    document.getElementById("section1").style.display = 'none';
    document.getElementById("section2").style.display = 'none';
    document.getElementById("section3").style.display = 'none';
    $("#inputCitizenship").val("");
    $("#inputCitizenship1").val("");
    $("#inputCitizenship2").val("");
    
  });
  $("#eduAgent").change(function(){ //2022-07-20
    //alert("A"); 
    var ddlFruits = document.getElementById("eduAgent");
        if (ddlFruits.value == "Yes") {
            document.getElementById("section4").style.display = 'flex';
        }if (ddlFruits.value == "No") {
            document.getElementById("section4").style.display = 'none';
            $("#nameEduAgent").val("");
        }
    
    //$("#inputCitizenship").val("");
    
  });

});
</script>