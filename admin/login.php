<?php
if (isset($_POST['id_usuario'])) {
    ini_set("session.cookie_lifetime", "888800");
    ini_set('session.gc_maxlifetime', "888800");
    session_start();
    $_SESSION["id_administrador"] = $_POST["id_usuario"];
    $_SESSION["codemp"] = $_POST["codemp"];
    $_SESSION["codsuc"] = $_POST["codsuc"];
    $_SESSION["codtienda"] = $_POST["codtienda"];
    $_SESSION["nomtienda"] = $_POST["nomtienda"];
    // $_SESSION["rol_id"] = $_POST["rol_id"];
    // $_SESSION["nombres"] = $_POST["nombres"];
    // $_SESSION["apellidos"] = $_POST["apellidos"];
    // $_SESSION["correo"] = $_POST["correo"];
    // $_SESSION["imagen"] = $_POST["imagen"];
    // $_SESSION["nombre_rol_user"] = $_POST["nombre_rol_user"];


    /* TRAER PERMISOS DEL USUARIO */
    header("Location: cotizador.php");
} else {
    session_start();
    session_unset();
    session_destroy();
}
?>
<!DOCTYPE html>
<html data-bs-theme="light" lang="en-US" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <!-- ===============================================-->
    <!--    Document Title-->
    <!-- ===============================================-->
    <title>COMISARIATO DEL CONSTRUCTOR | LOGIN</title>


    <!-- ===============================================-->
    <!--    Favicons-->
    <!-- ===============================================-->
    <link rel="apple-touch-icon" sizes="180x180" href="http://192.168.1.153/img/ICONO_CONSTRUCTOR.png">
    <link rel="icon" type="image/png" sizes="32x32" href="http://192.168.1.153/img/ICONO_CONSTRUCTOR.png">
    <link rel="icon" type="image/png" sizes="16x16" href="http://192.168.1.153/img/ICONO_CONSTRUCTOR.png">
    <link rel="shortcut icon" type="image/x-icon" href="http://192.168.1.153/img/ICONO_CONSTRUCTOR.png">
    <link rel="manifest" href="../theme/public/assets/img/favicons/manifest.json">
    <meta name="msapplication-TileImage" content="http://192.168.1.153/img/ICONO_CONSTRUCTOR.png">
    <meta name="theme-color" content="#ffffff">
    <script src="../theme/public/assets/js/config.js"></script>
    <script src="../theme/public/vendors/simplebar/simplebar.min.js"></script>


    <!-- ===============================================-->
    <!--    Stylesheets-->
    <!-- ===============================================-->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">
    <link href="../theme/public/vendors/simplebar/simplebar.min.css" rel="stylesheet">
    <link href="../theme/public/assets/css/theme-rtl.css" rel="stylesheet" id="style-rtl">
    <link href="../theme/public/assets/css/theme.css" rel="stylesheet" id="style-default">
    <link href="../theme/public/assets/css/user-rtl.css" rel="stylesheet" id="user-style-rtl">
    <link href="../theme/public/assets/css/user.css" rel="stylesheet" id="user-style-default">
    <link href="../theme/public/assets/css/estilos_personalizados.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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
</head>

<body>

    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->
    <main class="main" id="top">
        <div class="container-fluid">
            <div class="row min-vh-100 flex-center g-0">
                <div class="col-lg-8 col-xxl-5 py-3 position-relative"><img class="bg-auth-circle-shape" src="../../../assets/img/icons/spot-illustrations/bg-shape.png" alt="" width="250"><img class="bg-auth-circle-shape-2" src="../../../assets/img/icons/spot-illustrations/shape-1.png" alt="" width="150">
                    <div class="col-sm-12 col-md-8 px-sm-0 align-self-center mx-auto py-5">
                        <div class="row justify-content-center g-0">
                            <div class="col-lg-12 col-xl-12 col-xxl-12">
                                <div class="card">
                                    <div class="card-header text-center p-2 bg-light"><a class="font-sans-serif fw-bolder fs-4 z-1 position-relative link-light" href="login.php" data-bs-theme="light"><img src="../img/LOGO_CONSTRUCTOR.png" width="260" height="80"></a></div>
                                    <div class="card-body p-4">
                                        <div class="row flex-between-center">
                                            <div class="col-auto">
                                                <h3>Iniciar Sesión</h3>
                                            </div>
                                        </div>
                                        <form id="formlogin" action="login.php" method="POST">

                                            <div class="mb-3">
                                                <label class="form-label" for="username">Usuario</label>
                                                <input class="form-control" id="username" type="text" placeholder="Ingrese el usuario"/>
                                            </div>
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <label class="form-label" for="password">Contraseña</label>
                                                </div>
                                                <input class="form-control" id="password" type="password" placeholder="Ingrese la Contraseña"/>
                                            </div>
                                        </form>
                                        <div class="mb-3">
                                            <button class="btn btn-primary2 text-dark d-block fw-bold w-100 mt-3" type="submit" name="submit" onclick="logear()">Login</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
    </main>



    <!-- ===============================================-->
    <!--    JavaScripts-->
    <!-- ===============================================-->
    <script src="../theme/public/vendors/popper/popper.min.js"></script>
    <script src="../theme/public/vendors/bootstrap/bootstrap.min.js"></script>
    <script src="../theme/public/vendors/anchorjs/anchor.min.js"></script>
    <script src="../theme/public/vendors/is/is.min.js"></script>
    <script src="../theme/public/vendors/fontawesome/all.min.js"></script>
    <script src="../theme/public/vendors/lodash/lodash.min.js"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=window.scroll"></script>
    <script src="../theme/public/vendors/list.js/list.min.js"></script>
    <script src="../theme/public/assets/js/theme.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <!-- SELECT 2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Conexión API js -->
    <script src="js/login.js?v1.0.33"></script>

    <!-- Alerts js -->
    <script src="js/alerts.js"></script>


</body>

</html>