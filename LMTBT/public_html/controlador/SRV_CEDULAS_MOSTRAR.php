<?php
//iniciamos la variable de session
session_start();
//incluimos el php donde se estable la conexion
include("SRV_CONEXION.php");
//instanciamos la clase SRV_CONEXION 
    $db = new SRV_CONEXION();

//establecemos un switch para cada peticion del js_mostar_datos_cedulas
switch ($_POST['tipo']){
    //en el caso que el js desea obtener el nombre del equipo
    case "Obtener_nombre_equipo":
        //preparamos una sentencia sql para la peticion
        $sql = sprintf("SELECT * FROM equipos WHERE ID_EQUIPO =". $_POST['team']);
        //obtenemos la conexion de la clase SRV_CONEXION
        $conexcion= $db->getConnection();
        //ponemos la sentencia y la ejecutamos
        $resultado=$conexcion->query($sql);
        //recuperamos lo que nos devolvio la peticion
        $info=$resultado->fetch_assoc();
        //evaluamos que la peticcion no sea vacia
        if ($info['NOMBRE_EQUIPO']) {
            //retornamos el nombre del equipo
		echo $info['NOMBRE_EQUIPO'];
         }
    break;
    //en el caso que el js desea obtener los jugadores del equipo
    case "Obtener_jugador_equipo":
        //variable que guardara la ID del roster
        $ID_ROSTER;
        //vriable que gurdara el numero de jugadores
        $numero_jugador;
        //array que hara la funcion de tabla, y guardara un array en casa posicion, dicho array guardara los datos de cada jugador
        $Tabla_Jugadores= array();
        //preparamos la sentencia para solicitar el ID del roster del equipo
        $sql = sprintf("SELECT * FROM rosters WHERE ID_EQUIPO=%s AND ID_CONVOCATORIA =%s",$_POST['team'],$_POST['ID_CONVOCSTORIA']);
        //obtenemos la conexion de la clase SRV_CONEXION
        $conexcion= $db->getConnection();
        //ponemos y ejecutamos la sentencia y guardamos la respuesta en la variable $resultado
        $resultado=$conexcion->query($sql);
        //obtemos la fila de la sentencia
        $info=$resultado->fetch_assoc();
        //guardamos el ID del roster
        $ID_ROSTER=$info["ID_ROSTER"];
        //preparamos la sentencia para obtener a los jugadores del equipo
        $sql = sprintf("select * from rosters inner join participantes_rosters on rosters.ID_ROSTER = participantes_rosters.ID_ROSTER inner join usuarios on participantes_rosters.ID_JUGADOR = usuarios.ID_USUARIO where rosters.ID_EQUIPO=%s AND rosters.ID_ROSTER=%s ORDER BY participantes_rosters.NUMERO",$_POST['team'],$ID_ROSTER);
         //obtenemos la conexion de la clase SRV_CONEXION 
        $conexcion= $db->getConnection();
         //ponemos y ejecutamos la sentencia y guardamos la respuesta en la variable $resultado
        $resultado=$conexcion->query($sql);
        //como la peticion nos delvovio varias columnas, las guardaremos en un array
        $info=array();
        //recorremos las columnas de la peticion
         while ($row = $resultado->fetch_assoc()) {
             //guardamos cada columna en el array
            $info[] = $row;
        }
       //preparamos la sentencia para solicitar el ID del roster del equipo
        $sql = sprintf("SELECT * FROM rosters WHERE ID_EQUIPO=%s AND ID_CONVOCATORIA =%s",$_POST['team'],$_POST['ID_CONVOCSTORIA']);
        //obtenemos la conexion de la clase SRV_CONEXION 
        $conexcion0= $db->getConnection();
         //ponemos y ejecutamos la sentencia y guardamos la respuesta en la variable $resultado0
        $resultado0=$conexcion0->query($sql);
         //obtemos la fila de la sentencia
        $info0=$resultado0->fetch_assoc();
        //preparamos la sentencia que nos indicara si el roster esta creado o no
         $sql = sprintf("select count(*) from cedulas where ID_ROL_JUEGO=%s AND ID_ROSTER=%s",$_POST['ROL'],$info0["ID_ROSTER"]);
          //ponemos y ejecutamos la sentencia y guardamos la respuesta en la variable $resultado2
          $resultado2=$conexcion->query($sql);
          //obtemos la fila de la sentencia
          $info2=$resultado2->fetch_assoc();
          //recuperamos lo que contó la peticion, si nos devuelve o, no indica que la cedula no esta creada,de caso contrario esta creada
        $numero_de_filas=$info2["count(*)"];
        //verificamos si la cedula no esta creada, si se obtuvo un conteo de 0, se creara la cedula
        if($numero_de_filas==0){
         //recorremos la variable donde guardamos a los jugadores y creamos los datos de cada jugador
        foreach ($info as $info2) {
        //preparamos la sentencia que creara los datos de cada jugagor
	$sql = sprintf("insert into cedulas values(0,%s,%s,%s,0,0,0,0,0,0,0,0,0,0)",$_POST["ROL"],$info2["ID_JUGADOR"],$ID_ROSTER);
        //obtenemos la conexion de la clase SRV_CONEXION 
         $conexcion= $db->getConnection();
         //ponemos y ejecutamos la sentencia
        $resultado=$conexcion->query($sql);
        }
       
        }  
        //verificamos si el usuario que esta accediendo a la cedula es un administrador_capturistas(solo ellos pueden modificar las cedulas)
       // si el usuario no es capturista o administrador, inavilitamos la tabla para que no sea modificable
         if (isset($_SESSION["TIPO_USUARIO"]) == 'CAPTURISTA' || isset($_SESSION["TIPO_USUARIO"]) == 'ADMINISTRADOR'){
             //variable que ontendra el numero de jugadores
             $numero_jugadores=0;
             //esta variable nos servira para indicar en la tabla que datos corresponden los inputs
             $datos="<tr><th>JUGADOR</th><th> TACKLES </th><th> SACKS </th><th> INTERCEPCIONES </th><th>ANOTACIONES</th><th> CONVERSION 1 </th><th> CONVERSION 2 </th><th> CONVERSION 3 </th><th> PASE DE ANOTACION </th><th> SAFETY </th><th> INTERCEPCION </th></tr>";
             //Creamos un array que delvolvera al js, la tabla de los judaores(visual), la tabla de los jugadores(datos), y el numero de jugadores
             $retorno=Array("Resultado","jugdores","numero de jugadores");
             //recorremos la variable donde guardamos a los jugadores
             foreach ($info as $info2) {
                 //aumentamos en uno a la variable que contendra el numero de jugadores del equipo
                 $numero_jugadores++;
                 //sentencia que obtendra los datos de cada jugador
                $sql = sprintf(" select * from cedulas where ID_ROL_JUEGO=%s AND ID_JUGADOR=%s AND ID_ROSTER=%s",$_POST["ROL"],$info2["ID_JUGADOR"],$ID_ROSTER);
                //obtenemos la conexion de la clase SRV_CONEXION 
                $conexcion= $db->getConnection();
                //ponemos y ejecutamos la sentencia
                $resultado3=$conexcion->query($sql);
                //guardamos los datos en la variale $info3
                $info3=$resultado3->fetch_assoc();
                //creamos un array que contendra los datos del jugador
                $jugador= array("ID_JUGADOR","T","S","I","A","C1","C2","C3","PA","SA","I4");
                //almacenamos los datos que nos delvovio la paticion en cada espacion del arreglo que contendra los datos de los jugadores
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
                //verificamos que el jugador se incluyo despues de crear el roster, si el jugador se añadio despues de crear el roster
                //aparecera en la cedula, pero tendra datos nulos, asi que le crearemos dicho datos
              if($info3["T"]==null || $info3["S"]==null || $info3["I"]==null || $info3["A"]==null || $info3["C1"]==null ||
                  $info3["C2"]==null || $info3["C3"]==null ||$info3["PA"]==null || $info3["SA"]==null || $info3["I4"]==null){
                   //obtenemos la conexion de la clase SRV_CONEXION 
                   $conexciones= $db->getConnection();  
                   //preparamos la consulta para crear los datos del nuevo jugador
                  $consulta=$conexciones->prepare("INSERT INTO cedulas values(0,?,?,?,0,0,0,0,0,0,0,0,0,0)");
                  //indicamos los valores de la sentencia
                    $consulta->bind_param("iii",$_POST['ROL'],$info2["ID_JUGADOR"],$info0["ID_ROSTER"]);
                    //comprobamos si se hizo la ejeccion
                    if($consulta->execute()){
                        //sentencia que recuperara los datos creados del nuevo jugador
                   $sql = sprintf(" select * from cedulas where ID_ROL_JUEGO=%s AND ID_JUGADOR=%s AND ID_ROSTER=%s",$_POST["ROL"],$info2["ID_JUGADOR"],$info0["ID_ROSTER"]);
                   //obtenemos la conexion de la clase SRV_CONEXION 
                    $conexcions= $db->getConnection();
                    //ejecutamos la sentencia y guardamos el redultado en $resultado7
                    $resultado7=$conexcions->query($sql);
                    //recuperamos la fila de la respuesta
                    $info7=$resultado7->fetch_assoc();
                    //almacenamos los datos que nos delvovio la paticion en cada espacion del arreglo que contendra los datos de los jugadores
                $jugador[1]=$info7["T"];
                $jugador[2]=$info7["S"];
                $jugador[3]=$info7["I"];
                $jugador[4]=$info7["A"];
                $jugador[5]=$info7["C1"];
                $jugador[6]=$info7["C2"];
                $jugador[7]=$info7["C3"];
                $jugador[8]=$info7["PA"];
                $jugador[9]=$info7["SA"];
                $jugador[10]=$info7["I4"];
                    }else{  
                        //si no se pudo realizar la ejecion, enviamos un "no", y dejamos de ejecutar el php con la funcion "die()"
                    echo "no";
                    die();
                    }
                }
                //insertamos en la tabla el array del jugador con sus datos
                array_push($Tabla_Jugadores,$jugador);
                //preparamos el array de la tabla del jugador con sus datos(visual)
                $datos=$datos."<tr><th>"
                 .$info2["NUMERO"]."-".$info2["NOMBRE"]." ".$info2["APELLIDO_PATERNO"]."</th>".
                 "<th><input type='text' class='form-control' id='".$info2["ID_JUGADOR"]."T' maxlength='30' value='".$jugador[1]."' required readonly='readonly'> <input type='submit' value='<' onclick='reduce(\"T\"".",\"".$info2["ID_JUGADOR"]."T\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'><input type='submit' value='>' onclick='add(\"T\"".",\"".$info2["ID_JUGADOR"]."T\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'></th>"
                ."<th><input type='text' class='form-control' id='".$info2["ID_JUGADOR"]."S' maxlength='30' value='".$jugador[2]."' required readonly='readonly'> <input type='submit' value='<' onclick='reduce(\"S\"".",\"".$info2["ID_JUGADOR"]."S\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'><input type='submit' value='>' onclick='add(\"S\"".",\"".$info2["ID_JUGADOR"]."S\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'></th>".
                 "<th><input type='text' class='form-control' id='".$info2["ID_JUGADOR"]."I' maxlength='30' value='".$jugador[3]."' required readonly='readonly'> <input type='submit' value='<' onclick='reduce(\"I\"".",\"".$info2["ID_JUGADOR"]."I\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'><input type='submit' value='>' onclick='add(\"I\"".",\"".$info2["ID_JUGADOR"]."I\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'></th>".
                 "<th><input type='text' class='form-control' id='".$info2["ID_JUGADOR"]."A' maxlength='30' value='".$jugador[4]."' required readonly='readonly'> <input type='submit' value='<' onclick='reduce(\"A\"".",\"".$info2["ID_JUGADOR"]."A\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'><input type='submit' value='>' onclick='add(\"A\"".",\"".$info2["ID_JUGADOR"]."A\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'></th>".
                 "<th><input type='text' class='form-control' id='".$info2["ID_JUGADOR"]."C1' maxlength='30' value='".$jugador[5]."' required readonly='readonly'> <input type='submit' value='<' onclick='reduce(\"C1\"".",\"".$info2["ID_JUGADOR"]."C1\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'><input type='submit' value='>' onclick='add(\"C1\"".",\"".$info2["ID_JUGADOR"]."C1\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'></th>".
                 "<th><input type='text' class='form-control' id='".$info2["ID_JUGADOR"]."C2' maxlength='30' value='".$jugador[6]."' required readonly='readonly'> <input type='submit' value='<' onclick='reduce(\"C2\"".",\"".$info2["ID_JUGADOR"]."C2\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'><input type='submit' value='>' onclick='add(\"C2\"".",\"".$info2["ID_JUGADOR"]."C2\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'></th>".
                 "<th><input type='text' class='form-control' id='".$info2["ID_JUGADOR"]."C3' maxlength='30' value='".$jugador[7]."' required readonly='readonly'> <input type='submit' value='<' onclick='reduce(\"C3\"".",\"".$info2["ID_JUGADOR"]."C3\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'><input type='submit' value='>' onclick='add(\"C3\"".",\"".$info2["ID_JUGADOR"]."C3\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'></th>".
                 "<th><input type='text' class='form-control' id='".$info2["ID_JUGADOR"]."PA' maxlength='30' value='".$jugador[8]."' required readonly='readonly'> <input type='submit' value='<' onclick='reduce(\"PA\"".",\"".$info2["ID_JUGADOR"]."PA\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'><input type='submit' value='>' onclick='add(\"PA\"".",\"".$info2["ID_JUGADOR"]."PA\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'></th>".
                 "<th><input type='text' class='form-control' id='".$info2["ID_JUGADOR"]."SA' maxlength='30' value='".$jugador[9]."' required readonly='readonly'> <input type='submit' value='<' onclick='reduce(\"SA\"".",\"".$info2["ID_JUGADOR"]."SA\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'><input type='submit' value='>' onclick='add(\"SA\"".",\"".$info2["ID_JUGADOR"]."SA\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'></th>".
                 "<th><input type='text' class='form-control' id='".$info2["ID_JUGADOR"]."I4' maxlength='30' value='".$jugador[10]."' required readonly='readonly'> <input type='submit' value='<' onclick='reduce(\"I4\"".",\"".$info2["ID_JUGADOR"]."I4\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'><input type='submit' value='>' onclick='add(\"I4\"".",\"".$info2["ID_JUGADOR"]."I4\",".$info2["ID_JUGADOR"].",\"".$_POST['TIPO']."\")'></th></tr>"; 
		}
               //insertamos en el array que devolveremos al js la tabla de los jugadores(visual)
                $retorno[0]=$datos;
                //insertamos en el array que devolveremos al js la tabla de los jugadores(datos)
               $retorno[1]=$Tabla_Jugadores;
               //insertamos en el array que devolveremos al js el numero de jugadores del equipo
               $retorno[2]=$numero_jugadores;
               //encriptamos el array en un json para poderlo enviar al js
                echo json_encode($retorno);
                 
         }else{
             //en caso que el usuario no sea administrador ni capturista solo se enviara la tabla de los jugadores(visual) y el numero de jugadores
             
             //variable que contendra el numero de jugadores
              $numero_jugadores=0;
               //esta variable nos servira para indicar en la tabla que datos corresponden los inputs
            $datos="<tr><th>JUGADOR</th><th> TACKLES </th><th> SACKS </th><th> INTERCEPCIONES </th><th> ANOTACIONES </th><th> CONVERSION 1 </th><th> CONVERSION 2 </th><th> CONVERSION 3 </th><th> PASE DE ANOTACION </th><th> SAFETY </th><th> INTERCEPCION </th></tr>";
            //creamos el array que delvoremos al js 
            $retorno=Array("Resultado","numero de jugadores");
            //recorremos la variable donde guardamos a los jugadores
            foreach ($info as $info2){
                //sentencia que recuperara los datos de cada jugador
                 $sql = sprintf(" select * from cedulas where ID_JUGADOR=" .$info2["ID_JUGADOR"]);
                  //obtenemos la conexion de la clase SRV_CONEXION 
                 $conexcion= $db->getConnection();
                  //ejecutamos la sentencia y guardamos el redultado en $resultado3
                 $resultado3=$conexcion->query($sql);
                 //recuperamos la fila de la respuesta
                $info3=$resultado3->fetch_assoc();
                 //preparamos el array de la tabla del jugador con sus datos(visual)
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
                //insertamos en el array que devolveremos al js la tabla de los jugadores(visual)
                $retorno[0]=$datos;
                 //insertamos en el array que devolveremos al js el numero de jugadores del equipo
                $retorno[1]=$numero_jugadores;
                 //encriptamos el array en un json para poderlo enviar al js
                echo json_encode($retorno);
             
        } 
        break;
   //en el caso que el js desea hablitar el boton de guardar datos
        case "GET_BOTON":
           //verificamos si el usuario es de tipo adminitrador o capturists,ya que solo ellos puede modificar y/o guardar datos en las cedulas
         if (isset($_SESSION["TIPO_USUARIO"]) == 'CAPTURISTA' || isset($_SESSION["TIPO_USUARIO"]) == 'ADMINISTRADOR'){
             //retornamos el boton habilitado
             echo " <center>"
                 ."<input type='submit' class='btn btn-primary' value='Guardar datos' onclick='llenar_rol_juego(".$_POST["ROL"].",".$_POST["TEAM1"].",".$_POST["TEAM2"].")'>"
                 ."</center><br>";
         }else{
             //en caso que el usuario no sea administridador ni capturista enviaremos un boton inhabilitado
              echo " <center>"
                    ."<input type='submit' class='btn btn-primary' value='Guardar datos' class='btn btn-default' disabled>"
                    ."</center><br>";
         }
    break;
    //en el caso que se desea guardar los datos
    case "GUARDAR_DATOS":
      //variable que contendra la ID del equipo 1
      $ID_ROSTER_TEAM_1;
        //variable que contendra la ID del equipo 1
      $ID_ROSTER_TEAM_2;
      //variable que contendra la ID del equipo ganador
      $ID_TEAM_GANADOR;
      //variable que almacenara los puntos echos en el partido por el equipo 1
      $PUNTOS_TEAM_1=0;
      //variable que almacenara los puntos echos en el partido por el equipo 1
      $PUNTOS_TEAM_2=0;
      //variable que nos ayudara a recorrer un ciclo for
      $i;
      //recuperamos el numero de integrantes del equipo 1
      $numero_de_jugadores_team1=$_POST['NumeroDeIntegrasteDelEquipo1'];
       //recuperamos el numero de integrantes del equipo 1
      $numero_de_jugadores_team2=$_POST['NumeroDeIntegrasteDelEquipo2'];
      //recuperamos la tabla del equipo 1
      $Tabla_Jugadores_Team1=$_POST['TablaTeam1'];
      //recuperamos la tabla del equipo 2
      $Tabla_Jugadores_Team2=$_POST['TablaTeam2'];
      //setencia que obtendra el ID del roster del equipo 1
      $sql = sprintf("SELECT * FROM rosters where ID_EQUIPO=%s AND ID_CONVOCATORIA=%s",$_POST['TEAM1'],$_POST['ID_CONVOCSTORIA']); 
     //obtenemos la conexion de la clase SRV_CONEXION 
      $conexcion= $db->getConnection();  
      //ejecutamos la peticion y guardamos el resultado en la variable resultado
      $resultado=$conexcion->query($sql);
      //obtenemos  la infomacion del resultado
      $info=$resultado->fetch_assoc();
      //recuperamos el ID_ROSTER
      $ID_ROSTER_TEAM_1=$info["ID_ROSTER"];
      //preparamos peticion sql para obtener el ID_ROSTER del equipo 2
      $sql = sprintf("SELECT * FROM rosters where ID_EQUIPO=%s AND ID_CONVOCATORIA=%s",$_POST['TEAM2'],$_POST['ID_CONVOCSTORIA']); 
      //obtenemos la conexion de la clase SR_CONEXION 
      $conexcion2= $db->getConnection();  
      //ejecutamos la peticion y guardamos el resultado en la variable resultado
      $resultado2=$conexcion2->query($sql);
      //obtenemos  la infomacion del resultado
      $info2=$resultado2->fetch_assoc();
       //recuperamos el ID_ROSTER
      $ID_ROSTER_TEAM_2=$info2["ID_ROSTER"];
      
      //obtenemos la conexion de la clase SR_CONEXION 
      $conexciones= $db->getConnection();
      //Creamos un ciclo for para recorrer a cada jugador  actualizar sus datos del equipo 2
      for($i=0;$i<$numero_de_jugadores_team1;$i++){
          //preparamos la Sentencia sql que actualizara 
          $consulta=$conexciones->prepare("UPDATE cedulas SET T=?,S=?,I=?,A=?,C1=?,C2=?,C3=?,PA=?,SA=?,I4=? WHERE  ID_ROL_JUEGO=? AND ID_JUGADOR=? AND ID_ROSTER=?");
          //asignamos los valores de la sentencia prepara
          $consulta->bind_param("iiiiiiiiiiiii",$Tabla_Jugadores_Team1[$i][1],$Tabla_Jugadores_Team1[$i][2],$Tabla_Jugadores_Team1[$i][3],$Tabla_Jugadores_Team1[$i][4],$Tabla_Jugadores_Team1[$i][5],$Tabla_Jugadores_Team1[$i][6],$Tabla_Jugadores_Team1[$i][7],$Tabla_Jugadores_Team1[$i][8],$Tabla_Jugadores_Team1[$i][9],$Tabla_Jugadores_Team1[$i][10],$_POST['ID_ROL'],$Tabla_Jugadores_Team1[$i][0],$ID_ROSTER_TEAM_1);
         //En un ejecutamos la sentncia sql preparada
          if($consulta->execute()){
             //en caso de ser exitosa
            }else{                   
            //en caso de ser fallida regresa un "no" y matamos el php para que no se siga ejecutando.
           echo "no";
           die();
            }
      }
       //Creamos un ciclo for para recorrer a cada jugador  actualizar sus datos del equipo 2
       for($//En un ejecutamos la sentncia sql preparadai=0;$i<$numero_de_jugadores_team2;$i++){
           //preparamos la Sentencia sql que actualizara
          $consulta=$conexciones->prepare("UPDATE cedulas SET T=?,S=?,I=?,A=?,C1=?,C2=?,C3=?,PA=?,SA=?,I4=? WHERE  ID_ROL_JUEGO=? AND ID_JUGADOR=? AND ID_ROSTER=?");
           //asignamos los valores de la sentencia prepara
          $consulta->bind_param("iiiiiiiiiiiii",$Tabla_Jugadores_Team2[$i][1],$Tabla_Jugadores_Team2[$i][2],$Tabla_Jugadores_Team2[$i][3],$Tabla_Jugadores_Team2[$i][4],$Tabla_Jugadores_Team2[$i][5],$Tabla_Jugadores_Team2[$i][6],$Tabla_Jugadores_Team2[$i][7],$Tabla_Jugadores_Team2[$i][8],$Tabla_Jugadores_Team2[$i][9],$Tabla_Jugadores_Team2[$i][10],$_POST['ID_ROL'],$Tabla_Jugadores_Team2[$i][0],$ID_ROSTER_TEAM_2);
          //En un ejecutamos la sentncia sql preparada
          if($consulta->execute()){
               //en caso de ser exitosa
            }else{      
                //en caso de ser fallida regresa un "no" y matamos el php para que no se siga ejecutando.
           echo "no";
          die();
            }
      }
      //sentencia que recupera los puntos de cada jugador del equipo 1
       $sql = sprintf("SELECT * FROM cedulas WHERE ID_ROL_JUEGO =%s and ID_ROSTER=%s",$_POST['ID_ROL'],$ID_ROSTER_TEAM_1); 
        //obtenemos la conexion de la clase SR_CONEXION 
       $conexcion3= $db->getConnection();
       //obtenemos  la infomacion del resultado
        $resultado3=$conexcion3->query($sql);
        //Creamos un array para obtener cada fila del resutado
        $info3=array();
        //Con el Ciclo while assignamos cada fila del resultado al array que creamos previamente
         while ($row = $resultado3->fetch_assoc()) {
            $info3[] = $row;
        }
        //Ciclo for par obtener el total de puntos echos por elequipo 1
        foreach ($info3 as $infoX) {
                   $PUNTOS_TEAM_1=$PUNTOS_TEAM_1+($infoX["A"]*6)+($infoX["C1"]*1)+($infoX["C2"]*2)+($infoX["C3"]*3)+($infoX["SA"]*2)+($infoX["I4"]*4);
        }
      //sentencia que recupera los puntos de cada jugador del equipo 1  
        $sql = sprintf("SELECT * FROM cedulas WHERE ID_ROL_JUEGO =%s and ID_ROSTER=%s",$_POST['ID_ROL'],$ID_ROSTER_TEAM_2); 
        //obtenemos la conexion de la clase SR_CONEXION
        $conexcion4= $db->getConnection();
        //obtenemos  la infomacion del resultado
        $resultado4=$conexcion4->query($sql);
        //Creamos un array para obtener cada fila del resutado
        $info4=array();
        //Con el Ciclo while assignamos cada fila del resultado al array que creamos previamente
         while ($row = $resultado4->fetch_assoc()){
            $info4[] = $row;
        }
        //Ciclo for par obtener el total de puntos echos por elequipo 2
        foreach ($info4 as $infoY) {                
                   $PUNTOS_TEAM_2=$PUNTOS_TEAM_2+($infoY["A"]*6)+($infoY["C1"]*1)+($infoY["C2"]*2)+($infoY["C3"]*3)+($infoY["SA"]*2)+($infoY["I4"]*4);
        }
        //if anidados que determinan que equipo tiene mas puntos
        if($PUNTOS_TEAM_1>$PUNTOS_TEAM_2){
            $ID_TEAM_GANADOR=$_POST["TEAM1"];
        }else if($PUNTOS_TEAM_1<$PUNTOS_TEAM_2){
             $ID_TEAM_GANADOR=$_POST["TEAM2"];
        }else if($PUNTOS_TEAM_1==$PUNTOS_TEAM_2){
           $ID_TEAM_GANADOR=0; 
        }
       //Sentencia que acutalizara los datos con el equipo ganador
      $sql = sprintf("UPDATE roles_juego SET ID_EQUIPO_1=%s, ID_EQUIPO_2=%s, ID_EQUIPO_GANADOR=%s, PUNTOS_EQUIPO_1=%s, PUNTOS_EQUIPO_2=%s WHERE ID_ROL_JUEGO=%s",$_POST['TEAM1'],$_POST['TEAM2'],$ID_TEAM_GANADOR,$PUNTOS_TEAM_1,$PUNTOS_TEAM_2,$_POST['ID_ROL']); 
      //obtenemos la conexion de la clase SR_CONEXION
      $conexcion5= $db->getConnection();  
      //obtenemos  la infomacion del resultado
      $resultado5=$conexcion5->query($sql); 
      //si no ocurrio algun error regresamos un ok
     echo "ok";
    break;
   //en el caso que el js desea conocer si se ha iniciado session
    case "ComprobarLogin":
        //verificamos si la varible de session  se a creado
         if (!empty($_SESSION["ID_USUARIO"]) && !empty($_SESSION["TIPO_USUARIO"])){
             //retorna "si", si se ha iniciado session
             echo"si";
         }else{
             //retorna "no", si no ha iniciado session
             echo "no";
           
         }
    break;
}
?>
