$(document).ready(function () {
    // Get academic year
    $.ajax({
        url: '../data/get_academic_year.php',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.academic_year) {
                $('#academicYear').text(response.academic_year);
            } else {
                console.error('No academic year received');
            }
        },
        error: function (xhr, status, error) {
            console.error('Error fetching academic year:', error);
        }
    });

    $("#next").click(function (event) {
        event.preventDefault();

        // Check closing date first
        $.ajax({
            url: '../data/get_closing_date.php',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.closing_date) {
                    const closingDate = new Date(response.closing_date);
                    const today = new Date();

                    if (today > closingDate) {
                        toastr.error("The application submission deadline has passed.", "Application Closed", { timeOut: 3000 });
                    } else {
                        var page = "../content/application.html";
                        $("#content").load(page);
                    }
                } else {
                    //console.error('No closing date received from server');
                    toastr.error("Could not verify application deadline. Please try again later.", "Error", { timeOut: 3000 });
                }
            },
            error: function (xhr, status, error) {
                console.error('Error fetching closing date:', error);
                toastr.error("Could not verify application deadline. Please try again later.", "Error", { timeOut: 3000 });
            }
        });
    });

});