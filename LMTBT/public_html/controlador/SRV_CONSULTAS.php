<?php

session_start();
date_default_timezone_set('America/Mexico_City');

//PENDIENTE: Cambiar estas constantes
define("MIEMBROS_MAXIMOS_DE_ROSTER", 12);
define("MIEMBROS_MINIMOS_DE_ROSTER", 3);

include("SRV_CONEXION.php");
$db = new SRV_CONEXION();

function obtener_condiciones_de_categoria($categoria) {
	switch ($categoria) {
		//PENDIENTE : PREGUNTAR QUÉ CONDICIÓN SE APLICA A CADA CATEGORÍA
		case "VARONIL":
			return "AND SEXO = 'MASCULINO'";
			break;
		case "FEMENIL":
			return "AND SEXO = 'FEMENINO'";
			break;
		case "MIXTO":
			//PENDIENTE : PREGUNTAR SI SÓLO PARTICIPAN MAYORES DE CIERTA EDAD
			return " ";
			break;
		case "HEAVY":
			//PENDIENTE : HALLAR UN FORMA DE FILTRAR A ESTOS JUGADORES
			return " ";
			break;
		case "RABBIT":
			//PENDIENTE : PREGUNTAR EDAD MÍNIMA
			//Sólo participan los menores de 16 años
			return "AND DATEDIFF(CURDATE(), FECHA_NACIMIENTO)/365<16";
			break;
		case "MAS DE 40":
			return "AND DATEDIFF(CURDATE(), FECHA_NACIMIENTO)/365>39";
			break;
		default:
			return null;
	}
}

switch ($_POST['tipo']) {
	case "login":
		$sql = sprintf("SELECT ID_USUARIO, CORREO, tipos_de_usuario.NOMBRE FROM usuarios INNER JOIN tipos_de_usuario ON usuarios.TIPO_USUARIO = tipos_de_usuario.ID_TIPO_USUARIO WHERE CORREO = '%s'  AND PASSWORD = '%s' ", $_POST['e_mail'], $_POST['password']);
		$db->setQuery($sql);
		$info = $db->GetRow();

		if ($info['ID_USUARIO']) {
			$_SESSION['ID_USUARIO'] = $info['ID_USUARIO'];
			$_SESSION['CORREO'] = $info['CORREO'];
			$_SESSION['TIPO_USUARIO'] = $info['NOMBRE'];
			echo "ok";
		} else {
			echo "Tus datos son inválidos";
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
	case "crear_coach":
		//Crea un nuevo usuario de tipo choach.
		//Falta comprobar que no se creen cuentas con apellidos ni correos repetidos.
		$conexion = $db->getConnection();
		$consulta = $conexion->prepare('INSERT INTO usuarios(ID_USUARIO, CORREO, PASSWORD, TIPO_USUARIO, ESTADO, NOMBRE, APELLIDO_PATERNO, APELLIDO_MATERNO, FECHA_NACIMIENTO, SEXO, TIPO_SANGRE, TELEFONO, FOTO_PERFIL, FACEBOOK, INSTAGRAM, TWITTER, ID_REGISTRADOR) VALUES (0, ?, ?, ?, ?, ?, ?, ?, null, null, null, null, null, null, null, null, null)');
		$consulta->bind_param("sssssss", $_POST['correo'], $_POST['password'], $_POST['tipo_usuario'], $_POST['estado'], $_POST['nombre'], $_POST['apellido_paterno'], $_POST['apellido_materno']);
		if ($consulta->execute()) {
			echo "ok";
		} else {
			echo $consulta->error;
		}

		$consulta->close();
		/* CÓDIGO VIEJO
		  $sql = sprintf("INSERT INTO usuarios(CORREO, PASSWORD, TIPO_USUARIO, ESTADO, NOMBRE, APELLIDO_PATERNO, APELLIDO_MATERNO, FECHA_NACIMIENTO, SEXO, TIPO_SANGRE, TELEFONO, FOTO_PERFIL, FACEBOOK, INSTAGRAM, TWITTER) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')", $_POST['correo'], $_POST['password'], $_POST['tipo_usuario'], $_POST['estado'], $_POST['nombre'], $_POST['apellido_paterno'], $_POST['apellido_materno'], $_POST['fecha_nacimiento'], $_POST['sexo'], $_POST['tipo_sangre'], $_POST['telefono'], $_POST['foto_perfil'], $_POST['facebook'], $_POST['instagram'], $_POST['twiter']);
		  $db->setQuery($sql);
		  $db->ExecuteQuery();
		  echo "ok"; */
		break;
	case "get_info_cuenta":
		/**
		 * Permite obtener la información de una cuenta.
		 * Devuelve un JSON (cuyos atributos se llaman igual a los atributos de la tabla de usuarios en la BD) con los datos de la cuenta.
		 * Si hubo un error, el atributo 'error' del JSON tiene un string con la explicación.
		 * Si no hubo error, 'error' es nulo y el JSON tendrá los atributos de la cuenta.
		 * 
		 * El usuario leído es el que tenga su sesión iniciada en el momento de hacer la petición.
		 * Si el usuario con la sesión iniciada es un coach o el administrador, puede enviar
		 * el parámetro entero 'idCuenta', donde indica que quiere consultar los datos de otro usuario.
		 * 
		 * Se reciben 5 parámetros opcionales que indican qué partes del registro de van a retornar.
		 * Estos son booleanos, que se consideran falsos sólo si valen "0". 
		 * 1.- 'nombre', indica si se incluye el nombre y los apellidos.
		 * 2.- 'correo', para el correo electrónico.
		 * 3.- 'redes' para las redes sociales.
		 * 4.- 'otros' los campos de sexo, tipo de sangre y teléfono.
		 * 5.- 'foto' para la imagen de perfil.
		 * Si uno de estos 5 no se manda, se dará por entendido que no se desea que se retorne.
		 * Sin embargo, si ninguno de los 5 se manda, se devovera TODA la información de la cuenta (excepto contraseña).
		 * 
		 * Hecho por Argüello Tello.
		 */
		$info_cuenta = array();

		if (isset($_SESSION["ID_USUARIO"]) && isset($_SESSION["TIPO_USUARIO"])) {
			if ($_SESSION["TIPO_USUARIO"] == "COACH" || $_SESSION["TIPO_USUARIO"] == "ADMINISTRADOR") {
				if (empty($_POST['idCuenta'])) {
					$_POST['idCuenta'] = $_SESSION["ID_USUARIO"];
				}
			} else {
				$_POST['idCuenta'] = $_SESSION["ID_USUARIO"];
			}
		} else {
			unset($_POST['idCuenta']);
		}

		if (!empty($_POST['idCuenta'])) {
			$conexion = $db->getConnection();
			if ($conexion != null) {
				$datosASeleccionar = "";
				if (isset($_POST['nombre']))
					if (boolval($_POST['nombre']))
						$datosASeleccionar .= "NOMBRE, APELLIDO_PATERNO, APELLIDO_MATERNO";

				if (isset($_POST['tipo_usuario']))
					if (boolval($_POST['tipo_usuario']))
						$datosASeleccionar .= (empty($datosASeleccionar) ? "" : ", ") . "TIPO_USUARIO";

				if (isset($_POST['correo']))
					if (boolval($_POST['correo']))
						$datosASeleccionar .= (empty($datosASeleccionar) ? "" : ", ") . "CORREO";

				if (isset($_POST['redes']))
					if (boolval($_POST['redes']))
						$datosASeleccionar .= (empty($datosASeleccionar) ? "" : ", ") . "FACEBOOK, INSTAGRAM, TWITTER";

				if (isset($_POST['otros']))
					if (boolval($_POST['otros']))
						$datosASeleccionar .= (empty($datosASeleccionar) ? "" : ", ") . "FECHA_NACIMIENTO, SEXO, TIPO_SANGRE, TELEFONO";

				if (isset($_POST['foto']))
					if (boolval($_POST['foto']))
						$datosASeleccionar .= (empty($datosASeleccionar) ? "" : ", ") . "FOTO_PERFIL";

				if (empty($datosASeleccionar))
					$datosASeleccionar .= "*";


				//Creamos nuestra consulta preparada.
				$consulta = $conexion->prepare('SELECT ' . $datosASeleccionar . ' FROM usuarios WHERE ID_USUARIO = ?');
				$consulta->bind_param("i", $_POST['idCuenta']);

				if ($consulta->execute()) {
					$res = $consulta->get_result();

					if ($res->num_rows == 1) {
						//Vamos a enviar la fila de la tabla como resultado.
						$info_cuenta = $res->fetch_assoc();
						//Indicamos que no ocurrió ningún error.
						$info_cuenta['error'] = NULL;
						//Si se incluyó la foto de perfil, la convertimos a un formato legible.
						if (!empty($info_cuenta["FOTO_PERFIL"])) {
							$info_cuenta["FOTO_PERFIL"] = base64_encode($info_cuenta["FOTO_PERFIL"]);
						}
						//Eliminamos datos comprometedores.
						unset($info_cuenta['ID_USUARIO']);
						unset($info_cuenta['PASSWORD']);
					} else {
						$info_cuenta['error'] = "No existe un usuario con el ID señalado.";
					}
				} else {
					$info_cuenta['error'] = "El parámetro 'idCuenta' es incorrecto.";
				}

				$consulta->close();
			} else {
				$info_cuenta['error'] = "No se pudo hacer la conexión con la base de datos.";
			}
		} else {
			$info_cuenta['error'] = "No ha iniciado sesión.";
		}

		//Encriptamos $info_cuenta como un JSON y lo enviamos como respuesta.
		echo json_encode($info_cuenta);
		break;
	case "editar_cuenta":
		if (isset($_SESSION["ID_USUARIO"]) && isset($_SESSION["TIPO_USUARIO"])) {
			if (empty($_POST['id_cuenta'])) {
				$_POST['id_cuenta'] = $_SESSION["ID_USUARIO"];
			} else {
				if ($_SESSION["TIPO_USUARIO"] != "ADMINISTRADOR") {
					echo "Usted no tiene permiso de editar cuentas ajenas.";
					var_dump($_POST['id_cuenta']);
					unset($_POST['id_cuenta']);
				}
			}
		} else {
			echo "La sesión se cerró.";
			unset($_POST['id_cuenta']);
		}

		if (empty($_POST['id_cuenta'])) {
			break;
		}

		if (!empty($_FILES['FOTO_PERFIL']['name'])) {
			if ($_FILES["FOTO_PERFIL"]["size"] > 5242880) {
				echo "La foto de perfil es demasiada grande.";
				break;
			} else if (getimagesize($_FILES["FOTO_PERFIL"]["tmp_name"]) === false) {
				echo "La archivo que mandó como foto de perfil es inválido.";
				break;
			}
		}

		$parametros = array("");
		$modificaciones_en_query = "";
		$hay_datos_erroneos = false;

		if (!empty($_POST['APELLIDO_PATERNO'])) {
			$_POST['APELLIDO_PATERNO'] = trim($_POST['APELLIDO_PATERNO']);
			$_POST['APELLIDO_PATERNO'] = preg_replace('/\s+/', ' ', $_POST['APELLIDO_PATERNO']);
			if (preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ' -]{2,}$/", $_POST['APELLIDO_PATERNO'])) {
				$parametros[0] .= "s";
				array_push($parametros, $_POST['APELLIDO_PATERNO']);
				unset($_POST['APELLIDO_PATERNO']);
				$modificaciones_en_query .= "APELLIDO_PATERNO = ?";
			} else {
				echo "El apellido paterno está mal escrito (sólo debe contener letras).\n";
				$hay_datos_erroneos = true;
			}
		}

		if (!empty($_POST['APELLIDO_MATERNO'])) {
			$_POST['APELLIDO_MATERNO'] = trim($_POST['APELLIDO_MATERNO']);
			$_POST['APELLIDO_MATERNO'] = preg_replace('/\s+/', ' ', $_POST['APELLIDO_MATERNO']);
			if (preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ '-]{2,}$/", $_POST['APELLIDO_MATERNO'])) {
				$parametros[0] .= "s";
				array_push($parametros, $_POST['APELLIDO_MATERNO']);
				unset($_POST['APELLIDO_MATERNO']);
				$modificaciones_en_query .= (empty($modificaciones_en_query) ? "" : ", ") . "APELLIDO_MATERNO = ?";
			} else {
				echo "El apellido materno está mal escrito (sólo debe contener letras).\n";
				$hay_datos_erroneos = true;
			}
		}

		if (!empty($_POST['NOMBRE'])) {
			$_POST['NOMBRE'] = trim($_POST['NOMBRE']);
			$_POST['NOMBRE'] = preg_replace('/\s+/', ' ', $_POST['NOMBRE']);
			if (preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ '-]{2,}$/", $_POST['NOMBRE'])) {
				$parametros[0] .= "s";
				array_push($parametros, $_POST['NOMBRE']);
				unset($_POST['NOMBRE']);
				$modificaciones_en_query .= (empty($modificaciones_en_query) ? "" : ", ") . "NOMBRE = ?";
			} else {
				echo "El nombre está mal escrito (sólo debe contener letras).\n";
				$hay_datos_erroneos = true;
			}
		}

		if (!empty($_POST['CORREO'])) {
			$_POST['CORREO'] = trim($_POST['CORREO']);
			if (preg_match("/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/", $_POST['CORREO'])) {
				$parametros[0] .= "s";
				array_push($parametros, $_POST['CORREO']);
				//unset($_POST['CORREO']);
				$modificaciones_en_query .= (empty($modificaciones_en_query) ? "" : ", ") . "CORREO = ?";
			} else {
				echo "El correo electrónico es inválido.\n";
				$hay_datos_erroneos = true;
			}
		}

		if (!empty($_POST['PASSWORD'])) {
			$_POST['PASSWORD'] = trim($_POST['PASSWORD']);
			if (preg_match("/^[a-zA-Z]\w{4,15}$/", $_POST['PASSWORD'])) {
				$parametros[0] .= "s";
				array_push($parametros, $_POST['PASSWORD']);
				unset($_POST['PASSWORD']);
				$modificaciones_en_query .= (empty($modificaciones_en_query) ? "" : ", ") . "PASSWORD = ?";
			} else {
				echo "La contraseña es inválida [debe comenzar con una letra, debe tener entre 5 y 15 caracteres, sólo puede contener letras (sin contar la Ñ/ñ, ni letras acentuadas o con diéresis) y números].\n";
				$hay_datos_erroneos = true;
			}
		}

		if ($_SESSION["TIPO_USUARIO"] == "JUGADOR") {
			if (!empty($_POST['FECHA_NACIMIENTO'])) {
				$_POST['FECHA_NACIMIENTO'] = trim($_POST['FECHA_NACIMIENTO']);

				//Checamos si la fecha es válida
				$d = DateTime::createFromFormat("Y-m-d", $_POST['FECHA_NACIMIENTO']);
				$fecha_valida = $d && $d->format("Y-m-d") == $_POST['FECHA_NACIMIENTO'];
				if ($fecha_valida) {
					//PENDIENTE: Hallar una forma de obtener los años máximo y mínimo relativos.
					if (intval($d->format("Y")) < 1960 || intval($d->format("Y")) > 2009) {
						$fecha_valida = false;
						echo "Año fuera del rango permitido. ";
					}
				}
				unset($d);

				if ($fecha_valida) {
					$parametros[0] .= "s";
					array_push($parametros, $_POST['FECHA_NACIMIENTO']);
					unset($_POST['FECHA_NACIMIENTO']);
					$modificaciones_en_query .= (empty($modificaciones_en_query) ? "" : ", ") . "FECHA_NACIMIENTO = ?";
				} else {
					echo "La fecha de nacimiento es inválida.\n";
					$hay_datos_erroneos = true;
				}
			}

			if (!empty($_POST['TELEFONO'])) {
				$_POST['TELEFONO'] = trim($_POST['TELEFONO']);
				if (preg_match("/^[0-9]{10}$/", intval($_POST['TELEFONO']))) {
					$parametros[0] .= "i";
					array_push($parametros, intval($_POST['TELEFONO']));
					unset($_POST['TELEFONO']);
					$modificaciones_en_query .= (empty($modificaciones_en_query) ? "" : ", ") . "TELEFONO = ?";
				} else {
					echo "El teléfono es incorrecto (Debe de ser 10 dígitos sin símbolos, ej: \"9611234567\").\n";
					$hay_datos_erroneos = true;
				}
			}

			if (!empty($_POST['TIPO_SANGRE'])) {
				$parametros[0] .= "s";
				array_push($parametros, $_POST['TIPO_SANGRE']);
				unset($_POST['TIPO_SANGRE']);
				$modificaciones_en_query .= (empty($modificaciones_en_query) ? "" : ", ") . "TIPO_SANGRE = ?";
			}

			if (!empty($_POST['FACEBOOK'])) {
				$_POST['FACEBOOK'] = trim($_POST['FACEBOOK']);
				if (preg_match("/(?:(?:http|https):\/\/)?(?:www.)?facebook.com\/(?:(?:\w)*#!\/)?(?:pages\/)?(?:[?\w\-]*\/)?(?:profile.php\?id=(\d.*))?(?:[\w\-\.]*)?/", $_POST['FACEBOOK'], $coincidencias)) {
					$parametros[0] .= "s";
					if (substr($coincidencias[0], 0, 7) !== "http://" && substr($coincidencias[0], 0, 8) !== "https://")
						$coincidencias[0] = "https://" . $coincidencias[0];
					array_push($parametros, $coincidencias[0]);
					unset($_POST['FACEBOOK']);
					$modificaciones_en_query .= (empty($modificaciones_en_query) ? "" : ", ") . "FACEBOOK = ?";
				} else {
					echo "El enlace de Facebook no es válido.\n";
					$hay_datos_erroneos = true;
				}
			}

			if (!empty($_POST['TWITTER'])) {
				$_POST['TWITTER'] = trim($_POST['TWITTER']);
				if (preg_match("/(?:(?:http|https):\/\/)?(?:www\.)?twitter\.com\/(?:(?:\w)*#!\/)?(?:pages\/)?(?:[\w\-]*\/)*(?:[\w\-]*)/", $_POST['TWITTER'], $coincidencias)) {
					$parametros[0] .= "s";
					if (substr($coincidencias[0], 0, 7) !== "http://" && substr($coincidencias[0], 0, 8) !== "https://")
						$coincidencias[0] = "https://" . $coincidencias[0];
					array_push($parametros, $coincidencias[0]);
					unset($_POST['TWITTER']);
					$modificaciones_en_query .= (empty($modificaciones_en_query) ? "" : ", ") . "TWITTER = ?";
				} else {
					echo "El enlace de Twitter no es válido.\n";
					$hay_datos_erroneos = true;
				}
			}

			if (!empty($_POST['INSTAGRAM'])) {
				$_POST['INSTAGRAM'] = trim($_POST['INSTAGRAM']);
				if (preg_match("/(?:(?:http|https):\/\/)?(?:www.)?(?:instagram.com|instagr.am)\/(?:[A-Za-z0-9-_]+)/", $_POST['INSTAGRAM'], $coincidencias)) {
					$parametros[0] .= "s";
					if (substr($coincidencias[0], 0, 7) !== "http://" && substr($coincidencias[0], 0, 8) !== "https://")
						$coincidencias[0] = "https://" . $coincidencias[0];
					array_push($parametros, $coincidencias[0]);
					unset($_POST['INSTAGRAM']);
					$modificaciones_en_query .= (empty($modificaciones_en_query) ? "" : ", ") . "INSTAGRAM = ?";
				} else {
					echo "El enlace de Instagram no es válido.\n";
					$hay_datos_erroneos = true;
				}
			}
		}

		if ($hay_datos_erroneos) {
			break;
		} else if (empty($modificaciones_en_query) && empty($_FILES['FOTO_PERFIL']['name'])) {
			echo "No se mandó ningún dato para editar.";
			break;
		}/* else {
		  echo json_encode($parametros);
		  echo "\n" . $modificaciones_en_query;
		  } */

		$conexion = $db->getConnection();
		$conexion->autocommit(FALSE);
		//$conexion->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

		if (!empty($modificaciones_en_query)) {
			$parametros[0] .= "i";
			array_push($parametros, intval($_POST['id_cuenta']));
			$consulta = $conexion->prepare("UPDATE usuarios SET " . $modificaciones_en_query . " WHERE ID_USUARIO = ?");
			if (!$consulta) {
				echo "Error al crear la consulta preparada.";
				break;
			}

			if (!$consulta->bind_param(...$parametros)) {
				echo "Error introducir los parámetros a la consulta.";
				break;
			}

			if (!$consulta->execute()) {
				echo "Error al modificar los datos.";
				break;
			}

			if (strpos($modificaciones_en_query, "NOMBRE") !== false || strpos($modificaciones_en_query, "APELLIDO") !== false) {
				try {
					$consulta = $conexion->prepare("SELECT APELLIDO_PATERNO, APELLIDO_MATERNO, NOMBRE FROM usuarios WHERE ID_USUARIO = ?");
					$consulta->bind_param("i", $_POST['id_cuenta']);
					$consulta->execute();
					$res = $consulta->get_result();
					if ($res->num_rows == 0) {
						echo "¡El usuario ya no existe!";
						$conexion->rollback();
						$conexion->autocommit(TRUE);
						return;
					}
					$nombre_usuario = $res->fetch_assoc();

					$consulta = $conexion->prepare("SELECT ID_USUARIO FROM usuarios WHERE ID_USUARIO != ? AND (APELLIDO_PATERNO LIKE ? AND APELLIDO_MATERNO LIKE ? AND NOMBRE LIKE ?)");
					$consulta->bind_param("isss", $_POST['id_cuenta'], $nombre_usuario["APELLIDO_PATERNO"], $nombre_usuario["APELLIDO_MATERNO"], $nombre_usuario["NOMBRE"]);
					$consulta->execute();
					$res = $consulta->get_result();
					if ($res->num_rows != 0) {
						echo "Ya existe otro usuario con el mismo nombre completo.";
						$conexion->rollback();
						$conexion->autocommit(TRUE);
						return;
					}
				} catch (Exception $exc) {
					echo "Error al comprobar si ya existe otro usuario con el mismo nombre.";
					$conexion->rollback();
					$conexion->autocommit(TRUE);
					return;
				}
			}

			if (strpos($modificaciones_en_query, "CORREO") !== false) {
				try {
					$consulta = $conexion->prepare("SELECT ID_USUARIO FROM usuarios WHERE ID_USUARIO != ? AND CORREO LIKE ?");
					$consulta->bind_param("is", $_POST['id_cuenta'], $_POST['CORREO']);
					$consulta->execute();
					$res = $consulta->get_result();
					if ($res->num_rows != 0) {
						echo "Ya existe otro usuario con el mismo correo electrónico.";
						$conexion->rollback();
						$conexion->autocommit(TRUE);
						return;
					}
				} catch (Exception $exc) {
					echo "Error al comprobar si ya existe un usuario con el mismo correo.";
					$conexion->rollback();
					$conexion->autocommit(TRUE);
					return;
				}
			}

			try {
				$consulta = $conexion->prepare("SELECT CATEGORIA FROM rosters INNER JOIN roster_jugador ON rosters.ID_ROSTER = roster_jugador.ID_ROSTER WHERE ID_JUGADOR = ?");
				$consulta->bind_param("i", $_POST['id_cuenta']);
				$consulta->execute();
				$res = $consulta->get_result();
				$categorias_del_jugador = array();
				while ($fila = $res->fetch_assoc()) {
					array_push($categorias_del_jugador, $fila["CATEGORIA"]);
				}
				//Eliminamos datos repetidos.
				$categorias_del_jugador = array_unique($categorias_del_jugador);
				$rosters_con_conflicto = "";

				foreach ($categorias_del_jugador as $categoria) {
					$consulta = $conexion->prepare("SELECT ID_USUARIO from usuarios WHERE ID_USUARIO = ? " . obtener_condiciones_de_categoria($categoria));
					$consulta->bind_param("i", $_POST['id_cuenta']);
					$consulta->execute();
					$res = $consulta->get_result();

					if ($res->num_rows == 0) {
						$rosters_con_conflicto .= (empty($rosters_con_conflicto) ? "" : ", ") . $categoria;
					}
				}

				if (!empty($rosters_con_conflicto)) {
					echo "El usuario es un jugador y es miembro de uno o más rosters de la categoría(s) <" . $rosters_con_conflicto . ">. No se puede hacer la modificación, porque el cambio de uno o varios datos harían que el jugador deje de aplicar a la(s) categoría(s).";
					$conexion->rollback();
					$conexion->autocommit(TRUE);
					return;
				}
			} catch (Exception $exc) {
				echo "Error al comprobar si este usuario no ha alterado su categoría, en caso de ser jugador.";
				$conexion->rollback();
				$conexion->autocommit(TRUE);
				return;
			}
		}

		if (!empty($_FILES['FOTO_PERFIL']['name'])) {
			$nombre_archivo_tmp = uniqid() . ".png";
			//Cargamos la imagen del logo original.
			$fp = fopen($_FILES['FOTO_PERFIL']['tmp_name'], 'r+');
			$foto_original = imagecreatefromstring(fread($fp, filesize($_FILES['FOTO_PERFIL']['tmp_name'])));
			fclose($fp);

			//Calculamos el ancho y alto para una versión de la imagen achicada.
			$alto = 150;
			$ancho = imagesx($foto_original) / imagesy($foto_original) * $alto;

			//Achicamos la imagen.
			$foto_achicada = imagecreatetruecolor($ancho, $alto);
			imagecopyresampled($foto_achicada, $foto_original, 0, 0, 0, 0, $ancho, $alto, imagesx($foto_original), imagesy($foto_original));

			//Gurardamos la imagen achicada en un archivo y la volvemos a abrir en $logotipo_final.
			imagepng($foto_achicada, $nombre_archivo_tmp);

			//Ya que tenemos lista la imagen final, guardamos en la base de datos.
			$se_agrego_la_imagen = false;
			try {
				/* $null = NULL;
				  $consulta = $conexion->prepare('UPDATE usuarios SET FOTO_PERFIL = ? WHERE ID_USUARIO = ?');
				  $consulta->bind_param("bi", $null, $_POST['id_cuenta']);
				  $consulta->send_long_data(0, file_get_contents($nombre_archivo_tmp)); */

				$consulta = $conexion->prepare("UPDATE usuarios SET FOTO_PERFIL = '" . addslashes(file_get_contents($nombre_archivo_tmp)) . "' WHERE ID_USUARIO = ?");
				$consulta->bind_param("i", $_POST['id_cuenta']);
				if ($consulta->execute()) {
					$se_agrego_la_imagen = true;
				}
			} catch (Exception $exc) {
				echo $exc->getTraceAsString();
			}

			//Eliminamos la copia de la imagen achicada en nuestro servidor.
			unlink($nombre_archivo_tmp);
			$consulta->close();

			if (!$se_agrego_la_imagen) {
				echo "Hubo un error al cambiar la imagen.";
				$conexion->rollback();
				$conexion->autocommit(TRUE);
				return;
			}
		}

		echo "ok";
		$conexion->commit();
		$conexion->autocommit(TRUE);
		break;
	case "buscar_coaches":
		/**
		 * Devuelve en un arreglo de JSON los ID's y los nombres de los coaches que empiecen por
		 * ciertas letras especificadas en el parámetro 'criterio'.
		 * 
		 * El arreglo es bidimensional, donde la columna 0 correponde a los ID's, y la 1 a los nombre completos.
		 * Cada fila corresponde a un COACH distinto.
		 * 
		 * Si el arreglo está vacío no se encontraron resultados o hubo un error.
		 * 
		 * Hecho por Argüello Tello.
		 */
		$conexion = $db->getConnection();
		if ($conexion != null) {
			//Creamos nuestra consulta preparada.
			$consulta = $conexion->prepare('SELECT ID_USUARIO, NOMBRE, APELLIDO_PATERNO, APELLIDO_MATERNO FROM usuarios WHERE TIPO_USUARIO = \'COACH\' AND APELLIDO_PATERNO like ?');
			$consulta->bind_param("s", $_POST['criterio']);

			$_POST['criterio'] = str_replace(array('%', '_'), '', $_POST['criterio']);
			$_POST['criterio'] .= "%";
			$coaches = array();

			if ($consulta->execute()) {
				$res = $consulta->get_result();

				while ($fila = $res->fetch_assoc()) {
					array_push($coaches, array($fila["ID_USUARIO"], $fila["APELLIDO_PATERNO"] . " " . $fila["APELLIDO_MATERNO"] . " " . $fila["NOMBRE"]));
				}
			}

			$consulta->close();
		}

		//Encriptamos $coaches como un JSON y lo enviamos como respuesta.
		echo json_encode($coaches);
		break;
	case "crear_equipo":
		/**
		 * Permite crear un nuevo equipo.
		 * En caso de éxito devuelve la cadena "ok". Si hubo un error, se devuelve una explicación del mismo.
		 * 
		 * Parámetros (todos obligatorios):
		 * 'nombre' - El nombre del equipo.
		 * 'id_coach' - El id de un tipo de usuario Coach, en la base de datos.
		 * 'logotipo' - Una archivo de una imagen del logotipo del equipo.
		 * 
		 * Se le cambia el tamaño a la imagen antes de guardar en la base de datos, para ahorrar espacio.
		 * 
		 * Hecho por Argüello Tello.
		 */
		$conexion = $db->getConnection();
		if ($conexion != null) {
			$consulta = $conexion->prepare('SELECT ID_EQUIPO FROM equipos WHERE NOMBRE_EQUIPO like ?');
			$consulta->bind_param("s", $_POST['nombre']);

			if ($consulta->execute()) {
				$res = $consulta->get_result();
				$consulta->close();

				if ($res->num_rows == 0) {
					//$comprobacion nos indica si el archivo es una verdadera imagen.
					$comprobacion = getimagesize($_FILES["logotipo"]["tmp_name"]);

					if ($comprobacion !== false) {
						//Cargamos la imagen del logo original.
						$fp = fopen($_FILES['logotipo']['tmp_name'], 'r+');
						$logotipo_original = imagecreatefromstring(fread($fp, filesize($_FILES['logotipo']['tmp_name'])));
						fclose($fp);

						//Calculamos el ancho y alto para una versión de la imagen achicada.
						$alto = 150;
						$ancho = imagesx($logotipo_original) / imagesy($logotipo_original) * $alto;

						//Achicamos la imagen.
						$logotipo_achicado = imagecreatetruecolor($ancho, $alto);
						imagecopyresampled($logotipo_achicado, $logotipo_original, 0, 0, 0, 0, $ancho, $alto, imagesx($logotipo_original), imagesy($logotipo_original));

						//Gurardamos la imagen achicada en un archivo y la volvemos a abrir en $logotipo_final.
						imagepng($logotipo_achicado, $_POST["nombre"] . ".png");

						//Ya que tenemos lista la imagen final, guardamos en la base de datos.
						/* $null = NULL;
						  $consulta = $conexion->prepare('INSERT INTO equipos (ID_EQUIPO, ID_COACH, NOMBRE_EQUIPO, LOGOTIPO_EQUIPO) VALUES (0, ?, ?, ?)');
						  $consulta->bind_param("isb", $_POST['id_coach'], $_POST['nombre'], $null);
						  //En la tercera posición (la #2), cargamos el archivo.
						  $consulta->send_long_data(2, file_get_contents($_POST["nombre"] . ".png")); */

						$consulta = $conexion->prepare("INSERT INTO equipos (ID_EQUIPO, ID_COACH, NOMBRE_EQUIPO, LOGOTIPO_EQUIPO) VALUES (0, ?, ?, '" . addslashes(file_get_contents($_POST["nombre"] . ".png")) . "')");
						$consulta->bind_param("is", $_POST['id_coach'], $_POST['nombre']);

						if ($consulta->execute()) {
							echo "ok";
						} else {
							echo "Error al insertar en la base de datos.";
						}

						//Eliminamos la copia de la imagen achicada en nuestro servidor.
						unlink($_POST["nombre"] . ".png");
						$consulta->close();
					} else {
						echo "El logotipo que mandó no es una imagen.";
					}
				} else {
					echo 'Ya existe un equipo con el mismo nombre.';
				}
			} else {
				$consulta->close();
			}
		} else {
			echo 'Error al conectar con la base de datos.';
		}

		break;
	case "get_equipos":
		/**
		 * Devuelve los equipos es un arreglo bidimensional donde cada fila corresponde a un equipo
		 * y las columnas son: ID_EQUIPO, NOMBRE_EQUIPO y LOGOTIPO_EQUIPO.
		 * 
		 * Si un admin está logueado se devuelven todos los equipo.
		 * Si el coach está logueado, sólo de devuelven los equipos que le pertenecen.
		 * 
		 * En caso de haber error, se devolverá un arreglo con un sólo elemento 'error',
		 * que contiene la explicación del mismo.
		 * 
		 * Hecho por Argüello Tello.
		 */
		$conexion = $db->getConnection();
		$equipos = array();
		if ($conexion != null) {
			if ($_SESSION["TIPO_USUARIO"] == "ADMINISTRADOR") {
				$query = "SELECT * FROM equipos";
			} else if ($_SESSION["TIPO_USUARIO"] == "COACH") {
				$query = "SELECT * FROM equipos WHERE ID_COACH = " . $_SESSION["ID_USUARIO"];
			}

			if ($res = $conexion->query($query)) {
				while ($fila = $res->fetch_assoc()) {
					array_push($equipos, array($fila["ID_EQUIPO"], $fila["NOMBRE_EQUIPO"], base64_encode($fila["LOGOTIPO_EQUIPO"])));
				}
			} else {
				$equipos['error'] = "Error al hacer la consulta en la base de datos.";
			}
		} else {
			$equipos['error'] = "Error al conectar con la base de datos.";
		}

		echo json_encode($equipos);
		break;
	case "get_equipo":
		/*
		 * Devuelve un arreglo con la infomación de un equipo, cuyo id se recibe en 'id_equipo':
		 * id de su coach, nombre y apellidos del coach, nombre del equipo y logotipo.
		 * Los índices del arreglo corresponde a los de la base de datos.
		 * 
		 * En caso de haber error, se devuelve un arreglo con un sólo elemento 'error',
		 * que contiene la explicación del mismo.
		 * 
		 * Hecho por Argüello Tello.
		 */
		$conexion = $db->getConnection();
		$equipo = array();
		if ($conexion != null) {
			if (isset($_POST['get_id_coach']) || isset($_POST['get_nombre_equipo']) || isset($_POST['get_nombre_coach']) || isset($_POST['get_logotipo_equipo'])) {
				$datosASeleccionar = "";

				if (isset($_POST['get_id_coach']))
					if (boolval($_POST['get_id_coach']))
						$datosASeleccionar .= "ID_COACH";

				if (isset($_POST['get_nombre_equipo']))
					if (boolval($_POST['get_nombre_equipo']))
						$datosASeleccionar .= (empty($datosASeleccionar) ? "" : ", ") . "NOMBRE_EQUIPO";

				if (isset($_POST['get_nombre_coach']))
					if (boolval($_POST['get_nombre_coach']))
						$datosASeleccionar .= (empty($datosASeleccionar) ? "" : ", ") . "NOMBRE, APELLIDO_PATERNO, APELLIDO_MATERNO";

				if (isset($_POST['get_logotipo_equipo']))
					if (boolval($_POST['get_logotipo_equipo']))
						$datosASeleccionar .= (empty($datosASeleccionar) ? "" : ", ") . "LOGOTIPO_EQUIPO";

				if (empty($datosASeleccionar)) {
					$datosASeleccionar .= "*";
					$_POST['get_id_coach'] = $_POST['get_nombre_equipo'] = $_POST['get_nombre_coach'] = $_POST['get_logotipo_equipo'] = true;
				}

				if ($_SESSION["TIPO_USUARIO"] == "ADMINISTRADOR") {
					$consulta = $conexion->prepare("SELECT " . $datosASeleccionar . " FROM equipos INNER JOIN usuarios ON equipos.ID_COACH = usuarios.ID_USUARIO WHERE ID_EQUIPO = ?");
				} else if ($_SESSION["TIPO_USUARIO"] == "COACH") {
					$consulta = $conexion->prepare("SELECT " . $datosASeleccionar . " FROM equipos INNER JOIN usuarios ON equipos.ID_COACH = usuarios.ID_USUARIO WHERE ID_EQUIPO = ? AND ID_COACH =" . $_SESSION["ID_USUARIO"]);
				} else {
					die;
				}
				$consulta->bind_param("i", $_POST['id_equipo']);

				if ($consulta->execute()) {
					$res = $consulta->get_result();

					if ($res->num_rows == 1) {
						$fila = $res->fetch_assoc();

						if (isset($_POST['get_id_coach']))
							if (boolval($_POST['get_id_coach']))
								$equipo["ID_COACH"] = $fila["ID_COACH"];

						if (isset($_POST['get_nombre_equipo']))
							if (boolval($_POST['get_nombre_equipo']))
								$equipo["NOMBRE_EQUIPO"] = $fila["NOMBRE_EQUIPO"];

						if (isset($_POST['get_nombre_coach']))
							if (boolval($_POST['get_nombre_coach']))
								$equipo["NOMBRE_COACH"] = $fila["APELLIDO_PATERNO"] . " " . $fila["APELLIDO_MATERNO"] . " " . $fila["NOMBRE"];

						if (isset($_POST['get_logotipo_equipo']))
							if (boolval($_POST['get_logotipo_equipo']))
								$equipo["LOGOTIPO_EQUIPO"] = base64_encode($fila["LOGOTIPO_EQUIPO"]);
					} else {
						$equipo["error"] = "El equipo ya no existe o no tiene permisos para verlo.";
					}
				} else {
					$equipo["error"] = "Error al hacer la consulta en la base de datos.";
				}

				$consulta->close();
			}
		} else {
			$equipo['error'] = "Error al conectar con la base de datos.";
		}

		if (isset($_POST['get_id_y_categoria_rosters'])) {
			if (boolval($_POST['get_id_y_categoria_rosters'])) {
				if ($_SESSION["TIPO_USUARIO"] == "ADMINISTRADOR") {
					$consulta = $conexion->prepare("SELECT ID_ROSTER, CATEGORIA FROM rosters WHERE ID_EQUIPO = ?");
				} else if ($_SESSION["TIPO_USUARIO"] == "COACH") {
					$consulta = $conexion->prepare("SELECT ID_ROSTER, CATEGORIA FROM rosters INNER JOIN equipos ON rosters.ID_EQUIPO = equipos.ID_EQUIPO WHERE rosters.ID_EQUIPO = ? AND ID_COACH =" . $_SESSION["ID_USUARIO"]);
				} else {
					die;
				}
				$consulta->bind_param("i", $_POST['id_equipo']);

				if ($consulta->execute()) {
					$res = $consulta->get_result();
					$equipo["id_y_categoria_rosters"] = array();

					while ($fila = $res->fetch_assoc()) {
						array_push($equipo["id_y_categoria_rosters"], array($fila["ID_ROSTER"], $fila["CATEGORIA"]));
					}
				} else {
					$equipo["error"] = "Error al hacer la consulta en la base de datos.";
				}

				$consulta->close();
			}
		}

		echo json_encode($equipo);
		break;
	case "editar_equipo":
		/**
		 * Permite editar un equipo.
		 * "id_equipo" es un parámetro obligatorio, que indica el id del equipo a editar.
		 * 
		 * "id_coach", "nombre" y "logotipo", son atributos opcionales, mándelos si desea editar los campos.
		 * Estos parámetros corresponden al nuevo valor.
		 * 
		 * Hecho por Argüello Tello.
		 */
		$conexion = $db->getConnection();
		if ($conexion != null && ($_SESSION["TIPO_USUARIO"] == "ADMINISTRADOR" || $_SESSION["TIPO_USUARIO"] == "COACH")) {
			$hay_permiso = true;

			//Si el usuario es un coach, debemos corroborar que tiene acceso al equipo.
			if ($_SESSION["TIPO_USUARIO"] == "COACH") {
				$consulta = $conexion->prepare('SELECT ID_EQUIPO from equipos WHERE ID_EQUIPO = ? AND ID_COACH = ?');
				$consulta->bind_param("ii", $_POST["id_equipo"], $_SESSION["ID_USUARIO"]);

				if ($consulta->execute()) {
					$res = $consulta->get_result();

					if ($res->num_rows != 1) {
						$hay_permiso = false;
					}
				} else {
					$hay_permiso = false;
				}
				$consulta->close();
			}

			if ($hay_permiso) {
				//Iniciamos una transacción.
				$conexion->autocommit(FALSE);
				//$conexion->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

				$cambios_hechos = true;

				if (isset($_POST["nombre"])) {
					$consulta = $conexion->prepare('SELECT ID_EQUIPO FROM equipos WHERE NOMBRE_EQUIPO like ?');
					$consulta->bind_param("s", $_POST['nombre']);
					if ($consulta->execute()) {
						$res = $consulta->get_result();

						if ($res->num_rows == 0) {
							$consulta = $conexion->prepare('UPDATE equipos SET NOMBRE_EQUIPO = ? WHERE ID_EQUIPO = ?');
							$consulta->bind_param("si", $_POST["nombre"], $_POST["id_equipo"]);
							if (!$consulta->execute()) {
								$cambios_hechos = false;
							}
							$consulta->close();
						} else {
							echo "Ya existe un existe un equipo con ese nombre.\n";
							$cambios_hechos = false;
						}
					} else {
						$cambios_hechos = false;
					}
				}

				if (isset($_POST["id_coach"])) {
					$consulta = $conexion->prepare('UPDATE equipos SET ID_COACH = ? WHERE ID_EQUIPO = ?');
					$consulta->bind_param("ii", $_POST["id_coach"], $_POST["id_equipo"]);
					if (!$consulta->execute()) {
						$cambios_hechos = false;
					}
					$consulta->close();
				}

				if (!empty($_FILES['logotipo']['name'])) {
					$nombre_archivo_tmp = uniqid() . ".png";
					//Cargamos la imagen del logo original.
					$fp = fopen($_FILES['logotipo']['tmp_name'], 'r+');
					$logotipo_original = imagecreatefromstring(fread($fp, filesize($_FILES['logotipo']['tmp_name'])));
					fclose($fp);

					//Calculamos el ancho y alto para una versión de la imagen achicada.
					$alto = 150;
					$ancho = imagesx($logotipo_original) / imagesy($logotipo_original) * $alto;

					//Achicamos la imagen.
					$logotipo_achicado = imagecreatetruecolor($ancho, $alto);
					imagecopyresampled($logotipo_achicado, $logotipo_original, 0, 0, 0, 0, $ancho, $alto, imagesx($logotipo_original), imagesy($logotipo_original));

					//Gurardamos la imagen achicada en un archivo y la volvemos a abrir en $logotipo_final.
					imagepng($logotipo_achicado, $nombre_archivo_tmp);

					//Ya que tenemos lista la imagen final, guardamos en la base de datos.
					/* $null = NULL;
					  $consulta = $conexion->prepare('UPDATE equipos SET LOGOTIPO_EQUIPO = ? WHERE ID_EQUIPO = ?');
					  $consulta->bind_param("bi", $null, $_POST["id_equipo"]);
					  $consulta->send_long_data(0, file_get_contents($nombre_archivo_tmp)); */
					$consulta = $conexion->prepare("UPDATE equipos SET LOGOTIPO_EQUIPO = '" . addslashes(file_get_contents($nombre_archivo_tmp)) . "' WHERE ID_EQUIPO = ?");
					$consulta->bind_param("i", $_POST["id_equipo"]);
					if (!$consulta->execute()) {
						$cambios_hechos = false;
					}
					//Eliminamos la copia de la imagen achicada en nuestro servidor.
					unlink($nombre_archivo_tmp);
					$consulta->close();
				}

				//Terminamos la transacción.
				if ($cambios_hechos) {
					if ($conexion->commit()) {
						echo "ok";
					} else {
						echo "Falló la consignación de la transacción.";
					}
				} else {
					$conexion->rollback();
					echo "Error al cambiar uno de los datos del equipo.";
				}
				$conexion->autocommit(TRUE);
			} else {
				echo "No tiene permiso para editar al equipo.";
			}
		} else {
			if ($conexion == null) {
				echo "Error al conectarse a la base de datos.";
			} else {
				echo "Tipo de usuario erróneo.";
			}
		}
		break;
	case "get_categorias_disponibles":
		/**
		 * Permite saber de un equipo, qué categorías están libres para crearles un roster.
		 * 
		 * Recibe el id del equipo ("id_equipo").
		 * Devuelve un arreglo unidimensional JSON con las categorías.
		 */
		$conexion = $db->getConnection();
		$categorias = array();
		if ($conexion != null && ($_SESSION["TIPO_USUARIO"] == "ADMINISTRADOR" || $_SESSION["TIPO_USUARIO"] == "COACH")) {
			$hay_permiso = true;

			//Si el usuario es un coach, debemos corroborar que tiene acceso al equipo.
			if ($_SESSION["TIPO_USUARIO"] == "COACH") {
				$consulta = $conexion->prepare('SELECT ID_EQUIPO from equipos WHERE ID_EQUIPO = ? AND ID_COACH = ?');
				$consulta->bind_param("ii", $_POST["id_equipo"], $_SESSION["ID_USUARIO"]);

				if ($consulta->execute()) {
					$res = $consulta->get_result();

					if ($res->num_rows != 1) {
						$hay_permiso = false;
					}
				} else {
					$hay_permiso = false;
				}
				$consulta->close();
			}

			if ($hay_permiso) {
				$posibles_categorias = array("VARONIL", "FEMENIL", "MIXTO", "HEAVY", "RABBIT", "MAS DE 40");
				$consulta = $conexion->prepare('SELECT ID_ROSTER FROM rosters WHERE ID_EQUIPO = ? AND CATEGORIA = ?');

				foreach ($posibles_categorias as $cat_actual) {
					$consulta->bind_param("is", $_POST["id_equipo"], $cat_actual);
					if ($consulta->execute()) {
						$res = $consulta->get_result();
						if ($res->num_rows == 0) {
							array_push($categorias, $cat_actual);
						}
					} else {
						$categorias["error"] = "Error al consultar la base de datos.";
						break;
					}
				}

				$consulta->close();
			} else {
				$categorias["error"] = "Usted ya no es propietario del equipo.";
			}
		} else {
			$categorias["error"] = "No tiene permisos.";
		}

		if (empty($categorias)) {
			$categorias["error"] = "El equipo ya tiene rosters en todas las categorías.";
		}

		echo json_encode($categorias);
		break;
	case "buscar_jugadores":
		/* Permite buscar los jugadores según el apellido parterno y una categoría.
		 * 
		 * Recibe:
		 * - criterio: Las primeras letras del apellido paterno. Si se coloca "per", se
		 *   devolveran la gente apellidada "Pérez", "Perilla", "Peralda", etc.
		 * - categoría: una categoría (VARONIL, FEMENIL, etc).
		 * 
		 * Devuelve un arreglo bidimensional donde cada fila corresponde a un jugador, y las columnas (sin) llaves son:
		 * [0] - El id del usuario (tipo jugador).
		 * [1] - Su correo electrónico.
		 * [2] - Su nombre completo.
		 */
		$conexion = $db->getConnection();
		$jugadores = array();
		if ($conexion != null) {
			$condicion = obtener_condiciones_de_categoria($_POST["categoria"]);

			if ($condicion != null) {
				//Creamos nuestra consulta preparada.
				$consulta = $conexion->prepare('SELECT ID_USUARIO, CORREO, NOMBRE, APELLIDO_PATERNO, APELLIDO_MATERNO FROM usuarios WHERE TIPO_USUARIO = \'JUGADOR\' AND APELLIDO_PATERNO like ? ' . $condicion);
				$consulta->bind_param("s", $_POST['criterio']);

				$_POST['criterio'] = str_replace(array('%', '_'), '', $_POST['criterio']);
				$_POST['criterio'] .= "%";

				if ($consulta->execute()) {
					$res = $consulta->get_result();

					while ($fila = $res->fetch_assoc()) {
						array_push($jugadores, array($fila["ID_USUARIO"], $fila["CORREO"], $fila["APELLIDO_PATERNO"] . " " . $fila["APELLIDO_MATERNO"] . " " . $fila["NOMBRE"]));
					}
				}

				$consulta->close();
			}
		}

		//Encriptamos $coaches como un JSON y lo enviamos como respuesta.
		echo json_encode($jugadores);
		break;
	case "crear_roster":
		/*
		 * Permite crear un roster.
		 * 
		 * Recibe: "id_equipo", "categoria", "jugadores" (un arreglo unidimensional JSON con los id's de los jugadores miembros).
		 * 
		 * Devuelve "ok" en caso de éxito, un string explicativo en caso de error.
		 */
		$jugadores = json_decode($_POST['jugadores'], true);

		if (count($jugadores) < constant("MIEMBROS_MINIMOS_DE_ROSTER")) {
			echo "El roster debe tener " . constant("MIEMBROS_MINIMOS_DE_ROSTER") . " jugadores como mínimo.";
			break;
		} else if (count($jugadores) > constant("MIEMBROS_MAXIMOS_DE_ROSTER")) {
			echo "Sólo se permiten " . constant("MIEMBROS_MAXIMOS_DE_ROSTER") . " jugadores como máximo.";
			break;
		}

		$conexion = $db->getConnection();
		if ($conexion != null && ($_SESSION["TIPO_USUARIO"] == "ADMINISTRADOR" || $_SESSION["TIPO_USUARIO"] == "COACH")) {
			$hay_permiso = true;

			//Si el usuario es un coach, debemos corroborar que tiene acceso al equipo.
			if ($_SESSION["TIPO_USUARIO"] == "COACH") {
				$consulta = $conexion->prepare('SELECT ID_EQUIPO from equipos WHERE ID_EQUIPO = ? AND ID_COACH = ?');
				$consulta->bind_param("ii", $_POST["id_equipo"], $_SESSION["ID_USUARIO"]);

				if ($consulta->execute()) {
					$res = $consulta->get_result();

					if ($res->num_rows != 1) {
						$hay_permiso = false;
					}
				} else {
					$hay_permiso = false;
				}
				$consulta->close();
			}

			if ($hay_permiso) {
				//Iniciamos una transacción.
				$conexion->autocommit(FALSE);
				//$conexion->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

				$cambios_hechos = true;

				$consulta = $conexion->prepare("SELECT ID_ROSTER FROM rosters WHERE ID_EQUIPO = ? AND CATEGORIA = ?");
				$consulta->bind_param("is", $_POST['id_equipo'], $_POST['categoria']);

				if ($consulta->execute()) {
					$res = $consulta->get_result();

					if ($res->num_rows == 0) {
						$consulta = $conexion->prepare("INSERT INTO rosters (ID_ROSTER, ID_CONVOCATORIA, ID_EQUIPO, CATEGORIA) VALUES (0, null, ?, ?)");
						$consulta->bind_param("is", $_POST['id_equipo'], $_POST['categoria']);

						if ($consulta->execute()) {
							$id_roster = $consulta->insert_id;
							$numero_jugador = 1;

							$consulta->close();
							$consulta = $conexion->prepare("INSERT INTO roster_jugador (ID_EQUIPOS_JUGADOR, ID_ROSTER, ID_JUGADOR, NUMERO) VALUES (0, ?, ?, ?)");

							foreach ($jugadores as $id_jugador) {
								$consulta->bind_param("iii", $id_roster, $id_jugador, $numero_jugador);
								if (!$consulta->execute()) {
									$cambios_hechos = false;
									echo "Error al relacionar los jugadores con el roster.";
									return;
								}
								$numero_jugador++;
							}

							$consulta->close();

							$consulta = $conexion->prepare("SELECT ID_USUARIO FROM usuarios WHERE TIPO_USUARIO = 'JUGADOR' AND ID_USUARIO = ? " . obtener_condiciones_de_categoria($_POST["categoria"]));
							foreach ($jugadores as $id_jugador) {
								$consulta->bind_param("i", $id_jugador);
								if ($consulta->execute()) {
									$res = $consulta->get_result();

									if ($res->num_rows == 0) {
										$cambios_hechos = false;
										echo "Al menos uno de los jugadores ya no existe o no corresponde a la categoría seleccionada.";
										break;
									}
								} else {
									$cambios_hechos = false;
									echo "Error comprobar la existencia de los jugadores en la base de datos.";
									break;
								}
							}
							$consulta->close();
						} else {
							$cambios_hechos = false;
							echo "Error al insertar roster.";
						}
					} else {
						$cambios_hechos = false;
						echo "El equipo ya tiene un roster con la categoría seleccionada.";
					}
				} else {
					$cambios_hechos = false;
					echo "Error al ver si ya existe un roster con la categoría seleccionada para el equipo.";
				}

				//Terminamos la transacción.
				if ($cambios_hechos) {
					if ($conexion->commit()) {
						echo "ok";
					} else {
						echo "Falló la consignación de la transacción.";
					}
				} else {
					$conexion->rollback();
				}
				$conexion->autocommit(TRUE);
			} else {
				echo "Usted ya no es propietario del equipo.";
			}
		} else {
			echo "No tiene permisos.";
		}

		break;
	case "editar_roster":
		/*
		 * Permite editar un roster..
		 * 
		 * Recibe:
		 * - "id_equipo", "id_roster" y "categoria". Información sobre el roster.
		 * - "jugadores". Un arreglo unidimensional con los id's de los miembros del roster actualizado.
		 * 
		 * Devuelve "ok" en caso de éxito, un string explicativo en caso de error.
		 */
		$miembros_roster_nuevo = json_decode($_POST['jugadores'], true);

		if (count($miembros_roster_nuevo) < constant("MIEMBROS_MINIMOS_DE_ROSTER")) {
			echo "El roster debe tener " . constant("MIEMBROS_MINIMOS_DE_ROSTER") . " jugadores como mínimo.";
			break;
		} else if (count($miembros_roster_nuevo) > constant("MIEMBROS_MAXIMOS_DE_ROSTER")) {
			echo "Sólo se permiten " . constant("MIEMBROS_MAXIMOS_DE_ROSTER") . " jugadores como máximo.";
			break;
		}

		$conexion = $db->getConnection();
		if ($conexion != null && ($_SESSION["TIPO_USUARIO"] == "ADMINISTRADOR" || $_SESSION["TIPO_USUARIO"] == "COACH")) {
			$hay_permiso = true;

			//Si el usuario es un coach, debemos corroborar que tiene acceso al equipo.
			if ($_SESSION["TIPO_USUARIO"] == "COACH") {
				$consulta = $conexion->prepare('SELECT ID_EQUIPO from equipos WHERE ID_EQUIPO = ? AND ID_COACH = ?');
				$consulta->bind_param("ii", $_POST["id_equipo"], $_SESSION["ID_USUARIO"]);

				if ($consulta->execute()) {
					$res = $consulta->get_result();

					if ($res->num_rows != 1) {
						$hay_permiso = false;
					}
				} else {
					$hay_permiso = false;
				}
				$consulta->close();
			}

			if ($hay_permiso) {
				//Iniciamos una transacción.
				$conexion->autocommit(FALSE);
				//$conexion->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

				$cambios_hechos = true;

				$consulta = $conexion->prepare("SELECT ID_ROSTER FROM rosters WHERE ID_ROSTER = ? AND ID_EQUIPO = ? AND CATEGORIA = ?");
				$consulta->bind_param("iis", $_POST['id_roster'], $_POST['id_equipo'], $_POST['categoria']);

				if ($consulta->execute()) {
					$res = $consulta->get_result();

					if ($res->num_rows == 1) {
						$consulta = $conexion->prepare("SELECT ID_JUGADOR FROM roster_jugador WHERE ID_ROSTER = ? ORDER BY NUMERO");
						$consulta->bind_param("i", $_POST['id_roster']);
						$miembros_roster_viejo = array();

						if ($consulta->execute()) {
							$res = $consulta->get_result();
							while ($fila = $res->fetch_assoc()) {
								array_push($miembros_roster_viejo, $fila["ID_JUGADOR"]);
							}

							if (count($miembros_roster_viejo) != 0) {
								if ($miembros_roster_nuevo === $miembros_roster_viejo) {
									$cambios_hechos = false;
									echo "No se ha detectado ningún cambio.";
								}

								if ($cambios_hechos) {
									$consulta = $conexion->prepare("SELECT ID_USUARIO FROM usuarios WHERE TIPO_USUARIO = 'JUGADOR' AND ID_USUARIO = ? " . obtener_condiciones_de_categoria($_POST["categoria"]));
									foreach ($miembros_roster_nuevo as $id_jugador) {
										$consulta->bind_param("i", $id_jugador);
										if ($consulta->execute()) {
											$res = $consulta->get_result();

											if ($res->num_rows == 0) {
												$cambios_hechos = false;
												echo "Al menos uno de los jugadores ya no existe o no corresponde a la categoría seleccionada.";
												break;
											}
										} else {
											$cambios_hechos = false;
											echo "Error comprobar la existencia de los jugadores en la base de datos.";
											break;
										}
									}
								}

								if ($cambios_hechos) {
									//Debemos actualizar los números de los jugadores que se mantienen en el roster.
									$consulta = $conexion->prepare("UPDATE roster_jugador SET NUMERO = ? WHERE ID_ROSTER = ? AND ID_JUGADOR = ?");
									foreach (array_intersect($miembros_roster_nuevo, $miembros_roster_viejo) as $id_jugador) {
										$numero_nuevo = array_search($id_jugador, $miembros_roster_nuevo) + 1;
										$consulta->bind_param("iii", $numero_nuevo, $_POST['id_roster'], $id_jugador);
										if (!$consulta->execute()) {
											$cambios_hechos = false;
											echo "Error al actualizar el número de un jugador que se mantiene en el roster.";
											break;
										}
									}
								}

								if ($cambios_hechos) {
									//Eliminamos a los jugadores que abandonan el roster.
									$consulta = $conexion->prepare("DELETE FROM roster_jugador WHERE ID_ROSTER = ? AND ID_JUGADOR = ?");
									foreach (array_diff($miembros_roster_viejo, $miembros_roster_nuevo) as $id_jugador) {
										$consulta->bind_param("ii", $_POST['id_roster'], $id_jugador);

										if (!$consulta->execute()) {
											$cambios_hechos = false;
											echo "Error al eliminar a los jugadores del roster.";
											break;
										}
									}
								}

								if ($cambios_hechos) {
									//Agregamos a los nuevos jugadores.
									$consulta = $conexion->prepare("INSERT INTO roster_jugador (ID_EQUIPOS_JUGADOR, ID_ROSTER, ID_JUGADOR, NUMERO) VALUES (0, ?, ?, ?)");
									foreach (array_diff($miembros_roster_nuevo, $miembros_roster_viejo) as $id_jugador) {
										$numero_nuevo = array_search($id_jugador, $miembros_roster_nuevo) + 1;
										$consulta->bind_param("iii", $_POST['id_roster'], $id_jugador, $numero_nuevo);

										if (!$consulta->execute()) {
											$cambios_hechos = false;
											echo "Error al ingresar a los jugadores al roster.";
											break;
										}
									}
								}
							} else {
								$cambios_hechos = false;
								echo "Error interno: el roster no tiene ningún jugador. Trate de eliminarlo y crearlo de nuevo.";
							}
						} else {
							$cambios_hechos = false;
							echo "Error al consultar los miembros acutales del roster.";
						}
					} else {
						$cambios_hechos = false;
						echo "El roster ya no existe.";
					}
				} else {
					$cambios_hechos = false;
					echo "Error comprobar la existencia y los permisos del roster.";
				}

				//Terminamos la transacción.
				if ($cambios_hechos) {
					if ($conexion->commit()) {
						echo "ok";
					} else {
						echo "Falló la consignación de la transacción.";
					}
				} else {
					$conexion->rollback();
				}
				$conexion->autocommit(TRUE);
			} else {
				echo "Usted ya no es propietario del equipo.";
			}
		} else {
			echo "No tiene permisos.";
		}

		break;
	case "eliminar_roster":
		/*
		 * Permite eliminar un roster:
		 * 
		 * Parámetros obligatorios: "id_roster", "id_equipo", "categoria".
		 * 
		 * Parámetro opcional: "confirmacion" (un booleano que vale "0" o "1" e indica si se quiere borrar el
		 * roster aunque esté participando en un torneo).
		 * 
		 * Devuelve:
		 * - "ok" en caso de éxito.
		 * - Un string explicativo en caso de error.
		 * - "?" si el roster está participando en un roster. En caso de salir, la consulta debe rehacerce
		 *   enviando el parámetro opcional.
		 */
		$conexion = $db->getConnection();
		if ($conexion != null && ($_SESSION["TIPO_USUARIO"] == "ADMINISTRADOR" || $_SESSION["TIPO_USUARIO"] == "COACH")) {
			$hay_permiso = true;

			//Si el usuario es un coach, debemos corroborar que tiene acceso al equipo.
			if ($_SESSION["TIPO_USUARIO"] == "COACH") {
				$consulta = $conexion->prepare('SELECT ID_EQUIPO from equipos WHERE ID_EQUIPO = ? AND ID_COACH = ?');
				$consulta->bind_param("ii", $_POST["id_equipo"], $_SESSION["ID_USUARIO"]);

				if ($consulta->execute()) {
					$res = $consulta->get_result();

					if ($res->num_rows != 1) {
						$hay_permiso = false;
					}
				} else {
					$hay_permiso = false;
				}
				$consulta->close();
			}

			if ($hay_permiso) {
				//Iniciamos una transacción.
				$conexion->autocommit(FALSE);
				//$conexion->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

				$cambios_hechos = true;

				$consulta = $conexion->prepare("SELECT ID_ROSTER FROM rosters WHERE ID_ROSTER = ? AND ID_EQUIPO = ? AND CATEGORIA = ?");
				$consulta->bind_param("iis", $_POST['id_roster'], $_POST['id_equipo'], $_POST['categoria']);

				if ($consulta->execute()) {
					$res = $consulta->get_result();

					if ($res->num_rows == 1) {
						$consulta = $conexion->prepare("SELECT ID_CONVOCATORIA FROM rosters WHERE ID_ROSTER = ?");
						$consulta->bind_param("i", $_POST['id_roster']);
						if ($consulta->execute()) {
							$res = $consulta->get_result();

							if ($res->num_rows != 0) {
								$fila = $res->fetch_assoc();
								if ($fila["ID_CONVOCATORIA"] !== null) {
									if (isset($_POST['confirmacion'])) {
										if (!boolval($_POST['confirmacion'])) {
											echo "Ha elegido no borrar el roster";
											$cambios_hechos = false;
										}
									} else {
										echo "?";
										$cambios_hechos = false;
									}
								}
							} else {
								$cambios_hechos = false;
							}
						} else {
							echo "Error al comprobar si el roster está participando en un torneo.";
							$cambios_hechos = false;
						}

						if ($cambios_hechos) {
							$consulta = $conexion->prepare("DELETE FROM participantes_no_registrados WHERE ID_ROSTER = ?");
							$consulta->bind_param("i", $_POST['id_roster']);
							if (!$consulta->execute()) {
								$cambios_hechos = false;
								echo "Error al eliminar los jugadores pendientes de registrar ligados a este roster.";
							}
						}

						if ($cambios_hechos) {
							$consulta = $conexion->prepare("DELETE FROM roster_jugador WHERE ID_ROSTER = ?");
							$consulta->bind_param("i", $_POST['id_roster']);
							if (!$consulta->execute()) {
								$cambios_hechos = false;
								echo "Error al desligar los jugadores del roster.";
							}
						}

						if ($cambios_hechos) {
							$consulta = $conexion->prepare("DELETE FROM cedulas WHERE ID_ROSTER = ?");
							$consulta->bind_param("i", $_POST['id_roster']);
							if (!$consulta->execute()) {
								$cambios_hechos = false;
								echo "Error al eliminar las cédulas del roster.";
							}
						}

						if ($cambios_hechos) {
							$consulta = $conexion->prepare("DELETE FROM rosters WHERE ID_ROSTER = ?");
							$consulta->bind_param("i", $_POST['id_roster']);
							if (!$consulta->execute()) {
								$cambios_hechos = false;
								echo "Error al elminiar el registro del roster.";
							}
						}
					} else {
						$cambios_hechos = false;
						echo "El roster ya no existe.";
					}
				} else {
					$cambios_hechos = false;
					echo "Error comprobar la existencia y los permisos del roster.";
				}

				//Terminamos la transacción.
				if ($cambios_hechos) {
					if ($conexion->commit()) {
						echo "ok";
					} else {
						echo "Falló la consignación de la transacción.";
					}
				} else {
					$conexion->rollback();
				}
				$conexion->autocommit(TRUE);
			} else {
				echo "Usted ya no es propietario del equipo.";
			}
		} else {
			echo "No tiene permisos.";
		}

		break;
	case "get_roster":
		/* Permite obtener la información de un roster.
		 * 
		 * Parámetros:
		 * - "id_roster" El id del roster a consultar en la tabla "rosters".
		 * - "id_equipo" El id del equipo (en la tabla "equipos") al que pertenece el roster (por seguridad).
		 * 
		 * Retorla la siguiente información en un JSON:
		 * - ID_CONVOCATORIA
		 * - CATEGORIA
		 * - MIEMBROS. Un arreglo bidimensional donde cada fila coresponde a un jugador miembro.
		 */
		$conexion = $db->getConnection();
		$roster = array();
		if ($conexion != null && ($_SESSION["TIPO_USUARIO"] == "ADMINISTRADOR" || $_SESSION["TIPO_USUARIO"] == "COACH")) {
			$hay_permiso = true;

			//Si el usuario es un coach, debemos corroborar que tiene acceso al equipo.
			if ($_SESSION["TIPO_USUARIO"] == "COACH") {
				$consulta = $conexion->prepare('SELECT ID_EQUIPO from equipos WHERE ID_EQUIPO = ? AND ID_COACH = ?');
				$consulta->bind_param("ii", $_POST["id_equipo"], $_SESSION["ID_USUARIO"]);

				if ($consulta->execute()) {
					$res = $consulta->get_result();

					if ($res->num_rows != 1) {
						$hay_permiso = false;
					}
				} else {
					$hay_permiso = false;
				}
				$consulta->close();
			}

			if ($hay_permiso) {
				$consulta = $conexion->prepare('SELECT * from rosters WHERE ID_ROSTER = ? AND ID_EQUIPO = ?');
				$consulta->bind_param("ii", $_POST["id_roster"], $_POST["id_equipo"]);

				if ($consulta->execute()) {
					$res = $consulta->get_result();

					if ($res->num_rows == 1) {
						$fila = $res->fetch_assoc();
						$roster["ID_CONVOCATORIA"] = $fila["ID_CONVOCATORIA"];
						$roster["CATEGORIA"] = $fila["CATEGORIA"];
						$roster["MIEMBROS"] = array();

						$consulta = $conexion->prepare("SELECT NOMBRE, APELLIDO_PATERNO, APELLIDO_MATERNO, CORREO, NUMERO, ID_JUGADOR FROM roster_jugador INNER JOIN usuarios on roster_jugador.ID_JUGADOR = usuarios.ID_USUARIO WHERE ID_ROSTER = ? ORDER BY NUMERO");
						$consulta->bind_param("i", $_POST["id_roster"]);

						if ($consulta->execute()) {
							$res = $consulta->get_result();
							while ($fila_jugador = $res->fetch_assoc()) {
								array_push($roster["MIEMBROS"], $fila_jugador);
							}
						} else {
							$roster["error"] = "Error al consultar los jugadores del roster.";
						}
					} else {
						$roster["error"] = "El roster ya no existe.";
					}
				} else {
					$roster["error"] = "Error al consultar los rosters.";
				}
			} else {
				$roster["error"] = "El equipo ya no existe o no tiene permiso al él.";
			}
		} else {
			$roster["error"] = "Su cuenta no tiene permiso.";
		}

		echo json_encode($roster);
		break;
	case "iniciar_cerrar_session":
		if (isset($_SESSION['ID_USUARIO'])) {
			$db->setQuery(sprintf("SELECT ID_USUARIO, TIPO_USUARIO FROM usuarios WHERE ID_USUARIO = " . $_SESSION['ID_USUARIO']));
			$resul = $db->GetRow();
			if ($_SESSION['ID_USUARIO'] == $resul['ID_USUARIO'] && $_SESSION['TIPO_USUARIO'] == $resul['TIPO_USUARIO']) {
				echo "ok";
			} else {
				echo "no";
			}
		} else {
			echo "no";
		}
		break;
	case "acceso_registrar_jugador":
		if (isset($_SESSION['ID_USUARIO']) && $_SESSION['TIPO_USUARIO'] == 'ADMINISTRADOR' || $_SESSION['TIPO_USUARIO'] == 'COACH') {
			$db->setQuery(sprintf("SELECT ID_USUARIO, TIPO_USUARIO FROM usuarios WHERE ID_USUARIO = " . $_SESSION['ID_USUARIO']));
			$resul = $db->GetRow();
			if ($_SESSION['ID_USUARIO'] == $resul['ID_USUARIO'] && $_SESSION['TIPO_USUARIO'] == $resul['TIPO_USUARIO']) {
				echo "ok";
			} else {
				echo "no";
			}
		} else {
			echo "no";
		}
		break;
	case "acceso_convocatoria":
		if (isset($_SESSION['ID_USUARIO']) && $_SESSION['TIPO_USUARIO'] == 'ADMINISTRADOR') {
			echo "ok";
		} else {
			echo "no";
		}
		break;
	case "acceso_torneo_coach":
		if (isset($_SESSION['ID_USUARIO']) && $_SESSION['TIPO_USUARIO'] == 'COACH') {
			$fecha_actual = date('Y-m-d');
			$db->setQuery("SELECT * FROM convocatoria WHERE FECHA_CIERRE_CONVOCATORIA >= '$fecha_actual'");
			$resultado = $db->GetResult();

			if (empty($resultado) || is_null($resultado)) {
				echo "no";
			} else {
				echo "ok";
			}
		} else {
			echo "no";
		}
		break;
	case "acceso_crear_noticias":
		if (isset($_SESSION['ID_USUARIO']) && $_SESSION['TIPO_USUARIO'] == 'ADMINISTRADOR' || $_SESSION['TIPO_USUARIO'] == 'FOTOGRAFO') {
			echo "ok";
		} else {
			echo "no";
		}
		break;
	case "acceso_asignacion_horarios":
		if (isset($_SESSION['ID_USUARIO']) && $_SESSION['TIPO_USUARIO'] == 'ADMINISTRADOR' || $_SESSION['TIPO_USUARIO'] == 'CAPTURISTA') {
			echo "ok";
		} else {
			echo "no";
		}


		break;
	case "acceso_select_registrar_jugador":
		if (isset($_SESSION['ID_USUARIO']) && $_SESSION['TIPO_USUARIO'] == 'ADMINISTRADOR') {
			echo "ok";
		} else {
			echo "no";
		}
		break;
	case "lista_coach":
		if (isset($_SESSION['ID_USUARIO']) && $_SESSION['TIPO_USUARIO'] == 'ADMINISTRADOR') {
			$sql = sprintf("SELECT * FROM usuarios WHERE TIPO_USUARIO = 'COACH' ");
			$db->setQuery($sql);
			$resultado = $db->GetResult();
			echo json_encode($resultado);
		}
		break;
	case "validar_registro_jugador":
		if (isset($_SESSION['ID_USUARIO']) && $_SESSION['TIPO_USUARIO'] == 'ADMINISTRADOR') {
			echo "ok";
		} else if (isset($_SESSION['ID_USUARIO']) && $_SESSION['TIPO_USUARIO'] == 'COACH') {
			echo "no";
		}
		break;
	case "coach_registra_jugador":
		$sql = sprintf("INSERT INTO usuarios(CORREO, PASSWORD, TIPO_USUARIO, ESTADO, NOMBRE, APELLIDO_PATERNO, APELLIDO_MATERNO, FECHA_NACIMIENTO, SEXO, TIPO_SANGRE, TELEFONO, FOTO_PERFIL, FACEBOOK, INSTAGRAM, TWITTER, ID_REGISTRADOR) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')", $_POST['correo'], $_POST['password'], $_POST['tipo_usuario'], $_POST['estado'], $_POST['nombre'], $_POST['apellido_paterno'], $_POST['apellido_materno'], $_POST['fecha_nacimiento'], $_POST['sexo'], $_POST['tipo_sangre'], $_POST['telefono'], $_POST['foto_perfil'], $_POST['facebook'], $_POST['instagram'], $_POST['twiter'], $_SESSION['ID_USUARIO']);
		$db->setQuery($sql);
		$db->ExecuteQuery();
		echo "ok";
		break;
	case "consultar_torneos_activos":
		$conexion = $db->getConnection();
		$sql = "SELECT *FROM convocatoria WHERE ESTADO = 'ACTIVO'";
		$resultado = $conexion->query($sql);
		while ($fila = $resultado->fetch_assoc()) {
			echo "<tr id='fila_" . $fila["ID_CONVOCATORIA"] . "'><td>" . $fila["NOMBRE_TORNEO"] . "</td><td>" .
			"<button class='btn-warning' onclick='abrir_pantalla_para_terminar_torneo(" . $fila["ID_CONVOCATORIA"] . ")'>terminar</button>";
		}
		break;
	case "modificar_torneo_rol_generado":
		$id = $_POST['id'];
		$conexion = $db->getConnection();
		$sql = "SELECT *FROM convocatoria WHERE ID_CONVOCATORIA = $id";
		if ($resultado = $conexion->query($sql)) {
			$fila = $resultado->fetch_assoc();
			$fecha_recuperada = $fila["FECHA_CIERRE_CONVOCATORIA"];
			$nuevafecha = strtotime('-1 day', strtotime($fecha_recuperada));
			$nuevafecha = date('Y-m-j', $nuevafecha);
			$sql = "UPDATE convocatoria SET FECHA_CIERRE_CONVOCATORIA = '$nuevafecha' WHERE ID_CONVOCATORIA = $id";
			if ($resultado = $conexion->query($sql)) {
				echo "ok";
			} else {
				echo "Error al modificar la fecha";
			}
		} else {
			echo "Error al recuperar convocatorias";
		}
		break;
	case "modificar_torneo_activo":
		$id = $_POST['id'];
		$conexion = $db->getConnection();
		$sql = "SELECT *FROM convocatoria WHERE ID_CONVOCATORIA = $id";
		if ($resultado = $conexion->query($sql)) {
			$fila = $resultado->fetch_assoc();
			$fecha_recuperada = $fila["ESTADO"];
			//$nuevafecha = strtotime ( '-1 day' , strtotime ($fecha_recuperada));
			//$nuevafecha = date( 'Y-m-j',$nuevafecha); 
			$sql = "UPDATE convocatoria SET ESTADO = 'ACTIVO' WHERE ID_CONVOCATORIA = $id";
			if ($resultado = $conexion->query($sql)) {
				echo "ok";
			} else {
				echo "Error al modificar la fecha";
			}
		} else {
			echo "Error al recuperar convocatorias";
		}
		break;
	case "terminar_torneo":
		$conexion = $db->getConnection();
		$conexion->autocommit(FALSE);
		//$conexion->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
		$id = $_POST['id'];
		$cambios_hechos = true;
		$id_equipo_ganador_torneo = array();
		$categoria_torneo;
		$imagen_torneo;

		$sql = "SELECT *FROM convocatoria WHERE ID_CONVOCATORIA = $id";
		if ($resultado = $conexion->query($sql)) {
			$fila = $resultado->fetch_assoc();
			$categoria_torneo = $fila["CATEGORIA"];
			$imagen_torneo = addslashes($fila["IMAGEN_ANUNCIO"]);
			$sql = "UPDATE convocatoria SET ESTADO = 'INACTIVO' WHERE ID_CONVOCATORIA = $id";
			$resultado = $conexion->query($sql);
			if ($resultado) {
				//echo "ok";                                
				$id_convocatoria = null;
				$sql = "UPDATE rosters SET ID_CONVOCATORIA = '$id_convocatoria'  WHERE ID_CONVOCATORIA = $id";
				if ($resultado = $conexion->query($sql)) {
					$sql = "SELECT *FROM estadisticas WHERE ID_CONVOCATORIA = $id ORDER BY PUNTOS_FAVOR DESC";
					if ($resultado = $conexion->query($sql)) {
						$cont = 0;
						while ($fila = $resultado->fetch_assoc()) {
							if ($cont == 3) {
								break;
							} else {
								$id_equipo_ganador = $fila["ID_EQUIPO"];
								$id_equipo_ganador_torneo[] = $fila["ID_EQUIPO"];
								$sql = "INSERT INTO ganadores VALUES(0,'$id','$id_equipo_ganador')";
								$cont++;
								if ($resultado2 = $conexion->query($sql)) {
									//$cambios_hechos = true;                       
								} else {
									$cambios_hechos = false;
									echo "No pude insertar ganadores";
								}
							}
						}
						$nombres_equipos = array();
						$contador = 0;
						foreach ($id_equipo_ganador_torneo as $valor) {
							$sql = "SELECT *FROM equipos WHERE ID_EQUIPO = $valor";
							if ($resultado = $conexion->query($sql)) {
								$fila = $resultado->fetch_assoc();
								$nombres_equipos[] = $fila["NOMBRE_EQUIPO"];
								$contador++;
								/* //$imagen = addslashes($fila["LOGOTIPO_EQUIPO"]);
								  $titulo_noticia = "Campeon de la liga ".$categoria_torneo;
								  $descripcion = "El equipo ".$fila["NOMBRE_EQUIPO"]." se alzo con el triunfo esta temporada";
								  $sql = "INSERT INTO noticias VALUES(0,'$imagen_torneo','$titulo_noticia','$descripcion')";
								  if($resultado = $conexion->query($sql)){
								  //$cambios_hechos = true;
								  }else{
								  $cambios_hechos = false;
								  echo "No pude insertar noticia";
								  } */
							} else {
								$cambios_hechos = false;
								echo "No pude leer equipos";
							}
						}
						if ($contador == 3) {
							$titulo_noticia = "Ganadores categoria " . $categoria_torneo;
							$descripcion = "Primer lugar para " . $nombres_equipos[0] . "\nSegundo lugar para " . $nombres_equipos[1] . "\nTercer lugar para " . $nombres_equipos[2];
							$sql = "INSERT INTO noticias VALUES(0,'$imagen_torneo','$titulo_noticia','$descripcion')";
							if ($resultado = $conexion->query($sql)) {
								
							} else {
								$cambios_hechos = false;
								echo "No pude insertar noticia";
							}
						} else {
							$cambios_hechos = false;
							echo "El numero de equipos no es valido -> ";
						}
					} else {
						$cambios_hechos = false;
						echo "No pude leer estadisticas";
					}
				} else {
					$cambios_hechos = false;
					echo "No pude modificar roster";
				}
				//$resultado = $conexion->query($sql);  
			} else {
				//echo "Error";
				$cambios_hechos = false;
			}
		} else {
			$cambios_hechos = false;
		}
		if ($cambios_hechos) {
			if ($conexion->commit()) {
				echo "ok";
			} else {
				echo "Falló la consignación de la transacción.";
			}
		} else {
			$conexion->rollback();
			echo "Error en la transaccion";
		}
		$conexion->autocommit(TRUE);
		break;
	case "insertar_convocatoria":
		$id = 0;
		$nombre_torneo = $_POST['nombre'];
		$fecha_cierre_convocatoria = $_POST['fecha_cierre'];
		$fecha_inicio_torneo = $_POST['fecha_inicio'];
		$fecha_fin_torneo = $_POST['fecha_fin'];
		$categoria = $_POST['categoria'];
		$imagen = addslashes(file_get_contents($_FILES['imagen']['tmp_name']));
		$estado = $_POST['estado'];
		//cambiar a formato fecha
		$fecha_inicio_torneo = strtotime($fecha_inicio_torneo);
		$fecha_inicio_torneo = date("Y-m-d", $fecha_inicio_torneo);
		$fecha_cierre_convocatoria = strtotime($fecha_cierre_convocatoria);
		$fecha_cierre_convocatoria = date("Y-m-d", $fecha_cierre_convocatoria);
		$fecha_fin_torneo = strtotime($fecha_fin_torneo);
		$fecha_fin_torneo = date("Y-m-d", $fecha_fin_torneo);
		/////////////////////////////////////////////////////////////
		/////////////////////////////////////////////////////////////
		$validar_fecha_cierre = explode('/', $_POST['fecha_cierre']);
		$validar_fecha_inicio = explode('/', $_POST['fecha_inicio']);
		$validar_fecha_fin = explode('/', $_POST['fecha_fin']);
		if ($categoria == "VARONIL" || $categoria == "FEMENIL" || $categoria == "HEAVY" || $categoria == "MIXTO" || $categoria == "RABBIT" || $categoria == "MAS DE 40") {
			if (count($validar_fecha_cierre) == 3 && count($validar_fecha_inicio) == 3 && count($validar_fecha_fin) == 3) {
				if ($validar_fecha_cierre[0] != "" && $validar_fecha_cierre[1] != "" && $validar_fecha_cierre[2] != "" && $validar_fecha_inicio[0] != "" && $validar_fecha_inicio[1] != "" && $validar_fecha_inicio[2] != "" && $validar_fecha_fin[0] != "" && $validar_fecha_fin[0] != "" && $validar_fecha_fin[2] != "") {
					try {
						if (checkdate($validar_fecha_cierre[0], $validar_fecha_cierre[1], $validar_fecha_cierre[2]) == true && checkdate($validar_fecha_inicio[0], $validar_fecha_inicio[1], $validar_fecha_inicio[2]) == true && checkdate($validar_fecha_fin[0], $validar_fecha_fin[1], $validar_fecha_fin[2]) == true) {
							////////////////////////////////////////////////////////////
							$size = $_FILES['imagen']['size'];
							if ($size >= 16777215) {
								echo "El tamaño de la imagen que intenta insertar es muy grande pruebe con otra";
							} else {
								$sql = sprintf("INSERT INTO convocatoria(ID_CONVOCATORIA,NOMBRE_TORNEO,FECHA_CIERRE_CONVOCATORIA,FECHA_INICIO_PARTIDO,FECHA_FIN_PARTIDO,CATEGORIA,IMAGEN_ANUNCIO,ESTADO) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s')", $id, $nombre_torneo, $fecha_cierre_convocatoria, $fecha_inicio_torneo, $fecha_fin_torneo, $categoria, $imagen, $estado);
								$db->setQuery($sql);
								$db->ExecuteQuery();
								echo "ok";
							}
						} else {
							echo "Ingrese una fecha valida";
						}
					} catch (Exception $e) {
						echo "Ingresa un formato de fecha valido";
					}
				} else {
					echo "Ingresa un formato de fecha valido";
				}
			} else {
				echo "Ingresa un formato de fecha valido";
			}
		} else {
			echo "Ingresa una categoria validad";
		}
		break;
	case "insertar_noticia":
		$id = 0;
		$titulo_noticia = $_POST['titulo_noticia'];
		$descripcion_noticia = $_POST['descripcion'];
		$imagen_noticia = addslashes(file_get_contents($_FILES['imagen_noticia']['tmp_name']));
		$size = $_FILES['imagen_noticia']['size'];
		if ($size >= 16777215) {
			echo "El tamaño de la imagen que intenta insetar es muy grande";
		} else {
			$salida = null;
			$tipo = pathinfo($_FILES['imagen_noticia']['name'], PATHINFO_EXTENSION);
			$temporal = $_FILES['imagen_noticia']['tmp_name'];
			if ($tipo == "jpg") {
				//cambiar dimension de la imagen
				$original = imagecreatefromjpeg($temporal);
				$ancho_original = imagesx($original);
				$alto_original = imagesy($original);
				$copia = imagecreatetruecolor(960, 640);
				imagecopyresampled($copia, $original, 0, 0, 0, 0, 960, 640, $ancho_original, $alto_original);
				imagejpeg($copia, 'temporal.jpg', 100);
				$salida = addslashes(file_get_contents("temporal.jpg"));

				$sql = sprintf("INSERT INTO noticias(ID_NOTICIAS,IMAGEN_NOTICIA,TITULO,NOTICIA) VALUES ('%s','%s','%s','%s')", $id, $salida, $titulo_noticia, $descripcion_noticia);
				$db->setQuery($sql);
				$db->ExecuteQuery();
				echo "ok";
			} else if ($tipo == "png") {
				$original = imagecreatefrompng($temporal);
				$ancho_original = imagesx($original);
				$alto_original = imagesy($original);
				$copia = imagecreatetruecolor(960, 640);
				imagecopyresampled($copia, $original, 0, 0, 0, 0, 960, 640, $ancho_original, $alto_original);
				imagepng($copia, 'temporal.png');
				$salida = addslashes(file_get_contents("temporal.png"));

				$sql = sprintf("INSERT INTO noticias(ID_NOTICIAS,IMAGEN_NOTICIA,TITULO,NOTICIA) VALUES ('%s','%s','%s','%s')", $id, $salida, $titulo_noticia, $descripcion_noticia);
				$db->setQuery($sql);
				$db->ExecuteQuery();
				echo "ok";
			} else {
				echo "Ingresa un formato de imagen valido";
			}
			///////////////////////////////////////////////////////                                
		}
		break;
	case "recuperar_nocicias":
		$noticias = array();
		$bandera = true;
		$conexion = $db->getConnection();
		$sql = "SELECT * FROM noticias";
		$resultado = $conexion->query($sql);
		while ($fila = $resultado->fetch_assoc()) {
			$noticias["IMAGEN_NOTICIA"] = base64_encode($fila["IMAGEN_NOTICIA"]);
			echo "<li>" .
			"<div class='blog-img'><img src='data:image/png;base64," . $noticias["IMAGEN_NOTICIA"] . "'class='img-responsive' alt=''/>" .
			"</div><div class='blog-info'><a class='news' href='#'>" . $fila["TITULO"] . "</a>" .
			"<p>" . $fila["NOTICIA"] . "</p>" .
			"<div class='bog_post_info infoPost'><span class='datePost'><a href='#' class='post_date'>Sep 30, 2017</a></span>" .
			"<span class='commentPost'><a class='icon-comment-1' title='Comments - 2' href='#'><i class='glyphicon glyphicon-comment'></i>2</a></span>" .
			"<span class='likePost'><i class='glyphicon glyphicon-heart'></i><a class='icon-heart' title='Likes - 4' href='#'>4</a></span>" .
			"<div class='clearfix'></div></div></div></li>";
		}
		break;
	case "administrador_registra_jugador":
		$sql = sprintf("INSERT INTO usuarios(CORREO, PASSWORD, TIPO_USUARIO, ESTADO, NOMBRE, APELLIDO_PATERNO, APELLIDO_MATERNO, FECHA_NACIMIENTO, SEXO, TIPO_SANGRE, TELEFONO, FOTO_PERFIL, FACEBOOK, INSTAGRAM, TWITTER, ID_REGISTRADOR) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')", $_POST['correo'], $_POST['password'], $_POST['tipo_usuario'], $_POST['estado'], $_POST['nombre'], $_POST['apellido_paterno'], $_POST['apellido_materno'], $_POST['fecha_nacimiento'], $_POST['sexo'], $_POST['tipo_sangre'], $_POST['telefono'], $_POST['foto_perfil'], $_POST['facebook'], $_POST['instagram'], $_POST['twiter'], $_POST['id_registrador']);
		$db->setQuery($sql);
		$db->ExecuteQuery();
		echo "ok";
		break;
	case "eliminar_equipo":
		if (isset($_SESSION['ID_USUARIO']) && $_SESSION['TIPO_USUARIO'] == 'ADMINISTRADOR' || $_SESSION['TIPO_USUARIO'] == 'COACH') {
			$db->setQuery("SELECT ID_USUARIO, TIPO_USUARIO FROM usuarios WHERE ID_USUARIO = " . $_SESSION['ID_USUARIO']);
			$resul = $db->GetRow();
			if ($_SESSION['ID_USUARIO'] == $resul['ID_USUARIO'] && $_SESSION['TIPO_USUARIO'] == $resul['TIPO_USUARIO']) {
				$permiso_eliminar_equipo = 1;
				if ($_SESSION['TIPO_USUARIO'] == 'COACH') {
					$db->setQuery("SELECT ID_COACH FROM equipos WHERE ID_EQUIPO =" . $_POST['id_equipo']);
					$resul = $db->GetRow();
					if ($_SESSION['ID_USUARIO'] == $resul['ID_COACH']) {
						$permiso_eliminar_equipo = 1;
					} else {
						$permiso_eliminar_equipo = 0;
					}
				}
				$db->setQuery(sprintf("SELECT ID_EQUIPO_1, ID_EQUIPO_2 FROM roles_juego INNER JOIN convocatoria ON roles_juego.ID_CONVOCATORIA = convocatoria.ID_CONVOCATORIA WHERE ID_EQUIPO_1 = %s OR ID_EQUIPO_2 = %s AND convocatoria.FECHA_FIN_PARTIDO < '%s'", $_POST['id_equipo'], $_POST['id_equipo'], date('Y-m-d')));
				$resultado = $db->GetResult();


				if ($permiso_eliminar_equipo == 1 && count($resultado) == 0) {
					echo count($resultado);
					$db->setQuery("DELETE ganadores FROM ganadores WHERE ID_EQUIPO_GANADOR = " . $_POST['id_equipo']);
					$db->ExecuteQuery();
					$db->setQuery("DELETE roster_jugador FROM roster_jugador INNER JOIN rosters ON roster_jugador.ID_ROSTER = rosters.ID_ROSTER WHERE rosters.ID_EQUIPO = " . $_POST['id_equipo']);
					$db->ExecuteQuery();
					$db->setQuery("DELETE participantes_roster FROM participantes_roster INNER JOIN rosters ON participantes_roster.ID_ROSTER = rosters.ID_ROSTER WHERE rosters.ID_EQUIPO = " . $_POST['id_equipo']);
					$db->ExecuteQuery();
					$db->setQuery("DELETE participantes_no_registrados FROM participantes_no_registrados INNER JOIN rosters ON participantes_no_registrados.ID_ROSTER = rosters.ID_ROSTER WHERE rosters.ID_EQUIPO = " . $_POST['id_equipo']);
					$db->ExecuteQuery();
					$db->setQuery("DELETE multimedia FROM multimedia INNER JOIN novedades_partidos ON multimedia.ID_NOVEDADES_PARTIDOS = novedades_partidos.ID_NOTICIAS INNER JOIN roles_juego ON novedades_partidos.ID_ROL_JUEGO = roles_juego.ID_ROL_JUEGO INNER JOIN cedulas ON roles_juego.ID_ROL_JUEGO = cedulas.ID_ROL_JUEGO INNER JOIN rosters ON cedulas.ID_ROSTER = rosters.ID_ROSTER INNER JOIN equipos ON rosters.ID_EQUIPO = equipos.ID_EQUIPO WHERE equipos.ID_EQUIPO = " . $_POST['id_equipo']);
					$db->ExecuteQuery();
					$db->setQuery("DELETE novedades_partidos FROM novedades_partidos INNER JOIN roles_juego ON novedades_partidos.ID_ROL_JUEGO = roles_juego.ID_ROL_JUEGO INNER JOIN cedulas ON roles_juego.ID_ROL_JUEGO = cedulas.ID_ROL_JUEGO INNER JOIN rosters ON cedulas.ID_ROSTER = rosters.ID_ROSTER INNER JOIN equipos ON rosters.ID_EQUIPO = equipos.ID_EQUIPO WHERE equipos.ID_EQUIPO = " . $_POST['id_equipo']);
					$db->ExecuteQuery();
					$db->setQuery("DELETE roles_juego FROM roles_juego INNER JOIN cedulas ON roles_juego.ID_ROL_JUEGO = cedulas.ID_ROL_JUEGO INNER JOIN rosters ON cedulas.ID_ROSTER = rosters.ID_ROSTER INNER JOIN equipos ON rosters.ID_EQUIPO = equipos.ID_EQUIPO WHERE equipos.ID_EQUIPO = " . $_POST['id_equipo']);
					$db->ExecuteQuery();
					$db->setQuery("DELETE cedulas FROM cedulas INNER JOIN rosters ON cedulas.ID_ROSTER = rosters.ID_ROSTER INNER JOIN equipos ON rosters.ID_EQUIPO = equipos.ID_EQUIPO WHERE equipos.ID_EQUIPO = " . $_POST['id_equipo']);
					$db->ExecuteQuery();
					$db->setQuery("DELETE rosters FROM rosters INNER JOIN equipos ON rosters.ID_EQUIPO = equipos.ID_EQUIPO WHERE equipos.ID_EQUIPO = " . $_POST['id_equipo']);
					$db->ExecuteQuery();
					$db->setQuery("DELETE equipos FROM equipos WHERE equipos.ID_EQUIPO = " . $_POST['id_equipo']);
					$db->ExecuteQuery();
					$db->setQuery("DELETE estadisticas FROM estadisticas WHERE estadisticas.ID_EQUIPO = " . $_POST['id_equipo']);
					$db->ExecuteQuery();
					echo "ok";
				} else {
					echo "no";
				}
			} else {
				echo "no tienes permisos";
			}
		} else {
			echo "no";
		}
		break;
	case "lista_convocatorias_inactivas":
		if (isset($_SESSION['ID_USUARIO'])) {
			$db->setQuery(sprintf("SELECT * FROM convocatoria WHERE FECHA_CIERRE_CONVOCATORIA < '%s' AND FECHA_FIN_PARTIDO >= '%s'", date('Y-m-d'), date('Y-m-d')));
			$resultado = $db->GetResult();
			$list_convocatoria = "<option value='-1'>Seleccione el torneo</option>";
			foreach ($resultado as $key => $convocatorias_roles) {
				$list_convocatoria .= "<option value='" . $convocatorias_roles['ID_CONVOCATORIA'] . "'>" . $convocatorias_roles['NOMBRE_TORNEO'] . "</option>";
			}
			echo $list_convocatoria;
		} else {
			echo "no";
		}
		break;
	case "roles_juegos_convocatoria_seleccionada":
		if (isset($_SESSION['ID_USUARIO'])) {
			if ($_SESSION['TIPO_USUARIO'] == 'ADMINISTRADOR' || $_SESSION['TIPO_USUARIO'] == 'CAPTURISTA') {
				$db->setQuery(sprintf("SELECT ID_USUARIO, TIPO_USUARIO FROM usuarios WHERE ID_USUARIO = " . $_SESSION['ID_USUARIO']));
				$resul = $db->GetRow();
				if ($_SESSION['ID_USUARIO'] == $resul['ID_USUARIO'] && $_SESSION['TIPO_USUARIO'] == $resul['TIPO_USUARIO']) {
					$usuario_permitido = "Generar cedula";
				} else {
					$usuario_permitido = "Ver cedula";
				}
			} else {
				$usuario_permitido = "Ver cedula";
			}
		} else {
			echo "no";
		}
		$db->setQuery(sprintf("SELECT * FROM roles_juego INNER JOIN convocatoria ON roles_juego.ID_CONVOCATORIA = convocatoria.ID_CONVOCATORIA WHERE convocatoria.FECHA_CIERRE_CONVOCATORIA < '%s' AND convocatoria.FECHA_FIN_PARTIDO >= '%s' AND convocatoria.ID_CONVOCATORIA = %s", date('Y-m-d'), date('Y-m-d'), $_POST['id_convocatoria']));
		$resultado = $db->GetResult();

		foreach ($resultado as $key => $roles) {
			$db->setQuery("SELECT NOMBRE_EQUIPO FROM equipos WHERE ID_EQUIPO = " . $roles['ID_EQUIPO_1']);
			$r1 = $db->GetRow();
			$resultado[$key]['NOMBRE_EQUIPO_1'] = $r1['NOMBRE_EQUIPO'];
			$db->setQuery("SELECT NOMBRE_EQUIPO FROM equipos WHERE ID_EQUIPO = " . $roles['ID_EQUIPO_2']);
			$r2 = $db->GetRow();
			$resultado[$key]['NOMBRE_EQUIPO_2'] = $r2['NOMBRE_EQUIPO'];
		}

		// relaciono los datos de los roles de juego y genero el codigo html para crear la tabla
		$roles_juego = "";
		foreach ($resultado as $key => $roles) {
			if (is_null($roles['GOLES_EQUIPO_1'])) {
				$goles_equipo_1 = 0;
			} else {
				$goles_equipo_1 = $roles['GOLES_EQUIPO_1'];
			}
			if (is_null($roles['GOLES_EQUIPO_2'])) {
				$goles_equipo_2 = 0;
			} else {
				$goles_equipo_2 = $roles['GOLES_EQUIPO_2'];
			}

			if (is_null($roles['ID_EQUIPO_GANADOR'])) {
				$equipo_ganador = "Por definir";
			} else if ($roles['ID_EQUIPO_GANADOR'] == $roles['ID_EQUIPO_1']) {
				$equipo_ganador = $roles['NOMBRE_EQUIPO_1'];
			} else {
				$equipo_ganador = $roles['NOMBRE_EQUIPO_2'];
			}
			$bloqueo = 'enabled';
			if ($roles['ID_EQUIPO_1'] == 0) {
				$roles['NOMBRE_EQUIPO_1'] = "EQUIPO BAI";
				$equipo_ganador = $roles['NOMBRE_EQUIPO_2'];
				$bloqueo = 'disabled';
			}
			if ($roles['ID_EQUIPO_2'] == 0) {
				$roles['NOMBRE_EQUIPO_2'] = "EQUIPO BAI";
				$equipo_ganador = $roles['NOMBRE_EQUIPO_1'];
				$bloqueo = 'disabled';
			}
			$roles_juego .= "<tr>"
					. "<td><center><h5 style='font-family:Calibri;'>" . $roles['NOMBRE_TORNEO'] . "</h5></center></td>"
					. "<td><center><h5>" . $roles['CATEGORIA'] . "</h5></center></td>"
					. "<td><center><h5>" . $goles_equipo_1 . "</h5></center></td>"
					. "<td><center><h5>" . $roles['NOMBRE_EQUIPO_1'] . "</h5></center></td>"
					. "<td><center><h4>VS</h4></center></td>"
					. "<td><center><h5>" . $roles['NOMBRE_EQUIPO_2'] . "</h5></center></td>"
					. "<td><center><h5>" . $goles_equipo_2 . "</h5></center></td>"
					. "<td><center><h5>" . $equipo_ganador . "</h5></center></td>"
					. "<td><center><button " . $bloqueo . " onclick='guardar_cedula(" . $roles['ID_EQUIPO_1'] . "," . $roles['ID_EQUIPO_2'] . "," . $roles['ID_ROL_JUEGO'] . "," . $roles['ID_CONVOCATORIA'] . ")'>" . $usuario_permitido . "</button></center></td>"
					. "</tr>";
		}
		echo $roles_juego;
		break;
	case "Obtener_nombre_equipo":
		$sql = sprintf("SELECT * FROM equipos WHERE ID_EQUIPO =" . $_POST['team1']);
		$db->setQuery($sql);
		$info = $db->GetRow();
		if ($info['NOMBRE_EQUIPO']) {
			echo $info['NOMBRE_EQUIPO'];
		}
		break;
	case "Obtener_jugador_equipo":
		$sql = sprintf("select * from rosters inner join roster_jugador on rosters.ID_ROSTER = roster_jugador.ID_ROSTER inner join usuarios on roster_jugador.ID_JUGADOR = usuarios.ID_USUARIO  where ID_EQUIPO=" . $_POST['team1']);


		$db->setQuery($sql);
		$info = $db->GetResult();

		if ($_SESSION['TIPO_USUARIO'] == 'CAPTURISTA' || $_SESSION['TIPO_USUARIO'] == 'ADMINISTRADOR') {
			foreach ($info as $info2) {
				echo "<tr><td>" . $info2["NOMBRE"] . " " . $info2["APELLIDO_PATERNO"] . " " . $info2["APELLIDO_MATERNO"] . "</td><td><input type='button' id ='" . $info2["ID_USUARIO"] . "'onclick='AbrirPantalla(this.id)' value='ingresar dato'></td><td><input type='button' id='" . $info2["ID_USUARIO"] . "' onclick='Editar_datos(this.id)' value='editar dato'></td><td><input type='button' id='" . $info2["ID_USUARIO"] . "' onclick='Mostrar_Datos(this.id)' value='ver datos'></td></tr>";
			}
		} else {
			foreach ($info as $info2) {
				echo "<tr><td>" . $info2["NOMBRE"] . " " . $info2["APELLIDO_PATERNO"] . " " . $info2["APELLIDO_MATERNO"] . "</td><td><td><input type='button' id='" . $info2["ID_USUARIO"] . "' onclick='Mostrar_Datos(this.id)' value='ver datos'></td></tr>";
			}
		}
		break;
	case "Obtener_jugador_equipo2":
		$sql = sprintf("select * from rosters inner join roster_jugador on rosters.ID_ROSTER = roster_jugador.ID_ROSTER inner join usuarios on roster_jugador.ID_JUGADOR = usuarios.ID_USUARIO  where ID_EQUIPO=" . $_POST['team1']);

		$db->setQuery($sql);
		$info = $db->GetResult();

		if ($_SESSION['TIPO_USUARIO'] == 'CAPTURISTA' || $_SESSION['TIPO_USUARIO'] == 'ADMINISTRADOR') {
			foreach ($info as $info2) {
				echo "<tr><td>" . $info2["NOMBRE"] . " " . $info2["APELLIDO_PATERNO"] . " " . $info2["APELLIDO_MATERNO"] . "</td><td><input type='button' id ='" . $info2["ID_USUARIO"] . "'onclick='AbrirPantalla2(this.id)' value='ingresar dato'></td><td><input type='button' id='" . $info2["ID_USUARIO"] . "' onclick='Editar_datos2(this.id)' value='editar dato'></td><td><input type='button' id='" . $info2["ID_USUARIO"] . "' onclick='Mostrar_Datos(this.id)' value='ver datos'></td></tr>";
			}
		} else {
			foreach ($info as $info2) {
				echo "<tr><td>" . $info2["NOMBRE"] . " " . $info2["APELLIDO_PATERNO"] . " " . $info2["APELLIDO_MATERNO"] . "</td><td><td><input type='button' id='" . $info2["ID_USUARIO"] . "' onclick='Mostrar_Datos(this.id)' value='ver datos'></td></tr>";
			}
		}


		break;
	case "Obtenerid_roster":
		$sql = sprintf("SELECT * FROM rosters WHERE ID_EQUIPO =" . $_POST['ID_ROSTER']);
		$db->setQuery($sql);
		$info = $db->GetRow();
		if ($info['ID_ROSTER']) {
			echo $info['ID_ROSTER'];
		}
		break;
	case "insertar_cedulas":
		$sql = sprintf("INSERT INTO cedulas VALUES (0,'%s', '%s', '%s', '%s', '%s', '%s', '%s')", $_POST['ID_ROL_DEL_JUEGO'], $_POST['ID_DEL_JUGADOR'], $_POST['ID_DEL_ROSTER'], $_POST['ANOTACION'], $_POST['PASE'], $_POST['TACKLE'], $_POST['FAULT']);
		$db->setQuery($sql);
		$db->ExecuteQuery();
		echo "ok";
		break;
	case "recuperar_cedulas":
		$sql = sprintf("SELECT * FROM cedulas WHERE ID_JUGADOR =" . $_POST['ID_JUGADOR']);
		$db->setQuery($sql);
		$info2 = $db->GetRow();
		echo $info2["ANOTACIONES_JUGADOR"] . "," . $info2["PASES_JUGADOR"] . "," . $info2["TAKCLES_JUGADOR"] . "," . $info2["FAULTS_JUGADOR"];
		break;
	case "modificar_cedulas":
		$sql = sprintf("UPDATE cedulas SET ANOTACIONES_JUGADOR=" . $_POST['ANOTACION'] . ",PASES_JUGADOR=" . $_POST['PASE'] . ",TAKCLES_JUGADOR=" . $_POST['TACKLE'] . ",FAULTS_JUGADOR=" . $_POST['FAULT'] . " WHERE ID_JUGADOR=" . $_POST['ID_GMAER']);
		$db->setQuery($sql);
		$db->ExecuteQuery();
		echo "ok";
		break;
	case "totales_goles_equipo_1":
		$tota_goles_team_1 = 0;
		$tota_goles_team_2 = 0;
		$equipo_ganador;
		$sql = sprintf("SELECT * FROM cedulas WHERE ID_ROSTER =" . $_POST['ID_ROSTER1']);
		$db->setQuery($sql);
		$info = $db->GetResult();
		foreach ($info as $info2) {
			$tota_goles_team_1 = $tota_goles_team_1 + $info2['ANOTACIONES_JUGADOR'];
		}
		$sql = sprintf("SELECT * FROM cedulas WHERE ID_ROSTER =" . $_POST['ID_ROSTER2']);
		$db->setQuery($sql);
		$info = $db->GetResult();
		foreach ($info as $info2) {
			$tota_goles_team_2 = $tota_goles_team_2 + $info2['ANOTACIONES_JUGADOR'];
		}
		$sql = sprintf("SELECT * FROM rosters WHERE ID_ROSTER =" . $_POST['ID_ROSTER1']);
		$db->setQuery($sql);
		$info3 = $db->GetRow();
		$id_team1 = $info3['ID_EQUIPO'];
		$sql = sprintf("SELECT * FROM rosters WHERE ID_ROSTER =" . $_POST['ID_ROSTER2']);
		$db->setQuery($sql);
		$info3 = $db->GetRow();
		$id_team2 = $info3['ID_EQUIPO'];
		if ($tota_goles_team_1 > $tota_goles_team_2) {
			$equipo_ganador = $id_team1;
		} else if ($tota_goles_team_1 < $tota_goles_team_2) {
			$equipo_ganador = $id_team2;
		} else if ($tota_goles_team_1 == $tota_goles_team_2) {
			$equipo_ganador = 0;
		}
		$sql = sprintf("UPDATE roles_juego SET ID_EQUIPO_1 = %s, ID_EQUIPO_2 = %s, ID_EQUIPO_GANADOR = %s, GOLES_EQUIPO_1 = %s, GOLES_EQUIPO_2= %s WHERE ID_ROL_JUEGO = %s", $id_team1, $id_team2, $equipo_ganador, $tota_goles_team_1, $tota_goles_team_2, $_POST['ID_ROL_JUEGO']);
		$db->setQuery($sql);
		$db->ExecuteQuery();
		echo "ok";
		break;

	default:
		echo "Parámetro 'tipo' erróneo.";
}

$db->close();
?>
