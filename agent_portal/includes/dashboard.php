<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
  header('Location: ../index.php');
  exit();
}
?>

<!DOCTYPE html>
<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8" />
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>Agent Portal - KDU</title>

  <meta name="description" content="" />
  <link rel="icon" type="image/x-icon" href="../assets/img/favicon/Kdufav.png" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet" />

  <link rel="stylesheet" href="../../assets/vendor/fonts/boxicons.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

  <link rel="stylesheet" href="../../assets/vendor/css/core.css" class="template-customizer-core-css" />
  <link rel="stylesheet" href="../../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="../assets/css/demo.css" />
  <link rel="stylesheet" href="../assets/css/content.css" />
  <link rel="stylesheet" href="../assets/css/dashboard-custom.css" />

  <!-- Additional responsive styles -->
  <style>
    .layout-navbar-fixed .layout-wrapper:not(.layout-horizontal):not(.layout-without-menu) .layout-page {
      padding-top: 90px !important;
    }

    @media (max-width: 768px) {
      .layout-navbar-fixed .layout-wrapper:not(.layout-horizontal):not(.layout-without-menu) .layout-page {
        padding-top: 70px !important;
      }

      .app-brand-text {
        font-size: 1rem !important;
      }
    }
  </style>

  <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="../../assets/vendor/libs/apex-charts/apex-charts.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
  <script src="../../assets/vendor/js/helpers.js"></script>

  <script src="../assets/js/config.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-components-web/14.0.0/material-components-web.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.css" />

</head>

<body>
  <!-- Layout wrapper -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <!-- Menu -->

      <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
        <div class="app-brand demo">
          <a href="dashboard.php" class="app-brand-link">
            <span class="app-brand-logo demo">
              <img src="../assets/img/kdu/Kotelawala_Defence_University_crest.png" alt="KDU Logo" height="32" width="32">
            </span>
            <span class="demo menu-text fw-bolder ms-2 fs-4">KDU Agent Portal</span>
          </a>

          <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
          </a>
        </div>

        <div class="menu-inner-shadow"></div>

        <ul class="menu-inner py-1">
          <!-- Dashboard -->
          <li class="menu-item active">
            <a href="dashboard.php" class="menu-link">
              <i class="menu-icon tf-icons bx bx-home-circle" style="color:#3182ce;"></i>
              <div data-i18n="Analytics">Dashboard</div>
            </a>
          </li>

          <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Student Applications</span>
          </li>
          <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link" id="newapp">
              <i class="menu-icon tf-icons bx bx-user-plus" style="color:#4299e1;"></i>
              <div data-i18n="Account Settings">New Application</div>
            </a>
          </li>
          <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link" id="viewapp">
              <i class="menu-icon tf-icons bx bx-list-ul" style="color:#63b3ed;"></i>
              <div data-i18n="Authentications">All Applications</div>
            </a>
          </li>
          <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link" id="pendingapp">
              <i class="menu-icon tf-icons bx bx-time-five" style="color:#ed8936;"></i>
              <div data-i18n="Authentications">Pending Reviews</div>
            </a>
          </li>
          <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link" id="approvedapp">
              <i class="menu-icon tf-icons bx bx-check-circle" style="color:#38a169;"></i>
              <div data-i18n="Authentications">Approved Applications</div>
            </a>
          </li>

          <!-- <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Reports & Analytics</span>
          </li>
          <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link" id="reports">
              <i class="menu-icon tf-icons bx bx-bar-chart-alt-2"></i>
              <div data-i18n="Reports">Application Reports</div>
            </a>
          </li>
          <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link" id="analytics">
              <i class="menu-icon tf-icons bx bx-line-chart"></i>
              <div data-i18n="Analytics">Performance Analytics</div>
            </a>
          </li>

          <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Agency Management</span>
          </li>
          <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link" id="profile">
              <i class="menu-icon tf-icons bx bx-user"></i>
              <div data-i18n="Profile">Agency Profile</div>
            </a>
          </li>
          <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link" id="students">
              <i class="menu-icon tf-icons bx bx-group"></i>
              <div data-i18n="Students">Student Database</div>
            </a>
          </li> -->

          <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Settings</span>
          </li>
          <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link" id="changepw">
              <i class="menu-icon tf-icons bx bx-key" style="color:#2b6cb0;"></i>
              <div data-i18n="Account Settings">Change Password</div>
            </a>
          </li>
          <!-- <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link" id="notifications">
              <i class="menu-icon tf-icons bx bx-bell"></i>
              <div data-i18n="Notifications">Notifications</div>
            </a>
          </li>
          <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link" id="help">
              <i class="menu-icon tf-icons bx bx-help-circle"></i>
              <div data-i18n="Help">Help & Support</div>
            </a>
          </li> -->

          <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Account</span>
          </li>
          <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link" id="logout">
              <i class="menu-icon tf-icons bx bx-power-off" style="color:#e53e3e;"></i>
              <div data-i18n="Logout">Logout</div>
            </a>
          </li>
        </ul>
      </aside>
      <!-- / Menu -->

      <!-- Layout container -->
      <div class="layout-page  background-image">
        <!-- Navbar -->
        <nav
          class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
          id="layout-navbar">
          <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
            <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
              <i class="bx bx-menu bx-sm"></i>
            </a>
          </div>

          <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
            <!-- Search -->
            <div class="navbar-nav align-items-center">
              <div class="nav-item d-flex align-items-center">
                <i class="bx bx-search fs-4 lh-0"></i>
                <input
                  type="text"
                  class="form-control border-0 shadow-none"
                  placeholder="Search applications..."
                  aria-label="Search..." />
              </div>
            </div>
            <!-- /Search -->

            <ul class="navbar-nav flex-row align-items-center ms-auto">
              <!-- Place this tag where you want the button to render. -->
              <li class="nav-item lh-1 me-3">
                <span class="fw-semibold d-block"><?php echo isset($_SESSION['agent_name']) ? $_SESSION['agent_name'] : 'Agent'; ?></span>
                <small class="text-muted">Agent ID: <?php echo isset($_SESSION['agent_code']) ? $_SESSION['agent_code'] : 'N/A'; ?></small>
              </li>

              <!-- User -->
              <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                  <div class="avatar avatar-online" style="background: #e3f0fc; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%;">
                    <i class="bx bx-user" style="font-size: 1.7rem; color: #3182ce;"></i>
                  </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <a class="dropdown-item" href="#">
                      <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                          <div class="avatar avatar-online">
                            <div class="avatar avatar-online" style="background: #e3f0fc; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%;">
                              <i class="bx bx-user" style="font-size: 1.7rem; color: #3182ce;"></i>
                            </div>
                          </div>
                        </div>
                        <div class="flex-grow-1">
                          <span class="fw-semibold d-block"><?php echo isset($_SESSION['agent_name']) ? $_SESSION['agent_name'] : 'Agent'; ?></span>
                          <small class="text-muted">Agent</small>
                        </div>
                      </div>
                    </a>
                  </li>
                  <li>
                    <div class="dropdown-divider"></div>
                  </li>
                  <!-- <li>
                    <a class="dropdown-item" href="#" id="profile-menu">
                      <i class="bx bx-user me-2"></i>
                      <span class="align-middle">My Profile</span>
                    </a>
                  </li> -->
                  <li>
                    <a class="dropdown-item" href="#" id="settings-menu">
                      <i class="bx bx-cog me-2"></i>
                      <span class="align-middle">Settings</span>
                    </a>
                  </li>
                  <li>
                    <div class="dropdown-divider"></div>
                  </li>
                  <li>
                    <a class="dropdown-item" href="#" id="logout-menu">
                      <i class="bx bx-power-off me-2"></i>
                      <span class="align-middle">Log Out</span>
                    </a>
                  </li>
                </ul>
              </li>
              <!--/ User -->
            </ul>
          </div>
        </nav>
        <!-- / Navbar -->

        <!-- Content wrapper -->
        <div class="content-wrapper">
          <!-- Content -->

          <div class="container-xxl flex-grow-1 container-p-y" id="content">


          </div>
          <!-- / Content -->

          <!-- Footer -->
          <footer class="content-footer footer bg-footer-theme">
            <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
              <div class="mb-2 mb-md-0">
                ©
                <script>
                  document.write(new Date().getFullYear());
                </script>
                , made by
                <a href="https://kdu.ac.lk" target="_blank" class="footer-link fw-bolder">CITS & DS - KDU</a>
              </div>

            </div>
          </footer>
          <!-- / Footer -->

          <div class="content-backdrop fade"></div>
        </div>
        <!-- Content wrapper -->
      </div>
      <!-- / Layout page -->
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
  </div>
  <!-- / Layout wrapper -->


  <!-- Core JS -->
  <!-- build:js assets/vendor/js/core.js -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!--  <script src="../assets/vendor/libs/jquery/jquery.js"></script> -->
  <!-- <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.12.1/datatables.min.js"></script> -->
  <script src="../../assets/vendor/libs/popper/popper.js"></script>
  <script src="../../assets/vendor/js/bootstrap.js"></script>
  <script src="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
  <script src="../../assets/vendor/js/menu.js"></script>
  <!-- endbuild -->

  <!-- Vendors JS -->
  <script src="../../assets/vendor/libs/apex-charts/apexcharts.js"></script>

  <!-- Main JS -->
  <script src="../assets/js/main.js"></script>

  <!-- Page JS -->
  <script src="../assets/js/dashboards-analytics.js"></script>

  <!-- Place this tag in your head or just before your close body tag. -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
  <!-- Make agent code available to JS -->
  <script>
    window.agentCode = "<?php echo isset($_SESSION['agent_code']) ? $_SESSION['agent_code'] : ''; ?>";
  </script>
  <script src="../assets/js/dashboard.js"></script>
  <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>

</body>

</html>