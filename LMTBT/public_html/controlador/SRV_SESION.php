<?php

session_start();

include("SRV_CONEXION.php");
$db = new SRV_CONEXION();
$tipo =$_POST['tipo'];

switch ($tipo) {
	/*
	 * Al iniciar sesion se asignan los datos del usuario a las variables de SESSION**/
	case "login":
		// preparar una consilta para recuperar los datos del usuario para las variables de SESSION
		$consulta = $db->getConnection()->prepare("SELECT ID_USUARIO, CORREO, TIPO_USUARIO FROM usuarios WHERE CORREO = ?  AND PASSWORD = ?");
		$consulta->bind_param("ss", $_POST['e_mail'], $_POST['password']);
		//si la consulta se ejecuta correctamente entonces asociamos los datos y le asignamos valores a las variables de SESSION
		// de lo contrario el correo y la contraseña con la que el usuario inicio session son invalidos.
		if ($consulta->execute()){
			$resultado = $consulta->get_result();
			$informacion = $resultado->fetch_assoc();
			if ($informacion['ID_USUARIO']){
				$_SESSION['ID_USUARIO'] = $informacion['ID_USUARIO'];
				$_SESSION['CORREO'] = $informacion['CORREO'];
				$_SESSION['TIPO_USUARIO'] = $informacion['TIPO_USUARIO'];
				echo "ok";
			} else {
				echo "Tus datos son inválidos";
			}
		} else {
			echo "Ocurrió un error en el servidor, vuelva a intentarlo, por favor.";
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
		/*
		 * validamos si existe la sesión, en caso de existir el servidor responde "ok", lo que significa que la funcionalidad del boton de iniciar sesion cambiaria a cerrar sesion
		 * **/
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
