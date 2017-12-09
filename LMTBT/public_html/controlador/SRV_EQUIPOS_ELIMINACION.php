<?php

session_start();

include("SRV_CONEXION.php");
$db = new SRV_CONEXION();

switch ($_POST['tipo']) {
	case "eliminar_equipo":
		
		break;
	default:
		echo "Parámetro 'tipo' erróneo.";
}
$db->close();
?>
