<?php

/**
 * Step 1: Require the Slim Framework using Composer's autoloader
 *
 * If you are not using Composer, you need to load Slim Framework with your own
 * PSR-4 autoloader.
 */
require '../vendor/autoload.php';

/*====================*/
require_once dirname(__DIR__) . '/include/DbHandler2.php';
//require dirname(__DIR__) . '/./libs/Slim/Slim.php';
/*====================*/

/**
 * Step 2: Instantiate a Slim application
 *
 * This example instantiates a Slim application using
 * its default settings. However, you will usually configure
 * your Slim application now by passing an associative array
 * of setting names and values into the application constructor.
 */
$app = new Slim\App();

/**
 * Step 3: Define the Slim application routes
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, `Slim::patch`, and `Slim::delete`
 * is an anonymous function.
 */
$app->get('/', function ($request, $response, $args) {
    $response->write("Welcome to Slim!");
    return $response;
});

$app->get('/hello[/{name}]', function ($request, $response, $args) {
    $response->write("Hello, " . $args['name']);
    return $response;
})->setArgument('name', 'World!');

/**
 * Step 4: Run the Slim application
 *
 * This method should be called last. This executes the Slim application
 * and returns the HTTP response to the HTTP client.
 */




/**
 *ADMINISTRADORES
 */

/* LOGIN */
$app->post('/abraldes/admin/login', function ($request, $response) {

    $usuario = $request->getParsedBody()['username'];
    $clave   = $request->getParsedBody()['password'];
    $response = array();
    $db = new DbHandler2();
    $resultado = $db->adminlogin($usuario, $clave);

    if ($resultado == 2) {
        $datosusuario = $db->getAdminByUsuario($usuario);
        $response["error"] = false;
        $response["msg"] = "Inicio éxitoso";
        $response["administrador"] = $datosusuario;
    } else if ($resultado == 1) {
        $response["error"] = true;
        $response["msg"] = "La contraseña es incorrecta.";
    } else {
        $response["error"] = true;
        $response["msg"] = "El usuario no se encuentra registrado";
    }
    echo json_encode($response);
});
/* LOGIN */


/* CREAR UN ADMINISTRADOR */
$app->post('/abraldes/admin/create/', function ($request, $response) {

    $usuario = $request->getParsedBody()['usuario'];
    $clave = $request->getParsedBody()['clave'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->createAdmin($usuario,  $clave);

    if ($resultado == RECORD_CREATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "El usuario ha sido creado con éxito.";
    } else if ($resultado == RECORD_ALREADY_EXISTED) {
        $response["error"] = true;
        $response["msg"] = "El usuario ya existe. Verifique nuevamente los datos";
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error en la creación del suario. Por favor vuelva a verificar en otro momento.";
    }
    echo json_encode($response);
});
/* CREAR UN ADMINISTRADOR */


/* OBTENER TODOS LOS ADMINISTRADORES */
$app->get('/abraldes/admin/all', function ($request, $response) {

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getAllAdmins();

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["administradores"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor verificar nuevamente";
    }
    echo json_encode($response);
});
/* OBTENER TODOS LOS ADMINISTRADORES */


/* EDITAR CLAVE ADMINISTRADORES */
$app->post('/abraldes/admin/editpassword/', function ($request, $response) {

    $usuario = $request->getParsedBody()['usuario'];
    $password   = $request->getParsedBody()['password'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->updateAdminPassword($usuario,  $password);

    if ($resultado != 0) {
        $response["error"] = false;
        $response["msg"] = "La contraseña ha sido actualizada";
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor vuelva a intentar en otro momento.";
    }
    echo json_encode($response);
});
/* EDITAR CLAVE ADMINISTRADORES */


/* OBTENER UN ADMIN POR ID */
$app->get('/abraldes/admin/{id}', function ($request, $response, $args) {
    $id_administrador = $args['id'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getAdminById($id_administrador);

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["administrador"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "El usuario no existe";
    }

    echo json_encode($response);
});
/* OBTENER UN ADMIN POR ID */

/* ELIMINAR ADMIN */
$app->post('/abraldes/admin/delete/', function ($request, $response) {

    $id_administrador = $request->getParsedBody()['id_administrador'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->deleteAdmin($id_administrador);

    if ($resultado == RECORD_UPDATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "El administrador ha sido eliminado.";
    } else if ($resultado == RECORD_UPDATED_FAILED) {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor vuelva a intentar en otro momento.";
    } else {
        $response["error"] = true;
        $response["msg"] = "El usuario no existe. Verifique nuevamente los datos";
    }
    echo json_encode($response);
});
/* ELIMINAR ADMIN */



/* OBTENER TODAS LAS SOLICITUDES DE HERRAMIENTAS */
$app->get('/abraldes/admin/herramientas/solicitudes/', function ($request, $response) {

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getAllSolicitudesHerramientas();

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["solicitudes"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor verificar nuevamente";
    }
    echo json_encode($response);
});
/* OBTENER TODAS LAS SOLICITUDES DE HERRAMIENTAS


/* CANCELAR SOLICITUD HERRAMIENTA */
$app->post('/abraldes/admin/herramienta/cancelar/', function ($request, $response) {

    $id_solicitud = $request->getParsedBody()['id_solicitud'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->cancelarSolicitudHerramienta($id_solicitud);

    if ($resultado == RECORD_UPDATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "La solicitud ha sido eliminada.";
    } else if ($resultado == RECORD_UPDATED_FAILED) {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor vuelva a intentar en otro momento.";
    } else {
        $response["error"] = true;
        $response["msg"] = "La solicitud no existe. Verifique nuevamente los datos";
    }
    echo json_encode($response);
});
/* CANCELAR SOLICITUD HERRAMIENTA */


/* APROBAR SOLICITUD HERRAMIENTA */
$app->post('/abraldes/admin/herramienta/aprobar/', function ($request, $response) {

    $id_solicitud = $request->getParsedBody()['id_solicitud'];
    $nota = $request->getParsedBody()['descripcion'];
    $id_administrador = $request->getParsedBody()['id_administrador'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->aprobarSolicitudHerramienta($id_solicitud, $nota, $id_administrador);

    if ($resultado == RECORD_UPDATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "La solicitud ha sido aprobada.";
    } else if ($resultado == RECORD_UPDATED_FAILED) {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor vuelva a intentar en otro momento.";
    } elseif ($resultado == "no_stock") {
        $response["msg"] = "No hay suficientes unidades para aprobar esta solcicitud.";
    } else {
        $response["error"] = true;
        $response["msg"] = "La solicitud no existe. Verifique nuevamente los datos";
    }
    echo json_encode($response);
});
/* APROBAR SOLICITUD HERRAMIENTA */


/* DEVOLUCION DE HERRAMIENTA */
$app->post('/abraldes/admin/herramienta/devolver/', function ($request, $response) {

    $id_solicitud = $request->getParsedBody()['id_solicitud'];
    $nota = $request->getParsedBody()['descripcion'];
    $id_administrador = $request->getParsedBody()['id_administrador'];


    $response = array();
    $db = new DbHandler2();
    $resultado = $db->devolverHerramienta($id_solicitud, $nota, $id_administrador);

    if ($resultado == RECORD_UPDATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "La solicitud ha sido actualizada.";
    } else if ($resultado == RECORD_UPDATED_FAILED) {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor vuelva a intentar en otro momento.";
    } else {
        $response["error"] = true;
        $response["msg"] = "La solicitud no existe. Verifique nuevamente los datos";
    }
    echo json_encode($response);
});
/* DEVOLUCION DE HERRAMIENTA */



/* OBTENER TODAS LAS SOLICITUDES DE PARTES */
$app->get('/abraldes/admin/partes/solicitudes/', function ($request, $response) {

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getAllSolicitudesPartes();

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["solicitudes"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor verificar nuevamente";
    }
    echo json_encode($response);
});
/* OBTENER TODAS LAS SOLICITUDES DE PARTES


/* CANCELAR SOLICITUD PARTE */
$app->post('/abraldes/admin/parte/cancelar/', function ($request, $response) {

    $id_solicitud = $request->getParsedBody()['id_solicitud'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->cancelarSolicitudParte($id_solicitud);

    if ($resultado == RECORD_UPDATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "La solicitud ha sido eliminada.";
    } else if ($resultado == RECORD_UPDATED_FAILED) {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor vuelva a intentar en otro momento.";
    } else {
        $response["error"] = true;
        $response["msg"] = "La solicitud no existe. Verifique nuevamente los datos";
    }
    echo json_encode($response);
});
/* CANCELAR SOLICITUD PARTE */


/* APROBAR SOLICITUD DE PARTE */
$app->post('/abraldes/admin/parte/aprobar/', function ($request, $response) {

    $id_solicitud = $request->getParsedBody()['id_solicitud'];
    $nota = $request->getParsedBody()['descripcion'];
    $id_administrador = $request->getParsedBody()['id_administrador'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->aprobarSolicitudParte($id_solicitud, $nota, $id_administrador);

    if ($resultado == RECORD_UPDATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "La solicitud ha sido aprobada.";
    } else if ($resultado == RECORD_UPDATED_FAILED) {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor vuelva a intentar en otro momento.";
    } elseif ($resultado == "no_stock") {
        $response["msg"] = "No hay suficientes unidades para aprobar esta solcicitud.";
    } else {
        $response["error"] = true;
        $response["msg"] = "La solicitud no existe. Verifique nuevamente los datos";
    }
    echo json_encode($response);
});
/* APROBAR SOLICITUD DE PARTE */


/* DEVOLUCION DE PARTE */
$app->post('/abraldes/admin/parte/devolver/', function ($request, $response) {

    $id_solicitud = $request->getParsedBody()['id_solicitud'];
    $nota = $request->getParsedBody()['descripcion'];
    $id_administrador = $request->getParsedBody()['id_administrador'];
    $cantidad_devuelta = $request->getParsedBody()['cantidad_devuelta'];


    $response = array();
    $db = new DbHandler2();
    $resultado = $db->devolverParte($id_solicitud, $nota, $id_administrador, $cantidad_devuelta);

    if ($resultado == RECORD_UPDATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "La solicitud ha sido actualizada.";
    } else if ($resultado == RECORD_UPDATED_FAILED) {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor vuelva a intentar en otro momento.";
    } else {
        $response["error"] = true;
        $response["msg"] = "La solicitud no existe. Verifique nuevamente los datos";
    }
    echo json_encode($response);
});
/* DEVOLUCION DE PARTE */



/**
 *ADMINISTRADORES
 */


/**
 *CLIENTES
 */

/* CREAR UN CLIENTE */
$app->post('/abraldes/cliente/create/', function ($request, $response) {

    $nombres = $request->getParsedBody()['nombres'];
    $apellidos = $request->getParsedBody()['apellidos'];
    $cedula = $request->getParsedBody()['cedula'];
    $telefono = $request->getParsedBody()['telefono'];
    $correo = $request->getParsedBody()['correo'];
    $direccion = $request->getParsedBody()['direccion'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->createCliente($nombres, $apellidos, $cedula, $telefono, $correo, $direccion);

    if ($resultado == RECORD_CREATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "El cliente ha sido creado con éxito.";
    } else if ($resultado == RECORD_ALREADY_EXISTED) {
        $response["error"] = true;
        $response["msg"] = "Ya existe un cliente con esta cédula. Verifique nuevamente los datos";
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error en la creación del cliente. Por favor vuelva a verificar en otro momento.";
    }
    echo json_encode($response);
});
/* CREAR UN CLIENTE */

/* EDITAR CLIENTE */
$app->post('/abraldes/cliente/edit/', function ($request, $response) {

    $id_cliente = $request->getParsedBody()['id_cliente'];
    $nombres = $request->getParsedBody()['nombres'];
    $apellidos = $request->getParsedBody()['apellidos'];
    $cedula = $request->getParsedBody()['cedula'];
    $telefono = $request->getParsedBody()['telefono'];
    $correo = $request->getParsedBody()['correo'];
    $direccion = $request->getParsedBody()['direccion'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->editCliente($id_cliente, $nombres, $apellidos, $cedula, $telefono, $correo, $direccion);

    if ($resultado == RECORD_UPDATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "El cliente ha sido editado con éxito.";
    } else if ($resultado == RECORD_UPDATED_FAILED) {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor vuelva a intentar en otro momento.";
    } else {
        $response["error"] = true;
        $response["msg"] = "El cliente no existe. Verifique nuevamente los datos";
    }
    echo json_encode($response);
});
/* EDITAR CLIENTE */

/* OBTENER TODOS LOS CLIENTES */
$app->get('/abraldes/clientes/all', function ($request, $response) {

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getAllClientes();

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["clientes"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor verificar nuevamente";
    }
    echo json_encode($response);
});
/* OBTENER TODOS LOS CLIENTES */


/* OBTENER UN CLIENTE POR ID */
$app->get('/abraldes/cliente/{id}', function ($request, $response, $args) {
    $id_cliente = $args['id'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getClienteById($id_cliente);

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["cliente"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "El cliente no existe";
    }

    echo json_encode($response);
});
/* OBTENER UN CLIENTE POR ID */

/* ELIMINAR CLIENTE */
$app->post('/abraldes/cliente/delete/', function ($request, $response) {

    $id_cliente = $request->getParsedBody()['id_cliente'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->deleteCliente($id_cliente);

    if ($resultado == RECORD_UPDATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "El cliente ha sido eliminado.";
    } else if ($resultado == RECORD_UPDATED_FAILED) {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor vuelva a intentar en otro momento.";
    } else {
        $response["error"] = true;
        $response["msg"] = "El cliente no existe. Verifique nuevamente los datos";
    }
    echo json_encode($response);
});
/* ELIMINAR CLIENTE */

/**
 *CLIENTES
 */


/**
 *TECNICOS
 */

/* LOGIN */
$app->post('/abraldes/tecnico/login', function ($request, $response) {

    $usuario = $request->getParsedBody()['username'];
    $clave   = $request->getParsedBody()['password'];
    $response = array();
    $db = new DbHandler2();
    $resultado = $db->tecnicoLogin($usuario, $clave);

    if ($resultado == 2) {
        $datosusuario = $db->getTecnicoByUsuario($usuario);
        $response["error"] = false;
        $response["msg"] = "Inicio éxitoso";
        $response["tecnico"] = $datosusuario;
    } else if ($resultado == 1) {
        $response["error"] = true;
        $response["msg"] = "La contraseña es incorrecta.";
    } else {
        $response["error"] = true;
        $response["msg"] = "El usuario no se encuentra registrado";
    }
    echo json_encode($response);
});
/* LOGIN */


/* CREAR UN TECNICO */
$app->post('/abraldes/tecnico/create/', function ($request, $response) {

    $nombres = $request->getParsedBody()['nombres'];
    $apellidos = $request->getParsedBody()['apellidos'];
    $telefono = $request->getParsedBody()['telefono'];
    $correo = $request->getParsedBody()['correo'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->createTecnico($nombres, $apellidos, $telefono, $correo);

    if ($resultado == RECORD_CREATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "El tecnico ha sido creado con éxito.";
    } else if ($resultado == RECORD_ALREADY_EXISTED) {
        $response["error"] = true;
        $response["msg"] = "Ya existe un tecnico con este usuario. Verifique nuevamente los datos";
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error en la creación del tecnico. Por favor vuelva a verificar en otro momento.";
    }
    echo json_encode($response);
});
/* CREAR UN TECNICO */


/* EDITAR TECNICO */
$app->post('/abraldes/tecnico/edit/', function ($request, $response) {

    $id_tecnico = $request->getParsedBody()['id_tecnico'];
    $nombres = $request->getParsedBody()['nombres'];
    $apellidos = $request->getParsedBody()['apellidos'];
    $telefono = $request->getParsedBody()['telefono'];
    $usuario = $request->getParsedBody()['username'];
    $clave   = $request->getParsedBody()['password'];
    $correo = $request->getParsedBody()['correo'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->editTecnico($id_tecnico, $nombres, $apellidos, $telefono, $usuario, $clave, $correo);

    if ($resultado == RECORD_UPDATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "El tecnico ha sido editado con éxito.";
    } else if ($resultado == RECORD_UPDATED_FAILED) {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor vuelva a intentar en otro momento.";
    } else {
        $response["error"] = true;
        $response["msg"] = "El tecnico no existe. Verifique nuevamente los datos";
    }
    echo json_encode($response);
});
/* EDITAR TECNICO */


/* OBTENER TODOS LOS TECNICOS */
$app->get('/abraldes/tecnico/all', function ($request, $response) {

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getAllTecnicos();

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["tecnicos"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor verificar nuevamente";
    }
    echo json_encode($response);
});
/* OBTENER TODOS LOS TECNICOS */

/* OBTENER UN TECNICO POR ID */
$app->get('/abraldes/tecnico/{id}', function ($request, $response, $args) {
    $id_tecnico = $args['id'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getTecnicoById($id_tecnico);

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["tecnico"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "El tecnico no existe";
    }

    echo json_encode($response);
});
/* OBTENER UN TECNICO POR ID */

/* ELIMINAR TECNICO */
$app->post('/abraldes/tecnico/delete/', function ($request, $response) {

    $id_tecnico = $request->getParsedBody()['id_tecnico'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->deleteTecnico($id_tecnico);

    if ($resultado == RECORD_UPDATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "El tecnico ha sido eliminado.";
    } else if ($resultado == RECORD_UPDATED_FAILED) {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor vuelva a intentar en otro momento.";
    } else {
        $response["error"] = true;
        $response["msg"] = "El tecnico no existe. Verifique nuevamente los datos";
    }
    echo json_encode($response);
});
/* ELIMINAR TECNICO */


/* OBTENER TODAS LAS OBRAS CON MEDICIONES PENDIENTES Y LAS TAREAS PENDIENTES DE UN TECNICO */
$app->get('/abraldes/tecnico/{id}/pendientes/', function ($request, $response, $args) {
    $id_tecnico = $args['id'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getAllPendientes($id_tecnico);


    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["pendientes"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor verificar nuevamente";
    }

    /* if (array_key_exists('tareas', $resultado) && array_key_exists('mediciones', $resultado)) {
        $response["error"] = false;
        $response["pendientes"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor vuelva a intentar en otro momento.";
    }*/
    echo json_encode($response);
});
/* OBTENER TODAS LAS OBRAS CON MEDICIONES PENDIENTES Y LAS TAREAS PENDIENTES DE UN TECNICO */



/* OBTENER TODAS LAS OBRAS CON TAREAS PE DE UN TECNICO */
$app->get('/abraldes/tecnico/{id}/TareasPendientes/', function ($request, $response, $args) {
    $id_tecnico = $args['id'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getAllTareasPendientes($id_tecnico);

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["tareas"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor verificar nuevamente";
    }

    /* if (array_key_exists('tareas', $resultado) && array_key_exists('mediciones', $resultado)) {
        $response["error"] = false;
        $response["pendientes"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor vuelva a intentar en otro momento.";
    }*/
    echo json_encode($response);
});
/* OBTENER TODAS LAS OBRAS CON TAREAS PENDIENTES DE UN TECNICO */




/* OBTENER DETALLES DE UNA PRODUCTO A DE UNA OBRA */
$app->get('/abraldes/tecnico/producto/{id}', function ($request, $response, $args) {
    $id_obras_productos = $args['id'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getObraByProductoId($id_obras_productos);

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["producto"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor vuelva a intentar en otro momento.";
    }
    echo json_encode($response);
});
/* OBTENER DETALLES DE UNA PRODUCTO A DE UNA OBRA */


/* OBTENER DETALLES DE UN TAREA POR ID */
$app->get('/abraldes/tecnico/tarea/{id}', function ($request, $response, $args) {
    $id_tarea = $args['id'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getDetalleTareaById($id_tarea);

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["tarea"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "La tarea no existe";
    }
    echo json_encode($response);
});
/* OBTENER DETALLES DE UN TAREA POR ID */



/* SOLICITAR HERRAMIENTA */
$app->post('/abraldes/tecnico/herramienta/solicitar/', function ($request, $response) {

    $id_tecnico = $request->getParsedBody()['id_tecnico'];
    $id_herramienta = $request->getParsedBody()['id_herramienta'];
    $cantidad = $request->getParsedBody()['cantidad'];
    $id_obra = $request->getParsedBody()['id_obra'];
    $id_obra_producto = $request->getParsedBody()['id_obra_producto'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->solicitarHerramienta($id_tecnico, $id_herramienta, $cantidad, $id_obra, $id_obra_producto);

    if ($resultado == RECORD_CREATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "Solicitud ingresada con éxito";
    } else if ($resultado == RECORD_ALREADY_EXISTED) {
        $response["error"] = true;
        $response["msg"] = "Ya existe una solicitud activa de esta herramienta.";
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error en la creación de la solicitud. Por favor vuelva a verificar en otro momento.";
    }
    echo json_encode($response);
});
/* SOLICITAR HERRAMIENTA */


/* CANCELAR SOLICITUD HERRAMIENTA */
$app->post('/abraldes/tecnico/herramienta/cancelar/', function ($request, $response) {

    $id_solicitud = $request->getParsedBody()['id_solicitud'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->cancelarSolicitudHerramienta($id_solicitud);

    if ($resultado == RECORD_UPDATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "La solicitud ha sido eliminada.";
    } else if ($resultado == RECORD_UPDATED_FAILED) {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor vuelva a intentar en otro momento.";
    } else {
        $response["error"] = true;
        $response["msg"] = "La solicitud no existe. Verifique nuevamente los datos";
    }
    echo json_encode($response);
});
/* CANCELAR SOLICITUD HERRAMIENTA */


/* OBTENER LAS HERAMIENTAS SOLICITADAS DE UN TECNICO POR ID */
$app->get('/abraldes/tecnico/{id}/herramientas/', function ($request, $response, $args) {
    $id_tecnico = $args['id'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getAllSolicitudesHerramientasTecnico($id_tecnico);

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["herramientas"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "El tecnico no existe";
    }
    echo json_encode($response);
});
/* OBTENER LAS HERAMIENTAS SOLICITADAS DE UN TECNICO POR ID */



/* SOLICITAR PARTE */
$app->post('/abraldes/tecnico/parte/solicitar/', function ($request, $response) {

    $id_tecnico = $request->getParsedBody()['id_tecnico'];
    $id_parte = $request->getParsedBody()['id_parte'];
    $cantidad = $request->getParsedBody()['cantidad'];
    $id_obra = $request->getParsedBody()['id_obra'];
    $id_obra_producto = $request->getParsedBody()['id_obra_producto'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->solicitarParte($id_tecnico, $id_parte, $cantidad, $id_obra, $id_obra_producto);

    if ($resultado == RECORD_CREATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "Solicitud ingresada con éxito";
    } else if ($resultado == RECORD_ALREADY_EXISTED) {
        $response["error"] = true;
        $response["msg"] = "Ya existe una solicitud activa de esta herramienta.";
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error en la creación de la solicitud. Por favor vuelva a verificar en otro momento.";
    }
    echo json_encode($response);
});
/* SOLICITAR PARTE */

/* CANCELAR SOLICITUD PARTE */
$app->post('/abraldes/tecnico/parte/cancelar/', function ($request, $response) {

    $id_solicitud = $request->getParsedBody()['id_solicitud'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->cancelarSolicitudParte($id_solicitud);

    if ($resultado == RECORD_UPDATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "La solicitud ha sido eliminada.";
    } else if ($resultado == RECORD_UPDATED_FAILED) {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor vuelva a intentar en otro momento.";
    } else {
        $response["error"] = true;
        $response["msg"] = "La solicitud no existe. Verifique nuevamente los datos";
    }
    echo json_encode($response);
});
/* CANCELAR SOLICITUD PARTE */

/* OBTENER LAS PARTES SOLICITADAS DE UN TECNICO POR ID */
$app->get('/abraldes/tecnico/{id}/partes/', function ($request, $response, $args) {
    $id_tecnico = $args['id'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getAllSolicitudesPartesTecnico($id_tecnico);

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["partes"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "El tecnico no existe";
    }
    echo json_encode($response);
});
/* OBTENER LAS PARTES SOLICITADAS DE UN TECNICO POR ID */


/**
 *TECNICOS
 */



/**
 *OBRAS
 */

/* OBTENER TODOS LAS OBRAS */
$app->get('/abraldes/obras/all', function ($request, $response) {

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getAllObras();

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["obras"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor verificar nuevamente";
    }
    echo json_encode($response);
});
/* OBTENER TODOS LAS OBRAS */


/* OBTENER UNA OBRA POR ID */
$app->get('/abraldes/obra/{id}', function ($request, $response, $args) {
    $id_obra = $args['id'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getObraById($id_obra);

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["obra"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "El tecnico no existe";
    }
    echo json_encode($response);
});
/* OBTENER UNA OBRA POR ID */


/* CREAR UNA OBRA */
$app->post('/abraldes/obra/create/', function ($request, $response) {

    /* $nombre = $request->getParsedBody()['nombre'];
    $id_cliente = $request->getParsedBody()['id_cliente'];
    $nota = $request->getParsedBody()['nota'];
    $productos = $request->getParsedBody()['productos']; */

    //DATOS DE LA CREACIÓN DE LA OBRA
    $id_cliente = $request->getParsedBody()['id_cliente'];
    $id_tecnico = $request->getParsedBody()['id_tecnico'];
    $georeferencia = $request->getParsedBody()['georeferencia'];
    $ciudad = $request->getParsedBody()['ciudad'];
    $sector = $request->getParsedBody()['sector'];
    $manzana_etapa = $request->getParsedBody()['manzana_etapa'];
    $solar_villa = $request->getParsedBody()['solar_villa'];
    $medidas_referenciales = $request->getParsedBody()['medidas_referenciales'];
    $fecha_vencimiento = $request->getParsedBody()['fecha_vencimiento'];
    $plazo = $request->getParsedBody()['plazo'];


    //DATOS PARA LA CREACIÓN DE FACTURACIÓN 
    $nombre_factura = $request->getParsedBody()['nombre_factura'];
    $cedula_ruc = $request->getParsedBody()['cedula_ruc'];
    $numero_factura = $request->getParsedBody()['numero_factura'];
    $fecha_emision = $request->getParsedBody()['fecha_emision'];
    $fecha_anticipo = $request->getParsedBody()['fecha_anticipo'];
    $direccion = $request->getParsedBody()['direccion'];
    $email = $request->getParsedBody()['email'];
    $telefono = $request->getParsedBody()['telefono'];

    //array de productos
    $dataGaraje = $request->getParsedBody()['dataGaraje'];
    $dataPeatonal = $request->getParsedBody()['dataPeatonal'];
    $dataOrnamental = $request->getParsedBody()['dataOrnamental'];
    $dataOcasional = $request->getParsedBody()['dataOcasional'];
    $categorias = $request->getParsedBody()['categorias'];

    //DATOS DE IMÁGENES Y VALORES

    $dataImagen = $request->getParsedBody()['dataImagen'];
    $valor_total = $request->getParsedBody()['valor_total'];
    $valor_abonado = $request->getParsedBody()['valor_abonado'];


    $response = array();
    $db = new DbHandler2();
    $resultado = $db->createObraData($id_cliente, $id_tecnico, $georeferencia, $ciudad, $sector, $manzana_etapa, $solar_villa, $medidas_referenciales, $fecha_vencimiento, $plazo, $nombre_factura, $cedula_ruc, $numero_factura, $fecha_emision, $fecha_anticipo, $direccion, $email, $telefono, $dataGaraje, $dataPeatonal, $dataOrnamental, $dataOcasional, $categorias, $dataImagen, $valor_total, $valor_abonado);

    // var_dump($resultado);
    if ($resultado == RECORD_CREATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "La obra ha sido creado con éxito.";
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error en la creación de la obra. Por favor vuelva a verificar en otro momento.";
    }
    echo json_encode($response);
});
/* $app->post('/abraldes/obra/create/', function ($request, $response) {

    $nombre = $request->getParsedBody()['nombre'];
    $id_cliente = $request->getParsedBody()['id_cliente'];
    $nota = $request->getParsedBody()['nota'];
    $id_tecnico = $request->getParsedBody()['id_tecnico'];
    $response = array();
    $db = new DbHandler2();
    $resultado = $db->createObra($nombre, $id_cliente, $nota, $id_tecnico);

    // var_dump($resultado);
    if ($resultado == RECORD_CREATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "La obra ha sido creado con éxito.";
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error en la creación de la obra. Por favor vuelva a verificar en otro momento.";
    }
    echo json_encode($response);
}); */
/* CREAR UNA OBRA */


/**
 *OBRAS
 */



/**
 *MODULACIONES
 */


/* OBTENER TODAS LAS MODULACIONES DE UNA OBRA */

$app->post('/abraldes/getModulaciones/', function ($request, $response) {

    $id_obra = $request->getParsedBody()['id_obra'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getAllModulaciones($id_obra);

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["modulaciones"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "La obra no existe";
    }
    echo json_encode($response);
});
/* OBTENER TODAS LAS MODULACIONES DE UNA OBRA */


/* CREAR UN GRUPO DE MODULACIONES A UNA OBRA */
$app->post('/abraldes/modulacion/create/', function ($request, $response) {

    $id_obra = $request->getParsedBody()['id_obra'];
    $num_modulaciones = $request->getParsedBody()['num_modulaciones'];
    $modulaciones = $request->getParsedBody()['modulaciones'];

    // var_dump($modulaciones);
    $response = array();
    $db = new DbHandler2();
    $resultado = $db->createModulacionesData($id_obra, $num_modulaciones, $modulaciones);

    if ($resultado === RECORD_CREATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "La modulación y sus tareas han sido creadas con éxito.";
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error en la creación. Por favor vuelva a verificar en otro momento.";
    }
    echo json_encode($response);
});
/* CREAR UN GRUPO DE MODULACIONES A UNA OBRA */



/**
 *MODULACIONES
 */



/**
 *MEDICIONES
 */

$app->post('/abraldes/mediciones/create/', function ($request, $response) {

    $data = $request->getParsedBody()['data'];

    // var_dump($data);
    $response = array();
    $db = new DbHandler2();
    $resultado = $db->createMedicionesData($data);

    if ($resultado === RECORD_CREATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "Las mediciones han sido creadas con éxito.";
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error en la creación. Por favor vuelva a verificar en otro momento.";
    }
    echo json_encode($response);
});


/* OBTENER TODAS LAS MEDICIONES DE UNA OBRA POR ID */
$app->get('/abraldes/mediciones/obra/{id}', function ($request, $response, $args) {
    $id_obra = $args['id'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getAllMedicionesByObra($id_obra);

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["medidas"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "La obra no existe";
    }
    echo json_encode($response);
});
/* OBTENER TODAS LAS MEDICIONES DE UNA OBRA POR ID */

/**
 *MEDICIONES
 */


/**
 *TAREAS
 */

/* EDITAR UNA TAREA */
$app->post('/abraldes/tareas/edit/', function ($request, $response) {

    $tareas = $request->getParsedBody()['tareas'];
    $response = array();
    $db = new DbHandler2();
    $resultado = $db->editTareasData($tareas);

    if ($resultado === RECORD_UPDATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "Las tareas han sido actualizadas con éxito.";
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error en la creación. Por favor vuelva a verificar en otro momento.";
    }
    echo json_encode($response);
});
/* EDITAR UNA TAREA */


/* OBTENER UNA TAREA POR ID */
$app->get('/abraldes/tarea/{id}', function ($request, $response, $args) {
    $id_tarea = $args['id'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getTareaById($id_tarea);

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["obra"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "La tarea no existe";
    }
    echo json_encode($response);
});
/* OBTENER UNA TAREA POR ID */

/* OBTENER UNA TAREA POR ID */
$app->get('/abraldes/tarea2/{id}', function ($request, $response, $args) {
    $id_tarea = $args['id'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getTarea2ById($id_tarea);

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["obra"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "La tarea no existe";
    }
    echo json_encode($response);
});
/* OBTENER UNA TAREA POR ID */

/* OBTENER TODAS LAS TAREAS PENDIENTES DE UN TECNICO */
$app->get('/abraldes/tareas/tecnico/{id}', function ($request, $response, $args) {
    $id_tecnico = $args['id'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getAllTareasTecnico($id_tecnico);

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["tareas"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "El tecnico no existe";
    }
    echo json_encode($response);
});
/* OBTENER TODAS LAS TAREAS PENDIENTES DE UN TECNICO */


/**
 *TAREAS
 */


/**
 *HERRAMIENTAS
 */

/* CREAR UNA HERRAMIENTA */
$app->post('/abraldes/herramientas/create/', function ($request, $response) {

    $foto = $request->getParsedBody()['foto'];
    $descripcion = $request->getParsedBody()['descripcion'];
    $cantidad = $request->getParsedBody()['cantidad'];
    $ubicacion = $request->getParsedBody()['ubicacion'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->createHerramienta($foto, $descripcion, $cantidad, $ubicacion);

    if ($resultado == RECORD_CREATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "Herramienta ingresada con éxito.";
    } else if ($resultado == RECORD_ALREADY_EXISTED) {
        $response["error"] = true;
        $response["msg"] = "Esa herramienta ya existe. Verifique nuevamente los datos";
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error en la creación. Por favor vuelva a verificar en otro momento.";
    }
    echo json_encode($response);
});
/* CREAR UNA HERRAMIENTA */

/* OBTENER TODAS LAS HERRAMIENTAS */
$app->get('/abraldes/herramientas/all', function ($request, $response) {

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getAllHerramientas();

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["herramientas"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor verificar nuevamente";
    }
    echo json_encode($response);
});
/* OBTENER TODAS LAS HERRAMIENTAS */


/* OBTENER UNA HERRAMIENTA POR ID */
$app->get('/abraldes/herramienta/{id}', function ($request, $response, $args) {
    $id_herramienta = $args['id'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getHerramientaById($id_herramienta);

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["herramienta"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "La herramienta no existe";
    }
    echo json_encode($response);
});
/* OBTENER UNA HERRAMIENTA POR ID */


/* EDITAR UNA HERRAMIENTA */
$app->post('/abraldes/herramienta/edit/', function ($request, $response) {

    $id_herramienta = $request->getParsedBody()['id_herramienta'];
    $foto = $request->getParsedBody()['foto'];
    $descripcion = $request->getParsedBody()['descripcion'];
    $cantidad = $request->getParsedBody()['cantidad'];
    $ubicacion = $request->getParsedBody()['ubicacion'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->editHerramienta($foto, $descripcion, $cantidad, $ubicacion, $id_herramienta);

    if ($resultado == RECORD_UPDATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "La herramienta ha sido actualizada.";
    } else if ($resultado == RECORD_UPDATED_FAILED) {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor vuelva a intentar en otro momento.";
    } else {
        $response["error"] = true;
        $response["msg"] = "La herramienta no existe. Verifique nuevamente los datos";
    }

    echo json_encode($response);
});
/* EDITAR UNA HERRAMIENTA */


/* ELIMINAR HERRAMIENTA */
$app->post('/abraldes/herramienta/delete/', function ($request, $response) {

    $id_herramienta = $request->getParsedBody()['id_herramienta'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->deleteHerramienta($id_herramienta);

    if ($resultado == RECORD_UPDATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "La herramienta ha sido eliminada.";
    } else if ($resultado == RECORD_UPDATED_FAILED) {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor vuelva a intentar en otro momento.";
    } else {
        $response["error"] = true;
        $response["msg"] = "La herramienta no existe. Verifique nuevamente los datos";
    }
    echo json_encode($response);
});
/* ELIMINAR HERRAMIENTA */


/**
 *HERRAMIENTAS
 */



/**
 *PARTES
 */


/* CREAR UNA PARTE */
$app->post('/abraldes/parte/create/', function ($request, $response) {

    $foto = $request->getParsedBody()['foto'];
    $descripcion = $request->getParsedBody()['descripcion'];
    $minimo = $request->getParsedBody()['minimo'];
    $ubicacion = $request->getParsedBody()['ubicacion'];
    $devolver = $request->getParsedBody()['devolver'];
    $categoria = $request->getParsedBody()['categoria'];
    $cantidad = $request->getParsedBody()['cantidad'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->createParte($foto, $descripcion, $minimo, $ubicacion, $devolver, $categoria, $cantidad);

    if ($resultado == RECORD_CREATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "Parte ingresada con éxito.";
    } else if ($resultado == RECORD_ALREADY_EXISTED) {
        $response["error"] = true;
        $response["msg"] = "Esta Parte ya existe. Verifique nuevamente los datos";
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error en la creación. Por favor vuelva a verificar en otro momento.";
    }
    echo json_encode($response);
});
/* CREAR UNA PARTE */

/* EDITAR UNA PARTE */
$app->post('/abraldes/partes/edit/', function ($request, $response) {

    $descripcion = $request->getParsedBody()['descripcion'];
    $foto = $request->getParsedBody()['foto'];
    $minimo = $request->getParsedBody()['minimo'];
    $ubicacion = $request->getParsedBody()['ubicacion'];
    $devolver = $request->getParsedBody()['devolver'];
    $categoria = $request->getParsedBody()['categoria'];
    $cantidad = $request->getParsedBody()['cantidad'];
    $id_parte = $request->getParsedBody()['id_parte'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->editParte($foto, $descripcion, $minimo, $ubicacion, $devolver, $categoria, $cantidad, $id_parte);

    if ($resultado == RECORD_UPDATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "Parte actualizada con éxito con éxito.";
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error en la actualización. Por favor vuelva a verificar en otro momento.";
    }
    echo json_encode($response);
});
/* EDITAR UNA PARTE */

/* CREAR UNA PARTE */
$app->post('/abraldes/partes/editSinImagen/', function ($request, $response) {

    $descripcion = $request->getParsedBody()['descripcion'];
    $minimo = $request->getParsedBody()['minimo'];
    $ubicacion = $request->getParsedBody()['ubicacion'];
    $devolver = $request->getParsedBody()['devolver'];
    $categoria = $request->getParsedBody()['categoria'];
    $cantidad = $request->getParsedBody()['cantidad'];
    $id_parte = $request->getParsedBody()['id_parte'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->editParteSinImagen($descripcion, $minimo, $ubicacion, $devolver, $categoria, $cantidad, $id_parte);

    if ($resultado == RECORD_UPDATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "Parte actualizada con éxito con éxito.";
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error en la actualización. Por favor vuelva a verificar en otro momento.";
    }
    echo json_encode($response);
});
/* CREAR UNA PARTE */


/* OBTENER TODAS LAS PARTES */
$app->get('/abraldes/partes/all', function ($request, $response) {

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getAllPartes();

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["partes"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor verificar nuevamente";
    }
    echo json_encode($response);
});
/* OBTENER TODAS LAS PARTES */

/* OBTENER TODAS LAS PARTES */
$app->get('/abraldes/tipoMotor/all', function ($request, $response) {

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getAllTipoMotor();

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["tipo_motor"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor verificar nuevamente";
    }
    echo json_encode($response);
});
/* OBTENER TODAS LAS PARTES */


/* OBTENER TODAS LAS PARTES */
$app->get('/abraldes/motor/', function ($request, $response) {

    $id_motor = $request->getParsedBody()['id_motor'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getMotorA($id_motor);

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["motor"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor verificar nuevamente";
    }
    echo json_encode($response);
});
/* OBTENER TODAS LAS PARTES */

/* OBTENER TODAS LAS PARTES */
$app->get('/abraldes/fijo_superior/all', function ($request, $response) {

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getAllFijo();

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["fijo_superior"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor verificar nuevamente";
    }
    echo json_encode($response);
});
/* OBTENER TODAS LAS PARTES */

/* OBTENER TODAS LAS PARTES */
$app->get('/abraldes/mecanismos/all', function ($request, $response) {

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getAllMecanismo();

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["mecanismos"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor verificar nuevamente";
    }
    echo json_encode($response);
});
/* OBTENER TODAS LAS PARTES */


/* OBTENER UNA PARTE POR ID */
$app->get('/abraldes/parte/{id}', function ($request, $response, $args) {
    $id_parte = $args['id'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getParteById($id_parte);

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["parte"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "La parte no existe";
    }
    echo json_encode($response);
});
/* OBTENER UNA PARTE POR ID */





/* ELIMINAR PARTE */
$app->post('/abraldes/parte/delete/', function ($request, $response) {

    $id_parte = $request->getParsedBody()['id_parte'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->deleteParte($id_parte);

    if ($resultado == RECORD_UPDATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "La parte ha sido eliminada.";
    } else if ($resultado == RECORD_UPDATED_FAILED) {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor vuelva a intentar en otro momento.";
    } else {
        $response["error"] = true;
        $response["msg"] = "La parte no existe. Verifique nuevamente los datos";
    }
    echo json_encode($response);
});
/* ELIMINAR PARTE */


/**
 *PARTES
 */

/* OBTENER TODOS LOS LOCALES */
$app->get('/abraldes/locales/all', function ($request, $response) {

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getAllLocales();

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["locales"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor verificar nuevamente";
    }
    echo json_encode($response);
});
/* OBTENER TODOS LOS LOCALES */

/* CREAR UNA LOCAL */
$app->post('/abraldes/local/create/', function ($request, $response) {

    $nombre = $request->getParsedBody()['nombre'];
    $response = array();
    $db = new DbHandler2();
    $resultado = $db->createLocal($nombre);

    if ($resultado == RECORD_CREATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "Local ingresado con éxito.";
    } else if ($resultado == RECORD_ALREADY_EXISTED) {
        $response["error"] = true;
        $response["msg"] = "El local ya existe. Verifique nuevamente los datos";
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error en la creación. Por favor vuelva a verificar en otro momento.";
    }
    echo json_encode($response);
});
/* CREAR UN LOCAL */

$app->post('/abraldes/presupuestoInsumos/all', function ($request, $response) {

    $productos = $request->getParsedBody()['productos'];
    $id_obra = $request->getParsedBody()['id_obra'];
    $id_obra_producto = $request->getParsedBody()['id_obra_producto'];

    $response = array();
    $db = new DbHandler2();

    $resultado = $db->createPresupuestoInsumo($productos, $id_obra, $id_obra_producto);

    if ($resultado == RECORD_CREATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "Local ingresado con éxito.";
    } else if ($resultado == RECORD_ALREADY_EXISTED) {
        $response["error"] = true;
        $response["msg"] = "El local ya existe. Verifique nuevamente los datos";
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error en la creación. Por favor vuelva a verificar en otro momento.";
    }
    echo json_encode($response);
});


/*OBTENER TODAS LAS BODEGAS*/
$app->get('/abraldes/bodegas/all', function ($request, $response) {

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getAllBodegas();

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["bodegas"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor verificar nuevamente";
    }
    echo json_encode($response);
});

/* CREAR UNA BODEGA */
$app->post('/abraldes/bodega/create/', function ($request, $response) {

    $nombre = $request->getParsedBody()['nombre'];
    $local = $request->getParsedBody()['local'];
    $response = array();
    $db = new DbHandler2();
    $resultado = $db->createBodega($nombre, $local);

    if ($resultado == RECORD_CREATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "Bodega ingresado con éxito.";
    } else if ($resultado == RECORD_ALREADY_EXISTED) {
        $response["error"] = true;
        $response["msg"] = "La Bodega ya existe. Verifique nuevamente los datos";
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error en la creación. Por favor vuelva a verificar en otro momento.";
    }
    echo json_encode($response);
});
/* CREAR UNA BODEGA */
/* ELIMINAR PROVEEDOR */
$app->post('/abraldes/bodega/delete/', function ($request, $response) {

    $id_bodega = $request->getParsedBody()['id_bodega'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->deleteBodega($id_bodega);

    if ($resultado == RECORD_UPDATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "El proveedor ha sido eliminado.";
    } else if ($resultado == RECORD_UPDATED_FAILED) {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor vuelva a intentar en otro momento.";
    } else {
        $response["error"] = true;
        $response["msg"] = "El proveedor no existe. Verifique nuevamente los datos";
    }
    echo json_encode($response);
});
/* ELIMINAR PROVEEDOR */

/**
 *PROVEEDORES
 */

/* OBTENER TODOS LOS POROVEEDORES */
$app->get('/abraldes/proveedores/all', function ($request, $response) {

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getAllProveedores();

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["proveedores"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor verificar nuevamente";
    }
    echo json_encode($response);
});
/* OBTENER TODOS LOS POROVEEDORES */


/* OBTENER UN PROVEEDOR POR ID */
$app->get('/abraldes/proveedor/{id}', function ($request, $response, $args) {
    $id_proveedor = $args['id'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getProveedorById($id_proveedor);

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["proveedor"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "El proveedor no existe";
    }
    echo json_encode($response);
});
/* OBTENER UN PROVEEDOR POR ID */


/* CREAR UNA PROVEEDOR */
$app->post('/abraldes/proveedor/create/', function ($request, $response) {

    $nombre = $request->getParsedBody()['nombre'];
    $response = array();
    $db = new DbHandler2();
    $resultado = $db->createProveedor($nombre);

    if ($resultado == RECORD_CREATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "Proveedor ingresado con éxito.";
    } else if ($resultado == RECORD_ALREADY_EXISTED) {
        $response["error"] = true;
        $response["msg"] = "El proveedor ya existe. Verifique nuevamente los datos";
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error en la creación. Por favor vuelva a verificar en otro momento.";
    }
    echo json_encode($response);
});
/* CREAR UNA PROVEEDOR */



/* ELIMINAR PROVEEDOR */
$app->post('/abraldes/proveedor/delete/', function ($request, $response) {

    $id_proveedor = $request->getParsedBody()['id_proveedor'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->deleteProveedor($id_proveedor);

    if ($resultado == RECORD_UPDATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "El proveedor ha sido eliminado.";
    } else if ($resultado == RECORD_UPDATED_FAILED) {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor vuelva a intentar en otro momento.";
    } else {
        $response["error"] = true;
        $response["msg"] = "El proveedor no existe. Verifique nuevamente los datos";
    }
    echo json_encode($response);
});
/* ELIMINAR PROVEEDOR */



/* EDITAR UNA PROVEEDOR */
$app->post('/abraldes/proveedor/edit/', function ($request, $response) {

    $id_proveedor = $request->getParsedBody()['id_proveedor'];
    $nombre = $request->getParsedBody()['nombre'];


    $response = array();
    $db = new DbHandler2();
    $resultado = $db->editProveedor($id_proveedor, $nombre);

    if ($resultado == RECORD_UPDATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "El proveedor ha sido actualizado.";
    } else if ($resultado == RECORD_UPDATED_FAILED) {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor vuelva a intentar en otro momento.";
    } else {
        $response["error"] = true;
        $response["msg"] = "El proveedor no existe. Verifique nuevamente los datos";
    }

    echo json_encode($response);
});
/* EDITAR UNA PROVEEDOR */




/**
 *PROVEEDORES
 */



/**
 *CATEGORIAS
 */

/* OBTENER TODAS LAS CATEGORIAS */
$app->get('/abraldes/categorias/all', function ($request, $response) {

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getAllCategorias();

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["categorias"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor verificar nuevamente";
    }
    echo json_encode($response);
});
/* OBTENER TODAS LAS CATEGORIAS */

/* OBTENER TODAS LAS CATEGORIAS DE INVENTARIOS*/
$app->get('/abraldes/categorias_partes/all', function ($request, $response) {

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getAllCategoriasPartes();

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["categorias"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor verificar nuevamente";
    }
    echo json_encode($response);
});
/* OBTENER TODAS LAS CATEGORIAS DE INVENTARIOS*/


/* CREAR UN CATEGORIA*/
$app->post('/abraldes/categoria/create/', function ($request, $response) {

    $nombre = $request->getParsedBody()['nombre'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->createCategoria($nombre);

    if ($resultado == RECORD_CREATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "Categoria ingresada con éxito.";
    } else if ($resultado == RECORD_ALREADY_EXISTED) {
        $response["error"] = true;
        $response["msg"] = "Este categoria ya existe. Verifique nuevamente los datos";
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error en la creación. Por favor vuelva a verificar en otro momento.";
    }
    echo json_encode($response);
});
/* CREAR UN CATEGORIA */

/* CREAR UN CATEGORIA PARTE*/
$app->post('/abraldes/categoriaParte/create/', function ($request, $response) {

    $nombre = $request->getParsedBody()['nombre'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->createCategoriaParte($nombre);

    if ($resultado == RECORD_CREATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "Categoria ingresada con éxito.";
    } else if ($resultado == RECORD_ALREADY_EXISTED) {
        $response["error"] = true;
        $response["msg"] = "Este categoria ya existe. Verifique nuevamente los datos";
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error en la creación. Por favor vuelva a verificar en otro momento.";
    }
    echo json_encode($response);
});
/* CREAR UN CATEGORIA PARTE*/


/* ELIMINAR CATEGORIA (TAMBIEN LAS REFRENCIAS EN productos_categorias)*/
$app->post('/abraldes/categoria/delete/', function ($request, $response) {

    $id_categoria = $request->getParsedBody()['id_categoria'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->deleteCategoria($id_categoria);

    if ($resultado == RECORD_UPDATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "La categoria ha sido eliminado.";
    } else if ($resultado == RECORD_UPDATED_FAILED) {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor vuelva a intentar en otro momento.";
    } else {
        $response["error"] = true;
        $response["msg"] = "La categoria no existe. Verifique nuevamente los datos";
    }
    echo json_encode($response);
});
/* ELIMINAR CATEGORIA */


/* OBTENER LOS PRODUCTOS DE UNA CATEGORIA*/
$app->get('/abraldes/categoria/{id}/productos/', function ($request, $response, $args) {
    $id_categoria = $args['id'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getAllProductosCategoria($id_categoria);

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["productos"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "La categoria no existe";
    }
    echo json_encode($response);
});
/* OBTENER LOS PRODUCTOS DE UNA CATEGORIA*/



/**
 *CATEGORIAS
 */


/**
 *PRODUCTOS
 */

/* OBTENER TODAS LAS PRODUCTOS */
$app->get('/abraldes/productos/all', function ($request, $response) {

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getAllProductos();

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["productos"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor verificar nuevamente";
    }
    echo json_encode($response);
});
/* OBTENER TODAS LAS PRODUCTOS */



/* CREAR UN PRODUCTO */
$app->post('/abraldes/productos/create/', function ($request, $response) {

    $nombre = $request->getParsedBody()['producto'];
    $categorias = $request->getParsedBody()['categoria'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->createProducto($nombre, $categorias);
    if ($resultado == RECORD_CREATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "Producto ingresada con éxito.";
    } else if ($resultado == RECORD_ALREADY_EXISTED) {
        $response["error"] = true;
        $response["msg"] = "Este producto ya existe. Verifique nuevamente los datos";
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error en la creación. Por favor vuelva a verificar en otro momento.";
    }
    echo json_encode($response);
});
/* CREAR UN PRODUCTO */


/* EDITAR UN PRODUCTO */
$app->post('/abraldes/producto/edit/', function ($request, $response) {

    $id_producto = $request->getParsedBody()['id_producto'];
    $nombre = $request->getParsedBody()['producto'];
    $categorias = $request->getParsedBody()['categoria'];

    // var_dump($categorias);

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->editProducto($id_producto, $nombre, $categorias);

    if ($resultado == RECORD_UPDATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "El producto ha sido editado con éxito.";
    } else if ($resultado == RECORD_UPDATED_FAILED) {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor vuelva a intentar en otro momento.";
    } else {
        $response["error"] = true;
        $response["msg"] = "El producto no existe. Verifique nuevamente los datos";
    }
    echo json_encode($response);
});
/* EDITAR UN PRODUCTO */



/* ELIMINAR PRODUCTO (TAMBIEN LAS REFRENCIAS EN productos_categorias)*/
$app->post('/abraldes/producto/delete/', function ($request, $response) {

    $id_producto = $request->getParsedBody()['id_producto'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->deleteProducto($id_producto);

    if ($resultado == RECORD_UPDATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["msg"] = "El producto ha sido eliminado.";
    } else if ($resultado == RECORD_UPDATED_FAILED) {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor vuelva a intentar en otro momento.";
    } else {
        $response["error"] = true;
        $response["msg"] = "El producto no existe. Verifique nuevamente los datos";
    }
    echo json_encode($response);
});
/* ELIMINAR PRODUCTO */


/* OBTENER UN PRODUCTO POR ID */
$app->get('/abraldes/producto/{id}', function ($request, $response, $args) {
    $id_producto = $args['id'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getProductoById($id_producto);

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["producto"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "El proveedor no existe";
    }
    echo json_encode($response);
});
/* OBTENER UN PRODUCTO POR ID */


/* OBTENER TODOS LAS OBRAS */
$app->get('/abraldes/obras_producto/{id}', function ($request, $response, $args) {
    $id_obra = $args['id'];
    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getProductosObra($id_obra);

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["obra_productos"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor verificar nuevamente";
    }
    echo json_encode($response);
});
/* OBTENER TODOS LAS OBRAS */


$app->get('/abraldes/tecnico_obra/{id}', function ($request, $response, $args) {
    $id_obra = $args['id'];

    $response = array();
    $db = new DbHandler2();
    $resultado = $db->getTecnicoObra($id_obra);

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["obra"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor verificar nuevamente";
    }
    echo json_encode($response);
});

/**
 *PRODUCTOS
 */






$app->run();
