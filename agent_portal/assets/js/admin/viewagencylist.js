$(document).ready(function () {
    var user = "";
    $.ajax({
        url: '../data/get_user.php',
        type: 'GET',
        success: function (response) {
            //console.log('user:', response);
            user = response.user;
            //console.log('user2:', user);
            getdata(user);
        },
        error: function (xhr, status, error) {
            console.error('AJAX request failed:', error);
        }
    });


    var table = $('#viewtbl').DataTable();

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


    function getdata(user) {
        //var approval_status_vc = '';
        $.ajax({
            url: '../data/get_agency_list.php',
            method: 'GET',
            success: function (data) {
                //console.log(data);
                $('#viewtbl').DataTable({
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
                        { data: 'agency_code' },
                        { data: 'fullname' },
                        { data: 'organisation' },
                        { data: 'country' },
                        { data: 'mobile' },
                        { data: 'email' },
                        {
                            data: null,
                            render: function (data, type, row) {
                                console.log('status5:', row);
                                if (row.status_vc !== null && row.status_vc !== "") {
                                    //approval_status_vc = row.status_vc;
                                    return row.status_vc + ' by VC';
                                } else {
                                    if (row.status_dvc !== null && row.status_dvc !== "") {
                                        return row.status_dvc + ' by DVC';
                                    }
                                    else {
                                        if (row.status_dr !== null && row.status_dr !== "") {
                                            return row.status_dr + ' by DR';
                                        } else {
                                            if (row.status_fro !== null && row.status_fro !== "") {
                                                return row.status_fro + ' by FRO';
                                            } else {
                                                //console.log('status5:', row);
                                                return '';
                                            }
                                        }
                                    }
                                }


                            }
                        },

                        { data: 'ismailgenerate' },
                        {
                            data: null,
                            render: function (data, type, row) {
                                let actions = '<td><div class="dropdown">' +
                                    '<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">' +
                                    '<i class="bx bx-dots-vertical-rounded"></i>' +
                                    '</button>' +
                                    '<div class="dropdown-menu">';
                                /*  '<a class="dropdown-item editapp" id="eidtapp" data-nic="' + row.agency_code + '"><i class="bx bx-edit-alt me-1"></i> Edit</a>' + */
                                console.log('logged user aa :', user);
                                if (user == "FRO" && row.status_vc == "APPROVED" && row.ismailgenerate != "Sent") {
                                    actions += '<a class="dropdown-item emailapp" id="emailapp" data-code="' + row.agency_code + '"><i class="bx bx-credit-card me-1"></i> email</a>';
                                }
                                actions += '<a class="dropdown-item viewapp" id="viewapp" data-code="' + row.agency_code + '"><i class="bx bx-edit-alt me-1"></i> View</a>' +

                                    /* '<a class="dropdown-item deleteapp" id="dltapp" data-nic="' + row.agency_code + '"><i class="bx bx-trash me-1"></i> Delete</a>' + */
                                    '</div>' +
                                    '</div></td>';
                                return actions;
                            }
                        }
                    ]
                });
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

