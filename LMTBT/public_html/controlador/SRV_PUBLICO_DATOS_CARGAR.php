<?php
	require 'SRV_CONEXION.php';
	$db = new SRV_CONEXION();
	$conn = $db->getConnection();
	$id = $_POST['id_roster'];
        //En este php se regresaran los datos de un roster
        //Primero obtenemos el id del equipo al que pertenece el roster
        $team=$conn->prepare("SELECT ID_EQUIPO FROM rosters WHERE ID_ROSTER=?");
        $team->bind_param("i",$id);
        $team->execute();
        $equipo = $team->get_result();
        $idEquipo = $equipo->fetch_array(MYSQLI_ASSOC);
        $team->close();
        //Obtenemos el id del coach
        $coach="SELECT ID_COACH FROM equipos WHERE ID_EQUIPO=".$idEquipo['ID_EQUIPO'];
        $rCoach = $conn->query($coach);
        $idCoach = $rCoach->fetch_array(MYSQLI_ASSOC);
        //Obtenemos los datos del coach (nombre y foto)
        $infoCoach = "SELECT CONCAT(NOMBRE,' ',APELLIDO_PATERNO,' ',APELLIDO_MATERNO) AS NOMBRE, FOTO_PERFIL FROM usuarios WHERE ID_USUARIO=".$idCoach['ID_COACH'];
        $dataCoach = $conn->query($infoCoach);
        $datos = $dataCoach->fetch_array(MYSQLI_ASSOC);
        //Regresamos los datos del coach del equipo
        echo "<div class='item'  style='display: inline-block; margin:10px;width: 160px;height: 160px;'>";
        echo "<center><h4><a>COACH</a></h4></center>";
        $foto = base64_encode($datos['FOTO_PERFIL']);
        if($foto==null){
            echo "<center><img class='img-responsive lot img-rounded' src='../modelo/img/RC_IF_ANONIMO.png' alt='' style='max-width:150px;max-height:150px;'/></center>";
        }else{
            echo "<center><img class='img-responsive lot img-rounded' src='data:image/png;base64,".$foto."' alt='' style='max-width:150px;max-height:150px;'/></center>";
        }
        echo "<center><h4><a>".$datos['NOMBRE']."</a></h4></center><br>";
        //Luego procedemos con los jugadores del equipo
        echo "<center><h4><a>JUGADORES</a></h4></center>";
	echo "</div><br>";
                        
        /*/Realizamos un select para recuperar el id y numero de todos los jugadores del roster
	$pre = $conn->prepare("SELECT ID_JUGADOR,NUMERO FROM participantes_rosters WHERE ID_ROSTER=?");
	$pre->bind_param("i",$id);
	$pre->execute();
	$result = $pre->get_result();
        //Comprobamos si al menos hay un jugador en dicho roster
	if($result && $result->num_rows>0){
                //Si hay jugadores, procedemos a recuperar la informacion de cada roster (nombre y foto)
		while($row = $result->fetch_array(MYSQLI_ASSOC)){
			$sql = "SELECT CONCAT(NOMBRE,' ',APELLIDO_PATERNO,' ',APELLIDO_MATERNO) AS NOMBRE, FOTO_PERFIL FROM usuarios WHERE ID_USUARIO = ".$row['ID_JUGADOR'];
			$jugador = $conn->query($sql);
			$jugador = $jugador->fetch_array(MYSQLI_ASSOC);
			//De cada jugador regresamos un div con la foto y nombre del jugador
			echo "<div class='item'  style='display: inline-block; margin:10px;width: 160px;height: 160px;'>";
			$foto = base64_encode($jugador['FOTO_PERFIL']);
                        if($foto==null){
                            //style='max-width:50px;max-height:50px;'
                            echo "<center><img class='img-responsive lot img-rounded' src='../modelo/img/RC_IF_ANONIMO.png' alt='' style='max-width:150px;max-height:150px;'/></center>";
                        }else{
                            echo "<center><img class='img-responsive lot img-rounded' src='data:image/png;base64,".$foto."' alt='' style='max-width:150px;max-height:150px;'/></center>";
                        }
                        echo "<center><h4><a>".$jugador['NOMBRE']."</a><br><a>".$row['NUMERO']."</a></h4></center>";
			echo "</div>";
		}
                
	}else{
		echo "<h2><a> No se encontraron jugadores</a></h2>";
	}*/
	//$pre->close();
	$conn->close();
?>