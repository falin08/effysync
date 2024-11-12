<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Dashboard</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="vendors/feather/feather.css">
  <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <link rel="stylesheet" href="vendors/datatables.net-bs4/dataTables.bootstrap4.css">
  <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" type="text/css" href="js/select.dataTables.min.css">
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="css/vertical-layout-light/style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="images/favicon.png" />
</head>
<body>
  <!-- <div class="container-scroller">     -->
    <div class="page-body-wrapper" style="margin: 0; padding: 0;">
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <div class="sidebar-logo text-center my-3">
         <img src="images/logo.png" style="max-width: 100px; height: auto;" class="img-fluid" alt="Company Logo">
        </div>
        <ul class="nav">
          <li class="nav-item">
            <a class="nav-link" href="{{url ('/dashboard')}}">
              <i class="icon-grid menu-icon"></i>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{url ('/kandang')}}" aria-expanded="false" aria-controls="form-elements">
              <i class="icon-columns menu-icon"></i>
              <span class="menu-title">Kandang</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{url ('/pakan')}}" aria-expanded="false" aria-controls="form-elements">
              <i class="icon-paper menu-icon"></i>
              <span class="menu-title">Stok Pakan</span>
              <!-- <i class="menu-arrow"></i> -->
            </a>
            <div class="collapse" id="form-elements">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"><a class="nav-link" href="pages/forms/basic_elements.html">Basic Elements</a></li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{url ('/penyakit')}}" aria-expanded="false" aria-controls="form-elements">
              <i class="icon-contract menu-icon"></i>
              <span class="menu-title">Penyakit</span>
              <!-- <i class="menu-arrow"></i> -->
            </a>
            <div class="collapse" id="form-elements">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"><a class="nav-link" href="pages/forms/basic_elements.html">Basic Elements</a></li>
              </ul>
            </div>
          </li>
        </ul>
      </nav>
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="grid-margin">
              <div class="row">
                <h3 class="font-weight-bold">Welcome '{{session('name')}}'</h3>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="grid-margin ">
              <div class="row">
                <div class="col-md-3" style="margin-right: 20px;" >
                  <div class="card border border-warning" style="width: 120px; height: 80px;">
                    <div class="card-body">
                      <p >Total Telur</p>
                      <p class="fs-15">4006</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-3"  style="margin-right: 20px;">
                  <div class="card border border-warning" style="width: 150px; height: 80px;">
                    <div class="card-body">
                      <p >Produksi Telur</p>
                      <p class="fs-15 ">61344</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-3" style="margin-left: 40px;">
                  <div class="card border border-warning"style="width: 120px; height: 80px;">
                    <div class="card-body">
                      <p>Stok Pakan</p>
                      <p class="fs-15 mb-2">61344</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-6 grid-margin grid-margin-lg-0 stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Grafik Kondisi Unggas</h4>
                  <canvas id="pieChart"></canvas>
                </div>
              </div>
            </div>
            <div class="col-lg-6 grid-margin grid-margin-lg-0 stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Grafik Stok Pakan</h4>
                  <canvas id="doughnutChart"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.html -->
        <footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2021.  Premium <a href="https://www.bootstrapdash.com/" target="_blank">Bootstrap admin template</a> from BootstrapDash. All rights reserved.</span>
            <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Hand-crafted & made with <i class="ti-heart text-danger ml-1"></i></span>
          </div>
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Distributed by <a href="https://www.themewagon.com/" target="_blank">Themewagon</a></span> 
          </div>
        </footer> 
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>   
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  <!-- plugins:js -->
  <script src="vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <script src="vendors/chart.js/Chart.min.js"></script>
  <script src="vendors/datatables.net/jquery.dataTables.js"></script>
  <script src="vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>
  <script src="js/dataTables.select.min.js"></script>

  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="js/off-canvas.js"></script>
  <script src="js/hoverable-collapse.js"></script>
  <script src="js/template.js"></script>
  <script src="js/settings.js"></script>
  <script src="js/todolist.js"></script>
  <!-- endinject -->
  <!-- Custom js for this page-->
  <script src="js/dashboard.js"></script>
  <script src="js/chart.js"></script>

  <script src="js/Chart.roundedBarCharts.js"></script>
  <!-- End custom js for this page-->
</body>

</html>

