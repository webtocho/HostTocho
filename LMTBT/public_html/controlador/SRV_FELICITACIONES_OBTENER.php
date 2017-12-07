<?php
	$conn = new mysqli("localhost","id3551892_team","tochoweb", "id3551892_tochoweb");

	$query = "SELECT NOMBRE,APELLIDO_PATERNO,APELLIDO_MATERNO FROM usuarios WHERE DATE_FORMAT(FECHA_NACIMIENTO, '%m%d') = DATE_FORMAT(CURDATE(),'%m%d');";//query para obtener cumpleañeros por dia
	$result = $conn->query($query);

	if($result){
                if(mysqli_num_rows($result)<=0) echo "<p>Hoy no hay cumpleañeros</p>";
		else{
                    echo "<p>En este día tan especial no hará falta un delicioso pastel para desearte un feliz cumpleaños. ¡Muchas felicidades!</p><br>";
                    while($row = mysqli_fetch_array($result)){
			echo "<p><img class='media-left response-text-left' src='img/RC_IF_ICONS_1.png' alt='' style='max-width: 20px; max-height: 20px'> ".$row['NOMBRE']." ".$row['APELLIDO_PATERNO']." ".$row['APELLIDO_MATERNO']."</p><br>";
                    }
                }
	}else echo 1;
	$conn->close();
?>