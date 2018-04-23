<?php
    //Agregamos la región y lugar para obtener correctamente las fechas
    date_default_timezone_set('America/Mexico_City');
    //Incluimos a la clase SRV_CONEXION(); para poder instanciarla
    include("SRV_CONEXION.php");
    //Instanciamos a la clase SRV_CONEXION();
    $db = new SRV_CONEXION();
    //Recuperamos la conexión.
    $conexion = $db->getConnection();
    /*
     * Comprobamos si la sesión con la que se está queriendo realizar una accion es la correcta
     * en este caso solo el administrador, de lo contrario se expulsa sin poder realizar 
     * ninguna de las demás acciones.
     */
    session_start();
    if (isset($_SESSION['ID_USUARIO']) && isset($_SESSION["TIPO_USUARIO"])) {
        if ($_SESSION["TIPO_USUARIO"] != "ADMINISTRADOR") {
            echo "error";
            $conexion->close();
            return;
        }
    } else {
        echo "error";
        $conexion->close();
        return;
    }
    //Declaramos un switch con todos los casos y los eventos con las cuenta una convocatoria
    switch ($_POST['tipo']) {
        //Se realiza un consulta a la base de datos y se regresa la información de una convocatoria en especifico, dependiendo de cual se haya seleccionado
        case "consulta_especifica":
            $consulta = $conexion->prepare("SELECT NOMBRE_TORNEO,FECHA_CIERRE_CONVOCATORIA FROM convocatoria WHERE ID_CONVOCATORIA = ?");
            $consulta->bind_param("i", $_POST['id']);
            if ($consulta->execute()) {
                $res = $consulta->get_result();
                $info = $res->fetch_assoc();
                echo "<h3 style='background-color:#333;color:#ffffff;width:93%;border-radius:3px;' id='nombre_convocatoria'>" . $info["NOMBRE_TORNEO"] . "</h3>" .
                "<h3 style='background-color:#333;color:#ffffff;width:93%;border-radius:3px;' id='fecha_cierre'>" . $info["FECHA_CIERRE_CONVOCATORIA"] . "</h3>";
            } else {
                echo "error";
            }
            break;
        //Realiza una consulta a la BD y regresa los equipos que se encuentran inscritos a una convocatoria en especifico la cual se haya seleccionado
        case "recuperar_equipos_inscritos":
            //Realizamos una consulta preparada por seguridad, ya que necesitamos pasar parametros
            $consulta = $conexion->prepare("SELECT NOMBRE_EQUIPO,CUOTA,ID_ROSTER FROM rosters r INNER JOIN equipos e WHERE r.ID_CONVOCATORIA = ? AND r.ID_EQUIPO = e.ID_EQUIPO");
            $consulta->bind_param("i", $_POST['id']);
            if ($consulta->execute()) {
                $res = $consulta->get_result();
                while ($fila = $res->fetch_assoc()) {
                    if ($fila["CUOTA"] == "NO PAGADO") {
                        echo "<tr id='" . $fila["ID_ROSTER"] . "'><td>" . $fila["NOMBRE_EQUIPO"] . "</td><td id='eventos" . $fila["ID_ROSTER"] . "'>" .
                        "<a class='news' href='#body' id='pago' onclick='abrir_pantalla_para_poner_pago(" . $fila["ID_ROSTER"] . ")'><h5>¿Ya pagó?</h5></a>" .
                        "<a class='news' href='#body' id='expulsar' onclick='abrir_pantalla_para_expulsar(" . $fila["ID_ROSTER"] . ")'><h5>Expulsar</h5></a></td></tr>";
                    } else {
                        echo "<tr id='" . $fila["ID_ROSTER"] . "'><td>" . $fila["NOMBRE_EQUIPO"] . "</td><td>" .
                        "<a class='news' href='#body'><h5>PAGADO</h5></a>" . "</td></tr>";
                    }
                }
            }
            break;
        //Se modifica el estatus de un roster en la BD a "PAGADO" para indicar que tal equipo a pagado su inscripcion
        case "poner_pagado":
            $cuota = "PAGADO";
            //Relizamos una consulta preparada para mayor seguridad ya que se requiere enviar datos
            $consulta = $conexion->prepare("UPDATE rosters SET CUOTA = ? WHERE ID_ROSTER = ?");
            $consulta->bind_param("si", $cuota, $_POST['id']);
            if ($consulta->execute()) {
                //Si las consultas y todo lo demas se ejecuta correctamente re regresa un "ok" para indicar que no hubo ningun problema.
                echo "ok";
            } else {
                echo "Error del servidor, intente más tarde";
            }
            break;
        //Se pasa a null la relacion entre la convocatoria lanzada y el roster inscrito, para asi expulsar a los que aun no han pagado su inscripcion
        case "expulsar":
            $consulta = $conexion->prepare("UPDATE rosters SET ID_CONVOCATORIA = NULL WHERE ID_ROSTER = ?");
            $consulta->bind_param("i", $_POST['id']);
            if ($consulta->execute()) {
                //Si las consultas y todo lo demas se ejecuta correctamente re regresa un "ok" para indicar que no hubo ningun problema.
                echo "ok";
            } else {
                echo "Error del servidor, intente más tarde";
            }
            break;
        //Realiza una consulta a la BD y regresa todas la convocatorias lanzadas para cargarlas en una tabla en el inicio
        case "consultar":
            $fecha_actual = date('Y-m-d');           
            $sql = "SELECT * FROM convocatoria WHERE ID_CONVOCATORIA NOT IN (SELECT ID_CONVOCATORIA FROM roles_juego)";
            $resultado = $conexion->query($sql);
            while ($fila = $resultado->fetch_assoc()) {                
                echo "<tr id='" . $fila["ID_CONVOCATORIA"] . "'><td>" . $fila["NOMBRE_TORNEO"] . "</td><td>" .
                "<a class='news' href='DETALLES_CONVOCATORIA.html' onclick='eviar_id_convocatoria(" . $fila["ID_CONVOCATORIA"] . ")'><h5>ver mas</h5></a></td></tr>";                
            }
            break;
        //Realiza una modificacion en la base de datos para cambiar la fecha de cierre de la convocatoria lanzada
        case "modificar":
            $nueva_fecha = $_POST['nueva_fecha'];
            $id = $_POST['id'];
            //Convierte el string ingresado al formato de fecha de php y la BD
            $nueva_fecha = strtotime($nueva_fecha);
            $nueva_fecha = date("Y-m-d", $nueva_fecha);
            //se valida la fecha para comprobar si es correcta y si existe
            $validar_nueva_fecha = explode('/', $_POST['nueva_fecha']);
            if (count($validar_nueva_fecha) == 3) {
                if ($validar_nueva_fecha[0] != "" && $validar_nueva_fecha[1] != "" && $validar_nueva_fecha[2] != "") {
                    if (checkdate($validar_nueva_fecha[0], $validar_nueva_fecha[1], $validar_nueva_fecha[2]) == true) {
                        //Se realiza una consulta preparada por seguridad, puesto que se necesitan eviar datos a la BD
                        $consulta = $conexion->prepare("UPDATE convocatoria SET FECHA_CIERRE_CONVOCATORIA = ? WHERE ID_CONVOCATORIA = ?");
                        $consulta->bind_param("si", $nueva_fecha, $id);
                        if ($consulta->execute()) {
                            //Si las consultas y todo lo demas se ejecuta correctamente re regresa un "ok" para indicar que no hubo ningun problema.
                            echo "ok";
                        } else {
                            echo "error";
                        }
                    } else {
                        //Se regresa un error en caso de que la fecha no tenga un formato valido                      
                        echo "Ingresa una fecha válida";
                    }
                } else {
                    //Se regresa un error en caso de que la fecha no tenga un formato valido                 
                    echo "Ingresa un formato de fecha válido";
                }
            } else {
                //Se regresa un error en caso de que la fecha no tenga un formato valido                
                echo "Ingresa un formato de fecha válido";
            }
            break;
    }
    $conexion->close();
?>
