<?php
require_once '../config/dbcon.php';
require_once '../config/iv_key.php';
require_once '../config/mystore_func.php'; //local
require_once('fpdf/fpdf.php');


$conn =  $con_fqsr;
$enc_nic_no = $_GET['nic'];


class PDF extends FPDF
{

	function Header()
	{
		// Use JPEG (no alpha channel) to avoid FPDF "Alpha channel not supported" error on PNGs
		$imgPath = '../assets/img/kdu/logo.jpg';
		$topY = 6; // mm from top

		// Determine a larger width based on usable page width
		$usableWidth = $this->w - $this->lMargin - $this->rMargin; // page width minus margins
		$scalePercent = 0.9; // 90% of usable width
		$minWidth = 35; // mm
		$maxWidth = 80; // mm (keep reasonable for A4)
		$imgWidth = max($minWidth, min($maxWidth, $usableWidth * $scalePercent));

		// Compute image height to position text below the image reliably
		$imgHeight = 0;
		if (file_exists($imgPath)) {
			$imgInfo = @getimagesize($imgPath);
			if ($imgInfo && isset($imgInfo[0]) && $imgInfo[0] > 0) {
				$imgHeight = ($imgInfo[1] / $imgInfo[0]) * $imgWidth; // maintain aspect ratio
			}
		}

		// Center the image horizontally within margins
		$imgX = $this->lMargin + ($usableWidth - $imgWidth) / 2;
		$this->Image($imgPath, $imgX, $topY, $imgWidth);

		// Move cursor below the image with a little padding
		$afterImageY = $topY + ($imgHeight > 0 ? $imgHeight : $imgWidth) + 6; // fallback to width if height unknown
		$this->SetY($afterImageY);

		$this->SetFont('Arial', 'B', 9);
		$this->Cell(0, 5, ' ' . '', 0, 1);
		$this->Cell(0, 5, 'APPLICATION FOR FOREIGN STUDENTS DEGREE PROGRAMS', 0, 1, 'C');

		//$this->Cell(50);
		//$this->Ln(20);
		//$this->Ln(5);

	}

	function AddBody($conn, $enc_nic_no)
	{


		$dec_nic_no = $enc_nic_no; //'5467546TEST';//$enc_nic_no ;
		$sql_get_personal = "SELECT * FROM mst_personal_details WHERE nic_no ='$dec_nic_no'";
		$res_get_personal = mysqli_query($conn, $sql_get_personal);
		$row_get_personal = mysqli_fetch_array($res_get_personal);

		$sql_edu_qual = "SELECT * FROM mst_educational_qualifications WHERE stu_nic = '$dec_nic_no' ORDER BY exam_name";
		$res_edu_qual = mysqli_query($conn, $sql_edu_qual);
		$edu_row_cnt = mysqli_num_rows($res_edu_qual);

		$sql_eng_prof = "SELECT * FROM mst_english_proficiency WHERE stu_passport_id = '$dec_nic_no' ";
		$res_eng_prof = mysqli_query($conn, $sql_eng_prof);
		$eng_row_cnt = mysqli_num_rows($res_eng_prof);

		$sql_family_father = "SELECT * FROM family_details WHERE stu_passport_id = '$dec_nic_no' AND relationship = 'FATHER'";
		$res_family_father = mysqli_query($conn, $sql_family_father);

		$row_get_father = mysqli_fetch_array($res_family_father);

		$sql_family_mother = "SELECT * FROM family_details WHERE stu_passport_id = '$dec_nic_no' AND relationship = 'MOTHER'";
		$res_family_mother = mysqli_query($conn, $sql_family_mother);
		$row_get_mother = mysqli_fetch_array($res_family_mother);

		$sql_family_guardian = "SELECT * FROM family_details WHERE stu_passport_id = '$dec_nic_no' AND relationship = 'GUARDIAN'";
		$res_family_guardian = mysqli_query($conn, $sql_family_guardian);
		$row_get_guardian = mysqli_fetch_array($res_family_guardian);

		$sql_refree = "SELECT * FROM refree WHERE stu_passport_id = '$dec_nic_no' ";
		$res_refree = mysqli_query($conn, $sql_refree);
		$refree_row_cnt = mysqli_num_rows($res_refree);

		// Fetch all applied degrees with preferences
		$applied_degrees = array();
		$sql_applied_degrees = "SELECT ad.preference_order, dc.degree_name FROM appliedDegrees ad JOIN mst_degree_courses dc ON ad.appliedDegreeCode = dc.degree_code WHERE ad.nic = '$dec_nic_no' ORDER BY ad.preference_order";
		$res_applied_degrees = mysqli_query($conn, $sql_applied_degrees);


		$stu_fullname = strtoupper($row_get_personal['stu_fullname']);
		$name_initials = strtoupper($row_get_personal['stu_title'] . ". " . $row_get_personal['stu_name_initials']);

		$stu_dob = $row_get_personal['stu_dob'];
		$stu_gender = $row_get_personal['stu_gender'];
		$stu_civil_status = $row_get_personal['civil_status'];
		$stu_permenant_address = strtoupper($row_get_personal['stu_permenant_address']);
		$stu_permenant_address =  trim($stu_permenant_address);
		$email_addr = $row_get_personal['stu_email'];
		$stu_nicno = $dec_nic_no;
		while ($row_degree = mysqli_fetch_assoc($res_applied_degrees)) {
			$applied_degrees[] = $row_degree;
		}
		$app_submit_dt = $row_get_personal['application_submit_dt'];
		//$applied_course = $row_get_personal['course_name'];
		$other_qualification = $row_get_personal['other_qualification'];
		$doc_upload_link = $row_get_personal['doc_upload_link'];
		$birth_country = $row_get_personal['birth_country'];
		$period_study_abroad = trim($row_get_personal['period_study_abroad']);
		$fund = $row_get_personal['fund'];
		$citizenship_type = $row_get_personal['citizenship_type'];
		$citizenship_1 = $row_get_personal['citizenship_1'];
		$citizenship_2 = $row_get_personal['citizenship_2'];
		$AL_sitting_country = $row_get_personal['AL_sitting_country'];
		$citizenship = $row_get_personal['stu_citizenship'];

		/* $stu_fullname = htmlentities($stu_fullname);
		$name_initials = htmlentities($name_initials);
		
		$stu_dob = htmlentities($stu_dob);
		$stu_gender = htmlentities($stu_gender);
		$stu_civil_status = htmlentities($stu_civil_status);
		$stu_permenant_address = htmlentities($stu_permenant_address);
		
		$email_addr = htmlentities($email_addr);
		$stu_nicno = htmlentities($stu_nicno);
		$applied_course = htmlentities($applied_course);
		$app_submit_dt = htmlentities($app_submit_dt);
		$other_qualification = htmlentities($other_qualification);
		$doc_upload_link = htmlentities($doc_upload_link);
		$birth_country = htmlentities($birth_country);
		$period_study_abroad = htmlentities($period_study_abroad);
		$fund = htmlentities($fund);  
		$citizenship_type = htmlentities($citizenship_type);
		$citizenship_1 = htmlentities($citizenship_1);
		$citizenship_2 = htmlentities($citizenship_2);
		$AL_sitting_country = htmlentities($AL_sitting_country);
		$citizenship = htmlentities($citizenship); */

		$this->Cell(0, 5, ' ' . '', 0, 1);
		$this->Cell(0, 5, 'General Sir John Kotelawala Defence University', 0, 1);
		$this->Cell(0, 5, 'Kandawala Road,', 0, 1);
		$this->Cell(0, 5, 'Rathmalana,', 0, 1);
		$this->Cell(0, 5, 'Sri Lanka.', 0, 1);
		$this->Cell(0, 5, 'Phone : +94-11-2634555', 0, 1);
		$this->Cell(0, 5, 'Email : admission@kdu.ac.lk', 0, 1);

		$width_cell = array(40, 50, 60, 40);

		$this->SetFont('Arial', 'B', 10);
		$this->Cell(0, 5, ' ' . '', 0, 1);
		$this->SetFillColor(193, 229, 252);
		$this->Cell(0, 10, ' PERSONAL DETAILS', 0, 1, 'L', true);
		$this->SetFont('Arial', '', 9);

		// Consistent label/value widths
		$labelW = 60;  // mm
		$valueW = 130; // mm (label + value = 190)

		$this->Cell($labelW, 12, 'Full Name', 0, 0, 'L', false);
		$this->Cell($valueW, 12, $stu_fullname, 0, 0, 'L', false);
		$this->Cell(0, 5, ' ' . '', 0, 1);

		$this->Cell($labelW, 10, 'Name with Initials', 0, 0, 'L', false);
		$this->Cell($valueW, 10, $name_initials, 0, 0, 'L', false);
		$this->Cell(0, 5, ' ' . '', 0, 1);

		$this->Cell($labelW, 10, 'Date of Birth', 0, 0, 'L', false);
		$this->Cell($valueW, 10, $stu_dob, 0, 0, 'L', false);
		$this->Cell(0, 5, ' ' . '', 0, 1);

		$this->Cell($labelW, 10, 'Gender', 0, 0, 'L', false);
		$this->Cell($valueW, 10, $stu_gender, 0, 0, 'L', false);
		$this->Cell(0, 5, ' ' . '', 0, 1);

		$this->Cell($labelW, 10, 'Civil Status', 0, 0, 'L', false);
		$this->Cell($valueW, 10, $stu_civil_status, 0, 0, 'L', false);
		$this->Cell(0, 5, ' ' . '', 0, 1);

		if (trim($stu_permenant_address) !== '') {
			$this->Cell($labelW, 10, 'Permanent Address', 0, 0, 'L', false);
			$this->MultiCell($valueW, 10, $stu_permenant_address, 0, 'L', false);
		}

		$this->Cell($labelW, 10, 'Email Address', 0, 0, 'L', false);
		$this->Cell($valueW, 10, $email_addr, 0, 0, 'L', false);
		$this->Cell(0, 5, ' ' . '', 0, 1);

		$this->Cell($labelW, 10, 'NIC No', 0, 0, 'L', false);
		$this->Cell($valueW, 10, $stu_nicno, 0, 0, 'L', false);
		$this->Cell(0, 5, ' ' . '', 0, 1);

		$this->Cell($labelW, 10, 'Citizenship', 0, 0, 'L', false);
		$this->Cell($valueW, 10, $citizenship_type, 0, 0, 'L', false);
		$this->Cell(0, 5, ' ' . '', 0, 1);

		if ($citizenship_type == 'Foreign Citizenship') {

			$this->Cell($labelW, 10, 'Country of Citizenship', 0, 0, 'L', false);
			$this->Cell($valueW, 10, $citizenship, 0, 0, 'L', false);
			$this->Cell(0, 5, ' ' . '', 0, 1);
		}
		if ($citizenship_type == 'Dual Citizenship') {

			$this->Cell($labelW, 10, '1st Country of Citizenship', 0, 0, 'L', false);
			$this->Cell($valueW, 10, $citizenship_1, 0, 0, 'L', false);
			$this->Cell(0, 5, ' ' . '', 0, 1);

			$this->Cell($labelW, 10, '2nd Country of Citizenship', 0, 0, 'L', false);
			$this->Cell($valueW, 10, $citizenship_2, 0, 0, 'L', false);
			$this->Cell(0, 5, ' ' . '', 0, 1);
		}


		$this->Cell($labelW, 10, 'Country of Birth', 0, 0, 'L', false);
		$this->Cell($valueW, 10, $birth_country, 0, 0, 'L', false);
		$this->Cell(0, 5, ' ' . '', 0, 1);



		if (trim($fund) !== '') {
			$this->Cell($labelW, 10, 'Funds', 0, 0, 'L', false);
			$this->MultiCell($valueW, 10, $fund, 0, 'L', false);
		}

		if (trim($AL_sitting_country) !== '') {
			$this->Cell($labelW, 10, 'Country appeared for A/L (High School Diploma)', 0, 0, 'L', false);
			$this->MultiCell($valueW, 10, $AL_sitting_country, 0, 'L', false);
		}

		$this->Cell($labelW, 10, 'Period of Study Apart from Sri Lanka', 0, 0, 'L', false);
		$this->Cell($valueW, 10, $period_study_abroad, 0, 0, 'L', false);
		$this->Cell(0, 5, ' ' . '', 0, 1);

		$this->Cell($labelW, 10, 'Application Submit Date', 0, 0, 'L', false);
		$this->Cell($valueW, 10, $app_submit_dt, 0, 0, 'L', false);
		$this->Cell(0, 5, ' ' . '', 0, 1);

		// Applied Degrees as a table
		$this->Cell(0, 5, ' ', 0, 1);
		$this->SetFont('Arial', 'B', 10);
		$this->SetFillColor(193, 229, 252);
		$this->Cell(0, 10, '  APPLIED DEGREES', 0, 1, 'L', true);
		$this->Cell(0, 5, ' ', 0, 1);

		$colPref = 30;
		$colDeg = 160; // total 190 to fit within default margins
		if (count($applied_degrees) > 0) {
			// Header
			$this->SetFont('Arial', 'B', 9);
			$this->SetDrawColor(180, 180, 180);
			$this->SetLineWidth(0.3);
			$this->SetFillColor(230, 240, 255);
			$this->Cell($colPref, 8, 'Preference', 1, 0, 'C', true);
			$this->Cell($colDeg, 8, 'Degree Name', 1, 1, 'C', true);

			// Rows
			$this->SetFont('Arial', '', 9);
			$this->SetFillColor(248, 248, 248);
			$fill = false;
			foreach ($applied_degrees as $degree) {
				$this->Cell($colPref, 7, (string)$degree['preference_order'], 1, 0, 'C', $fill);
				$this->Cell($colDeg, 7, $degree['degree_name'], 1, 1, 'L', $fill);
				$fill = !$fill;
			}
		} else {
			$this->SetFont('Arial', '', 9);
			$this->Cell($colPref + $colDeg, 8, 'No degrees selected', 1, 1, 'C');
		}


		$this->Cell(0, 5, ' ', 0, 1);
		$this->SetFont('Arial', 'B', 10);
		$this->SetFillColor(193, 229, 252);
		$this->Cell(0, 10, '  EDUCATIONAL QUALIFICATIONS', 0, 1, 'L', true);
		$this->Cell(0, 5, ' ', 0, 1);
		// Table styling
		$this->SetDrawColor(180, 180, 180);
		$this->SetLineWidth(0.3);
		$this->SetFont('Arial', 'B', 9);

		// Define table column widths (sum ~190mm for A4 with default margins)
		$colYear = 25;  // Year
		$colExam = 75;  // Exam Name
		$colSubject = 70; // Subject(s)
		$colGrade = 20; // Grade

		// Header row
		$this->SetFillColor(230, 240, 255);
		$this->Cell($colYear, 8, 'Year', 1, 0, 'C', true);
		$this->Cell($colExam, 8, 'Exam Name', 1, 0, 'C', true);
		$this->Cell($colSubject, 8, 'Subject(s)', 1, 0, 'C', true);
		$this->Cell($colGrade, 8, 'Grade', 1, 1, 'C', true);

		$this->SetFont('Arial', '', 9);
		$this->SetFillColor(248, 248, 248);
		$fill = false; // zebra rows

		if ($edu_row_cnt > 0) {
			while ($row_edu_qual = mysqli_fetch_array($res_edu_qual)) {
				$year = isset($row_edu_qual['exam_year']) ? $row_edu_qual['exam_year'] : '';
				$exam = isset($row_edu_qual['exam_name']) ? $row_edu_qual['exam_name'] : '';
				$subject = isset($row_edu_qual['subject_grade']) ? $row_edu_qual['subject_grade'] : '';
				$grade = isset($row_edu_qual['award']) ? $row_edu_qual['award'] : '';

				$this->Cell($colYear, 7, $year, 1, 0, 'C', $fill);
				$this->Cell($colExam, 7, $exam, 1, 0, 'L', $fill);
				$this->Cell($colSubject, 7, $subject, 1, 0, 'L', $fill);
				$this->Cell($colGrade, 7, $grade, 1, 1, 'C', $fill);

				$fill = !$fill;
			}
		} else {
			$this->Cell($colYear + $colExam + $colSubject + $colGrade, 8, 'No educational qualifications provided', 1, 1, 'C');
		}

		$this->Cell(0, 5, ' ', 0, 1);
		$this->SetFont('Arial', 'B', 10);
		$this->SetFillColor(193, 229, 252);
		$this->Cell(0, 10, '  ENGLISH PROFICIENCY', 0, 1, 'L', true);
		$this->SetFont('Arial', 'B', 9);
		$this->Cell(0, 5, ' ', 0, 1);

		// English Proficiency as a table
		$colQual = 90; // Qualification Type
		$colRes = 60;  // Result
		$colYear = 40; // Year

		if ($eng_row_cnt > 0) {
			// Header
			$this->SetDrawColor(180, 180, 180);
			$this->SetLineWidth(0.3);
			$this->SetFillColor(230, 240, 255);
			$this->Cell($colQual, 8, 'Qualification Type', 1, 0, 'C', true);
			$this->Cell($colRes, 8, 'Result', 1, 0, 'C', true);
			$this->Cell($colYear, 8, 'Year', 1, 1, 'C', true);

			// Rows
			$this->SetFont('Arial', '', 9);
			$this->SetFillColor(248, 248, 248);
			$fill = false;
			while ($row_eng_prof = mysqli_fetch_array($res_eng_prof)) {
				$this->Cell($colQual, 7, $row_eng_prof['qualification_type'], 1, 0, 'L', $fill);
				$this->Cell($colRes, 7, $row_eng_prof['result'], 1, 0, 'L', $fill);
				$this->Cell($colYear, 7, $row_eng_prof['year'], 1, 1, 'C', $fill);
				$fill = !$fill;
			}
		} else {
			$this->SetFont('Arial', '', 9);
			$this->Cell($colQual + $colRes + $colYear, 8, 'No English qualifications provided', 1, 1, 'C');
		}

		$this->SetFont('Arial', 'B', 10);
		$this->Cell(0, 5, ' ' . '', 0, 1);
		$this->SetFillColor(193, 229, 252);
		$this->Cell(0, 10, ' OTHER QUALIFICATIONS', 0, 1, 'L', true);
		$this->SetFont('Arial', '', 9);

		$this->Cell(40, 12, $other_qualification, 0, 0, 'L', false);
		$this->Cell(0, 5, ' ' . '', 0, 1);

		$this->SetFont('Arial', 'B', 10);
		$this->Cell(0, 5, ' ' . '', 0, 1);
		$this->SetFillColor(193, 229, 252);
		$this->Cell(0, 10, ' FATHER DETAILS', 0, 1, 'L', true);
		$this->SetFont('Arial', '', 9);

		// Conditionally render Father details only when values are present
		$fatherFields = array(
			'Name' => isset($row_get_father['name']) ? trim($row_get_father['name']) : '',
			'Occupation' => isset($row_get_father['job']) ? trim($row_get_father['job']) : '',
			'Employer Address' => isset($row_get_father['employey_details']) ? trim($row_get_father['employey_details']) : '',
			'Email' => isset($row_get_father['email']) ? trim($row_get_father['email']) : '',
			'Fixed Phone' => isset($row_get_father['fixed_phone']) ? trim($row_get_father['fixed_phone']) : '',
			'Mobile' => isset($row_get_father['mobile_no']) ? trim($row_get_father['mobile_no']) : ''
		);
		foreach ($fatherFields as $label => $value) {
			if ($value !== '') {
				$this->Cell(40, 12, $label, 0, 0, 'L', false);
				$this->Cell(40, 12, $value, 0, 0, 'L', false);
				$this->Cell(0, 5, ' ' . '', 0, 1);
			}
		}

		$this->SetFont('Arial', 'B', 10);
		$this->Cell(0, 5, ' ' . '', 0, 1);
		$this->SetFillColor(193, 229, 252);
		$this->Cell(0, 10, ' MOTHER DETAILS', 0, 1, 'L', true);
		$this->SetFont('Arial', '', 9);

		// Conditionally render Mother details only when values are present
		$motherFields = array(
			'Name' => isset($row_get_mother['name']) ? trim($row_get_mother['name']) : '',
			'Occupation' => isset($row_get_mother['job']) ? trim($row_get_mother['job']) : '',
			'Employer Address' => isset($row_get_mother['employey_details']) ? trim($row_get_mother['employey_details']) : '',
			'Email' => isset($row_get_mother['email']) ? trim($row_get_mother['email']) : '',
			'Fixed Phone' => isset($row_get_mother['fixed_phone']) ? trim($row_get_mother['fixed_phone']) : '',
			'Mobile' => isset($row_get_mother['mobile_no']) ? trim($row_get_mother['mobile_no']) : ''
		);
		foreach ($motherFields as $label => $value) {
			if ($value !== '') {
				$this->Cell(40, 12, $label, 0, 0, 'L', false);
				$this->Cell(40, 12, $value, 0, 0, 'L', false);
				$this->Cell(0, 5, ' ' . '', 0, 1);
			}
		}

		$this->SetFont('Arial', 'B', 10);
		$this->Cell(0, 5, ' ' . '', 0, 1);
		$this->SetFillColor(193, 229, 252);
		$this->Cell(0, 10, ' GUARDIAN DETAILS', 0, 1, 'L', true);
		$this->SetFont('Arial', '', 9);
		// Conditionally render Guardian details only when values are present
		$guardianFields = array(
			'Name' => isset($row_get_guardian['name']) ? trim($row_get_guardian['name']) : '',
			'Occupation' => isset($row_get_guardian['job']) ? trim($row_get_guardian['job']) : '',
			'Employer Address' => isset($row_get_guardian['employey_details']) ? trim($row_get_guardian['employey_details']) : '',
			'Email' => isset($row_get_guardian['email']) ? trim($row_get_guardian['email']) : '',
			'Fixed Phone' => isset($row_get_guardian['fixed_phone']) ? trim($row_get_guardian['fixed_phone']) : '',
			'Mobile' => isset($row_get_guardian['mobile_no']) ? trim($row_get_guardian['mobile_no']) : ''
		);
		foreach ($guardianFields as $label => $value) {
			if ($value !== '') {
				$this->Cell(40, 12, $label, 0, 0, 'L', false);
				$this->Cell(40, 12, $value, 0, 0, 'L', false);
				$this->Cell(0, 5, ' ' . '', 0, 1);
			}
		}


		$this->Cell(0, 5, ' ', 0, 1);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(0, 10, '  REFEREE DETAILS', 0, 1, 'L', true);
		$this->SetFont('Arial', 'B', 9);



		if ($refree_row_cnt > 0) {

			$this->SetFont('Arial', '', 9);
			while ($row_get_refree = mysqli_fetch_array($res_refree)) {

				$this->Cell($width_cell[0], 10, 'Referee Name', 0, 0, 'L', false);
				$this->Cell($width_cell[0], 10, $row_get_refree['refree_details'], 0, 0, 'L', false);
				$this->Cell(0, 5, ' ' . '', 0, 1);

				$this->Cell($width_cell[0], 10, 'Contact No', 0, 0, 'L', false);
				$this->Cell($width_cell[0], 10, $row_get_refree['contact_no'], 0, 0, 'L', false);
				$this->Cell(0, 5, ' ' . '', 0, 1);

				$this->Cell($width_cell[0], 10, 'Type', 0, 0, 'L', false);
				$this->Cell($width_cell[0], 10, $row_get_refree['type'], 0, 0, 'L', false);
				$this->Cell(0, 5, ' ' . '', 0, 1);

				$this->Cell(0, 5, ' ' . '', 0, 1);
				$this->Cell(0, 5, ' ' . '', 0, 1);
			}
		}
	}


	function Footer()
	{

		$this->SetY(-15);
		$this->SetFont('Arial', 'I', 8);
		$this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
	}
}


$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->AddBody($conn, $enc_nic_no);
//$this->SetFont('Arial','B',9);



$pdf->Output();
