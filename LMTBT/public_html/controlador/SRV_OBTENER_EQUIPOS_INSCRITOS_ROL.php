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
        if($i===0) {$rawdata=$rawdata.$row['ID_EQUIPO']; $i=2;}
        else{$rawdata=$rawdata.','.$row['ID_EQUIPO'];}
    }
    echo $rawdata;
} else {
    echo -1;
}}
$conn->close();

?>