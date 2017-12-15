<?php
    include("SRV_CONEXION.php");
    $db = new SRV_CONEXION();
    $conexion = $db->getConnection();
    $cambios_hechos = true;
    $correo_existe = false;
    $conexion->autocommit(FALSE); 
    session_start();
    if (isset($_SESSION['ID_USUARIO']) && isset($_SESSION["TIPO_USUARIO"])) {
        switch ($_SESSION["TIPO_USUARIO"]){
            case "ADMINISTRADOR":
                if($_POST['tipo_cuenta'] != "ADMINISTRADOR" && $_POST['tipo_cuenta'] != "COACH" && $_POST['tipo_cuenta'] != "JUGADOR" &&
                $_POST['tipo_cuenta'] != "FOTOGRAFO" && $_POST['tipo_cuenta'] != "CAPTURISTA"){
                    echo "El tipo de cuenta que intenta crear es invalido";
                    $conexion->autocommit(TRUE);
                    $conexion->close();
                    return;
                }
            break;
            case "COACH":
                if($_POST['tipo_cuenta'] != "JUGADOR"){
                    echo "No tienes permios para crear este tipo de cuenta";
                    $conexion->autocommit(TRUE);
                    $conexion->close();
                    return;
                }
            break;
        }
    } else {
	if($_POST['tipo_cuenta'] != "JUGADOR"){
            echo "No tienes permios para crear este tipo de cuenta";
            $conexion->autocommit(TRUE);
            $conexion->close();
            return;
        }
    }                            
    if(empty($_POST['correo']) == false && empty($_POST['password']) == false && empty($_POST['nombre']) == false && empty($_POST['apellido_paterno']) == false && empty($_POST['apellido_materno']) == false && empty($_POST['tipo_cuenta']) == false){
        if(strlen($_POST['password']) > 7){
            if (filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL)){
                $sql = "SELECT *FROM usuarios";
                if($resultado = $conexion->query($sql)){
                    while($fila = $resultado->fetch_assoc()){
                        if($fila["CORREO"] == $_POST['correo']){
                            $correo_existe = true;
                            break;
                        }
                    }
                    if($correo_existe == false){    
                        //$tipo_usuario = "COACH";
                        $consulta = $conexion->prepare('INSERT INTO usuarios VALUES (0,?,?,?,?,?,null,null,null,null,null,null,null,null,?)');
                        $consulta->bind_param("ssssss",$_POST['correo'], $_POST['password'], $_POST['nombre'], $_POST['apellido_paterno'], $_POST['apellido_materno'],$_POST['tipo_cuenta']);
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
        }else{
            echo "La contraseña debe tener al menos 7 caracteres";
            $conexion->autocommit(TRUE);
            $conexion->close();
            return;
        }
    }else{
        echo "Debes llenar todos los campos indicados";
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