$(document).ready(function () {
    // Load homepage content on initial load from existing includes/content folder
    $("#content").load("content/homepage.php");

    // Set agentCode from server-side (should be rendered in the page by PHP)
    var agentCode = window.agentCode || '';

    // Main menu handlers
    $("#newapp").click(function (event) {
        event.preventDefault();
        console.log('click newapp - agent code:', agentCode);
        // Open index.php in the main folder in a new tab, passing agent_code if available
        var url = '../../index.php';
        if (agentCode) {
            url += '?agent_code=' + encodeURIComponent(agentCode);
        }
        window.open(url, '_blank');
    });

    $("#viewapp").click(function (event) {
        event.preventDefault();
        console.log('click viewapp');
        var page = "content/viewappdatalist.php";
        $("#content").load(page);
        updateActiveMenu($(this));
    });

    $("#pendingapp").click(function (event) {
        event.preventDefault();
        console.log('click pending applications');
        // Load pending applications page
        $("#content").html('<div class="alert alert-info"><i class="bx bx-time-five me-2"></i>Loading pending applications...</div>');
        updateActiveMenu($(this));
    });

    $("#approvedapp").click(function (event) {
        event.preventDefault();
        console.log('click approved applications');
        // Load approved applications page
        $("#content").html('<div class="alert alert-success"><i class="bx bx-check-circle me-2"></i>Loading approved applications...</div>');
        updateActiveMenu($(this));
    });

    $("#reports").click(function (event) {
        event.preventDefault();
        console.log('click reports');
        $("#content").html('<div class="alert alert-info"><i class="bx bx-bar-chart-alt-2 me-2"></i>Application reports feature coming soon!</div>');
        updateActiveMenu($(this));
    });

    $("#analytics").click(function (event) {
        event.preventDefault();
        console.log('click analytics');
        $("#content").html('<div class="alert alert-info"><i class="bx bx-line-chart me-2"></i>Performance analytics feature coming soon!</div>');
        updateActiveMenu($(this));
    });

    $("#profile").click(function (event) {
        event.preventDefault();
        console.log('click profile');
        $("#content").html('<div class="alert alert-info"><i class="bx bx-user me-2"></i>Agency profile management coming soon!</div>');
        updateActiveMenu($(this));
    });

    $("#students").click(function (event) {
        event.preventDefault();
        console.log('click students');
        $("#content").html('<div class="alert alert-info"><i class="bx bx-group me-2"></i>Student database feature coming soon!</div>');
        updateActiveMenu($(this));
    });

    $("#notifications").click(function (event) {
        event.preventDefault();
        console.log('click notifications');
        $("#content").html('<div class="alert alert-info"><i class="bx bx-bell me-2"></i>Notification settings coming soon!</div>');
        updateActiveMenu($(this));
    });

    $("#help").click(function (event) {
        event.preventDefault();
        console.log('click help');
        $("#content").html('<div class="alert alert-info"><i class="bx bx-help-circle me-2"></i>Help and support documentation coming soon!</div>');
        updateActiveMenu($(this));
    });

    $("#changepw").click(function (event) {
        event.preventDefault();
        console.log('click change password');
        $("#content").load("content/changepassword.html");
        updateActiveMenu($(this));
    });
    
    $("#logout, #logout-menu").click(function (event) {
        event.preventDefault();
        console.log('click logout');
        // Show confirmation dialog
        Swal.fire({
            title: 'Are you sure?',
            text: "You will be logged out of the system",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, logout'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "../index.php";
            }
        });
    });
    
    // Top navbar menu handlers
    $("#profile-menu").click(function (event) {
        event.preventDefault();
        $("#profile").click();
    });
    
    $("#settings-menu").click(function (event) {
        event.preventDefault();
        $("#changepw").click();
    });
    
    // Dashboard home click
    $('a[href="dashboard.php"]').click(function (event) {
        event.preventDefault();
        $("#content").load("content/homepage.php");
        updateActiveMenu($(this));
    });    // Function to update active menu item
    function updateActiveMenu(clickedItem) {
        // Remove active class from all menu items
        $('.menu-item').removeClass('active');
        // Add active class to the clicked item's parent
        clickedItem.closest('.menu-item').addClass('active');
    }

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Search functionality
    $('input[placeholder="Search applications..."]').on('keyup', function () {
        var searchTerm = $(this).val();
        if (searchTerm.length > 2) {
            console.log('Searching for:', searchTerm);
            // Implement search functionality here
        }
    });
});