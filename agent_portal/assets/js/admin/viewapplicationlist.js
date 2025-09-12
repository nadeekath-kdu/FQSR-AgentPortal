$(document).ready(function () {
    let table;

    getagency();
    loadApplications();

    // Filter applications based on selected agent
    /* $('#filterAgent').on('change', function () {
        var selectedAgent = $(this).val();
        console.log('Selected Agent:', selectedAgent);

        if (table) {
            table.column(5).search(selectedAgent ? '^' + selectedAgent + '$' : '', true, false).draw();
        }
    }); */


    $(document).on('click', '.editapp', function (event) {
        event.preventDefault();
        var nic_no = $(this).data('nic');
        console.log('click editapp', nic_no);
        var page = "content/edit_applicationform.php?nic=" + nic_no;
        $("#content").load(page);
    });
    $(document).on('click', '.dwnapp', function (event) {
        event.preventDefault();
        var nic_no = $(this).data('nic');
        console.log('click dwnapp', nic_no);
        var page = "content/application_formpdf.php?nic=" + nic_no;
        $("#content").load(page);
    });
    /* $(document).on('click', '.editapp', function (event) {
        event.preventDefault();
        var nic_no = $(this).data('nic');
        console.log('click editapp', nic_no);
        var page = "content/edit_applicationform.php?nic=" + nic_no;
        $("#content").load(page);
    }); */

    function getagency() {
        $.ajax({
            url: '../data/get_distinct_agency_list.php',
            method: 'GET',
            dataType: 'json', // Explicitly tell jQuery to expect JSON
            success: function (data) {
                var select = $('#filterAgent');
                select.empty(); // Clear previous options
                data.forEach(function (agent) {
                    select.append($('<option>', {
                        value: agent,
                        text: agent
                    }));
                });
            },
            error: function (xhr, status, error) {
                console.error('Failed to load agents:', error);
                alert('An error occurred while loading agents. Please try again later.');
            }
        });
    }
    function loadApplications() {
        $.ajax({
            url: '../data/get_application_list.php',
            method: 'GET',
            success: function (data) {
                //console.log('data:'.data);
                if ($.fn.DataTable.isDataTable('#viewtbl')) {
                    $('#viewtbl').DataTable().destroy();
                }
                table = $('#viewtbl').DataTable({
                    searching: true,
                    data: data,
                    destroy: true,
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
                        { data: 'nic_no' },
                        { data: 'course_name' },
                        { data: 'intake' },
                        { data: 'stu_name_initials' },
                        { data: 'citizenship_type' },
                        { data: 'nameEduAgent' },
                        /*  { data: 'intake' }, */
                        {
                            data: null,
                            render: function (data, type, row) {
                                return '<td><div class="dropdown">' +
                                    '<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">' +
                                    '<i class="bx bx-dots-vertical-rounded"></i>' +
                                    '</button>' +
                                    '<div class="dropdown-menu">' +
                                    '<a class="dropdown-item editapp" id="eidtapp" data-nic="' + row.nic_no + '"><i class="bx bx-edit-alt me-1"></i> Edit</a>' +
                                    '<a class="dropdown-item dwnapp" id="dwnapp" data-nic="' + row.nic_no + '"><i class="bx bx-download me-1"></i> Download</a>' +
                                    '<a class="dropdown-item payapp" id="payapp" data-nic="' + row.nic_no + '"><i class="bx bx-credit-card me-1"></i> Payment</a>' +
                                    '<a class="dropdown-item deleteapp" id="dltapp" data-nic="' + row.nic_no + '"><i class="bx bx-trash me-1"></i> Delete</a>' +
                                    '</div>' +
                                    '</div></td>';
                            }
                        }
                    ]
                });
                $('#filterAgent').on('change', function () {
                    var selectedAgent = $(this).val();
                    console.log('Filtering by Agent:', selectedAgent);

                    if (table) {
                        console.log('Filtering by Agent add:', selectedAgent);
                        table.column(5).search(selectedAgent || '', false, false).draw();
                    }
                });
                /* $(document).on('change', '.js-check-all', function () {
                    var checkboxes = $('#viewtbl tbody input[type="checkbox"]');
                    checkboxes.prop('checked', $(this).prop('checked'));
                }); */
            },
            error: function (xhr, status, error) {
                console.error("error".xhr.responseText);
                console.log('data11:'.data);
            }
        });
    }
});

