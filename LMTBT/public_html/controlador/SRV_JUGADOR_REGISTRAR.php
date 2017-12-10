<?php

session_start();

include("SRV_CONEXION.php");
$db = new SRV_CONEXION();

switch ($_POST['tipo']) {
	case "registra_jugador":
		$conexion = $db->getConnection();
		$conexion->autocommit(FALSE);

		$consulta = $conexion->prepare('INSERT INTO usuarios(CORREO, PASSWORD, NOMBRE, APELLIDO_PATERNO, APELLIDO_MATERNO, FECHA_NACIMIENTO, SEXO, TIPO_SANGRE, TELEFONO, FOTO_PERFIL, FACEBOOK, INSTAGRAM, TWITTER, TIPO_USUARIO) VALUES (?, ?, ?, ?, ?, null, ?, null, null, null, null, null, null, ?)');
		$consulta->bind_param("ssssssi", $_POST['correo'], $_POST['password'], $_POST['nombre'], $_POST['apellido_paterno'], $_POST['apellido_materno'], $_POST['sexo'], $_POST['tipo_usuario']);
		if ($consulta->execute()) {
			echo "ok";
			$conexion->commit();
			$conexion->autocommit(TRUE);
		} else {
			echo "Ha ocurrido un error al guardar la informacion. Intentelo mas tarde.";
			$conexion->rollback();
			$conexion->autocommit(TRUE);
		}
		$consulta->close();
		break;
	default:
		echo "Parámetro 'tipo' erróneo.";
}
$db->close();
?>
