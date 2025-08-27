<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
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

  <title>KDU-FSR</title>

  <meta name="description" content="" />
  <link rel="icon" type="image/x-icon" href="../assets/img/favicon/Kdufav.png" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet" />

  <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

  <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
  <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="../assets/css/demo.css" />
  <link rel="stylesheet" href="../assets/css/content.css" />

  <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
  <script src="../assets/vendor/js/helpers.js"></script>

  <script src="../assets/js/config.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-components-web/14.0.0/material-components-web.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.css" />

</head>

<body>
  <!-- Main Content -->
  <div class="main-content background-image min-vh-100 d-flex flex-column">
    <div class="container py-4">
      <div class="row justify-content-center">
        <!-- <div class="col-12"> --> <!-- Removed col-lg-10 to allow full width -->
        <!-- Content Area -->
        <div class="card shadow-sm">
          <div class="card-header bg-transparent border-0 pt-4">
            <img src="../assets/img/kdu/logo.jpg" alt="KDU Logo" class="d-block mx-auto mb-3" style="max-height: 80px;">
            <h4 class="text-center mb-0">Application for Admission of Students with Foreign Qualifications for the Academic Year
              <span id="academicYear"></span>
            </h4>
          </div>
          <div class="card-body p-0" id="content"> <!-- Added p-0 to remove padding -->
            <!-- Dynamic content will be loaded here -->
          </div>
        </div>
        <!--  </div> -->
      </div>

      <!-- Footer -->
      <footer class="footer bg-transparent mt-auto">
        <div class="text-center py-3">
          <div class="mb-2">
            <a href="https://www.kdu.ac.lk" target="_blank" class="footer-link fw-bolder">Kotelawala Defence University</a>
          </div>
          <div class="text-muted">
            © <?php echo date('Y'); ?> Foreign Student Registration Portal
          </div>
        </div>
      </footer>
      <!-- / Footer -->
    </div>
  </div>
  <!-- / Main Content -->


  <!-- Core JS -->
  <!-- build:js assets/vendor/js/core.js -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!--  <script src="../assets/vendor/libs/jquery/jquery.js"></script> -->
  <!-- <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.12.1/datatables.min.js"></script> -->
  <script src="../assets/vendor/libs/popper/popper.js"></script>
  <script src="../assets/vendor/js/bootstrap.js"></script>
  <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
  <script src="../assets/vendor/js/menu.js"></script>
  <!-- endbuild -->

  <!-- Vendors JS -->
  <script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>

  <!-- Main JS -->
  <script src="../assets/js/main.js"></script>

  <!-- Page JS -->
  <script src="../assets/js/dashboards-analytics.js"></script>

  <!-- Place this tag in your head or just before your close body tag. -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
  <script src="../assets/js/app/dashboard.js"></script>
  <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>

</body>

</html>