<?php
    $servername = "localhost";
$username = "id3551892_team";
$password = "tochoweb";
$dbname = "id3551892_tochoweb";

// Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

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