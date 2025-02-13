<?php

// require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
// require 'vendor/autoload.php';

/* use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception; */
/*

*/

class DbHandler
{
    private $conn;

    function __construct()
    {
        require_once dirname(__FILE__) . '/DbConnect.php';
        require_once 'PassHash.php';


        /* require_once 'PHPMailer/src/Exception.php';
        require_once 'PHPMailer/src/PHPMailer.php';
        require_once 'PHPMailer/src/SMTP.php'; */
        date_default_timezone_set('America/Guayaquil');
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
        $this->conn->set_charset('utf8');
    }

    /**
     *ADMINISTRADORES
     */


    /*TRAER CLAVE DEL ADMIN*/
    
    /*TRAER CLAVE DEL ADMIN*/


    public function getGrupos()
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT * FROM grupo");
        // $stmt->bind_param("s", $id_obra_producto);

        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        return $response;
    }

    public function getSubGrupos()
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT * FROM subgrupo");
        // $stmt->bind_param("s", $id_obra_producto);

        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        return $response;
    }

    public function getMarca()
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT * FROM marcas");
        // $stmt->bind_param("s", $id_obra_producto);

        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        return $response;
    }

    public function getProductos($searchTerm, $selectedGroups, $selectedSubgroups, $selectedBrands)
    {
        $response = array();

        // Recibir parámetros
        // Variables de filtro
        $whereClauses = [];
        $params = [];

        // Filtro de búsqueda por nombre
        if (!empty($searchTerm)) {
            $whereClauses[] = "p.nombre LIKE ?";
            $params[] = "%" . $searchTerm . "%";
        }

        // Filtro por grupos
        if (!empty($selectedGroups)) {
            $groupPlaceholders = implode(',', array_fill(0, count($selectedGroups), '?'));
            $whereClauses[] = "g.id_grupo IN ($groupPlaceholders)";
            $params = array_merge($params, $selectedGroups);
        }

        // Filtro por subgrupos
        if (!empty($selectedSubgroups)) {
            $subgroupPlaceholders = implode(',', array_fill(0, count($selectedSubgroups), '?'));
            $whereClauses[] = "sg.id_subgrupo IN ($subgroupPlaceholders)";
            $params = array_merge($params, $selectedSubgroups);
        }

        // Filtro por marcas
        if (!empty($selectedBrands)) {
            $brandPlaceholders = implode(',', array_fill(0, count($selectedBrands), '?'));
            $whereClauses[] = "m.id_marca IN ($brandPlaceholders)";
            $params = array_merge($params, $selectedBrands);
        }

        // Construir la consulta con los filtros
        $sql = "SELECT pr.*, p.id_producto, p.nombre, p.ancho, p.espesor, p.imagen, l.nombre as nombre_linea, g.nombre as nombre_grupo, sg.nombre as nombre_subgrupo, m.nombre as nombre_marca 
                FROM precios pr 
                INNER JOIN productos p ON p.id_producto = pr.cod_prod
                INNER JOIN lineas l ON l.id_linea = p.linea 
                INNER JOIN grupo g ON g.id_grupo = p.grupo 
                INNER JOIN subgrupo sg ON sg.id_subgrupo = p.subgru 
                INNER JOIN marcas m ON m.id_marca = p.marca";

        if (!empty($whereClauses)) {
            $sql .= " WHERE " . implode(" AND ", $whereClauses);
        }

        // Preparar la consulta
        $stmt = $this->conn->prepare($sql);

        // Asociar parámetros
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);

        // Ejecutar consulta
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        return $response;
    }

    /* ======================= DATOS DE LA API DE COMISARIATO DEL CONSTRUCTOR ======================== */

    //OBTENER TODOS LOS GRUPOS DESDE LA API
    public function getTokenApi()
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://192.168.1.199:14560/api/auth/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                                        "codigoVendedor": "000"
                                    }
                                    ',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        // Convertir la respuesta en un array
        $responseArray = json_decode($response, true);

        return $responseArray;
    }
    // INICIAR SESIÓN CON USUARIO Y CONTRASEÑA
    public function getLoginApi($username, $password)
    {

        $token = $this->getTokenApi()["token"];

        $curl = curl_init();

        $postData = json_encode([
            "username" => $username,
            "password" => $password
        ]);

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://192.168.1.199:14560/api/auth/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        // Convertir la respuesta en un array
        $responseArray = json_decode($response, true);



        if ($responseArray['mssg'] === "El usuario no existe o no tiene perfil de vendedor.") {
            return RECORD_DOES_NOT_EXIST;
        } else if ($responseArray['mssg'] === "Contraseña incorrecta.") {
            return OPERATION_COMPLETED;
        } else {
            return $responseArray;
        }
    }



    //OBTENER TODOS LAS SUCURSALES DESDE LA API
    public function getSucursal($codigo)
    {

        $token = $this->getTokenApi()["token"];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://192.168.1.199:14560/api/Sucursales/buscar?codigo=15',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $responseSucursal = json_decode($response, true);

        return $responseSucursal;
    }
    //OBTENER TODOS LAS SUCURSALES DESDE LA API


    public function getGruposApi()
    {

        $token = $this->getTokenApi()["token"];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://192.168.1.199:14560/api/DBTablas/grupos',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $responseGrupo = json_decode($response, true);

        return $responseGrupo;
    }

    //OBTENER TODOS LOS GRUPOS DESDE LA API

    //OBTENER TODOS LOS SUBGRUPOS DESDE LA API
    public function getSubGruposApi()
    {

        $token = $this->getTokenApi()["token"];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://192.168.1.199:14560/api/DBTablas/subgrupos',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $responseSubGrupo = json_decode($response, true);

        return $responseSubGrupo;
    }
    //OBTENER TODOS LOS SUBGRUPOS DESDE LA API

    //OBTENER TODOS LAS LINEAS DESDE LA API
    public function getLineasApi()
    {

        $token = $this->getTokenApi()["token"];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://192.168.1.199:14560/api/DBTablas/lineas',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $responseSubGrupo = json_decode($response, true);

        return $responseSubGrupo;
    }
    //OBTENER TODOS LAS LINEAS DESDE LA API

    //OBTENER TODOS LAS MARCAS DESDE LA API
    public function getMarcasApi()
    {

        $token = $this->getTokenApi()["token"];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://192.168.1.199:14560/api/DBTablas/marcas',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $responseSubGrupo = json_decode($response, true);

        return $responseSubGrupo;
    }
    //OBTENER TODOS LAS MARCAS DESDE LA API

    //OBTENER TODOS LAS TIENDAS DESDE LA API
    public function getTiendasApi()
    {

        $token = $this->getTokenApi()["token"];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://192.168.1.199:14560/api/DBTablas/tiendas',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $responseSubGrupo = json_decode($response, true);

        return $responseSubGrupo;
    }
    //OBTENER TODOS LAS TIENDAS DESDE LA API

    //INICIAR SESIÓN CON EL CODIGO DEL VENDEDOR
    public function getVendedorLogin($codigo)
    {

        $token = $this->getTokenApi()["token"];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://192.168.1.199:14560/api/Vendedores/buscar?CodigoVendedor=' . $codigo,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);


        curl_close($curl);

        $responseVendedor = json_decode($response, true);

        return $responseVendedor;
    }
    //INICIAR SESIÓN CON EL CODIGO DEL VENDEDOR

    // OBTENER TODOS LOS PRODUCTOS DESDE LA API

    public function getProductoApiBuscar($codigo_Empresa, $codigo_Sucursal, $nombre, $codigoLinea, $codigoGrupo, $codigoSubgrupo, $codigo_Tienda, $codigoMarca, $opcionBusqueda, $page, $pageSize)
    {
        $token = $this->getTokenApi()["token"];

        // Construir la URL para la API de productos
        $urlProducto = 'http://192.168.1.199:14560/api/Productos/buscar?Codigo_Empresa=' . $codigo_Empresa
            . '&Codigo_Sucursal=' . $codigo_Sucursal
            . '&page=' . $page
            . '&pageSize=' . $pageSize
            . '&Codigo_Tienda=' . $codigo_Tienda;

        if ($opcionBusqueda == "option1") {
            if (!empty($nombre)) {
                $urlProducto .= '&nombre=' . urlencode($nombre);
            }
        } else if ($opcionBusqueda == "option2") {
            if (!empty($nombre)) {
                $urlProducto .= '&codigoProducto=' . urlencode($nombre);
            }
        }
        if (!empty($codigoLinea)) {
            $urlProducto .= '&codigosLinea=' . $codigoLinea;
        }

        if (!empty($codigoGrupo)) {
            $urlProducto .= '&codigosGrupo=' . $codigoGrupo;
        }

        if (!empty($codigoSubgrupo)) {
            $urlProducto .= '&codigosSubgrupo=' . $codigoSubgrupo;
        }

        if (!empty($codigoMarca)) {
            $urlProducto .= '&codigosMarca=' . $codigoMarca;
        }

        // Agrega esta línea justo antes de curl_init para depurar:
        error_log("URL generada: " . $urlProducto);

        // Realizar la solicitud a la API de productos
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $urlProducto,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HEADER => true, // Incluye los headers en la salida
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token
            ),
        ));

        $responseProducto = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);

        // Separar el header y el cuerpo de la respuesta
        $headers = substr($responseProducto, 0, $header_size);
        $body = substr($responseProducto, $header_size);
        curl_close($curl);

        // Decodificar el cuerpo JSON de productos
        $productos = json_decode($body, true);

        // Procesar los headers para extraer la información relevante
        $headersArray = [];
        foreach (explode("\r\n", $headers) as $header) {
            if (strpos($header, 'X-Total-Count:') === 0) {
                $headersArray['X-Total-Count'] = trim(substr($header, 14));
            } elseif (strpos($header, 'X-Total-Pages:') === 0) {
                $headersArray['X-Total-Pages'] = trim(substr($header, 14));
            } elseif (strpos($header, 'X-Current-Page:') === 0) {
                $headersArray['X-Current-Page'] = trim(substr($header, 16));
            } elseif (strpos($header, 'X-Page-Size:') === 0) {
                $headersArray['X-Page-Size'] = trim(substr($header, 12));
            }
        }

        if (empty($productos)) {
            return []; // Retorna vacío si no hay productos
        }

        // Retorna un array que contiene el header y la lista de productos
        return [
            'header' => $headersArray,
            'productos' => $productos
        ];
    }

    public function getProductoApiBuscar2($codigo_Empresa, $codigo_Sucursal, $nombre, $codigoLinea, $codigoGrupo, $codigoSubgrupo, $codigo_Tienda, $codigoMarca, $opcionBusqueda, $page, $pageSize)
    {
        $token = $this->getTokenApi()["token"];

        // Construir la URL para la API de productos
        $urlProducto = 'http://192.168.1.199:14560/api/Productos/buscar?stock=1&Codigo_Empresa=' . $codigo_Empresa
            . '&Codigo_Sucursal=' . $codigo_Sucursal
            . '&page=' . $page
            . '&pageSize=' . $pageSize
            . '&Codigo_Tienda=' . $codigo_Tienda;

        if ($opcionBusqueda == "option1") {
            if (!empty($nombre)) {
                $urlProducto .= '&nombre=' . urlencode($nombre);
            }
        } else if ($opcionBusqueda == "option2") {
            if (!empty($nombre)) {
                $urlProducto .= '&codigoProducto=' . urlencode($nombre);
            }
        }

        if (!empty($codigoLinea)) {
            $urlProducto .= '&codigosLinea=' . $codigoLinea;
        }

        if (!empty($codigoGrupo)) {
            $urlProducto .= '&codigosGrupo=' . $codigoGrupo;
        }

        if (!empty($codigoSubgrupo)) {
            $urlProducto .= '&codigosSubgrupo=' . $codigoSubgrupo;
        }

        if (!empty($codigoMarca)) {
            $urlProducto .= '&codigosMarca=' . $codigoMarca;
        }

        // Agrega esta línea justo antes de curl_init para depurar:
        error_log("URL generada: " . $urlProducto);

        // Realizar la solicitud a la API de productos
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $urlProducto,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HEADER => true, // Incluye los headers en la salida
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token
            ),
        ));

        $responseProducto = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);

        // Separar el header y el cuerpo de la respuesta
        $headers = substr($responseProducto, 0, $header_size);
        $body = substr($responseProducto, $header_size);
        curl_close($curl);

        // Decodificar el cuerpo JSON de productos
        $productos = json_decode($body, true);

        // Procesar los headers para extraer la información relevante
        $headersArray = [];
        foreach (explode("\r\n", $headers) as $header) {
            if (strpos($header, 'X-Total-Count:') === 0) {
                $headersArray['X-Total-Count'] = trim(substr($header, 14));
            } elseif (strpos($header, 'X-Total-Pages:') === 0) {
                $headersArray['X-Total-Pages'] = trim(substr($header, 14));
            } elseif (strpos($header, 'X-Current-Page:') === 0) {
                $headersArray['X-Current-Page'] = trim(substr($header, 16));
            } elseif (strpos($header, 'X-Page-Size:') === 0) {
                $headersArray['X-Page-Size'] = trim(substr($header, 12));
            }
        }

        if (empty($productos)) {
            return []; // Retorna vacío si no hay productos
        }

        // Retorna un array que contiene el header y la lista de productos
        return [
            'header' => $headersArray,
            'productos' => $productos
        ];
    }

    public function getPrecioApi($codigo_Tienda, $codigo_Producto)
    {
        $token = $this->getTokenApi()["token"];


        // Primero buscar en la API de precios con el código de tienda
        $urlPrecios = 'http://192.168.1.199:14560/api/Precios/buscar?codigoTienda=' . urlencode($codigo_Tienda) . '&codigoProducto=' . $codigo_Producto;
        // Realizar la solicitud a la API de precios
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $urlPrecios,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token
            ),
        ));

        $responsePrecios = curl_exec($curl);
        curl_close($curl);

        $precios = json_decode($responsePrecios, true);

        if (empty($precios)) {
            return []; // Retorna vacío si no hay resultados en la API de precios
        }

        return $precios;
    }
    //OBTENER TODOS LOS PRODUCTOS DESDE LA API


    // OBTENER TODOS LOS CLIENTES DESDE LA API
    public function getClientesApiAll($nombre_o_ruc, $codigo_vendedor)
    {
        $token = $this->getTokenApi()["token"];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://192.168.1.199:14560/api/Clientes/buscar?ruc=' . $nombre_o_ruc . '&codigovendedor=' . $codigo_vendedor,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        // Decodificar la respuesta
        $responseCliente = json_decode($response, true);

        return $responseCliente;
    }

    // OBTENER TODOS LOS CLIENTES DESDE LA API
    public function getClientesApi($nombre_o_ruc, $codigo_vendedor)
    {
        $token = $this->getTokenApi()["token"];

        // Intentar buscar primero por nombre
        $clientes = $this->buscarClienteApi($nombre_o_ruc, $token, $codigo_vendedor);

        // Si no encuentra por nombre, intentar por RUC
        if (empty($clientes)) {
            $clientes = $this->buscarClienteApiPorRuc($nombre_o_ruc, $token, $codigo_vendedor);

            // Si no encuentra ni por nombre ni por RUC, devolver un mensaje de error
            if (empty($clientes)) {
                return ['error' => 'No se encontraron resultados por nombre ni por RUC'];
            } else {
                // Retornar los resultados encontrados
                return $clientes;
            }
        } else {
            // Retornar los resultados encontrados
            return $clientes;
        }
    }

    // Función auxiliar para realizar la búsqueda por nombre
    public function buscarClienteApi($nombre, $token, $codigo_vendedor)
    {
        return $this->realizarConsultaApi('http://192.168.1.199:14560/api/Clientes/buscar?nombre=' . urlencode($nombre), $token);
    }

    // Función auxiliar para realizar la búsqueda por RUC
    public function buscarClienteApiPorRuc($ruc, $token, $codigo_vendedor)
    {
        return $this->realizarConsultaApi('http://192.168.1.199:14560/api/Clientes/buscar?ruc=' . urlencode($ruc), $token);
    }

    // Función genérica para realizar consultas a la API
    private function realizarConsultaApi($url, $token)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        // Decodificar la respuesta
        return json_decode($response, true);
    }


    public function buscarClienteApiPorRucSelect($ruc, $token, $codigo_vendedor)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://192.168.1.199:14560/api/Clientes/buscar?ruc=' . $ruc,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        // Decodificar la respuesta
        $responseCliente = json_decode($response, true);

        return $responseCliente;
    }


    // INICIAR SESIÓN CON USUARIO Y CONTRASEÑA
    public function getClienteApiCreate($nombre_cliente, $ruc, $correo, $telefono, $direccion, $codigo_Empresa, $codigo_Sucursal, $codigo_Vendedor)
    {
        $token = $this->getTokenApi()["token"];


        $curl = curl_init();


        $postData = json_encode([
            "codigo_Empresa" => $codigo_Empresa,
            "codigo_Sucursal" => $codigo_Sucursal,
            "nombre_Cliente" => $nombre_cliente,
            "codigo_Cliente" => $ruc,
            "ruc" => $ruc,
            "direccion" => $direccion,
            "telefono" => $telefono,
            "correo" => $correo,
            "codigo_Vendedor" => $codigo_Vendedor
        ]);

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://192.168.1.199:14560/api/Clientes/crear',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token

            ),
        ));



        $response = curl_exec($curl);
        // $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);


        curl_close($curl);

        // Convertir la respuesta en un array
        $responseArray = json_decode($response, true);

        //Verificar si el mensaje indica que el RUC ya existe
        if ($responseArray['mssg'] === "El cliente con el mismo RUC ya existe.") {
            return RECORD_ALREADY_EXISTED;
        } else {
            return $responseArray;

            //Retornar la respuesta si no existe el error
        }
    }

    // INICIAR SESIÓN CON USUARIO Y CONTRASEÑA
    public function getClienteApiUpdate($nombre_cliente, $ruc, $correo, $telefono, $direccion)
    {
        $token = $this->getTokenApi()["token"];

        $curl = curl_init();

        $postData = json_encode([
            "nombres" => $nombre_cliente,
            "direccion" => $direccion,
            "telefono" => $telefono,
            "correo" => $correo
        ]);

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://192.168.1.199:14560/api/Clientes/actualizar-contacto/' . $ruc,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        // Convertir la respuesta en un array
        $responseArray = json_decode($response, true);

        //Verificar si el mensaje indica que el RUC ya existe
        if ($responseArray['mssg'] === "No se encontró el cliente con el código proporcionado.") {
            return RECORD_ALREADY_EXISTED;
        } else {
            return $responseArray;

            //Retornar la respuesta si no existe el error
        }
    }


    // OBTENER TODOS LOS CLIENTES DESDE LA API
    public function getClientesApiTable($nombre_o_ruc, $page, $pageSize, $codigoVendedor)
    {
        $token = $this->getTokenApi()["token"];

        // Realizar la primera consulta buscando por nombre
        $clientesPorNombre = $this->buscarClienteApi($nombre_o_ruc, $token, $codigoVendedor);

        // Verificar si no se encontraron resultados por nombre
        if (empty($clientesPorNombre)) {
            // Realizar la segunda consulta buscando por RUC
            $clientesPorRuc = $this->buscarClienteApiPorRuc($nombre_o_ruc, $token, $codigoVendedor);

            // Si tampoco encuentra por RUC, devolver un mensaje de error o resultados vacíos
            if (empty($clientesPorRuc)) {
                return ['error' => 'No se encontraron resultados por nombre ni por RUC'];
            }

            // Si encontró por RUC, devolver esos resultados
            return $clientesPorRuc;
        }

        // Si encontró por nombre, devolver esos resultados
        return $clientesPorNombre;
    }

    public function buscarClienteApiHeadersRUC($nombre_o_ruc, $page, $pageSize, $codigoVendedor)
    {
        $token = $this->getTokenApi()["token"];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://192.168.1.199:14560/api/Clientes/buscar?ruc=' . $nombre_o_ruc . '&page=' . $page . '&pageSize=' . $pageSize . '&codigovendedor=' . $codigoVendedor,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_HEADER => true, // Incluye los headers en la salida
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);

        $headers = substr($response, 0, $header_size);
        curl_close($curl);

        // Procesar los headers para extraer la información relevante
        $headersArray = [];
        foreach (explode("\r\n", $headers) as $header) {
            if (strpos($header, 'X-Total-Count:') === 0) {
                $headersArray['X-Total-Count'] = trim(substr($header, 14));
            } elseif (strpos($header, 'X-Total-Pages:') === 0) {
                $headersArray['X-Total-Pages'] = trim(substr($header, 14));
            } elseif (strpos($header, 'X-Current-Page:') === 0) {
                $headersArray['X-Current-Page'] = trim(substr($header, 16));
            } elseif (strpos($header, 'X-Page-Size:') === 0) {
                $headersArray['X-Page-Size'] = trim(substr($header, 12));
            }
        }

        return $headersArray;
    }





    public function crearCotizacionApi($codsuc, $codemp, $codigo_Tienda, $codigo_Cliente, $forma_Pago, $codigo_Vendedor, $detalle)
    {

        $token = $this->getTokenApi()["token"];

        $curl = curl_init();

        $data = array(
            "codsuc" => $codsuc,
            "codemp" => $codemp,
            "codigo_Tienda" => $codigo_Tienda,
            "codigo_Cliente" => $codigo_Cliente,
            "forma_Pago" => $forma_Pago,
            "codigo_Vendedor" => $codigo_Vendedor,
            "detalle" => $detalle // Debe ser un array de objetos con "codigo_Producto" y "cantidad"
        );

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://192.168.1.199:14560/api/Cotizacion/crear',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // Decodificar la respuesta
        $responseCotizacion = json_decode($response, true);

        return $responseCotizacion;
    }


    public function getCotizacionesApi($codigoCotizacion, $codigoTienda, $codigoEmp, $codigoSuc, $rucCliente, $page, $pageSize, $codigovendedor, $nombreCliente, $fechaInicio, $fechaFin)
    {

        $token = $this->getTokenApi()["token"];
        $codigoCotizacion = intval($codigoCotizacion);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://192.168.1.199:14560/api/Cotizacion/buscar?codigoCotizacion=' . $codigoCotizacion . '&rucCliente=' . $rucCliente . '&page=' . $page . '&pageSize=' . $pageSize . '&codigovendedor=' . $codigovendedor . '&nombreCliente=' . $nombreCliente . '&fechaInicio=' . $fechaInicio . '&fechaFin=' . $fechaFin,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HEADER => true, // Incluye los headers en la salida
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        // Separar el header y el cuerpo de la respuesta
        $headers = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        // Separar el header y el cuerpo de la respuesta
        $headers = substr($response, 0, $header_size);
        curl_close($curl);
        // Decodificar la respuesta
        $responseCotizacion = json_decode($body, true);

        // Procesar los headers para extraer la información relevante
        $headersArray = [];
        foreach (explode("\r\n", $headers) as $header) {
            if (strpos($header, 'X-Total-Count:') === 0) {
                $headersArray['X-Total-Count'] = trim(substr($header, 14));
            } elseif (strpos($header, 'X-Total-Pages:') === 0) {
                $headersArray['X-Total-Pages'] = trim(substr($header, 14));
            } elseif (strpos($header, 'X-Current-Page:') === 0) {
                $headersArray['X-Current-Page'] = trim(substr($header, 16));
            } elseif (strpos($header, 'X-Page-Size:') === 0) {
                $headersArray['X-Page-Size'] = trim(substr($header, 12));
            }
        }

        if (empty($responseCotizacion)) {
            return []; // Retorna vacío si no hay productos
        }

        // Retorna un array que contiene el header y la lista de productos
        return [
            'header' => $headersArray,
            'cotizacion' => $responseCotizacion
        ];




        return $responseCotizacion;
    }

    public function getCotizacionesApiHeader($codigoCotizacion, $codigoTienda, $codigoEmp, $codigoSuc, $rucCliente)
    {
        $token = $this->getTokenApi()["token"];
        $codigoCotizacion = intval($codigoCotizacion);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://192.168.1.199:14560/api/Cotizacion/buscar?codigoTienda=' . $codigoTienda . '&codigoEmp=' . $codigoEmp . '&codigoSuc=' . $codigoSuc . '&codigoCotizacion=' . $codigoCotizacion . '&rucCliente=' . $rucCliente,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_HEADER => true, // Incluye los headers en la salida
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);

        $headers = substr($response, 0, $header_size);
        curl_close($curl);

        // Procesar los headers para extraer la información relevante
        $headersArray = [];
        foreach (explode("\r\n", $headers) as $header) {
            if (strpos($header, 'X-Total-Count:') === 0) {
                $headersArray['X-Total-Count'] = trim(substr($header, 14));
            } elseif (strpos($header, 'X-Total-Pages:') === 0) {
                $headersArray['X-Total-Pages'] = trim(substr($header, 14));
            } elseif (strpos($header, 'X-Current-Page:') === 0) {
                $headersArray['X-Current-Page'] = trim(substr($header, 16));
            } elseif (strpos($header, 'X-Page-Size:') === 0) {
                $headersArray['X-Page-Size'] = trim(substr($header, 12));
            }
        }

        return $headersArray;
    }

    // Función auxiliar para realizar la búsqueda por RUC
    public function buscarClienteApiPorRucTable($ruc, $page, $pageSize, $codigoVendedor, $nombre)
    {
        $token = $this->getTokenApi()["token"];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://192.168.1.199:14560/api/Clientes/buscar?ruc=' . $ruc . '&page=' . $page . '&pageSize=' . $pageSize . '&codigovendedor=' . $codigoVendedor . '&nombre=' . $nombre,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HEADER => true, // Incluye los headers en la salida
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        // Separar el header y el cuerpo de la respuesta
        $headers = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        // Separar el header y el cuerpo de la respuesta
        $headers = substr($response, 0, $header_size);
        curl_close($curl);
        // Decodificar la respuesta
        $responseCliente = json_decode($body, true);

        // Procesar los headers para extraer la información relevante
        $headersArray = [];
        foreach (explode("\r\n", $headers) as $header) {
            if (strpos($header, 'X-Total-Count:') === 0) {
                $headersArray['X-Total-Count'] = trim(substr($header, 14));
            } elseif (strpos($header, 'X-Total-Pages:') === 0) {
                $headersArray['X-Total-Pages'] = trim(substr($header, 14));
            } elseif (strpos($header, 'X-Current-Page:') === 0) {
                $headersArray['X-Current-Page'] = trim(substr($header, 16));
            } elseif (strpos($header, 'X-Page-Size:') === 0) {
                $headersArray['X-Page-Size'] = trim(substr($header, 12));
            }
        }

        if (empty($responseCliente)) {
            return []; // Retorna vacío si no hay productos
        }

        // Retorna un array que contiene el header y la lista de productos
        return [
            'header' => $headersArray,
            'clientes' => $responseCliente
        ];
        return $responseCliente;
    }

    public function actualizarCotizacionApi($codsuc, $codemp, $codigo_Tienda, $codigo_Cliente, $forma_Pago, $codigo_Vendedor, $detalle, $numero_orden)
    {

        $token = $this->getTokenApi()["token"];

        $curl = curl_init();

        $data = array(
            "codsuc" => $codsuc,
            "codemp" => $codemp,
            "codigo_Tienda" => $codigo_Tienda,
            "codigo_Cliente" => $codigo_Cliente,
            "forma_Pago" => $forma_Pago,
            "codigo_Vendedor" => $codigo_Vendedor,
            "detalle" => $detalle // Debe ser un array de objetos con "codigo_Producto" y "cantidad"
        );

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://192.168.1.199:14560/api/Cotizacion/actualizar/' . $numero_orden,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // Decodificar la respuesta
        $responseCotizacion = json_decode($response, true);

        return $responseCotizacion;
    }


    //GET DE FILTROS
    public function buscarCategoriasApi($lineas, $grupos)
    {
        $token = $this->getTokenApi()["token"];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://192.168.1.199:14560/api/DBTablas/buscarCategorias?grupos=' . $grupos . '&lineas=' . $lineas,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        // Decodificar la respuesta
        $responseCategoria = json_decode($response, true);

        return $responseCategoria;
    }
    
    public function solicitarDescuento($numeroProforma, $codigoTienda, $nombreCuenta)
    {

        $token = $this->getTokenApi()["token"];

        $curl = curl_init();

        $data = array(
            'Codemp' => '15', // Opcional, con valor por defecto
            'Codsuc' => '15', // Opcional, con valor por defecto
            'Tienda' => $codigoTienda,
            'Numero' => (float)$numeroProforma,
            'UsuarioSolicita' => $nombreCuenta // Reemplaza con el usuario que realiza la solicitud
        );


        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://192.168.1.199:14560/api/Descuentos/solicitar',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // Decodificar la respuesta
        $responseCotizacion = json_decode($response, true);
        return $responseCotizacion;

    }

    public function stock($producto)
    {
        $urlProducto = 'http://192.168.1.199:14560/api/Productos/stock?codigoProducto=' . $producto;
        $token = $this->getTokenApi()["token"];
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $urlProducto,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $responseSucursal = json_decode($response, true);

        return $responseSucursal;
    }
    
    public function crearFacturaApi($numeroCotizacion, $codigo_Cliente, $bodega, $metodoPago, $cobrador)
    {
        // 1) Obtener token
        $token = $this->getTokenApi()["token"];
        // 2) Preparar datos para enviar al backend .NET
        $data = array(
            "numeroCotizacion" => $numeroCotizacion,
            "codigo_Cliente"   => $codigo_Cliente,
            "bodega"           => $bodega,
            "metodoPago"       => $metodoPago,
            "cobrador"         => $cobrador
        );

        // 3) Configurar cURL
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://192.168.1.199:14560/api/Factura/facturar',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ),
        ));

        // 4) Ejecutar cURL y obtener respuesta
        $response   = curl_exec($curl);
        $httpcode   = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($curl);

        // 5) Cerrar cURL
        curl_close($curl);

        // 6) Decodificar la respuesta
        $responsefactura = json_decode($response, true);

        // 7) Si no es un array, forzamos uno vacío
        if (!is_array($responsefactura)) {
            $responsefactura = [];
        }

        // 8) Adjuntar código HTTP y posible error de cURL
        $responsefactura['statusCode'] = $httpcode;
        if (!empty($curl_error)) {
            $responsefactura['curlError'] = $curl_error;
        }

        // 9) Retornar la respuesta
        return $responsefactura;
    }


    /* ======================= DATOS DE LA API DE COMISARIATO DEL CONSTRUCTOR ======================== */
}