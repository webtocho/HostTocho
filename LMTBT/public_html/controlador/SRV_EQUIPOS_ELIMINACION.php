<?php

session_start();
date_default_timezone_set('America/Mexico_City');
include("SRV_CONEXION.php");
$db = new SRV_CONEXION();

switch ($_POST['tipo']) {
	case "eliminar_equipo":
		// recupero los roles de juego en los que participa el equipo que se decea eliminar, verificando que en la fecha actual no este participando en alguna convocatoria o torneo en ejecucion 
		// si el tamaño del arreglo es 0 entonces quiere decir que el equipo se encuentra participando en alguna convocatoria y se le negara la eliminacion, de lo contrario si el arreglo es mayor que 0 
		// entonces el equipo ya no esta participando en algun torneo que se este ejecutando y por lo tanto se podra eliminar y consigo los datos de las tablas en el que se ve involucrado
		$db->setQuery(sprintf("SELECT ID_EQUIPO_1, ID_EQUIPO_2 FROM roles_juego INNER JOIN convocatoria ON roles_juego.ID_CONVOCATORIA = convocatoria.ID_CONVOCATORIA WHERE ID_EQUIPO_1 = %s AND convocatoria.FECHA_FIN_TORNEO < '%s' OR ID_EQUIPO_2 = %s AND convocatoria.FECHA_FIN_TORNEO < '%s'", $_POST['id_equipo'], date('Y-m-d'), $_POST['id_equipo'], date('Y-m-d')));
		$resultado = $db->GetResult();

		if (count($resultado) > 0) {
			// creamos una transaccion ya al tratarse de muchas cunsultas, en algun momento puede ocurrir algun fallo y gracias a la transaccion se podra revertir aquellas consultas que se habian ejecutado con exito
			$conexion = $db->getConnection();
			$conexion->autocommit(FALSE);

			$consulta = $conexion->prepare('DELETE participantes_rosters FROM participantes_rosters INNER JOIN rosters ON participantes_rosters.ID_ROSTER = rosters.ID_ROSTER WHERE rosters.ID_EQUIPO =  ? ');
			$consulta->bind_param("i", $_POST['id_equipo']);
			if ($consulta->execute()) {
				$consulta = $conexion->prepare("DELETE participantes_no_registrados FROM participantes_no_registrados INNER JOIN rosters ON participantes_no_registrados.ID_ROSTER = rosters.ID_ROSTER WHERE rosters.ID_EQUIPO =  ?");
				$consulta->bind_param("i", $_POST['id_equipo']);
				if ($consulta->execute()) {
					$consulta = $conexion->prepare("DELETE tabla_posiciones FROM tabla_posiciones INNER JOIN equipos ON tabla_posiciones.ID_EQUIPO = equipos.ID_EQUIPO WHERE equipos.ID_EQUIPO = ?");
					$consulta->bind_param("i", $_POST['id_equipo']);
					if ($consulta->execute()) {
						$consulta = $conexion->prepare("DELETE cedulas FROM cedulas INNER JOIN rosters ON cedulas.ID_ROSTER = rosters.ID_ROSTER WHERE rosters.ID_EQUIPO = ?");
						$consulta->bind_param("i", $_POST['id_equipo']);
						if ($consulta->execute()) {
							$consulta = $conexion->prepare("DELETE roles_juego FROM roles_juego INNER JOIN equipos ON roles_juego.ID_EQUIPO_1 = equipos.ID_EQUIPO or roles_juego.ID_EQUIPO_2 = equipos.ID_EQUIPO WHERE equipos.ID_EQUIPO = ?");
							$consulta->bind_param("i", $_POST['id_equipo']);
							if ($consulta->execute()) {
								$consulta = $conexion->prepare("DELETE rosters FROM rosters WHERE rosters.ID_EQUIPO = ?");
								$consulta->bind_param("i", $_POST['id_equipo']);
								if ($consulta->execute()) {
									$consulta = $conexion->prepare("DELETE equipos FROM equipos WHERE equipos.ID_EQUIPO = ?");
									$consulta->bind_param("i", $_POST['id_equipo']);
									if ($consulta->execute()) {
										$conexion->commit();
										$conexion->autocommit(TRUE);
										echo "ok";
									} else {
										// en caso se que ocurra un error, se revierten las consultas, evitando que que las tablas sean afectadas
										$conexion->rollback();
										$conexion->autocommit(TRUE);
										echo "Ha ocurrido un error al tratar de eliminar el equipo. Intentelo mas tarde.";
									}
								} else {
									$conexion->rollback();
									$conexion->autocommit(TRUE);
									echo "Ha ocurrido un error al tratar de eliminar el equipo. Intentelo mas tarde.";
								}
							} else {
								$conexion->rollback();
								$conexion->autocommit(TRUE);
								echo "Ha ocurrido un error al tratar de eliminar el equipo. Intentelo mas tarde.";
							}
						} else {
							$conexion->rollback();
							$conexion->autocommit(TRUE);
							echo "Ha ocurrido un error al tratar de eliminar el equipo. Intentelo mas tarde.";
						}
					} else {
						$conexion->rollback();
						$conexion->autocommit(TRUE);
						echo "Ha ocurrido un error al tratar de eliminar el equipo. Intentelo mas tarde.";
					}
				} else {
					$conexion->rollback();
					$conexion->autocommit(TRUE);
					echo "Ha ocurrido un error al tratar de eliminar el equipo. Intentelo mas tarde.";
				}
			} else {
				$conexion->rollback();
				$conexion->autocommit(TRUE);
				echo "Ha ocurrido un error al tratar de eliminar el equipo. Intentelo mas tarde.";
			}
		} else {
			echo "no";
		}
		break;
	default:
		echo "Parámetro 'tipo' erróneo.";
}
$db->close();
?>