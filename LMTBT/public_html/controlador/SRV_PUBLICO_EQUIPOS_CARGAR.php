<?php 
	require 'SRV_CONEXION.php';
	$db = new SRV_CONEXION();
	$conn = $db->getConnection();
	//En este php regresamos todos los equipos en el sistema
	$pre = $conn->query("SELECT ID_EQUIPO,NOMBRE_EQUIPO FROM equipos");

	if($pre && $pre->num_rows>0){
                //Si hay al menos un equpo regresamos el option para el select al que se agregaran
		echo "<option value='' disabled selected hidden>Selecciona un equipo</option>";
		while($row = $pre->fetch_array(MYSQLI_ASSOC)){
                    //El value sera el id del equipo y el texto del option sus nombre
			echo "<option value='".$row['ID_EQUIPO']."'>".$row['NOMBRE_EQUIPO']."</option>";
		}
	}else{
            //Si no se encuentra ni un equipo se regresa un mensaje especial
		echo "<option value='' disabled selected hidden> No se encontraron equipos</option>";
	}

	$conn->close();
?>