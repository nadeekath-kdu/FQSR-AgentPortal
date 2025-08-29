// Global variables
var closingDateLoaded = false;

// Helper functions
function updateCitizenshipSections() {
    var selectedValue = $('input[name="citizenship_type"]:checked').val();
    $('#section1, #section2, #section3').hide();
    $('#inputCitizenship, #inputCitizenship1, #inputCitizenship2').removeClass('is-invalid');

    if (selectedValue === 'Foreign Citizenship') {
        $('#section1').css('display', 'block');
        $('#inputCitizenship1, #inputCitizenship2').val('');
    } else if (selectedValue === 'Dual Citizenship') {
        $('#section2, #section3').css('display', 'block');
        $('#inputCitizenship').val('');
    } else if (selectedValue === 'Sri Lankan Citizenship Only') {
        $('#inputCitizenship, #inputCitizenship1, #inputCitizenship2').val('');
    }
}

function validateAge() {
    if (!closingDateLoaded) {
        toastr.error("Please wait, loading application closing date...", '', { timeOut: 2000 });
        return false;
    }
    const dob = new Date($('#inputDob').val());
    const closingDate = new Date($('#closingDate').val());
    let age = closingDate.getFullYear() - dob.getFullYear();
    const m = closingDate.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && closingDate.getDate() < dob.getDate())) {
        age--;
    }

    const degreeCode = $('#inputCourse').val();
    if (degreeCode === 'MED') {
        if (age < 16 || age > 29) {
            toastr.error("For medicine programs, age must be between 16 and 29 years", '', { timeOut: 2000 });
            return false;
        }
    } else {
        if (age < 16 || age > 25) {
            toastr.error("For non-medicine programs, age must be between 16 and 25 years", '', { timeOut: 2000 });
            return false;
        }
    }
    return true;
}

function validateEmailField() {
    var email = $('#inputEmailAddress').val().trim();
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email) {
        toastr.error("Please enter Email address", '', { timeOut: 1000 });
        return false;
    } else if (!emailPattern.test(email)) {
        toastr.error("Please enter a valid Email address", '', { timeOut: 1000 });
        return false;
    }
    return true;
}

function validateForm() {
    let isValid = true;
    let errorMessage = '';

    // Required fields validation
    const requiredFields = [
        { name: 'inputFullname', label: 'Full Name' },
        { name: 'inputNameInitials', label: 'Name with Initials' },
        { name: 'inputDob', label: 'Date of Birth' },
        { name: 'inputGender', label: 'Gender' },
        { name: 'inputCivilSts', label: 'Civil Status' },
        { name: 'citizenship_type', label: 'Citizenship' },
        { name: 'inputCountryBirth', label: 'Country of Birth' },
        { name: 'periodStudy', label: 'Period of Study apart from Sri Lanka' },
        { name: 'addressPermanent', label: 'Permanent Address' },
        { name: 'inputEmailAddress', label: 'Email Address' },
        { name: 'elegibleState', label: 'State University Eligibility' },
        { name: 'fatherName', label: "Father's Name" },
        { name: 'fatherMobileNo', label: "Father's Contact" },
        { name: 'motherName', label: "Mother's Name" },
        { name: 'inputCourse', label: "Course" },
        { name: 'refree1_details', label: "First Referee Name" },
        { name: 'refree1_phone', label: "First Referee Contact" },
        { name: 'refree2_details', label: "Second Referee Name" },
        { name: 'refree2_phone', label: "Second Referee Contact" }
    ];

    // Check required fields
    for (let field of requiredFields) {
        const value = $(`[name="${field.name}"]`).val();
        if (!value || value.trim() === '') {
            isValid = false;
            errorMessage += `${field.label} is required.\n`;
            $(`[name="${field.name}"]`).addClass('is-invalid');
        } else {
            $(`[name="${field.name}"]`).removeClass('is-invalid');
        }
    }

    // Validate age
    if (!validateAge()) {
        isValid = false;
        return false;
    }

    // Validate citizenship
    let citizenshipType = document.forms["my-form"]["citizenship_type"].value;
    if (citizenshipType === "") {
        toastr.error("Please select Citizenship type", '', { timeOut: 1000 });
        return false;
    }

    if (citizenshipType === "Foreign Citizenship" && !$('#inputCitizenship').val().trim()) {
        toastr.error("Please enter Citizenship", '', { timeOut: 1000 });
        return false;
    }

    if (citizenshipType === "Dual Citizenship") {
        if (!$('#inputCitizenship1').val().trim()) {
            toastr.error("Please enter first Citizenship", '', { timeOut: 1000 });
            return false;
        }
        if (!$('#inputCitizenship2').val().trim()) {
            toastr.error("Please enter second Citizenship", '', { timeOut: 1000 });
            return false;
        }
    }

    // Check documents
    if (selectedFilesArray.length === 0) {
        toastr.error("Please upload required documents", '', { timeOut: 1000 });
        return false;
    }

    // Email validation
    if (!validateEmailField()) {
        return false;
    }

    // Eligibility check
    if ($('#elegibleState').val() === "Please Select") {
        toastr.error("Please select eligibility state", '', { timeOut: 1000 });
        $('#elegibleState').focus();
        return false;
    }

    // Educational results validation
    if (!validateEducationalResults()) {
        return false;
    }

    // Photo validation
    const photoInput = $('#Photo').length > 0 ? $('#Photo') : $('#inputPhoto');
    if (photoInput.length > 0 && photoInput[0].files.length > 0) {
        const file = photoInput[0].files[0];
        const fileSize = file.size / 1024 / 1024;
        const allowedTypes = ['image/jpeg', 'image/png'];

        if (fileSize > 2) {
            errorMessage += 'Photo size should not exceed 2MB.\n';
            isValid = false;
        }

        if (!allowedTypes.includes(file.type)) {
            errorMessage += 'Photo must be in JPG or PNG format.\n';
            isValid = false;
        }
    }

    if (!isValid) {
        toastr.error(errorMessage, "Validation Error");
    }

    return isValid;
}

// Document ready handler
$(document).ready(function () {
    // Initialize validation handlers
    $('#inputEmailAddress').on('blur', validateEmailField);
    $('#inputDob, #inputCourse').on('change', validateAge);

    // Initialize citizenship handlers
    $('input[name="citizenship_type"]').on('change', updateCitizenshipSections);

    // Initialize citizenship field validation
    $('#inputCitizenship').on('blur', function () {
        if ($('input[name="citizenship_type"]:checked').val() === 'Foreign Citizenship') {
            if (!$(this).val().trim()) {
                $(this).addClass('is-invalid');
                toastr.error('Please enter Citizenship', '', { timeOut: 1000 });
            } else {
                $(this).removeClass('is-invalid');
            }
        }
    });

    $('#inputCitizenship1, #inputCitizenship2').on('blur', function () {
        if ($('input[name="citizenship_type"]:checked').val() === 'Dual Citizenship') {
            if (!$(this).val().trim()) {
                $(this).addClass('is-invalid');
                toastr.error('Please enter ' + (this.id === 'inputCitizenship1' ? 'first' : 'second') + ' Citizenship', '', { timeOut: 1000 });
            } else {
                $(this).removeClass('is-invalid');
            }
        }
    });

    // Add trim on blur for referee fields
    $('#refree1_details, #refree2_details, #refree1_phone, #refree2_phone').on('blur', function () {
        $(this).val($(this).val().trim());
    });

    // Initialize sections
    updateCitizenshipSections();

    // Load closing date
    $.ajax({
        url: '../data/get_closing_date.php',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success' && response.closing_date) {
                $('#closingDate').val(response.closing_date);
                closingDateLoaded = true;
            } else {
                console.error('No closing date received:', response);
                toastr.error("Could not load closing date", '', { timeOut: 2000 });
            }
        },
        error: function (xhr, status, error) {
            console.error('Error details:', { xhr: xhr.responseText, status, error });
            toastr.error("Could not connect to server", '', { timeOut: 2000 });
        }
    });

    // Button handlers
    $('.btn-update').click(function (e) {
        e.preventDefault();
        if (!validateForm()) return;

        // Trim all text inputs
        $('#my-form input[type="text"], #my-form textarea').each(function () {
            $(this).val($(this).val().trim());
        });

        var formData = new FormData($('#my-form')[0]);
        var photoInput = $('#Photo').length > 0 ? $('#Photo')[0] : $('#inputPhoto')[0];
        if (photoInput && photoInput.files.length > 0) {
            formData.append('Photo', photoInput.files[0]);
        }

        if (!$('#inputNic').val().trim()) {
            toastr.error("NIC/Passport number is required", "Error");
            return;
        }

        $.ajax({
            url: '../pages/formupdate.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                try {
                    console.log('Raw response:', response);
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }

                    if (response.status === 'success') {
                        toastr.success(response.message || "Saved successfully");

                        // Handle file operations feedback
                        if (response.uploaded_files?.length > 0) {
                            console.log('Uploaded files:', response.uploaded_files);
                        }
                        if (response.upload_errors?.length > 0) {
                            console.error('Upload errors:', response.upload_errors);
                            response.upload_errors.forEach(error => {
                                toastr.warning(error, "File Upload Warning");
                            });
                        }

                        // Redirect if passport number is present
                        if (response.passport_no) {
                            $("#content").load("../content/view_applicationform.php?nic=" + response.passport_no);
                        } else {
                            console.warn('Success response missing passport_no:', response);
                            toastr.warning("Success but no passport number returned");
                        }
                    } else {
                        toastr.error(response.message || "Error updating form", "Error");
                        if (response.upload_errors?.length > 0) {
                            response.upload_errors.forEach(error => {
                                toastr.error(error, "File Upload Error");
                            });
                        }
                        console.error('Error response:', response);
                    }
                } catch (e) {
                    console.error('Response processing error:', e, response);
                    toastr.error("Error processing response: " + e.message, "Error");
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', { status, error, response: xhr.responseText });
                if (xhr.status === 404) {
                    toastr.error("Server script not found", "Error 404");
                } else if (xhr.status === 500) {
                    toastr.error("Server error occurred", "Error 500");
                } else {
                    toastr.error("Connection error: " + error, "Error " + xhr.status);
                }
            }
        });
    });

    // Checkout button handler
    $(".btn-checkout").on("click", function (e) {
        e.preventDefault();
        var nic = $(this).data('nic');
        if (!nic) {
            toastr.error("No passport/NIC number found", "Error");
            return;
        }
        window.open('http://enlistment.kdu.ac.lk/pg_sampath/pgrequest_check_fsr.php?idn=' + nic, '_blank');
        window.location.href = '../content/application_formpdf.php?nic=' + encodeURIComponent(nic);
    });

    // Edit button handler
    $('.btn-edit').click(function (e) {
        e.preventDefault();
        var nic = $(this).data('nic') || $('#inputNic').val();
        if (!nic) {
            toastr.error("No passport/NIC number found", "Error");
            return;
        }

        $("#content").load("../content/edit_applicationform.php?nic=" + encodeURIComponent(nic),
            function (response, status, xhr) {
                if (status === "success") {
                    if (response.includes("No application ID provided") ||
                        response.includes("Application not found")) {
                        toastr.error(response, "Error");
                    } else {
                        toastr.info("Form is ready for editing", "");
                    }
                } else {
                    toastr.error("Error loading form", "");
                }
            });
    });

    // Cancel button handler
    $(".btn-cancel").on("click", function (e) {
        e.preventDefault();
        $("#content").load("../content/viewappdatalist.html");
    });
});
