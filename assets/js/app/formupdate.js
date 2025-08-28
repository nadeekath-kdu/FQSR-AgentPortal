// Handle citizenship type radio buttons
$(document).ready(function () {
    // Initialize validation handlers
    $('#inputEmailAddress').on('blur', validateEmailField);
    $('#inputDob').on('change', validateAge);
    $('#inputCourse').on('change', validateAge);

    function updateCitizenshipSections() {
        //console.log('Updating citizenship sections');
        // Get the selected radio button
        var selectedValue = $('input[name="citizenship_type"]:checked').val();
        //console.log('Selected citizenship type:', selectedValue);

        // Hide all sections first and clear validation states
        $('#section1, #section2, #section3').hide();
        $('#inputCitizenship, #inputCitizenship1, #inputCitizenship2').removeClass('is-invalid');

        // Show relevant sections based on selection
        if (selectedValue === 'Foreign Citizenship') {
            $('#section1').css('display', 'block');
            //console.log('Showing Foreign Citizenship section');
            // Clear other citizenship fields
            $('#inputCitizenship1, #inputCitizenship2').val('');
        } else if (selectedValue === 'Dual Citizenship') {
            $('#section2, #section3').css('display', 'block');
            //console.log('Showing Dual Citizenship sections');
            // Clear foreign citizenship field
            $('#inputCitizenship').val('');
        } else if (selectedValue === 'Sri Lankan Citizenship Only') {
            // Clear all citizenship fields for Sri Lankan
            $('#inputCitizenship, #inputCitizenship1, #inputCitizenship2').val('');
        }
    }

    // Add change event listeners to all citizenship radio buttons
    $('input[name="citizenship_type"]').on('change', function () {
        // console.log('Citizenship type changed to:', this.value);
        updateCitizenshipSections();

        // Clear validation states when type changes
        $('#inputCitizenship, #inputCitizenship1, #inputCitizenship2').removeClass('is-invalid');

        // Clear input fields when changing type
        if (this.value !== 'Foreign Citizenship') {
            $('#inputCitizenship').val('');
        }
        if (this.value !== 'Dual Citizenship') {
            $('#inputCitizenship1, #inputCitizenship2').val('');
        }
    });

    // Add blur validation for citizenship fields
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

    // Initialize sections on page load
    //console.log('Initializing citizenship sections');
    updateCitizenshipSections();
});

var closingDateLoaded = false;

// Get application closing date
$.ajax({
    url: '../data/get_closing_date.php',
    type: 'GET',
    dataType: 'json',
    success: function (response) {
        if (response.closing_date) {
            $('#closingDate').val(response.closing_date);
            closingDateLoaded = true;
        } else {
            //console.error('No closing date received from server');
            toastr.error("Error: Could not get application closing date", '', { timeOut: 2000 });
        }
    },
    error: function (xhr, status, error) {
        //console.error('Error fetching closing date:', error);
        toastr.error("Error: Could not get application closing date", '', { timeOut: 2000 });
    }
});

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

    // Check if medicine program (MED)
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
        //{ name: 'fatherAddress', label: "Father's Address" },
        { name: 'fatherMobileNo', label: "Father's Contact" },
        //{ name: 'fatherEmail', label: "Father's Email" },
        { name: 'motherName', label: "Mother's Name" },
        { name: 'inputCourse', label: "Course" },
        //{ name: 'motherContact', label: "Mother's Contact" },
        //{ name: 'motherEmail', label: "Mother's Email" },
        { name: 'refree1_details', label: "First Referee Name" },
        { name: 'refree1_phone', label: "First Referee Contact" },
        //{ name: 'referee1Contact', label: "First Referee Contact" },
        { name: 'refree2_details', label: "Second Referee Name" },
        { name: 'refree2_phone', label: "Second Referee Contact" },
        //{ name: 'referee2Contact', label: "Second Referee Contact" },
        //{ name: 'docupldlink', label: 'Documents Upload Link' }
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

    // Validate citizenship based on type
    if (document.forms["my-form"]["citizenship_type"].value === "") {
        if (!$('#inputCitizenship').val().trim()) {
            toastr.error("Please enter Citizenship type", '', { timeOut: 1000 });
            isValid = false;
            return false;
        }
    }

    if (document.forms["my-form"]["citizenship_type"].value === "Foreign Citizenship") {
        if (!$('#inputCitizenship').val().trim()) {
            toastr.error("Please enter Citizenship", '', { timeOut: 1000 });
            isValid = false;
            return false;
        }
    }

    if (document.forms["my-form"]["citizenship_type"].value === "Dual Citizenship") {
        if (!$('#inputCitizenship1').val().trim()) {
            toastr.error("Please enter first Citizenship", '', { timeOut: 1000 });
            isValid = false;
            return false;
        }
        if (!$('#inputCitizenship2').val().trim()) {
            toastr.error("Please enter second Citizenship", '', { timeOut: 1000 });
            isValid = false;
            return false;
        }
    }

    // Email validation
    if (!validateEmailField()) {
        isValid = false;
        return false;
    }

    // Check eligibility state before validating results
    if ($('#elegibleState').val() === "Please Select") {
        toastr.error("Please select eligibility state", '', { timeOut: 1000 });
        $('#elegibleState').focus();
        isValid = false;
        return false;
    }

    // Educational results validation
    if (!validateEducationalResults()) {
        isValid = false;
        return false;
    }

    // Photo validation if file input exists
    const photoInput = $('#Photo').length > 0 ? $('#Photo') : $('#inputPhoto');
    if (photoInput.length > 0 && photoInput[0].files.length > 0) {
        const file = photoInput[0].files[0];
        const fileSize = file.size / 1024 / 1024; // in MB
        const allowedTypes = ['image/jpeg', 'image/png'];

        if (fileSize > 2) {
            isValid = false;
            errorMessage += 'Photo size should not exceed 2MB.\n';
        }

        if (!allowedTypes.includes(file.type)) {
            isValid = false;
            errorMessage += 'Photo must be in JPG or PNG format.\n';
        }
    }

    if (!isValid) {
        toastr.error(errorMessage, "Validation Error");
    }

    return isValid;
}

$(document).ready(function () {
    // Add trim on blur for referee fields
    $('#refree1_details, #refree2_details, #refree1_phone, #refree2_phone').on('blur', function () {
        let trimmedValue = $(this).val().trim();
        $(this).val(trimmedValue);
    });

    $('.btn-update').click(function (e) {
        e.preventDefault();
        if (!validateForm()) return;

        // Trim all text inputs before creating FormData
        $('#my-form input[type="text"], #my-form textarea').each(function () {
            $(this).val($(this).val().trim());
        });

        // Specifically trim referee fields
        $('#refree1_details').val($('#refree1_details').val().trim());
        $('#refree2_details').val($('#refree2_details').val().trim());
        $('#refree1_phone').val($('#refree1_phone').val().trim());
        $('#refree2_phone').val($('#refree2_phone').val().trim());

        var formData = new FormData($('#my-form')[0]);

        // Add the photo if it exists
        var photoInput = $('#Photo').length > 0 ? $('#Photo')[0] : $('#inputPhoto')[0];
        if (photoInput && photoInput.files.length > 0) {
            formData.append('Photo', photoInput.files[0]);
        }
        if (!$('#inputNic').val().trim()) {
            toastr.error("NIC/Passport number is required", "Error");
            return false;
        }

        // Debug form data before sending
        //console.log('Form Data Contents:');
        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

        $.ajax({
            url: '../pages/formupdate.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                try {
                    //console.log('Raw response:', response);

                    // Try to parse if it's a string
                    if (typeof response === 'string') {
                        try {
                            response = JSON.parse(response);
                            //console.log('Parsed response:', response);
                        } catch (parseError) {
                            //console.error('Parse error:', parseError);
                            //console.log('Response that failed to parse:', response);
                            toastr.error("Server returned invalid JSON. Check console for details.", "Error");
                            return;
                        }
                    }

                    if (response.status === 'success') {
                        toastr.success(response.message || "Saved successfully");
                        if (response.passport_no) {
                            var page = "../content/view_applicationform.php?nic=" + response.passport_no;
                            $("#content").load(page);
                        } else {
                            toastr.warning("Success but no passport number returned");
                            //console.warn('Success response missing passport_no:', response);
                        }
                    } else {
                        toastr.error(response.message || "Error updating form", "Error");
                        //console.error('Error response:', response);
                    }
                } catch (e) {
                    toastr.error("Error processing response: " + e.message, "Error");
                    //console.error('Processing error:', e);
                    //console.error('Response that caused error:', response);
                }
            },
            error: function (xhr, status, error) {
                toastr.error("Error updating form: " + error, "Error");
                //console.error(xhr.responseText);
            }
        });

    });

    $('.btn-submit').click(function (e) { // not use
        e.preventDefault();
        var formData = $('#my-form').serialize();
        $.ajax({
            url: '////https://enlistment.kdu.ac.lk/fqsr/formsave.php?idn='.$enc_nic_no,
            type: 'POST',
            data: formData,
            success: function (response) {
                toastr.success("Saved successfully", "");
                var page = "../content/viewappdatalist.html";
                $("#content").load(page);
            },
            error: function (xhr, status, error) {
                toastr.error("Error sending form data:.", "Error");
            }
        });

    });

    $(".btn-checkout").on("click", function (e) {
        e.preventDefault();
        //console.log('Proceeding to checkout');
        var nic = $(this).data('nic');
        if (!nic) {
            toastr.error("No passport/NIC number found", "Error");
            return;
        }
        // Open the payment/checkout page in a new tab
        var paymentUrl = 'http://enlistment.kdu.ac.lk/pg_sampath/pgrequest_check_fsr.php?idn=' + nic;
        window.open(paymentUrl, '_blank');
        // Open the PDF in the current tab for auto-download
        var pdfUrl = '../content/application_formpdf.php?nic=' + encodeURIComponent(nic);
        window.location.href = pdfUrl;
    });

    $('.btn-edit').click(function (e) {
        e.preventDefault();
        // Get the NIC from the data attribute
        var nic = $(this).data('nic');
        // If no NIC in data attribute, try to get it from the hidden input
        if (!nic) {
            nic = $('#inputNic').val();
        }
        if (!nic) {
            toastr.error("No passport/NIC number found", "Error");
            return;
        }
        var page = "../content/edit_applicationform.php";
        page += "?nic=" + encodeURIComponent(nic);

        $("#content").load(page, function (response, status, xhr) {
            if (status == "success") {
                if (response.includes("No application ID provided") || response.includes("Application not found")) {
                    toastr.error(response, "Error");
                    return;
                }
                // Initialize any form elements or load data if needed
                toastr.info("Form is ready for editing", "");
            } else {
                toastr.error("Error loading form", "");
            }
        });
    });

    $(".btn-cancel").on("click", function (e) {
        e.preventDefault();
        //console.log('cance, no update');
        var page = "../content/viewappdatalist.html";
        $("#content").load(page);
    });

});