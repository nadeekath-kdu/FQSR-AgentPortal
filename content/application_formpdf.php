<?php
require_once '../../config/dbcon.php';
require_once '../../config/iv_key.php';
require_once '../../config/mystore_func.php'; //local
require_once ('fpdf/fpdf.php');


$conn =  $con; 
$enc_nic_no = $_GET['nic'];


class PDF extends FPDF
{

	function Header()
	{
	    //$this->Image('https://enlistment.kdu.ac.lk/agent_portal/assets/img/kdu/Kotelawala_Defence_University_crest.png',10,6,30);
	    $this->SetFont('Arial','B',9);
		$this->Cell(0,5,' '.'',0,1);
		$this->Cell(0,5,'GENERAL SIR JOHN KOTELAWALA DEFENCE UNIVERSITY APPLICATION FOR FOREIGN STUDENTS DEGREE PROGRAMS',0,1);
		
	    //$this->Cell(50);
	    //$this->Ln(20);
		//$this->Ln(5);
		
	}

	function AddBody($conn,$enc_nic_no)
	{
		

		$dec_nic_no = $enc_nic_no ;//'5467546TEST';//$enc_nic_no ;
		$sql_get_personal = "SELECT * FROM mst_personal_details WHERE nic_no ='$dec_nic_no'";
		$res_get_personal = mysqli_query($conn,$sql_get_personal);
		$row_get_personal = mysqli_fetch_array($res_get_personal);
		
		$sql_edu_qual = "SELECT * FROM mst_educational_qualifications WHERE stu_nic = '$dec_nic_no' ORDER BY exam_name";
		$res_edu_qual = mysqli_query($conn,$sql_edu_qual);
		$edu_row_cnt = mysqli_num_rows($res_edu_qual);
		
		$sql_eng_prof = "SELECT * FROM mst_english_proficiency WHERE stu_passport_id = '$dec_nic_no' ";
		$res_eng_prof = mysqli_query($conn,$sql_eng_prof);
		$eng_row_cnt = mysqli_num_rows($res_eng_prof);
		
		$sql_family_father = "SELECT * FROM family_details WHERE stu_passport_id = '$dec_nic_no' AND relationship = 'FATHER'";
		$res_family_father = mysqli_query($conn,$sql_family_father);
		
		$row_get_father = mysqli_fetch_array($res_family_father);
		
		$sql_family_mother = "SELECT * FROM family_details WHERE stu_passport_id = '$dec_nic_no' AND relationship = 'MOTHER'";
		$res_family_mother = mysqli_query($conn,$sql_family_mother);
		$row_get_mother = mysqli_fetch_array($res_family_mother);
		
		$sql_family_guardian = "SELECT * FROM family_details WHERE stu_passport_id = '$dec_nic_no' AND relationship = 'GUARDIAN'";
		$res_family_guardian = mysqli_query($conn,$sql_family_guardian);
		$row_get_guardian = mysqli_fetch_array($res_family_guardian);
		
		$sql_refree = "SELECT * FROM refree WHERE stu_passport_id = '$dec_nic_no' ";
		$res_refree = mysqli_query($conn,$sql_refree);
		$refree_row_cnt = mysqli_num_rows($res_refree);
		
		
		$stu_fullname = strtoupper($row_get_personal['stu_fullname']);
		$name_initials = strtoupper($row_get_personal['stu_title'].". ".$row_get_personal['stu_name_initials']);
		
		$stu_dob = $row_get_personal['stu_dob'];
		$stu_gender = $row_get_personal['stu_gender'];
		$stu_civil_status = $row_get_personal['civil_status'];
		$stu_permenant_address = strtoupper($row_get_personal['stu_permenant_address']);
		$stu_permenant_address =  trim($stu_permenant_address);
		$email_addr = $row_get_personal['stu_email'];
		$stu_nicno = $dec_nic_no;
		$applied_course = $row_get_personal['course_name'];
		$app_submit_dt = $row_get_personal['application_submit_dt'];
		$applied_course = $row_get_personal['course_name'];
		$other_qualification = $row_get_personal['other_qualification'];
		$doc_upload_link = $row_get_personal['doc_upload_link'];
		$birth_country = $row_get_personal['birth_country'];
		$period_study_abroad = $row_get_personal['period_study_abroad'];
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
		
		$this->Cell(0,5,' '.'',0,1);
		$this->Cell(0,5,'General Sir John Kotelawala Defence University',0,1);
		$this->Cell(0,5,'Kandawala Road,',0,1);
		$this->Cell(0,5,'Rathmalana,',0,1);
		$this->Cell(0,5,'Sri Lanka.',0,1);
		$this->Cell(0,5,'Phone : +94-11-2634555',0,1);
		$this->Cell(0,5,'Email : admission@kdu.ac.lk',0,1);

	$width_cell=array(40,50,60,40);

	$this->SetFont('Arial','B',10);
	$this->Cell(0,5,' '.'',0,1);
	$this->SetFillColor(193,229,252); 
	$this->Cell(0,10,' PERSONAL DETAILS',0,1,'L',true);
	$this->SetFont('Arial','',9);
	
	$this->Cell(40,12,'Applied course',0,0,'L',false); 
	$this->Cell(150,12,$applied_course,0,0,'L',false); 
	$this->Cell(0,5,' '.'',0,1);

	$this->Cell(40,12,'Application submit date',0,0,'L',false); 
	$this->Cell(150,12,$app_submit_dt,0,0,'L',false); 
	$this->Cell(0,5,' '.'',0,1);

	$this->Cell(40,12,'Full Name',0,0,'L',false); 
	$this->Cell(150,12,$stu_fullname,0,0,'L',false); 
	$this->Cell(0,5,' '.'',0,1);

	
	$this->Cell(40,10,'Name with initials',0,0,'L',false); 
	$this->Cell(150,10,$name_initials,0,0,'L',false);
	$this->Cell(0,5,' '.'',0,1);	

	
	$this->Cell(40,10,'Date of birth',0,0,'L',false); 
	$this->Cell(150,10,$stu_dob,0,0,'L',false);
	$this->Cell(0,5,' '.'',0,1);

	$this->Cell(40,10,'Gender',0,0,'L',false); 
	$this->Cell(150,10,$stu_gender,0,0,'L',false);
	$this->Cell(0,5,' '.'',0,1);

	$this->Cell(40,10,'Civil status',0,0,'L',false); 
	$this->Cell(150,10,$stu_civil_status,0,0,'L',false);
	$this->Cell(0,5,' '.'',0,1);

	
	$this->Cell(40,10,'Permenant Address',0,0,'L',false); 
	
	$this->MultiCell(150,10,$stu_permenant_address,'0','L',false); 
	$this->Cell(0,5,' '.'',0,1);
	
	$this->Cell(40,10,'Email Address',0,0,'L',false); 
	$this->Cell(150,10,$email_addr,0,0,'L',false);
	$this->Cell(0,5,' '.'',0,1);
	
	$this->Cell(40,10,'NIC No',0,0,'L',false); 
	$this->Cell(150,10,$stu_nicno,0,0,'L',false);
	$this->Cell(0,5,' '.'',0,1);	
	
	$this->Cell(40,10,'Citizenship',0,0,'L',false); 
	$this->Cell(150,10,$citizenship_type,0,0,'L',false);
	$this->Cell(0,5,' '.'',0,1);

	if($citizenship_type == 'Foreign Citizenship'){

		$this->Cell(40,10,'Contry of Citizenship',0,0,'L',false); 
		$this->Cell(150,10,$citizenship,0,0,'L',false);
		$this->Cell(0,5,' '.'',0,1);

	}if($citizenship_type == 'Dual Citizenship'){

		$this->Cell(40,10,'1st Contry of Citizenship',0,0,'L',false); 
		$this->Cell(150,10,$citizenship_1,0,0,'L',false);
		$this->Cell(0,5,' '.'',0,1);

		$this->Cell(40,10,'2st Contry of Citizenship',0,0,'L',false); 
		$this->Cell(150,10,$citizenship_2,0,0,'L',false);
		$this->Cell(0,5,' '.'',0,1);	
	}


	$this->Cell(40,10,'Country of Birth',0,0,'L',false); 
	$this->Cell(150,10,$birth_country,0,0,'L',false);
	$this->Cell(0,5,' '.'',0,1);	
	
	

	$this->Cell(40,10,'Funds',0,0,'L',false); 
	
	$this->MultiCell(150,10,$fund,'0','L',false); 
	$this->Cell(0,5,' '.'',0,1);	

	$this->Cell(80,10,'Country appeared for A/L(High School Diploma)',0,0,'L',false); 
	
	$this->MultiCell(150,10,$AL_sitting_country,'0','L',false); 
	$this->Cell(0,5,' '.'',0,1);	

	$this->Cell(80,10,'Period of study apart from Sri Lanka',0,0,'L',false); 
	$this->Cell(150,10,$period_study_abroad,0,0,'L',false);
	$this->Cell(0,5,' '.'',0,1);	

	$this->Cell(80,10,'Downloadable Link of uploaded documents',0,0,'L',false); 
	
	$this->MultiCell(150,10,$doc_upload_link,'0','L',false); 
	$this->Cell(0,5,' '.'',0,1);	

	$this->Cell(0,5,' ',0,1);
	$this->SetFont('Arial','B',10);
	$this->Cell(0,10,'  EDUCATIONAL QUALIFICATIONS',0,1,'L',true);
	$this->SetFont('Arial','B',9);
	
	

	if($edu_row_cnt > 0){
		
		$this->SetFont('Arial','',9);
		while($row_edu_qual = mysqli_fetch_array($res_edu_qual)){
			
			$this->Cell($width_cell[0],10,'Year of exam',0,0,'L',false);
			$this->Cell($width_cell[0],10,$row_edu_qual['exam_year'],0,0,'L',false);
			$this->Cell(0,5,' '.'',0,1);

			$this->Cell($width_cell[0],10,'Name of the Exam',0,0,'L',false);
			$this->Cell($width_cell[0],10,$row_edu_qual['exam_name'],0,0,'L',false);
			$this->Cell(0,5,' '.'',0,1);

			$this->Cell($width_cell[0],10,'Subject & Grade',0,0,'L',false);
			$this->Cell($width_cell[0],10,$row_edu_qual['subject_grade'],0,0,'L',false);
			$this->Cell(0,5,' '.'',0,1);

			$this->Cell($width_cell[0],10,'Award',0,0,'L',false);
			$this->Cell($width_cell[0],10,$row_edu_qual['award'],0,0,'L',false);
			$this->Cell(0,5,' '.'',0,1);

			$this->Cell(0,5,' '.'',0,1);
			$this->Cell(0,5,' '.'',0,1);

		}
	}

	$this->Cell(0,5,' ',0,1);
	$this->SetFont('Arial','B',10);
	$this->Cell(0,10,'  ENGLISH PROFICIENCY',0,1,'L',true);
	$this->SetFont('Arial','B',9);
	
	if($edu_row_cnt > 0){
		$this->Cell(0,5,' '.'',0,1);
		$this->Cell($width_cell[0],10,'Qualification Type',0,0,'L',false);
		$this->Cell($width_cell[1],10,'Result',0,0,'L',false);
		$this->Cell($width_cell[2],10,'Year',0,0,'L',false);
		
		$this->Cell(0,10,' '.'',0,1);
		
		$this->SetFont('Arial','',9);
		
		while($row_eng_prof = mysqli_fetch_array($res_eng_prof)){
			$this->Cell(0,5,' '.'',0,1);
			$this->Cell($width_cell[0],10,$row_eng_prof['qualification_type'],0,0,'L',false);
			$this->Cell($width_cell[1],10,$row_eng_prof['result'],0,0,'L',false);
			$this->Cell($width_cell[2],10,$row_eng_prof['year'],0,0,'L',false);
			
			$this->Cell(0,10,' '.'',0,1);
			
		}
	}else{
		$this->Cell(0,5,' ',0,1);
		$this->SetFont('Arial','B',10);
		$this->Cell(0,10,'-',0,1,'L',true);
		$this->SetFont('Arial','B',9);
	}
	
	$this->SetFont('Arial','B',10);
	$this->Cell(0,5,' '.'',0,1);
	$this->SetFillColor(193,229,252); 
	$this->Cell(0,10,' OTHER QUALIFICATION',0,1,'L',true);
	$this->SetFont('Arial','',9);
	
	$this->Cell(40,12,$other_qualification,0,0,'L',false); 
	$this->Cell(0,5,' '.'',0,1);

	$this->SetFont('Arial','B',10);
	$this->Cell(0,5,' '.'',0,1);
	$this->SetFillColor(193,229,252); 
	$this->Cell(0,10,' FATHER DETAILS',0,1,'L',true);
	$this->SetFont('Arial','',9);
	
	$this->Cell(40,12,'Name',0,0,'L',false); 
	$this->Cell(40,12,$row_get_father['name'],0,0,'L',false); 
	$this->Cell(0,5,' '.'',0,1);
	$this->Cell(40,12,'Occupation',0,0,'L',false); 
	$this->Cell(40,12,$row_get_father['job'],0,0,'L',false); 
	$this->Cell(0,5,' '.'',0,1);
	$this->Cell(40,12,'Employer Address',0,0,'L',false); 
	$this->Cell(40,12,$row_get_father['employey_details'],0,0,'L',false); 
	$this->Cell(0,5,' '.'',0,1);
	$this->Cell(40,12,'Email',0,0,'L',false); 
	$this->Cell(40,12,$row_get_father['email'],0,0,'L',false); 
	$this->Cell(0,5,' '.'',0,1);
	$this->Cell(40,12,'Fixed Phone',0,0,'L',false); 
	$this->Cell(40,12,$row_get_father['fixed_phone'],0,0,'L',false); 
	$this->Cell(0,5,' '.'',0,1);
	$this->Cell(40,12,'Mobile',0,0,'L',false); 
	$this->Cell(40,12,$row_get_father['mobile_no'],0,0,'L',false); 
	$this->Cell(0,5,' '.'',0,1);

	$this->SetFont('Arial','B',10);
	$this->Cell(0,5,' '.'',0,1);
	$this->SetFillColor(193,229,252); 
	$this->Cell(0,10,' MOTHER DETAILS',0,1,'L',true);
	$this->SetFont('Arial','',9);
	
	$this->Cell(40,12,'Name',0,0,'L',false);  
	$this->Cell(40,12,$row_get_mother['name'],0,0,'L',false); 
	$this->Cell(0,5,' '.'',0,1);
	$this->Cell(40,12,'Occupation',0,0,'L',false); 
	$this->Cell(40,12,$row_get_mother['job'],0,0,'L',false);
	$this->Cell(0,5,' '.'',0,1);
	$this->Cell(40,12,'Employer Address',0,0,'L',false);  
	$this->Cell(40,12,$row_get_mother['employey_details'],0,0,'L',false); 
	$this->Cell(0,5,' '.'',0,1);
	$this->Cell(40,12,'Email',0,0,'L',false);
	$this->Cell(40,12,$row_get_mother['email'],0,0,'L',false);
	$this->Cell(0,5,' '.'',0,1);
	$this->Cell(40,12,'Fixed Phone',0,0,'L',false);
	$this->Cell(40,12,$row_get_mother['fixed_phone'],0,0,'L',false); 
	$this->Cell(0,5,' '.'',0,1);
	$this->Cell(40,12,'Mobile',0,0,'L',false);
	$this->Cell(40,12,$row_get_mother['mobile_no'],0,0,'L',false); 
	$this->Cell(0,5,' '.'',0,1);

	$this->SetFont('Arial','B',10);
	$this->Cell(0,5,' '.'',0,1);
	$this->SetFillColor(193,229,252); 
	$this->Cell(0,10,' GUARDIAN DETAILS',0,1,'L',true);
	$this->SetFont('Arial','',9);
	// First row of data 
	$this->Cell(40,12,'Name',0,0,'L',false);  
	$this->Cell(40,12,$row_get_guardian['name'],0,0,'L',false); 
	$this->Cell(0,5,' '.'',0,1);
	$this->Cell(40,12,'Occupation',0,0,'L',false); 
	$this->Cell(40,12,$row_get_guardian['job'],0,0,'L',false);
	$this->Cell(0,5,' '.'',0,1);
	$this->Cell(40,12,'Employer Address',0,0,'L',false);  
	$this->Cell(40,12,$row_get_guardian['employey_details'],0,0,'L',false); 
	$this->Cell(0,5,' '.'',0,1);
	$this->Cell(40,12,'Email',0,0,'L',false);
	$this->Cell(40,12,$row_get_guardian['email'],0,0,'L',false);
	$this->Cell(0,5,' '.'',0,1);
	$this->Cell(40,12,'Fixed Phone',0,0,'L',false);
	$this->Cell(40,12,$row_get_guardian['fixed_phone'],0,0,'L',false); 
	$this->Cell(0,5,' '.'',0,1);
	$this->Cell(40,12,'Mobile',0,0,'L',false);
	$this->Cell(40,12,$row_get_guardian['mobile_no'],0,0,'L',false); 
	$this->Cell(0,5,' '.'',0,1);


	$this->Cell(0,5,' ',0,1);
	$this->SetFont('Arial','B',10);
	$this->Cell(0,10,'  REFREE DETAILS',0,1,'L',true);
	$this->SetFont('Arial','B',9);

	

	if($refree_row_cnt > 0){
		
		$this->SetFont('Arial','',9);
		while($row_get_refree = mysqli_fetch_array($res_refree)){
			
			$this->Cell($width_cell[0],10,'Refree Name',0,0,'L',false);
			$this->Cell($width_cell[0],10,$row_get_refree['refree_details'],0,0,'L',false);
			$this->Cell(0,5,' '.'',0,1);

			$this->Cell($width_cell[0],10,'Contact No',0,0,'L',false);
			$this->Cell($width_cell[0],10,$row_get_refree['contact_no'],0,0,'L',false);
			$this->Cell(0,5,' '.'',0,1);

			$this->Cell($width_cell[0],10,'Type',0,0,'L',false);
			$this->Cell($width_cell[0],10,$row_get_refree['type'],0,0,'L',false);
			$this->Cell(0,5,' '.'',0,1);

			$this->Cell(0,5,' '.'',0,1);
			$this->Cell(0,5,' '.'',0,1);

		}
	}
	}

	
	function Footer()
	{
	   
	    $this->SetY(-15);
	    $this->SetFont('Arial','I',8);
	    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}
}


$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->AddBody($conn,$enc_nic_no);
//$this->SetFont('Arial','B',9);


	
$pdf->Output();
?>