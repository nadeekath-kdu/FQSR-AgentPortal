$(document).ready(function() {
    function fetchApplicationCount() {
    var url = "../includes/data/get_unsubmitted_app_count.php";

    $.ajax({
        url: url,
        type: "GET",
        dataType: "json", 
        success: function(data) {
            $('#application-count').text(data[0].total);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error(textStatus, errorThrown);
        }
    });
}
function fetchClosingDate() {
    var url = "../includes/data/get_closing_date.php";

    $.ajax({
        url: url,
        type: "GET",
        dataType: "json", 
        success: function(data) {
           $('#closing-date').text(data.closing_date);
            console.log(data);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error(textStatus, errorThrown);
        }
    });
}

fetchApplicationCount();
fetchClosingDate();
//window.onload = fetchApplicationCount();
});
