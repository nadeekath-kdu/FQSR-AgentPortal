$(document).ready(function () {
    $('.btn-update').click(function(e){
        e.preventDefault(); 
        var formData = $('#my-form').serialize(); 
        $.ajax({
            url: '../pages/formupdate.php', 
            type: 'POST',
            data: formData,
            success: function(response) {
                toastr.success("Saved successfully", "");
                //window.location.href = "dashboard.php";
                var page = "content/viewapplicationslist.html";
                $("#content").load(page);
            },
            error: function(xhr, status, error) {
                toastr.error("Error sending form data:.", "Error");
            }
        });
        
    });

    $('.btn-submit').click(function(e){
        e.preventDefault(); 
        var formData = $('#my-form').serialize(); 
        $.ajax({
            url: 'https://enlistment.kdu.ac.lk/fqsr/formsave.php?idn='.$enc_nic_no, 
            type: 'POST',
            data: formData,
            success: function(response) {
                toastr.success("Saved successfully", "");
                var page = "content/viewappdatalist.html";
                $("#content").load(page);
            },
            error: function(xhr, status, error) {
                toastr.error("Error sending form data:.", "Error");
            }
        });
        
    });

    $(".btn-cancel" ).on( "click", function(e) {
        e.preventDefault(); 
        console.log('cance, no update');
        var page = "content/viewappdatalist.html";
        $("#content").load(page);
    });

});