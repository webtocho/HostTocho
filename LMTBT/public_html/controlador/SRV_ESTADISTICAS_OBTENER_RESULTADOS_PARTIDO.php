<?php
    require 'SRV_CONEXION.php';
    $db = new SRV_CONEXION();
    $conn = $db->getConnection();
    $id_rol = $_POST['rol'];
    $roster = $_POST['roster'];
    //En este php recuperamos la informacion de un solo partido mediante la id del roster y del rol de juegos
    $stmt = $conn->prepare("SELECT * FROM cedulas WHERE ID_ROL_JUEGO=? AND ID_ROSTER=?");
    $stmt->bind_param("ii",$id_rol,$roster);
    $stmt->execute();
    $result = $stmt->get_result();
    //Si hay informacion del partido procedemos a retornar la informacion pertinente 
    if($stmt->num_rows>-1){
        //Primero retornamos el nombre del equipo
    	$nameEquipo = "SELECT NOMBRE_EQUIPO FROM equipos INNER JOIN rosters ON equipos.ID_EQUIPO=rosters.ID_EQUIPO WHERE ID_ROSTER=".$roster;
        $equipo = $conn->query($nameEquipo);
        $equipo = $equipo->fetch_array(MYSQLI_ASSOC);
        echo "<center><h2><a>".$equipo['NOMBRE_EQUIPO']."</a></h2></center>";
        //Luego generamos la cabecera de una tabla que contendra las estadisticas del partido
    	echo "<center><h3><a>PUNTAJE EN EL PARTIDO</a></h3></center>";
    	echo "<table class='table'>";
    	echo '<thead><tr><th><center>JUGADOR</center></th> <th><center>NUMERO</center></th> <th><center>T</center></th> <th><center>S</center></th>'; 
        echo '<th><center>I</center></th> <th><center>A</center></th> <th><center>C1</center></th> <th><center>C2</center></th><th><center>C3</center></th> <th><center>PA</center></th> <th><center>SA</center></th> <th><center>I4</center></th>'; 
        echo '</tr></thead><tbody>';
    	//Despues retornamos la informacion de cada jugador del equipo (su nombre y sus resultados del partido)
    	while ($row = $result->fetch_array(MYSQLI_ASSOC)){
            $query="SELECT NUMERO FROM participantes_rosters WHERE ID_JUGADOR=".$row['ID_JUGADOR']." and ID_ROSTER=".$row['ID_ROSTER'];
            $numero = $conn->query($query);
            $numero = $numero->fetch_array(MYSQLI_ASSOC);
            $numero = $numero['NUMERO'];
    		$sent = "SELECT CONCAT(NOMBRE,' ',APELLIDO_PATERNO,' ',APELLIDO_MATERNO) AS NOMBRE FROM usuarios WHERE ID_USUARIO=".$row['ID_JUGADOR'];
            $nombre = $conn->query($sent);
            $nombre = $nombre->fetch_array(MYSQLI_ASSOC);
            $nombre = $nombre['NOMBRE'];
    		echo "<tr>";
        	echo "<td>".$nombre."</td> <td>".$numero."</td> <td>".$row['T']."</td>";
        	echo "<td>".$row['S']."</td> <td>".$row['I']."</td> <td>".$row['A']."</td> <td>".$row['C1']."</td>";
        	echo "<td>".$row['C2']."</td> <td>".$row['C3']."</td>";
                echo "<td>".$row['PA']."</td> <td>".$row['SA']."</td>";
                echo "<td>".$row['I4']."</td>";// <td>".$row['PT']."</td>";
        	echo "</tr>";
    	}
    	echo "</tbody></table>";
    }else{
       
    }
    $stmt->close();
    $conn->close();
?>