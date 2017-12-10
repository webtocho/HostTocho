<?php
    include("SRV_CONEXION.php");
    $db = new SRV_CONEXION();
    $conexion = $db->getConnection();
    $cambios_hechos = true;
    $correo_existe = false;
    $conexion->autocommit(FALSE);       
    $sql = "SELECT *FROM usuarios";
    if (filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL)){
        if($resultado = $conexion->query($sql)){
            while($fila = $resultado->fetch_assoc()){
                if($fila["CORREO"] == $_POST['correo']){
                    $correo_existe = true;
                    break;
                }
            }
            if($correo_existe == false){             
                    $tipo_usuario = "COACH";
                    $consulta = $conexion->prepare('INSERT INTO usuarios VALUES (0,?,?,?,?,?,null,null,null,null,null,null,null,null,?)');
                    $consulta->bind_param("ssssss",$_POST['correo'], $_POST['password'], $_POST['nombre'], $_POST['apellido_paterno'], $_POST['apellido_materno'],$tipo_usuario);
                    if($consulta->execute()){
                        $cambios_hechos = true;
                    } else {
                        $cambios_hechos = false;
                    }                                    
            }else{
                //insert code here
                echo "El correo ingresando ya esta ligado a un cuenta";
                $conexion->autocommit(TRUE);
                $conexion->close();
                return;
            }
            }else{
                $cambios_hechos = false;
            } 
    }else{
        echo "El correo ingresado es invalido";
        $conexion->autocommit(TRUE);
        $conexion->close();
        return;        
    }
        if($cambios_hechos){
            if ($conexion->commit()) {
                echo "ok";
            }else{
                echo "Falló la consignación de la transacción.";
            }
        }else{              
            $conexion->rollback();
            echo "Error en la transaccion";
        }
    
    $conexion->autocommit(TRUE);
    $conexion->close();
?>