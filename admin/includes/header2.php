<?php
session_start();
ini_set("display_errors", 0);
header('Cache-Control: no cache'); //no cache
// session_cache_limiter('private_no_expire'); // works
date_default_timezone_set('America/Guayaquil');
if (!isset($_SESSION["id_administrador"])) {
    echo "<script>window.location.href = 'login.php'</script>";
}
$idUsuario = $_SESSION["id_administrador"];
$codemp = $_SESSION["codemp"];
$codsuc = $_SESSION["codsuc"];
$codtienda = $_SESSION["codtienda"];
$nomtienda = $_SESSION["nomtienda"];

echo "<input type='hidden' id='idAdministrador' value='$idUsuario'>";
echo "<input type='hidden' id='codemp' value='$codemp'>";
echo "<input type='hidden' id='codsuc' value='$codsuc'>";
echo "<input type='hidden' id='codtienda' value='$codtienda'>";
echo "<input type='hidden' id='nomtienda' value='$nomtienda'>";

?>
<!DOCTYPE html>
<html class="navbar-vertical-collapsed" data-bs-theme="light" lang="en-US" dir="ltr">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">


  <!-- ===============================================-->
  <!--    Document Title-->
  <!-- ===============================================-->
  <title>Comisariato del Constructor</title>


  <!-- ===============================================-->
  <!--    Favicons-->
  <!-- ===============================================-->
  <link rel="apple-touch-icon" sizes="180x180" href="http://192.168.1.153/img/ICONO_CONSTRUCTOR.png">
  <link rel="icon" type="image/png" sizes="32x32" href="http://192.168.1.153/img/ICONO_CONSTRUCTOR.png">
  <link rel="icon" type="image/png" sizes="16x16" href="http://192.168.1.153/img/ICONO_CONSTRUCTOR.png">
  <link rel="shortcut icon" type="image/x-icon" href="http://192.168.1.153/img/ICONO_CONSTRUCTOR.png">
  <link rel="manifest" href="../../../assets/img/favicons/manifest.json">
  <meta name="msapplication-TileImage" content="http://192.168.1.153/img/ICONO_CONSTRUCTOR.png">
  <meta name="theme-color" content="#ffffff">
  <script src="../theme/public/assets/js/config.js"></script>
  <script src="../theme/public/vendors/simplebar/simplebar.min.js"></script>
  <link href="../theme/public/assets/css/estilos_personalizados.css" rel="stylesheet">



  <!-- ===============================================-->
  <!--    Stylesheets-->
  <!-- ===============================================-->
  <link href="../theme/public/vendors/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="../theme/public/vendors/glightbox/glightbox.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">
  <link href="../theme/public/vendors/simplebar/simplebar.min.css" rel="stylesheet">
  <link href="../theme/public/assets/css/theme-rtl.css" rel="stylesheet" id="style-rtl">
  <link href="../theme/public/assets/css/theme.css" rel="stylesheet" id="style-default">
  <link href="../theme/public/assets/css/user-rtl.css" rel="stylesheet" id="user-style-rtl">
  <link href="../theme/public/assets/css/user.css" rel="stylesheet" id="user-style-default">
  
  <!-- LIBRERÍA DE SELECT2 -->
  <!-- <script src="../theme/public/vendors/select2/select2.min.css"></script>
  <script src="../theme/public/vendors/select2-bootstrap-5-theme/select2-bootstrap-5-theme.min.css"></script> -->
  <!-- Toastr CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

  <!-- <link href="../theme/public/vendors/choices/choices.min.css" rel="stylesheet" />
  <link href="../theme/public/vendors/select2/select2.min.css"> -->
  </link>
  <!-- <link href="../theme/public/vendors/select2-bootstrap-5-theme/select2-bootstrap-5-theme.min.css">
  <link href="../theme/public/vendors/datatables.net-bs5/dataTables.bootstrap5.min.css" /> -->
    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-1.13.6/b-2.4.1/b-colvis-2.4.1/b-html5-2.4.1/b-print-2.4.1/datatables.min.css" rel="stylesheet">
  </link>
  <script>
    var isRTL = JSON.parse(localStorage.getItem('isRTL'));
    if (isRTL) {
      var linkDefault = document.getElementById('style-default');
      var userLinkDefault = document.getElementById('user-style-default');
      linkDefault.setAttribute('disabled', true);
      userLinkDefault.setAttribute('disabled', true);
      document.querySelector('html').setAttribute('dir', 'rtl');
    } else {
      var linkRTL = document.getElementById('style-rtl');
      var userLinkRTL = document.getElementById('user-style-rtl');
      linkRTL.setAttribute('disabled', true);
      userLinkRTL.setAttribute('disabled', true);
    }
  </script>

  <style>
    .choices__inner::before {
      display: none !important;
    }
  </style>
</head>


<body>

  <!-- ===============================================-->
  <!--    Main Content-->
  <!-- ===============================================-->
  <main class="main" id="top">
    <div class="container-fluid" data-layout="container">
      <script>
        var isFluid = JSON.parse(localStorage.getItem('isFluid'));
        if (isFluid) {
          var container = document.querySelector('[data-layout]');
          container.classList.remove('container');
          container.classList.add('container-fluid');
        }
      </script>
      <nav class="navbar navbar-light navbar-glass navbar-top navbar-expand-lg">
        <a class="navbar-brand me-1 me-sm-3" href="cotizador.php">
          <div class="d-flex align-items-center"><img class="me-2" src="../img/LOGO_CONSTRUCTOR.png" height="80" width="260" /><span class="font-sans-serif"></span>
          </div>
        </a>
        <ul class="navbar-nav align-items-center d-none d-lg-block">
              <li class="nav-item">
                <a class="btn btn-md text-white fw-bold" type="button" style="background-color: #e84e0f" href="ver_cotizaciones.php"> Mis Cotizaciones</a>
                <a class="btn btn-md text-white fw-bold" type="button" style="background-color: #0f3d53" href="ver_clientes.php"> Mis Clientes</a>
              </li>
            </ul>
        <ul class="navbar-nav navbar-nav-icons ms-auto flex-row align-items-center">
          <li class="nav-item">
            <a class="nav-link px-0 notification-indicator notification-indicator-warning notification-indicator-fill fa-icon-wait d-flex justify-content-center align-items-center fw-bold" style="color: #e84e0f" id="navbarDropdownNotification" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-hide-on-body-scroll="data-hide-on-body-scroll"> <span class="fas fa-shopping-cart" data-fa-transform="shrink-7" style="font-size: 33px;"></span><span class="notification-indicator-number bg-white rounded-circle text-dark" id="cartCount">0</span></a>
            <div class="dropdown-menu dropdown-caret dropdown-caret dropdown-menu-end dropdown-menu-card dropdown-menu-notification dropdown-caret-bg" aria-labelledby="navbarDropdownNotification">
              <div class="card card-notification shadow-none">
                <div class="card-header bg-light">
                  <div class="row justify-content-between align-items-center">
                    <div class="col-auto">
                      <h5 class="card-header-title mb-0">Mi Carrito</h5>
                    </div>
                  </div>
                </div>
                <div class="scrollbar-overlay" style="max-height:19rem">
                  <ul class="list-group" id="cartItems">

                  </ul>
                </div>
                <div class="card-footer text-center border-top">
                  <a class="card-link d-block" type="button" onclick="irDetalleCotizar()" style="color: #0f3d53">Ir a cotizar</a>
                </div>
              </div>
            </div>
          </li>

          <li class="nav-item dropdown"><a class="nav-link pe-0 ps-2" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <div class="avatar avatar-xl">
                <img class="rounded-circle" src="https://cdn-icons-png.flaticon.com/512/2534/2534465.png" alt="" />

              </div>
            </a>
            <div class="dropdown-menu dropdown-caret dropdown-caret dropdown-menu-end py-0" aria-labelledby="navbarDropdownUser">
              <div class="bg-white dark__bg-1000 rounded-2 py-2">
                <a class="dropdown-item fw-bold text-danger" href="cotizador.php"><span>Comisariato del Constructor</span></a>

                <div class="dropdown-divider"></div>
                <!-- <a class="dropdown-item" href="#!">Set status</a>
                <a class="dropdown-item" href="../../../pages/user/profile.html">Profile &amp; account</a>-->
                <a class="dropdown-item" href="ver_cotizaciones.php">Mis Cotizaciones</a>
                <!-- <a class="dropdown-item" href="#!">Mi Datos Personales</a> -->

                <div class="dropdown-divider"></div>
                <!-- <a class="dropdown-item" href="../../../pages/user/settings.html">Settings</a>  -->
                <a class="dropdown-item" href="login.php">Cerrar Sesión</a>
              </div>
            </div>
          </li>
        </ul>
      </nav>
      <div class="content">