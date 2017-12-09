<?php
require 'SRV_CONEXION.php';
$db = new SRV_CONEXION();
$conn = $db->getConnection();

$id_convocatoria=$_POST['convocatoria'];

$stmt="SELECT * FROM roles_juego WHERE ID_CONVOCATORIA=".$id_convocatoria;

$result = $conn->query($stmt);

if($result && mysqli_num_rows($result)>0){
    echo -1;
}
else{
	$stmt="SELECT ID_EQUIPO FROM rosters WHERE ID_CONVOCATORIA=".$id_convocatoria;

	$result = $conn->query($stmt);
	$rawdata = "";

	if ($result && mysqli_num_rows($result)>0) {
    	$i=0;
    	while($row = mysqli_fetch_array($result)){
        	if($i==0) {$rawdata=$rawdata.$row['ID_EQUIPO']; $i=1;}
        	else{$rawdata=$rawdata.','.$row['ID_EQUIPO'];}
    	}
    	echo $rawdata;
	} else {
    	echo -1;
	}
}
$conn->close();

?>