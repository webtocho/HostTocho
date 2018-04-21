<?php
	require 'SRV_CONEXION.php';
	$db = new SRV_CONEXION();
	$conn = $db->getConnection();
	$id_roster = $_POST['id_roster'];
        $id_jugador = $_POST['id_jugador'];
                        
        //Realizamos un select para recuperar el numero del jugador a mostrar
	$pre = $conn->prepare("SELECT NUMERO FROM participantes_rosters WHERE ID_ROSTER=? AND ID_JUGADOR=?");
	$pre->bind_param("ii",$id_roster,$id_jugador);
	$pre->execute();
	$resultadoNumero = $pre->get_result();
        $numero = $resultadoNumero->fetch_array(MYSQLI_ASSOC);
        $numeroJugador = $numero['NUMERO'];
        $pre->close();
        //$sql = ".$row['ID_JUGADOR'];
	$pre = $conn->prepare("SELECT CONCAT(NOMBRE,' ',APELLIDO_PATERNO,' ',APELLIDO_MATERNO) AS NOMBRE, FOTO_PERFIL FROM usuarios WHERE ID_USUARIO = ? ");
	$pre->bind_param("i",$id_jugador);
	$pre->execute();
	$resultadoSelect = $pre->get_result();
        $jugador = $resultadoSelect->fetch_array(MYSQLI_ASSOC);
        //De cada jugador regresamos un div con la foto y nombre del jugador
        echo "<div class='item'>";
	$foto = base64_encode($jugador['FOTO_PERFIL']);
        if($foto==null){
            echo "<center><img id='fotoPlayer' class='img-responsive lot img-rounded' src='../modelo/img/RC_IF_ANONIMO.png'/></center>";
        }else{
            echo "<center><img id='fotoPlayer' class='img-responsive lot img-rounded' src='data:image/png;base64,".$foto."'/></center>";
        }
            echo "<center><h4><a>".$jugador['NOMBRE']."</a><br><a>NUMERO ".$numeroJugador."</a></h4></center>";
            echo "</div>";
        $pre->close();
	$conn->close();
?>