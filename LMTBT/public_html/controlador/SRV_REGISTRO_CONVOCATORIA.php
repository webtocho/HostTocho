<?php
    date_default_timezone_set('America/Mexico_City');
    include("SRV_CONEXION.php");
    $db = new SRV_CONEXION();
    $conexion = $db->getConnection();
    $conexion->autocommit(FALSE);
    $categoria_existe = false;
    $cambios_hechos = true;    
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
    $id = 0;
    $nombre_torneo = $_POST['nombre'];
    $fecha_cierre_convocatoria = $_POST['fecha_cierre'];
    $fecha_inicio_torneo = $_POST['fecha_inicio'];
    $fecha_fin_torneo = $_POST['fecha_fin'];
    $categoria = $_POST['categoria'];
    //$imagen = addslashes(file_get_contents($_FILES['imagen']['tmp_name']));
    $estado = "INACTIVO";                        
    //cambiar a formato fecha
    $fecha_inicio_torneo = strtotime($fecha_inicio_torneo);
    $fecha_inicio_torneo = date("Y-m-d", $fecha_inicio_torneo);
    $fecha_cierre_convocatoria = strtotime($fecha_cierre_convocatoria);
    $fecha_cierre_convocatoria = date("Y-m-d", $fecha_cierre_convocatoria);
    $fecha_fin_torneo = strtotime($fecha_fin_torneo);
    $fecha_fin_torneo = date("Y-m-d", $fecha_fin_torneo);
    /////////////////////////////////////////////////////////////           
    /////////////////////////////////////////////////////////////
    $validar_fecha_cierre = explode('/', $_POST['fecha_cierre']);
    $validar_fecha_inicio = explode('/', $_POST['fecha_inicio']);
    $validar_fecha_fin = explode('/', $_POST['fecha_fin']);
    
    if(empty($_POST['nombre']) == false && empty($_POST['fecha_cierre']) == false && empty($_POST['fecha_inicio']) == false && empty($_POST['fecha_fin']) == false && empty($_POST['categoria']) == false){
        $sql = "SELECT *FROM categorias";        
        if($resultado = $conexion->query($sql)){
            while($fila = $resultado->fetch_assoc()){
                        if($fila["NOMBRE_CATEGORIA"] == $_POST['categoria']){
                            $categoria_existe = true;
                            break;
                        }
            }
            if($categoria_existe == true){
                $sql = "SELECT *FROM categorias WHERE NOMBRE_CATEGORIA = '$categoria'";
                if($resultado = $conexion->query($sql)){
                    $fila = $resultado->fetch_assoc();
                    $id_categoria = $fila["ID_CATEGORIA"];
                    if (count($validar_fecha_cierre) == 3 && count($validar_fecha_inicio) == 3 && count($validar_fecha_fin) == 3){
                        if ($validar_fecha_cierre[0] != "" && $validar_fecha_cierre[1] != "" && $validar_fecha_cierre[2] != "" && $validar_fecha_inicio[0] != "" && $validar_fecha_inicio[1] != "" && $validar_fecha_inicio[2] != "" && $validar_fecha_fin[0] != "" && $validar_fecha_fin[0] != "" && $validar_fecha_fin[2] != "") {
                            try {
                                if (checkdate($validar_fecha_cierre[0], $validar_fecha_cierre[1], $validar_fecha_cierre[2]) == true && checkdate($validar_fecha_inicio[0], $validar_fecha_inicio[1], $validar_fecha_inicio[2]) == true && checkdate($validar_fecha_fin[0], $validar_fecha_fin[1], $validar_fecha_fin[2]) == true) {
                                ////////////////////////////////////////////////////////////
                                        $tipo = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                                        $temporal = $_FILES['imagen']['tmp_name'];                                        
                                        $imagen = redimensionar_imagen($temporal,$tipo);
                                        if($imagen != null){           
                                            $consulta = $conexion->prepare("INSERT INTO convocatoria VALUES (0,?,?,?,?,'".$imagen."',?,?)");
                                            $consulta->bind_param("sssssi",$nombre_torneo,$fecha_cierre_convocatoria,$fecha_inicio_torneo,$fecha_fin_torneo,$estado,$id_categoria);
                                            if($consulta->execute()){
                                                $cambios_hechos = true;
                                            } else {                                                                                   
                                                $cambios_hechos = false;
                                            }
                                        }else{
                                            echo "La imagen insertada es incompatible o es muy grande";
                                            $conexion->autocommit(TRUE);
                                            $conexion->close();
                                            return;
                                        }                          
                                } else {
                                    echo "Ingrese una fecha valida";
                                    $conexion->autocommit(TRUE);
                                    $conexion->close();
                                    return;
                                }
                            } catch (Exception $e) {
                                echo "Ingresa un formato de fecha valido";
                                $conexion->autocommit(TRUE);
                                $conexion->close();
                                return;
                            }
                        }else{
                            echo "Ingresa un formato de fecha valido";
                            $conexion->autocommit(TRUE);
                            $conexion->close();
                            return;
                        }
                    }else{
                        echo "Ingresa un formato de fecha valido";
                        $conexion->autocommit(TRUE);
                        $conexion->close();
                        return;
                    }
                }else{
                    $cambios_hechos = false;
                }
            }else{
                echo "Ingresa una categoria existente";
                $conexion->autocommit(TRUE);
                $conexion->close();
                return;
            }
	} else {
            $cambios_hechos = false;            
	}
    }else{
        echo "Debesde llenar todos los campos";
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