<?php
require 'SRV_CONEXION.php';
$db = new SRV_CONEXION();
$conn = $db->getConnection();
//Se inserta el round robin generado a la tabla de roles de juego poniendo en ceros, nulo o -1 los campos que no son el id de los equipos y la convocatoria
    $stmt = $conn->prepare("INSERT INTO roles_juego  VALUES (0,?,?,?,-1,0,0,null,null,null)");
    $stmt->bind_param("iii", $id_convocatoria, $auxLocal, $auxVisitante);    
    $id_convocatoria = $_POST['convocatoria'];
    $tam = $_POST['tam'];
    $local=json_decode($_POST['local']);;
    $visitante=json_decode($_POST['visitante']);;
    $totalP=($tam*($tam-1))/2;
    //Como es una sentencia preparada y esta enlazada a variables simplemente actualizamos esas variables y ejecutamos
    for($i=0;$i<$totalP;$i++){
        $auxLocal = $local[$i];
        $auxVisitante = $visitante[$i];       
        $stmt->execute();
        //echo 1;
    }
    echo 1;
    $conn->close();
?>