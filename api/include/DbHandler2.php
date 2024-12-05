<?php

class DbHandler2
{
    private $conn;

    function __construct()
    {
        require_once dirname(__FILE__) . '/DbConnect.php';
        require_once 'PassHash.php';
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
    public function adminlogin($usuario, $clave)
    {
        $stmt = $this->conn->prepare("SELECT clave FROM administradores WHERE usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $stmt->bind_result($password_hash);
        $stmt->store_result(); //Devuelve un objeto de resultados almacenado en buffer o false si ocurrió un error.
        if ($stmt->num_rows > 0) { //Obtiene el número de filas de un resultado
            $stmt->fetch();
            $stmt->close();

            if (PassHash::check_password($password_hash, $clave)) {
                return 2;
            } else {
                return 1;
            }
        } else {
            $stmt->close();
            return 0;
        }
    }
    /*TRAER CLAVE DEL ADMIN*/

    public function getAdminByUsuario($usuario)
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT * from administradores where usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $response = array(
                'id_administrador' => $row['id_administrador'],
                'usuario' => utf8_encode($row['usuario']),
            );
            return $response;
        } else return RECORD_DOES_NOT_EXIST;
    }


    public function createAdmin($usuario, $clave)
    {
        if (!$this->isAdminExists($usuario)) {
            $password_hash = PassHash::hash($clave);
            $stmt = $this->conn->prepare("INSERT INTO administradores(usuario, clave) values(?,?)");
            $stmt->bind_param("ss", $usuario, $password_hash);
            $result = $stmt->execute();
            $stmt->close();
            if ($result) {
                return RECORD_CREATED_SUCCESSFULLY;
            } else {
                return RECORD_CREATION_FAILED;
            }
        } else {
            return RECORD_ALREADY_EXISTED;
        }
    }


    public function isAdminExists($usuario)
    {
        $stmt = $this->conn->prepare("SELECT usuario from administradores WHERE usuario = ? AND estado = 'A'");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    public function getAllAdmins()
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT id_administrador, usuario, estado, fecha_creacion, fecha_actualizacion from administradores WHERE estado = 'A'");
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        return $response;
    }




    public function updateAdminPassword($usuario, $password)
    {
        if ($this->isAdminExists($usuario)) {
            $password_hash = PassHash::hash($password);
            $stmt = $this->conn->prepare("UPDATE administradores SET clave = ? WHERE usuario = ?");
            $stmt->bind_param("ss", $password_hash, $usuario);
            $result = $stmt->execute();
            $stmt->close();
            if ($result) {
                return RECORD_UPDATED_SUCCESSFULLY;
            } else {
                return RECORD_UPDATED_FAILED;
            }
        } else return RECORD_DOES_NOT_EXIST;
    }



    public function getAdminById($id_administrador)
    {
        $stmt = $this->conn->prepare("SELECT id_administrador, usuario, estado, fecha_creacion, fecha_actualizacion from administradores where id_administrador = ?");
        $stmt->bind_param("s", $id_administrador);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row;
        } else return RECORD_DOES_NOT_EXIST;
    }


    public function deleteAdmin($id_administrador)
    {
        return $this->deleteRecord("administradores", "id_administrador", $id_administrador);
    }

    /* SOLICITUDES HERRAMIENTAS */
    public function getAllSolicitudesHerramientas()
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT s.*, h.descripcion, CONCAT(t.nombres, ' ',t.apellidos ) as tecnico_nombres
        FROM solicitudes_herramientas s
        INNER JOIN herramientas h ON h.id_herramienta = s.id_herramienta
        INNER JOIN tecnicos t ON t.id_tecnico = s.id_tecnico
        WHERE s.estado IN ('A', 'S', 'D')");

        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        return $response;
    }

    public function aprobarSolicitudHerramienta($id_solicitud, $nota, $id_administrador)
    {
        $solicitud = $this->getSolicitudHerrramientaById($id_solicitud);

        if ($solicitud != RECORD_DOES_NOT_EXIST) {

            $herramienta = $this->getHerramientaById($solicitud["id_herramienta"]);

            if ($herramienta["cantidad"] >= $solicitud["cantidad"]) {
                $fecha_aprobada = date('Y-m-d');

                $stmt = $this->conn->prepare("UPDATE solicitudes_herramientas s 
                JOIN herramientas h on h.id_herramienta = s.id_herramienta 
                SET h.cantidad = (h.cantidad - ?), s.estado = 'A', s.fecha_aprobada = ?, s.nota_entrega = ?, s.id_administrador = ?
                WHERE id_solicitud = ?");
                $stmt->bind_param("sssss", $solicitud["cantidad"], $fecha_aprobada, $nota, $id_administrador, $id_solicitud);
                $result = $stmt->execute();
                $affected_rows = $stmt->affected_rows; // Obtener el número de filas afectadas
                $stmt->close();

                if ($result && $affected_rows > 0) {
                    return RECORD_UPDATED_SUCCESSFULLY;
                } else if (!$result && $affected_rows < 1) {
                    return RECORD_UPDATED_FAILED;
                }
            } else {
                return "no_stock";
            }
        } else return $solicitud;
    }

    public function devolverHerramienta($id_solicitud, $nota,  $id_administrador)
    {
        $solicitud = $this->getSolicitudHerrramientaById($id_solicitud);

        if ($solicitud != RECORD_DOES_NOT_EXIST) {


            $fecha_devuelto = date('Y-m-d');

            $stmt = $this->conn->prepare("UPDATE solicitudes_herramientas s 
                JOIN herramientas h on h.id_herramienta = s.id_herramienta 
                SET h.cantidad = (h.cantidad + ?), s.estado = 'D', s.fecha_devuelto = ?, s.nota_devolucion = ?, s.id_administrador = ?
                WHERE id_solicitud = ?");
            $stmt->bind_param("sssss", $solicitud["cantidad"], $fecha_devuelto, $nota,  $id_administrador, $id_solicitud);
            $result = $stmt->execute();
            $affected_rows = $stmt->affected_rows; // Obtener el número de filas afectadas
            $stmt->close();

            if ($result && $affected_rows > 0) {
                return RECORD_UPDATED_SUCCESSFULLY;
            } else if (!$result && $affected_rows < 1) {
                return RECORD_UPDATED_FAILED;
            }
        } else return $solicitud;
    }

    public function getSolicitudHerrramientaById($id_solicitud)
    {
        $stmt = $this->conn->prepare("SELECT * from solicitudes_herramientas where id_solicitud = ?");
        $stmt->bind_param("s", $id_solicitud);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row;
        } else return RECORD_DOES_NOT_EXIST;
    }


    /* SOLICITUDES PARTES */
    public function getAllSolicitudesPartes()
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT s.*, p.devolver, p.descripcion, CONCAT(t.nombres, ' ',t.apellidos ) as tecnico_nombres
        FROM solicitudes_partes s
        INNER JOIN partes p ON p.id_parte = s.id_parte
        INNER JOIN tecnicos t ON t.id_tecnico = s.id_tecnico
        WHERE s.estado IN ('A', 'S', 'D')");

        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        return $response;
    }

    public function aprobarSolicitudParte($id_solicitud, $nota, $id_administrador)
    {
        $solicitud = $this->getSolicitudParteById($id_solicitud);

        if ($solicitud != RECORD_DOES_NOT_EXIST) {

            $parte = $this->getParteById($solicitud["id_parte"]);

            if ($parte["cantidad"] >= $solicitud["cantidad"]) {

                $fecha_aprobada = date('Y-m-d');

                $stmt = $this->conn->prepare("UPDATE solicitudes_partes s 
                JOIN partes p on p.id_parte = s.id_parte 
                SET p.cantidad = (p.cantidad - ?), s.estado = 'A', s.fecha_aprobada = ?, s.nota_entrega = ?, s.id_administrador = ?
                WHERE id_solicitud = ?");
                $stmt->bind_param("sssss", $solicitud["cantidad"], $fecha_aprobada, $nota, $id_administrador, $id_solicitud);
                $result = $stmt->execute();
                $affected_rows = $stmt->affected_rows; // Obtener el número de filas afectadas
                $stmt->close();

                if ($result && $affected_rows > 0) {
                    return RECORD_UPDATED_SUCCESSFULLY;
                } else if (!$result && $affected_rows < 1) {
                    return RECORD_UPDATED_FAILED;
                }
            } else {
                return "no_stock";
            }
        } else return $solicitud;
    }

    public function getSolicitudParteById($id_solicitud)
    {
        $stmt = $this->conn->prepare("SELECT * from solicitudes_partes where id_solicitud = ?");
        $stmt->bind_param("s", $id_solicitud);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row;
        } else return RECORD_DOES_NOT_EXIST;
    }

    public function devolverParte($id_solicitud, $nota,  $id_administrador, $cantidad_devuelta)
    {
        $solicitud = $this->getSolicitudParteById($id_solicitud);

        if ($solicitud != RECORD_DOES_NOT_EXIST) {

            $fecha_devuelto = date('Y-m-d');

            $stmt = $this->conn->prepare("UPDATE solicitudes_partes s 
                JOIN partes p on p.id_parte = s.id_parte 
                SET p.cantidad = (p.cantidad + ?), s.estado = 'D', s.fecha_devuelto = ?, s.nota_devolucion = ?, s.id_administrador = ?, s.cantidad_devuelta = ?
                WHERE id_solicitud = ?");
            $stmt->bind_param("ssssss", $cantidad_devuelta, $fecha_devuelto, $nota,  $id_administrador, $cantidad_devuelta, $id_solicitud);
            $result = $stmt->execute();
            $affected_rows = $stmt->affected_rows; // Obtener el número de filas afectadas
            $stmt->close();

            if ($result && $affected_rows > 0) {
                return RECORD_UPDATED_SUCCESSFULLY;
            } else if (!$result && $affected_rows < 1) {
                return RECORD_UPDATED_FAILED;
            }
        } else return $solicitud;
    }

    /**
     *ADMINISTRADORES
     */



    /* BORRAR UN REGISTRO DE UNA TABLA */
    private function deleteRecord($tabla, $campo, $id)
    {
        $sql_statement = "UPDATE " . $tabla . " SET estado = 'E' WHERE " . $campo . " = ?";
        $stmt = $this->conn->prepare($sql_statement);
        $stmt->bind_param("s", $id);
        $result = $stmt->execute();
        $affected_rows = $stmt->affected_rows; // Obtener el número de filas afectadas
        $stmt->close();

        if ($result && $affected_rows > 0) {
            return RECORD_UPDATED_SUCCESSFULLY;
        } else if (!$result && $affected_rows < 1) {
            return RECORD_UPDATED_FAILED;
        } else
            return RECORD_DOES_NOT_EXIST;
    }
    /* BORRAR UN REGISTRO DE UNA TABLA */


    /**
     *CLIENTES
     */
    public function createCliente($nombres, $apellidos, $cedula, $telefono, $correo, $direccion)
    {
        if (!$this->isClienteExists($cedula)) {
            $stmt = $this->conn->prepare("INSERT INTO clientes(nombres, apellidos, cedula, telefono, correo, direccion) values(?,?,?,?,?,?)");
            $stmt->bind_param("ssssss", $nombres, $apellidos, $cedula, $telefono, $correo, $direccion);
            $result = $stmt->execute();
            $stmt->close();
            if ($result) {
                return RECORD_CREATED_SUCCESSFULLY;
            } else
                return RECORD_CREATION_FAILED;
        } else
            return RECORD_ALREADY_EXISTED;
    }

    public function isClienteExists($cedula_id, $usar_id = false)

    {

        if ($usar_id) {
            $stmt = $this->conn->prepare("SELECT cedula from clientes WHERE id_cliente = ? AND estado = 'A'");
            $stmt->bind_param("s", $cedula_id);
        } else {
            $stmt = $this->conn->prepare("SELECT cedula from clientes WHERE cedula = ? AND estado = 'A'");
            $stmt->bind_param("s", $cedula_id);
        }

        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    public function editCliente($id_cliente, $nombres, $apellidos, $cedula, $telefono, $correo, $direccion)
    {
        if ($this->isClienteExists($id_cliente, true)) {
            $stmt = $this->conn->prepare("UPDATE clientes SET nombres = ?, apellidos = ?, cedula = ?, telefono = ?, correo = ?, direccion = ? WHERE id_cliente = ?");
            $stmt->bind_param("sssssss", $nombres, $apellidos, $cedula, $telefono, $correo, $direccion, $id_cliente);
            $result = $stmt->execute();
            $stmt->close();
            if ($result) {
                return RECORD_UPDATED_SUCCESSFULLY;
            } else
                return RECORD_UPDATED_FAILED;
        } else
            return RECORD_DOES_NOT_EXIST;
    }


    public function getAllClientes()
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT * from clientes WHERE estado = 'A'");
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        return $response;
    }


    public function getClienteById($id_cliente)
    {
        $stmt = $this->conn->prepare("SELECT * from clientes where id_cliente = ?");
        $stmt->bind_param("s", $id_cliente);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row;
        } else return RECORD_DOES_NOT_EXIST;
    }


    public function deleteCliente($id_cliente)
    {
        return $this->deleteRecord("clientes", "id_cliente", $id_cliente);
    }


    /**
     *CLIENTES
     */


    /**
     *TECNICOS
     */

    /*TRAER CLAVE DEL TECNICO*/
    public function tecnicoLogin($usuario, $clave)
    {
        $stmt = $this->conn->prepare("SELECT clave FROM tecnicos WHERE usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $stmt->bind_result($password_hash);
        $stmt->store_result(); //Devuelve un objeto de resultados almacenado en buffer o false si ocurrió un error.
        if ($stmt->num_rows > 0) { //Obtiene el número de filas de un resultado
            $stmt->fetch();
            $stmt->close();

            if (PassHash::check_password($password_hash, $clave)) {
                return 2;
            } else {
                return 1;
            }
        } else {
            $stmt->close();
            return 0;
        }
    }
    /*TRAER CLAVE DEL TECNICO*/

    public function getTecnicoByUsuario($usuario)
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT * from tecnicos where usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $response = array(
                'id_tecnico' => $row['id_tecnico'],
                'usuario' => utf8_encode($row['usuario']),
                'nombres' => utf8_encode($row['nombres']),
                'apellidos' => utf8_encode($row['apellidos']),
                'correo' => utf8_encode($row['correo']),
            );
            return $response;
        } else return RECORD_DOES_NOT_EXIST;
    }

    public function createTecnico($nombres, $apellidos, $telefono, $usuario, $clave, $correo)
    {

        if (!$this->isTecnicoExists($usuario)) {

            $password_hash = PassHash::hash($clave);
            $stmt = $this->conn->prepare("INSERT INTO tecnicos(nombres, apellidos, telefono, usuario, clave, correo) values(?,?,?,?,?,?)");
            $stmt->bind_param("ssssss", $nombres, $apellidos, $telefono, $usuario, $password_hash, $correo);
            $result = $stmt->execute();
            $stmt->close();
            if ($result) {
                return RECORD_CREATED_SUCCESSFULLY;
            } else
                return RECORD_CREATION_FAILED;
        } else
            return RECORD_ALREADY_EXISTED;
    }


    public function isTecnicoExists($usuario_id, $usar_id = false)
    {
        if ($usar_id) {
            $stmt = $this->conn->prepare("SELECT usuario from tecnicos WHERE id_tecnico = ? AND estado = 'A'");
            $stmt->bind_param("s", $usuario_id);
        } else {
            $stmt = $this->conn->prepare("SELECT usuario from tecnicos WHERE usuario = ? AND estado = 'A'");
            $stmt->bind_param("s", $usuario_id);
        }

        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }


    public function editTecnico($id_tecnico, $nombres, $apellidos, $telefono, $usuario, $clave, $correo)
    {

        if ($this->isTecnicoExists($id_tecnico, true)) {

            if ($clave == "") {
                $stmt = $this->conn->prepare("UPDATE tecnicos SET nombres = ?, apellidos = ?, telefono = ?, usuario = ?, correo = ? WHERE id_tecnico = ?");
                $stmt->bind_param("ssssss",  $nombres, $apellidos, $telefono, $usuario, $correo, $id_tecnico);
            } else {
                $password_hash = PassHash::hash($clave);
                $stmt = $this->conn->prepare("UPDATE tecnicos SET nombres = ?, apellidos = ?, telefono = ?, usuario = ?, clave = ?, correo = ? WHERE id_tecnico = ?");
                $stmt->bind_param("sssssss",  $nombres, $apellidos, $telefono, $usuario, $password_hash, $correo, $id_tecnico);
            }

            $result = $stmt->execute();
            $stmt->close();
            if ($result) {
                return RECORD_UPDATED_SUCCESSFULLY;
            } else
                return RECORD_UPDATED_FAILED;
        } else
            return RECORD_DOES_NOT_EXIST;
    }

    public function getAllTecnicos()
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT id_tecnico, nombres, apellidos, telefono, usuario, correo, estado, fecha_creacion, fecha_actualizacion from tecnicos WHERE estado = 'A'");
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        return $response;
    }

    public function getTecnicoById($id_tecnico)
    {
        $stmt = $this->conn->prepare("SELECT id_tecnico, nombres, apellidos, telefono, usuario, correo, estado, fecha_creacion, fecha_actualizacion from tecnicos where id_tecnico = ?");
        $stmt->bind_param("s", $id_tecnico);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row;
        } else return RECORD_DOES_NOT_EXIST;
    }


    public function deleteTecnico($id_tecnico)
    {
        return $this->deleteRecord("tecnicos", "id_tecnico", $id_tecnico);
    }

    public function getAllPendientes($id_tecnico)
    {
        // var_dump($id_tecnico);
        $response = array();

        $stmt = $this->conn->prepare("SELECT * FROM obras o
        WHERE o.estado = 'A' AND o.id_tecnico = ?");
        $stmt->bind_param("s", $id_tecnico);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            // if (empty($this->getAllMedicionesProductos($row["id_obra"]))) {
            $obra_producto = $this->getAllMedicionesProductos($row["id_obra"]);
            $contador = 0;
            $contadorObraProducto = count($obra_producto);

            foreach ($obra_producto as $obra_p) {
                if ($obra_p["status"] != "pendiente") {
                    $contador = $contador + 1;
                }
            }

            if ($contador != $contadorObraProducto) {
                $row["cliente"] = $this->getClienteById($row["id_cliente"]);
                $row["status"] = "pendiente";
                $response[] = $row;
            } else {
                $row["cliente"] = $this->getClienteById($row["id_cliente"]);
                $row["status"] = "realizada";
                $response[] = $row;
            }

            // $response[] = $row;
        }

        /* $response = array(
            // "tareas" => $this->getAllTareasTecnico($id_tecnico),
            "tareas" => "",
            "mediciones" => $this->getAllMedicionesTecnico($id_tecnico),
        ); */

        return $response;
    }


    public function getAllTareasPendientes($id_tecnico)
    {
        $response = array();

        // Consulta SQL para seleccionar solo las obras donde todas las tareas tienen asignacion = 1
        $stmt = $this->conn->prepare("
        SELECT o.*, cl.nombres, cl.apellidos 
        FROM tareas t
        INNER JOIN modulaciones m ON m.id_modulacion = t.id_modulacion
        INNER JOIN obras_productos op ON op.id_obras_productos = m.id_obras_productos
        INNER JOIN obras o ON o.id_obra = op.id_obra
        INNER JOIN clientes cl ON cl.id_cliente = o.id_cliente
        WHERE t.id_tecnico = ? 
        AND NOT EXISTS (
            SELECT 1 
            FROM tareas t2 
            WHERE t2.id_modulacion = t.id_modulacion 
            AND t2.asignacion != '1'
        )
        GROUP BY o.id_obra
    ");
        $stmt->bind_param("s", $id_tecnico);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $row["obras_productos"] = $this->getProductosObra($row["id_obra"]);
            $response[] = $row;
        }

        return $response;
    }



    // public function getAllTareasPendientes($id_tecnico)
    // {
    //     // var_dump($id_tecnico);
    //     $response = array();

    //     $stmt = $this->conn->prepare("SELECT o.*, cl.nombres, cl.apellidos FROM tareas t
    //     INNER JOIN modulaciones m ON m.id_modulacion = t.id_modulacion
    //     INNER JOIN obras_productos op ON op.id_obras_productos = m.id_obras_productos
    //     INNER JOIN obras o ON o.id_obra = op.id_obra
    //     INNER JOIN clientes cl ON cl.id_cliente = o.id_cliente WHERE t.id_tecnico = ? GROUP BY o.id_obra");
    //     $stmt->bind_param("s", $id_tecnico);
    //     $stmt->execute();
    //     $result = $stmt->get_result();

    //     while ($row = $result->fetch_assoc()) {
    //         // if (empty($this->getAllMedicionesProductos($row["id_obra"]))) {
    //         $row["obras_productos"] = $this->getProductosObra($row["id_obra"]);
    //         $response[] = $row;
    //         // $response["tareas"] = $this->getAllTareasTecnico($id_tecnico);
    //     }
    //     return $response;
    // }


    public function getAllMedicionesProductos($id_obra)
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT * FROM obras_productos WHERE id_obra = ?");
        $stmt->bind_param("s", $id_obra);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $row["mediciones"] = empty($this->getAllMediciones($row["id_obra"])) ? false : true;
            if (empty($this->getAllMediciones($row["id_obras_productos"]))) {
                $row["status"] = "pendiente";
                $response[] = $row;
            } else {
                $row["status"] = "realizada";
                $response[] = $row;
            }
        }
        return $response;
    }

    public function getAllMedicionesTecnico($id_tecnico)
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT op.*, CONCAT(c.nombres, ' ',c.apellidos ) AS cliente, o.nombre AS obra, p.nombre AS producto
        FROM obras_productos op 
        INNER JOIN obras o ON o.id_obra = op.id_obra
        INNER JOIN clientes c ON c.id_cliente = o.id_cliente
        INNER JOIN productos p ON p.id_producto = op.id_producto
        WHERE op.estado = 'A' AND op.id_tecnico = ?");
        $stmt->bind_param("s", $id_tecnico);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            if (empty($this->getAllMediciones($row["id_obra"]))) {
                $response[] = $row;
            }
        }
        return $response;
    }

    public function getAllMediciones($id_obra_producto)
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT *
        FROM mediciones 
        WHERE estado = 'A' AND id_obra_producto = ?");
        $stmt->bind_param("s", $id_obra_producto);

        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        return $response;
    }


    public function getObraByProductoId($id_obras_productos)
    {
        $stmt = $this->conn->prepare("SELECT op.*, CONCAT(c.nombres, ' ',c.apellidos ) AS cliente, o.nombre AS obra, p.nombre AS producto
        FROM obras_productos op 
        INNER JOIN obras o ON o.id_obra = op.id_obra
        INNER JOIN clientes c ON c.id_cliente = o.id_cliente
        INNER JOIN productos p ON p.id_producto = op.id_producto
        WHERE op.estado = 'A' AND op.id_obras_productos = ?");
        $stmt->bind_param("s", $id_obras_productos);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row;
        } else return RECORD_DOES_NOT_EXIST;
    }

    public function getTecnicoObra($id_obra)
    {
        $stmt = $this->conn->prepare("SELECT * FROM obras o
        WHERE o.estado = 'A' AND o.id_obra = ?");
        $stmt->bind_param("s", $id_obra);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $row["cliente"] = $this->getClienteById($row["id_cliente"]);
            return $row;
        } else return RECORD_DOES_NOT_EXIST;
    }

    /* SOLICITUD HERRAMIENTA */

    public function getProductosObra($id_obra)
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT op.*, p.nombre, p.id_categoria, c.nombre as nombre_categoria
        FROM obras_productos op
        INNER JOIN productos p ON p.id_producto = op.id_producto
        INNER JOIN categorias c ON c.id_categoria = p.id_categoria
        WHERE op.estado = 'A' and c.estado = 'A' AND op.id_obra = ?");
        $stmt->bind_param("s", $id_obra);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $row["mediciones"] = empty($this->getAllMedicionesByObra($row["id_obras_productos"])) ? false : true;
            $row["mediciones"] = $this->getAllMedicionesByObra($row["id_obras_productos"]);
            $row["tareas"] = $this->getAllTareasTecnico($row["id_obras_productos"]);
            if ($row["id_categoria"] == "1") {
                $row["detalleGaraje"] = $this->getDetalleGaraje($row["id_obras_productos"]);
            } else if ($row["id_categoria"] == "2") {
                $row["detallePeatonal"] = $this->getDetallePeatonal($row["id_obras_productos"]);
            }
            $row["productos"] = $this->getProductoById($row["id_producto"]);

            $response[] = $row;
        }
        return $response;
    }

    /*GDETALLE DE GARAJE*/
    public function getDetalleGaraje($id_obra_producto)
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT dg.*, m.nombre as mecanismo, fs.nombre as fijo, mt.nombre as motor from detalle_garaje dg INNER JOIN mecanismos m ON m.id = dg.id_mecanismo 
                                           INNER JOIN fijo_superior fs ON fs.id = dg.id_fijo_superior
                                           INNER JOIN motores mt ON mt.id = dg.id_motor WHERE dg.estado = 'A' AND dg.id_obra_producto =?");
        $stmt->bind_param("s", $id_obra_producto);

        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        return $response;
    }
    /* DETALLE DE GARAJE*/

    /*GDETALLE DE GARAJE*/
    public function getDetallePeatonal($id_obra_producto)
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT * FROM detalle_peatonal WHERE id_obra_productos =?");
        $stmt->bind_param("s", $id_obra_producto);

        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response["agarradera"] = $this->getPartePeatonal($row["tipo_agarradera"]);
            $response["chapa"] = $this->getPartePeatonal($row["tipo_chapa"]);
            $response[] = $row;
        }
        return $response;
    }
    /*DETALLE DE PEATONALs */

    /*GDETALLE DE GARAJE*/
    public function getPartePeatonal($id_parte)
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT * FROM partes WHERE id_parte =?");
        $stmt->bind_param("s", $id_parte);

        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        return $response;
    }
    /*DETALLE DE PEATONALs */

    public function solicitarHerramienta($id_tecnico, $id_herramienta, $cantidad, $id_obra, $id_obra_producto)
    {
        if (!$this->isSolicitudHerramientaExists($id_tecnico, $id_herramienta)) {
            $fecha_solicitud = date('Y-m-d');

            $stmt = $this->conn->prepare("INSERT INTO solicitudes_herramientas(id_tecnico, id_herramienta, cantidad, fecha_solicitud, id_obra, id_obra_producto) values(?,?,?,?,?,?)");
            $stmt->bind_param("ssssss", $id_tecnico, $id_herramienta, $cantidad, $fecha_solicitud, $id_obra, $id_obra_producto);
            $result = $stmt->execute();
            $stmt->close();
            if ($result) {
                return RECORD_CREATED_SUCCESSFULLY;
            } else
                return RECORD_CREATION_FAILED;
        } else
            return RECORD_ALREADY_EXISTED;
    }

    public function cancelarSolicitudHerramienta($id_solicitud)
    {
        return $this->deleteRecord("solicitudes_herramientas", "id_solicitud", $id_solicitud);
    }

    public function isSolicitudHerramientaExists($id_tecnico, $id_herramienta)
    {
        $stmt = $this->conn->prepare("SELECT * FROM solicitudes_herramientas WHERE id_tecnico = ? AND id_herramienta = ? AND estado IN('A', 'S')");
        $stmt->bind_param("ss", $id_tecnico, $id_herramienta);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }


    public function getAllSolicitudesHerramientasTecnico($id_tecnico)
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT s.*, h.descripcion
        FROM solicitudes_herramientas s
        INNER JOIN herramientas h ON h.id_herramienta = s.id_herramienta
        WHERE s.estado IN ('A', 'S') AND s.id_tecnico = ?");
        $stmt->bind_param("s", $id_tecnico);

        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        return $response;
    }


    /* SOLICITUD PARTES */
    public function solicitarParte($id_tecnico, $id_parte, $cantidad, $id_obra, $id_obra_producto)
    {

        if (!$this->isSolicitudParteExists($id_tecnico, $id_parte)) {

            $fecha_solicitud = date('Y-m-d');
            $stmt = $this->conn->prepare("INSERT INTO solicitudes_partes (id_tecnico, id_parte, cantidad, fecha_solicitud, id_obra, id_obra_producto) VALUES(?,?,?,?,?,?)");
            $stmt->bind_param("ssssss", $id_tecnico, $id_parte, $cantidad, $fecha_solicitud, $id_obra, $id_obra_producto);
            $result = $stmt->execute();

            $stmt->close();
            if ($result) {
                return RECORD_CREATED_SUCCESSFULLY;
            } else
                return RECORD_CREATION_FAILED;
        } else
            return RECORD_ALREADY_EXISTED;
    }

    public function isSolicitudParteExists($id_tecnico, $id_parte)
    {
        $stmt = $this->conn->prepare("SELECT * FROM solicitudes_partes WHERE id_tecnico = ? AND id_parte = ? AND estado IN('A', 'S')");
        $stmt->bind_param("ss", $id_tecnico, $id_parte);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    public function cancelarSolicitudParte($id_solicitud)
    {
        return $this->deleteRecord("solicitudes_partes", "id_solicitud", $id_solicitud);
    }

    public function getAllSolicitudesPartesTecnico($id_tecnico)
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT s.*, p.descripcion
        FROM solicitudes_partes s
        INNER JOIN partes p ON p.id_parte = s.id_parte
        WHERE s.estado IN ('A', 'S') AND s.id_tecnico = ?");
        $stmt->bind_param("s", $id_tecnico);

        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        return $response;
    }


    /**
     *TECNICOS
     */



    /**
     *OBRAS
     */
    public function getAllObras()
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT o.*, CONCAT(c.nombres, ' ',c.apellidos ) as cliente
        FROM obras o
        INNER JOIN clientes c on c.id_cliente = o.id_cliente 
        WHERE o.estado = 'A'");
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {

            $row["mediciones"] = empty($this->getAllMedicionesByObra($row["id_obra"])) ? false : true;
            $row["productos"] = count($this->getProductosObra($row["id_obra"]));
            $response[] = $row;
        }
        return $response;
    }

    public function getObraById($id_obra)
    {
        $response = array();

        $stmt = $this->conn->prepare("SELECT o.*
        FROM obras o 
        where o.id_obra = ?");
        $stmt->bind_param("s", $id_obra);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {

            // Obtener las modulaciones de esta obra
            $modulaciones = $this->getAllModulaciones($id_obra);


            foreach ($modulaciones as $key => $modulacion) {

                // Obtener las tareas de esta modulacion
                $tareas = $this->getAllTareas($modulacion["id_modulacion"]);
                $modulaciones[$key]["tareas"] = $tareas;
            }
            $row["modulaciones"] = $modulaciones;

            $row["cliente"] = $this->getClienteById($row["id_cliente"]);
            $row["mediciones"] = $this->getAllMediciones($row["id_obra"]);
            return $row;
        } else return RECORD_DOES_NOT_EXIST;
    }

    public function createObraData($id_cliente, $id_tecnico, $georeferencia, $ciudad, $sector, $manzana_etapa, $solar_villa, $medidas_referenciales, $fecha_vencimiento, $plazo, $nombre_factura, $cedula_ruc, $numero_factura, $fecha_emision, $fecha_anticipo, $direccion, $email, $telefono, $dataGaraje, $dataPeatonal, $dataOrnamental, $dataOcasional, $categorias)
    {

        // insertar los datos de la obra
        $id_obra = $this->createObra($id_cliente, $id_tecnico, $georeferencia, $ciudad, $sector, $manzana_etapa, $solar_villa, $medidas_referenciales, $fecha_vencimiento, $plazo);

        if ($id_obra !== RECORD_CREATION_FAILED) {
            $this->createFacturacion($nombre_factura, $cedula_ruc, $numero_factura, $fecha_emision, $fecha_anticipo, $direccion, $email, $telefono, $id_obra);
            $this->createProductoObra($id_obra, $dataGaraje,  $dataPeatonal, $dataOrnamental, $dataOcasional, $categorias);


            // foreach ($categorias as $cat) {

            //     // $id_modulacion = $this->createModulacion($id_obra, $modulacion["nombre"], $modulacion["nota"]);

            //     $id_producto_obra = $this->createProductoObra($id_obra, $producto["id_producto"], $producto["id_tecnico"], $producto["nota"]);

            //     if ($id_producto_obra == RECORD_CREATION_FAILED) {
            //         continue;
            //     }
            // }
            return RECORD_CREATED_SUCCESSFULLY;
        } else {
            return RECORD_CREATION_FAILED;
        }
    }
    /* public function createObraData($nombre, $id_cliente, $nota, $productos)
    {

        // insertar los datos de la obra
        $id_obra = $this->createObra($nombre, $id_cliente, $nota);

        if ($id_obra !== RECORD_CREATION_FAILED) {

            foreach ($productos as $producto) {

                // $id_modulacion = $this->createModulacion($id_obra, $modulacion["nombre"], $modulacion["nota"]);

                $id_producto_obra = $this->createProductoObra($id_obra, $producto["id_producto"], $producto["id_tecnico"], $producto["nota"]);

                if ($id_producto_obra == RECORD_CREATION_FAILED) {
                    continue;
                }
            }
            return RECORD_CREATED_SUCCESSFULLY;
        } else {
            return RECORD_CREATION_FAILED;
        }
    } */


    public function updateNumTareas($id_obra, $num_modulaciones)
    {
        $stmt = $this->conn->prepare("UPDATE obras SET num_tareas = ? WHERE id_obra = ?");
        $stmt->bind_param("ss", $num_modulaciones, $id_obra);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            return RECORD_UPDATED_SUCCESSFULLY;
        } else
            return RECORD_UPDATED_FAILED;
    }


    public function createObra($id_cliente, $id_tecnico, $georeferencia, $ciudad, $sector, $manzana_etapa, $solar_villa, $medidas_referenciales, $fecha_vencimiento, $plazo)
    {
        // $nombres, $apellidos, $cedula, $telefono, $correo

        $fechaActual = date("Y-m-d");

        $stmt = $this->conn->prepare("INSERT INTO obras(id_cliente, id_tecnico, georefencia, ciudad, sector, manzana_etapa, solar_villa, medidas_referenciales, fecha_vencimiento, fecha_obra, plazo) values(?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sssssssssss", $id_cliente, $id_tecnico, $georeferencia, $ciudad, $sector, $manzana_etapa, $solar_villa, $medidas_referenciales, $fecha_vencimiento, $fechaActual, $plazo);
        $result = $stmt->execute();
        $id_obra = $stmt->insert_id;

        $stmt->close();
        if ($result) {
            return $id_obra;
        } else
            return RECORD_CREATION_FAILED;
    }

    public function createFacturacion($nombre_factura, $cedula_ruc, $numero_factura, $fecha_emision, $fecha_anticipo, $direccion, $email, $telefono, $id_obra)
    {
        // $nombres, $apellidos, $cedula, $telefono, $correo

        $fechaActual = date("Y-m-d");

        $stmt = $this->conn->prepare("INSERT INTO facturacion_pago(nombre_factura, cedula_ruc, numero_factura, fecha_emision, fecha_anticipo, direccion, email, telefono, id_obra) values(?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sssssssss", $nombre_factura, $cedula_ruc, $numero_factura, $fecha_emision, $fecha_anticipo, $direccion, $email, $telefono, $id_obra);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            return RECORD_CREATED_SUCCESSFULLY;
        } else
            return RECORD_CREATION_FAILED;
    }

    public function createProductoObra($id_obra, $dataGaraje,  $dataPeatonal, $dataOrnamental, $dataOcasional, $categorias)
    {
        for ($i = 0; $i < count($categorias); $i++) {
            if ($categorias[$i] == "1") {
            }
        }
        $stmt = $this->conn->prepare("INSERT INTO obras_productos(id_obra, id_producto, id_tecnico, nota) values(?,?,?,?)");
        $stmt->bind_param("ssss", $id_obra, $id_producto, $id_tecnico, $nota);
        $result = $stmt->execute();
        $id_obra_producto = $stmt->insert_id;

        $stmt->close();
        if ($result) {
            return $id_obra_producto;
        } else
            return RECORD_CREATION_FAILED;
    }


    public function createDetalleGaraje($id_obra, $dataGaraje,  $dataPeatonal, $dataOrnamental, $dataOcasional, $categorias)
    {
        $stmt = $this->conn->prepare("INSERT INTO obras_productos(id_obra, id_producto, id_tecnico, nota) values(?,?,?,?)");
        $stmt->bind_param("ssss", $id_obra, $id_producto, $id_tecnico, $nota);
        $result = $stmt->execute();
        // $id_obra = $stmt->insert_id;

        $stmt->close();
        if ($result) {
            return RECORD_UPDATED_SUCCESSFULLY;
        } else
            return RECORD_CREATION_FAILED;
    }

    public function createDetallePeatonal($id_obra, $dataGaraje,  $dataPeatonal, $dataOrnamental, $dataOcasional, $categorias)
    {
        $stmt = $this->conn->prepare("INSERT INTO obras_productos(id_obra, id_producto, id_tecnico, nota) values(?,?,?,?)");
        $stmt->bind_param("ssss", $id_obra, $id_producto, $id_tecnico, $nota);
        $result = $stmt->execute();
        // $id_obra = $stmt->insert_id;

        $stmt->close();
        if ($result) {
            return RECORD_UPDATED_SUCCESSFULLY;
        } else
            return RECORD_CREATION_FAILED;
    }



    /* public function getProductosObra($id_obra)
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT op.*, p.nombre, CONCAT(t.nombres, ' ',t.apellidos ) as tecnico
        FROM obras_productos op
        INNER JOIN productos p ON p.id_producto = op.id_producto
        INNER JOIN tecnicos t on t.id_tecnico = op.id_tecnico 
        WHERE op.estado = 'A' AND op.id_obra = ?");
        $stmt->bind_param("s", $id_obra);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        return $response;
    } */
    // public function getProductosObra($id_obra)
    // {
    //     $response = array();
    //     $stmt = $this->conn->prepare("SELECT op.*, p.nombre, p.id_categoria
    //     FROM obras_productos op
    //     INNER JOIN productos p ON p.id_producto = op.id_producto
    //     WHERE op.estado = 'A' AND op.id_obra = ?");
    //     $stmt->bind_param("s", $id_obra);
    //     $stmt->execute();
    //     $result = $stmt->get_result();

    //     while ($row = $result->fetch_assoc()) {
    //         $row["mediciones"] = $this->getAllMedicionesByObra($row["id_obras_productos"]);
    //         $row["tareas"] = $this->getAllTareasTecnico($row["id_obras_productos"]);
    //         $response[] = $row;
    //     }
    //     return $response;
    // }

    /**
     *OBRAS
     */


    /**
     *MODULACIONES
     */

    public function getAllModulaciones($id_obra)
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT * from modulaciones WHERE estado = 'A' AND id_obra =?");
        $stmt->bind_param("s", $id_obra);

        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $row["tareas"] = $this->getAllTareas($row["id_modulacion"]);
            $response[] = $row;
        }
        return $response;
    }

    public function createModulacion($id_obra, $nombre, $nota)
    {

        $fecha_inicio = date('Y-m-d');
        $fecha_estimada = date('Y-m-d');

        $stmt = $this->conn->prepare("INSERT INTO modulaciones(id_obra, nombre, nota, fecha_inicio, fecha_estimada) values(?,?,?,?,?)");
        $stmt->bind_param("sssss", $id_obra, $nombre, $nota, $fecha_inicio, $fecha_estimada);
        $result = $stmt->execute();
        $id_modulacion = $stmt->insert_id;

        $stmt->close();
        if ($result) {
            return $id_modulacion;
        } else
            return RECORD_CREATION_FAILED;
    }


    public function createModulacionesData($id_obra, $num_modulaciones, $modulaciones)
    {
        $this->updateNumTareas($id_obra, $num_modulaciones);

        foreach ($modulaciones as $modulacion) {

            $id_modulacion = $this->createModulacion($id_obra, $modulacion["nombre"], $modulacion["nota"]);

            if ($id_modulacion !== RECORD_CREATION_FAILED) {

                foreach ($modulacion["tareas"] as $tarea) {

                    $id_tarea = $this->createTarea($id_modulacion, $tarea["id_tecnico"], $tarea["nombre"], $tarea["nota"]);
                }
            } else {
                continue;
            }
        }
        return RECORD_CREATED_SUCCESSFULLY;
    }

    /**
     *MODULACIONES
     */


    /**
     *TAREAS
     */

    public function getAllTareas($id_modulacion)
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT t.* , CONCAT(tc.nombres, ' ',tc.apellidos ) as tecnico 
        FROM tareas t 
        INNER JOIN tecnicos tc on t.id_tecnico = tc.id_tecnico 
        WHERE t.estado = 'A' AND id_modulacion = ?");
        $stmt->bind_param("s", $id_modulacion);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        return $response;
    }


    public function createTarea($id_modulacion, $id_tecnico, $nombre, $nota)
    {

        $fecha_inicio = date('Y-m-d');
        $fecha_estimada = date('Y-m-d');

        $stmt = $this->conn->prepare("INSERT INTO tareas(id_modulacion, id_tecnico, nombre, nota, fecha_inicio, fecha_estimada) values(?,?,?,?,?,?)");
        $stmt->bind_param("ssssss", $id_modulacion, $id_tecnico, $nombre, $nota, $fecha_inicio, $fecha_estimada);
        $result = $stmt->execute();
        $id_tarea = $stmt->insert_id;

        $stmt->close();
        if ($result) {
            return $id_tarea;
        } else
            return RECORD_CREATION_FAILED;
    }

    public function createTareasData($tareas)
    {

        foreach ($tareas as $tarea) {

            $id_tarea = $this->createTarea($tarea["id_modulacion"], $tarea["id_tecnico"], $tarea["nombreTarea"], $tarea["notaTarea"]);
        }
        return RECORD_CREATED_SUCCESSFULLY;
    }

    public function editTareasData($tareas)
    {

        foreach ($tareas as $tarea) {
            $id_tarea = $this->editTarea($tarea["id_tecnico"], $tarea["nombreTarea"], $tarea["notaTarea"], $tarea["id_tarea"]);
        }
        return RECORD_UPDATED_SUCCESSFULLY;
    }



    public function editTarea($id_tecnico, $nombre, $nota, $id_tarea)
    {
        $stmt = $this->conn->prepare("UPDATE tareas SET id_tecnico = ?, nombre = ?, nota = ? WHERE id_tarea = ?");
        $stmt->bind_param("ssss", $id_tecnico, $nombre, $nota, $id_tarea);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            return RECORD_UPDATED_SUCCESSFULLY;
        } else
            return RECORD_UPDATED_FAILED;
    }

    public function getAllTareasTecnico($id_obra_producto)
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT t.*
        FROM tareas t
        INNER JOIN modulaciones m ON m.id_modulacion = t.id_modulacion
        WHERE (t.estado = 'A' or t.estado = 'C') AND m.id_obras_productos = ?");
        $stmt->bind_param("s", $id_obra_producto);
        $stmt->execute();
        $result = $stmt->get_result();

        $todas_asignadas = true;

        while ($row = $result->fetch_assoc()) {
            if ($row['asignacion'] != '1') {
                $todas_asignadas = false;
                break; // Salir del bucle si alguna tarea no tiene asignacion = 1
            }
            $response[] = $row;
        }

        // Si todas las tareas tienen asignacion = 1, retorna el arreglo, caso contrario retorna un arreglo vacío
        return $todas_asignadas ? $response : array();
    }



    public function getTareaById($id_tarea)
    {
        $stmt = $this->conn->prepare("SELECT id_tecnico, nombres, apellidos, telefono, usuario, correo, estado, fecha_creacion, fecha_actualizacion from tecnicos where id_tecnico = ?");
        $stmt->bind_param("s", $id_tarea);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row;
        } else return RECORD_DOES_NOT_EXIST;
    }

    public function getTarea2ById($id_tarea)
    {
        $stmt = $this->conn->prepare("SELECT * from tareas where id_tarea = ?");
        $stmt->bind_param("s", $id_tarea);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if ($row["tipo"] == "F" && $row["estado"] == "C") {
                $row["archivo"] = $this->getArchivoById($id_tarea);
            }
            return $row;
        } else return RECORD_DOES_NOT_EXIST;
    }

    public function getArchivoById($id_tarea)
    {
        $stmt = $this->conn->prepare("SELECT * from archivo_tareas where id_tarea = ?");
        $stmt->bind_param("s", $id_tarea);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row;
        } else return RECORD_DOES_NOT_EXIST;
    }


    public function getDetalleTareaById($id_tarea)
    {
        $stmt = $this->conn->prepare("SELECT
            t.id_tarea,
            t.nombre AS nombre_tarea,
            t.nota AS nota_tarea,
            t.id_tecnico  AS id_tecnico_tarea,
            m.nombre AS nombre_modulacion,
            o.id_obra,
            o.nombre AS nombre_obra,
            o.nota AS nota_obra,
            o.fecha_creacion  AS creacion_obra,
            CONCAT(c.nombres, ' ',c.apellidos ) as cliente 
        FROM tareas t
        JOIN modulaciones m ON t.id_modulacion = m.id_modulacion
        JOIN obras o ON m.id_obra = o.id_obra
        JOIN clientes c ON o.id_cliente = c.id_cliente
        WHERE t.id_tarea = ?");

        $stmt->bind_param("s", $id_tarea);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row;
        } else return RECORD_DOES_NOT_EXIST;
    }



    /**
     *TAREAS
     */



    /**
     *MEDICIONES
     */

    public function getAllMedicionesByObra($id_obra_producto)
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT m.*
        FROM mediciones m 
        INNER JOIN obras_productos op ON op.id_obras_productos = m.id_obra_producto
        INNER JOIN obras o ON o.id_obra = op.id_obra
        WHERE m.estado = 'A' AND m.id_obra_producto = ?");
        $stmt->bind_param("s", $id_obra_producto);

        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        return $response;
    }


    public function createMedicionesData($data)
    {
        if ($data["id_obras_productos"]) {
            foreach ($data["mediciones"] as $medicion) {
                $medicion = $this->createMedicion($data["id_obras_productos"], $medicion["nombre"], $medicion["valor"]);
            }
            return RECORD_CREATED_SUCCESSFULLY;
        } else {
            return RECORD_CREATION_FAILED;
        }
    }


    public function createMedicion($id_obras_productos, $nombre, $valor)
    {
        $stmt = $this->conn->prepare("INSERT INTO mediciones(id_obras_productos, nombre, valor) values(?,?,?)");
        $stmt->bind_param("sss", $id_obras_productos, $nombre, $valor);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            return RECORD_CREATED_SUCCESSFULLY;
        } else
            return RECORD_CREATION_FAILED;
    }
    /**
     *MEDICIONES
     */



    /**
     *HERRAMIENTAS
     */


    public function createHerramienta($foto, $descripcion, $cantidad, $ubicacion)
    {
        $descripcion = utf8_decode($descripcion);
        $ubicacion = utf8_decode($ubicacion);

        if (!$this->isHerramientaExists($descripcion)) {
            $stmt = $this->conn->prepare("INSERT INTO herramientas(foto, descripcion, cantidad, ubicacion) values(?,?,?,?)");
            $stmt->bind_param("ssss", $foto, $descripcion, $cantidad, $ubicacion);
            $result = $stmt->execute();
            $stmt->close();
            if ($result) {
                return RECORD_CREATED_SUCCESSFULLY;
            } else {
                return RECORD_CREATION_FAILED;
            }
        } else {
            return RECORD_ALREADY_EXISTED;
        }
    }

    public function isHerramientaExists($descripcion)
    {
        $stmt = $this->conn->prepare("SELECT descripcion FROM herramientas WHERE descripcion = ? AND estado = 'A'");
        $stmt->bind_param("s", $descripcion);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    public function getAllHerramientas()
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT * FROM herramientas WHERE estado = 'A'");
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        return $response;
    }

    public function getHerramientaById($id_herramienta)
    {
        $stmt = $this->conn->prepare("SELECT * FROM herramientas WHERE id_herramienta = ?");
        $stmt->bind_param("s", $id_herramienta);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row;
        } else return RECORD_DOES_NOT_EXIST;
    }

    public function editHerramienta($foto, $descripcion, $cantidad, $ubicacion, $id_herramienta)
    {

        if ($this->isHerramientaIdExists($id_herramienta)) {

            $stmt = $this->conn->prepare("UPDATE herramientas SET foto = ?, descripcion = ?, cantidad = ?, ubicacion = ? WHERE id_herramienta = ?");
            $stmt->bind_param("sssss", $foto, $descripcion, $cantidad, $ubicacion, $id_herramienta);
            $result = $stmt->execute();
            $stmt->close();

            if ($result) {
                return RECORD_UPDATED_SUCCESSFULLY;
            } else
                return RECORD_UPDATED_FAILED;
        } else
            return RECORD_DOES_NOT_EXIST;
    }

    public function isHerramientaIdExists($id_herramienta)
    {
        $stmt = $this->conn->prepare("SELECT id_herramienta from herramientas WHERE id_herramienta = ? AND estado = 'A'");
        $stmt->bind_param("s", $id_herramienta);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    public function deleteHerramienta($id_herramienta)
    {
        return $this->deleteRecord("herramientas", "id_herramienta", $id_herramienta);
    }


    public function createSolicitudHerramiente($id_tecnico, $id_herramienta, $cantidad)
    {

        if (!$this->isTecnicoExists($id_tecnico, true)) {

            $fecha_solicitud = date('Y-m-d');

            $stmt = $this->conn->prepare("INSERT INTO solicitudes_herramientas(id_tecnico, id_herramienta, cantidad, fecha_solicitud) values(?,?,?,?)");
            $stmt->bind_param("ssss", $id_tecnico, $id_herramienta, $cantidad, $fecha_solicitud);
            $result = $stmt->execute();
            $stmt->close();
            if ($result) {
                return RECORD_CREATED_SUCCESSFULLY;
            } else {
                return RECORD_CREATION_FAILED;
            }
        } else
            return RECORD_ALREADY_EXISTED;
    }

    /**
     *HERRAMIENTAS
     */



    /**
     *PARTES
     */


    public function createParte($foto, $descripcion, $minimo, $ubicacion, $devolver, $categoria, $cantidad)
    {
        if (!$this->isParteExists($descripcion)) {
            $stmt = $this->conn->prepare("INSERT INTO partes(foto, descripcion, minimo, ubicacion, devolver, id_categoria, cantidad) values(?,?,?,?,?,?,?)");
            $stmt->bind_param("sssssss", $foto, $descripcion, $minimo, $ubicacion, $devolver, $categoria, $cantidad);
            $result = $stmt->execute();
            $stmt->close();
            if ($result) {
                return RECORD_CREATED_SUCCESSFULLY;
            } else {
                return RECORD_CREATION_FAILED;
            }
        } else {
            return RECORD_ALREADY_EXISTED;
        }
    }


    public function isParteExists($descripcion)
    {
        $stmt = $this->conn->prepare("SELECT descripcion FROM partes WHERE descripcion = ? AND estado = 'A'");
        $stmt->bind_param("s", $descripcion);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }


    public function getAllPartes()
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT pt.*, cpt.nombre as nombre_categoria FROM partes pt INNER JOIN categoria_parte cpt ON cpt.id_categoria = pt.id_categoria WHERE pt.estado = 'A'");
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        return $response;
    }

    public function getAllTipoMotor()
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT mt.*, tm.nombre as nombre_tipo FROM motores mt INNER JOIN tipo_motor tm ON tm.id = mt.id_tipo_motor WHERE mt.estado = 'A' and tm.estado = 'A'");
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        return $response;
    }

    public function getMotorA($id)
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT * FROM motores WHERE id_tipo_motor = '$id' and estado = 'A'");
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        return $response;
    }


    public function getAllFijo()
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT * FROM fijo_superior WHERE estado = 'A'");
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        return $response;
    }


    public function getAllMecanismo()
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT * FROM mecanismos WHERE estado = 'A'");
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        return $response;
    }


    public function getParteById($id_parte)
    {
        $stmt = $this->conn->prepare("SELECT * FROM partes WHERE id_parte = ?");
        $stmt->bind_param("s", $id_parte);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row;
        } else return RECORD_DOES_NOT_EXIST;
    }

    public function editParte($foto, $descripcion, $minimo, $ubicacion, $devolver, $categoria, $cantidad, $id_parte)
    {

        if ($this->isParteIdExists($id_parte)) {

            $stmt = $this->conn->prepare("UPDATE partes SET foto = ?, descripcion = ?, minimo = ?, ubicacion = ?, devolver = ?, id_categoria = ?, cantidad = ? WHERE id_parte = ?");
            $stmt->bind_param("ssssssss", $foto, $descripcion, $minimo, $ubicacion, $devolver, $categoria, $cantidad, $id_parte);
            $result = $stmt->execute();
            $stmt->close();

            if ($result) {
                return RECORD_UPDATED_SUCCESSFULLY;
            } else
                return RECORD_UPDATED_FAILED;
        } else
            return RECORD_DOES_NOT_EXIST;
    }
    public function editParteSinImagen($descripcion, $minimo, $ubicacion, $devolver, $categoria, $cantidad, $id_parte)
    {
        if ($this->isParteIdExists($id_parte)) {

            $stmt = $this->conn->prepare("UPDATE partes SET descripcion = ?, minimo = ?, ubicacion = ?, devolver = ?, id_categoria = ?, cantidad = ? WHERE id_parte = ?");
            $stmt->bind_param("sssssss", $descripcion, $minimo, $ubicacion, $devolver, $categoria, $cantidad, $id_parte);
            $result = $stmt->execute();
            $stmt->close();

            if ($result) {
                return RECORD_UPDATED_SUCCESSFULLY;
            } else
                return RECORD_UPDATED_FAILED;
        } else
            return RECORD_DOES_NOT_EXIST;
    }

    public function isParteIdExists($id_parte)
    {
        $stmt = $this->conn->prepare("SELECT * from partes WHERE id_parte = ? AND estado = 'A'");
        $stmt->bind_param("s", $id_parte);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    public function deleteParte($id_parte)
    {
        return $this->deleteRecord("partes", "id_parte", $id_parte);
    }

    /**
     *PARTES
     */



    /**
     *PROVEEDORES
     */
    public function getAllProveedores()
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT * from proveedores WHERE estado = 'A'");
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        return $response;
    }


    public function getProveedorById($id_proveedor)
    {
        $stmt = $this->conn->prepare("SELECT * from proveedores where id_proveedor = ?");
        $stmt->bind_param("s", $id_proveedor);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row;
        } else return RECORD_DOES_NOT_EXIST;
    }


    /* 
        LOCALES Y BODEGAS
    */

    public function getAllLocales()
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT * from locales WHERE estado = 'A'");
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        return $response;
    }

    public function createLocal($nombre)
    {

        if (!$this->isLocalExists($nombre)) {
            $stmt = $this->conn->prepare("INSERT INTO locales(nombre) values(?)");
            $stmt->bind_param("s", $nombre);
            $result = $stmt->execute();
            $stmt->close();
            if ($result) {
                return RECORD_CREATED_SUCCESSFULLY;
            } else {
                return RECORD_CREATION_FAILED;
            }
        } else {
            return RECORD_ALREADY_EXISTED;
        }
    }


    public function getAllBodegas()
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT bg.*, lc.nombre as nombre_local from bodegas bg INNER JOIN locales lc ON lc.id_local = bg.id_local WHERE bg.estado = 'A' and lc.estado = 'A'");
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        return $response;
    }
    public function createBodega($nombre, $local)
    {

        if (!$this->isBodegaExists($nombre)) {
            $stmt = $this->conn->prepare("INSERT INTO bodegas(nombre, id_local) values(?,?)");
            $stmt->bind_param("ss", $nombre, $local);
            $result = $stmt->execute();
            $stmt->close();
            if ($result) {
                return RECORD_CREATED_SUCCESSFULLY;
            } else {
                return RECORD_CREATION_FAILED;
            }
        } else {
            return RECORD_ALREADY_EXISTED;
        }
    }


    public function isLocalExists($nombre)
    {
        $stmt = $this->conn->prepare("SELECT * FROM locales WHERE nombre = ? AND estado = 'A'");
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    public function isBodegaExists($nombre)
    {
        $stmt = $this->conn->prepare("SELECT * FROM bodegas WHERE nombre = ? AND estado = 'A'");
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }


    public function createProveedor($nombre)
    {

        if (!$this->isProveedorExists($nombre)) {
            $stmt = $this->conn->prepare("INSERT INTO proveedores(nombre) values(?)");
            $stmt->bind_param("s", $nombre);
            $result = $stmt->execute();
            $stmt->close();
            if ($result) {
                return RECORD_CREATED_SUCCESSFULLY;
            } else {
                return RECORD_CREATION_FAILED;
            }
        } else {
            return RECORD_ALREADY_EXISTED;
        }
    }

    public function isProveedorExists($nombre)
    {
        $stmt = $this->conn->prepare("SELECT * FROM proveedores WHERE nombre = ? AND estado = 'A'");
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    public function deleteProveedor($id_proveedor)
    {
        return $this->deleteRecord("proveedores", "id_proveedor", $id_proveedor);
    }

    public function deleteBodega($id_bodega)
    {
        return $this->deleteRecord("bodegas", "id_bodega", $id_bodega);
    }

    public function editProveedor($id_proveedor, $nombre)
    {

        if ($this->isProveedorIdExists($id_proveedor)) {

            $stmt = $this->conn->prepare("UPDATE proveedores SET nombre = ? WHERE id_proveedor = ?");
            $stmt->bind_param("ss", $nombre, $id_proveedor);
            $result = $stmt->execute();
            $stmt->close();

            if ($result) {
                return RECORD_UPDATED_SUCCESSFULLY;
            } else
                return RECORD_UPDATED_FAILED;
        } else
            return RECORD_DOES_NOT_EXIST;
    }

    public function isProveedorIdExists($id_proveedor)
    {
        $stmt = $this->conn->prepare("SELECT * from proveedores WHERE id_proveedor = ? AND estado = 'A'");
        $stmt->bind_param("s", $id_proveedor);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }


    /**
     *PROVEEDORES
     */



    /**
     *CATEGORIAS
     */

    public function getAllCategorias()
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT * FROM categorias WHERE estado = 'A'");
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        return $response;
    }

    public function getAllCategoriasPartes()
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT * FROM categoria_parte WHERE estado = 'A'");
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        return $response;
    }


    public function createCategoria($nombre)
    {

        if (!$this->isCategoriaExists($nombre)) {

            $stmt = $this->conn->prepare("INSERT INTO categorias(nombre) values(?)");
            $stmt->bind_param("s", $nombre);
            $result = $stmt->execute();
            $stmt->close();
            if ($result) {
                return RECORD_CREATED_SUCCESSFULLY;
            } else {
                return RECORD_CREATION_FAILED;
            }
        } else {
            return RECORD_ALREADY_EXISTED;
        }
    }

    public function createCategoriaParte($nombre)
    {

        if (!$this->isCategoriaParteExists($nombre)) {

            $stmt = $this->conn->prepare("INSERT INTO categoria_parte(nombre) values(?)");
            $stmt->bind_param("s", $nombre);
            $result = $stmt->execute();
            $stmt->close();
            if ($result) {
                return RECORD_CREATED_SUCCESSFULLY;
            } else {
                return RECORD_CREATION_FAILED;
            }
        } else {
            return RECORD_ALREADY_EXISTED;
        }
    }

    public function isCategoriaExists($nombre_id, $usar_id = false)
    {
        if ($usar_id) {
            $stmt = $this->conn->prepare("SELECT * FROM categorias WHERE id_categoria = ? AND estado = 'A'");
            $stmt->bind_param("s", $nombre_id);
        } else {
            $stmt = $this->conn->prepare("SELECT * FROM categorias WHERE nombre = ? AND estado = 'A'");
            $stmt->bind_param("s", $nombre_id);
        }

        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    public function isCategoriaParteExists($nombre_id, $usar_id = false)
    {
        if ($usar_id) {
            $stmt = $this->conn->prepare("SELECT * FROM categoria_parte WHERE id_categoria = ? AND estado = 'A'");
            $stmt->bind_param("s", $nombre_id);
        } else {
            $stmt = $this->conn->prepare("SELECT * FROM categoria_parte WHERE nombre = ? AND estado = 'A'");
            $stmt->bind_param("s", $nombre_id);
        }

        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }


    public function deleteCategoria($id_categoria)
    {
        $stmt = $this->conn->prepare("UPDATE categorias c
        INNER JOIN productos_categorias pc ON c.id_categoria = pc.id_categoria
        SET c.estado = 'E',
        pc.estado = 'E'
        WHERE c.id_categoria = ?;");
        $stmt->bind_param("s", $id_categoria);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            return RECORD_UPDATED_SUCCESSFULLY;
        } else
            return RECORD_UPDATED_FAILED;
    }

    public function editCategoria($id_categoria, $nombre)
    {
        if ($this->isCategoriaExists($id_categoria, true)) {
            $stmt = $this->conn->prepare("UPDATE categorias SET nombre = ? WHERE id_categoria = ?");
            $stmt->bind_param("ss", $nombre, $id_categoria);
            $result = $stmt->execute();
            $stmt->close();
            if ($result) {
                return RECORD_UPDATED_SUCCESSFULLY;
            } else
                return RECORD_UPDATED_FAILED;
        } else
            return RECORD_DOES_NOT_EXIST;
    }


    public function getAllProductosCategoria($id_categoria)
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT p.*
        FROM productos_categorias pc 
        INNER JOIN productos p ON p.id_producto = pc.id_producto 
        WHERE pc.id_categoria = ? AND pc.estado = 'A'");
        $stmt->bind_param("s", $id_categoria);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        return $response;
    }

    /**
     *CATEGORIAS
     */



    /**
     *PRODUCTOS
     */
    public function getAllProductos()
    {
        $response = array();
        $stmt = $this->conn->prepare("SELECT p.*, ct.nombre as nombre_categoria FROM productos p INNER JOIN categorias ct ON ct.id_categoria = p.id_categoria WHERE p.estado = 'A' and ct.estado = 'A'");
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            //$row["categorias"] = $this->gatProductoCategorias($row["id_producto"], true);
            $response[] = $row;
        }
        return $response;
    }


    public function createProducto($nombre, $categorias)
    {
        if (!$this->isProductoExists($nombre, $categorias)) {

            foreach ($categorias as $categoria) {
                $stmt = $this->conn->prepare("INSERT INTO productos(nombre, id_categoria) values(?,?)");
                $stmt->bind_param("ss", $nombre, $categoria);
                $result = $stmt->execute();
                //$id_producto = $stmt->insert_id;
                $stmt->close();
                if ($result) {
                    return RECORD_CREATED_SUCCESSFULLY;
                } else {
                    return RECORD_CREATION_FAILED;
                }
            }
        } else {
            return RECORD_ALREADY_EXISTED;
        }
    }

    public function createProductoCategoria($id_producto, $id_categoria)
    {
        $stmt = $this->conn->prepare("INSERT INTO productos_categorias(id_producto, id_categoria) values(?,?)");
        $stmt->bind_param("ss", $id_producto, $id_categoria);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            return RECORD_CREATED_SUCCESSFULLY;
        } else
            return RECORD_CREATION_FAILED;
    }


    public function isProductoExists($nombre_id, $categorias, $usar_id = false)
    {
        if ($usar_id) {
            $stmt = $this->conn->prepare("SELECT * from productos WHERE id_producto = ? AND estado = 'A'");
            $stmt->bind_param("s", $nombre_id);
        } else {
            $stmt = $this->conn->prepare("SELECT * from productos WHERE nombre = ? AND id_categoria = ? AND estado = 'A'");
            $stmt->bind_param("ss", $nombre_id, $categorias);
        }

        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    public function isProductoCategoriaExists($id_producto, $id_categoria)
    {

        $stmt = $this->conn->prepare("SELECT * from productos_categorias WHERE id_producto = ? AND id_categoria = ? AND estado = 'A'");
        $stmt->bind_param("ss", $id_producto, $id_categoria);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    public function editProducto($id_producto, $nombre, $categorias)
    {
        if ($this->isProductoExists($id_producto, $categorias, true)) {

            foreach ($categorias as $categoria) {
                $stmt = $this->conn->prepare("UPDATE productos SET nombre = ?, id_categoria = ? WHERE id_producto = ?");
                $stmt->bind_param("sss", $nombre, $categoria, $id_producto);
                $result = $stmt->execute();
                $stmt->close();
                if ($result) {
                    // actualmente el producto tiene tiene categorias (1,2,4) en la tabla productos_categorias

                    // Eliminar las categorías no presentes en el nuevo array
                    // $eliminar = implode(",", $categorias);
                    // // $eliminar queda asi "3,4,5"

                    // $stmt = $this->conn->prepare("UPDATE productos_categorias SET estado = 'E' WHERE id_producto = ? AND id_categoria NOT IN ($eliminar)");
                    // $stmt->bind_param("s", $id_producto);
                    // $stmt->execute();
                    // $stmt->close();

                    // // despues de ejequtar la consulta el producto deberia quedar solo con categoria  (4) en la tabla productos_categorias, un error en la consulta hace que la (4) tambien se marque como estado 'E'

                    // // insertar las nuevas categorias para el producto (3,5) en productos_categorias
                    // foreach ($categorias as $categoria) {
                    //     if (!$this->isProductoCategoriaExists($id_producto, $categoria)) {
                    //         $this->createProductoCategoria($id_producto, $categoria);
                    //     }
                    // }
                    return RECORD_UPDATED_SUCCESSFULLY;
                } else
                    return RECORD_UPDATED_FAILED;
            }
        } else
            return RECORD_DOES_NOT_EXIST;
    }

    public function gatProductoCategorias($id_producto, $info = false)
    {

        $response = array();

        if ($info) {

            $stmt = $this->conn->prepare("SELECT pc.id_producto, pc.id_categoria, c.nombre FROM productos_categorias pc
            INNER JOIN categorias c ON c.id_categoria = pc.id_categoria
            WHERE pc.estado = 'A' AND pc.id_producto = ?");
            $stmt->bind_param("s", $id_producto);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $response[] = $row;
            }
        } else {

            $stmt = $this->conn->prepare("SELECT pc.id_categoria FROM productos_categorias pc
            INNER JOIN categorias c ON c.id_categoria = pc.id_categoria
            WHERE pc.estado = 'A' AND pc.id_producto = ?");
            $stmt->bind_param("s", $id_producto);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $response[] = $row["id_categoria"];
            }
        }
        return $response;
    }
    public function deleteProducto($id_producto)
    {
        $stmt = $this->conn->prepare("UPDATE productos p 
        SET p.estado = 'E'
        WHERE p.id_producto = ?;");
        $stmt->bind_param("s", $id_producto);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            return RECORD_UPDATED_SUCCESSFULLY;
        } else
            return RECORD_UPDATED_FAILED;
    }


    public function getProductoById($id_producto)
    {
        $stmt = $this->conn->prepare("SELECT * from productos where id_producto = ?");
        $stmt->bind_param("s", $id_producto);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            //$row["categorias"] = $this->gatProductoCategorias($id_producto);
            return $row;
        } else return RECORD_DOES_NOT_EXIST;
    }




    /**
     *PRODUCTOS
     */
}
