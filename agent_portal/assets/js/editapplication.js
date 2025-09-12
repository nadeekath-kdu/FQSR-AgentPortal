$(document).ready(function () {
    $('#btn-update').click(function(){
        e.preventDefault(); // Prevent the default button behavior
        var formData = $('#my-form').serialize(); // Serialize form data
        console.log(formData);
    });
});
function updateAction1() {
    document.getElementById("my-form").action = "../pages/formupdate.php";
}

function noUpdateAction1() {
    console.log('noUpdateAction');
    //document.getElementById("my-form").action = "../includes/content/viewapplicationslist.html";
}

function submitAction1() {
    document.getElementById("my-form").action = "https://enlistment.kdu.ac.lk/fqsr/formsave.php?idn=".$enc_nic_no;
}
function validateForm(){
    //alert("radio");     

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


/*  if(document.forms["my-form"]["eduAgent"].value == "" ){ //2022-07-20
    Swal.fire({
            icon: 'warning',
            title: 'Missing Data',
            text: 'Please Enter Education Agent!',
            onAfterClose: () => {
                document.forms["my-form"]["eduAgent"].focus();
            }
            })
        return false;
}else{
    if(document.forms["my-form"]["eduAgent"].value == "Yes" ){
        if(document.forms["my-form"]["nameEduAgent"].value == "" ){
            Swal.fire({
            icon: 'warning',
            title: 'Missing Data',
            text: 'Please Enter Name of education agent!',
            onAfterClose: () => {
                document.forms["my-form"]["nameEduAgent"].focus();
            }
            })
        return false;
    }
}
} */

var closingDate = document.forms["my-form"]["closingDate"].value;
//alert('closing date:'+closingDate);
var dob1 = document.forms["my-form"]["inputDob"].value;
//alert('dob'+dob1);
var dob = moment(dob1, 'YYYY-MM-DD').format('MM/DD/YYYY');
var endDate = moment(closingDate, 'YYYY-MM-DD').format('MM/DD/YYYY');
//console.log(dob);

var year25 =  moment(endDate, 'MM/DD/YYYY').subtract(25, 'years').format('MM/DD/YYYY');

var year17 =  moment(endDate, 'MM/DD/YYYY').subtract(17, 'years').format('MM/DD/YYYY');
   //console.log('25 year :' + year25);
    //console.log('17 year :' + year17);
var d_dob = dob.split("/");
var d_25 = year25.split("/");
var d_17 = year17.split("/");


var bday = new Date(d_dob[2], parseInt(d_dob[0])-1, d_dob[1]);  // -1 because months are from 0 to 11
var date25   = new Date(d_25[2], parseInt(d_25[0])-1, d_25[1]);
var date17 = new Date(d_17[2], parseInt(d_17[0])-1, d_17[1]);
//console.log((bday >= date25) && (bday <= date17)) ;
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
          text: 'You are older than 25!',
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


/*var minDate = document.forms["my-form"]["minDate"].value;
var maxDate = document.forms["my-form"]["maxDate"].value;
var dob = document.forms["my-form"]["dob"].value;
if(maxDate < dob ){
    Swal.fire({
          icon: 'warning',
          title: 'Age Limit',
          text: 'You are younger than 17!',
         onAfterClose: () => {
            document.forms["my-form"]["inputDob"].focus();
         }
        })
    return false;
}if(dob < minDate){
    Swal.fire({
          icon: 'warning',
          title: 'Age Limit',
          text: 'You are older than 25!',
         onAfterClose: () => {
            document.forms["my-form"]["inputDob"].focus();
         }
        })
    return false;
}
*/
}

function validateDate(val) {
//console.log(val);
var dob = moment(val, 'YYYY-MM-DD').format('MM/DD/YYYY');
var closingDate = document.forms["my-form"]["closingDate"].value;
//alert('closing date:'+closingDate);
var endDate = moment(closingDate, 'YYYY-MM-DD').format('MM/DD/YYYY');
//console.log(dob);

var year25 =  moment(endDate, 'MM/DD/YYYY').subtract(25, 'years').format('MM/DD/YYYY');

var year17 =  moment(endDate, 'MM/DD/YYYY').subtract(17, 'years').format('MM/DD/YYYY');
    //console.log('17 year :' + year17);
var d_dob = dob.split("/");
var d_25 = year25.split("/");
var d_17 = year17.split("/");


var bday = new Date(d_dob[2], parseInt(d_dob[0])-1, d_dob[1]);  // -1 because months are from 0 to 11
var date25   = new Date(d_25[2], parseInt(d_25[0])-1, d_25[1]);
var date17 = new Date(d_17[2], parseInt(d_17[0])-1, d_17[1]);
//console.log((bday >= date25) && (bday <= date17)) ;
if(date17 < bday ){
    Swal.fire({
          icon: 'warning',
          title: 'Age Limit',
          text: 'You are younger than 17!',
         onAfterClose: () => {
            document.forms["my-form"]["inputDob"].focus();
         }
        })
}if(bday < date25){
    Swal.fire({
          icon: 'warning',
          title: 'Age Limit',
          text: 'You are older than 25!',
         onAfterClose: () => {
            document.forms["my-form"]["inputDob"].focus();
         }
        })
}
$("#minDate").val(date25);
$("#maxDate").val(date17);
$("#dob").val(bday);
console.log('17: '+date17);
console.log('25:'+date25 );
console.log('17 > age: '+date17 > bday );
console.log('age > 25: '+bday > date25 );


}
$(document).ready(function(){
// Prepare the preview for profile picture
$("#Photo").change(function(){
    readURL(this);
});
});
function readURL(input) {
if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function (e) {
        $('#wizardPicturePreview').attr('src', e.target.result).fadeIn('slow');
    }
    reader.readAsDataURL(input.files[0]);
}
}
$(document).ready(function(){

if(document.forms["my-form"]["citizenship_type"].value == "Sri Lankan Citizenship Only"){

document.getElementById("section1").style.display = 'none';
document.getElementById("section2").style.display = 'none';
document.getElementById("section3").style.display = 'none';
}else if(document.forms["my-form"]["citizenship_type"].value == "Foreign Citizenship"){

document.getElementById("section1").style.display = 'flex';
document.getElementById("section2").style.display = 'none';
document.getElementById("section3").style.display = 'none';
}else if(document.forms["my-form"]["citizenship_type"].value == "Dual Citizenship"){

document.getElementById("section1").style.display = 'none';
document.getElementById("section2").style.display = 'flex';
document.getElementById("section3").style.display = 'flex';
}
//alert(document.forms["my-form"]["inputIntakeYr"].value)
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

$("#eduAgent").change(function(){ //2022-07-20
//alert("A"); 
var ddlFruits = document.getElementById("eduAgent");
    if (ddlFruits.value == "Yes") {
        document.getElementById("section4").style.display = 'flex';
    }if (ddlFruits.value == "No") {
        document.getElementById("section4").style.display = 'none';
        $("#nameEduAgent").val("");
    }

//$("#inputCitizenship").val("");

});

});