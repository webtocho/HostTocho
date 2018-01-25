<?php
	require 'SRV_CONEXION.php';
	$db = new SRV_CONEXION();
	$conn = $db->getConnection();
	$id = $_POST['id_roster'];
        
        $team=$conn->prepare("SELECT ID_EQUIPO FROM rosters WHERE ID_ROSTER=?");
        $team->bind_param("i",$id);
        $team->execute();
        $equipo = $team->get_result();
        $idEquipo = $equipo->fetch_array(MYSQLI_ASSOC);
        $team->close();
        $coach="SELECT ID_COACH FROM equipos WHERE ID_EQUIPO=".$idEquipo['ID_EQUIPO'];
        $rCoach = $conn->query($coach);
        $idCoach = $rCoach->fetch_array(MYSQLI_ASSOC);
        
        $infoCoach = "SELECT CONCAT(NOMBRE,' ',APELLIDO_PATERNO,' ',APELLIDO_MATERNO) AS NOMBRE, FOTO_PERFIL FROM usuarios WHERE ID_USUARIO=".$idCoach['ID_COACH'];
        $dataCoach = $conn->query($infoCoach);
        $datos = $dataCoach->fetch_array(MYSQLI_ASSOC);
        
        echo "<div class='item'  style='display: inline-block; margin:10px;width: 160px;height: 160px;'>";
        echo "<center><h4><a>COACH</a></h4></center>";
        $foto = base64_encode($datos['FOTO_PERFIL']);
        if($foto==null){
            echo "<center><img class='img-responsive lot img-rounded' src='img/RC_IF_ANONIMO.png' alt='' style='max-width:150px;max-height:150px;'/></center>";
        }else{
            echo "<center><img class='img-responsive lot img-rounded' src='data:image/png;base64,".$foto."' alt='' style='max-width:150px;max-height:150px;'/></center>";
        }
        echo "<center><h4><a>".$datos['NOMBRE']."</a></h4></center><br>";
        echo "<center><h4><a>JUGADORES</a></h4></center>";
	echo "</div><br>";
                        
                        
	$pre = $conn->prepare("SELECT ID_JUGADOR,NUMERO FROM participantes_rosters WHERE ID_ROSTER=?");
	$pre->bind_param("i",$id);
	$pre->execute();
	$result = $pre->get_result();
	if($result && $result->num_rows>0){
		while($row = $result->fetch_array(MYSQLI_ASSOC)){
			$sql = "SELECT CONCAT(NOMBRE,' ',APELLIDO_PATERNO,' ',APELLIDO_MATERNO) AS NOMBRE, FOTO_PERFIL FROM usuarios WHERE ID_USUARIO = ".$row['ID_JUGADOR'];
			$jugador = $conn->query($sql);
			$jugador = $jugador->fetch_array(MYSQLI_ASSOC);
			// 
			echo "<div class='item'  style='display: inline-block; margin:10px;width: 160px;height: 160px;'>";
			$foto = base64_encode($jugador['FOTO_PERFIL']);
                        if($foto==null){
                            //style='max-width:50px;max-height:50px;'
                            echo "<center><img class='img-responsive lot img-rounded' src='img/RC_IF_ANONIMO.png' alt='' style='max-width:150px;max-height:150px;'/></center>";
                        }else{
                            echo "<center><img class='img-responsive lot img-rounded' src='data:image/png;base64,".$foto."' alt='' style='max-width:150px;max-height:150px;'/></center>";
                        }
                        echo "<center><h4><a>".$jugador['NOMBRE']."</a></h4></center>";
			echo "</div>";
		}
                
	}else{
		echo "<h2><a> No se encontraron jugadores</a></h2>";
	}
	$pre->close();
	$conn->close();
?>