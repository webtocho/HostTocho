<?php

session_start();

include("SRV_CONEXION.php");
$db = new SRV_CONEXION();
$tipo =$_POST['tipo'];

switch ($tipo) {
	case "login":
		// preparar una consilta para recuperar los datos del usuario para la SESSION
		$consulta = $db->getConnection()->prepare("SELECT ID_USUARIO, CORREO, TIPO_USUARIO FROM usuarios WHERE CORREO = ?  AND PASSWORD = ?");
		$consulta->bind_param("ss", $_POST['e_mail'], $_POST['password']);
		//si al recuperar la informacion todo sale bien entonces asociamos los datos y le asignamos valores a las variables de SESSION
		// de lo contrario el correo y la contraseña con la que inician sessionson invalidos.
		if ($consulta->execute()){
			$res = $consulta->get_result();
			$info = $res->fetch_assoc();
			if ($info['ID_USUARIO']){
				$_SESSION['ID_USUARIO'] = $info['ID_USUARIO'];
				$_SESSION['CORREO'] = $info['CORREO'];
				$_SESSION['TIPO_USUARIO'] = $info['TIPO_USUARIO'];
				echo "ok";
			} else {
				echo "Tus datos son inválidos";
			}
		} else {
			echo "Ocurrio un error en el servidor, vuelva a intentarlo por favor";
		}
		break;
	case "logout":
		// borramos la cookie de la SESSION.
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(
					session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
			);
		}
		// destruimos la SESSION.
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
