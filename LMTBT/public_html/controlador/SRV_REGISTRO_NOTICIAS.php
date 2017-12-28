<?php   
    date_default_timezone_set('America/Mexico_City');
    include("SRV_CONEXION.php");
    $db = new SRV_CONEXION();
    $conexion = $db->getConnection();
    $conexion->autocommit(FALSE);
    $cambios_hechos = true;  
    session_start();   
    
    if (isset($_SESSION['ID_USUARIO']) && isset($_SESSION["TIPO_USUARIO"])) {
        if($_SESSION["TIPO_USUARIO"] != "ADMINISTRADOR" && $_SESSION["TIPO_USUARIO"] != "FOTOGRAFO"){
            echo "No tienes permisos para crear noticias";
            $conexion->autocommit(TRUE);
            $conexion->close();
            return;
        }
    } else {
        echo "No tienes permisos para crear noticias";
        $conexion->autocommit(TRUE);
        $conexion->close();
        return;
    }            
    if(empty($_POST['titulo_noticia']) == false && empty($_POST['descripcion']) == false && empty($_FILES['imagen_noticia']) == false){
        $fecha_actual = date('Y-m-d');
        $consulta = $conexion->prepare('INSERT INTO noticias VALUES (0,?,?,?)');
        $texto =$_POST['descripcion'];
        $texto  =str_replace(array("\r\n", "\r", "\n"), "<br />", $texto );
        
        $consulta->bind_param("sss",$_POST['titulo_noticia'],$texto,$fecha_actual);
        if($consulta->execute()){
            $cambios_hechos = true;
        } else {
            $cambios_hechos = false;
        }
        $sql = "SELECT LAST_INSERT_ID()";
        if($resultado = $conexion->query($sql)){
            $fila = $resultado->fetch_assoc();
            $id_noticia = $fila["LAST_INSERT_ID()"];
        }else{
            $cambios_hechos = false;
        }
        $imagenes = $_FILES['imagen_noticia']['name'];
        $tam = count($imagenes);
        $i;
        for($i = 0; $i < $tam; $i++){       
            try{
                $tipo = pathinfo($_FILES['imagen_noticia']['name'][$i], PATHINFO_EXTENSION);
                $size = $_FILES['imagen_noticia']['size'][$i];
                $imagen = addslashes(file_get_contents($_FILES['imagen_noticia']['tmp_name'][$i]));     
                if($tipo == "jpg" || $tipo == "jpeg" || $tipo == "png" && $size <= 16777215){
                    $consulta = $conexion->prepare("INSERT INTO multimedia VALUES (0,'".$imagen."',?)");
                    $consulta->bind_param("i",$id_noticia);
                    if($consulta->execute()){              
                    }else{
                        $cambios_hechos = false;
                    }
                }else{
                    $cambios_hechos = false;                    
                }                               
            }catch(Exception $e){
                $cambios_hechos = false;
            }
        }
    }else{
        echo "Debes de llenar todos los campos";
        $conexion->autocommit(TRUE);
        $conexion->close();
        return;
    }
        if($cambios_hechos){
            if ($conexion->commit()){
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