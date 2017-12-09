<?php

session_start();

include("SRV_CONEXION.php");
$db = new SRV_CONEXION();

switch ($_POST['tipo']) {
	case "login":
		$consulta = $db->getConnection()->prepare("SELECT ID_USUARIO, CORREO, tipos_de_usuario.NOMBRE FROM usuarios INNER JOIN tipos_de_usuario ON usuarios.TIPO_USUARIO = tipos_de_usuario.ID_TIPO_USUARIO WHERE CORREO = ?  AND PASSWORD = ?");
		$consulta->bind_param("ss", $_POST['e_mail'], $_POST['password']);
		if ($consulta->execute()) {
			$res = $consulta->get_result();
			$info = $res->fetch_assoc();
			if ($info['ID_USUARIO']) {
				$_SESSION['ID_USUARIO'] = $info['ID_USUARIO'];
				$_SESSION['CORREO'] = $info['CORREO'];
				$_SESSION['TIPO_USUARIO'] = $info['NOMBRE'];
				echo "ok";
			} else {
				echo "Tus datos son inválidos";
			}
		} else {
			echo "Ocurrio un error en el servidor, vuelva a intentarlo por favor";
		}
		break;
	case "logout":
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(
					session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
			);
		}
		session_destroy();
		echo "ok";
		break;
	case "iniciar_cerrar_session":
		if (isset($_SESSION['ID_USUARIO'])) {
			echo "ok";
		} else {
			echo "no";
		}
		break;
	default:
		echo "Parámetro 'tipo' erróneo.";
}
$db->close();
?>
