<?php
	include("SRV_CONEXION.php");
    $db = new SRV_CONEXION();
    $conn = $db->getConnection();
        //En este php se retornan los 5 partidos mas cercanos (que no se han disputado aun), comprobando por hora y fecha en el momento que se llama al php
	$query="SELECT * FROM roles_juego WHERE CONCAT(FECHA,' ',HORA) > LOCALTIME() AND ID_EQUIPO_GANADOR=-1 ORDER BY FECHA ASC,HORA ASC LIMIT 5";
        
	$resultado = $conn->query($query);
        //Se comprueba que al menos haya un partido a retornar
	if($resultado && mysqli_num_rows($resultado)>0){
                //Si se encuentra un partido regresamos una tabla con la fecha, hora y los nombres de los equipos que jugaran en ese partido
		echo "<tbody>";
		while ($row = mysqli_fetch_array($resultado)) {
			$Q1 = "SELECT NOMBRE_EQUIPO FROM equipos WHERE ID_EQUIPO=".$row['ID_EQUIPO_1'];
			$Q2 = "SELECT NOMBRE_EQUIPO FROM equipos WHERE ID_EQUIPO=".$row['ID_EQUIPO_2'];

			$name1 = mysqli_fetch_array($conn->query($Q1));
			$name2 = mysqli_fetch_array($conn->query($Q2));

			echo "<tr><td class='ONE'>".$row['FECHA']." ".$row['HORA']."</td>";
			echo "<td clas='ONE'>".$name1['NOMBRE_EQUIPO']."</td>";
			echo "<td clas='ONE'>VS</td>";
			echo "<td clas='ONE'>".$name2['NOMBRE_EQUIPO']."</td></tr>";
		}
		echo "</tbody>";
	}else{
            //de no encontrarse ningun partido regresamos un mensaje especial
		echo "<a><h2>NO SE HAN ENCONTRADO PARTIDOS</h2><br><a><h3>ESPERA NUESTRO SIGUIENTE TORNEO</h3></a>";
	}

	
?>