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
        echo "<div class='item'>";
        echo "<center><h4><a>COACH</a></h4></center>";
        $foto = base64_encode($datos['FOTO_PERFIL']);
        if($foto==null){
            echo "<center><img id='fotoPlayer' class='img-responsive lot img-rounded' src='../modelo/img/RC_IF_ANONIMO.png' /></center>";
        }else{
            echo "<center><img id='fotoPlayer' class='img-responsive lot img-rounded' src='data:image/png;base64,".$foto."' /></center>";
        }
        echo "<center><h4><a>".$datos['NOMBRE']."</a></h4></center><br>";
        //Luego procedemos con los jugadores del equipo
        echo "<center><h4><a>JUGADORES</a></h4></center>";
	echo "</div><br>";
                        
	$conn->close();
?>