<?php   
    date_default_timezone_set('America/Mexico_City');
    include("SRV_CONEXION.php");
    $db = new SRV_CONEXION();
    $conexion = $db->getConnection();
    $conexion->autocommit(FALSE);
    $cambios_hechos = true;  
    session_start();
    function redimensionar_imagen($temporal,$tipo){
        $imagen_recuperada = null;
        $tamanio;
        if ($tipo == "jpg" || $tipo == "jpeg"){
        //cambiar dimension de la imagen
            $nombre_archivo_tmp = uniqid() . ".jpg";
            $original = imagecreatefromjpeg($temporal);
            $ancho_original = imagesx($original);
            $alto_original = imagesy($original);
            $copia = imagecreatetruecolor(960, 640);
            imagecopyresampled($copia, $original, 0, 0, 0, 0, 960, 640, $ancho_original, $alto_original);
            imagejpeg($copia, $nombre_archivo_tmp, 100);
            $imagen_recuperada = addslashes(file_get_contents($nombre_archivo_tmp));
            $tamanio = filesize($nombre_archivo_tmp);
            unlink($nombre_archivo_tmp);
        }else if($tipo == "png"){
            $nombre_archivo_tmp = uniqid() . ".png";
            $original = imagecreatefrompng($temporal);
            $ancho_original = imagesx($original);
            $alto_original = imagesy($original);
            $copia = imagecreatetruecolor(960, 640);
            imagecopyresampled($copia, $original, 0, 0, 0, 0, 960, 640, $ancho_original, $alto_original);
            imagepng($copia, $nombre_archivo_tmp);
            $imagen_recuperada = addslashes(file_get_contents($nombre_archivo_tmp));
            $tamanio = filesize($nombre_archivo_tmp);
            unlink($nombre_archivo_tmp);
        }
        if($tamanio >= 16777215){
            return null;
        } else {
            return $imagen_recuperada;
        }
    } 
    
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
                $temporal = $_FILES['imagen_noticia']['tmp_name'][$i];     
                $imagen = redimensionar_imagen($temporal,$tipo);
                if($imagen != null){
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