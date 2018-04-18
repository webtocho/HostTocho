<?php
//AGREGAR EN EL METODO CREACION DE ROL DE JUEGOS UN APARTADO PARA QUE INSERTE LOS EQUIPOS INSCRITOS EN UNA CONVOCATORIA A LA TABLA ESTADISTICAS, PARA PODER HACER SOLAMENTE ACTUALIZACIONES
    require 'SRV_CONEXION.php';
    $db = new SRV_CONEXION();
    $conn = $db->getConnection();
    //En esta funcion se obtienen las convocatorias de los torneos segun el parametro tipo (1 para convocatorias activas, 2 para inactivas y cualquier otro numero cae en default -todas cuya fecha de cierra ya haya pasado)
    $tipo = $_POST['tipo'];
    $query="SELECT ID_CONVOCATORIA,NOMBRE_TORNEO FROM convocatoria WHERE CURDATE()>FECHA_CIERRE_CONVOCATORIA";
   
    if($tipo==1){$query="SELECT ID_CONVOCATORIA,NOMBRE_TORNEO FROM convocatoria WHERE ESTADO='ACTIVO'";}
    else if($tipo==2){$query="SELECT ID_CONVOCATORIA,NOMBRE_TORNEO FROM convocatoria WHERE ESTADO='INACTIVO' AND CURDATE()>FECHA_CIERRE_CONVOCATORIA";}

    $result = $conn->query($query);
    //Se hace una consulta a la base de datos y se comprueba si al menos se obtuvo un resultado
    //Si la condicion se cumple se retornan las convocatorias como codigo html para un select donde el value es el id de la convocatoria y el texto mostrado es el nombre de la convocatoria
    if($result&&mysqli_num_rows($result)>0){
        echo "<option value='' disabled selected hidden>Selecciona una convocatoria...</option>";
    	while($row =  mysqli_fetch_array($result)){
    		echo '<option value='.$row['ID_CONVOCATORIA'].'>'.$row['NOMBRE_TORNEO'].'</option>';
    	}
    }//Si no se cumple se retorna una sola opcion donde marca que no hay torneos
    else echo "<option value='' disabled selected hidden>No hay torneos</option>";
    
    $conn->close();
?>