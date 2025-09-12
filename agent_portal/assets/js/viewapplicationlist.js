$(document).ready(function () {
    var table = $('#viewtbl').DataTable();

    /*  $(document).on('click', '.editapp', function (event) {
         event.preventDefault();
         var nic_no = $(this).data('nic');
         console.log('click editapp', nic_no);
         var page = "../../content/view_applicationform.php?nic=" + nic_no;
         window.open(page, '_blank');
     }); */
    $(document).on('click', '.dwnapp', function (event) {
        event.preventDefault();
        var nic_no = $(this).data('nic');
        console.log('click dwnapp', nic_no);
        var page = "../../content/application_formpdf.php?nic=" + nic_no;
        window.open(page, '_blank');
        //$("#content").load(page);
    });




    $.ajax({
        url: '../includes/data/get_application_list.php',
        method: 'GET',
        success: function (data) {
            console.log(data);
            $('#viewtbl').DataTable({
                searching: true,
                data: data,
                destroy: true,
                columns: [
                    /* {
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
                    { data: 'stu_name_initials' },
                    { data: 'stu_email' },
                    { data: 'course_name' },
                    /* { data: 'formStatus' }, */
                    {
                        data: null,
                        render: function (data, type, row) {
                            let actions = '<td><div class="dropdown">' +
                                '<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">' +
                                '<i class="bx bx-dots-vertical-rounded"></i>' +
                                '</button>' +
                                '<div class="dropdown-menu">';
                            /* actions += '<a class="dropdown-item editapp" id="editapp" data-nic="' + row.nic_no + '"><i class="bx bx-edit-alt me-1"></i> View</a>';
 */
                            actions += '<a class="dropdown-item dwnapp" id="dwnapp" data-nic="' + row.nic_no + '"><i class="bx bx-download me-1"></i> Download</a>' +
                                '</div>' +
                                '</div></td>';

                            return actions;
                        }
                    }
                ]
            });
            /*  $(document).on('change', '.js-check-all', function () {
                 var checkboxes = $('#viewtbl tbody input[type="checkbox"]');
                 checkboxes.prop('checked', $(this).prop('checked'));
             }); */
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
});

