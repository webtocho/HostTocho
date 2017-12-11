<?php
	require 'SRV_CONEXION.php';
    $db = new SRV_CONEXION();
    $conn = $db->getConnection();

    $convocatoria = $_POST['convocatoria'];
    //primero obtenemos todos los rosters en una convocatoria
    $sql = "SELECT R.ID_ROSTER,E.NOMBRE_EQUIPO FROM rosters AS R INNER JOIN equipos AS E ON R.ID_EQUIPO=E.ID_EQUIPO WHERE ID_CONVOCATORIA = $convocatoria";
    $id = $conn->query($sql);
    //comprobamos si hay al menos un equipo(roster) inscrito
    if($id && mysqli_num_rows($id)>0){
    	echo "<option value='' disabled selected hidden>Selecciona un equipo...</option>";
    	while ($row = mysqli_fetch_array($id)) {
    		$id_roster = $row['ID_ROSTER'];
    		$nombre_equipo = $row['NOMBRE_EQUIPO'];
    		echo "<option value='$id_roster'>$nombre_equipo</option>";
    	}
    }else{
    	//si no encontramos un equipo(roster) retornamos que no hay nada
    	echo "<option value='' disabled selected hidden>No hay equipos</option>"; 
    }
?>