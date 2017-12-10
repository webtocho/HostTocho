<?php

session_start();

include("SRV_CONEXION.php");
$db = new SRV_CONEXION();

switch ($_POST['tipo']) {
	case "mostrar_categorias_al_registrar_convocatoria":
		$sql = "SELECT * FROM categorias";
		$consulta = $db->getConnection()->prepare($sql);
		if ($consulta->execute()) {
			$consulta->close();
			$db->setQuery($sql);
			$resultado = $db->GetResult();
			$categorias = "<option value=''>Seleccione una categoria</option>";
			foreach ($resultado as $key => $lista_categorias) {
				$categorias .= "<option value='".$lista_categorias['ID_CATEGORIA'] . "'>".$lista_categorias['NOMBRE_CATEGORIA']."</option>";
			}
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
