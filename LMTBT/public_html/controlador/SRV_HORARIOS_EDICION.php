<?php
session_start();
include("SRV_CONEXION.php");
$db = new SRV_CONEXION();
$sql;
$accion = $_POST["accion"];
switch ($accion) {
    case "getData":
        $ide = $_POST["id"]; // almacena el ID del rol de juego seleccionado para su edición.
        $sql = "SELECT equipos.NOMBRE_EQUIPO,eq.NOMBRE_EQUIPO as NAME,FECHA,HORA,CAMPO FROM roles_juego INNER JOIN equipos ON roles_juego.ID_EQUIPO_1 = equipos.ID_EQUIPO INNER JOIN equipos as eq ON roles_juego.ID_EQUIPO_2 = eq.ID_EQUIPO WHERE ID_ROL_JUEGO = " . $ide;
        $db->setQuery($sql); // se ejecuta la consulta
        $result = $db->getResult(); // se recuperan los datos
        if ($result != null) {  // si existen datos del rol de juegos se retornal al usuario para su edición.
            $data = json_encode($result, JSON_UNESCAPED_UNICODE);
            echo $data;
        } else {
            // si ocurre un error se notifica al usuario.
            echo "Fail";
        }
        break;
    case "update":
        // Se recuperan los datos del formulario correspondiente a la edición de horarios.
        $fecha = $_POST["fecha"]; // almacena la fecha del rol de juegos
        $hora = $_POST["hora"]; // almacena la hora en que ocurrira el rol de juego.
        $campo = $_POST["campo"]; // almacena el lugar en donde se ejecutará el rol de juego.
        $id = $_POST["id"];   // almacena el ID el rol de juego.
        $sql = "UPDATE roles_juego SET FECHA = '$fecha',HORA ='$hora',CAMPO ='$campo' WHERE ID_ROL_JUEGO ='$id'";
        $db->setQuery($sql);
        // se ejecuta la consulta y se notifica al usuario el caso de exito o viceversa.
        $result = $db->ExecuteQuery();
        if (result) {
            echo "ok";
        } else {
            echo "Fail";
        }
        break;
}
?> 




