<?php 
	require 'SRV_CONEXION.php';
	$db = new SRV_CONEXION();
	$conn = $db->getConnection();
	$id = $_POST['id_equipo'];
	$pre = $conn->prepare("SELECT ID_ROSTER,ID_CATEGORIA FROM rosters WHERE ID_EQUIPO=?");
	$pre->bind_param("i", $id);
	$pre->execute();
	$result = $pre->get_result();

	if($result && $result->num_rows>0){
		echo "<option value='' disabled selected hidden>Selecciona una categoria</option>";
		while($row = $result->fetch_array(MYSQLI_ASSOC)){
			$sql = "SELECT NOMBRE_CATEGORIA FROM categorias WHERE ID_CATEGORIA = ".$row['ID_CATEGORIA'];
			$name = $conn->query($sql);
			$name = $name->fetch_array(MYSQLI_ASSOC);
			$categoria = $name['NOMBRE_CATEGORIA'];
			echo "<option value='".$row['ID_ROSTER']."'>".$categoria."</option>";
		}
	}else{
		echo "<option value='' disabled selected hidden> No se encontraron equipos</option>";
	}
	$pre->close();
	$conn->close();
?>