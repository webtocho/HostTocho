<?php
//ACTUALIZAR LA TABLA ESTADISTICAS
//OBTENER TODA LA INFO DE TABLA ROLES_JUEGO
//ESTADISTICAS VALUE(PARTIDOS_JUGADOS,PARTIDOS_GANADOS,PARTIDOS_PERDIDOS,PARTIDOS_EMPATADOS,PUNTOS_FAVOR,PUNTOS_CONTRA,DIFERENCIA,ID_CONVOCATORIA,ID_EQUIPO)
$conn = new mysqli("localhost","id3551892_team","tochoweb", "id3551892_tochoweb");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$id_convocatoria=$_POST['convocatoria'];

$stmt="SELECT * FROM rosters WHERE ID_CONVOCATORIA=".$id_convocatoria;

$result = $conn->query($stmt);

	if($result&&mysqli_num_rows($result)>0){
		$equipos=array();
		$total=0;
		//Obtnemos todos los equipos inscritos en una convocatoria
    	while($row = mysqli_fetch_array($result)){
    		$equipos[]=$row['ID_EQUIPO'];

            $comprobamos = "SELECT * FROM estadisticas WHERE ID_EQUIPO=".$row['ID_EQUIPO'];
            $resultadoComprobar = $conn->query($comprobamos);

            if($resultadoComprobar && mysqli_num_rows($resultadoComprobar)){

            }
            else{
                $insertar="INSERT INTO estadisticas VALUES(0,0,0,0,0,0,0,".$id_convocatoria.",".$row['ID_EQUIPO'].")";
                $conn->query($insertar);
            }

    		$total++;
    	}
    	//Query para obtener todos los partidos (ya jugados) de un equipo
            $empate=0;
    	for($i=0;$i<$total;$i++){
            $id_equipo=$equipos[$i];

            $query2="SELECT * FROM roles_juego WHERE ID_CONVOCATORIA=$id_convocatoria AND (ID_EQUIPO_1 = $id_equipo OR ID_EQUIPO_2 = $id_equipo) AND ID_EQUIPO_GANADOR>-1";

            $result2 = $conn->query($query2);
    		//Comprobamos si ya se jugo al menos un partido
            //if($result2)
    		if($result2&&mysqli_num_rows($result2)>0){
                $pj=mysqli_num_rows($result2);
    			$pg=0;
    			$pp=0;
    			$pe=0;
    			$pf=0;
    			$pc=0;
    			$dif=0;
                $flag=0;
    			//Sacamos todos los valores para la tabla ESTADISTICAS
                
    			while($row2 = mysqli_fetch_array($result2)){
                        $id_equipo_ganador=$row2['ID_EQUIPO_GANADOR'];
                        if($id_equipo_ganador==$equipos[$i]) {$pg++;}//partidos ganados
                        else if($id_equipo_ganador==$empate) {$pe++;}    //partidos empatados
                        else if($id_equipo_ganador!=$id_equipo&&$id_equipo_ganador>$empate)  {$pp++;}                                  //partidos perdidos
                        if($row2['ID_EQUIPO_1']==$id_equipo){         //comprobamos si en el rol de juegos el equipo era el 1 o el 2
                            $pf=$pf+$row2['GOLES_EQUIPO_1'];             //si era el equipo 1 se acumulan los goles_equipo_1 como puntos favor
                            $pc=$pc+$row2['GOLES_EQUIPO_2'];             //y los goles_equipo_2 como puntos contra
                        }
                        else{
                            $pf=$pf+$row2['GOLES_EQUIPO_2'];             //si era el equipo 2 se acumulan los goles_equipo 2 como puntos favor
                            $pc=$pc+$row2['GOLES_EQUIPO_1'];             // y los goles_equipo_1 como puntos contra
                        }
                        $dif=$pf-$pc; 
    			}
    			//actualizamos los valores en estadistica

                $query3 = "UPDATE estadisticas SET PARTIDOS_JUGADOS = $pj , PARTIDOS_GANADOS=$pg , PARTIDOS_PERDIDOS = $pp , PARTIDOS_EMPATADOS = $pe , PUNTOS_FAVOR = $pf, PUNTOS_CONTRA = $pc, DIFERENCIA = $dif WHERE ID_CONVOCATORIA = $id_convocatoria AND ID_EQUIPO = $id_equipo";
                $conn->query($query3);
                
    		}
    	}
        echo "Estadisticas actualizadas";
	}
	else{
        echo "Error no se ha podido actualizar las estadisticas";
	}	
$conn->close();
?>