$(document).ready(function() {
    var table = $('#viewtbl').DataTable();
 
    $(document).on('click', '.editapp', function(event) {
        event.preventDefault();
        var nic_no = $(this).data('nic'); 
        console.log('click editapp', nic_no);
        var page = "content/view_applicationform.php?nic=" + nic_no;
        $("#content").load(page);
    });
    $(document).on('click', '.dwnapp', function(event) {
        event.preventDefault();
        var nic_no = $(this).data('nic'); 
        console.log('click dwnapp', nic_no);
        var page = "content/application_formpdf.php?nic=" + nic_no; 
        window.open(page, '_blank');
        //$("#content").load(page);
    });
    $(document).on('click', '.payapp', function(event) {
        event.preventDefault();
        var passportno = $(this).data('nic'); 
        console.log('click payapp', passportno);

        $.ajax({
            url: '../includes/data/check_availabiliy_fqsr.php', // The URL of the PHP file that checks the passport number
            type: 'POST',
            async: false,
            data: { passportno: passportno },
            success: function(response) {
                console.log(response[0]);
                if (response[0] === 'exist') {
                    var page = "../pg_sampath/pgrequest_check_fsr.php?idn=" + passportno;
                    //$("#content").load(page);
                    window.open(page, '_blank');
                }else{
                    toastr.error("Record not exist.", '', { timeOut: 1000,});
                }
            },
            error: function() {
                toastr.error("Error while checking availability.", '', { timeOut: 1000,});
               
            }
        });
        //var page = "../pg_sampath/pgrequest_check_fsr.php?idn=" + passportno;
        //$("#content").load(page);
    });
  
    

    $.ajax({
        url: '../includes/data/get_application_list.php',
        method: 'GET',
        success: function(data) {
            console.log(data);
            $('#viewtbl').DataTable({
                searching: true,
                data: data,
                destroy: true,
                columns: [
                    {
                        data: null,
                        render: function(data, type, row) {
                            return '<label class="control control--checkbox">' +
                                '<input type="checkbox"/>' +
                                '<div class="control__indicator"></div>' +
                                '</label>';
                        },
                        orderable: false 
                    },
                    { data: 'nic_no' },
                    { data: 'stu_name_initials' },
                    { data: 'stu_email' },
                    { data: 'course_name' },
                    { data: 'formStatus' },
                    {
                        data: null,
                        render: function(data, type, row) {
                            let actions = '<td><div class="dropdown">' +
                                '<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">' +
                                '<i class="bx bx-dots-vertical-rounded"></i>' +
                                '</button>' +
                                '<div class="dropdown-menu">';
                            console.log(row.formStatus);
                            if (row.formStatus !== 'SUBMITTED') {
                                actions += '<a class="dropdown-item editapp" id="editapp" data-nic="' + row.nic_no + '"><i class="bx bx-edit-alt me-1"></i> View</a>' +
                                           '<a class="dropdown-item deleteapp" id="dltapp" data-nic="' + row.nic_no + '"><i class="bx bx-trash me-1"></i> Delete</a>';
                            }else{
                                actions += '<a class="dropdown-item editapp" id="editapp" data-nic="' + row.nic_no + '"><i class="bx bx-edit-alt me-1"></i> View</a>' +
                                            '<a class="dropdown-item payapp" id="payapp" data-nic="' + row.nic_no + '"><i class="bx bx-credit-card me-1"></i> Payment</a>';
                            }
    
                            actions += '<a class="dropdown-item dwnapp" id="dwnapp" data-nic="' + row.nic_no + '"><i class="bx bx-download me-1"></i> Download</a>' +
                                       '</div>' +
                                       '</div></td>';
                            
                            return actions;
                        }
                    }
                ]
            });
            $(document).on('change', '.js-check-all', function() {
                var checkboxes = $('#viewtbl tbody input[type="checkbox"]');
                checkboxes.prop('checked', $(this).prop('checked'));
            });
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
});

