<?php
	require 'SRV_CONEXION.php';
    $db = new SRV_CONEXION();
    $conn = $db->getConnection();
    $id_rol = $_POST['rol'];
    $roster = $_POST['roster'];
    $stmt = $conn->prepare("SELECT * FROM cedulas WHERE ID_ROL_JUEGO=? AND ID_ROSTER=?");
    $stmt->bind_param("ii",$id_rol,$roster);
    $stmt->execute();
    $result = $stmt->get_result();

    if($stmt->num_rows>-1){
    	
    	echo "<center><h3><a>PUNTAJE EN EL PARTIDO</a></h3></center>";
    	echo "<table class='table'>";
    	echo '<thead><tr><th>JUGADOR</th> <th>NUMERO</th> <th>T</th> <th>S</th>'; 
        echo '<th>I</th> <th>A</th> <th>C1</th> <th>C2</th> <th>PT</th>'; 
        echo '</tr></thead><tbody>';
        
    	
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
        	echo "<td>".$row['C2']."</td> <td>".$row['PT']."</td>";
        	echo "</tr>";
    	}
    	echo "</tbody></table>";
    }else{
       
    }
    $stmt->close();
    $conn->close();
?>