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
    //Si los hay obtenemos los datos del equipo (nombre)y de cada participante del roster recibido al principio
    if($jugadores&&mysqli_num_rows($jugadores)>0){
        $query = "SELECT NOMBRE_EQUIPO FROM equipos INNER JOIN rosters ON equipos.ID_EQUIPO=rosters.ID_EQUIPO WHERE ID_ROSTER=".$roster;
        $equipo = $conn->query($query);
        $equipo = $equipo->fetch_array(MYSQLI_ASSOC);
        //Regresamos el nombre del equipo del roster
        echo "<center><h2><a>".$equipo['NOMBRE_EQUIPO']."</a></h2></center>";
        echo "<center><h3><a>PUNTAJE TOTAL POR JUGADOR</a></h3></center>";
        //Generamos una tabla para los datos, con un header para cada campo registrado en la cedula
    	echo "<table class='table table-responsive'>";
    	echo '<thead><tr><th><center>JUGADOR</center></th> <th><center>NUMERO</center></th> <th><center>T</center></th> <th><center>S</center></th>'; 
        echo '<th><center>I</center></th> <th><center>A</center></th> <th><center>C1</center></th> <th><center>C2</center></th>'; 
        echo '<th><center>C3</center></th> <th><center>PA</center></th> <th><center>SA</center></th> <th><center>I4</center></th>';
        echo '<th><center>PT</center></th></tr></thead><tbody>';
        while ($row=mysqli_fetch_array($jugadores)) {
            //En esta parte obtenemos los datos de cada jugador mediante su id
        	$id = $row['ID_JUGADOR'];
        	$numero = $row['NUMERO'];
                
        	$consulta = "SELECT NOMBRE,APELLIDO_PATERNO,APELLIDO_MATERNO FROM usuarios WHERE ID_USUARIO=$id";
        	$result = $conn->query($consulta);
        	$result = mysqli_fetch_array($result);
        	$nombre = $result['NOMBRE']." ".$result['APELLIDO_PATERNO']." ".$result['APELLIDO_MATERNO'];
                //En esta parte obtenemos todos los resultados en las cedulas de cada jugador y realizamos una sumatoria para mostrar sus estadisticas generales de cada dato guardado
        	$datos = "SELECT * FROM cedulas WHERE ID_ROSTER=$roster AND ID_JUGADOR=$id";
        	$stats = $conn->query($datos);
        	$t=0;$s=0;$i=0;$a=0;$c1=0;$c2=0;$c3=0;$pa=0;$sa=0;$i4=0;//$pt=0;
        	while ($fila = mysqli_fetch_array($stats)) {
        		$t=$t+$fila['T'];
        		$s=$s+$fila['S'];
        		$i=$i+$fila['I'];
        		$a=$a+$fila['A'];
        		$c1=$c1+$fila['C1'];
        		$c2=$c2+$fila['C2'];
                        $c3=$c3+$fila['C3'];
                        $pa=$pa+$fila['PA'];
                        $sa=$sa+$fila['SA'];
                        $i4=$i4+$fila['I4'];
        		//$pt=$pt+$fila['PT'];//campo repetido es el mismo que pa
        	}
                //Regresamos la informacion en codigo html para una tabla
        	echo "<tr>";
        	echo "<td>$nombre</td> <td>$numero</td> <td>$t</td>";
        	echo "<td>$s</td> <td>$i</td> <td>$a</td> <td>$c1</td>";
        	echo "<td>$c2</td> <td>$c3</td> <td>$pa</td>";
                echo "<td>$sa</td> <td>$i4</td> ";//<td>$pt</td>";
        	echo "</tr>";
        }
        echo "</tbody></table>";
    }else{
    	echo "<center><h2><a>NO HAY JUGADORES EN EL EQUIPO</a></h2></center>";
    }
?>