<?php
// OBTENER INFO DE LA TABLA ESTADISTICAS QUE VA A CREAR JIMMY
//ESTADISTICAS VALUE(PARTIDOS_JUGADOS,PARTIDOS_GANADOS,PARTIDOS_PERDIDOS,PARTIDOS_EMPATADOS,PUNTOS_FAVOR,PUNTOS_CONTRA,DIFERENCIA,ID_CONVOCATORIA,ID_EQUIPO)
	require 'SRV_CONEXION.php';
    $db = new SRV_CONEXION();
    $conn = $db->getConnection();

    $id_convocatoria=$_POST['convocatoria'];

    $query="SELECT E.PARTIDOS_JUGADOS,E.PARTIDOS_GANADOS,E.PARTIDOS_PERDIDOS,E.PARTIDOS_EMPATADOS,E.PUNTOS_FAVOR,E.PUNTOS_CONTRA,E.DIFERENCIA,T.NOMBRE_EQUIPO FROM tabla_posiciones AS E INNER JOIN equipos AS T ON (E.ID_EQUIPO=T.ID_EQUIPO AND E.ID_CONVOCATORIA=$id_convocatoria) ORDER BY E.PARTIDOS_JUGADOS,E.PARTIDOS_GANADOS,E.PARTIDOS_PERDIDOS,E.PARTIDOS_EMPATADOS,E.PUNTOS_FAVOR,E.PUNTOS_CONTRA,E.DIFERENCIA";

    if($id_convocatoria>=0){
        $result = $conn->query($query);
        
        if($result&&mysqli_num_rows($result)>0){

            echo '<thead><tr><th>EQUIPO</th> <th>PJ</th> <th>PG</th> <th>PP</th>'; 
            echo '<th>PE</th> <th>PF</th> <th>PC</th> <th>DIF</th>'; 
            echo '</tr></thead><tbody>';
            
            while($row =  mysqli_fetch_array($result)){
                $nombre_equipo=$row['NOMBRE_EQUIPO'];
                $pj=$row['PARTIDOS_JUGADOS'];
                $pg=$row['PARTIDOS_GANADOS'];
                $pp=$row['PARTIDOS_PERDIDOS'];
                $pe=$row['PARTIDOS_EMPATADOS'];
                $pf=$row['PUNTOS_FAVOR'];
                $pc=$row['PUNTOS_CONTRA'];
                $dif=$row['DIFERENCIA'];
    
                echo '<tr><td>'.$nombre_equipo.'</td>';
                echo '<td>'.$pj.'</td>';
                echo '<td>'.$pg.'</td>';
                echo '<td>'.$pp.'</td>';
                echo '<td>'.$pe.'</td>';
                echo '<td>'.$pf.'</td>';
                echo '<td>'.$pc.'</td>';
                echo '<td>'.$dif.'</td></tr>';
            }
            echo "</tbody>";
        }
        else echo 1;
    }else echo 1;
    
    $conn->close();

?>