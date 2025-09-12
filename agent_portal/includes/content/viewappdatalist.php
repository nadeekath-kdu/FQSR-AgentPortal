<?php
if (!isset($_SESSION)) {
    session_start();
}
include '../../config/dbcon.php';

$ag_code = isset($_SESSION['agent_code']) ? $_SESSION['agent_code'] : '';
?>

<!-- Applications List Page -->
<div class="row">
    <div class="col-12">
        <div class="card" style="background: #ffffff; border: 1px solid rgba(99, 179, 237, 0.08);">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0" style="color: #2d3748; font-weight: 600;">All Applications</h5>
                    <small class="text-muted">Manage and track all student applications</small>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary btn-sm" id="export-btn" style="border-color: #e2e8f0; color: #4a5568; border-radius: 8px;">
                        <i class="bx bx-export me-1"></i>Export
                    </button>
                    <button class="btn btn-sm" id="new-application-btn" style="background: linear-gradient(45deg, #63b3ed, #4299e1); color: white; border: none; border-radius: 8px;">
                        <i class="bx bx-plus me-1"></i>New Application
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label class="form-label" style="color: #4a5568; font-weight: 500;">Status Filter</label>
                        <select class="form-select" id="status-filter" style="border-color: #e2e8f0; border-radius: 8px;">
                            <option value="">All Status</option>
                            <option value="PENDING">Pending</option>
                            <option value="APPROVED">Approved</option>
                            <option value="REJECTED">Rejected</option>
                            <option value="UNDER_REVIEW">In Review</option>
                            <option value="UNSUBMITTED">Unsubmitted</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="color: #4a5568; font-weight: 500;">Program Filter</label>
                        <select class="form-select" id="program-filter" style="border-color: #e2e8f0; border-radius: 8px;">
                            <option value="">All Programs</option>
                            <!-- Programs will be loaded dynamically -->
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="color: #4a5568; font-weight: 500;">Search</label>
                        <input type="text" class="form-control" id="search-input" placeholder="Search by name or NIC" style="border-color: #e2e8f0; border-radius: 8px;">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="color: #4a5568; font-weight: 500;">Actions</label>
                        <button class="btn btn-outline-primary w-100" id="refresh-btn" style="border-color: #63b3ed; color: #3182ce; border-radius: 8px;">
                            <i class="bx bx-refresh me-1"></i>Refresh
                        </button>
                    </div>
                </div>

                <!-- Applications Table -->
                <div class="table-responsive">
                    <table class="table table-hover" id="applications-table">
                        <thead>
                            <tr>
                                <th style="color: #4a5568; font-weight: 500; padding: 1.2rem;">Application ID</th>
                                <th style="color: #4a5568; font-weight: 500; padding: 1.2rem;">Student Name</th>
                                <th style="color: #4a5568; font-weight: 500; padding: 1.2rem;">Program</th>
                                <th style="color: #4a5568; font-weight: 500; padding: 1.2rem;">Status</th>
                                <th style="color: #4a5568; font-weight: 500; padding: 1.2rem;">Email</th>
                                <th style="color: #4a5568; font-weight: 500; padding: 1.2rem;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="applications-tbody">
                            <!-- Data will be loaded here -->
                        </tbody>
                    </table>
                </div>

                <!-- Loading indicator -->
                <div id="loading-indicator" class="text-center" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2" style="color: #4a5568;">Loading applications...</p>
                </div>

                <!-- No data message -->
                <div id="no-data-message" class="text-center" style="display: none; padding: 3rem;">
                    <i class="bx bx-file-blank" style="font-size: 3rem; color: #cbd5e0;"></i>
                    <h6 style="color: #4a5568; margin-top: 1rem;">No applications found</h6>
                    <p style="color: #718096;">Create your first application to get started.</p>
                    <button class="btn btn-primary" id="create-first-application">
                        <i class="bx bx-plus me-2"></i>Create Application
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let applicationsData = [];

        // Load applications on page load
        loadApplications();
        loadPrograms();

        function loadApplications() {
            $('#loading-indicator').show();
            $('#applications-tbody').empty();
            $('#no-data-message').hide();

            $.ajax({
                url: 'data/get_application_list.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#loading-indicator').hide();

                    if (data && data.length > 0) {
                        applicationsData = data;
                        displayApplications(data);
                    } else {
                        $('#no-data-message').show();
                    }
                },
                error: function(xhr, status, error) {
                    $('#loading-indicator').hide();
                    console.error('Error loading applications:', error);
                    toastr.error('Failed to load applications. Please try again.');
                    $('#no-data-message').show();
                }
            });
        }

        function loadPrograms() {
            $.ajax({
                url: 'data/get_degree_list.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    const programSelect = $('#program-filter');
                    programSelect.find('option:not(:first)').remove();

                    if (data && data.length > 0) {
                        const uniquePrograms = [...new Set(data.map(item => item.course_name))];
                        uniquePrograms.forEach(function(program) {
                            programSelect.append(`<option value="${program}">${program}</option>`);
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading programs:', error);
                }
            });
        }

        function displayApplications(applications) {
            const tbody = $('#applications-tbody');
            tbody.empty();

            if (applications.length === 0) {
                $('#no-data-message').show();
                return;
            }

            applications.forEach(function(app, index) {
                const initials = getInitials(app.stu_name_initials);
                const colors = ['bg-label-primary', 'bg-label-success', 'bg-label-info', 'bg-label-warning', 'bg-label-danger'];
                const color = colors[index % colors.length];
                const statusBadge = getStatusBadge(app.formStatus);

                const row = `
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 1.2rem; color: #2d3748;">
                        <strong>${app.nic_no}</strong>
                        <div class="text-muted small">KDU-${new Date().getFullYear()}-${String(index + 1).padStart(3, '0')}</div>
                    </td>
                    <td style="padding: 1.2rem;">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <span class="avatar-initial rounded-circle ${color}">${initials}</span>
                            </div>
                            <div>
                                <strong style="color: #2d3748;">${app.stu_name_initials}</strong>
                                <div class="text-muted small">${app.nic_no}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 1.2rem;">
                        <span class="badge bg-label-info">${app.course_name}</span>
                    </td>
                    <td style="padding: 1.2rem;">
                        ${statusBadge}
                    </td>
                    <td style="padding: 1.2rem; color: #2d3748;">
                        ${app.stu_email || 'N/A'}
                    </td>
                    <td style="padding: 1.2rem;">
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" style="border-color: #e2e8f0; border-radius: 6px;">
                                Actions
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="javascript:void(0);" onclick="viewApplication('${app.nic_no}')">
                                    <i class="bx bx-show me-2"></i>View Details
                                </a>
                                ${app.formStatus === 'UNSUBMITTED' ? `
                                <a class="dropdown-item" href="javascript:void(0);" onclick="editApplication('${app.nic_no}')">
                                    <i class="bx bx-edit-alt me-2"></i>Edit
                                </a>
                                ` : ''}
                                <a class="dropdown-item" href="javascript:void(0);" onclick="downloadApplication('${app.nic_no}')">
                                    <i class="bx bx-download me-2"></i>Download PDF
                                </a>
                                ${app.formStatus === 'UNSUBMITTED' ? `
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="deleteApplication('${app.nic_no}')">
                                    <i class="bx bx-trash me-2"></i>Delete
                                </a>
                                ` : ''}
                            </div>
                        </div>
                    </td>
                </tr>
            `;

                tbody.append(row);
            });
        }

        function getInitials(name) {
            if (!name) return 'UN';
            const names = name.split(' ');
            let initials = '';
            for (let i = 0; i < Math.min(2, names.length); i++) {
                initials += names[i].charAt(0).toUpperCase();
            }
            return initials || 'UN';
        }

        function getStatusBadge(status) {
            const statusUpper = (status || 'UNKNOWN').toUpperCase();
            let badgeClass = 'bg-label-secondary';

            switch (statusUpper) {
                case 'APPROVED':
                    badgeClass = 'bg-label-success';
                    break;
                case 'PENDING':
                case 'SUBMITTED':
                    badgeClass = 'bg-label-warning';
                    break;
                case 'UNDER_REVIEW':
                    badgeClass = 'bg-label-info';
                    break;
                case 'REJECTED':
                    badgeClass = 'bg-label-danger';
                    break;
                case 'UNSUBMITTED':
                    badgeClass = 'bg-label-secondary';
                    break;
            }

            return `<span class="badge ${badgeClass}">${statusUpper}</span>`;
        }

        // Filter functionality
        $('#status-filter, #program-filter').change(function() {
            filterApplications();
        });

        $('#search-input').on('keyup', function() {
            filterApplications();
        });

        function filterApplications() {
            const statusFilter = $('#status-filter').val();
            const programFilter = $('#program-filter').val();
            const searchTerm = $('#search-input').val().toLowerCase();

            let filteredData = applicationsData;

            if (statusFilter) {
                filteredData = filteredData.filter(app => app.formStatus === statusFilter);
            }

            if (programFilter) {
                filteredData = filteredData.filter(app => app.course_name === programFilter);
            }

            if (searchTerm) {
                filteredData = filteredData.filter(app =>
                    app.stu_name_initials.toLowerCase().includes(searchTerm) ||
                    app.nic_no.toLowerCase().includes(searchTerm) ||
                    (app.stu_email && app.stu_email.toLowerCase().includes(searchTerm))
                );
            }

            displayApplications(filteredData);
        }

        // Button handlers
        $('#refresh-btn').click(function() {
            loadApplications();
            toastr.success('Applications refreshed successfully!');
        });

        $('#export-btn').click(function() {
            toastr.info('Export functionality will be implemented soon!');
        });

        $('#new-application-btn, #create-first-application').click(function() {
            $('#newapp').click();
        });
    });

    // Global functions for dropdown actions
    function viewApplication(nicNo) {
        toastr.info('Opening application details for: ' + nicNo);
        // Implement view functionality
    }

    function editApplication(nicNo) {
        toastr.info('Opening edit form for: ' + nicNo);
        // Implement edit functionality
    }

    function downloadApplication(nicNo) {
        toastr.info('Downloading application for: ' + nicNo);
        // Implement download functionality
    }

    function deleteApplication(nicNo) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e53e3e',
            cancelButtonColor: '#4a5568',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Implement delete functionality
                toastr.success('Application deleted successfully!');
            }
        });
    }
</script>