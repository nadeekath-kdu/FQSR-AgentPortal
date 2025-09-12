$(document).ready(function() {
    $("#content").load("../content/viewagencylist.html");
    
    $("#agencies").click(function(event) {
        event.preventDefault();
        console.log('click agencies');
        var page = "../content/viewagencylist.html";
        $("#content").load(page);
    });
    $("#applications").click(function(event) {
        event.preventDefault();
        console.log('click applications');
        var page = "../content/viewappdatalist.html";
        $("#content").load(page);
    });
    $("#pendinglist").click(function(event) {
        event.preventDefault();
        console.log('click pendinglist');
        //var page = "content/viewapplicationslist.html";
        var page = "../content/viewpendinglist.html";
        $("#content").load(page);
    });
    $("#logout").click(function(event) {
        event.preventDefault();
        console.log('click logout');
        window.location.href = "../login.html";
    });

});