<?php 
	require 'SRV_CONEXION.php';
	$db = new SRV_CONEXION();
	$conn = $db->getConnection();
	
	$pre = $conn->query("SELECT ID_EQUIPO,NOMBRE_EQUIPO FROM equipos");

	if($pre && $pre->num_rows>0){
		echo "<option value='' disabled selected hidden>Selecciona un equipo</option>";
		while($row = $pre->fetch_array(MYSQLI_ASSOC)){
			echo "<option value='".$row['ID_EQUIPO']."'>".$row['NOMBRE_EQUIPO']."</option>";
		}
	}else{
		echo "<option value='' disabled selected hidden> No se encontraron equipos</option>";
	}

	$conn->close();
?>