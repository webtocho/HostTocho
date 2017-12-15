<?php
session_start();
include("SRV_CONEXION.php");

    $db = new SRV_CONEXION();


switch ($_POST['tipo']){
    case "Obtener_nombre_equipo":
        $sql = sprintf("SELECT * FROM equipos WHERE ID_EQUIPO =" . $_POST['team']);
        $conexcion= $db->getConnection();
        $resultado=$conexcion->query($sql);
        $info=$resultado->fetch_assoc();
        if ($info['NOMBRE_EQUIPO']) {
			echo $info['NOMBRE_EQUIPO'];
         }
    break;
    
    case "Obtener_jugador_equipo":
        $ID_ROSTER;
         $sql = sprintf("SELECT * FROM rosters WHERE ID_EQUIPO =" . $_POST['team']);
          $conexcion= $db->getConnection();
        $resultado=$conexcion->query($sql);
        $info=$resultado->fetch_assoc();
        $ID_ROSTER=$info["ID_ROSTER"];
         $sql = sprintf(" select * from rosters inner join participantes_rosters on rosters.ID_ROSTER = participantes_rosters.ID_ROSTER inner join usuarios on participantes_rosters.ID_JUGADOR = usuarios.ID_USUARIO where ID_EQUIPO=" . $_POST['team']);
         $conexcion= $db->getConnection();
        $resultado=$conexcion->query($sql);
        
        $info=array();
         while ($row = $resultado->fetch_assoc()) {
            $info[] = $row;
        }
         $sql = sprintf("select count(*) from cedulas where ID_ROL_JUEGO=" . $_POST['ROL']);
          $resultado2=$conexcion->query($sql);
            $info2=$resultado2->fetch_assoc();
        $numero_de_filas=$info2["count(*)"];
        if($numero_de_filas==0){
        foreach ($info as $info2) {
	$sql = sprintf("insert into cedulas values(0,%s,%s,%s,0,0,0,0,0,0,0)",$_POST["ROL"],$info2["ID_JUGADOR"],$ID_ROSTER);
         $conexcion= $db->getConnection();
        $resultado=$conexcion->query($sql);
        }
       
        }  
         if (isset($_SESSION["TIPO_USUARIO"]) == 'CAPTURISTA' || isset($_SESSION["TIPO_USUARIO"]) == 'ADMINISTRADOR'){
            foreach ($info as $info2) { 
                $sql = sprintf(" select * from cedulas where ID_JUGADOR=" .$info2["ID_JUGADOR"]);
                 $conexcion= $db->getConnection();
                 $resultado3=$conexcion->query($sql);
                $info3=$resultado3->fetch_assoc();
                echo '<tr><th>'.$info2["NUMERO"]."-".$info2["NOMBRE"]." ".$info2["APELLIDO_PATERNO"]." ".$info2["APELLIDO_MATERNO"]."</th>".
                   "<th><input type='number' class='form-control' id='".$info2["ID_JUGADOR"]."T' maxlength='30' required  min='0' value='". $info3["T"]."' onclick='guardarT(this.id,".$info2["ID_JUGADOR"].")'></th>"
                ."<th><input type='number' class='form-control' id='".$info2["ID_JUGADOR"]."S' maxlength='30' value='". $info3["S"]."' onclick='guardarS(this.id,".$info2["ID_JUGADOR"].")' required></th>".
                 "<th><input type='number' class='form-control' id='".$info2["ID_JUGADOR"]."I' maxlength='30' value='". $info3["I"]."' onclick='guardarI(this.id,".$info2["ID_JUGADOR"].")' required></th>".
                "<th><input type='number' class='form-control' id='".$info2["ID_JUGADOR"]."A' maxlength='30' value='". $info3["A"]."'  onclick='guardarA(this.id,".$info2["ID_JUGADOR"].")'required></th>".
                "<th><input type='number' class='form-control' id='".$info2["ID_JUGADOR"]."C1' maxlength='30' value='". $info3["C1"]."' onclick='guardarC1(this.id,".$info2["ID_JUGADOR"].")' required></th>".
               "<th><input type='number' class='form-control'id='".$info2["ID_JUGADOR"]."C2' maxlength='30' value='". $info3["C2"]."' onclick='guardarC2(this.id,".$info2["ID_JUGADOR"].")' required ></th>".
              "<th><input type='number' class='form-control' id='".$info2["ID_JUGADOR"]."PT' maxlength='30' value='". $info3["PT"]."' onclick='guardarPT(this.id,".$info2["ID_JUGADOR"].")'required></th></tr>"; 
		}
         }else{
            foreach ($info as $info2) {  
                 $sql = sprintf(" select * from cedulas where ID_JUGADOR=" .$info2["ID_JUGADOR"]);
                 $conexcion= $db->getConnection();
                 $resultado3=$conexcion->query($sql);
                $info3=$resultado3->fetch_assoc();
		echo '<tr><th>'
                .$info2["NUMERO"]."-".$info2["NOMBRE"]." ".$info2["APELLIDO_PATERNO"]." ".$info2["APELLIDO_MATERNO"]
                 ."</th><th><input type='number' class='form-control'  maxlength='30' value='". $info3["T"]."' required disabled>"
                 ."</th><th><input type='number' class='form-control'  maxlength='30' value='".$info3["S"]."'  required disabled>"
                 ."</th><th><input type='number' class='form-control'  maxlength='30' value='".$info3["I"]."'  required disabled>"
                 ."</th><th><input type='number' class='form-control'  maxlength='30' value='".$info3["A"]."'  required disabled>"
                 ."</th><th><input type='number' class='form-control'  maxlength='30' value='".$info3["C1"]."' required disabled>"
                 ."</th><th><input type='number' class='form-control'  maxlength='30' value='".$info3["C2"]."' required disabled>"
                 ."</th><th><input type='number' class='form-control'  maxlength='30' value='".$info3["PT"]."' required disabled></th></tr>"; 
             }
        } 
       
        break;
    case "guardarT":
      $sql = sprintf(" UPDATE cedulas SET  T=%s where ID_JUGADOR=%s",$_POST["DATO"],$_POST["ID_USUARIO"]); 
      $conexcion= $db->getConnection();
      $resultado3=$conexcion->query($sql);
      echo $resultado3;
    break;
    case "guardarS":
      $sql = sprintf(" UPDATE cedulas SET  S=%s where ID_JUGADOR=%s",$_POST["DATO"],$_POST["ID_USUARIO"]); 
      $conexcion= $db->getConnection();
      $resultado3=$conexcion->query($sql);
      echo $resultado3;
    break;
    case "guardarI":
      $sql = sprintf(" UPDATE cedulas SET  I=%s where ID_JUGADOR=%s",$_POST["DATO"],$_POST["ID_USUARIO"]); 
      $conexcion= $db->getConnection();
      $resultado3=$conexcion->query($sql);
      echo $resultado3;
    break;
    case "guardarA":
      $sql = sprintf(" UPDATE cedulas SET  A=%s where ID_JUGADOR=%s",$_POST["DATO"],$_POST["ID_USUARIO"]); 
      $conexcion= $db->getConnection();
      $resultado3=$conexcion->query($sql);
      echo $resultado3;
    break;
    case "guardarC1":
      $sql = sprintf(" UPDATE cedulas SET  C1=%s where ID_JUGADOR=%s",$_POST["DATO"],$_POST["ID_USUARIO"]); 
      $conexcion= $db->getConnection();
      $resultado3=$conexcion->query($sql);
      echo $resultado3;
    break;
    case "guardarC2":
      $sql = sprintf(" UPDATE cedulas SET  C2=%s where ID_JUGADOR=%s",$_POST["DATO"],$_POST["ID_USUARIO"]); 
      $conexcion= $db->getConnection();
      $resultado3=$conexcion->query($sql);
      echo $resultado3;
    break;
    case "guardarPT":
      $sql = sprintf(" UPDATE cedulas SET  PT=%s where ID_JUGADOR=%s",$_POST["DATO"],$_POST["ID_USUARIO"]); 
      $conexcion= $db->getConnection();
      $resultado3=$conexcion->query($sql);
      echo $resultado3;
    break;
    case "GET_BOTON":
        
         if (isset($_SESSION["TIPO_USUARIO"]) == 'CAPTURISTA' || isset($_SESSION["TIPO_USUARIO"]) == 'ADMINISTRADOR'){
             echo " <center>"
                 ."<input type='submit' value='guardar datos' class='btn btn-default' onclick='llenar_rol_juego(".$_POST["ROL"].",".$_POST["TEAM1"].",".$_POST["TEAM2"].")'>"
                 ."</center>";
         }else{
              echo " <center>"
                    ."<input type='submit' value='guardar datos' class='btn btn-default' disabled>"
                    ."</center>";
         }
    break;
    case "GUARDAR_DATOS":
      $ID_ROSTER_TEAM_1;
      $ID_ROSTER_TEAM_2;
      $ID_TEAM_GANADOR;
      $PUNTOS_TEAM_1=0;
      $PUNTOS_TEAM_2=0;
      
      $sql = sprintf("SELECT * FROM rosters where ID_EQUIPO=%s",$_POST['TEAM1']); 
      $conexcion= $db->getConnection();  
      $resultado=$conexcion->query($sql);
      $info=$resultado->fetch_assoc();
      $ID_ROSTER_TEAM_1=$info["ID_ROSTER"];
      
      $sql = sprintf("SELECT * FROM rosters where ID_EQUIPO=%s",$_POST['TEAM2']); 
      $conexcion2= $db->getConnection();  
      $resultado2=$conexcion2->query($sql);
      $info2=$resultado2->fetch_assoc();
      $ID_ROSTER_TEAM_2=$info2["ID_ROSTER"];
      
       $sql = sprintf("SELECT * FROM cedulas WHERE ID_ROL_JUEGO =%s and ID_ROSTER=%s",$_POST['ID_ROL'],$ID_ROSTER_TEAM_1); 
        $conexcion3= $db->getConnection();
        $resultado3=$conexcion3->query($sql);
        $info3=array();
         while ($row = $resultado3->fetch_assoc()) {
            $info3[] = $row;
        }
        foreach ($info3 as $infoX) {
                   $PUNTOS_TEAM_1=$PUNTOS_TEAM_1+($infoX["S"]*2)+($infoX["I"]*2)+($infoX["A"]*6)+($infoX["C1"]*1)+($infoX["C2"]*2);
        }
        
        $sql = sprintf("SELECT * FROM cedulas WHERE ID_ROL_JUEGO =%s and ID_ROSTER=%s",$_POST['ID_ROL'],$ID_ROSTER_TEAM_2); 
        $conexcion4= $db->getConnection();
        $resultado4=$conexcion4->query($sql);
        $info4=array();
         while ($row = $resultado4->fetch_assoc()){
            $info4[] = $row;
        }
        foreach ($info4 as $infoY) {                
                   $PUNTOS_TEAM_2=$PUNTOS_TEAM_2+($infoY["S"]*2)+($infoY["I"]*2)+($infoY["A"]*6)+($infoY["C1"]*1)+($infoY["C2"]*2);
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
     echo "ok";
    break;
}

?>