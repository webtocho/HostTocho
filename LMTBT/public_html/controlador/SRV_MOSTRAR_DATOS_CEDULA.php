<?php
session_start();
include("SRV_CONEXION.php");
    $db = new SRV_CONEXION();


switch ($_POST['tipo']){
    case "Obtener_nombre_equipo":
        $sql = sprintf("SELECT * FROM equipos WHERE ID_EQUIPO =". $_POST['team']);
        $conexcion= $db->getConnection();
        $resultado=$conexcion->query($sql);
        $info=$resultado->fetch_assoc();
        if ($info['NOMBRE_EQUIPO']) {
			echo $info['NOMBRE_EQUIPO'];
         }
    break;
    
    case "Obtener_jugador_equipo":
        $ID_ROSTER;
        $numero_jugador;
        $Tabla_Jugadores= array();
            $sql = sprintf("SELECT * FROM rosters WHERE ID_EQUIPO=%s AND ID_CONVOCATORIA =%s",$_POST['team'],$_POST['ID_CONVOCSTORIA']);
          $conexcion= $db->getConnection();
        $resultado=$conexcion->query($sql);
        $info=$resultado->fetch_assoc();
        $ID_ROSTER=$info["ID_ROSTER"];
        $sql = sprintf("select * from rosters inner join participantes_rosters on rosters.ID_ROSTER = participantes_rosters.ID_ROSTER inner join usuarios on participantes_rosters.ID_JUGADOR = usuarios.ID_USUARIO where rosters.ID_EQUIPO=%s AND rosters.ID_ROSTER=%s ORDER BY participantes_rosters.NUMERO",$_POST['team'],$ID_ROSTER);
         $conexcion= $db->getConnection();
        $resultado=$conexcion->query($sql);
        
        $info=array();
         while ($row = $resultado->fetch_assoc()) {
            $info[] = $row;
        }
        
            $sql = sprintf("SELECT * FROM rosters WHERE ID_EQUIPO=%s AND ID_CONVOCATORIA =%s",$_POST['team'],$_POST['ID_CONVOCSTORIA']);
        $conexcion0= $db->getConnection();
        $resultado0=$conexcion0->query($sql);
        $info0=$resultado0->fetch_assoc();
         $sql = sprintf("select count(*) from cedulas where ID_ROL_JUEGO=%s AND ID_ROSTER=%s",$_POST['ROL'],$info0["ID_ROSTER"]);
          $resultado2=$conexcion->query($sql);
            $info2=$resultado2->fetch_assoc();
        $numero_de_filas=$info2["count(*)"];
        if($numero_de_filas==0){
        foreach ($info as $info2) {
	$sql = sprintf("insert into cedulas values(0,%s,%s,%s,0,0,0,0,0,0,0,0,0,0)",$_POST["ROL"],$info2["ID_JUGADOR"],$ID_ROSTER);
         $conexcion= $db->getConnection();
        $resultado=$conexcion->query($sql);
        }
       
        }  
         if (isset($_SESSION["TIPO_USUARIO"]) == 'CAPTURISTA' || isset($_SESSION["TIPO_USUARIO"]) == 'ADMINISTRADOR'){
             $numero_jugadores=0;
             $datos="<tr><th>JUGADOR</th><th> TACKLES </th><th> SACKS </th><th> INTERCEPCIONES </th><th>ANOTACIONES</th><th> CONVERSION 1 </th><th> CONVERSION 2 </th><th> CONVERSION 3 </th><th> PASE DE ANOTACION </th><th> SAFETY </th><th> INTERCEPCION </th></tr>";
             $retorno=Array("Resultado","jugdores","numero de jugadores");
            foreach ($info as $info2) {
                 $numero_jugadores++;
                $sql = sprintf(" select * from cedulas where ID_ROL_JUEGO=%s AND ID_JUGADOR=%s AND ID_ROSTER=%s",$_POST["ROL"],$info2["ID_JUGADOR"],$ID_ROSTER);
                 $conexcion= $db->getConnection();
                 $resultado3=$conexcion->query($sql);
                $info3=$resultado3->fetch_assoc();
                $jugador= array("ID_JUGADOR","T","S","I","A","C1","C2","C3","PA","SA","I4");
                $jugador[0]=$info3["ID_JUGADOR"];
                $jugador[1]=$info3["T"];
                $jugador[2]=$info3["S"];
                $jugador[3]=$info3["I"];
                $jugador[4]=$info3["A"];
                $jugador[5]=$info3["C1"];
                $jugador[6]=$info3["C2"];
                $jugador[7]=$info3["C3"];
                $jugador[8]=$info3["PA"];
                $jugador[9]=$info3["SA"];
                $jugador[10]=$info3["I4"];
                array_push($Tabla_Jugadores,$jugador);
                $datos=$datos."<tr><th>"
                 .$info2["NUMERO"]."-".$info2["NOMBRE"]." ".$info2["APELLIDO_PATERNO"]."</th>".
                 "<th><input type='text' class='form-control' id='".$info2["ID_JUGADOR"]."T' maxlength='30' value='".$info3["T"]."' required readonly='readonly'> <input type='submit' value='<' onclick='reduce(\"T\"".",\"".$info2["ID_JUGADOR"]."T\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'><input type='submit' value='>' onclick='add(\"T\"".",\"".$info2["ID_JUGADOR"]."T\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'></th>"
                ."<th><input type='text' class='form-control' id='".$info2["ID_JUGADOR"]."S' maxlength='30' value='".$info3["S"]."' required readonly='readonly'> <input type='submit' value='<' onclick='reduce(\"S\"".",\"".$info2["ID_JUGADOR"]."S\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'><input type='submit' value='>' onclick='add(\"S\"".",\"".$info2["ID_JUGADOR"]."S\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'></th>".
                 "<th><input type='text' class='form-control' id='".$info2["ID_JUGADOR"]."I' maxlength='30' value='".$info3["I"]."' required readonly='readonly'> <input type='submit' value='<' onclick='reduce(\"I\"".",\"".$info2["ID_JUGADOR"]."I\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'><input type='submit' value='>' onclick='add(\"I\"".",\"".$info2["ID_JUGADOR"]."I\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'></th>".
                 "<th><input type='text' class='form-control' id='".$info2["ID_JUGADOR"]."A' maxlength='30' value='".$info3["A"]."' required readonly='readonly'> <input type='submit' value='<' onclick='reduce(\"A\"".",\"".$info2["ID_JUGADOR"]."A\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'><input type='submit' value='>' onclick='add(\"A\"".",\"".$info2["ID_JUGADOR"]."A\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'></th>".
                 "<th><input type='text' class='form-control' id='".$info2["ID_JUGADOR"]."C1' maxlength='30' value='".$info3["C1"]."' required readonly='readonly'> <input type='submit' value='<' onclick='reduce(\"C1\"".",\"".$info2["ID_JUGADOR"]."C1\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'><input type='submit' value='>' onclick='add(\"C1\"".",\"".$info2["ID_JUGADOR"]."C1\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'></th>".
                 "<th><input type='text' class='form-control' id='".$info2["ID_JUGADOR"]."C2' maxlength='30' value='".$info3["C2"]."' required readonly='readonly'> <input type='submit' value='<' onclick='reduce(\"C2\"".",\"".$info2["ID_JUGADOR"]."C2\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'><input type='submit' value='>' onclick='add(\"C2\"".",\"".$info2["ID_JUGADOR"]."C2\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'></th>".
                 "<th><input type='text' class='form-control' id='".$info2["ID_JUGADOR"]."C3' maxlength='30' value='".$info3["C3"]."' required readonly='readonly'> <input type='submit' value='<' onclick='reduce(\"C3\"".",\"".$info2["ID_JUGADOR"]."C3\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'><input type='submit' value='>' onclick='add(\"C3\"".",\"".$info2["ID_JUGADOR"]."C3\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'></th>".
                 "<th><input type='text' class='form-control' id='".$info2["ID_JUGADOR"]."PA' maxlength='30' value='".$info3["PA"]."' required readonly='readonly'> <input type='submit' value='<' onclick='reduce(\"PA\"".",\"".$info2["ID_JUGADOR"]."PA\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'><input type='submit' value='>' onclick='add(\"PA\"".",\"".$info2["ID_JUGADOR"]."PA\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'></th>".
                 "<th><input type='text' class='form-control' id='".$info2["ID_JUGADOR"]."SA' maxlength='30' value='".$info3["SA"]."' required readonly='readonly'> <input type='submit' value='<' onclick='reduce(\"SA\"".",\"".$info2["ID_JUGADOR"]."SA\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'><input type='submit' value='>' onclick='add(\"SA\"".",\"".$info2["ID_JUGADOR"]."SA\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'></th>".
                 "<th><input type='text' class='form-control' id='".$info2["ID_JUGADOR"]."I4' maxlength='30' value='".$info3["I4"]."' required readonly='readonly'> <input type='submit' value='<' onclick='reduce(\"I4\"".",\"".$info2["ID_JUGADOR"]."I4\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'><input type='submit' value='>' onclick='add(\"I4\"".",\"".$info2["ID_JUGADOR"]."I4\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'></th></tr>"; 
		}
                $retorno[0]=$datos;
               $retorno[1]=$Tabla_Jugadores;
               $retorno[2]=$numero_jugadores;
                echo json_encode($retorno);
                 
         }else{
              $numero_jugadores=0;
            $datos="<tr><th>JUGADOR</th><th> TACKLES </th><th> SACKS </th><th> INTERCEPCIONES </th><th> ANOTACIONES </th><th> CONVERSION 1 </th><th> CONVERSION 2 </th><th> CONVERSION 3 </th><th> PASE DE ANOTACION </th><th> SAFETY </th><th> INTERCEPCION </th></tr>";
             $retorno=Array("Resultado","numero de jugadores");
            foreach ($info as $info2) {  
                 $sql = sprintf(" select * from cedulas where ID_JUGADOR=" .$info2["ID_JUGADOR"]);
                 $conexcion= $db->getConnection();
                 $resultado3=$conexcion->query($sql);
                $info3=$resultado3->fetch_assoc();
		$datos=$datos."<tr><th>"
                .$info2["NUMERO"]."-".$info2["NOMBRE"]." ".$info2["APELLIDO_PATERNO"]." "
                 ."</th><th><input type='number' class='form-control'  maxlength='30' value='". $info3["T"]."' required readonly='readonly'>"
                 ."</th><th><input type='number' class='form-control'  maxlength='30' value='".$info3["S"]."'  required readonly='readonly'>"
                 ."</th><th><input type='number' class='form-control'  maxlength='30' value='".$info3["I"]."'  required readonly='readonly'>"
                 ."</th><th><input type='number' class='form-control'  maxlength='30' value='".$info3["A"]."'  required readonly='readonly'>"
                 ."</th><th><input type='number' class='form-control'  maxlength='30' value='".$info3["C1"]."' required readonly='readonly'>"
                 ."</th><th><input type='number' class='form-control'  maxlength='30' value='".$info3["C2"]."' required readonly='readonly'>"
                 ."</th><th><input type='number' class='form-control'  maxlength='30' value='".$info3["C3"]."' required readonly='readonly'>"
                 ."</th><th><input type='number' class='form-control'  maxlength='30' value='".$info3["PA"]."' required readonly='readonly'>"
                 ."</th><th><input type='number' class='form-control'  maxlength='30' value='".$info3["SA"]."' required readonly='readonly'></th>"
                 ."</th><th><input type='number' class='form-control'  maxlength='30' value='".$info3["I4"]."' required readonly='readonly'>"
                 ."</tr>"; 
             }
                $retorno[0]=$datos;
                $retorno[1]=$numero_jugadores;
                echo json_encode($retorno);
             
        } 
       
        break;
    case "GET_BOTON":
        
         if (isset($_SESSION["TIPO_USUARIO"]) == 'CAPTURISTA' || isset($_SESSION["TIPO_USUARIO"]) == 'ADMINISTRADOR'){
             echo " <center>"
                 ."<input type='submit' class='btn btn-primary' value='Guardar datos' onclick='llenar_rol_juego(".$_POST["ROL"].",".$_POST["TEAM1"].",".$_POST["TEAM2"].")'>"
                 ."</center><br>";
         }else{
              echo " <center>"
                    ."<input type='submit' class='btn btn-primary' value='Guardar datos' class='btn btn-default' disabled>"
                    ."</center><br>";
         }
    break;
    case "GUARDAR_DATOS":
      $ID_ROSTER_TEAM_1;
      $ID_ROSTER_TEAM_2;
      $ID_TEAM_GANADOR;
      $PUNTOS_TEAM_1=0;
      $PUNTOS_TEAM_2=0;
      $i;
      $numero_de_jugadores_team1=$_POST['NumeroDeIntegrasteDelEquipo1'];
      $numero_de_jugadores_team2=$_POST['NumeroDeIntegrasteDelEquipo1'];
      $Tabla_Jugadores_Team1=$_POST['TablaTeam1'];
      $Tabla_Jugadores_Team2=$_POST['TablaTeam2'];
      
      $sql = sprintf("SELECT * FROM rosters where ID_EQUIPO=%s AND ID_CONVOCATORIA=%s",$_POST['TEAM1'],$_POST['ID_CONVOCSTORIA']); 
      $conexcion= $db->getConnection();  
      $resultado=$conexcion->query($sql);
      $info=$resultado->fetch_assoc();
      $ID_ROSTER_TEAM_1=$info["ID_ROSTER"];
      $sql = sprintf("SELECT * FROM rosters where ID_EQUIPO=%s AND ID_CONVOCATORIA=%s",$_POST['TEAM2'],$_POST['ID_CONVOCSTORIA']); 
      $conexcion2= $db->getConnection();  
      $resultado2=$conexcion2->query($sql);
      $info2=$resultado2->fetch_assoc();
      $ID_ROSTER_TEAM_2=$info2["ID_ROSTER"];
      
      
      $conexciones= $db->getConnection();
      for($i=0;$i<$numero_de_jugadores_team1;$i++){
          $ID_DEL_JUGADOR=(int)$Tabla_Jugadores_Team1[$i][0];
          $T=(int)$Tabla_Jugadores_Team1[$i][1];
          $S=(int)$Tabla_Jugadores_Team1[$i][2];
          $I=(int)$Tabla_Jugadores_Team1[$i][3];
          $A=(int)$Tabla_Jugadores_Team1[$i][4];
          $C1=(int)$Tabla_Jugadores_Team1[$i][5];
          $C2=(int)$Tabla_Jugadores_Team1[$i][6];
          $C3=(int)$Tabla_Jugadores_Team1[$i][7];
          $PA=(int)$Tabla_Jugadores_Team1[$i][8];
          $SA=(int)$Tabla_Jugadores_Team1[$i][9]; 
          $I4=(int)$Tabla_Jugadores_Team1[$i][10];
        
          $consulta=$conexciones->prepare("UPDATE cedulas SET T=?,S=?,I=?,A=?,C1=?,C2=?,C3=?,PA=?,SA=?,I4=? WHERE  ID_ROL_JUEGO=? AND ID_JUGADOR=? AND ID_ROSTER=?");
          $consulta->bind_param("iiiiiiiiiiiii",$T,$S,$I,$A,$C1,$C2,$C3,$PA,$SA,$I4,$_POST['ID_ROL'],$ID_DEL_JUGADOR,$ID_ROSTER_TEAM_1);
          if($consulta->execute()){
               
            }else{                                                                                   
           echo "no";
           die();
            }
      }
        
       for($i=0;$i<$numero_de_jugadores_team2;$i++){
          $ID_DEL_JUGADOR=(int)$Tabla_Jugadores_Team2[$i][0];
          $T=(int)$Tabla_Jugadores_Team2[$i][1];
          $S=(int)$Tabla_Jugadores_Team2[$i][2];
          $I=(int)$Tabla_Jugadores_Team2[$i][3];
          $A=(int)$Tabla_Jugadores_Team2[$i][4];
          $C1=(int)$Tabla_Jugadores_Team2[$i][5];
          $C2=(int)$Tabla_Jugadores_Team2[$i][6];
          $C3=(int)$Tabla_Jugadores_Team2[$i][7];
          $PA=(int)$Tabla_Jugadores_Team2[$i][8];
          $SA=(int)$Tabla_Jugadores_Team2[$i][9]; 
          $I4=(int)$Tabla_Jugadores_Team2[$i][10];
         
          $consulta=$conexciones->prepare("UPDATE cedulas SET T=?,S=?,I=?,A=?,C1=?,C2=?,C3=?,PA=?,SA=?,I4=? WHERE  ID_ROL_JUEGO=? AND ID_JUGADOR=? AND ID_ROSTER=?");
           $consulta->bind_param("iiiiiiiiiiiii",$T,$S,$I,$A,$C1,$C2,$C3,$PA,$SA,$I4,$_POST['ID_ROL'],$ID_DEL_JUGADOR,$ID_ROSTER_TEAM_2);
          if($consulta->execute()){
              
            }else{                                                                                   
           echo "no";
          die();
            }
      }
      
      
      
      
      
      
       $sql = sprintf("SELECT * FROM cedulas WHERE ID_ROL_JUEGO =%s and ID_ROSTER=%s",$_POST['ID_ROL'],$ID_ROSTER_TEAM_1); 
        $conexcion3= $db->getConnection();
        $resultado3=$conexcion3->query($sql);
        $info3=array();
         while ($row = $resultado3->fetch_assoc()) {
            $info3[] = $row;
        }
        foreach ($info3 as $infoX) {
                   $PUNTOS_TEAM_1=$PUNTOS_TEAM_1+($infoX["A"]*6)+($infoX["C1"]*1)+($infoX["C2"]*2)+($infoX["C3"]*3)+($infoX["SA"]*2)+($infoX["I4"]*4);
        }
        
        $sql = sprintf("SELECT * FROM cedulas WHERE ID_ROL_JUEGO =%s and ID_ROSTER=%s",$_POST['ID_ROL'],$ID_ROSTER_TEAM_2); 
        $conexcion4= $db->getConnection();
        $resultado4=$conexcion4->query($sql);
        $info4=array();
         while ($row = $resultado4->fetch_assoc()){
            $info4[] = $row;
        }
        foreach ($info4 as $infoY) {                
                   $PUNTOS_TEAM_2=$PUNTOS_TEAM_2+($infoY["A"]*6)+($infoY["C1"]*1)+($infoY["C2"]*2)+($infoY["C3"]*3)+($infoY["SA"]*2)+($infoY["I4"]*4);
        }
        
        if($PUNTOS_TEAM_1>$PUNTOS_TEAM_2){
            $ID_TEAM_GANADOR=$_POST["TEAM1"];
        }else if($PUNTOS_TEAM_1<$PUNTOS_TEAM_2){
             $ID_TEAM_GANADOR=$_POST["TEAM2"];
        }else if($PUNTOS_TEAM_1==$PUNTOS_TEAM_2){
           $ID_TEAM_GANADOR=0; 
        }
        
      $sql = sprintf("UPDATE roles_juego SET ID_EQUIPO_1=%s, ID_EQUIPO_2=%s, ID_EQUIPO_GANADOR=%s, PUNTOS_EQUIPO_1=%s, PUNTOS_EQUIPO_2=%s WHERE ID_ROL_JUEGO=%s",$_POST['TEAM1'],$_POST['TEAM2'],$ID_TEAM_GANADOR,$PUNTOS_TEAM_1,$PUNTOS_TEAM_2,$_POST['ID_ROL']); 
      $conexcion5= $db->getConnection();  
      $resultado5=$conexcion5->query($sql);
     echo true;
    break;
    case "ComprobarLogin":
         if (!empty($_SESSION["ID_USUARIO"]) && !empty($_SESSION["TIPO_USUARIO"])){
             echo"si";
            
         }else{
             echo "no";
           
         }
    break;
}

?>
