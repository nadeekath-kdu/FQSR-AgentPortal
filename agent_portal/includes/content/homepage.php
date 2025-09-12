<?php
if (!isset($_SESSION)) {
    session_start();
}
include '../../../config/dbcon.php';
include '../../../config/global.php';

$ag_code = isset($_SESSION['agent_code']) ? $_SESSION['agent_code'] : '';
$agent_name = isset($_SESSION['agent_name']) ? $_SESSION['agent_name'] : 'Agent';

// Get statistics
$total_applications = 0;
$approved_applications = 0;
$pending_applications = 0;
$new_students = 0;

if (!empty($ag_code)) {
    // Total applications
    $sql_total = "SELECT COUNT(*) as total FROM mst_personal_details WHERE nameEduAgent = '$ag_code'";
    $result_total = $con_fqsr->query($sql_total);
    if ($result_total && $row = $result_total->fetch_assoc()) {
        $total_applications = $row['total'];
    }

    // Approved applications
    $sql_approved = "SELECT COUNT(*) as total FROM mst_personal_details WHERE nameEduAgent = '$ag_code' AND formStatus = 'APPROVED'";
    $result_approved = $con_fqsr->query($sql_approved);
    if ($result_approved && $row = $result_approved->fetch_assoc()) {
        $approved_applications = $row['total'];
    }

    // Pending applications
    $sql_pending = "SELECT COUNT(*) as total FROM mst_personal_details WHERE nameEduAgent = '$ag_code' AND formStatus IN ('PENDING', 'SUBMITTED', 'UNDER_REVIEW')";
    $result_pending = $con_fqsr->query($sql_pending);
    if ($result_pending && $row = $result_pending->fetch_assoc()) {
        $pending_applications = $row['total'];
    }

    // New students in last 3 months (up to current date)
    $sql_new = "SELECT COUNT(*) as total FROM mst_personal_details 
                WHERE nameEduAgent = '$ag_code' 
                AND DATE(application_submit_dt) >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
                AND DATE(application_submit_dt) <= CURDATE()";
    $result_new = $con_fqsr->query($sql_new);
    if ($result_new && $row = $result_new->fetch_assoc()) {
        $new_students = $row['total'];
    }

    // Get recent applications with proper error handling
    $recent_applications = array();

    if (!empty($ag_code)) {
        // First try with agent filter - last 5 months
        $sql_recent = "SELECT nic_no, stu_name_initials, course_name, formStatus, application_submit_dt, stu_email 
                       FROM mst_personal_details 
                       WHERE nameEduAgent = '$ag_code' 
                       AND DATE(application_submit_dt) >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
                       ORDER BY application_submit_dt DESC 
                       LIMIT 5";

        $result_recent = $con_fqsr->query($sql_recent);

        if ($result_recent && $result_recent->num_rows > 0) {
            while ($row = $result_recent->fetch_assoc()) {
                $recent_applications[] = $row;
            }
        }
    }
}
?>

<!-- Dashboard Header -->
<div class="row">
    <div class="col-lg-12 mb-4 order-0">
        <div class="card h-100" style="background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%); border: 1px solid rgba(99, 179, 237, 0.1);">
            <div class="card-body">
                <h5 class="card-title" style="color: #2d3748; font-weight: 600;">Welcome to KDU Agent Portal 🎓</h5>
                <p class="mb-4" style="color: #4a5568; line-height: 1.6;">
                    Effortlessly manage student applications for <span class="fw-semibold" style="color: #3182ce;">Kotelawala Defence University</span>.
                    Track progress, submit applications, and monitor performance with ease.
                </p>
                <p class="mb-3" style="color: #4a5568; font-size: 0.9rem;">
                    <strong>Agent:</strong> <?php echo isset($_SESSION['agent_name']) ? $_SESSION['agent_name'] : 'Unknown'; ?>
                    | <strong>Code:</strong> <?php echo $ag_code; ?>
                </p>
                <a href="javascript:void(0);" class="btn btn-sm" id="new-app-btn" style="background: #63b3ed; color: white; border: none; border-radius: 10px; padding: 0.5rem 1.5rem;">Create New Application</a>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-lg-3 col-md-6 col-6 mb-4">
        <div class="card" style="background: #ffffff; border: 1px solid rgba(99, 179, 237, 0.08);">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0" style="background: rgba(99, 179, 237, 0.1); color: #3182ce;">
                        <i class="bx bx-file-blank" style="font-size: 1.8rem;"></i>
                    </div>
                </div>
                <span class="fw-semibold d-block mb-1" style="color: #4a5568; font-size: 0.85rem;">Total Applications</span>
                <h3 class="card-title mb-2" style="color: #2d3748; font-weight: 600;"><?php echo $total_applications; ?></h3>
                <small class="fw-semibold" style="color: #38a169;"><i class="bx bx-up-arrow-alt"></i> All Time</small>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 col-6 mb-4">
        <div class="card" style="background: #ffffff; border: 1px solid rgba(72, 187, 120, 0.08);">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0" style="background: rgba(72, 187, 120, 0.1); color: #38a169;">
                        <i class="bx bx-check-circle" style="font-size: 1.8rem;"></i>
                    </div>
                </div>
                <span class="fw-semibold d-block mb-1" style="color: #4a5568; font-size: 0.85rem;">Approved Applications</span>
                <h3 class="card-title mb-2" style="color: #2d3748; font-weight: 600;"><?php echo $approved_applications; ?></h3>
                <small class="fw-semibold" style="color: #38a169;"><i class="bx bx-check"></i> Success Rate: <?php echo $total_applications > 0 ? round(($approved_applications / $total_applications) * 100, 1) : 0; ?>%</small>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 col-6 mb-4">
        <div class="card" style="background: #ffffff; border: 1px solid rgba(237, 137, 54, 0.08);">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0" style="background: rgba(237, 137, 54, 0.1); color: #dd6b20;">
                        <i class="bx bx-time-five" style="font-size: 1.8rem;"></i>
                    </div>
                </div>
                <span class="fw-semibold d-block mb-1" style="color: #4a5568; font-size: 0.85rem;">Pending Applications</span>
                <h3 class="card-title mb-2" style="color: #2d3748; font-weight: 600;"><?php echo $pending_applications; ?></h3>
                <small class="fw-semibold" style="color: #dd6b20;"><i class="bx bx-time"></i> Under Review</small>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 col-6 mb-4">
        <div class="card" style="background: #ffffff; border: 1px solid rgba(99, 179, 237, 0.08);">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0" style="background: rgba(99, 179, 237, 0.1); color: #3182ce;">
                        <i class="bx bx-user-plus" style="font-size: 1.8rem;"></i>
                    </div>
                </div>
                <span class="fw-semibold d-block mb-1" style="color: #4a5568; font-size: 0.85rem;">New Students</span>
                <h3 class="card-title mb-2" style="color: #2d3748; font-weight: 600;"><?php echo $new_students; ?></h3>
                <small class="fw-semibold" style="color: #3182ce;"><i class="bx bx-calendar"></i> Last 3 Months</small>
            </div>
        </div>
    </div>
</div>

<!-- Recent Applications & Quick Actions -->
<div class="row">
    <!-- Recent Applications -->
    <div class="col-md-8 col-lg-8 order-2 mb-4">
        <div class="card h-100" style="background: #ffffff; border: 1px solid rgba(99, 179, 237, 0.08);">
            <div class="card-header d-flex align-items-center justify-content-between pb-0">
                <div class="card-title mb-0">
                    <h5 class="m-0 me-2" style="color: #2d3748; font-weight: 600;">Recent Applications</h5>
                    <small class="text-muted">Latest submissions from your agency</small>
                </div>
                <div class="dropdown">
                    <button class="btn p-0" type="button" id="recentApps" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="recentApps">
                        <a class="dropdown-item" href="javascript:void(0);" id="view-all-apps">View All</a>
                        <a class="dropdown-item" href="javascript:void(0);">Export List</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th style="color: #4a5568; font-weight: 500;">Student Name</th>
                                <th style="color: #4a5568; font-weight: 500;">Program</th>
                                <th style="color: #4a5568; font-weight: 500;">Status</th>
                                <th style="color: #4a5568; font-weight: 500;">Date</th>
                                <th style="color: #4a5568; font-weight: 500;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_applications)): ?>
                                <tr>
                                    <td colspan="5" class="text-center" style="color: #4a5568; padding: 2rem;">
                                        No applications found. <a href="javascript:void(0);" id="create-first-app" style="color: #3182ce;">Create your first application</a>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recent_applications as $index => $app): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3">
                                                    <?php
                                                    $initials = '';
                                                    $names = explode(' ', $app['stu_name_initials']);
                                                    foreach ($names as $name) {
                                                        $initials .= strtoupper(substr($name, 0, 1));
                                                    }
                                                    $colors = array('bg-label-primary', 'bg-label-success', 'bg-label-info', 'bg-label-warning', 'bg-label-danger');
                                                    $color = $colors[$index % count($colors)];
                                                    ?>
                                                    <span class="avatar-initial rounded-circle <?php echo $color; ?>"><?php echo substr($initials, 0, 2); ?></span>
                                                </div>
                                                <div>
                                                    <strong style="color: #2d3748;"><?php echo htmlspecialchars($app['stu_name_initials']); ?></strong>
                                                    <div class="text-muted small"><?php echo htmlspecialchars($app['nic_no']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($app['course_name']); ?></td>
                                        <td>
                                            <?php
                                            $status = strtoupper($app['formStatus']);
                                            $badge_class = 'bg-label-secondary';
                                            switch ($status) {
                                                case 'APPROVED':
                                                    $badge_class = 'bg-label-success';
                                                    break;
                                                case 'PENDING':
                                                case 'SUBMITTED':
                                                    $badge_class = 'bg-label-warning';
                                                    break;
                                                case 'UNDER_REVIEW':
                                                    $badge_class = 'bg-label-info';
                                                    break;
                                                case 'REJECTED':
                                                    $badge_class = 'bg-label-danger';
                                                    break;
                                                case 'UNSUBMITTED':
                                                    $badge_class = 'bg-label-secondary';
                                                    break;
                                            }
                                            ?>
                                            <span class="badge <?php echo $badge_class; ?>"><?php echo $status; ?></span>
                                        </td>
                                        <td>
                                            <div style="color: #2d3748;"><?php echo date('M d, Y', strtotime(isset($app['application_submit_dt']) ? $app['application_submit_dt'] : 'now')); ?></div>
                                            <small class="text-muted"><?php echo date('h:i A', strtotime(isset($app['application_submit_dt']) ? $app['application_submit_dt'] : 'now')); ?></small>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-show me-2"></i>View Details</a>
                                                    <?php if ($status == 'UNSUBMITTED'): ?>
                                                        <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-edit-alt me-2"></i>Edit</a>
                                                    <?php endif; ?>
                                                    <a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-download me-2"></i>Download PDF</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Info -->
    <div class="col-md-4 col-lg-4 order-1 mb-4">
        <div class="row">
            <!-- Quick Actions -->
            <div class="col-12 mb-4">
                <div class="card" style="background: #ffffff; border: 1px solid rgba(99, 179, 237, 0.08);">
                    <div class="card-body">
                        <h5 class="card-title" style="color: #2d3748; font-weight: 600;">Quick Actions</h5>
                        <div class="d-grid gap-3">
                            <button class="btn" id="quick-new-app" style="background: linear-gradient(45deg, #63b3ed, #4299e1); color: white; border: none; border-radius: 10px; padding: 0.7rem 1rem;">
                                <i class="bx bx-plus me-2"></i>New Application
                            </button>
                            <button class="btn" id="quick-view-apps" style="background: #f7fafc; color: #3182ce; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.7rem 1rem;">
                                <i class="bx bx-list-ul me-2"></i>View All Applications
                            </button>
                            <button class="btn" id="quick-reports" style="background: #f7fafc; color: #4a5568; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.7rem 1rem;">
                                <i class="bx bx-bar-chart-alt-2 me-2"></i>Generate Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- University Info -->
            <div class="col-12 mb-4">
                <div class="card" style="background: linear-gradient(135deg, #e6fffa 0%, #f0fff4 100%); border: 1px solid rgba(72, 187, 120, 0.1);">
                    <div class="card-body">
                        <h5 class="card-title" style="color: #2d3748; font-weight: 600;">KDU Information</h5>
                        <p class="card-text small" style="color: #4a5568; line-height: 1.6;">
                            Kotelawala Defence University is Sri Lanka's premier defence university, offering world-class education in various fields.
                        </p>
                        <a href="https://kdu.ac.lk" target="_blank" class="btn btn-sm" style="background: #38a169; color: white; border: none; border-radius: 8px; padding: 0.4rem 1rem;">
                            Visit Website <i class="bx bx-link-external ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Application Deadlines -->
            <div class="col-12">
                <div class="card" style="background: #ffffff; border: 1px solid rgba(99, 179, 237, 0.08);">
                    <div class="card-body">
                        <h6 class="card-title" style="color: #2d3748; font-weight: 600;">Important Dates</h6>
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker" style="background-color: #63b3ed;"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title" style="color: #2d3748;">Application Deadline</h6>
                                    <p class="timeline-text" style="color: #4a5568;"><?php echo date('F j, Y', strtotime($application_closing_date)); ?></p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-marker" style="background-color: #ed8936;"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title" style="color: #2d3748;">Interview Period</h6>
                                    <p class="timeline-text" style="color: #4a5568;">
                                        <?php
                                        $interview_start = date('F j, Y', strtotime($application_closing_date . ' + 3 weeks'));
                                        $interview_end = date('F j, Y', strtotime($application_closing_date . ' + 4 weeks'));
                                        echo $interview_start . ' - ' . $interview_end;
                                        ?>
                                    </p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-marker" style="background-color: #48bb78;"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title" style="color: #2d3748;">Results Release</h6>
                                    <p class="timeline-text" style="color: #4a5568;"><?php echo date('F j, Y', strtotime($application_closing_date . ' + 6 weeks')); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Timeline Styles -->
<style>
    .timeline {
        position: relative;
        padding-left: 0;
    }

    .timeline-item {
        position: relative;
        padding-left: 30px;
        margin-bottom: 20px;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }

    .timeline-marker {
        position: absolute;
        left: 0;
        top: 2px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
    }

    .timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        left: 5px;
        top: 14px;
        width: 2px;
        height: calc(100% + 6px);
        background-color: #e2e8f0;
    }

    .timeline-title {
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .timeline-text {
        font-size: 0.75rem;
        margin-bottom: 0;
    }
</style>

<script>
    $(document).ready(function() {
        // Quick action button handlers
        $('#new-app-btn, #quick-new-app, #create-first-app').click(function(e) {
            e.preventDefault();
            $('#newapp').click();
        });

        $('#quick-view-apps, #view-all-apps').click(function(e) {
            e.preventDefault();
            $('#viewapp').click();
        });

        $('#quick-reports').click(function(e) {
            e.preventDefault();
            toastr.info('Report generation feature coming soon!');
        });

        // Auto-refresh data every 5 minutes
        setInterval(function() {
            $('#content').load('content/homepage.php');
        }, 300000);
    });
</script>