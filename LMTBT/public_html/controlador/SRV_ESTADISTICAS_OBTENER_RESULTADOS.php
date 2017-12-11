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
        echo "<center><h3><a>PUNTAJE TOTAL DE TODOS LOS PARTIDOS</a></h3></center>";
    	echo "<table class='table'>";
    	echo '<thead><tr><th>JUGADOR</th> <th>NUMERO</th> <th>T</th> <th>S</th>'; 
        echo '<th>I</th> <th>A</th> <th>C1</th> <th>C2</th> <th>PT</th>'; 
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