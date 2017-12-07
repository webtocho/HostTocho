<?php
// OBTENER INFO DE LA TABLA ESTADISTICAS QUE VA A CREAR JIMMY
//ESTADISTICAS VALUE(PARTIDOS_JUGADOS,PARTIDOS_GANADOS,PARTIDOS_PERDIDOS,PARTIDOS_EMPATADOS,PUNTOS_FAVOR,PUNTOS_CONTRA,DIFERENCIA,ID_CONVOCATORIA,ID_EQUIPO)
	$conn = new mysqli("localhost","id3551892_team","tochoweb", "id3551892_tochoweb");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $id_convocatoria=$_POST['convocatoria'];

    $query="SELECT E.PARTIDOS_JUGADOS,E.PARTIDOS_GANADOS,E.PARTIDOS_PERDIDOS,E.PARTIDOS_EMPATADOS,E.PUNTOS_FAVOR,E.PUNTOS_CONTRA,E.DIFERENCIA,T.NOMBRE_EQUIPO FROM estadisticas AS E INNER JOIN equipos AS T ON E.ID_EQUIPO=T.ID_EQUIPO AND E.ID_CONVOCATORIA=".$id_convocatoria;

    if($id_convocatoria>0){
        $result = $conn->query($query);
        
        if($result&&mysqli_num_rows($result)>0){
            echo '<table class="table"> <tr class="info">'; 
            echo '<th>EQUIPO</th> <th>PARTIDOS JUGADOS</th> <th>PARTIDOS GANADOS</th> <th>PARTIDOS PERDIDOS</th>'; 
            echo '<th>PARTIDOS EMPATADOS</th> <th>PUNTOS A FAVOR</th> <th>PUNTOS EN CONTRA</th> <th>DIFERENCIA</th>'; 
            echo '</tr>';
    
            while($row =  mysqli_fetch_array($result)){
                $nombre_equipo=$row['NOMBRE_EQUIPO'];
                $pj=$row['PARTIDOS_JUGADOS'];
                $pg=$row['PARTIDOS_GANADOS'];
                $pp=$row['PARTIDOS_PERDIDOS'];
                $pe=$row['PARTIDOS_EMPATADOS'];
                $pf=$row['PUNTOS_FAVOR'];
                $pc=$row['PUNTOS_CONTRA'];
                $dif=$row['DIFERENCIA'];
    
                echo '<tr class="success"><th>'.$nombre_equipo.'</th>';
                echo '<th>'.$pj.'</th>';
                echo '<th>'.$pg.'</th>';
                echo '<th>'.$pp.'</th>';
                echo '<th>'.$pe.'</th>';
                echo '<th>'.$pf.'</th>';
                echo '<th>'.$pc.'</th>';
                echo '<th>'.$dif.'</th></tr>';
            }
            
            echo '</table>';
        }
        else echo 1;
    }else echo 1;
    
    $conn->close();

?>