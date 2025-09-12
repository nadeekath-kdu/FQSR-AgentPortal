$(document).ready(function () {
    var serverUrl;
    var adminUrl;
    getUrls();
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
        console.log('Current step:', currentStep); // Debugging line to verify current step
        console.log('Previous step:', previousStep); 

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
  
  allPrevBtn.click(function(){
      var curStep = $(this).closest(".setup-content"),
          curStepBtn = curStep.attr("id"),
          prevStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().prev().children("a");

          prevStepWizard.removeAttr('disabled').trigger('click');
  });

  allNextBtn.click(function(){
      var curStep = $(this).closest(".setup-content"),
          curStepBtn = curStep.attr("id"),
          nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
          curInputs = curStep.find("input[type='text'],input[type='url']"),
          isValid = true;
          currentStep = $(this).closest('.setup-content').attr('id').split('-')[1];
          isValid = validateStep(parseInt(currentStep));

      $(".form-group").removeClass("has-error");
      for(var i=0; i<curInputs.length; i++){
          if (!curInputs[i].validity.valid){
              isValid = false;
              $(curInputs[i]).closest(".form-group").addClass("has-error");
          }
      }

      if (isValid)
          nextStepWizard.removeAttr('disabled').trigger('click');
  });

  $('div.setup-panel div a.btn-primary').trigger('click');
  
  

  $.ajax({
    url: '../includes/data/get_degree_list.php', 
    type: 'GET',
    dataType: 'json',
    success: function(data) {
        console.log('data:',data);
        var dropdown = $('#inputCourse');
        $.each(data, function(key, value) {
            dropdown.append($('<option></option>').attr('value', value.degree_code).text(value.degree_name));
        });
    },
    error: function(xhr, status, error) {
        console.error('Error fetching options:', error);
    }
});

$("#foreign").click(function(){
    //alert(document.forms["my-form"]["citizenship_type"].value);
    document.getElementById("section1").style.display = 'flex';
    document.getElementById("section2").style.display = 'none';
    document.getElementById("section3").style.display = 'none';
    $("#inputCitizenship1").val("");
    $("#inputCitizenship2").val("");
    
  });
  $("#dual").click(function(){
    //alert("A");
    document.getElementById("section1").style.display = 'none';
    document.getElementById("section2").style.display = 'flex';
    document.getElementById("section3").style.display = 'flex';
    $("#inputCitizenship").val("");
    
  });
  $("#sriLanakan").click(function(){
    document.getElementById("section1").style.display = 'none';
    document.getElementById("section2").style.display = 'none';
    document.getElementById("section3").style.display = 'none';
    $("#inputCitizenship").val("");
    $("#inputCitizenship1").val("");
    $("#inputCitizenship2").val("");
    
  });

  function getUrls() {
    $.ajax({
        url: 'https://enlistment.kdu.ac.lk/agent_portal/includes/data/get_server_url.php', 
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Server URL:', response.server_url);
            console.log('Admin URL:', response.url_admin);
            serverUrl =  response.server_url;
            adminUrl =  response.url_admin;
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
        }
    });
}

  $('#my-form').submit(function(event) {
    event.preventDefault(); 
    
    const serializedData = $(this).serialize();

    $.ajax({
      url: serverUrl+'/pages/formsave.php',
      method: 'POST',
      data: serializedData,
      success: function(response) {
        console.log('Success:', response);
        var page = "content/viewappdatalist.html";
        $("#content").load(page);
        toastr.success("Saved successfully", "");
      },
      error: function(serverUrl) {
        console.error('Error:', serverUrl);
        var page = "content/instructionform.html";
        $("#content").load(page);
        toastr.error("Something went wrong.", '', { timeOut: 1000,});
      }
    });
  });

  $(document).on("click", ".btn-save111", function (event) {// not use
    event.preventDefault();

    //var formData = new FormData($("#my-form"));
    //console.log('Click save btn1', formData);
    //validateForm();
    var passportno = $('#passportno').val().trim();
    var inputCourse = $("#inputCourse").val();
    //var inputPhoto = $("#inputPhoto").val();
    //var apply_course = $("#inputTitle").val();
    var inputTitle = $("#inputTitle").val();
    var inputFullname = $("#inputFullname").val();
    var inputCountryBirth = $("#inputCountryBirth").val();
    var inputInitials = $("#inputInitials").val();
    var inputDob = $("#inputDob").val();
    var inputGender = $("#inputGender").val();
    var citizenship_type = $("#citizenship_type").val();
    var inputCivilSts = $("#inputCivilSts").val();
    //var citizenship_type = $("#citizenship_type").val();
    var addressPermanent = $("#addressPermanent").val();
    var inputEmailAddress = $("#inputEmailAddress").val();
    var docupldlink = $("#docupldlink").val();
    var periodStudy = $("#periodStudy").val();
    var elegibleState = $("#elegibleState").val();
    var otherQualifications = $("#otherQualifications").val();
    var fund = $("#fund").val();
    var inputCitizenship = $("#inputCitizenship").val();
    var inputCitizenship1 = $("#inputCitizenship1").val();
    var inputCitizenship2 = $("#inputCitizenship2").val();
    var countryAL = $("#countryAL").val();
    //var eduAgent = $("#passportno").val();
    //var eduAgent = $("#passportno").val();
    console.log('Click save btn1', passportno);
    $.ajax({
      type: "POST",
      url: "../pages/formsave.php",
      data: {
        passportno: passportno,
        inputCourse: inputCourse,
        inputTitle: inputTitle,
        inputFullname: inputFullname,
        inputCountryBirth: inputCountryBirth,
        inputInitials: inputInitials,
        inputDob: inputDob,
        inputGender: inputGender,
        citizenship_type: citizenship_type,
        inputCivilSts: inputCivilSts,
        addressPermanent: addressPermanent,
        inputEmailAddress: inputEmailAddress,
        docupldlink: docupldlink,
        periodStudy: periodStudy,
        elegibleState: elegibleState,
        otherQualifications: otherQualifications,
        fund: fund,
        inputCitizenship: inputCitizenship,
        inputCitizenship1: inputCitizenship1,
        inputCitizenship2: inputCitizenship2,
        countryAL: countryAL,

        
      },
      dataType: "json",
      contentType: false, // Important: Set contentType to false when using FormData
      processData: false, // Important: Set processData to false when using FormData
      success: function (response) {
        if (response.status === "success") {
          toastr.success("Saved successfully", "");
          window.location.href = "dashboard.php";
        } else {
          console.log(response);
          //toastr.error("Invalid username or password.", "Error");
        }
      },
      error: function (error) {
        console.log(error);
        alert("An error occurred. Please try again.");
      },
    });
  });

});

function previewImage(event) {
    var input = event.target;
    var reader = new FileReader();
    reader.onload = function(){
        var dataURL = reader.result;
        var output = document.getElementById('wizardPicturePreview');
        output.src = dataURL;
    };
    reader.readAsDataURL(input.files[0]);
}


function validateStep(step) {
    var isValid = true;
    console.log(step);
 
    if (step === 1) {
        if (!$('#passportno').val().trim()) {
            toastr.error("Please enter Passport Number", '', { timeOut: 1000,});
            //showToastMessage("Please enter Passport Number");
            isValid = false;
            return;
        }else{
            var passportno = $("#passportno").val();
            //isValid = true; 
            $.ajax({
                url: 'data/check_passport.php', // The URL of the PHP file that checks the passport number
                type: 'POST',
                async: false,
                data: { passportno: passportno },
                success: function(response) {
                    console.log(response[0]);
                    if (response[0] === 'exist') {
                        toastr.error("Passport number already exists.", '', { timeOut: 1000,});
                        isValid = false;
                        //header('Location:applicationstatus.php?idn='.$enc_nic_no);
                        return;
                    }else{
                        isValid = true; 
                    }
                },
                error: function() {
                    toastr.error("Error while checking passport number. Please try again later.", '', { timeOut: 1000,});
                    isValid = false;
                    return;
                }
            });
            //console.log('isValid ff:',isValid);
            
        }
    } else if (step === 2) {
        if ($('#inputCourse').val() === "select degree") {
            toastr.error("Please select a degree", '', { timeOut: 1000,});
            isValid = false;
            return;
        }
    } else if (step === 3) {
        if (!$('#inputInitials').val().trim()) {
            toastr.error("Please enter Name with Initial", '', { timeOut: 1000,});
            isValid = false;
            return;
        }
        if (!$('#inputFullname').val().trim()) {
            toastr.error("Please enter Full Name", '', { timeOut: 1000,});
            isValid = false;
            return;
        }
        if (!$('#inputDob').val().trim()) {
            toastr.error("Please enter DoB", '', { timeOut: 1000,});
            isValid = false;
            return;
        }
        if ($('#inputGender').val() === "Select Gender") {
            toastr.error("Please select Gender", '', { timeOut: 1000,});
            isValid = false;
            return;
        }
        if ($('#inputCivilSts').val() === "Select Civil Status") {
            toastr.error("Please select Civil Ststus", '', { timeOut: 1000,});
            isValid = false;
            return;
        }
        if (!$('#inputEmailAddress').val().trim()) {
            toastr.error("Please enter Email address", '', { timeOut: 1000,});
            isValid = false;
            return;
        }
        if (!$('#inputCountryBirth').val().trim()) {
            toastr.error("Please enter Birth country", '', { timeOut: 1000,});
            isValid = false;
            return;
        }
        if (!$('#periodStudy').val().trim()) {
            toastr.error("Please enter period of study", '', { timeOut: 1000,});
            isValid = false;
            return;
        }
        if (!$('#addressPermanent').val().trim()) {
            toastr.error("Please enter Permanent Address", '', { timeOut: 1000,});
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
        
        /* if (!$('#citizenship_type').val().trim()) {
            showToastMessage("Please enter Name with Initial");
            isValid = false;
        } */

        /* if (!$('#inputCitizenship').val().trim()) {
            showToastMessage("Please enter Name with Initial");
            isValid = false;
        }
        if (!$('#inputCitizenship1').val().trim()) {
            showToastMessage("Please enter Name with Initial");
            isValid = false;
        }
        if (!$('#inputCitizenship2').val().trim()) {
            showToastMessage("Please enter Name with Initial");
            isValid = false;
        } */

    } else if (step === 4) {
        if ($('#elegibleState').val() === "Please Select") {
            toastr.error("Please select eligibility", '', { timeOut: 1000,});
            isValid = false;
        }
    } else if (step === 6) {
        if (!$('#fatherName').val().trim()) {
            toastr.error("Please enter Father's Name", '', { timeOut: 1000,});
            isValid = false;
            return;
        }if (!$('#fatherMobileNo').val().trim()) {
            toastr.error("Please enter Father's Mobile No", '', { timeOut: 1000,});
            isValid = false;
            return;
        }if (!$('#motherName').val().trim()) {
            toastr.error("Please enter Mother's Name", '', { timeOut: 1000,});
            isValid = false;
            return;
        }
    } else if (step === 7) {
        if (!$('#refree1_details').val().trim()) {
            toastr.error("Please enter Refree1 details", '', { timeOut: 1000,});
            isValid = false;
            return;
        }if (!$('#refree1_phone').val().trim()) {
            toastr.error("Please enter Refree1 Phone No", '', { timeOut: 1000,});
            isValid = false;
            return;
        }if (!$('#refree2_details').val().trim()) {
            toastr.error("Please enter Refree2 details", '', { timeOut: 1000,});
            isValid = false;
            return;
        }if (!$('#refree2_phone').val().trim()) {
            toastr.error("Please enter Refree2 Phone No", '', { timeOut: 1000,});
            isValid = false;
            return;
        }
    } else if (step === 8) {
        if (!$('#docupldlink').val().trim()) {
            toastr.error("Please enter document upload link", '', { timeOut: 1000,});
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

/* function showToastMessage(message) {
   
    $('.toast-body').text(message);
    $('.toast').toast('show');
} */

/* $('.nextBtn').click(function() {
    
    var currentStep = $(this).closest('.setup-content').attr('id').split('-')[1];
    if (validateStep(parseInt(currentStep))) {
        var nextStep = parseInt(currentStep) + 1;
        $('#step-' + nextStep).fadeIn();
        $('#step-' + currentStep).hide();
    }
}); */




function validateForm(){     

if(document.forms["my-form"]["inputCourse"].value == "" ){
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
if(document.forms["my-form"]["inputDob"].value == "" ){
           Swal.fire({
          icon: 'warning',
          title: 'Missing Data',
          text: 'Please Enter Birth Day!',
         onAfterClose: () => {
            document.forms["my-form"]["inputDob"].focus();
         }
        })
    return false;
}if(document.forms["my-form"]["citizenship_type"].value == "" ){
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
if(document.forms["my-form"]["inputGender"].value == "" ){
           Swal.fire({
          icon: 'warning',
          title: 'Missing Data',
          text: 'Please Select Gender!',
         onAfterClose: () => {
            document.forms["my-form"]["inputGender"].focus();
         }
        })
    return false;
}if(document.forms["my-form"]["inputCivilSts"].value == "" ){
           Swal.fire({
          icon: 'warning',
          title: 'Missing Data',
          text: 'Please Select Status!',
         onAfterClose: () => {
            document.forms["my-form"]["inputCivilSts"].focus();
         }
        })
    return false;
}if(document.forms["my-form"]["refree1_details"].value == "" ){
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
if(document.forms["my-form"]["refree1_phone"].value == "" ){
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
if(document.forms["my-form"]["citizenship_type"].value == "Foreign Citizenship" ){
    if(document.forms["my-form"]["inputCitizenship"].value == "" ){
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
}if(document.forms["my-form"]["citizenship_type"].value == "Dual Citizenship" ){
    if(document.forms["my-form"]["inputCitizenship1"].value == "" ){
           Swal.fire({
          icon: 'warning',
          title: 'Missing Data',
          text: 'Please Enter Dual Citizenship!',
         onAfterClose: () => {
            document.forms["my-form"]["inputCitizenship1"].focus();
         }
        })
        return false;
    }if(document.forms["my-form"]["inputCitizenship2"].value == "" ){
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

var year25 =  moment(endDate, 'MM/DD/YYYY').subtract(30, 'years').format('MM/DD/YYYY');

var year17 =  moment(endDate, 'MM/DD/YYYY').subtract(17, 'years').format('MM/DD/YYYY');

var d_dob = dob.split("/");
var d_25 = year25.split("/");
var d_17 = year17.split("/");


var bday = new Date(d_dob[2], parseInt(d_dob[0])-1, d_dob[1]);  // -1 because months are from 0 to 11
var date25   = new Date(d_25[2], parseInt(d_25[0])-1, d_25[1]);
var date17 = new Date(d_17[2], parseInt(d_17[0])-1, d_17[1]);
if(date17 < bday ){
    Swal.fire({
          icon: 'warning',
          title: 'Age Limit',
          text: 'You are younger than 17!',
         onAfterClose: () => {
            document.forms["my-form"]["inputDob"].focus();
         }
        })
     return false;
}if(bday < date25){
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
console.log('17: '+date17);
console.log('25:'+date25 );
console.log('17 > age: '+date17 > bday );
console.log('age > 25: '+bday > date25 );

}