<?php
require 'SRV_CONEXION.php';
session_start();
$conn = new SRV_CONEXION();
$db = $conn->getConnection();
$sql;
$accion = $_POST["accion"];
switch ($accion) {
    // Se recuperan los roles de juego del torneo seleccionado, para que posteriormente se le muestren al usuario, 
    // administrador o capturista.
    case "getTable":
        $idtorneo = $_POST["id"];  // almacena el ID del torneo seleccionado
        $sql = "SELECT ID_ROL_JUEGO,ID_CONVOCATORIA,equipos.NOMBRE_EQUIPO,eq.NOMBRE_EQUIPO as NAME,FECHA,HORA,CAMPO FROM roles_juego INNER JOIN equipos ON roles_juego.ID_EQUIPO_1 = equipos.ID_EQUIPO INNER JOIN equipos as eq ON roles_juego.ID_EQUIPO_2 = eq.ID_EQUIPO WHERE ID_CONVOCATORIA =" . $idtorneo;
        $result = $db->query($sql);
        if ($result != null) {
            // se verifica si el resultado tiene elementos, en caso de que sí, se prepararán los datos a mostrar en la interfaz,
            // de lo contrario se notificará el error.
            if (mysqli_num_rows($result) <= 0) {
                echo "Failx";
            } else {
                if (isset($_SESSION["ID_USUARIO"])) {
                    // Se verifica el tipo de usuario para poder asignarle permisos de edición.
                    if ($_SESSION["TIPO_USUARIO"] == 'CAPTURISTA' || $_SESSION["TIPO_USUARIO"] == 'ADMINISTRADOR') {
                        while ($row = mysqli_fetch_array($result)) {
                            echo "<tr><td>" . $row['NOMBRE_EQUIPO'] . "</td><td></td><td>" . $row['NAME'] . "</td><td>" . $row['FECHA'] . "</td><td>" . $row['HORA'] . "</td><td>" . $row['CAMPO'] . "</td><td><a class='btn btn-floating  edit' onclick='editTable()' id='" . $row['ID_ROL_JUEGO'] . "'><i class='material-icons' >edit</i></a></td></tr>";
                        }
                        // en caso de que el usuario no sea el indicado, solo se le permitirá ver los horarios
                    } else {
                        while ($row = mysqli_fetch_array($result)) {
                            echo "<tr><td>" . $row['NOMBRE_EQUIPO'] . "</td><td></td><td>" . $row['NAME'] . "</td><td>" . $row['FECHA'] . "</td><td>" . $row['HORA'] . "</td><td>" . $row['CAMPO'] . "</tr>";
                        }
                    }
                } else {
                    // en caso de que no exista un usuario, se tomara como imformacion publica, y no se permitirá su edición.
                    while ($row = mysqli_fetch_array($result)) {
                        echo "<tr><td>" . $row['NOMBRE_EQUIPO'] . "</td><td></td><td>" . $row['NAME'] . "</td><td>" . $row['FECHA'] . "</td><td>" . $row['HORA'] . "</td><td>" . $row['CAMPO'] . "</tr>";
                    }
                }
            }
        } else {
            // en caso de que ocurra un error, se notificará al usuario
            echo "Fail";
        }
        break;
    case "getTorneo":
        // Se recuperan todos los torneos que se encuentren activos, es decir que estan por ejecutarse.
        $sql = "SELECT * FROM convocatoria WHERE ESTADO = 'ACTIVO'";
        $result = $db->query($sql);
        if ($result != null) {
            // se verifica que existan torneos activos, si no existe se le notifica al usuario
            if (mysqli_num_rows($result) <= 0) {
                echo "Failx";
            } else {
                // de lo contrario, se preparan los datos para mostrarlos al usuario.
                while ($row = mysqli_fetch_array($result)) {
                    echo "<option value='" . $row['ID_CONVOCATORIA'] . "'>" . $row['NOMBRE_TORNEO'] . "</option>";
                }
            }
        }
        break;
    case "guardar_Horario":
        // Se prepara una consulta con los datos del horario que tendra el rol de juego.
        $consulta = $conn->getConnection()->prepare("UPDATE roles_juego SET FECHA = ?, HORA = ?, CAMPO = ? WHERE ID_ROL_JUEGO = ?");
        // se envian los parametros del horario.
        $consulta->bind_param("sssi", $_POST['fecha'], $_POST['hora'], $_POST['campo'], $_POST['id']);
        // se ejecuta la consulta, en caso de que no haya ocurrido algun error se notifica al usuario
        if ($consulta->execute()) {
            echo "ok";
        } else {
            // en caso de que haya ocurrido algún error, se notifica al usuario.
            echo "no";
        }
        break;
}
?> 





