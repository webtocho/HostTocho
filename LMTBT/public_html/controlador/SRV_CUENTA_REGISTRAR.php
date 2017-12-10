<?php
    require 'SRV_CONEXION.php';
    $db = new SRV_CONEXION();
    $conn = $db->getConnection();    
    
    switch($_POST['tipo']){	
       case "registrarCuenta":
            $consulta = $conn->prepare('INSERT INTO usuarios(ID_USUARIO, CORREO, PASSWORD, NOMBRE,  APELLIDO_PATERNO, APELLIDO_MATERNO, FECHA_NACIMIENTO, SEXO, TIPO_SANGRE, TELEFONO, FOTO_PERFIL, FACEBOOK, INSTAGRAM, TWITTER,TIPO_USUARIO) VALUES (0, ?, ?, ?, ?, ?, null, ?, null, null, null, null,null,null, ?)');
            $consulta->bind_param("sssssii", $_POST['correo'], $_POST['password'],  $_POST['nombre'], $_POST['apellido_paterno'], $_POST['apellido_materno'],$_POST['sexo'], $_POST['tipo_usuario']);
           
            if ($consulta->execute()) {
		echo "ok";
            } else {
                echo $consulta->error;
            }
       break;
   
       
    }
	$conn->close();
?>