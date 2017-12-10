<?php
require 'SRV_CONEXION.php';
$db = new SRV_CONEXION();
$conn = $db->getConnection();
// prepare and bind
    $stmt = $conn->prepare("INSERT INTO roles_juego  VALUES (0,?,?,?,-1,0,0,null,null,null)");
    $stmt->bind_param("iii", $id_convocatoria, $auxLocal, $auxVisitante);    
    $id_convocatoria = $_POST['convocatoria'];
    $tam = $_POST['tam'];
    $local=json_decode($_POST['local']);;
    $visitante=json_decode($_POST['visitante']);;
    $totalP=($tam*($tam-1))/2;

    for($i=0;$i<$totalP;$i++){
        $auxLocal = $local[$i];
        $auxVisitante = $visitante[$i];       
        $stmt->execute();
        echo 1;
    }
    
    $conn->close();
?>