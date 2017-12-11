<?php
    date_default_timezone_set('America/Mexico_City');
    include("SRV_CONEXION.php");
    $db = new SRV_CONEXION();
    $conexion = $db->getConnection();
    $categoria_existe = false;
    $cambios_hechos = true;
    
    $id = 0;
    $nombre_torneo = $_POST['nombre'];
    $fecha_cierre_convocatoria = $_POST['fecha_cierre'];
    $fecha_inicio_torneo = $_POST['fecha_inicio'];
    $fecha_fin_torneo = $_POST['fecha_fin'];
    $categoria = $_POST['categoria'];
    $imagen = addslashes(file_get_contents($_FILES['imagen']['tmp_name']));
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
                        
        $sql = "SELECT *FROM categorias";        
        if($resultado = $conexion->query($sql)){
            while($fila = $resultado->fetch_assoc()){
                        if($fila["NOMBRE_CATEGORIA"] == $_POST['categoria']){
                            $categoria_existe = true;
                            break;
                        }
            }
        //if ($categoria == "VARONIL" || $categoria == "FEMENIL" || $categoria == "HEAVY" || $categoria == "MIXTO" || $categoria == "RABBIT" || $categoria == "MAS DE 40"){
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
                                    $size = $_FILES['imagen']['size'];
                                    if ($size >= 16777215){
                                        echo "El tamaño de la imagen que intenta insertar es muy grande pruebe con otra";
                                        $conexion->autocommit(TRUE);
                                        $conexion->close();
                                        return; 
                                    }else{
                                        $consulta = $conexion->prepare("INSERT INTO convocatoria VALUES (0,?,?,?,?".$imagen."',?,?)");
                                        $consulta->bind_param("sssssi",$nombre_torneo,$fecha_cierre_convocatoria,$fecha_inicio_torneo,$fecha_fin_torneo,$estado,$id_categoria);
                                        if($consulta->execute()){
                                            $cambios_hechos = true;
                                        } else {
                                            $cambios_hechos = false;
                                        }
                                        //if(){
                                        //$sql = sprintf("INSERT INTO convocatoria(ID_CONVOCATORIA,NOMBRE_TORNEO,FECHA_CIERRE_CONVOCATORIA,FECHA_INICIO_PARTIDO,FECHA_FIN_PARTIDO,CATEGORIA,IMAGEN_ANUNCIO,ESTADO) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s')", $id, $nombre_torneo, $fecha_cierre_convocatoria, $fecha_inicio_torneo,
                                        // $fecha_fin_torneo, $categoria, $imagen, $estado);
                                        //$db->setQuery($sql);
                                        //$db->ExecuteQuery();                                        
                                        //}
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
            //echo "Ingresa una categoria validad";
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