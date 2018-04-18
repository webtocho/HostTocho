<?php
//ACTUALIZAR LA TABLA ESTADISTICAS
//OBTENER TODA LA INFO DE TABLA ROLES_JUEGO
//ESTADISTICAS VALUE(PARTIDOS_JUGADOS,PARTIDOS_GANADOS,PARTIDOS_PERDIDOS,PARTIDOS_EMPATADOS,PUNTOS_FAVOR,PUNTOS_CONTRA,DIFERENCIA,ID_CONVOCATORIA,ID_EQUIPO)
require 'SRV_CONEXION.php';
$db = new SRV_CONEXION();
$conn = $db->getConnection();

$id_convocatoria=$_POST['convocatoria'];
//primero obtenemos los equipos inscritos a una convocatoria, de rosters puesto que el roster ya esta ligado al equipo
$stmt="SELECT * FROM rosters WHERE ID_CONVOCATORIA=".$id_convocatoria;

$result = $conn->query($stmt);
        //comprobamos si hay equipos
	if($result&&mysqli_num_rows($result)>0){
                    $equipos=array();
                    $total=0;
                //Obtnemos todos los equipos inscritos en una convocatoria
            while($row = mysqli_fetch_array($result)){
                    $equipos[]=$row['ID_EQUIPO'];
                //comprobamos cada equipo si ya esta registrado en la tabla de posiciones
                $comprobamos = "SELECT * FROM tabla_posiciones WHERE ID_EQUIPO=".$row['ID_EQUIPO']." AND ID_CONVOCATORIA=".$id_convocatoria;
                $resultadoComprobar = $conn->query($comprobamos);
                echo "";
                if($resultadoComprobar&&$resultadoComprobar->num_rows>0){
                    //si un equipo ya esta registrado en la tabla posiciones no hacemos nada mas
                }
                else{
                    //si un equipo no esta en la tabla lo agregamos, el filtro en la tabla es por convocatoria

                    $insertar="INSERT INTO tabla_posiciones VALUES(0,0,0,0,0,0,0,".$id_convocatoria.",".$row['ID_EQUIPO'].")";
                    $conn->query($insertar);
                }
                    //incrementamos el total de equipos registrados en la convocatoria
                    $total++;
                
            }
            mysqli_free_result($result);
            //Sacamos los puntajes de los partidos (ya jugados) de un equipo
                $empate=0;
            for($i=0;$i<$total;$i++){
                $id_equipo=$equipos[$i];

                $query2="SELECT * FROM roles_juego WHERE ID_CONVOCATORIA=".$id_convocatoria." AND ( ID_EQUIPO_1 = ". $id_equipo ." OR ID_EQUIPO_2 = ".$id_equipo." ) AND ID_EQUIPO_GANADOR>-1";
                //query para obtener todos los partidos jugados de un equipo
                $result2 = $conn->query($query2);
                    //Comprobamos si ya se jugo al menos un partido
                echo "";
                    if($result2&&$result2->num_rows>0){
                            $pj=mysqli_num_rows($result2);
                            $pg=0;
                            $pp=0;
                            $pe=0;
                            $pf=0;
                            $pc=0;
                            $dif=0;
                            $flag=0;
                            //Sacamos todos los valores para la Tabla Posiciones
                            while($row2 = mysqli_fetch_array($result2)){
                            $id_equipo_ganador=$row2['ID_EQUIPO_GANADOR'];
                            if($id_equipo_ganador==$equipos[$i]) {$pg++;}//partidos ganados
                            else if($id_equipo_ganador==$empate) {$pe++;}    //partidos empatados
                            else if($id_equipo_ganador!=$id_equipo&&$id_equipo_ganador>$empate)  {$pp++;}                                  //partidos perdidos
                            if($row2['ID_EQUIPO_1']==$id_equipo){         //comprobamos si en el rol de juegos el equipo era el 1 o el 2
                                $pf=$pf+$row2['PUNTOS_EQUIPO_1'];             //si era el equipo 1 se acumulan los goles_equipo_1 como puntos favor
                                $pc=$pc+$row2['PUNTOS_EQUIPO_2'];             //y los goles_equipo_2 como puntos contra
                            }
                            else{
                                $pf=$pf+$row2['PUNTOS_EQUIPO_2'];             //si era el equipo 2 se acumulan los goles_equipo 2 como puntos favor
                                $pc=$pc+$row2['PUNTOS_EQUIPO_1'];             // y los goles_equipo_1 como puntos contra
                            }
                            $dif=$pf-$pc; 
                            }
                            //Actualizamos los valores en Tabla Posiciones

                    $query3 = "UPDATE tabla_posiciones SET PARTIDOS_JUGADOS =". $pj." , PARTIDOS_GANADOS=".$pg." , PARTIDOS_PERDIDOS = ".$pp." , PARTIDOS_EMPATADOS = ".$pe." , PUNTOS_FAVOR = ".$pf.", PUNTOS_CONTRA = ".$pc.", DIFERENCIA = ".$dif." WHERE ID_CONVOCATORIA = ".$id_convocatoria." AND ID_EQUIPO = ".$id_equipo;
                    $conn->query($query3);

                    }
            }
            echo "Tabla de posiciones actualizadas";
	}
	else{
            //es que no hay equipos registrados en una convocatorias
            echo "Error no se ha podido actualizar la Tabla de Posiciones";
	}	
$conn->close();
?>