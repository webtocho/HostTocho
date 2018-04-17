<?php
    require 'SRV_CONEXION.php';
    $db = new SRV_CONEXION();
    $conn = $db->getConnection();    
    //En este php se retornaran todos los cumpleañeros del dia haciendo un select conforme al dia y mes del momento y comparado con la fecha de nacimiento de los usuarios
	$query = "SELECT NOMBRE,APELLIDO_PATERNO,APELLIDO_MATERNO,FOTO_PERFIL FROM usuarios WHERE DATE_FORMAT(FECHA_NACIMIENTO, '%m%d') = DATE_FORMAT(CURDATE(),'%m%d');";//query para obtener cumpleañeros por dia
	$result = $conn->query($query);
        //Comprobamos si se obtuvo alguna informacion
	if($result){
        if(mysqli_num_rows($result)<=0) //Si no hay ni un cumpleañero se retorna un uno
            echo 1;
        else{//Si al menos hay un cumpleañero se retorna su nombre completo y su foto
            $inicia=0;
            //De haber mas de un cumpleañero se retornan los datos en un div para cada cumpleañero
            while($row = mysqli_fetch_array($result)){
                echo "<div class='item'>"; $inicia=1;
                $foto = base64_encode($row['FOTO_PERFIL']);
                echo "<center>";
                if($foto==null){
                    echo "<img class='img-responsive lot' src='img/RC_IF_CUMPLE.png' alt='' style='max-width:250px;max-height:250px;'/>";
                }else{
                    echo "<img class='img-responsive lot' src='data:image/png;base64,".$foto."' alt='' style='max-width:250px;max-height:250px;'/>";
                }
                
                echo "</center><br><a>".$row['NOMBRE']." ".$row['APELLIDO_PATERNO']." ".$row['APELLIDO_MATERNO']."</a>";
                echo "</div>";
                }
        }
	}else echo 1;
	$conn->close();
?>