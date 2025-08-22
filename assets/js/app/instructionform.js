$(document).ready(function () {

    $("#next").click(function (event) {
        event.preventDefault();
        console.log('click next');
        var page = "../content/application.html";
        $("#content").load(page);
    });

});