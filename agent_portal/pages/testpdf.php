<?php
// Include the TCPDF library
require_once('../tcpdf/tcpdf.php');

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//$pdf->AliasNbPages();
//$pdf->SetFont('times', '', 10);
// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Sample PDF using TCPDF');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');



// Add a page
$pdf->AddPage();

// Set some content to display
$pdf->SetFont('times', '', 10);
//$pdf->SetFont('times', 'BI', 16);
//$pdf->Write(10, 'Hello, World!');
//$pdf->Write(10, 'GENERAL SIR JOHN KOTELAWALA DEFENCE UNIVERSITY APPLICATION FOR FOREIGN STUDENTS DEGREE PROGRAMS');
//$pdf = new PDF();
    

    // Output personal details section
$pdf->Cell(0, 10, 'PERSONAL DETAILS', 0, 1, 'L');
$pdf->SetFont('times', '', 9);
    $pdf->Cell(40, 10, 'Applied course', 0, 0, 'L');
    //$pdf->Cell(150, 10, $row_get_personal['course_name'], 0, 1, 'L');
    $pdf->Cell(40, 10, 'Application submit date', 0, 0, 'L');
    //$pdf->Cell(150, 10, $row_get_personal['application_submit_dt'], 0, 1, 'L');
    $pdf->Cell(40, 10, 'Full Name', 0, 0, 'L');
    //$pdf->Cell(150, 10, strtoupper($row_get_personal['stu_fullname']), 0, 1, 'L');
    $pdf->Cell(40, 10, 'Name with initials', 0, 0, 'L');
    //$pdf->Cell(150, 10, strtoupper($row_get_personal['stu_title'] . ". " . $row_get_personal['stu_name_initials']), 0, 1, 'L');
    $pdf->Cell(40, 10, 'Date of birth', 0, 0, 'L');
    //$pdf->Cell(150, 10, $row_get_personal['stu_dob'], 0, 1, 'L');
    $pdf->Cell(40, 10, 'Gender', 0, 0, 'L');
    //$pdf->Cell(150, 10, $row_get_personal['stu_gender'], 0, 1, 'L');
    $pdf->Cell(40, 10, 'Civil status', 0, 0, 'L');
    //$pdf->Cell(150, 10, $row_get_personal['civil_status'], 0, 1, 'L');
    $pdf->Cell(40, 10, 'Permanent Address', 0, 0, 'L');
    //$pdf->MultiCell(150, 10, strtoupper($row_get_personal['stu_permenant_address']), 0, 'L');
    $pdf->Cell(40, 10, 'Email Address', 0, 0, 'L');
    //$pdf->Cell(150, 10, $row_get_personal['stu_email'], 0, 1, 'L');
    $pdf->Cell(40, 10, 'NIC No', 0, 0, 'L');
    //$pdf->Cell(150, 10, $row_get_personal['nic_no'], 0, 1, 'L');
    $pdf->Cell(40, 10, 'Citizenship', 0, 0, 'L');
    //$pdf->Cell(150, 10, $row_get_personal['citizenship_type'], 0, 1, 'L');

    /* if ($row_get_personal['citizenship_type'] == 'Foreign Citizenship') {
        $pdf->Cell(40, 10, 'Country of Citizenship', 0, 0, 'L');
        $pdf->Cell(150, 10, $row_get_personal['citizenship'], 0, 1, 'L');
    } elseif ($row_get_personal['citizenship_type'] == 'Dual Citizenship') {
        $pdf->Cell(40, 10, '1st Country of Citizenship', 0, 0, 'L');
        $pdf->Cell(150, 10, $row_get_personal['citizenship_1'], 0, 1, 'L');
        $pdf->Cell(40, 10, '2nd Country of Citizenship', 0, 0, 'L');
        $pdf->Cell(150, 10, $row_get_personal['citizenship_2'], 0, 1, 'L');
    } */

    $pdf->Cell(40, 10, 'Country of Birth', 0, 0, 'L');
    //$pdf->Cell(150, 10, $row_get_personal['country_of_birth'], 0, 1, 'L');
    $pdf->Cell(40, 10, 'Country of Permanent Residence', 0, 0, 'L');
    //$pdf->Cell(150, 10, $row_get_personal['country_of_permenant_residence'], 0, 1, 'L');
    $pdf->Cell(40, 10, 'Contact Number', 0, 0, 'L');
    //$pdf->Cell(150, 10, $row_get_personal['stu_mobile'], 0, 1, 'L');
    $pdf->Cell(40, 10, 'Passport No', 0, 0, 'L');
    //$pdf->Cell(150, 10, $row_get_personal['passport_no'], 0, 1, 'L');
    $pdf->Ln(10);

    // Output educational qualifications section
    $pdf->SetFont('times', 'B', 10);
    $pdf->Cell(0, 10, 'EDUCATIONAL QUALIFICATIONS', 0, 1, 'L');
    $pdf->SetFont('times', '', 9);

    $pdf->Cell(60, 10, 'Name of the Exam', 1, 0, 'C');
    $pdf->Cell(60, 10, 'Year', 1, 0, 'C');
    $pdf->Cell(60, 10, 'Index Number', 1, 1, 'C');
    
    /* while ($row_edu_qual = mysqli_fetch_array($res_edu_qual)) {
        $pdf->Cell(60, 10, $row_edu_qual['exam_name'], 1, 0, 'C');
        $pdf->Cell(60, 10, $row_edu_qual['exam_year'], 1, 0, 'C');
        $pdf->Cell(60, 10, $row_edu_qual['exam_index_no'], 1, 1, 'C');
    } */
    $pdf->Ln(10);

    // Output English proficiency section
    $pdf->SetFont('times', 'B', 10);
    $pdf->Cell(0, 10, 'ENGLISH PROFICIENCY', 0, 1, 'L');
    $pdf->SetFont('times', '', 9);
    
   /*  while ($row_eng_prof = mysqli_fetch_array($res_eng_prof)) {
        $pdf->Cell(40, 10, 'Exam Name', 0, 0, 'L');
        $pdf->Cell(150, 10, $row_eng_prof['exam_name'], 0, 1, 'L');
        $pdf->Cell(40, 10, 'Year', 0, 0, 'L');
        $pdf->Cell(150, 10, $row_eng_prof['exam_year'], 0, 1, 'L');
        $pdf->Cell(40, 10, 'Result', 0, 0, 'L');
        $pdf->Cell(150, 10, $row_eng_prof['exam_result'], 0, 1, 'L');
    } */
    $pdf->Ln(10);

    // Output family details section
    $pdf->SetFont('times', 'B', 10);
    $pdf->Cell(0, 10, 'FAMILY DETAILS', 0, 1, 'L');
    $pdf->SetFont('times', '', 9);
    
    // Father's details
    /* if ($row_get_father) {
        $pdf->Cell(40, 10, 'Father\'s Name', 0, 0, 'L');
        $pdf->Cell(150, 10, strtoupper($row_get_father['guardian_name']), 0, 1, 'L');
        $pdf->Cell(40, 10, 'Father\'s Occupation', 0, 0, 'L');
        $pdf->Cell(150, 10, $row_get_father['occupation'], 0, 1, 'L');
        $pdf->Cell(40, 10, 'Father\'s Contact No', 0, 0, 'L');
        $pdf->Cell(150, 10, $row_get_father['contact_no'], 0, 1, 'L');
        $pdf->Cell(40, 10, 'Father\'s Email Address', 0, 0, 'L');
        $pdf->Cell(150, 10, $row_get_father['email'], 0, 1, 'L');
    } */
    $pdf->Ln(10);

    // Mother's details
   /*  if ($row_get_mother) {
        $pdf->Cell(40, 10, 'Mother\'s Name', 0, 0, 'L');
        $pdf->Cell(150, 10, strtoupper($row_get_mother['guardian_name']), 0, 1, 'L');
        $pdf->Cell(40, 10, 'Mother\'s Occupation', 0, 0, 'L');
        $pdf->Cell(150, 10, $row_get_mother['occupation'], 0, 1, 'L');
        $pdf->Cell(40, 10, 'Mother\'s Contact No', 0, 0, 'L');
        $pdf->Cell(150, 10, $row_get_mother['contact_no'], 0, 1, 'L');
        $pdf->Cell(40, 10, 'Mother\'s Email Address', 0, 0, 'L');
        $pdf->Cell(150, 10, $row_get_mother['email'], 0, 1, 'L');
    } */
    $pdf->Ln(10);

    // Guardian's details
   /*  if ($row_get_guardian) {
        $pdf->Cell(40, 10, 'Guardian\'s Name', 0, 0, 'L');
        $pdf->Cell(150, 10, strtoupper($row_get_guardian['guardian_name']), 0, 1, 'L');
        $pdf->Cell(40, 10, 'Guardian\'s Occupation', 0, 0, 'L');
        $pdf->Cell(150, 10, $row_get_guardian['occupation'], 0, 1, 'L');
        $pdf->Cell(40, 10, 'Guardian\'s Contact No', 0, 0, 'L');
        $pdf->Cell(150, 10, $row_get_guardian['contact_no'], 0, 1, 'L');
        $pdf->Cell(40, 10, 'Guardian\'s Email Address', 0, 0, 'L');
        $pdf->Cell(150, 10, $row_get_guardian['email'], 0, 1, 'L');
    }  */
// Output the PDF to browser (downloadable file)
$pdf->Output('example.pdf', 'D');
