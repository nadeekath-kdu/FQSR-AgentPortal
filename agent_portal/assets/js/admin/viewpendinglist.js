$(document).ready(function() {
    var table = $('#viewtbl').DataTable();
 
    $(document).on('click', '.editapp', function(event) {
        event.preventDefault();
        var agency_code = $(this).data('agency_code'); 
        console.log('click editapp', agency_code);
        var page = "../content/viewagency.php?agency_code=" + agency_code;
        $("#content").load(page);
    });
   
    $(document).on('click', '.viewdoc', function(event) {
        event.preventDefault();
        var agency_code = $(this).data('agency_code');
        console.log('View Documents for:', agency_code);
    
        // Fetch the documents from the server
        $.ajax({
            url: '../data/get_documents.php',
            method: 'GET',
            data: { agency_code: agency_code },
            success: function(response) {
                console.log('Documents Response:', response);
                // Remove existing modal to ensure fresh content
                $('#documentsModal').remove();
                if (response.success) {
                    var documentList = response.documents.map(function(doc) {
                        var filePath = "../../upload/" + agency_code + "/" + doc;
                        console.log(filePath);
                        return `<li><a href="${filePath}" target="_blank">${doc}</a></li>`;
                    }).join('');
    
                    // Display documents in a modal or alert
                    var modalContent = `
                        <div class="modal fade" id="documentsModal" tabindex="-1" aria-labelledby="documentsModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="documentsModalLabel">Documents for ${agency_code}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <ul>${documentList}</ul>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    $('body').append(modalContent);
                    $('#documentsModal').modal('show');
                } else {
                    toastr.error(response.message || "Unable to fetch documents.");
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching documents:", xhr.responseText);
                toastr.error("An error occurred while fetching documents.");
            }
        });
    });
    

    $.ajax({
        url: '../data/get_pending_agency_list.php',
        method: 'GET',
        success: function(data) {
            console.log('Data:',data);
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
                    { data: 'agency_code' },
                    { data: 'fullname' },
                    { data: 'organisation' },
                    { data: 'country' },
                    { data: 'mobile' },
                    { data: 'email' },
                    { data: 'status' },
                    { data: 'ismailgenerate' },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return '<td><div class="dropdown">' +
                                '<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">' +
                                '<i class="bx bx-dots-vertical-rounded"></i>' +
                                '</button>' +
                                '<div class="dropdown-menu">' +
                                '<a class="dropdown-item editapp" id="eidtapp" data-agency_code="' + row.agency_code + '"><i class="bx bx-edit-alt me-1"></i> Edit</a>' +
                                '<a class="dropdown-item viewdoc" id="viewdoc" data-agency_code="' + row.agency_code + '"><i class="bx bx-download me-1"></i> View Documents</a>' +
                               /*  '<a class="dropdown-item dwnapp" id="dwnapp" data-nic="' + row.agency_code + '"><i class="bx bx-download me-1"></i> View</a>' + */
                               /*  '<a class="dropdown-item payapp" id="payapp" data-nic="' + row.agency_code + '"><i class="bx bx-credit-card me-1"></i> email</a>' + */
                                /* '<a class="dropdown-item deleteapp" id="dltapp" data-nic="' + row.agency_code + '"><i class="bx bx-trash me-1"></i> Delete</a>' + */
                                '</div>' +
                                '</div></td>';
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

