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

function validateFormData($data)
{
    $errors = array();

    // Required fields validation
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
$dec_nic_no = $enc_nic_no;

if (empty($dec_nic_no)) {
    echo json_encode(array(
        'status' => 'error',
        'message' => 'NIC/Passport number is required'
    ));
    exit;
}

// Validate form data
$errors = validateFormData($_POST);
if (!empty($errors)) {
    echo json_encode(array(
        'status' => 'error',
        'message' => implode("\n", $errors)
    ));
    exit;
}

// Initialize variables
$last_id = 0;
$err_code = 0;
$response = array();

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

    // Process photo upload
    $Photo = $oldphoto;
    if (isset($_FILES["Photo"]) && !empty($_FILES["Photo"]["name"]) && $_FILES["Photo"]["error"] == 0) {
        $uploaddir = "../profile/";
        $extension = pathinfo($_FILES["Photo"]["name"], PATHINFO_EXTENSION);
        $uploadfile = $uploaddir . $dec_nic_no . '.' . $extension;

        if (move_uploaded_file($_FILES["Photo"]["tmp_name"], $uploadfile)) {
            $Photo = $dec_nic_no . '.' . $extension;
            error_log("File uploaded successfully");
        } else {
            error_log("Photo Upload failed");
            $err_code = 3;
        }
    }

    // Update personal details
    $sql_personal_data = "UPDATE mst_personal_details SET 
        course_name = ?,
        course_code = ?,
        intake = ?,
        stu_title = ?,
        stu_fullname = ?,
        stu_name_initials = ?,
        stu_dob = ?,
        stu_gender = ?,
        stu_citizenship = ?,
        civil_status = ?,
        stu_permenant_address = ?,
        stu_email = ?,
        application_submit_dt = NOW(),
        media_source_name = ?,
        doc_upload_link = ?,
        birth_country = ?,
        period_study_abroad = ?,
        eligibility_uni_admision = ?,
        other_qualification = ?,
        fund = ?,
        citizenship_type = ?,
        citizenship_1 = ?,
        citizenship_2 = ?,
        AL_sitting_country = ?,
        nameEduAgent = ?,
        isEduAgent = ?,
        photo = ?
        WHERE nic_no = ?";

    $stmt = $conn->prepare($sql_personal_data);
    $stmt->bind_param(
        "sssssssssssssssssssssssssss",
        $_POST['inputCourse'],
        $_POST['inputCourse'],
        $_POST['inputIntakeYr'],
        $_POST['inputTitle'],
        $_POST['inputFullname'],
        $_POST['inputNameInitials'],
        $_POST['inputDob'],
        $_POST['inputGender'],
        $_POST['inputCitizenship'],
        $_POST['inputCivilSts'],
        $_POST['addressPermanent'],
        $_POST['inputEmailAddress'],
        $media_source_name,
        $_POST['docupldlink'],
        $_POST['inputCountryBirth'],
        $_POST['periodStudy'],
        $_POST['elegibleState'],
        $_POST['otherQualifications'],
        $_POST['fund'],
        $_POST['citizenship_type'],
        $_POST['inputCitizenship1'],
        $_POST['inputCitizenship2'],
        $_POST['inputCountryAL'],
        $_POST['nameEduAgent'],
        $_POST['eduAgent'],
        $Photo,
        $dec_nic_no
    );

    if (!$stmt->execute()) {
        error_log("Failed to update personal details: " . $stmt->error);
        echo json_encode(array(
            'status' => 'error',
            'message' => 'Failed to update personal details'
        ));
        exit;
    }

    // Process educational qualifications
    $edu_counter = isset($_POST['edurowcnt']) ? $_POST['edurowcnt'] : 0;
    $sql_educational_dl = "DELETE from mst_educational_qualifications WHERE stu_nic = ? AND exm_type = 'A/L'";
    $stmt = $conn->prepare($sql_educational_dl);
    $stmt->bind_param("s", $dec_nic_no);
    $stmt->execute();

    // Insert A/L records
    for ($i = 0; $i <= $edu_counter; $i++) {
        $subject = isset($_POST["subject_AL_$i"]) ? trim($_POST["subject_AL_$i"]) : '';
        $grade = isset($_POST["result_AL_$i"]) ? trim($_POST["result_AL_$i"]) : '';
        $year = isset($_POST["year_AL_$i"]) ? trim($_POST["year_AL_$i"]) : '';

        if (!empty($subject) || !empty($grade) || !empty($year)) {
            $sql = "INSERT INTO mst_educational_qualifications (stu_nic, exam_year, exam_name, exm_type, subject_grade, award, stu_id) VALUES (?, ?, ?, 'A/L', ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $examName = $_POST['examNameAL'];
            $stmt->bind_param("sssssi", $dec_nic_no, $year, $examName, $subject, $grade, $last_id);
            if (!$stmt->execute()) {
                error_log("Failed to insert A/L record: " . $stmt->error);
                $err_code = 4;
            }
        }
    }

    // Insert O/L records similarly...

    // Update application status
    if ($err_code == 0) {
        $sql_updt = "UPDATE mst_personal_details SET application_confirm_status = 'Y', payment_status = 'PENDING' WHERE nic_no = ?";
        $stmt = $conn->prepare($sql_updt);
        $stmt->bind_param("s", $dec_nic_no);

        if ($stmt->execute()) {
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
    } else {
        echo json_encode(array(
            'status' => 'error',
            'message' => 'One or more operations failed'
        ));
    }
} else {
    echo json_encode(array(
        'status' => 'error',
        'message' => 'Applicant not found'
    ));
}
