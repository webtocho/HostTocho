<?php
	include("SRV_CONEXION.php");
    $db = new SRV_CONEXION();
    $conn = $db->getConnection();

	$query="SELECT * FROM roles_juego WHERE FECHA >= CURDATE() AND ID_EQUIPO_GANADOR=-1 AND HORA>CURTIME() ORDER BY FECHA ASC,HORA ASC LIMIT 4";

	$resultado = $conn->query($query);

	if($resultado && mysqli_num_rows($resultado)>0){
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
	}

	
?>