/**
 * Enhanced Dashboard JavaScript
 * Professional KDU Admin Portal
 */

class AdminDashboard {
    constructor() {
        this.init();
        this.setupEventListeners();
        this.loadDashboardData();
        this.setupDataTables();
        this.setupToastr();
    }

    init() {
        console.log('🚀 Admin Dashboard initialized');
        this.showLoadingOverlay();

        // Add fade-in animation to content
        setTimeout(() => {
            document.getElementById('content').classList.add('fade-in');
            this.hideLoadingOverlay();
        }, 500);
    }

    setupEventListeners() {
        // Enhanced menu interactions
        document.querySelectorAll('.menu-link').forEach(link => {
            link.addEventListener('click', (e) => {
                this.handleMenuClick(e);
            });
        });

        // Enhanced logout functionality
        const logoutBtn = document.getElementById('logout');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleLogout();
            });
        }

        // Enhanced pending list functionality
        const pendingListBtn = document.getElementById('pendinglist');
        if (pendingListBtn) {
            pendingListBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.loadPendingRequests();
            });
        }

        // Enhanced agencies functionality
        const agenciesBtn = document.getElementById('agencies');
        if (agenciesBtn) {
            agenciesBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.loadAgencies();
            });
        }

        // Enhanced applications functionality
        const applicationsBtn = document.getElementById('applications');
        if (applicationsBtn) {
            applicationsBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.loadApplications();
            });
        }

        // Responsive menu toggle
        const menuToggle = document.querySelector('.layout-menu-toggle');
        if (menuToggle) {
            menuToggle.addEventListener('click', this.toggleMobileMenu);
        }
    }

    handleMenuClick(e) {
        // Remove active class from all menu items
        document.querySelectorAll('.menu-item').forEach(item => {
            item.classList.remove('active');
        });

        // Add active class to clicked menu item
        const menuItem = e.target.closest('.menu-item');
        if (menuItem) {
            menuItem.classList.add('active');
        }

        // Add click animation
        const menuLink = e.target.closest('.menu-link');
        if (menuLink) {
            menuLink.style.transform = 'scale(0.95)';
            setTimeout(() => {
                menuLink.style.transform = '';
            }, 150);
        }
    }

    showLoadingOverlay() {
        const overlay = document.querySelector('.loading-overlay') || this.createLoadingOverlay();
        overlay.classList.add('show');
    }

    hideLoadingOverlay() {
        const overlay = document.querySelector('.loading-overlay');
        if (overlay) {
            overlay.classList.remove('show');
        }
    }

    createLoadingOverlay() {
        const overlay = document.createElement('div');
        overlay.className = 'loading-overlay';
        overlay.innerHTML = `
            <div class="loading-spinner"></div>
        `;
        document.body.appendChild(overlay);
        return overlay;
    }

    setupToastr() {
        // Configure toastr for professional notifications
        toastr.options = {
            closeButton: true,
            debug: false,
            newestOnTop: true,
            progressBar: true,
            positionClass: "toast-top-right",
            preventDuplicates: false,
            onclick: null,
            showDuration: "300",
            hideDuration: "1000",
            timeOut: "5000",
            extendedTimeOut: "1000",
            showEasing: "swing",
            hideEasing: "linear",
            showMethod: "fadeIn",
            hideMethod: "fadeOut"
        };
    }

    showNotification(type, title, message) {
        switch (type) {
            case 'success':
                toastr.success(message, title);
                break;
            case 'error':
                toastr.error(message, title);
                break;
            case 'warning':
                toastr.warning(message, title);
                break;
            case 'info':
                toastr.info(message, title);
                break;
            default:
                toastr.info(message, title);
        }
    }

    loadDashboardData() {
        // Create enhanced dashboard layout
        const dashboardHTML = `
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="fw-bold text-dark mb-1">Dashboard Overview</h2>
                            <p class="text-muted mb-0">Welcome back to KDU Admin Portal</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary" onclick="adminDashboard.refreshData()">
                                <i class="bx bx-refresh"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4" id="dashboardStats">
                <div class="col-xl-3 col-md-6">
                    <div class="dashboard-card slide-up">
                        <div class="dashboard-card-icon primary">
                            <i class="bx bx-user"></i>
                        </div>
                        <div class="dashboard-card-title" id="totalAgencies">-</div>
                        <div class="dashboard-card-subtitle">Total Agencies</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="dashboard-card slide-up" style="animation-delay: 0.1s">
                        <div class="dashboard-card-icon success">
                            <i class="bx bx-file"></i>
                        </div>
                        <div class="dashboard-card-title" id="totalApplications">-</div>
                        <div class="dashboard-card-subtitle">Total Applications</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="dashboard-card slide-up" style="animation-delay: 0.2s">
                        <div class="dashboard-card-icon warning">
                            <i class="bx bx-time"></i>
                        </div>
                        <div class="dashboard-card-title" id="pendingRequests">-</div>
                        <div class="dashboard-card-subtitle">Pending Requests</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="dashboard-card slide-up" style="animation-delay: 0.3s">
                        <div class="dashboard-card-icon info">
                            <i class="bx bx-check-circle"></i>
                        </div>
                        <div class="dashboard-card-title" id="approvedRequests">-</div>
                        <div class="dashboard-card-subtitle">Approved Today</div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12">
                    <div class="dashboard-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">Recent Activities</h5>
                            <button class="btn btn-sm btn-outline-primary" onclick="adminDashboard.loadAllActivities()">
                                View All
                            </button>
                        </div>
                        <div id="recentActivities">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.getElementById('content').innerHTML = dashboardHTML;
        //this.loadStatistics();
        //this.loadRecentActivities();
    }







    setupDataTables() {
        // Enhanced DataTables configuration
        this.dataTableConfig = {
            responsive: true,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            language: {
                search: "Search records:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            },
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
            columnDefs: [
                {
                    targets: 'no-sort',
                    orderable: false
                }
            ],
            initComplete: function (settings, json) {
                console.log('✅ DataTable initialized successfully');
            }
        };
    }

    loadPendingRequests() {
        this.showLoadingOverlay();

        console.log('🔄 Loading pending requests...');

        // Load the actual viewpendinglist.html file
        const contentDiv = document.getElementById('content');
        if (contentDiv) {
            // Show loading message
            contentDiv.innerHTML = `
                <div class="d-flex justify-content-center align-items-center" style="min-height: 300px;">
                    <div class="text-center">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <h5 class="text-muted">Loading pending requests...</h5>
                    </div>
                </div>
            `;

            // Load the pending list content
            $(contentDiv).load('content/viewpendinglist.html', (response, status, xhr) => {
                this.hideLoadingOverlay();

                if (status === "error") {
                    console.error('❌ Failed to load pending requests:', xhr.status, xhr.statusText);
                    contentDiv.innerHTML = `
                        <div class="alert alert-danger" role="alert">
                            <i class="bx bx-error-circle me-2"></i>
                            <strong>Error loading pending requests!</strong><br>
                            Status: ${xhr.status} ${xhr.statusText}<br>
                            <small>Please check if the file exists: content/viewpendinglist.html</small>
                        </div>
                    `;
                } else {
                    console.log('✅ Pending requests loaded successfully');
                    // Show success notification
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Pending requests loaded successfully', 'Success');
                    }
                }
            });
        }
    }

    fetchPendingRequests() {
        // This method can be used if needed for additional data loading
        console.log('🔄 Fetching pending requests data...');
    }

    loadAgencies() {
        this.showLoadingOverlay();

        const agenciesHTML = `
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="fw-bold text-dark mb-1">Registered Agencies</h2>
                            <p class="text-muted mb-0">Manage all registered education agencies</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-success" onclick="adminDashboard.addNewAgency()">
                                <i class="bx bx-plus"></i> Add Agency
                            </button>
                            <button class="btn btn-primary" onclick="adminDashboard.refreshAgencies()">
                                <i class="bx bx-refresh"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="dashboard-card">
                        <div class="table-responsive">
                            <table class="table table-hover" id="agenciesTable">
                                <thead>
                                    <tr>
                                        <th>Agency Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Registration Date</th>
                                        <th class="no-sort">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.getElementById('content').innerHTML = agenciesHTML;
        this.fetchAgencies();
        this.hideLoadingOverlay();
    }

    fetchAgencies() {
        // Simulate API call with sample data
        setTimeout(() => {
            const sampleData = [
                {
                    name: "Global Education Services",
                    email: "contact@globaledu.com",
                    phone: "+94771234567",
                    location: "Colombo",
                    status: "active",
                    date: "2024-01-15"
                },
                {
                    name: "International Study Center",
                    email: "info@intlstudy.com",
                    phone: "+94777654321",
                    location: "Kandy",
                    status: "active",
                    date: "2024-02-20"
                }
            ];

            const tbody = document.querySelector('#agenciesTable tbody');
            tbody.innerHTML = sampleData.map(item => `
                <tr>
                    <td>${item.name}</td>
                    <td>${item.email}</td>
                    <td>${item.phone}</td>
                    <td>${item.location}</td>
                    <td>
                        <span class="badge badge-success">
                            ${item.status.charAt(0).toUpperCase() + item.status.slice(1)}
                        </span>
                    </td>
                    <td>${item.date}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-primary" onclick="adminDashboard.editAgency('${item.email}')">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button class="btn btn-danger" onclick="adminDashboard.deleteAgency('${item.email}')">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');

            // Initialize DataTable
            $('#agenciesTable').DataTable(this.dataTableConfig);
        }, 1000);
    }

    loadApplications() {
        this.showLoadingOverlay();

        const applicationsHTML = `
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="fw-bold text-dark mb-1">Applications</h2>
                            <p class="text-muted mb-0">View and manage all student applications</p>
                        </div>
                        <button class="btn btn-primary" onclick="adminDashboard.refreshApplications()">
                            <i class="bx bx-refresh"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="dashboard-card">
                        <div class="table-responsive">
                            <table class="table table-hover" id="applicationsTable">
                                <thead>
                                    <tr>
                                        <th>Application ID</th>
                                        <th>Student Name</th>
                                        <th>Email</th>
                                        <th>Course</th>
                                        <th>Status</th>
                                        <th>Submit Date</th>
                                        <th class="no-sort">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.getElementById('content').innerHTML = applicationsHTML;
        this.fetchApplications();
        this.hideLoadingOverlay();
    }

    fetchApplications() {
        // Simulate API call with sample data
        setTimeout(() => {
            const sampleData = [
                {
                    id: "APP-2024-001",
                    name: "John Doe",
                    email: "john@example.com",
                    course: "Computer Science",
                    status: "pending",
                    date: "2024-09-23"
                },
                {
                    id: "APP-2024-002",
                    name: "Jane Smith",
                    email: "jane@example.com",
                    course: "Information Technology",
                    status: "approved",
                    date: "2024-09-22"
                }
            ];

            const tbody = document.querySelector('#applicationsTable tbody');
            tbody.innerHTML = sampleData.map(item => `
                <tr>
                    <td>${item.id}</td>
                    <td>${item.name}</td>
                    <td>${item.email}</td>
                    <td>${item.course}</td>
                    <td>
                        <span class="badge ${this.getStatusBadgeClass(item.status)}">
                            ${item.status.charAt(0).toUpperCase() + item.status.slice(1)}
                        </span>
                    </td>
                    <td>${item.date}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-primary" onclick="adminDashboard.viewApplication('${item.id}')">
                                <i class="bx bx-show"></i>
                            </button>
                            <button class="btn btn-success" onclick="adminDashboard.approveApplication('${item.id}')">
                                <i class="bx bx-check"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');

            // Initialize DataTable
            $('#applicationsTable').DataTable(this.dataTableConfig);
        }, 1000);
    }

    getStatusBadgeClass(status) {
        switch (status) {
            case 'pending': return 'badge-warning';
            case 'approved': return 'badge-success';
            case 'rejected': return 'badge-danger';
            case 'under_review': return 'badge-info';
            default: return 'badge-secondary';
        }
    }

    // Action methods
    approveRequest(email) {
        Swal.fire({
            title: 'Approve Request',
            text: `Are you sure you want to approve the request from ${email}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, approve it!'
        }).then((result) => {
            if (result.isConfirmed) {
                this.showNotification('success', 'Success', 'Request approved successfully!');
                this.refreshPendingRequests();
            }
        });
    }

    rejectRequest(email) {
        Swal.fire({
            title: 'Reject Request',
            text: `Are you sure you want to reject the request from ${email}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, reject it!'
        }).then((result) => {
            if (result.isConfirmed) {
                this.showNotification('error', 'Rejected', 'Request has been rejected.');
                this.refreshPendingRequests();
            }
        });
    }

    handleLogout() {
        Swal.fire({
            title: 'Logout',
            text: 'Are you sure you want to logout?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, logout!'
        }).then((result) => {
            if (result.isConfirmed) {
                this.showLoadingOverlay();
                // Simulate logout process
                setTimeout(() => {
                    window.location.href = '../login.html';
                }, 1000);
            }
        });
    }

    refreshData() {
        this.showNotification('info', 'Refreshing', 'Loading latest data...');
        this.loadDashboardData();
    }

    refreshPendingRequests() {
        this.loadPendingRequests();
    }

    refreshAgencies() {
        this.loadAgencies();
    }

    refreshApplications() {
        this.loadApplications();
    }

    toggleMobileMenu() {
        const layoutMenu = document.getElementById('layout-menu');
        layoutMenu.classList.toggle('menu-expanded');
    }
}

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.adminDashboard = new AdminDashboard();
});

// Global utility functions
window.showNotification = (type, title, message) => {
    if (window.adminDashboard) {
        window.adminDashboard.showNotification(type, title, message);
    }
};

window.showLoading = () => {
    if (window.adminDashboard) {
        window.adminDashboard.showLoadingOverlay();
    }
};

window.hideLoading = () => {
    if (window.adminDashboard) {
        window.adminDashboard.hideLoadingOverlay();
    }
};