<?php
    require 'SRV_CONEXION.php';
    $db = new SRV_CONEXION();
    $conn = $db->getConnection();    
	$query = "SELECT NOMBRE,APELLIDO_PATERNO,APELLIDO_MATERNO,FOTO_PERFIL FROM usuarios WHERE DATE_FORMAT(FECHA_NACIMIENTO, '%m%d') = DATE_FORMAT(CURDATE(),'%m%d');";//query para obtener cumpleañeros por dia
	$result = $conn->query($query);

	if($result){
        if(mysqli_num_rows($result)<=0) echo "<div class='item'><a>Hoy no hay cumpleañeros</a></div>";
		else{
            $inicia=0;
            while($row = mysqli_fetch_array($result)){
                echo "<div class='item'>"; $inicia=1;
                $foto = base64_encode($row['FOTO_PERFIL']);
                if($foto==null){
                    echo "<img class='img-responsive lot' src='img/CUMPLE_ICON.png' alt=''/>";
                }else{
                    echo "<img class='img-responsive lot' src='data:image/png;base64,".$foto."' alt=''/>";
                }
                echo "<br><a>".$row['NOMBRE']." ".$row['APELLIDO_PATERNO']." ".$row['APELLIDO_MATERNO']."</a>";
                echo "</div>";
                }
        }
	}else echo 1;
    //media-left response-text-left
    //style='max-width: 40px; max-height: 40px'
	$conn->close();
?>