<?php 
	require 'SRV_CONEXION.php';
    $db = new SRV_CONEXION();
    $conn = $db->getConnection();
    //En este php recuperamos los partidos jugados por un equipo(roster)
    $id_equipo=-1;
    $id_roster = $_POST['roster'];
    $id_convocatoria= $_POST['convocatoria'];
    //primero obtenemos el id del equipo ya que solo tenemos el id del roster.
    $stmt = $conn->prepare("SELECT ID_EQUIPO FROM rosters WHERE ID_ROSTER=?");
    $stmt->bind_param("i",$id_roster);
    $stmt->execute();
    $stmt->bind_result($id_equipo);
    $stmt->fetch();
    $stmt->close();
    //Si el equipo existe recuperamos la información pertinente
    if($id_equipo>0){
        //Recuperamos todos los partidos ya jugados (en los que participo el equipo)
    	$stmt=$conn->prepare("SELECT * FROM roles_juego WHERE ID_CONVOCATORIA=? AND (ID_EQUIPO_1=? OR ID_EQUIPO_2=?) AND ID_EQUIPO_GANADOR>-1");
    	$stmt->bind_param("iii",$id_convocatoria,$id_equipo,$id_equipo);
    	$stmt->execute();
    	$result = $stmt->get_result();
        //Comprobamos si almenos ya hay un partido jugado
    	//Si se cumple la condición regresamos los partidos como codigo html para un select con el estilo de NombreEquipo1(Puntaje) vs NombreEquipo2(Puntaje) y con value del id del rol de juego
    	if($stmt->num_rows>-1){
            echo "<option value='' disabled selected hidden>Selecciona un partido</option>";
    		while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    		$sentenciaNombre=$conn->prepare("SELECT NOMBRE_EQUIPO FROM equipos WHERE ID_EQUIPO=?");
    		$sentenciaNombre->bind_param("i",$row['ID_EQUIPO_1']);
    		$sentenciaNombre->execute();
    		$sentenciaNombre->bind_result($nombre1);
    		$sentenciaNombre->fetch();

    		$sentenciaNombre->bind_param("i",$row['ID_EQUIPO_2']);
    		$sentenciaNombre->execute();
    		$sentenciaNombre->bind_result($nombre2);
    		$sentenciaNombre->fetch();

    		$sentenciaNombre->close();
    	
			echo "<option value='".$row['ID_ROL_JUEGO']."'>".$nombre1."(".$row['PUNTOS_EQUIPO_1'].") vs ".$nombre2."(".$row['PUNTOS_EQUIPO_2'].")</option>";
    		}
    	}else{
            //Caso contrario retornamos una opcion de que no hay partidos
            echo "<option value='' disabled selected hidden> No se encontraron partidos jugados</option>";
    	}
    	$stmt->close(); 
    }else{
        //Caso contrario regresamos una opcion de que no hay datos
    	echo "<option value='' disabled selected hidden> No se encontraron partidos jugados</option>";
    }
    $conn->close();
?>