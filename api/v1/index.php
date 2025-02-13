<?php

/**
 * Step 1: Require the Slim Framework using Composer's autoloader
 *
 * If you are not using Composer, you need to load Slim Framework with your own
 * PSR-4 autoloader.
 */
require '../vendor/autoload.php';

/*====================*/
require_once dirname(__DIR__) . '/include/DbHandler.php';
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
$app->post('/constructor/admin/login', function ($request, $response) {

    $usuario = $request->getParsedBody()['username'];
    $clave   = $request->getParsedBody()['password'];
    $response = array();
    $db = new DbHandler();
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
        $response["msg"] = "El vendedor no se encuentra registrado";
    }
    echo json_encode($response);
});
/* LOGIN */

/**
 *ADMINISTRADORES
 */


/**
 *CLIENTES
 */



/* OBTENER TODOS LOS CLIENTES */
$app->get('/constructor/clientes/all', function ($request, $response) {

    $response = array();
    $db = new DbHandler();
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


/**
 *CLIENTES
 */

$app->get('/constructor/getGrupos', function ($request, $response) {
    // $id_administrador = $args['id'];

    $response = array();
    $db = new DbHandler();
    $resultado = $db->getGrupos();

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["grupos"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "El usuario no existe";
    }

    echo json_encode($response);
});

$app->get('/constructor/getsubgrupos', function ($request, $response, $args) {
    // $id_administrador = $args['id'];

    $response = array();
    $db = new DbHandler();
    $resultado = $db->getSubGrupos();

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["subgrupos"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "El usuario no existe";
    }

    echo json_encode($response);
});

$app->get('/constructor/getmarcas', function ($request, $response, $args) {
    // $id_administrador = $args['id'];

    $response = array();
    $db = new DbHandler();
    $resultado = $db->getMarca();

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["marcas"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "El usuario no existe";
    }

    echo json_encode($response);
});

$app->post('/constructor/getCotizaciones', function ($request, $response, $args) {
    // $id_administrador = $args['id'];

    $numero_orden   = $request->getParsedBody()['numero_orden'];

    $response = array();
    $db = new DbHandler();
    $resultado = $db->getCotizaciones($numero_orden);

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["cotizacion"] = $resultado[0];
    } else {
        $response["error"] = true;
        $response["msg"] = "El usuario no existe";
    }

    echo json_encode($response);
});

$app->get('/constructor/getCotizacionesAll', function ($request, $response, $args) {
    // $id_administrador = $args['id'];

    // $numero_orden   = $request->getParsedBody()['numero_orden'];

    $response = array();
    $db = new DbHandler();
    $resultado = $db->getCotizacionesAll();

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["cotizaciones"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "El usuario no existe";
    }

    echo json_encode($response);
});

$app->post('/constructor/getProductos', function ($request, $response, $args) {
    // $id_administrador = $args['id'];

    $searchTerm   = $request->getParsedBody()['searchTerm'];
    $selectedGroups   = $request->getParsedBody()['selectedGroups'];
    $selectedSubgroups   = $request->getParsedBody()['selectedSubgroups'];
    $selectedBrands   = $request->getParsedBody()['selectedBrands'];


    $response = array();
    $db = new DbHandler();
    $resultado = $db->getProductos($searchTerm, $selectedGroups, $selectedSubgroups, $selectedBrands);

    if ($resultado != RECORD_DOES_NOT_EXIST) {
        $response["error"] = false;
        $response["productos"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "El usuario no existe";
    }

    echo json_encode($response);
});

/* GUARDAR COTIZACIÓN */
$app->post('/constructor/guardar_cotizacion/', function ($request, $response) {

    $id_cliente = $request->getParsedBody()['id_cliente'];
    $cart = $request->getParsedBody()['cart'];
    $subtotal = $request->getParsedBody()['subtotal'];
    $monto_iva = $request->getParsedBody()['monto_iva'];
    $total = $request->getParsedBody()['total'];
    $codemp = $request->getParsedBody()['codemp'];
    $codsuc = $request->getParsedBody()['codsuc'];
    $metodo_pago = $request->getParsedBody()['metodo_pago'];

    $response = array();
    $db = new DbHandler();
    $resultado = $db->crearCotizacion($id_cliente, $cart, $subtotal, $monto_iva, $total, $codemp, $codsuc, $metodo_pago);


    if ($resultado != RECORD_CREATION_FAILED) {
        $response["error"] = false;
        $response["numero_orden"] = $resultado;
    } else {
        $response["error"] = true;
        $response["msg"] = "Hubo un error. Por favor vuelva a intentar en otro momento.";
    }
    echo json_encode($response);
});
/* GUARDAR COTIZACIÓN */


/* ======================= DATOS DE LA API DE COMISARIATO DEL CONSTRUCTOR ======================== */


//OBTENER TODOS LAS SUCURSALES DESDE LA API
$app->post('/constructor/getSucursalesApi', function ($request, $response) {
    $codigo   = $request->getParsedBody()['codigo'];

    $response = array();
    $db = new DbHandler();
    $resultado = $db->getSucursal($codigo);
    $response["error"] = false;
    $response["sucursales"] = $resultado;


    echo json_encode($response);
});
//OBTENER TODOS LAS SUCURSALES DESDE LA API

//OBTENER TODOS LOS GRUPOS DESDE LA API
$app->get('/constructor/getGruposApi', function ($request, $response) {
    // $id_administrador = $args['id'];

    $response = array();
    $db = new DbHandler();
    $resultado = $db->getGruposApi();
    $response["error"] = false;
    $response["grupos"] = $resultado;


    echo json_encode($response);
});
//OBTENER TODOS LOS GRUPOS DESDE LA API

//OBTENER TODOS LOS SUBGRUPOS DESDE LA API
$app->get('/constructor/getSubGruposApi', function ($request, $response) {
    // $id_administrador = $args['id'];

    $response = array();
    $db = new DbHandler();
    $resultado = $db->getSubGruposApi();
    $response["error"] = false;
    $response["subgrupos"] = $resultado;

    echo json_encode($response);
});
//OBTENER TODOS LOS SUBGRUPOS DESDE LA API

//OBTENER TODOS LAS LINEAS DESDE LA API
$app->get('/constructor/getLineasApi', function ($request, $response) {
    // $id_administrador = $args['id'];

    $response = array();
    $db = new DbHandler();
    $resultado = $db->getLineasApi();
    $response["error"] = false;
    $response["lineas"] = $resultado;

    echo json_encode($response);
});
//OBTENER TODOS LAS LINEAS DESDE LA API

//OBTENER TODOS LAS MARCAS DESDE LA API
$app->get('/constructor/getMarcasApi', function ($request, $response) {
    // $id_administrador = $args['id'];

    $response = array();
    $db = new DbHandler();
    $resultado = $db->getMarcasApi();
    $response["error"] = false;
    $response["marcas"] = $resultado;

    echo json_encode($response);
});
//OBTENER TODOS LAS MARCAS DESDE LA API


//INICIAR SESIÓN CON USUARIO Y CONTRASEÑA
$app->post('/constructor/getLoginUsername', function ($request, $response) {
    // $id_administrador = $args['id'];
    $username = $request->getParsedBody()['username'];
    $password = $request->getParsedBody()['password'];

    $response = array();
    $db = new DbHandler();
    $resultado = $db->getLoginApi($username, $password);
    $response["error"] = false;

    if ($resultado == RECORD_DOES_NOT_EXIST) {
        $response["error"] = true;
        $response["msg"] = "El usuario no existe o no tiene perfil de vendedor.";
    } else if ($resultado == OPERATION_COMPLETED) {
        $response["error"] = true;
        $response["msg"] = "Contraseña incorrecta.";
    } else {
        $response["error"] = false;
        $response["login"] = $resultado;
    }


    echo json_encode($response);
});
//INICIAR SESIÓN CON USUARIO Y CONTRASEÑA

//INICIAR SESIÓN CON CÓDIGO VENDEDOR
$app->post('/constructor/getVendedorCodigo', function ($request, $response) {
    // $id_administrador = $args['id'];
    $codigo = $request->getParsedBody()['codigo'];

    $response = array();
    $db = new DbHandler();
    $resultado = $db->getVendedorLogin($codigo);
    $response["error"] = false;
    $response["vendedor"] = $resultado;

    echo json_encode($response);
});
//INICIAR SESIÓN CON CÓDIGO VENDEDOR

//OBTENER TODOS LOS PRODUCTOS CON PRECIOS
$app->post('/constructor/getProductoApiBuscar', function ($request, $response) {

    $codigo_Empresa = $request->getParsedBody()['codigo_Empresa'];
    $codigo_Sucursal = $request->getParsedBody()['codigo_Sucursal'];
    $nombre = $request->getParsedBody()['nombre'];
    $codigoLinea = $request->getParsedBody()['codigoLinea'];
    $codigoMarca = $request->getParsedBody()['codigoMarca'];
    $codigoGrupo = $request->getParsedBody()['codigoGrupo'];
    $codigoSubgrupo = $request->getParsedBody()['codigoSubgrupo'];
    $codigo_Tienda = $request->getParsedBody()['codigo_Tienda'];
    $opcionBusqueda = $request->getParsedBody()['opcionBusqueda'];
    $page = $request->getParsedBody()['page'];
    $pageSize = $request->getParsedBody()['pageSize'];

    $opcionBusqueda = $request->getParsedBody()['opcionBusqueda'];

    $response = array();
    $db = new DbHandler();
    $resultado = $db->getProductoApiBuscar($codigo_Empresa, $codigo_Sucursal, $nombre, $codigoLinea, $codigoGrupo, $codigoSubgrupo, $codigo_Tienda, $codigoMarca, $opcionBusqueda, $page, $pageSize);

    $response["error"] = false;
    $response["productos"] = $resultado;

    echo json_encode($response);
});

$app->post('/constructor/getProductoApiBuscar2', function ($request, $response) {

    $codigo_Empresa = $request->getParsedBody()['codigo_Empresa'];
    $codigo_Sucursal = $request->getParsedBody()['codigo_Sucursal'];
    $nombre = $request->getParsedBody()['nombre'];
    $codigoLinea = $request->getParsedBody()['codigoLinea'];
    $codigoMarca = $request->getParsedBody()['codigoMarca'];
    $codigoGrupo = $request->getParsedBody()['codigoGrupo'];
    $codigoSubgrupo = $request->getParsedBody()['codigoSubgrupo'];
    $codigo_Tienda = $request->getParsedBody()['codigo_Tienda'];
    $opcionBusqueda = $request->getParsedBody()['opcionBusqueda'];
    $page = $request->getParsedBody()['page'];
    $pageSize = $request->getParsedBody()['pageSize'];

    $opcionBusqueda = $request->getParsedBody()['opcionBusqueda'];

    $response = array();
    $db = new DbHandler();
    $resultado = $db->getProductoApiBuscar2( $codigo_Empresa, $codigo_Sucursal, $nombre, $codigoLinea, $codigoGrupo, $codigoSubgrupo, $codigo_Tienda, $codigoMarca, $opcionBusqueda, $page, $pageSize);
    $response["error"] = false;
    $response["productos"] = $resultado;

    echo json_encode($response);
});


//OBTENER TODOS LOS PRODUCTOS CON PRECIOS


//OBTENER TODOS LOS PRECIOS CON PRODUCTOS
$app->post('/constructor/getPrecioApi', function ($request, $response) {


    $codigo_Tienda = $request->getParsedBody()['codigo_Tienda'];
    $codigo_Producto = $request->getParsedBody()['codigo_Producto'];


    $response = array();
    $db = new DbHandler();
    $resultado = $db->getPrecioApi($codigo_Tienda, $codigo_Producto);
    $response["error"] = false;
    $response["precio"] = $resultado[0];

    echo json_encode($response);
});
//OBTENER TODOS LOS PRECIOS CON PRODUCTOS


//CREAR CLIENTE MEDIANTE API COMISARIATO
$app->post('/constructor/getClienteApiCreate', function ($request, $response) {

    $nombre_cliente = $request->getParsedBody()['nombre_cliente'];
    $ruc = $request->getParsedBody()['ruc'];
    $correo = $request->getParsedBody()['correo'];
    $telefono = $request->getParsedBody()['telefono'];
    $direccion = $request->getParsedBody()['direccion'];
    $codigo_Empresa = $request->getParsedBody()['codigo_Empresa'];
    $codigo_Sucursal = $request->getParsedBody()['codigo_Sucursal'];
    $codigo_Vendedor = $request->getParsedBody()['codigo_Vendedor'];



    $response = array();
    $db = new DbHandler();
    $resultado = $db->getClienteApiCreate($nombre_cliente, $ruc, $correo, $telefono, $direccion, $codigo_Empresa, $codigo_Sucursal, $codigo_Vendedor);

    if ($resultado != RECORD_ALREADY_EXISTED) {
        $response["error"] = false;
        $response["msg"] = "Cliente Registrado con éxito.";
    } else {
        $response["error"] = true;
        $response["msg"] = "El Cliente ya existe. Vuelva a intentar con otro RUC.";
    }

    echo json_encode($response);
});
//CREAR CLIENTE MEDIANTE API COMISARIATO


//ACTUALIZAR CLIENTE MEDIANTE API COMISARIATO
$app->post('/constructor/getClienteApiUpdate', function ($request, $response) {

    $nombre_cliente = $request->getParsedBody()['nombre_cliente'];
    $ruc = $request->getParsedBody()['ruc'];
    $correo = $request->getParsedBody()['correo'];
    $telefono = $request->getParsedBody()['telefono'];
    $direccion = $request->getParsedBody()['direccion'];




    $response = array();
    $db = new DbHandler();
    $resultado = $db->getClienteApiUpdate($nombre_cliente, $ruc, $correo, $telefono, $direccion);

    if ($resultado != RECORD_ALREADY_EXISTED) {
        $response["error"] = false;
        $response["msg"] = "Cliente Actualizado con éxito.";
    } else {
        $response["error"] = true;
        $response["msg"] = "Error al actualizar, vuelva a intentar con otro RUC.";
    }

    echo json_encode($response);
});
//ACTUALIZAR CLIENTE MEDIANTE API COMISARIATO
//OBTENER TODOS LOS CLIENTES 
$app->post('/constructor/getClientesApi', function ($request, $response) {

    $nombre = $request->getParsedBody()['nombre'];
    $codigovendedor = $request->getParsedBody()['codigovendedor'];
    $type = $request->getParsedBody()['type'];

    $response = array();
    $db = new DbHandler();

    $token = $db->getTokenApi()["token"];

    if ($type === 'nombre') {
        $resultado = $db->buscarClienteApi($nombre, $token, $codigovendedor);
    } elseif ($type === 'ruc') {
        $resultado = $db->buscarClienteApiPorRuc($nombre, $token, $codigovendedor);
    } 

    $response["error"] = false;
    $response["clientes"] = $resultado;

    echo json_encode($response);
});
//OBTENER TODOS LOS CLIENTES


//OBTENER TODOS LOS CLIENTES 
$app->post('/constructor/getClientesApiAll', function ($request, $response) {

    $nombre = $request->getParsedBody()['nombre'];
    $codigovendedor = $request->getParsedBody()['codigovendedor'];

    $response = array();
    $db = new DbHandler();
    $resultado = $db->getClientesApiAll($nombre, $codigovendedor);
    $response["error"] = false;
    $response["clientes"] = $resultado;

    echo json_encode($response);
});
//OBTENER TODOS LOS CLIENTES

//OBTENER TODOS LOS CLIENTES POR ID
$app->post('/constructor/getClientesApiById', function ($request, $response) {

    $nombre = $request->getParsedBody()['nombre'];
    $codigovendedor = $request->getParsedBody()['codigovendedor'];
    $response = array();
    $db = new DbHandler();

    $token = $db->getTokenApi()["token"];

    $resultado = $db->buscarClienteApiPorRuc($nombre, $token, $codigovendedor);
    $response["error"] = false;
    $response["clientes"] = $resultado[0];

    echo json_encode($response);
});
//OBTENER TODOS LOS CLIENTES POR ID


//OBTENER TODOS LOS CLIENTES TABLA
$app->post('/constructor/getClientesApiTable', function ($request, $response) {

    $ruc = $request->getParsedBody()['ruc'];
    $page = $request->getParsedBody()['page'];
    $pageSize = $request->getParsedBody()['pageSize'];
    $codigo_Vendedor = $request->getParsedBody()['codigo_Vendedor'];
    $nombre = $request->getParsedBody()['nombre'];

    $response = array();
    $db = new DbHandler();

    $resultado = $db->buscarClienteApiPorRucTable($ruc, $page, $pageSize, $codigo_Vendedor, $nombre);
    $response["error"] = false;
    $response["clientes"] = $resultado;
    // $response["totalClientes"] = $db->buscarClienteApiHeadersRUC($nombre, $page, $pageSize, $codigo_Vendedor);

    echo json_encode($response);
});
//OBTENER TODOS LOS CLIENTES TABLA

/* GUARDAR COTIZACIÓN */
$app->post('/constructor/guardar_cotizacionApi/', function ($request, $response) {


    $codigo_Cliente = $request->getParsedBody()['codigo_Cliente'];
    $detalle = $request->getParsedBody()['detalle'];
    $forma_Pago = $request->getParsedBody()['forma_Pago'];
    $codigo_Tienda = $request->getParsedBody()['codigo_Tienda'];
    $codemp = $request->getParsedBody()['codemp'];
    $codsuc = $request->getParsedBody()['codsuc'];
    $codigo_Vendedor = $request->getParsedBody()['codigo_Vendedor'];

    $response = array();
    $db = new DbHandler();
    $resultado = $db->crearCotizacionApi($codsuc, $codemp, $codigo_Tienda, $codigo_Cliente, $forma_Pago, $codigo_Vendedor, $detalle);

    $response["error"] = false;
    $response["numero_orden"] = $resultado["numeroCotizacion"];
    echo json_encode($response);
});
/* GUARDAR COTIZACIÓN */

/* ACTUALIZAR COTIZACIÓN */
$app->post('/constructor/actualizar_cotizacionApi/', function ($request, $response) {
    $codigo_Cliente = $request->getParsedBody()['codigo_Cliente'];
    $detalle = $request->getParsedBody()['detalle'];
    $forma_Pago = $request->getParsedBody()['forma_Pago'];
    $codigo_Tienda = $request->getParsedBody()['codigo_Tienda'];
    $codemp = $request->getParsedBody()['codemp'];
    $codsuc = $request->getParsedBody()['codsuc'];
    $codigo_Vendedor = $request->getParsedBody()['codigo_Vendedor'];
    $numero_orden = $request->getParsedBody()['numero_orden'];

    $response = array();
    $db = new DbHandler();
    $resultado = $db->actualizarCotizacionApi($codsuc, $codemp, $codigo_Tienda, $codigo_Cliente, $forma_Pago, $codigo_Vendedor, $detalle, $numero_orden);

    $response["error"] = false;
    $response["numero_orden"] = $resultado["numeroCotizacion"];
    echo json_encode($response);
});
/* ACTUALIZAR COTIZACIÓN */


$app->post('/constructor/getCotizacionesApi', function ($request, $response, $args) {
    $parsedBody = $request->getParsedBody();

    $codigoCotizacion = $parsedBody['codigoCotizacion'] ?? null;
    $codigoTienda = $parsedBody['codigoTienda'] ?? null;
    $codigoEmp = $parsedBody['codigoEmp'] ?? null;
    $codigoSuc = $parsedBody['codigoSuc'] ?? null;
    $rucCliente = $parsedBody['rucCliente'] ?? null;
    $page = $parsedBody['page'] ?? null;
    $pageSize = $parsedBody['pageSize'] ?? null;
    $codigovendedor = $parsedBody['codigovendedor'] ?? null;
    $nombreCliente = $parsedBody['nombreCliente'] ?? null;
    $fechaInicio = $parsedBody['fechaInicio'] ?? null;
    $fechaFin = $parsedBody['fechaFin'] ?? null;

    $response = array();
    $db = new DbHandler();
    $resultado = $db->getCotizacionesApi($codigoCotizacion, $codigoTienda, $codigoEmp, $codigoSuc, $rucCliente, $page, $pageSize, $codigovendedor, $nombreCliente, $fechaInicio, $fechaFin);

    $response["error"] = false;
    $response["cotizacion"] = $resultado;

    echo json_encode($response);
});

//BUSCAR FILTR
$app->post('/constructor/buscarCategorias', function ($request, $response) {
    // $id_administrador = $args['id'];
    $lineas = $request->getParsedBody()['lineas'];
    $grupos = $request->getParsedBody()['grupos'];


    $response = array();
    $db = new DbHandler();
    $resultado = $db->buscarCategoriasApi($lineas, $grupos);
    $response["error"] = false;
    $response["categorias"] = $resultado;

    echo json_encode($response);
});
//BUSCAR FILTR

$app->post('/constructor/solicitarDescuento', function($request, $response){
    $numeroProforma = $request->getParsedBody()['numeroProforma'];
    $codigoTienda = $request->getParsedBody()['codigoTienda'];
    $nombreCuenta = $request->getParsedBody()['nombreCuenta'];
    $response = array();
    $db = new DbHandler();
    $resultado = $db->solicitarDescuento($numeroProforma, $codigoTienda, $nombreCuenta);
    $response["error"] = false;
    $response["msg"] = $resultado;
    echo json_encode($response);
});

$app->get('/constructor/stock', function ($request, $response, $args) {
    $codigoProducto = $request->getQueryParams()['codigo'];
    $db = new DbHandler();
    $result = $db->stock($codigoProducto);
    return $response->withJson($result);
});

$app->post('/constructor/facturar', function ($request, $response) {
    // 1) Obtener parámetros
    $numeroCotizacion = $request->getParsedBody()['numeroCotizacion'] ?? '';
    $codigo_Cliente   = $request->getParsedBody()['codigo_Cliente']   ?? '';
    $bodega           = $request->getParsedBody()['bodega']           ?? '';
    $metodoPago       = $request->getParsedBody()['metodoPago']       ?? '';
    $cobrador         = $request->getParsedBody()['cobrador']         ?? '';

    // 2) Validar parámetros obligatorios
    //    Si alguno está vacío, devolvemos error inmediato
    if (empty($numeroCotizacion) || empty($codigo_Cliente) || empty($bodega) || empty($metodoPago) || empty($cobrador)) {
        $responseData = [
            "error" => true,
            "mssg"  => "Los campos numeroCotizacion, codigo_Cliente, bodega, metodoPago y cobrador son obligatorios."
        ];
        echo json_encode($responseData);
        return $response;
    }

    // 3) Llamar a DBHandler
    $db = new DbHandler();
    $resultado = $db->crearFacturaApi($numeroCotizacion, $codigo_Cliente, $bodega, $metodoPago, $cobrador);

    // Estructura por defecto en la respuesta final
    $responseData = [
        "error" => false,
        "mssg"  => "",
        "numeroFactura" => null
    ];

    // 4) Validar el código HTTP
    //    Por ejemplo, si es >= 400, interpretamos un error
    $httpcode = $resultado['statusCode'] ?? 0;
    if ($httpcode >= 400) {
        // Forzamos error
        $responseData["error"] = true;
        // Podemos usar 'mssg' del backend si existe, sino, un genérico
        $responseData["mssg"]  = $resultado['mssg'] ?? "Error HTTP " . $httpcode . " al generar la factura.";
        
        // Si el backend .NET devolvió 'productosFaltantes'
        if (isset($resultado['productosFaltantes'])) {
            $responseData["productosFaltantes"] = $resultado['productosFaltantes'];
        }

        // Devolvemos la respuesta
        echo json_encode($responseData);
        return $response;
    }

    // 5) Ahora procesamos la respuesta JSON que retornó .NET
    //    Manejo similar al que ya tenías:

    if (!is_array($resultado)) {
        // El backend no devolvió JSON válido o la solicitud falló
        $responseData["error"] = true;
        $responseData["mssg"]  = "Respuesta inesperada del servidor.";
    } else {
        // Revisar si hay mensaje de error por stock u otro
        // 5.1) "mssg" con la palabra "error"
        if (isset($resultado['mssg']) && (strpos(strtolower($resultado['mssg']), 'error') !== false)) {
            $responseData["error"] = true;
            $responseData["mssg"]  = $resultado['mssg'];
            if (isset($resultado['productosFaltantes'])) {
                $responseData["productosFaltantes"] = $resultado['productosFaltantes'];
            }
        }
        // 5.2) Si hay productosFaltantes sin la palabra "error" en mssg
        else if (isset($resultado['productosFaltantes'])) {
            $responseData["error"] = true;
            $responseData["mssg"]  = $resultado['mssg'] ?? "No hay stock suficiente.";
            $responseData["productosFaltantes"] = $resultado['productosFaltantes'];
        }
        // 5.3) Caso de éxito: se asume que hay "numeroFactura"
        else if (isset($resultado['mssg']) && isset($resultado['numeroFactura'])) {
            $responseData["error"]         = false;
            $responseData["mssg"]          = $resultado['mssg'];
            $responseData["numeroFactura"] = $resultado['numeroFactura'];
        }
        // 5.4) Caso desconocido
        else {
            $responseData["error"] = true;
            $responseData["mssg"]  = $resultado['mssg'] ?? "No se pudo generar la factura por razones desconocidas.";
        }
    }

    // 6) Imprimir la respuesta final como JSON
    echo json_encode($responseData);
    return $response;
});


/* ======================= DATOS DE LA API DE COMISARIATO DEL CONSTRUCTOR ======================== */

$app->run();
