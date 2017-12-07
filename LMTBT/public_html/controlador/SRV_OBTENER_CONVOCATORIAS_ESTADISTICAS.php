<?php
//AGREGAR EN EL METODO CREACION DE ROL DE JUEGOS UN APARTADO PARA QUE INSERTE LOS EQUIPOS INSCRITOS EN UNA CONVOCATORIA A LA TABLA 
//ESTADISTICAS, PARA PODER HACER SOLAMENTE ACTUALIZACIONES

    $conn = new mysqli("localhost","id3551892_team","tochoweb", "id3551892_tochoweb");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $tipo = $_POST['tipo'];
    $query="SELECT ID_CONVOCATORIA,NOMBRE_TORNEO FROM convocatoria";
   
    if($tipo==1){$query="SELECT ID_CONVOCATORIA,NOMBRE_TORNEO FROM convocatoria WHERE ESTADO='ACTIVO'";}
    else if($tipo==2){$query="SELECT ID_CONVOCATORIA,NOMBRE_TORNEO FROM convocatoria WHERE ESTADO='INACTIVO'";}

    $result = $conn->query($query);
    
    if($result&&mysqli_num_rows($result)>0){
        echo '<option value=0>Selecciona una convocatoria</option>';
    	while($row =  mysqli_fetch_array($result)){
    		echo '<option value='.$row['ID_CONVOCATORIA'].'>'.$row['NOMBRE_TORNEO'].'</option>';
    	}
    }
    else echo '<option value=0>No hay torneos</option>';
    
    $conn->close();
?>