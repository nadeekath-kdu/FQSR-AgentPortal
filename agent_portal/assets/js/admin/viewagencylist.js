$(document).ready(function () {
    var user = "";
    var dataTable = null;
    var allData = [];
    var currentUserRole = ""; // Store current user's role

    // Get current user role from session/server
    function getCurrentUserRole() {
        return currentUserRole || user || 'FRO'; // Fallback to user or FRO
    }

    // Helper function to get role code from role name
    function getRoleCode(roleName) {
        const roleMap = {
            'Foreign Relations Officer': 'FRO',
            'Deputy Registrar': 'DR',
            'Deputy Vice Chancellor': 'DVC',
            'Vice Chancellor': 'VC'
        };
        return roleMap[roleName] || '';
    }

    // Function to get role display name
    function getRoleDisplayName(roleCode) {
        const roleNames = {
            'FRO': 'Foreign Relations Officer',
            'DR': 'Deputy Registrar',
            'DVC': 'Deputy Vice Chancellor',
            'VC': 'Vice Chancellor'
        };
        return roleNames[roleCode] || roleCode;
    }

    $.ajax({
        url: '../data/get_user.php',
        type: 'GET',
        success: function (response) {
            //console.log('user:', response);
            user = response.user;
            currentUserRole = response.user; // Set the current user role
            //console.log('user2:', user);
            getdata(user);
            setUserRoleLabel(); // Set the user role label
        },
        error: function (xhr, status, error) {
            console.error('AJAX request failed:', error);
        }
    });

    // Set the user role label
    function setUserRoleLabel() {
        const roleDisplayName = getRoleDisplayName(getCurrentUserRole());
        $('#userRoleLabel').text(roleDisplayName);
    }

    // Initialize filters
    function initializeFilters() {
        // Populate country filter
        const countries = [...new Set(allData.map(item => item.country))].sort();
        const countrySelect = $('#countryFilter');
        countrySelect.empty().append('<option value="">All Countries</option>');
        countries.forEach(country => {
            if (country) {
                countrySelect.append(`<option value="${country}">${country}</option>`);
            }
        });

        // Update statistics with role-based calculations
        updateStatistics();

        // Set up filter change handlers
        $('#statusFilter, #countryFilter, #mailFilter, #levelFilter').off('change').on('change', function () {
            applyFilters();
            updateStatistics();
        });
    }

    // Approval workflow helper functions
    function getOverallStatus(row) {
        // Check if any level rejected/not approved
        if (row.status_fro === 'NOTREVIEWED' || row.status_fro === 'HOLD' ||
            row.status_dr === 'REJECT' ||
            row.status_dvc === 'NOTRECOMMENDED' ||
            row.status_vc === 'NOTAPPROVED') {
            return 'REJECTED';
        }

        // Check if fully approved (VC has approved)
        if (row.status_vc === 'APPROVED') {
            return 'APPROVED';
        }

        return 'PENDING';
    }

    function getNextPendingRole(row) {
        const levels = [
            { key: 'status_fro', name: 'Foreign Relations Officer', approved: 'REVIEWED', rejected: ['NOTREVIEWED', 'HOLD'] },
            { key: 'status_dr', name: 'Deputy Registrar', approved: 'VERIFIED', rejected: ['REJECT'] },
            { key: 'status_dvc', name: 'Deputy Vice Chancellor', approved: 'RECOMMENDED', rejected: ['NOTRECOMMENDED'] },
            { key: 'status_vc', name: 'Vice Chancellor', approved: 'APPROVED', rejected: ['NOTAPPROVED'] }
        ];

        for (let level of levels) {
            const status = row[level.key];

            // If status is empty, null, or undefined - this level is pending
            if (!status || status === '') {
                return level.name;
            }

            // If rejected at this level
            if (level.rejected.includes(status)) {
                return `Rejected by ${level.name}`;
            }

            // If not approved yet, continue to next level
            if (status !== level.approved) {
                return level.name;
            }
        }

        return 'Completed';
    }

    function getApprovalProgress(row) {
        const levels = [
            { key: 'status_fro', approved: 'REVIEWED' },
            { key: 'status_dr', approved: 'VERIFIED' },
            { key: 'status_dvc', approved: 'RECOMMENDED' },
            { key: 'status_vc', approved: 'APPROVED' }
        ];
        let approved = 0;

        for (let level of levels) {
            if (row[level.key] === level.approved) {
                approved++;
            } else {
                break; // Stop at first non-approved level
            }
        }

        return (approved / levels.length) * 100;
    }

    function getRejectedBy(row) {
        const levels = [
            { key: 'status_fro', name: 'Foreign Relations Officer', rejected: ['NOTREVIEWED', 'HOLD'] },
            { key: 'status_dr', name: 'Deputy Registrar', rejected: ['REJECT'] },
            { key: 'status_dvc', name: 'Deputy Vice Chancellor', rejected: ['NOTRECOMMENDED'] },
            { key: 'status_vc', name: 'Vice Chancellor', rejected: ['NOTAPPROVED'] }
        ];

        for (let level of levels) {
            if (level.rejected.includes(row[level.key])) {
                return level.name;
            }
        }

        return 'Unknown';
    }

    // Get total agencies count from database (unfiltered)
    function getTotalAgenciesCount() {
        $.ajax({
            url: '../data/get_total_agencies_count.php',
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                console.log('Total agencies response:', response); // Debug log
                if (response.success && response.total !== undefined) {
                    $('#totalAgencies').text(response.total);
                } else {
                    console.warn('Invalid response format:', response);
                    // Fallback to current data length if endpoint doesn't exist
                    $('#totalAgencies').text(allData.length);
                }
            },
            error: function (xhr, status, error) {
                console.error('Failed to get total agencies count:', error, xhr.responseText);
                // Fallback to current data length if endpoint fails
                $('#totalAgencies').text(allData.length);
            }
        });
    }

    // Update statistics cards with role-based pending counts
    function updateStatistics() {
        const total = allData.length;
        let approved = 0;
        let pending = 0;
        let rejected = 0;
        let userPending = 0; // Pending for current user's level
        let overallPending = 0; // Overall pending (all levels without rejections)

        const currentRole = getCurrentUserRole();

        // Get the true total count of all agencies (not just current data)
        getTotalAgenciesCount();

        allData.forEach(row => {
            const status = getOverallStatus(row);

            switch (status) {
                case 'APPROVED':
                    approved++;
                    break;
                case 'PENDING':
                    pending++;

                    // Check if pending at current user's level
                    const nextRole = getNextPendingRole(row);
                    const nextRoleCode = getRoleCode(nextRole);

                    if (nextRoleCode === currentRole) {
                        userPending++;
                    }

                    // For overall pending: count only if no rejections and VC status is pending or empty
                    const hasRejection = (
                        row.status_fro === 'NOTREVIEWED' ||
                        row.status_fro === 'HOLD' ||
                        row.status_dr === 'REJECT' ||
                        row.status_dvc === 'NOTRECOMMENDED' ||
                        row.status_vc === 'NOTAPPROVED'
                    );

                    const vcIsPendingOrEmpty = (
                        !row.status_vc ||
                        row.status_vc === ''
                        //||
                        // row.status_vc !== 'APPROVED'
                    );

                    if (!hasRejection && vcIsPendingOrEmpty) {
                        overallPending++;
                    }

                    break;
                case 'REJECTED':
                    rejected++;
                    break;
            }
        });

        const emailsSent = allData.filter(item => item.ismailgenerate === 'Sent').length;

        // $('#totalAgencies').text(total);
        $('#approvedAgencies').text(approved);
        $('#pendingAgencies').text(pending);
        $('#rejectedAgencies').text(rejected);
        $('#emailsSent').text(emailsSent);
        $('#userPending').text(userPending); // New card for user's pending
        $('#overallPending').text(overallPending); // Updated overall pending

        // Highlight user's pending card if they have actions
        if (userPending > 0) {
            $('.user-pending-card').addClass('user-has-pending');
        } else {
            $('.user-pending-card').removeClass('user-has-pending');
        }

        $('#pendingActions').text(userPending);
        $('#approvedAgencies').text(approved);
        $('#pendingAgencies').text(pending);
        $('#emailsSent').text(emailsSent);
        $('#tableCount').text(`${total} agencies`);
    }

    // Global functions for buttons
    window.refreshTable = function () {
        if (dataTable) {
            dataTable.ajax.reload();
        } else {
            getdata(user);
        }
    };

    window.exportTable = function () {
        if (dataTable) {
            // Create CSV export
            const csvContent = dataTable.buttons.exportData({
                columns: [0, 1, 2, 3, 4, 5, 6]
            });
            // Implementation for CSV export would go here
            alert('Export functionality - CSV download would be implemented here');
        }
    };

    window.clearFilters = function () {
        $('#statusFilter').val('');
        $('#countryFilter').val('');
        $('#mailFilter').val('');
        $('#levelFilter').val('');
        if (dataTable) {
            dataTable.search('').columns().search('').draw();
        }
    };

    window.applyFilters = function () {
        if (!dataTable) return;

        const statusFilter = $('#statusFilter').val();
        const countryFilter = $('#countryFilter').val();
        const mailFilter = $('#mailFilter').val();
        const levelFilter = $('#levelFilter').val();

        // Clear previous custom search
        $.fn.dataTable.ext.search.pop();

        // Add custom filter function
        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            const row = allData[dataIndex];

            // Status filter
            if (statusFilter) {
                const overallStatus = getOverallStatus(row);
                if (overallStatus !== statusFilter) return false;
            }

            // Country filter
            if (countryFilter && row.country !== countryFilter) {
                return false;
            }

            // Mail filter
            if (mailFilter && row.ismailgenerate !== mailFilter) {
                return false;
            }

            // Level filter (pending at specific level)
            if (levelFilter) {
                const nextRole = getNextPendingRole(row);
                const nextRoleCode = getRoleCode(nextRole);

                if (levelFilter === 'MY_LEVEL') {
                    // Show only applications pending at current user's level
                    const currentRole = getCurrentUserRole();
                    if (nextRoleCode !== currentRole) {
                        return false;
                    }
                } else {
                    // Show applications pending at specific level
                    const levelNames = {
                        'FRO': 'Foreign Relations Officer',
                        'DR': 'Deputy Registrar',
                        'DVC': 'Deputy Vice Chancellor',
                        'VC': 'Vice Chancellor'
                    };

                    if (nextRole !== levelNames[levelFilter]) {
                        return false;
                    }
                }
            }

            return true;
        });

        dataTable.draw();
    };

    $(document).on('click', '.emailapp', function (event) {
        event.preventDefault();
        var code = $(this).data('code');
        //console.log('click emailapp', code);
        $.ajax({
            url: '../pages/agent_register_success.php?code=' + code,
            type: 'POST',
            success: function (response) {
                if (response.status === 1) {
                    toastr.success("Email sent successfully", "");
                    //console.log('response:', response);
                    var page = "viewagencylist.html";
                    $("#content").load(page);
                }

            },
            error: function (xhr, status, error) {
                console.error('AJAX request failed:', error);
                toastr.error("Email not sent", "");
            }
        });

    });
    $(document).on('click', '.viewapp', function (event) {
        event.preventDefault();
        var code = $(this).data('code');
        //console.log('click viewapp', code);
        var page = "../content/viewagencydt.php?agency_code=" + code;
        $("#content").load(page);
    });

    // Handle "My Pending" card click to load pending items page
    $(document).on('click', '.user-pending-card', function (event) {
        event.preventDefault();

        const currentRole = getCurrentUserRole();
        const roleDisplayName = getRoleDisplayName(currentRole);

        // Load the pending list page
        var page = "../content/viewpendinglist.html";
        $("#content").load(page, function (response, status, xhr) {
            if (status === "success") {
                // Show success message
                if (typeof toastr !== 'undefined') {
                    toastr.success(`Loading your pending approvals (${roleDisplayName})`, "Pending List");
                }
                console.log('✅ Pending list loaded successfully');
            } else {
                // Show error message
                if (typeof toastr !== 'undefined') {
                    toastr.error('Failed to load pending list', "Error");
                }
                console.error('❌ Failed to load pending list:', xhr.status, xhr.statusText);
            }
        });

        // Add visual feedback
        $('.user-pending-card').addClass('clicked-feedback');
        setTimeout(() => {
            $('.user-pending-card').removeClass('clicked-feedback');
        }, 200);
    });


    function getdata(user) {
        //var approval_status_vc = '';
        $.ajax({
            url: '../data/get_agency_list.php',
            method: 'GET',
            success: function (data) {
                //console.log(data);
                allData = data; // Store data globally for filtering

                dataTable = $('#viewtbl').DataTable({
                    searching: true,
                    data: data,
                    destroy: true,
                    responsive: true,
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    order: [[0, 'desc']], // Sort by agency code descending
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                        '<"row"<"col-sm-12"tr>>' +
                        '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                    language: {
                        search: "Search agencies:",
                        searchPlaceholder: "Type to search...",
                        lengthMenu: "Show _MENU_ agencies per page",
                        info: "Showing _START_ to _END_ of _TOTAL_ agencies",
                        infoEmpty: "No agencies found",
                        infoFiltered: "(filtered from _MAX_ total agencies)",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        }
                    },
                    columnDefs: [
                        {
                            targets: [7], // Actions column
                            orderable: false,
                            searchable: false,
                            className: "text-center"
                        },
                        {
                            targets: [0], // Agency Code
                            className: "text-center fw-bold"
                        },
                        {
                            targets: 1, // Full Name - narrow with ellipsis
                            className: "dt-col-name",
                            width: "16%"
                        },
                        {
                            targets: 2, // Organization - narrow with ellipsis
                            className: "dt-col-org",
                            width: "18%"
                        },
                        {
                            targets: [5], // Status column - compact visuals
                            className: "text-center dt-col-status",
                            width: "18%"
                        },
                        {
                            targets: [6], // Mail Status column
                            className: "text-center"
                        }
                    ],
                    columns: [
                        /*  {
                             data: null,
                             render: function(data, type, row) {
                                 return '<label class="control control--checkbox">' +
                                     '<input type="checkbox"/>' +
                                     '<div class="control__indicator"></div>' +
                                     '</label>';
                             },
                             orderable: false 
                         }, */
                        { data: 'agency_code' },
                        { data: 'fullname' },
                        { data: 'organisation' },
                        { data: 'country' },
                        { data: 'mobile' },
                        /* { data: 'email' }, */
                        {
                            data: null,
                            render: function (data, type, row) {
                                console.log('status5:', row);
                                const overallStatus = getOverallStatus(row);

                                if (overallStatus === 'APPROVED') {
                                    return `
                                        <div class="approval-status compact">
                                            <span class="badge bg-success mb-1">
                                                <i class="bx bx-check-circle me-1"></i>APPROVED
                                            </span>
                                            <div class="small text-muted">All levels completed</div>
                                        </div>
                                    `;
                                } else if (overallStatus === 'REJECTED') {
                                    const rejectedBy = getRejectedBy(row);
                                    return `
                                        <div class="approval-status compact">
                                            <span class="badge bg-danger mb-1">
                                                <i class="bx bx-x-circle me-1"></i>REJECTED
                                            </span>
                                            <div class="small text-muted">By ${rejectedBy}</div>
                                        </div>
                                    `;
                                } else {
                                    // Pending - show current level and progress
                                    const progress = getApprovalProgress(row);
                                    const nextRole = getNextPendingRole(row);

                                    return `
                                        <div class="approval-status compact">
                                            <span class="badge bg-warning mb-1">
                                                <i class="bx bx-time me-1"></i>PENDING
                                            </span>
                                            <div class="progress mb-1" style="height: 3px;">
                                                <div class="progress-bar bg-info" style="width: ${progress}%"></div>
                                            </div>
                                            <div class="small text-muted">
                                                Awaiting: <strong>${nextRole}</strong>
                                            </div>
                                        </div>
                                    `;
                                }
                            }
                        },

                        {
                            data: 'ismailgenerate',
                            render: function (data, type, row) {
                                if (data === 'Sent') {
                                    return '<span class="badge bg-success"><i class="bx bx-check-circle me-1"></i>Sent</span>';
                                } else if (data === 'Pending') {
                                    return '<span class="badge bg-warning text-dark"><i class="bx bx-time me-1"></i>Pending</span>';
                                } else {
                                    return '<span class="badge bg-secondary"><i class="bx bx-x-circle me-1"></i>Not Sent</span>';
                                }
                            }
                        },
                        {
                            data: null,
                            render: function (data, type, row) {
                                let actions = '<div class="btn-group" role="group">';

                                console.log('logged user aa :', user);

                                // Email button - only show for FRO users when conditions are met
                                if (user == "FRO" && row.status_vc == "APPROVED" && row.ismailgenerate != "Sent") {
                                    actions += `<button type="button" class="btn btn-sm btn-outline-primary emailapp" 
                                               data-code="${row.agency_code}" 
                                               title="Send Email">
                                               <i class="bx bx-envelope"></i>
                                               </button>`;
                                }

                                // View button - always available
                                actions += `<button type="button" class="btn btn-sm btn-outline-info viewapp" 
                                           data-code="${row.agency_code}" 
                                           title="View Details">
                                           <i class="bx bx-show"></i>
                                           </button>`;

                                actions += '</div>';
                                return actions;
                            }
                        }
                    ]
                });

                // Initialize filters after table is created
                initializeFilters();

                $(document).on('change', '.js-check-all', function () {
                    var checkboxes = $('#viewtbl tbody input[type="checkbox"]');
                    checkboxes.prop('checked', $(this).prop('checked'));
                });
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }
});

// Global action functions
window.viewAgency = function (agencyCode) {
    var page = "../content/viewagencydt.php?agency_code=" + agencyCode;
    $("#content").load(page);
};

window.processApproval = function (agencyCode) {
    // Open approval interface - you can customize this
    window.open(`approval.php?code=${agencyCode}`, '_blank');
};

window.sendEmail = function (agencyCode) {
    if (confirm('Send approval email to this agency?')) {
        $.ajax({
            url: '../pages/agent_register_success.php?code=' + agencyCode,
            type: 'POST',
            success: function (response) {
                if (response.status === 1) {
                    alert("Email sent successfully!");
                    location.reload();
                } else {
                    alert("Failed to send email");
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX request failed:', error);
                alert("Error sending email");
            }
        });
    }
};

