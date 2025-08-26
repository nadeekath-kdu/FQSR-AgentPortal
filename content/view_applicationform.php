<?php
require_once '../pages/load_data_formedit.php';

?>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="../assets/css/edit_application.css" rel="stylesheet" />

<div class="imagebg"></div>
<div id="layoutAuthentication">
    <div id="layoutAuthentication_content">
        <main>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card shadow-lg border-0 rounded-lg mt-5">
                            <div class="card-header">
                                <center><img border="0" src="../assets/img/kdu/logo.jpg"></center>
                                <h4 class="text-center font-weight-light my-4">
                                    <b>Application for Admission of Students with Foreign Qualification for the Academic Year <?php echo $academic_year ?></b>
                                </h4>
                            </div>
                            <div class="card-body">
                                <?php
                                if ($err_code == 1 || $err_code == 2 || $err_code == 4 || $err_code == 5 || $err_code == 6 || $err_code == 7 || $err_code == 8) {
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
                                <!-- onsubmit="return validateForm()"  -->
                                <form name="my-form" id="my-form" method="post" enctype="multipart/form-data">

                                    <div class="form-row">
                                        <div class="col-lg- col-md-8 col-sm-12">
                                            <h5>Applicant Passport No : <?php echo $dec_nic_no; ?></h5>
                                        </div>
                                        <div class="col-lg- col-md-4 col-sm-12">
                                            <div class="container">
                                                <div class="picture-container">
                                                    <div class="picture">
                                                        <!-- <img  class="picture-src" id="wizardPicturePreview" title="">
                                                                <input type="file" id="Photo" name = "Photo" class="form-control" placeholder="Choose Image"> -->
                                                        <?PHP
                                                        if ($row_get_personal['Photo'] != '') {
                                                        ?>
                                                            <img src="../profile/<?php echo $row_get_personal['Photo'] ?>" class="picture-src" id="wizardPicturePreview" title="">

                                                        <?php
                                                        } else { ?>
                                                            <img class="picture-src" id="wizardPicturePreview" title="">

                                                        <?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-row">
                                        <div class="col-lg- col-md-6 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="inputCourse">Choose a course intending to follow</label>
                                                <input class="form-control" id="inputCourse" name="inputCourse" type="text" value="<?php echo $row_get_personal['course_name']; ?>" readonly />
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-12">
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-12">
                                            <div class="form-group" style="display: none;">
                                                <label class="small mb-1" for="inputIntakeYr">Intake year</label>
                                                <input class="form-control" id="inputAcademicYear" name="inputAcademicYear" type="text" value="<?php echo $academic_year; ?>" />
                                                <input class="form-control" id="inputIntakeYr" name="inputIntakeYr" type="text" value="<?php echo $intake; ?>" />
                                                <input class="form-control" id="inputNic" name="inputNic" type="hidden" required value="<?php echo $enc_nic_no; ?>" />
                                                <input type="hidden" id="closingDate" name="closingDate" value="<?php echo $application_closing_date; ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <h5>Personal Details</h5>
                                    <hr>
                                    <div class="form-row">
                                        <div class="col-lg-2 col-md-2 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="inputTitle">Title</label>
                                                <input class="form-control" id="inputTitle" name="inputTitle" type="text" value="<?php echo $row_get_personal['stu_title']; ?>" readonly />
                                            </div>
                                        </div>
                                        <div class="col-lg-7 col-md-7 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="inputFullname">Full Name <span class="error" style="color: #FF0000;">*</span></label>
                                                <input class="form-control" id="inputFullname" name="inputFullname" type="text" value="<?php echo $row_get_personal['stu_fullname']; ?>" readonly />
                                            </div>
                                        </div>

                                        <div class="col-lg-3 col-md-3 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="inputNameInitials">Name with initials <span class="error" style="color: #FF0000;">*</span></label>
                                                <input class="form-control" id="inputNameInitials" name="inputNameInitials" type="text" value="<?php echo $row_get_personal['stu_name_initials']; ?>" readonly />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="col-lg-3 col-md-3 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="inputDob">Date of birth <span class="error" style="color: #FF0000;">*</span></label>
                                                <input class="form-control" id="inputDob" name="inputDob" type="date" value="<?php echo $row_get_personal['stu_dob']; ?>" readonly />
                                                <!-- <input class="form-control" id="inputDob" name="inputDob" type="date" onchange="validateDate(this.value)" required placeholder="Enter your Date of birth" /> -->
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="inputGender">Gender <span class="error" style="color: #FF0000;">*</span></label>
                                                <input class="form-control" id="inputGender" name="inputGender" type="text" value="<?php echo $row_get_personal['stu_gender']; ?>" readonly />

                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="inputCivilSts">Civil Status <span class="error" style="color: #FF0000;">*</span>
                                                </label>
                                                <input class="form-control" id="inputCivilSts" name="inputCivilSts" type="text" value="<?php echo $row_get_personal['civil_status']; ?>" readonly />

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
                                            <input class="small mb-1" type="radio" id="sriLanakan" name="citizenship_type" required value="Sri Lankan Citizenship Only" <?php echo ($row_get_personal['citizenship_type'] == "Sri Lankan Citizenship Only") ? 'checked' : '' ?> disabled />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label class="small mb-1" for="sriLanakan" name="citizenship_ty">Sri Lankan Citizenship Only</label>

                                        </div>
                                        <div class="col-lg-3">
                                            <input class="small mb-1" type="radio" id="foreign" name="citizenship_type" value="Foreign Citizenship" <?php echo ($row_get_personal['citizenship_type'] == 'Foreign Citizenship') ? 'checked' : '' ?> disabled />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label class="small mb-1" for="foreign" name="citizenship_ty">Foreign Citizenship</label>
                                        </div>
                                        <div class="col-lg-3">
                                            <input class="small mb-1" type="radio" id="dual" name="citizenship_type" value="Dual Citizenship" <?php echo ($row_get_personal['citizenship_type'] == 'Dual Citizenship') ? 'checked' : '' ?> disabled />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label class="small mb-1" for="dual" name="citizenship_ty">Dual Citizenship</label>
                                        </div>
                                    </div>
                                    &nbsp;

                                    <div class="form-row" id="section1" style="display: none;">
                                        <label class="small mb-1" for="inputCitizenship">Country of Citizenship </label>
                                        <input class="form-control py-4" id="inputCitizenship" name="inputCitizenship" type="text" value="<?php echo $row_get_personal['stu_citizenship']; ?>" placeholder="Enter your Country of Citizenship" readonly />
                                    </div>
                                    &nbsp;
                                    <div class="form-row" id="section2" style="display: none;">
                                        <label class="small mb-1" for="inputCitizenship1">Mention Your Country of Citizenship 1 </label>
                                        <input class="form-control py-4" id="inputCitizenship1" name="inputCitizenship1" type="text" value="<?php echo $row_get_personal['citizenship_1']; ?>" placeholder="Enter your 1st Country" readonly />
                                    </div>
                                    &nbsp;
                                    <div class="form-row" id="section3" style="display: none;">
                                        <label class="small mb-1" for="inputCitizenship2">Mention Your Country of Citizenship 2</label>
                                        <input class="form-control py-4" id="inputCitizenship2" name="inputCitizenship2" type="text" value="<?php echo $row_get_personal['citizenship_2']; ?>" placeholder="Enter your 2nd Country" readonly />
                                    </div>
                                    &nbsp;
                                    <div class="form-group">
                                        <label class="small mb-1" for="inputCountryAL">What is the country that you have appeared for Advanced Level examination/ High School Diploma</label>
                                        <input class="form-control" id="inputCountryAL" name="inputCountryAL" type="text" value="<?php echo $row_get_personal['AL_sitting_country']; ?>" readonly />
                                    </div>
                                    <div class="form-row">
                                        <div class="col-lg-4 col-md-3 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="inputCountryBirth">Country of Birth<span class="error" style="color: #FF0000;">*</span></label>
                                                <input class="form-control" id="inputCountryBirth" name="inputCountryBirth" type="text" value="<?php echo $row_get_personal['birth_country']; ?>" readonly />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-3 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="periodStudy">Period of Study apart from Sri Lanka <span class="error" style="color: #FF0000;">*</span></label>
                                                <input class="form-control" id="periodStudy" name="periodStudy" type="text" value="<?php echo $row_get_personal['period_study_abroad']; ?>" readonly />
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
                                                <label class="small mb-1" for="addressPermanent">Permanent Address to which correspondence should be sent<span class="error" style="color: #FF0000;">*</span></label><?php $Address = $row_get_personal['stu_permenant_address']; ?>
                                                <textarea class="form-control text-start" id="addressPermanent" name="addressPermanent" rows="3" required style="align-content:left;" readonly>
                                                            <?php echo $Address; ?>
                                                        </textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="small mb-1" for="inputEmailAddress">Email address <span class="error" style="color: #FF0000;">*</span></label>
                                        <input class="form-control" id="inputEmailAddress" name="inputEmailAddress" type="email" aria-describedby="emailHelp" value="<?php echo $row_get_personal['stu_email']; ?>" readonly />
                                    </div>
                                    <h5>Educational Qualifications</h5>
                                    <hr>
                                    <h6>Examination equivalent to Advanced Level/ High School</h6>
                                    <?php
                                    $sql_edu_qual_al = "SELECT * FROM mst_educational_qualifications WHERE stu_nic = '$dec_nic_no' AND exm_type = 'A/L'";
                                    $res_edu_qual_al = mysqli_query($db_connection, $sql_edu_qual_al);
                                    $edu_al = mysqli_fetch_array($res_edu_qual_al);

                                    ?>
                                    <div class="form-group">
                                        <label class="small mb-1" for="examNameAL">Name of Examination</label>
                                        <input class="form-control" id="examNameAL" name="examNameAL" value="<?php echo $edu_al['exam_name']; ?>" readonly />
                                    </div>
                                    <!-- <div class="form-group">
                                                        <label class="small mb-1" for="examYearAL">Year of Examination</label>
                                                        <input class="form-control py-4" id="examYearAL" name="examYearAL"/>
                                                    </div> -->



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
                                                    <?php
                                                    //print_r(($edu_al));
                                                    $sql_edu_qual_al = "SELECT * FROM mst_educational_qualifications WHERE stu_nic = '$dec_nic_no' AND exm_type = 'A/L'";
                                                    $res_edu_qual_al = mysqli_query($db_connection, $sql_edu_qual_al);
                                                    $counter_al = 0;
                                                    while ($row_edu_qual_al = mysqli_fetch_array($res_edu_qual_al)) {
                                                        $sub_al = 'subject_AL_' . $counter_al;
                                                        $res_al = 'result_AL_' . $counter_al;
                                                        $year_al = 'year_AL_' . $counter_al;
                                                    ?>
                                                        <tr>
                                                            <td><input class="form-control" id="<?php echo $sub_al; ?>" type="text" name="<?php echo $sub_al; ?>" value="<?php echo $row_edu_qual_al['subject_grade']; ?>" readonly /></td>
                                                            <td><input class="form-control" id="<?php echo $res_al; ?>" type="text" name="<?php echo $res_al; ?>" value="<?php echo $row_edu_qual_al['award']; ?>" readonly /></td>
                                                            <td><input class="form-control" id="<?php echo $year_al; ?>" type="text" name="<?php echo $year_al; ?>" value="<?php echo $row_edu_qual_al['exam_year']; ?>" readonly /></td>
                                                        </tr>
                                                    <?php
                                                        $counter_al++;
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>



                                    <input class="form-control" id="edurowcnt" type="hidden" name="edurowcnt" value="<?php echo $counter_al; ?>" />
                                    <hr>
                                    <h6>Examination equivalent to Ordinary Level/ Secondary Education</h6>
                                    <?php
                                    $sql_edu_qual_ol = "SELECT * FROM mst_educational_qualifications WHERE stu_nic = '$dec_nic_no' AND exm_type = 'O/L'";
                                    $res_edu_qual_ol = mysqli_query($db_connection, $sql_edu_qual_ol);
                                    $row_edu_qual_ol = mysqli_fetch_array($res_edu_qual_ol);
                                    ?>
                                    <div class="form-group">
                                        <label class="small mb-1" for="examNameOL">Name of Examination</label>
                                        <input class="form-control" id="examNameOL" name="examNameOL" value="<?php echo $row_edu_qual_ol['exam_name']; ?>" readonly />
                                    </div>
                                    <!-- <div class="form-group">
                                                        <label class="small mb-1" for="examYearOL">Year of Examination</label>
                                                        <input class="form-control py-4" id="examYearOL" name="examYearOL"/>
                                                    </div> -->


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
                                                    <?php
                                                    $sql_edu_qual_ol = "SELECT * FROM mst_educational_qualifications WHERE stu_nic = '$dec_nic_no' AND exm_type = 'O/L'";
                                                    $res_edu_qual_ol = mysqli_query($db_connection, $sql_edu_qual_ol);
                                                    //$row_edu_qual_ol = mysqli_fetch_array($res_edu_qual_ol);
                                                    $counter_ol = 0;
                                                    while ($row_edu_qual_ol = mysqli_fetch_array($res_edu_qual_ol)) {
                                                        $sub_ol = 'subject_OL_' . $counter_ol;
                                                        $res_ol = 'result_OL_' . $counter_ol;
                                                        $year_ol = 'year_OL_' . $counter_ol;
                                                    ?>
                                                        <tr>
                                                            <td><input class="form-control" id="<?php echo $sub_ol; ?>" type="text" name="<?php echo $sub_ol; ?>" value="<?php echo $row_edu_qual_ol['subject_grade']; ?>" readonly /></td>
                                                            <td><input class="form-control" id="<?php echo $res_ol; ?>" type="text" name="<?php echo $res_ol; ?>" value="<?php echo $row_edu_qual_ol['award']; ?>" readonly /></td>
                                                            <td><input class="form-control" id="<?php echo $year_ol; ?>" type="text" name="<?php echo $year_ol; ?>" value="<?php echo $row_edu_qual_ol['exam_year']; ?>" readonly /></td>
                                                        </tr>
                                                    <?php $counter_ol++;
                                                    } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>


                                    <input class="form-control" id="edurowcnt2" type="hidden" name="edurowcnt2" value="<?php echo $counter_ol; ?>" readonly />
                                    <hr>
                                    <div class="form-row">
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="elegibleState1">State whether you are eligible for the admission to a state University in your country <span class="error" style="color: #FF0000;">*</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="elegibleState"> </label>
                                                <input class="form-control" id="elegibleState" name="elegibleState" type="text" value="<?php echo $row_get_personal['eligibility_uni_admision']; ?>" readonly />

                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <label class="small mb-1" for="ele"></label>
                                    </div>

                                    <div class="form-row"> <!-- 2022-07-20 -->
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="eduAgent">Have you been assisted by an education agent <span class="error" style="color: #FF0000; font-size: x-large;">*</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="eduAgent"> </label>
                                                <input class="form-control" id="eduAgent" name="eduAgent" type="text" value="<?php echo $row_get_personal['isEduAgent']; ?>" readonly />

                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                        </div>
                                    </div>
                                    <div class="form-group" id="section4" style="display: none;">
                                        <label class="small mb-1" for="nameEduAgent">Name of Agent</label>
                                        <input class="form-control" id="nameEduAgent" name="nameEduAgent" value="<?php echo $row_get_personal['nameEduAgent']; ?>" readonly />
                                    </div> <!-- end 2022-07-20 -->

                                    <h5>English Language Proficiency </h5>
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
                                                    <?php
                                                    $sql_eng_prof = "SELECT * FROM mst_english_proficiency WHERE stu_passport_id = '$dec_nic_no' AND qualification_type != 'SAT'";
                                                    $res_eng_prof = mysqli_query($db_connection, $sql_eng_prof);
                                                    //$row_eng_prof[] = mysqli_fetch_array($res_eng_prof);
                                                    $counter_ep = 0;
                                                    while ($eng_prof = mysqli_fetch_array($res_eng_prof)) {
                                                        $sub_ep = 'name_EP_' . $counter_ep;
                                                        $res_ep = 'result_EP_' . $counter_ep;
                                                        $year_ep = 'year_EP_' . $counter_ep;
                                                    ?>
                                                        <tr>
                                                            <td><input class="form-control" id="<?php echo $sub_ep; ?>" type="text" name="<?php echo $sub_ep; ?>" value="<?php echo $eng_prof['qualification_type']; ?>" readonly /></td>
                                                            <td><input class="form-control" id="<?php echo $res_ep; ?>" type="text" name="<?php echo $res_ep; ?>" value="<?php echo $eng_prof['result']; ?>" readonly /></td>
                                                            <td><input class="form-control" id="<?php echo $year_ep; ?>" type="text" name="<?php echo $year_ep; ?>" value="<?php echo $eng_prof['year']; ?>" readonly /></td>
                                                        </tr>
                                                    <?php $counter_ep++;
                                                    } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>


                                    <input class="form-control" id="edurowcnt3" type="hidden" name="edurowcnt3" value="<?php echo $counter_ep; ?>" />
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
                                                        <td><input class="form-control" id="sat_result" type="text" name="sat_result" value="<?php echo $row_eng_prof_sat['result']; ?>" readonly /></td>
                                                        <td><input class="form-control" id="sat_passing_year" type="text" name="sat_passing_year" value="<?php echo $row_eng_prof_sat['year']; ?>" readonly /></td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>


                                    <h5>Other Qualifications (If Any)</h5>
                                    <hr>

                                    <div class="form-row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="otherQualifications">Enter any other qualification details in below area</label>
                                                <textarea class="form-control text-start" id="otherQualifications" name="otherQualifications" rows="4" maxlength="250" readonly>
                                                            <?php echo $row_get_personal['other_qualification']; ?>
                                                        </textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <h5>Parent Details </h5>
                                    <hr>
                                    Father's Details <span class="error" style="color: #FF0000;">*</span>

                                    <div class="form-row">
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="fatherName">Name</label>
                                                <input class="form-control" id="fatherName" name="fatherName" type="text" required value="<?php echo $row_family_father['name']; ?>" readonly />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="fatherJob">Occupation</label>
                                                <input class="form-control" id="fatherJob" name="fatherJob" type="text" value="<?php echo $row_family_father['job']; ?>" readonly />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="father_employer">Employer Address</label>
                                                <input class="form-control" id="father_employer" name="father_employer" type="text" value="<?php echo $row_family_father['employey_details']; ?>" readonly />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="fatherEmail">Email</label>
                                                <input class="form-control" id="fatherEmail" name="fatherEmail" type="text" type="email" aria-describedby="emailHelp" value="<?php echo $row_family_father['email']; ?>" readonly />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="fatherFixedPhone">Tel.(Local)</label>
                                                <input class="form-control" id="fatherFixedPhone" name="fatherFixedPhone" type="text" value="<?php echo $row_family_father['fixed_phone']; ?>" readonly />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="fatherMobileNo">Mobile No</label>
                                                <input class="form-control" id="fatherMobileNo" name="fatherMobileNo" type="text" value="<?php echo $row_family_father['mobile_no']; ?>" readonly />
                                            </div>
                                        </div>
                                    </div>

                                    <hr>
                                    Mother's Details <span class="error" style="color: #FF0000;">*</span>

                                    <div class="form-row">
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="motherName">Name</label>
                                                <input class="form-control" id="motherName" name="motherName" type="text" required value="<?php echo $row_family_mother['name']; ?>" readonly />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="motherJob">Occupation</label>
                                                <input class="form-control" id="motherJob" name="motherJob" type="text" value="<?php echo $row_family_mother['job']; ?>" readonly />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="mother_employer">Employer Address</label>
                                                <input class="form-control" id="mother_employer" name="mother_employer" type="text" value="<?php echo $row_family_mother['employey_details']; ?>" readonly />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="motherEmail">Email</label>
                                                <input class="form-control" id="motherEmail" name="motherEmail" type="text" type="email" aria-describedby="emailHelp" value="<?php echo $row_family_mother['email']; ?>" readonly />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="motherFixelPhone">Tel.(Local)</label>
                                                <input class="form-control" id="motherFixelPhone" name="motherFixelPhone" type="text" value="<?php echo $row_family_mother['fixed_phone']; ?>" readonly />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="motherMobileNo">Mobile No</label>
                                                <input class="form-control" id="motherMobileNo" name="motherMobileNo" type="text" value="<?php echo $row_family_mother['mobile_no']; ?>" readonly />
                                            </div>
                                        </div>
                                    </div>

                                    <hr>
                                    Guardian's Details

                                    <div class="form-row">
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="guardianName">Name</label>
                                                <input class="form-control" id="guardianName" name="guardianName" type="text" value="<?php echo $row_family_guardian['name']; ?>" readonly />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="guardianJob">Occupation</label>
                                                <input class="form-control" id="guardianJob" name="guardianJob" type="text" value="<?php echo $row_family_guardian['job']; ?>" readonly />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="guardian_employer">Employer Address</label>
                                                <input class="form-control" id="guardian_employer" name="guardian_employer" type="text" value="<?php echo $row_family_guardian['employey_details']; ?>" readonly />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="guardianEmail">Email</label>
                                                <input class="form-control" id="guardianEmail" name="guardianEmail" type="text" type="email" aria-describedby="emailHelp" value="<?php echo $row_family_guardian['email']; ?>" readonly />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="guardianFixelPhone">Tel.(Local)</label>
                                                <input class="form-control" id="guardianFixelPhone" name="guardianFixelPhone" type="text" value="<?php echo $row_family_guardian['fixed_phone']; ?>" readonly />
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="guardianMobileNo">Mobile No</label>
                                                <input class="form-control" id="guardianMobileNo" name="guardianMobileNo" type="text" value="<?php echo $row_family_guardian['mobile_no']; ?>" readonly />
                                            </div>
                                        </div>
                                    </div>
                                    <h5>Non-Related Refrees <span class="error" style="color: #FF0000;">*</span></h5>
                                    <hr>
                                    <label class="small mb-1" for="refree1">Give the Name & Address of two non-related persons of good standing in your own country who could, from their personal knowledge, attest your character, academic background and capacity to undertake the study</label>
                                    <div class="form-row">
                                        <?php
                                        $sql_refree = "SELECT * FROM refree WHERE stu_passport_id = '$dec_nic_no' AND type = 'FOREIGN'";
                                        $res_refree = mysqli_query($db_connection, $sql_refree);

                                        $ref_name_1 = "";
                                        $ref_contact_1 = "";
                                        $ref_name_2 = "";
                                        $ref_contact_2 = "";
                                        $ref_name = '$ref_name_';
                                        $ref_contact = '$ref_contact_';

                                        $num = 0;
                                        while ($row = mysqli_fetch_assoc($res_refree)) {
                                            if ($num == 0) {
                                                $ref_name_1 = $row['refree_details'];
                                                $ref_contact_1  =   $row['contact_no'];
                                            } else {
                                                $ref_name_2 = $row['refree_details'];
                                                $ref_contact_2  =   $row['contact_no'];
                                            }

                                            $num = $num + 1;
                                        }

                                        //echo $ref_name_1 . "-----------" .$ref_name_2;
                                        ?>
                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                            <div class="form-group">
                                                <textarea class="form-control text-start" id="refree1_details" name="refree1_details" rows="3" readonly>
                                                            <?php echo $ref_name_1; ?>
                                                        </textarea>
                                                <div class="form-row">
                                                    <label class="small mb-1" for="refree1_phone"> Contact No</label>
                                                    <input class="form-control" id="refree1_phone" name="refree1_phone" type="text" value="<?php echo $ref_contact_1; ?>" readonly />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                            <div class="form-group">
                                                <textarea class="form-control text-start" id="refree2_details" name="refree2_details" rows="3" readonly>
                                                            <?php echo $ref_name_2; ?>
                                                        </textarea>
                                                <div class="form-row">
                                                    <label class="small mb-1" for="refree2_phone"> Contact No</label>
                                                    <input class="form-control" id="refree2_phone" name="refree2_phone" type="text" value="<?php echo $ref_contact_2; ?>" readonly />
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
                                                <textarea class="form-control text-start" id="refree_sl_details" name="refree_sl_details" rows="3" readonly>
                                                            <?php echo $row_refree_sl['refree_details']; ?>
                                                        </textarea>
                                                <div class="form-row">
                                                    <label class="small mb-1" for="refree_sl_phone"> Contact No</label>
                                                    <input class="form-control" id="refree_sl_phone" name="refree_sl_phone" type="text" value="<?php echo $row_refree_sl['contact_no']; ?>" readonly />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="form-row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="fund">Specify how much fund would be available to you whilst in Sri Lanaka, and the sourse of such funds.</label>
                                                <input class="form-control" id="fund" type="text" name="fund" value="<?php echo $row_get_personal['fund']; ?>" readonly />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="form-group">
                                                <label class="small mb-1" for="docupldlink">Copy the link of your uploaded documents <span class="error" style="color: #FF0000;">*</span> (Upload scanned copies of your educational,employment certificates to any storage like google drive, dropbox under a folder named by your pasport number.Then get the publicly downloadable link and paste it here.)</label>
                                                <input class="form-control" id="docupldlink" type="text" name="docupldlink" required value="<?php echo $row_get_personal['doc_upload_link']; ?>" readonly />
                                            </div>
                                        </div>
                                    </div>

                                    <hr>


                                    <div class="form-row">
                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                            <div class="form-group mt-4 mb-0"><input name="submit1" type="button" class="btn btn-primary btn-block btn-edit" value="Need to Edit Details" data-nic="<?php echo htmlspecialchars($dec_nic_no); ?>" />
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                            <div class="form-group mt-4 mb-0">
                                                <input type="button" class="btn btn-success btn-block btn-checkout" value="Details are Correct, Proceed to Checkout" data-nic="<?php echo htmlspecialchars($dec_nic_no); ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

</div>

<!-- <script src="../assets/js/managerows.js"></script>-->
<script src="../assets/js/app/formupdate.js"></script>
<script src="../assets/js/view_applicationform.js"></script>