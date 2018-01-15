<?php
    require 'SRV_CONEXION.php';
    $db = new SRV_CONEXION();
    $conn = $db->getConnection();

    $roster = $_POST['roster'];
    //OBTENEMOS LOS JUGADORES DE UN ROSTER
    $jugadores=null;
    if($roster>0){
    	$sql = "SELECT ID_JUGADOR,NUMERO FROM participantes_rosters WHERE ID_ROSTER=$roster";
        $jugadores = $conn->query($sql);
    }
    //comprobamos si hay jugadores en ese roster
    if($jugadores&&mysqli_num_rows($jugadores)>0){
        $nameEquipo = "SELECT NOMBRE_EQUIPO FROM equipos INNER JOIN rosters ON equipos.ID_EQUIPO=rosters.ID_EQUIPO WHERE ID_ROSTER=".$roster;
        $equipo = $conn->query($nameEquipo);
        $equipo = $equipo->fetch_array(MYSQLI_ASSOC);
        echo "<center><h2><a>".$equipo['NOMBRE_EQUIPO']."</a></h2></center>";
        echo "<center><h3><a>PUNTAJE TOTAL POR JUGADOR</a></h3></center>";
    	echo "<table class='table'>";
    	echo '<thead><tr><th><center>JUGADOR</center></th> <th><center>NUMERO</center></th> <th><center>T</center></th> <th><center>S</center></th>'; 
        echo '<th><center>I</center></th> <th><center>A</center></th> <th><center>C1</center></th> <th><center>C2</center></th> <th><center>PT</center></th>'; 
        echo '</tr></thead><tbody>';
        while ($row=mysqli_fetch_array($jugadores)) {
        	$id = $row['ID_JUGADOR'];
        	$numero = $row['NUMERO'];

        	$consulta = "SELECT NOMBRE,APELLIDO_PATERNO,APELLIDO_MATERNO FROM usuarios WHERE ID_USUARIO=$id";
        	$result = $conn->query($consulta);
        	$result = mysqli_fetch_array($result);
        	$nombre = $result['NOMBRE']." ".$result['APELLIDO_PATERNO']." ".$result['APELLIDO_MATERNO'];

        	$datos = "SELECT * FROM cedulas WHERE ID_ROSTER=$roster AND ID_JUGADOR=$id";
        	$stats = $conn->query($datos);
        	$t=0;$s=0;$i=0;$a=0;$c1=0;$c2=0;$pt=0;
        	while ($fila = mysqli_fetch_array($stats)) {
        		$t=$t+$fila['T'];
        		$s=$s+$fila['S'];
        		$i=$i+$fila['I'];
        		$a=$a+$fila['A'];
        		$c1=$c1+$fila['C1'];
        		$c2=$c2+$fila['C2'];
        		$pt=$pt+$fila['PT'];
        	}
        	echo "<tr>";
        	echo "<td>$nombre</td> <td>$numero</td> <td>$t</td>";
        	echo "<td>$s</td> <td>$i</td> <td>$a</td> <td>$c1</td>";
        	echo "<td>$c2</td> <td>$pt</td>";
        	echo "</tr>";
        }
        echo "</tbody></table>";
    }else{
    	echo "<center><h2><a>NO HAY JUGADORES EN EL EQUIPO</a></h2></center>";
    }
?>