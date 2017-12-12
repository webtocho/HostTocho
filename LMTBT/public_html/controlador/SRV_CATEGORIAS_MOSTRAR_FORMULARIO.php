<?php

session_start();

include("SRV_CONEXION.php");
$db = new SRV_CONEXION();

switch ($_POST['tipo']) {
	case "mostrar_categorias_al_registrar_convocatoria":
		// creamos una consulta preparada para poder recuperar la lista de las categorias disponibles, esto para mostrarla en el formulario de registro de convocatorias.
		$consulta = $db->getConnection()->prepare("SELECT * FROM categorias");
		if ($consulta->execute()) {
			$resultado = $consulta->get_result();
			$categorias = "<option value=''>Seleccione una categoria</option>";
			while ($lista_categorias = $resultado->fetch_assoc())
				$categorias .= "<option value='".$lista_categorias['NOMBRE_CATEGORIA'] . "'>".$lista_categorias['NOMBRE_CATEGORIA']."</option>";
			echo $categorias;
		} else {
			echo "Ha ocurrido un error al recuperar la informacion solicitada. Intente mas tarde porfavor.";
		}
		break;
	default:
		echo "Parámetro 'tipo' erróneo.";
}
$db->close();
?>
