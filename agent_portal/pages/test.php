
<?php
 if (isset($_POST['submit'])) {
    try {
        if (!isset($_FILES['inputPhoto'])) {
            throw new Exception("No file was uploaded.");
        }

        $target_dir = "../profile/";
        if (!is_dir($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) {
                throw new Exception("Failed to create upload directory.");
            }
        }

        $target_file = $target_dir . basename($_FILES["inputPhoto"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if the file is an actual image or a fake image
        $check = getimagesize($_FILES["inputPhoto"]["tmp_name"]);
        if ($check === false) {
            throw new Exception("File is not an image.");
        }

        // Check if the file already exists
        if (file_exists($target_file)) {
            throw new Exception("Sorry, file already exists.");
        }

        // Check file size (limit set to 5MB)
        if ($_FILES["inputPhoto"]["size"] > 5000000) {
            throw new Exception("Sorry, your file is too large.");
        }

        // Allow only certain file formats
        $allowed_file_types = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($imageFileType, $allowed_file_types)) {
            throw new Exception("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
        }

        // Try to upload file
        if (!move_uploaded_file($_FILES["inputPhoto"]["tmp_name"], $target_file)) {
            // Get the error code
            $error_code = $_FILES['inputPhoto']['error'];
            switch ($error_code) {
                case UPLOAD_ERR_INI_SIZE:
                    throw new Exception("The uploaded file exceeds the upload_max_filesize directive in php.ini.");
                case UPLOAD_ERR_FORM_SIZE:
                    throw new Exception("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.");
                case UPLOAD_ERR_PARTIAL:
                    throw new Exception("The uploaded file was only partially uploaded.");
                case UPLOAD_ERR_NO_FILE:
                    throw new Exception("No file was uploaded.");
                case UPLOAD_ERR_NO_TMP_DIR:
                    throw new Exception("Missing a temporary folder.");
                case UPLOAD_ERR_CANT_WRITE:
                    throw new Exception("Failed to write file to disk.");
                case UPLOAD_ERR_EXTENSION:
                    throw new Exception("A PHP extension stopped the file upload.");
                default:
                    throw new Exception("Unknown upload error.");
            }
        }

        echo "The file " . htmlspecialchars(basename($_FILES["inputPhoto"]["name"])) . " has been uploaded.<br>";

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "<br>";
    }
}
?>

 <form id="my-form" name="my-form" method="post"  enctype="multipart/form-data"> <!-- action="../pages/formsave.php" -->
    <div class="col-md-4">
    <div class="mb-3">
            <label class="form-label" for="inputPhoto">Photo</label>
                <div class="picture-container">
                    <div class="picture">
                        <img class="picture-src" id="wizardPicturePreview" title="" >
                        <input type="file" id="inputPhoto" name="inputPhoto" class="form-control" accept="image/jpeg" placeholder="Choose Image" onchange="previewImage(event)">
                    </div>
                </div>
                <label class="small mb-1" for="inputPhoto" style="color: #007300;" >Choose Image</label>
        </div>
    </div>
    <input type="submit" value="Upload Photo" name="submit">
 </form>

