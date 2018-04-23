<?php

session_start();

include("SRV_CONEXION.php");
$db = new SRV_CONEXION();

switch ($_POST['tipo']) {
	/**
	 * creamos una consulta preparada para poder recuperar la lista de las categorias disponibles, esto para mostrarla en el formulario de registro de convocatorias.
	 * @return $categorias retorna la lista de categorias
	 * **/
	case "mostrar_categorias_al_registrar_convocatoria":
		// creamos una consulta preparada
		$consulta = $db->getConnection()->prepare("SELECT * FROM categorias");
		if ($consulta->execute()) {
			// si se ejecuta con éxito, asignamos los datos a la variable $categorias
			$resultado = $consulta->get_result();
			$categorias = "<option value=''>Seleccione una categoría</option>";
			while ($lista_categorias = $resultado->fetch_assoc())
				$categorias .= "<option value='" . $lista_categorias['NOMBRE_CATEGORIA'] . "'>" . $lista_categorias['NOMBRE_CATEGORIA'] . "</option>";
			echo $categorias;
		} else {
			echo "Ha ocurrido un error al recuperar la información solicitada. Intente mas tarde, por favor.";
		}
		break;
	default:
		echo "Parámetro 'tipo' erróneo.";
}
$db->close();
?>
