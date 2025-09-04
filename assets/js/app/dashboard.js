$(document).ready(function () {
    // Get academic year using async AJAX
    $.ajax({
        url: '../data/get_academic_year.php',
        type: 'GET',
        dataType: 'json',
        async: true,
        cache: false,
        success: function (response) {
            if (response && response.academic_year) {
                $('#academicYear').val(response.academic_year).text(response.academic_year);
            } else {
                const currentYear = new Date().getFullYear();
                const fallbackYear = currentYear + '/' + (currentYear + 1);
                $('#academicYear').val(fallbackYear).text(fallbackYear);
            }
        },
        error: function () {
            const currentYear = new Date().getFullYear();
            const fallbackYear = currentYear + '/' + (currentYear + 1);
            $('#academicYear').val(fallbackYear).text(fallbackYear);
        }
    });
    $("#content").load("../content/instructionform.html");

    //$("#dashboard").load("../content/dashboard.html");
});