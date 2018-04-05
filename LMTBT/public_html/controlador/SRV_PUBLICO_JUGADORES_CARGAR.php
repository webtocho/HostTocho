<?php
	require 'SRV_CONEXION.php';
	$db = new SRV_CONEXION();
	$conn = $db->getConnection();
	$id = $_POST['id_roster'];
                        
        //Realizamos un select para recuperar el id de todos los jugadores del roster
	$pre = $conn->prepare("SELECT ID_JUGADOR FROM participantes_rosters WHERE ID_ROSTER=?");
	$pre->bind_param("i",$id);
	$pre->execute();
	$result = $pre->get_result();
        //Comprobamos si al menos hay un jugador en dicho roster
	if($result && $result->num_rows>0){
            //Si hay jugadores, procedemos a recuperar la informacion de cada roster (nombre y foto)
            $i=0;
            //Retornamos una cadena con los ids separados por comas
            //$rawdata="";
            while($row = mysqli_fetch_array($result)){
                    if($i==0) {$rawdata=$row['ID_JUGADOR']; $i=1;}
                    else{$rawdata=$rawdata.','.$row['ID_JUGADOR'];}
            }
            echo $rawdata;
        }else echo -1;  
	$pre->close();
	$conn->close();
?>