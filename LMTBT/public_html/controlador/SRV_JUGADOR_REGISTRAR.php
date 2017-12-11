<?php

session_start();

include("SRV_CONEXION.php");
$db = new SRV_CONEXION();

switch ($_POST['tipo']) {
	case "registra_jugador":
		// creamos una transaccion y una consulta preparada para poder registrar a los jugadores, si ocurre un error, se podra revertir la operacion, evitando perjudicar datos de la tabla usuario
		$conexion = $db->getConnection();
		$conexion->autocommit(FALSE);

		$consulta = $conexion->prepare('SELECT CORREO FROM usuarios WHERE CORREO = ?');
		$consulta->bind_param("s", $_POST['correo']);
		if ($consulta->execute()) {
			$result = $consulta->get_result();
			$correo = $result->fetch_assoc();
			if ($correo['CORREO']) {
				// si existe el correo, el servidor le contesta si
				$conexion->rollback();
				$conexion->autocommit(TRUE);
				echo "existe";
			} else {
				// si no existe el correo, el servidor responde no
				$consulta = $conexion->prepare('INSERT INTO usuarios(CORREO, PASSWORD, NOMBRE, APELLIDO_PATERNO, APELLIDO_MATERNO, FECHA_NACIMIENTO, SEXO, TIPO_SANGRE, TELEFONO, FOTO_PERFIL, FACEBOOK, INSTAGRAM, TWITTER, TIPO_USUARIO) VALUES (?, ?, ?, ?, ?, null, ?, null, null, null, null, null, null, ?)');
				$consulta->bind_param("sssssss", $_POST['correo'], $_POST['password'], $_POST['nombre'], $_POST['apellido_paterno'], $_POST['apellido_materno'], $_POST['sexo'], $_POST['tipo_usuario']);
				if ($consulta->execute()) {
					// si todo salio bien entonces se acepta la transaccion 
					$conexion->commit();
					$conexion->autocommit(TRUE);
					echo "ok";
				} else {
					// si ocurre un error se revierte la operacion
					$conexion->rollback();
					$conexion->autocommit(TRUE);
					echo "Ha ocurrido un error al guardar la informacion. Intentelo mas tarde.";
				}
			}
		} else {
			$conexion->rollback();
			$conexion->autocommit(TRUE);
			echo "Ha ocurrido un error al guardar la informacion. Intentelo mas tarde.";
		}		
		break;
	default:
		echo "Parámetro 'tipo' erróneo.";
}
$db->close();
?>
