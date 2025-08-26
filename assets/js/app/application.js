// Add loading indicator styles
$('<style>')
    .text(`
        .loading {
            background-image: url('assets/img/loading.gif') !important;
            background-repeat: no-repeat !important;
            background-position: right 10px center !important;
            background-size: 20px !important;
        }
    `)
    .appendTo('head');

$(document).ready(function () {
    console.log('application.js loaded');
    var serverUrl;
    var adminUrl;

    // Handle URL parameters for agent code
    const urlParams = new URLSearchParams(window.location.search);
    const agentCode = urlParams.get('agent_code');
    if (agentCode) {
        $('#agent_code').val(agentCode);
    }



    var navListItems = $('div.setup-panel div a'),
        allWells = $('.setup-content'),
        allNextBtn = $('.nextBtn'),
        allPrevBtn = $('.prevBtn');

    allWells.hide();

    // Navigate to the first step
    $('div.setup-panel div a.btn-primary').trigger('click');

    // Disable click on step links
    $('div.setup-panel div a.step-link').click(function (e) {
        e.preventDefault();
    });

    navListItems.click(function (e) {
        e.preventDefault();
        var $item = $(this),
            target = $($item.attr('href')),
            currentStep = parseInt($item.data('step')),
            previousStep = currentStep - 1;
        isValid = true;
        //console.log('Current step:', currentStep); // Debugging line to verify current step
        //console.log('Previous step:', previousStep);

        // Validate previous step before proceeding
        for (var i = 1; i < currentStep; i++) {
            if (!validateStep(i)) {
                isValid = false;
                break;
            }
        }

        if (!isValid) {
            return;
        }

        if (!$item.hasClass('disabled')) {
            navListItems.removeClass('btn-primary').addClass('btn-default');
            $item.addClass('btn-primary');
            allWells.hide();
            target.show();
            target.find('input:eq(0)').focus();
        }
    });

    allPrevBtn.click(function () {
        var curStep = $(this).closest(".setup-content"),
            curStepBtn = curStep.attr("id"),
            prevStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().prev().children("a");

        prevStepWizard.removeAttr('disabled').trigger('click');
    });

    allNextBtn.click(function () {
        var curStep = $(this).closest(".setup-content"),
            curStepBtn = curStep.attr("id"),
            nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
            curInputs = curStep.find("input[type='text'],input[type='url']"),
            isValid = true;
        currentStep = $(this).closest('.setup-content').attr('id').split('-')[1];
        isValid = validateStep(parseInt(currentStep));

        $(".form-group").removeClass("has-error");
        for (var i = 0; i < curInputs.length; i++) {
            if (!curInputs[i].validity.valid) {
                isValid = false;
                $(curInputs[i]).closest(".form-group").addClass("has-error");
            }
        }

        if (isValid)
            nextStepWizard.removeAttr('disabled').trigger('click');
    });

    $('div.setup-panel div a.btn-primary').trigger('click');



    $.ajax({
        url: '../data/get_degree_list.php',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            //console.log('data:', data);
            var dropdown = $('#inputCourse');
            $.each(data, function (key, value) {
                dropdown.append($('<option></option>').attr('value', value.degree_code).text(value.degree_name));
            });
        },
        error: function (xhr, status, error) {
            console.error('Error fetching options:', error);
        }
    });

    $("#foreign").click(function () {
        //alert(document.forms["my-form"]["citizenship_type"].value);
        document.getElementById("section1").style.display = 'flex';
        document.getElementById("section2").style.display = 'none';
        document.getElementById("section3").style.display = 'none';
        $("#inputCitizenship1").val("");
        $("#inputCitizenship2").val("");

    });
    $("#dual").click(function () {
        //alert("A");
        document.getElementById("section1").style.display = 'none';
        document.getElementById("section2").style.display = 'flex';
        document.getElementById("section3").style.display = 'flex';
        $("#inputCitizenship").val("");

    });
    $("#sriLanakan").click(function () {
        document.getElementById("section1").style.display = 'none';
        document.getElementById("section2").style.display = 'none';
        document.getElementById("section3").style.display = 'none';
        $("#inputCitizenship").val("");
        $("#inputCitizenship1").val("");
        $("#inputCitizenship2").val("");

    });

    function getUrls() {
        $.ajax({
            url: '../data/get_server_url.php',
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                //console.log('Server URL:', response.server_url);
                //console.log('Admin URL:', response.url_admin);
                serverUrl = response.server_url;
                adminUrl = response.url_admin;
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }
    $('#my-form2').submit(function (event) {
        console.log('Form submit handler triggered');
        event.preventDefault();
        // ...existing code...
    });
    $('#my-form').submit(function (event) {
        event.preventDefault();
        console.log('Form submitted');

        const form = $(this)[0];
        const formData = new FormData(form);

        // Ensure agent_code is included in form submission
        const agentCode = $('#agent_code').val();
        if (agentCode) {
            formData.append('agent_code', agentCode);
        } $.ajax({
            url: '../pages/formsave.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                //console.log('Raw AJAX response:', response);
                var res = response;
                if (typeof response === "string") {
                    try {
                        res = JSON.parse(response);
                        //console.log('Parsed response:', res);
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        res = {};
                    }
                }
                if (res.status === "success") {
                    toastr.success("Saved successfully", "");
                    if (res.passport_no) {
                        //console.log('Loading view page with passport:', res.passport_no);
                        var page = "../content/view_applicationform.php?nic=" + res.passport_no;
                        $("#content").load(page, function (response, status, xhr) {
                            if (status == "error") {
                                console.error("Error loading view page:", xhr.status, xhr.statusText);
                                toastr.error("Error loading view page: " + xhr.statusText, "", { timeOut: 1000 });
                            } else {
                                console.log('View page loaded successfully');
                            }
                        });
                    } else {
                        console.error('No passport number in response');
                        toastr.error("Error loading application view", "", { timeOut: 1000 });
                    }
                } else {
                    toastr.error(res.message || "Data not saved.", "", { timeOut: 1000 });
                }
            },
            error: function (xhr, status, error) {
                console.error('Error details:', {
                    status: status,
                    error: error,
                    response: xhr.responseText
                });
                toastr.error("Something went wrong.", '', { timeOut: 1000 });
            }
        });
    });

});

function previewImage(event) {
    var input = event.target;
    var reader = new FileReader();
    reader.onload = function () {
        var dataURL = reader.result;
        var output = document.getElementById('wizardPicturePreview');
        output.src = dataURL;
    };
    reader.readAsDataURL(input.files[0]);
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

// Call on blur
$('#inputEmailAddress').on('blur', function () {
    validateEmailField();
});

function validateStep(step) {
    var isValid = true;
    //console.log(step);

    if (step === 1) {
        if (!$('#passportno').val().trim()) {
            toastr.error("Please enter Passport Number", '', { timeOut: 1000, });
            //showToastMessage("Please enter Passport Number");
            isValid = false;
            return;
        } else {
            var passportno = $("#passportno").val();
            //isValid = true; 
            $.ajax({
                url: '../data/check_passport.php', // The URL of the PHP file that checks the passport number
                type: 'POST',
                async: false,
                data: { passportno: passportno },
                success: function (response) {
                    //console.log(response[0]);
                    if (response[0] === 'exist') {
                        toastr.error("Passport number already exists.", '', { timeOut: 1000, });
                        isValid = false;
                        // Redirect to application status
                        window.location.href = '../content/applicationstatus.php?idn=' + encodeURIComponent(passportno);
                        return;
                    } else {
                        isValid = true;
                    }
                },
                error: function () {
                    toastr.error("Error while checking passport number. Please try again later.", '', { timeOut: 1000, });
                    isValid = false;
                    return;
                }
            });
            //console.log('isValid ff:',isValid);

        }
    } else if (step === 2) {
        if ($('#inputCourse').val() === "select degree") {
            toastr.error("Please select a degree", '', { timeOut: 1000, });
            isValid = false;
            return;
        }
    } else if (step === 3) {
        var photoInput = $('#inputPhoto')[0];
        if (!photoInput.files || !photoInput.files[0]) {
            toastr.error("Please select a photo", '', { timeOut: 1000 });
            isValid = false;
            return;
        }

        if (!$('#inputInitials').val().trim()) {
            toastr.error("Please enter Name with Initial", '', { timeOut: 1000, });
            isValid = false;
            return;
        }
        if (!$('#inputFullname').val().trim()) {
            toastr.error("Please enter Full Name", '', { timeOut: 1000, });
            isValid = false;
            return;
        }
        if (!$('#inputDob').val().trim()) {
            toastr.error("Please enter Date of Birth", '', { timeOut: 1000, });
            isValid = false;
            return;
        }
        if ($('#inputGender').val() === "Select Gender") {
            toastr.error("Please select Gender", '', { timeOut: 1000, });
            isValid = false;
            return;
        }
        if ($('#inputCivilSts').val() === "Select Civil Status") {
            toastr.error("Please select Civil Ststus", '', { timeOut: 1000, });
            isValid = false;
            return;
        }
        if (!$('#inputEmailAddress').val().trim()) {
            toastr.error("Please enter Email address", '', { timeOut: 1000, });
            isValid = false;
            return;
        }
        if (!validateEmailField()) {
            isValid = false;
            return;
        }
        if (!$('#inputCountryBirth').val().trim()) {
            toastr.error("Please enter Birth country", '', { timeOut: 1000, });
            isValid = false;
            return;
        }
        if (!$('#periodStudy').val().trim()) {
            toastr.error("Please enter period of study", '', { timeOut: 1000, });
            isValid = false;
            return;
        }
        if (!$('#addressPermanent').val().trim()) {
            toastr.error("Please enter Permanent Address", '', { timeOut: 1000, });
            isValid = false;
            return;
        }
        if (document.forms["my-form"]["citizenship_type"].value === "") {
            if (!$('#inputCitizenship').val().trim()) {
                toastr.error("Please enter Citizenship type", '', { timeOut: 1000 });
                isValid = false;
                return;
            }
        }
        if (document.forms["my-form"]["citizenship_type"].value === "Foreign Citizenship") {
            if (!$('#inputCitizenship').val().trim()) {
                toastr.error("Please enter Citizenship", '', { timeOut: 1000 });
                isValid = false;
                return;
            }
        }
        if (document.forms["my-form"]["citizenship_type"].value === "Dual Citizenship") {
            if (!$('#inputCitizenship1').val().trim()) {
                toastr.error("Please enter Citizenship 1", '', { timeOut: 1000 });
                isValid = false;
                return;
            }
            if (!$('#inputCitizenship2').val().trim()) {
                toastr.error("Please enter Citizenship 2", '', { timeOut: 1000 });
                isValid = false;
                return;
            }
        }
        if ($('#citizenship_type').val() === "Dual Citizenship") {
            if (!$('#inputCitizenship1').val().trim()) {
                toastr.error("Please enter Citizenship 1", '', { timeOut: 1000 });
                isValid = false;
                return;
            }
            if (!$('#inputCitizenship2').val().trim()) {
                toastr.error("Please enter Citizenship 2", '', { timeOut: 1000 });
                isValid = false;
                return;
            }
        }

    } else if (step === 4) {
        if ($('#elegibleState').val() === "Please Select") {
            toastr.error("Please select eligibility", '', { timeOut: 1000, });
            isValid = false;
        }
    } else if (step === 6) {
        if (!$('#fatherName').val().trim()) {
            toastr.error("Please enter Father's Name", '', { timeOut: 1000, });
            isValid = false;
            return;
        } if (!$('#fatherMobileNo').val().trim()) {
            toastr.error("Please enter Father's Mobile No", '', { timeOut: 1000, });
            isValid = false;
            return;
        } if (!$('#motherName').val().trim()) {
            toastr.error("Please enter Mother's Name", '', { timeOut: 1000, });
            isValid = false;
            return;
        }
    } else if (step === 7) {
        if (!$('#refree1_details').val().trim()) {
            toastr.error("Please enter Refree1 details", '', { timeOut: 1000, });
            isValid = false;
            return;
        } if (!$('#refree1_phone').val().trim()) {
            toastr.error("Please enter Refree1 Phone No", '', { timeOut: 1000, });
            isValid = false;
            return;
        } if (!$('#refree2_details').val().trim()) {
            toastr.error("Please enter Refree2 details", '', { timeOut: 1000, });
            isValid = false;
            return;
        } if (!$('#refree2_phone').val().trim()) {
            toastr.error("Please enter Refree2 Phone No", '', { timeOut: 1000, });
            isValid = false;
            return;
        }
    } else if (step === 8) {
        if (!$('#docupldlink').val().trim()) {
            toastr.error("Please enter document upload link", '', { timeOut: 1000, });
            isValid = false;
            return;
        }
    }

    return isValid;
}
function showToastMessage(message) {
    var toast = $('<div class="toast" role="alert" aria-live="assertive" aria-atomic="true">')
        .addClass('bg-primary') // Example: Add background color
        .appendTo(document.body); // Append to the body

    // Create toast header
    var toastHeader = $('<div class="toast-header">').appendTo(toast);
    $('<strong class="me-auto">').text('Notification').appendTo(toastHeader);
    $('<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close">').appendTo(toastHeader);

    // Create toast body with the provided message
    $('<div class="toast-body">').text(message).appendTo(toast);

    // Show the toast
    toast.toast('show');


    toast.on('hidden.bs.toast', function () {
        toast.remove();
    });
}


function validateForm1() {

    if (document.forms["my-form"]["inputCourse"].value == "") {
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
    if (document.forms["my-form"]["inputDob"].value == "") {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Data',
            text: 'Please Enter Birth Day!',
            onAfterClose: () => {
                document.forms["my-form"]["inputDob"].focus();
            }
        })
        return false;
    } if (document.forms["my-form"]["citizenship_type"].value == "") {
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
    if (document.forms["my-form"]["inputGender"].value == "") {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Data',
            text: 'Please Select Gender!',
            onAfterClose: () => {
                document.forms["my-form"]["inputGender"].focus();
            }
        })
        return false;
    } if (document.forms["my-form"]["inputCivilSts"].value == "") {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Data',
            text: 'Please Select Status!',
            onAfterClose: () => {
                document.forms["my-form"]["inputCivilSts"].focus();
            }
        })
        return false;
    } if (document.forms["my-form"]["refree1_details"].value == "") {
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
    if (document.forms["my-form"]["refree1_phone"].value == "") {
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
    if (document.forms["my-form"]["citizenship_type"].value == "Foreign Citizenship") {
        if (document.forms["my-form"]["inputCitizenship"].value == "") {
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
    } if (document.forms["my-form"]["citizenship_type"].value == "Dual Citizenship") {
        if (document.forms["my-form"]["inputCitizenship1"].value == "") {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Data',
                text: 'Please Enter Dual Citizenship!',
                onAfterClose: () => {
                    document.forms["my-form"]["inputCitizenship1"].focus();
                }
            })
            return false;
        } if (document.forms["my-form"]["inputCitizenship2"].value == "") {
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



    var closingDate = document.forms["my-form"]["closingDate"].value;
    var dob1 = document.forms["my-form"]["inputDob"].value;
    var dob = moment(dob1, 'YYYY-MM-DD').format('MM/DD/YYYY');
    var endDate = moment(closingDate, 'YYYY-MM-DD').format('MM/DD/YYYY');

    var year25 = moment(endDate, 'MM/DD/YYYY').subtract(30, 'years').format('MM/DD/YYYY');

    var year17 = moment(endDate, 'MM/DD/YYYY').subtract(17, 'years').format('MM/DD/YYYY');

    var d_dob = dob.split("/");
    var d_25 = year25.split("/");
    var d_17 = year17.split("/");


    var bday = new Date(d_dob[2], parseInt(d_dob[0]) - 1, d_dob[1]);  // -1 because months are from 0 to 11
    var date25 = new Date(d_25[2], parseInt(d_25[0]) - 1, d_25[1]);
    var date17 = new Date(d_17[2], parseInt(d_17[0]) - 1, d_17[1]);
    if (date17 < bday) {
        Swal.fire({
            icon: 'warning',
            title: 'Age Limit',
            text: 'You are younger than 17!',
            onAfterClose: () => {
                document.forms["my-form"]["inputDob"].focus();
            }
        })
        return false;
    } if (bday < date25) {
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
    console.log('17: ' + date17);
    console.log('25:' + date25);
    console.log('17 > age: ' + date17 > bday);
    console.log('age > 25: ' + bday > date25);

}

