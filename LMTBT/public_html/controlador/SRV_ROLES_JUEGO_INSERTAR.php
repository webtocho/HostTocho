<?php
require 'SRV_CONEXION.php';
$db = new SRV_CONEXION();
$conn = $db->getConnection();
//Se inserta el round robin generado a la tabla de roles de juego poniendo en ceros, nulo o -1 los campos que no son el id de los equipos y la convocatoria
    $stmt = $conn->prepare("INSERT INTO roles_juego  VALUES (0,?,?,?,-1,0,0,null,null,null,?)");
    $stmt->bind_param("iiii", $id_convocatoria, $auxLocal, $auxVisitante,$jornada);    
    $id_convocatoria = $_POST['convocatoria'];
    $tam = $_POST['tam'];//cantidad de equipos en una convocatoria
    $repetibilidad = $_POST['repeticion'];//cantidad de veces que se repetira el rol de juegos
    $local=json_decode($_POST['local']);//Equipos locales del rol
    $visitante=json_decode($_POST['visitante']);//equipos visitantes del rol de juegos
    $jornada = 1;//variable para las jornadas del torneo
    $contador =0;//contador para saber si ya se cumplio el cupo de partidos por jornada
    $partidosJornada = $tam/2;//cantidad de partidos por jornada
    $totalP=($tam*($tam-1))/2;//total de partidos por rol de juego
    //Como es una sentencia preparada y esta enlazada a variables simplemente actualizamos esas variables y ejecutamos
    //Para generar el rol de juegos se utiliza round robin, con la modificacion de que el rol generado se repite un numero
    //Indefinido de veces (numero dado por el administrador) por ello realizamos un ciclo para repetir las veces necesarias
    for($repeticion=0;$repeticion<$repetibilidad;$repeticion++){
        for($i=0;$i<$totalP;$i++){
            $auxLocal = $local[$i];
            $auxVisitante = $visitante[$i];
            $stmt->execute();
            $contador++;
            if($contador==$partidosJornada){
                $jornada++;
                $contador=0;
            }  
        }
    }
    //Finalmente creamos los registros con equipos vacios debido a que son las semifinales y la final
    //Y no se han decidido esos partidos aun
    $jornada++;
    for($finales=0;$finales<3;$finales++){
        $auxLocal = 0;
        $auxVisitante = 0;
        if($finales==2)$jornada++;
        $stmt->execute();
    }
    
    
    echo 1;
    $conn->close();
?>