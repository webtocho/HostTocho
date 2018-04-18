<?php
require 'SRV_CONEXION.php';
$db = new SRV_CONEXION();
$conn = $db->getConnection();
//Este php retorna todos id de los equipos inscritos a una convocatoria
$id_convocatoria=$_POST['convocatoria'];
//Comprobamos si ya genero el rol de juegos de la convocatoria, para no duplicar la informacion
$stmt="SELECT * FROM roles_juego WHERE ID_CONVOCATORIA=".$id_convocatoria;

$result = $conn->query($stmt);
//Si ya se genero retornamos un -1
if($result && mysqli_num_rows($result)>0){
    echo -1;
}
else{
        //Caso contrario recuperamos los ids de los equipos inscritos a una convocatoria.
	$stmt="SELECT ID_EQUIPO FROM rosters WHERE ID_CONVOCATORIA=".$id_convocatoria;

	$result = $conn->query($stmt);
	$rawdata = "";
        
	if ($result && mysqli_num_rows($result)>0) {
    	$i=0;
        //Retornamos una cadena con los ids separados por comas
    	while($row = mysqli_fetch_array($result)){
        	if($i==0) {$rawdata=$row['ID_EQUIPO']; $i=1;}
        	else{$rawdata=$rawdata.','.$row['ID_EQUIPO'];}
    	}
    	echo $rawdata;
	} else {
    	echo -1;
	}
}
$conn->close();

?>