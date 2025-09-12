$(document).ready(function(){
    var user_role = document.forms["my-form"]["user_role"].value;
  console.log(user_role);
  $("#groups").change(function(){
    
    if(user_role === 'FRO'){
      var approval = document.forms["my-form"]["review"].value;
      if(approval == 'NOTREVIEWED' ){
        document.getElementById("review-section").style.display = 'flex';
        document.getElementById("section2").style.display = 'none';
      }else if(approval == "Hold And Ask for Re-submit" || approval == "HOLD"){
          
      document.getElementById("section2").style.display = 'flex';
      document.getElementById("review-section").style.display = 'none';
    }else{
        document.getElementById("section2").style.display = 'none';
        document.getElementById("review-section").style.display = 'none';
    }
    }else if(user_role == 'DR'){
        var approval = document.forms["my-form"]["verification"].value;
          if(approval == 'REJECT' ){
            document.getElementById("section1").style.display = 'flex';
            //document.getElementById("section2").style.display = 'none';
        }else if(approval == 'VERIFIED' ){
            document.getElementById("section1").style.display = 'none';
            //document.getElementById("section2").style.display = 'none';
        }
      }
    else if(user_role == 'DVC'){
      var approval = document.forms["my-form"]["recommendation"].value;
      if(approval == 'NOTRECOMMENDED' ){
          document.getElementById("section3").style.display = 'flex';
      }else{
        
          document.getElementById("section3").style.display = 'none';
      }
    }else if(user_role == 'VC'){
      var approval = document.forms["my-form"]["approval"].value;
      if(approval == 'NOTAPPROVED' ){
          document.getElementById("section4").style.display = 'flex';
      }else{
        
          document.getElementById("section4").style.display = 'none';
      }
    }else{
      
        //document.getElementById("section1").style.display = 'none';
        //document.getElementById("section2").style.display = 'none';
    }
  }); 
  

    $('#my-form').on('submit', function(e) {
        e.preventDefault();
        if (!validateForm()) { 
          return; 
        }
        var formData = $('#my-form').serialize(); 
      console.log('submit',formData);
        $.ajax({
            url: '../pages/updateagencystatus.php',
            type: 'POST',
            data: formData, 
            success: function(response) {
              console.log('Success:', response);
                if(response.success) {
                  toastr.success("Update successfully", "");
                  if(response.status == 'APPROVED') {
                    window.location.href = "../pages/agent_register_success.php/code="+response.code;
                  }
                  var page = "../content/viewpendinglist.html";
                  $("#content").load(page);
                 
                } else {
                    console.error('Update failed.');
                } 
            },
            error: function(xhr, status, error) {
                console.error('AJAX request failed:', error);
            }
        });
    });


});
function validateForm(){
  var isValid = true;
  var user_role = document.forms["my-form"]["user_role"].value;
      //alert("radio"+user_role);   
      if(user_role === 'FRO'){  
          if(document.forms["my-form"]["review"].value == "" ){
            Swal.fire({
          icon: 'warning',
          title: 'Missing Data',
          text: 'Please Select review!',
          onAfterClose: () => {
            document.forms["my-form"]["review"].focus();
          }
        })
        isValid = false;
    return false;
    }
        if(document.forms["my-form"]["review"].value == "NOTREVIEWED" ){
        if(document.forms["my-form"]["review_comments"].value == "" ){
                Swal.fire({
              icon: 'warning',
              title: 'Missing Data',
              text: 'Please Enter Reject Reason!',
              onAfterClose: () => {
                document.forms["my-form"]["review_comments"].focus();
              }
            })
            isValid = false;
        return false;
          }
        }
      
      if(document.forms["my-form"]["review"].value == "HOLD" ){
        if(document.forms["my-form"]["reCallingReason"].value == "" ){
                Swal.fire({
                icon: 'warning',
                title: 'Missing Data',
                text: 'Please Enter Required Details!',
              onAfterClose: () => {
                  document.forms["my-form"]["reCallingReason"].focus();
              }
              })
              isValid = false;
          return false;
            }
      }  
}else if(user_role === 'DR'){
  if(document.forms["my-form"]["verification"].value == "" ){
             Swal.fire({
            icon: 'warning',
            title: 'Missing Data',
            text: 'Please Select verification!',
           onAfterClose: () => {
              document.forms["my-form"]["verification"].focus();
           }
          })
          isValid = false;
      return false;
  }
  if(document.forms["my-form"]["verification"].value == "REJECT" ){
    if(document.forms["my-form"]["remark_dr"].value == "" ){
             Swal.fire({
            icon: 'warning',
            title: 'Missing Data',
            text: 'Please Enter Reject Reason!',
           onAfterClose: () => {
              document.forms["my-form"]["remark_dr"].focus();
           }
          })
          isValid = false;
      return false;
        }
  }
}else if(user_role === 'DVC'){
  if(document.forms["my-form"]["recommendation"].value == "" ){
    Swal.fire({
   icon: 'warning',
   title: 'Missing Data',
   text: 'Please Select recommendation!',
  onAfterClose: () => {
     document.forms["my-form"]["recommendation"].focus();
  }
 })
 isValid = false;
return false;
}
if(document.forms["my-form"]["recommendation"].value == "NOTRECOMMENDED" ){
if(document.forms["my-form"]["remark_dvc"].value == "" ){
    Swal.fire({
   icon: 'warning',
   title: 'Missing Data',
   text: 'Please Enter Reason!',
  onAfterClose: () => {
     document.forms["my-form"]["remark_dvc"].focus();
  }
 })
 isValid = false;
return false;
}
}
}else if(user_role === 'VC'){
if(document.forms["my-form"]["approval"].value == "" ){
  Swal.fire({
 icon: 'warning',
 title: 'Missing Data',
 text: 'Please Select approval!',
onAfterClose: () => {
   document.forms["my-form"]["approval"].focus();
}
})
isValid = false;
return false;
}
if(document.forms["my-form"]["approval"].value == "NOTAPPROVED" ){
if(document.forms["my-form"]["remark_vc"].value == "" ){
  Swal.fire({
 icon: 'warning',
 title: 'Missing Data',
 text: 'Please Enter Reason!',
onAfterClose: () => {
   document.forms["my-form"]["remark_vc"].focus();
}
})
isValid = false;
return false;
}
}
} 
return isValid;

}