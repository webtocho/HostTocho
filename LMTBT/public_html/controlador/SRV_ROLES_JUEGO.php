<?php

session_start();
// asignar la fecha correspondiente a nuestro pais
date_default_timezone_set('America/Mexico_City');

include("SRV_CONEXION.php");
$db = new SRV_CONEXION();

switch ($_POST['tipo']) {
	case "lista_convocatorias_inactivas":
		// preparamos una consulta para retornar la lista de convocatorias (torneos) que aun no han vencido
		$consulta = $db->getConnection()->prepare("SELECT ID_CONVOCATORIA, NOMBRE_TORNEO FROM convocatoria WHERE ESTADO = 'ACTIVO'");
		// si se pudo ejecuar la consulta, entonces se recuperan los datos, de lo contrario se informa un error
		if ($consulta->execute()) {
			$resultado = $consulta->get_result();
			$lista_convocatoria = "<option value='-1' selected hidden>Seleccione el torneo</option>";
			while ($arreglo_convocatoria = $resultado->fetch_assoc())
				$lista_convocatoria .= "<option value='" . $arreglo_convocatoria['ID_CONVOCATORIA'] . "'>" . $arreglo_convocatoria['NOMBRE_TORNEO'] . "</option>";
			echo $lista_convocatoria;
		} else {
			echo "Ha ocurrido un error al recuperar informacion para continuar con el registro de la convocatoria. Intente de nuevo mas tarde.";
		}
		break;
	case "roles_juegos_convocatoria_seleccionada":
		// es necesario validar la sesion y tipo de usuario para poder acceder a varios elementos del rol de juegos por ejemplo para ver las cedulas y para generarlas
		if (isset($_SESSION['ID_USUARIO'])) {
			if ($_SESSION['TIPO_USUARIO'] == 'ADMINISTRADOR' || $_SESSION['TIPO_USUARIO'] == 'CAPTURISTA') {

				$consulta = $db->getConnection()->prepare("SELECT ID_USUARIO, TIPO_USUARIO FROM usuarios WHERE ID_USUARIO = ? ");
				$consulta->bind_param("i", $_SESSION['ID_USUARIO']);
				if ($consulta->execute()) {
					$respuesta = $consulta->get_result();
					$informacion_usuario = $respuesta->fetch_assoc();
					// se le asigna permisos a los usuarios correspondientes para generar las cedulas, se son usuarios que no tienen permisos entonces solo se les permitira ver las cedulas.
					if ($_SESSION['ID_USUARIO'] == $informacion_usuario['ID_USUARIO'] && $_SESSION['TIPO_USUARIO'] == $informacion_usuario['TIPO_USUARIO']) {
						$usuario_permitido = "Generar cedula";
					} else {
						$usuario_permitido = "Ver cedula";
					}
				} else {
					echo "no";
					return;
				}
			} else {
				$usuario_permitido = "Ver cedula";
			}
		} else {
			echo "no";
			return;
		}
		// preparamos una consulta para obtener los roles de juegos de las convocatorias que aun no han vencido
		$fecha_hoy = date('Y-m-d');
		$consulta = $db->getConnection()->prepare("SELECT * FROM roles_juego INNER JOIN convocatoria ON roles_juego.ID_CONVOCATORIA = convocatoria.ID_CONVOCATORIA WHERE convocatoria.ESTADO = 'ACTIVO' AND convocatoria.ID_CONVOCATORIA = ?");
		$consulta->bind_param("i", $_POST['id_convocatoria']);
		if ($consulta->execute()) {

			$consulta->close();
			$db->setQuery(sprintf("SELECT * FROM roles_juego INNER JOIN convocatoria ON roles_juego.ID_CONVOCATORIA = convocatoria.ID_CONVOCATORIA WHERE convocatoria.ESTADO = 'ACTIVO' AND convocatoria.ID_CONVOCATORIA = %s ORDER BY JORNADA", $_POST['id_convocatoria']));
			$resultado = $db->GetResult();
			// en la tabla de rol_juegos las filas contienen los ID´s de los equipos que se enfrentaran, interesa conocer el nombre de estos asi tambien la categoria en la que estan participando
			// para ello realizo un ciclo para relacionar los nombre con los ID´s mediante consultas preparadas

			foreach ($resultado as $key => $roles) {
				// Identificadores que permiten saber cuando un equipo es BAI (un equipo tiene un ID = 0) y se muestra en la tabla como equipo BAI
				$bandera_bai1 = false;
				$bandera_bai2 = false;
				if ($roles['ID_EQUIPO_1'] == 0) {
					$bandera_bai1 = true;
				}
				if ($roles['ID_EQUIPO_2'] == 0) {
					$bandera_bai2 = true;
				}
				// termino de verificacion de equipo BAI
				
				// se prepara una consulta para recuperar el nombre del equipo 1, participante en el rol de juego
				$consulta = $db->getConnection()->prepare("SELECT NOMBRE_EQUIPO FROM equipos WHERE ID_EQUIPO = ?");
				$consulta->bind_param("i", $roles['ID_EQUIPO_1']);
				if ($consulta->execute()) {
					$resultado_ejecutar_query = $consulta->get_result();
					$datos_equipo1 = $resultado_ejecutar_query->fetch_assoc();
					// despues de ejecutarse la consulta se verifica el identificador correspondiente al equipo 1 ($bandera_bai1), si no es BAI se le asigna el dato recuperado de la consulta
					if ($bandera_bai1) {
						// se crean un nuevo campo en la consulta principal para el nombre del equipo 1
						$resultado[$key]['NOMBRE_EQUIPO_1'] = "BYE";
					} else {
						$resultado[$key]['NOMBRE_EQUIPO_1'] = $datos_equipo1['NOMBRE_EQUIPO'];
					}
					// se prepara una consulta para recuperar el nombre del equipo 2, participante en el rol de juego
					$consulta = $db->getConnection()->prepare("SELECT NOMBRE_EQUIPO FROM equipos WHERE ID_EQUIPO = ?");
					$consulta->bind_param("i", $roles['ID_EQUIPO_2']);
					if ($consulta->execute()) {
						$resultado_ejecutar_query = $consulta->get_result();
						$datos_equipo2 = $resultado_ejecutar_query->fetch_assoc();
						// despues de ejecutarse la consulta se verifica el identificador correspondiente al equipo 2 ($bandera_bai2), si no es BAI se le asigna el dato recuperado de la consulta
						if ($bandera_bai2) {
							// se crean un nuevo campo en la consulta principal para el nombre del equipo 2
							$resultado[$key]['NOMBRE_EQUIPO_2'] = "BYE";
						} else {
							$resultado[$key]['NOMBRE_EQUIPO_2'] = $datos_equipo2['NOMBRE_EQUIPO'];
						}
						// se prepara una consulta para recuperar la categoria del torneo
						$consulta = $db->getConnection()->prepare("SELECT NOMBRE_CATEGORIA FROM categorias WHERE ID_CATEGORIA= ?");
						$consulta->bind_param("i", $roles['ID_CATEGORIA']);
						if ($consulta->execute()) {
							$resultado_ejecutar_query = $consulta->get_result();
							$datos_categoria = $resultado_ejecutar_query->fetch_assoc();
							// se crean un nuevo campo en la consulta principal para el nombre de la categoria
							$resultado[$key]['CATEGORIA'] = $datos_categoria['NOMBRE_CATEGORIA'];
						} else {
							echo "Ha ocurrido un error al recuperar la informacion. Intentelo mas tarde.";
							return;
						}
					} else {
						echo "Ha ocurrido un error al recuperar la informacion. Intentelo mas tarde.";
						return;
					}
				} else {
					echo "Ha ocurrido un error al recuperar la informacion. Intentelo mas tarde.";
					return;
				}
			}
		} else {
			echo "Ha ocurrido un error al recuperar la informacion. Intentelo mas tarde.";
			return;
		}

		// relaciono los datos de los roles de juego y genero el codigo html para llenar  la tabla
		$roles_juego = "";	// esta variable contiene la lista de roles de los roles de juego, es la variable que se retorna al final
		// se inicializan las variables hacer posible la asignacion de los colores
		$jornada = -1;		// almacena el numero de la jornada actual
		$aux_jornada = -1;	// almacena temporalmente el número de la jornada
		$color = "blue";	// contiene el color que se le asignara a cada jornada, el color es modificado al realizar el siglo		
		$bandera_cambiar_de_color = true;  // para saber cuando cambiar de color
		
		// se recorren los datos para concatenarlos a la variable  $roles_juego
		foreach ($resultado as $key => $roles) {
			// si en el campo ID_EQUIPO_GANADOR tiene el número -1, entonces no se a definido un ganador, por ende el partido no se ha ejecutado
			if ($roles['ID_EQUIPO_GANADOR'] == -1) {
				$equipo_ganador = "Por definir";
				// de lo contrario si existe un equipo ganador, se le asigna a la variable $equipo_ganador el nombre del equipo ganador
			} else if ($roles['ID_EQUIPO_GANADOR'] == $roles['ID_EQUIPO_1']) {
				$equipo_ganador = $roles['NOMBRE_EQUIPO_1'];
			} else {
				$equipo_ganador = $roles['NOMBRE_EQUIPO_2'];
			}
			// Se establece el color como identificador para cada JORNADA, con el objetivo de diferenciarlos y evitar su confusión
			$jornada = $roles['JORNADA'];	// realizar el ciclo se asigna a la variable $jornada el número de la jornada
			if ($jornada != $aux_jornada) {	// si los ID que contienen las variables no son diferentes entonces significa que comienza una nueva jornada . 
				$aux_jornada = $jornada;
				if ($bandera_cambiar_de_color) {
					$bandera_cambiar_de_color = false;
					$color = "DarkCyan";
				} else if (!$bandera_cambiar_de_color) {
					$bandera_cambiar_de_color = true;
					$color = "orange";
				}
			}
			$color_jornada = 'style="background-color:' . $color . '"';
			// Terminar de asignar color
			
			// si uno de los equipos resulta ser BAI entonces el boton de generar cedula será bloqueado.
			$bloquear_funciones_equipo_bai = 'enabled';
			if ($roles['ID_EQUIPO_1'] == 0) {
				$bloquear_funciones_equipo_bai = 'disabled';
			}
			if ($roles['ID_EQUIPO_2'] == 0) {
				$bloquear_funciones_equipo_bai = 'disabled';
			}
			// concatenamos los datos en la variable que será retornada por el servidor
			$roles_juego .= "<tr>"
					. "<td " . $color_jornada . "><center><h6>" . $roles['JORNADA'] . "</h6></center></td>"
					. "<td " . $color_jornada . "><center><h6>" . $roles['CATEGORIA'] . "</h6></center></td>"
					. "<td " . $color_jornada . "><center><h6>" . $roles['PUNTOS_EQUIPO_1'] . "</h6></center></td>"
					. "<td " . $color_jornada . "><center><h6>" . $roles['NOMBRE_EQUIPO_1'] . "</h6></center></td>"
					. "<td " . $color_jornada . "><center><h6>VS</h6></center></td>"
					. "<td " . $color_jornada . "><center><h6>" . $roles['NOMBRE_EQUIPO_2'] . "</h6></center></td>"
					. "<td " . $color_jornada . "><center><h6>" . $roles['PUNTOS_EQUIPO_2'] . "</h6></center></td>"
					. "<td " . $color_jornada . "><center><h6>" . $equipo_ganador . "</h6></center></td>"
					. "<td " . $color_jornada . "><center><h6><button " . $bloquear_funciones_equipo_bai . " onclick='guardar_cedula(" . $roles['ID_EQUIPO_1'] . "," . $roles['ID_EQUIPO_2'] . "," . $roles['ID_ROL_JUEGO'] . "," . $roles['ID_CONVOCATORIA'] . ")' style = 'color:DimGray'>" . $usuario_permitido . "</button></h6></center></td>"
					. "</tr>";
//			}
		}
		echo $roles_juego;
		break;
	default:
		echo "Parámetro 'tipo' erróneo.";
}
$db->close();
?>
