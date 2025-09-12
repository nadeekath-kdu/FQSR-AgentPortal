<?php
// Include database connection
require_once '../../config/dbcon.php';

date_default_timezone_set('Asia/Colombo'); // or 'UTC'
$createdAt = date('Y-m-d H:i:s');
// Function to sanitize and validate input
function sanitizeInput($connection, $input)
{
    // Trim whitespace
    $input = trim($input);
    // Remove backslashes
    $input = stripslashes($input);
    // Escape special characters to prevent SQL injection
    return mysqli_real_escape_string($connection, $input);
}

// Function to handle file uploads
function uploadFiles($agency_code)
{
    $uploadedFiles = array();
    $errors = array();

    // Allowed file types and max size
    //$allowedTypes = ['zip', 'pdf', 'docx', 'jpeg', 'jpg', 'png', 'gif'];
    $allowedTypes = array('zip', 'pdf', 'docx', 'jpeg', 'jpg', 'png', 'gif');
    $maxFileSize = 5 * 1024 * 1024; // 5MB



    // Check if files were uploaded
    if (!empty($_FILES['document']['name'][0])) {
        // Create directory if it doesn't exist
        $uploadDir = '../upload/' . $agency_code . '/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileCount = count($_FILES['document']['name']);

        for ($i = 0; $i < $fileCount; $i++) {
            $fileName = $_FILES['document']['name'][$i];
            $fileTmpName = $_FILES['document']['tmp_name'][$i];
            $fileSize = $_FILES['document']['size'][$i];
            $fileError = $_FILES['document']['error'][$i];

            // Get file extension
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Validate file
            if ($fileError !== UPLOAD_ERR_OK) {
                $errors[] = "Upload error for file $fileName: " . $fileError;
                continue;
            }

            if ($fileSize > $maxFileSize) {
                $errors[] = "File $fileName is too large. Maximum size is 5MB.";
                continue;
            }

            if (!in_array($fileExt, $allowedTypes)) {
                $errors[] = "Invalid file type for $fileName. Allowed types: " . implode(', ', $allowedTypes);
                continue;
            }

            // Generate unique filename to prevent overwriting
            $uniqueFileName = uniqid() . '_' . $fileName;
            $destination = $uploadDir . $uniqueFileName;

            // Move uploaded file
            if (move_uploaded_file($fileTmpName, $destination)) {
                $uploadedFiles[] = $uniqueFileName;
            } else {
                $errors[] = "Failed to move uploaded file $fileName";
            }
        }
    }

    return array(
        'files' => $uploadedFiles,
        'errors' => $errors
    );
}

// Main processing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    $requiredFields = array('organisation', 'addressLine1', 'fullname', 'nic', 'email');
    $missingFields = array();

    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $missingFields[] = $field;
        }
    }

    // If there are missing required fields, return error
    if (!empty($missingFields)) {
        $response = array(
            'success' => false,
            //'url' => false,
            'message' => 'Missing required fields: ' . implode(', ', $missingFields)
        );
        echo json_encode($response);
        exit;
    }

    // Sanitize inputs
    $organisation = sanitizeInput($con, $_POST['organisation']);
    $addressLine1 = sanitizeInput($con, $_POST['addressLine1']);
    $addressLine2 = isset($_POST['addressLine2']) ? sanitizeInput($con, $_POST['addressLine2']) : '';
    $addressLine3 = isset($_POST['addressLine3']) ? sanitizeInput($con, $_POST['addressLine3']) : '';
    $city = isset($_POST['city']) ? sanitizeInput($con, $_POST['city']) : '';
    $country = isset($_POST['country']) ? sanitizeInput($con, $_POST['country']) : '';
    $postcode = isset($_POST['postcode']) ? sanitizeInput($con, $_POST['postcode']) : '';
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) : '';
    $alt_email = isset($_POST['alt_email']) ? filter_var($_POST['alt_email'], FILTER_VALIDATE_EMAIL) : '';
    $fullname = sanitizeInput($con, $_POST['fullname']);
    $nic = sanitizeInput($con, $_POST['nic']);
    $owner_address = isset($_POST['ownerAddress']) ? sanitizeInput($con, $_POST['ownerAddress']) : '';
    $telephone1 = isset($_POST['telephone1']) ? sanitizeInput($con, $_POST['telephone1']) : '';
    $telephone2 = isset($_POST['telephone2']) ? sanitizeInput($con, $_POST['telephone2']) : '';
    $mobile = isset($_POST['mobile']) ? sanitizeInput($con, $_POST['mobile']) : '';
    $fax = isset($_POST['fax']) ? sanitizeInput($con, $_POST['fax']) : '';
    $url = isset($_POST['url']) ? filter_var($_POST['url'], FILTER_VALIDATE_URL) : '';


    // Validate email
    if ($email === false) {
        $response = array(
            'success' => false,
            'message' => 'Invalid email address'
        );
        echo json_encode($response);
        exit;
    }


    $checkEmailQuery = "SELECT agency_code FROM agency WHERE email = ?";
    $checkStmt = mysqli_prepare($con, $checkEmailQuery);
    mysqli_stmt_bind_param($checkStmt, "s", $email);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_store_result($checkStmt);

    if (mysqli_stmt_num_rows($checkStmt) > 0) {
        $response = array(
            'success' => false,
            'message' => 'Email already exists'
        );
        echo json_encode($response);
        mysqli_stmt_close($checkStmt);
        exit;
    }
    mysqli_stmt_close($checkStmt);

    // Generate unique agency code
    $lastRecQuery = "SELECT rec_id FROM agency ORDER BY rec_id DESC LIMIT 1";
    $lastRecResult = mysqli_query($con, $lastRecQuery);
    $lastRecRow = mysqli_fetch_assoc($lastRecResult);
    $lastRec = $lastRecRow ? $lastRecRow['rec_id'] + 1 : 1;
    $agency_code = date('Y') . 'AGNT' . $lastRec;

    // Handle file uploads
    $uploadResult = uploadFiles($agency_code);

    // Prepare uploaded files string (comma-separated)
    $uploadedFiles = implode(',', $uploadResult['files']);

    // Prepare SQL statement
    $insertQuery = "INSERT INTO agency (
        agency_code, fullname, organisation, addressLine1, addressLine2, addressLine3, 
        city, country, postcode, email, alt_email, telephone1, telephone2, 
        mobile, fax, url, document, owner_nic, owner_address,created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($con, $insertQuery);
    mysqli_stmt_bind_param(
        $stmt,
        "ssssssssssssssssssss",
        $agency_code,
        $fullname,
        $organisation,
        $addressLine1,
        $addressLine2,
        $addressLine3,
        $city,
        $country,
        $postcode,
        $email,
        $alt_email,
        $telephone1,
        $telephone2,
        $mobile,
        $fax,
        $url,
        $uploadedFiles,
        $nic,
        $owner_address,
        $createdAt
    );

    // Execute the statement
    if (mysqli_stmt_execute($stmt)) {
        $response = array(
            'success' => true,
            'message' => 'Agency registration successful'
            //'agency_code' => $agency_code
        );

        // Add any upload errors to the response
        /* if (!empty($uploadResult['errors'])) {
            $response = array('upload_warnings' => $uploadResult['errors']);
        } */
    } else {
        $response = array(
            'success' => false,
            'message' => 'Failed to save agency details: ' . mysqli_error($con)
        );
    }

    // Close statement
    mysqli_stmt_close($stmt);

    // Return JSON response
    //header('Content-Type: application/json');
    echo json_encode($response);
    exit;
} else {
    // Invalid request method
    http_response_code(405);
    echo json_encode(array(
        'success' => false,
        'message' => 'Method Not Allowed'
    ));
    exit;
}

// Close database connection
mysqli_close($con);
