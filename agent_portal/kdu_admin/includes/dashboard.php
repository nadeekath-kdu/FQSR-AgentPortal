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
  data-assets-path="../../assets/"
  data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8" />
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>Agent Portal - KDU - Admin</title>

  <meta name="description" content="" />
  <link rel="icon" type="image/x-icon" href="../../assets/img/favicon/Kdufav.png" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/vendor/fonts/boxicons.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

  <link rel="stylesheet" href="../../assets/vendor/css/core.css" class="template-customizer-core-css" />
  <link rel="stylesheet" href="../../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="../../assets/css/demo.css" />


  <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="../../assets/vendor/libs/apex-charts/apex-charts.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-components-web/14.0.0/material-components-web.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.css" />

  <!-- Enhanced Professional Dashboard CSS -->
  <link rel="stylesheet" href="../assets/css/admin-dashboard.css" />

</head>

<body>
  <!-- Layout wrapper -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <!-- Menu -->

      <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
        <div class="app-brand demo d-flex align-items-center justify-content-between px-3 py-2 bg-light rounded shadow-sm">
          <!-- Logo and Branding -->
          <a href="dashboard.php" class="app-brand-link d-flex align-items-center text-decoration-none">
            <img src="../../assets/img/favicon/Kdufav.png" alt="Logo" width="32" height="32" class="me-2">
            <div class="d-flex flex-column lh-sm">
              <span class="text-primary fw-bold fs-5">Agent Portal</span>
              <small class="text-muted fw-semibold">Admin Panel</small>
            </div>
          </a>

          <!-- Menu Toggle Icons -->
          <div class="d-flex align-items-center gap-2">
            <!-- Collapse/Expand (desktop + mobile) -->
            <button type="button" id="sidebarCollapseBtn" class="btn btn-sm btn-outline-secondary" title="Collapse/Expand menu" aria-label="Toggle sidebar" aria-expanded="true">
              <i class="bx bx-chevrons-left"></i>
            </button>
            <!-- Default template toggle for overlay on small screens -->
            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large d-xl-none" title="Toggle menu">
              <i class="bx bx-chevron-left bx-sm align-middle text-dark"></i>
            </a>
          </div>
        </div>



        <div class="menu-inner-shadow"></div>

        <ul class="menu-inner py-1">
          <!-- Dashboard -->
          <li class="menu-item active">
            <a href="dashboard.php" class="menu-link">
              <i class="menu-icon tf-icons bx bx-home-circle"></i>
              <div data-i18n="Analytics">Dashboard</div>
            </a>
          </li>
          <li class="menu-item ">
            <a href="javascript:void(0);" class="menu-link" id="pendinglist">
              <i class="menu-icon tf-icons bx bx-cube-alt"></i>
              <div data-i18n="Analytics">Pending Requests</div>
            </a>
          </li>

          <!-- Layouts -->

          <li class="menu-header small text-uppercase">
            <span class="menu-header-text">View Details</span>
          </li>
          <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link" id="agencies">
              <i class="menu-icon tf-icons bx bx-detail"></i>
              <div data-i18n="Account Settings">Agenies</div>
            </a>
          </li>
          <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link" id="applications">
              <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
              <div data-i18n="Authentications">Applications</div>
            </a>
          </li>

          <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Password Management</span>
          </li>
          <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link">
              <i class="menu-icon tf-icons bx bx-key"></i>
              <div data-i18n="Account Settings">Change Password</div>
            </a>
          </li>
          <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link" id="logout">
              <i class="menu-icon tf-icons bx bx-user"></i>
              <div data-i18n="Authentications">Logout</div>
            </a>
          </li>
        </ul>
      </aside>
      <!-- / Menu -->

      <!-- Layout container -->
      <div class="layout-page">
        <!-- Content wrapper -->
        <div class="content-wrapper" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; position: relative;">
          <!-- Background overlay -->
          <!-- <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: url('../../assets/img/backgrounds/bgimg.svg') center/cover; opacity: 0.1; z-index: 0;"></div>
 -->
          <!-- Content -->
          <div class="container-xxl flex-grow-1 container-p-y" id="content" style="position: relative; z-index: 1; background: rgba(255, 255, 255, 0.95); margin: 4px; border-radius: 4px; padding: 4px; box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2);">
            <!-- Content will be loaded here dynamically -->
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

  <!-- Floating toggle button for collapsed state -->
  <button type="button" id="sidebarToggleFab" class="sidebar-toggle-fab" title="Expand menu" aria-label="Toggle sidebar" aria-expanded="false">
    <i class="bx bx-chevrons-right"></i>
  </button>


  <!-- Core JS -->
  <!-- build:js assets/vendor/js/core.js -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="../../assets/vendor/js/helpers.js"></script>
  <script src="../../assets/js/config.js"></script>
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
  <script src="../../assets/js/main.js"></script>

  <!-- Page JS -->
  <script src="../../assets/js/dashboards-analytics.js"></script>

  <!-- Place this tag in your head or just before your close body tag. -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
  <script src="../../assets/js/admin/dashboard.js"></script>
  <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>

  <!-- Enhanced Professional Dashboard JS -->
  <!-- <script src="../assets/js/admin-dashboard-enhanced.js"></script> -->

  <script>
    // Sidebar collapse toggle with persistence using Sneat's standard class
    (function() {
      const htmlEl = document.documentElement;
      const STORAGE_KEY = 'kdu_admin_menu_collapsed';
      const btn = document.getElementById('sidebarCollapseBtn');
      const btnFab = document.getElementById('sidebarToggleFab');

      function setCollapsedState(isCollapsed) {
        if (isCollapsed) {
          htmlEl.classList.add('layout-menu-collapsed');
          if (btn) {
            btn.setAttribute('aria-expanded', 'false');
            btn.setAttribute('title', 'Expand menu');
            btn.innerHTML = '<i class="bx bx-chevrons-right"></i>';
          }
          if (btnFab) {
            btnFab.setAttribute('aria-expanded', 'false');
            btnFab.setAttribute('title', 'Expand menu');
            btnFab.innerHTML = '<i class="bx bx-chevrons-right"></i>';
          }
        } else {
          htmlEl.classList.remove('layout-menu-collapsed');
          if (btn) {
            btn.setAttribute('aria-expanded', 'true');
            btn.setAttribute('title', 'Collapse menu');
            btn.innerHTML = '<i class="bx bx-chevrons-left"></i>';
          }
          if (btnFab) {
            btnFab.setAttribute('aria-expanded', 'true');
            btnFab.setAttribute('title', 'Collapse menu');
            btnFab.innerHTML = '<i class="bx bx-chevrons-left"></i>';
          }
        }
        try {
          localStorage.setItem(STORAGE_KEY, isCollapsed ? '1' : '0');
        } catch (e) {}
      }

      // Initialize from storage
      try {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (saved === '1') setCollapsedState(true);
      } catch (e) {}

      // Click handler
      if (btn) {
        btn.addEventListener('click', function() {
          const isCollapsed = htmlEl.classList.contains('layout-menu-collapsed');
          setCollapsedState(!isCollapsed);
        });
      }
      if (btnFab) {
        btnFab.addEventListener('click', function() {
          const isCollapsed = htmlEl.classList.contains('layout-menu-collapsed');
          setCollapsedState(!isCollapsed);
        });
      }
    })();
  </script>

</body>

</html>