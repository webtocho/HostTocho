<?php
    //Agregamos la region y lugar para obtener correctamente las fechas
    date_default_timezone_set('America/Mexico_City');
    //Incluimos a la clase SRV_CONEXION(); para poder instanciarla
    include("SRV_CONEXION.php");    
    //Instanciamos a la clase SRV_CONEXION();
    $db = new SRV_CONEXION();
    //Recuperamos la conexion
    $conexion = $db->getConnection();
    //Declaramos el incio de una transaccion ya  que se requiere realizar varias consultas
    //Evitaremos perdida de datos en caso de que una consulta falle
    $conexion->autocommit(FALSE);
    $categoria_existe = false;
    $cambios_hechos = true;    
    /*
     * Comprobamos si la sesion con la que se esta queriendo realizar una accion es la correcta
     * en este caso solo el administrador, de lo contrario se expulsa sin poder realizar 
     * ninguna de las demas acciones
     */
    session_start();        
    if (isset($_SESSION['ID_USUARIO']) && isset($_SESSION["TIPO_USUARIO"])) {
        if($_SESSION["TIPO_USUARIO"] != "ADMINISTRADOR"){
            echo "No tienes permisos para lanzar una convocatoria";
            $conexion->autocommit(TRUE);
            $conexion->close();
            return;
        }
    } else {
        echo "No tienes permisos para lanzar una convocatoria";
        $conexion->autocommit(TRUE);
        $conexion->close();
        return;
    } 
    //Obtenemos los datos que nos han sido enviados por el metodo de tipo POST
    //los cuales seran registrados en la BD
    $id = 0;
    $nombre_torneo = $_POST['nombre'];
    $fecha_cierre_convocatoria = $_POST['fecha_cierre'];
    $fecha_inicio_torneo = $_POST['fecha_inicio'];
    $fecha_fin_torneo = $_POST['fecha_fin'];
    $categoria = $_POST['categoria'];    
    $estado = "INACTIVO";                        
    //Se realiza el cambio a formato fecha
    $fecha_inicio_torneo = strtotime($fecha_inicio_torneo);
    $fecha_inicio_torneo = date("Y-m-d", $fecha_inicio_torneo);
    $fecha_cierre_convocatoria = strtotime($fecha_cierre_convocatoria);
    $fecha_cierre_convocatoria = date("Y-m-d", $fecha_cierre_convocatoria);
    $fecha_fin_torneo = strtotime($fecha_fin_torneo);
    $fecha_fin_torneo = date("Y-m-d", $fecha_fin_torneo);
    //Se separan los los datos que contiene una fecha para ser validados
    $validar_fecha_cierre = explode('/', $_POST['fecha_cierre']);
    $validar_fecha_inicio = explode('/', $_POST['fecha_inicio']);
    $validar_fecha_fin = explode('/', $_POST['fecha_fin']);
    //Se evalua que los datos de entrada no esten vacios de lo contrario se regresa un error
    if(empty($_POST['nombre']) == false && empty($_POST['fecha_cierre']) == false && empty($_POST['fecha_inicio']) == false && empty($_POST['fecha_fin']) == false && empty($_POST['categoria']) == false){
        //Realizamos una primer consulta a la BD para validar que la categoria con la que se quiere registrar el torneo existe
        $sql = "SELECT *FROM categorias";        
        if($resultado = $conexion->query($sql)){
            while($fila = $resultado->fetch_assoc()){
                        if($fila["NOMBRE_CATEGORIA"] == $_POST['categoria']){
                            $categoria_existe = true;
                            break;
                        }
            }
            //Se obtiene el id de la categoria para pder registrarse la nueva convocatoria
            if($categoria_existe == true){
                $sql = "SELECT *FROM categorias WHERE NOMBRE_CATEGORIA = '$categoria'";
                if($resultado = $conexion->query($sql)){
                    $fila = $resultado->fetch_assoc();
                    $id_categoria = $fila["ID_CATEGORIA"];
                    //Se valida que las fechas tengan el formato correcto para poder guardarse en la BD
                    if (count($validar_fecha_cierre) == 3 && count($validar_fecha_inicio) == 3 && count($validar_fecha_fin) == 3){
                        if ($validar_fecha_cierre[0] != "" && $validar_fecha_cierre[1] != "" && $validar_fecha_cierre[2] != "" && $validar_fecha_inicio[0] != "" && $validar_fecha_inicio[1] != "" && $validar_fecha_inicio[2] != "" && $validar_fecha_fin[0] != "" && $validar_fecha_fin[0] != "" && $validar_fecha_fin[2] != "") {
                            try {
                                //Validamos que la fecha sea valida y exista como tal
                                if (checkdate($validar_fecha_cierre[0], $validar_fecha_cierre[1], $validar_fecha_cierre[2]) == true && checkdate($validar_fecha_inicio[0], $validar_fecha_inicio[1], $validar_fecha_inicio[2]) == true && checkdate($validar_fecha_fin[0], $validar_fecha_fin[1], $validar_fecha_fin[2]) == true) {
                                        //Obtenemos las imagenes que seran registradas con la noticia
                                        $tipo = pathinfo($_FILES['imagen_noticia']['name'][0], PATHINFO_EXTENSION);
                                        $size = $_FILES['imagen_noticia']['size'][0];
                                        //Convertimos la imagen a un formato valido para ser almacenado en la BD
                                        $imagen = addslashes(file_get_contents($_FILES['imagen_noticia']['tmp_name'][0]));
                                        //Comprabamos que el formato o tipo de imagen sea el correcto
                                        if($tipo == "jpg" || $tipo == "jpeg" || $tipo == "png" && $size <= 16777215){
                                            //Realizamos una consulta preparada para mayor seguridad, ya que se requieren almacenar datos en la BD
                                            $consulta = $conexion->prepare("INSERT INTO convocatoria VALUES (0,?,?,?,?,'".$imagen."',?,?)");
                                            $consulta->bind_param("sssssi",$nombre_torneo,$fecha_cierre_convocatoria,$fecha_inicio_torneo,$fecha_fin_torneo,$estado,$id_categoria);                                          
                                            if($consulta->execute()){
                                                //Si todo se ejecuto correctamente declaramos la transaccion como valida
                                                $cambios_hechos = true;
                                            } else {                                                                                   
                                                $cambios_hechos = false;
                                            }
                                        }else{
                                            //Se regresa un error en caso de que la imagen no tenga un formato valido
                                            //y se marca la transaccion como fallada
                                            echo "La imagen insertada es incompatible o es muy grande";
                                            $conexion->autocommit(TRUE);
                                            $conexion->close();
                                            return;
                                        }                                                             
                                } else {
                                    //Se regresa un error en caso de que la fecha no sea valida
                                    //y se marca la transaccion como fallada
                                    echo "Ingrese una fecha valida";
                                    $conexion->autocommit(TRUE);
                                    $conexion->close();
                                    return;
                                }
                            } catch (Exception $e) {
                                //Se regresa un error en caso de que la fecha no tenga un formato valido
                                //y se marca la transaccion como fallada
                                echo "Ingresa un formato de fecha valido";
                                $conexion->autocommit(TRUE);
                                $conexion->close();
                                return;
                            }
                        }else{
                            //Se regresa un error en caso de que la fecha no tenga un formato valido
                            //y se marca la transaccion como fallada
                            echo "Ingresa un formato de fecha valido";
                            $conexion->autocommit(TRUE);
                            $conexion->close();
                            return;
                        }
                    }else{
                        //Se regresa un error en caso de que la fecha no tenga un formato valido
                        //y se marca la transaccion como fallada
                        echo "Ingresa un formato de fecha valido";
                        $conexion->autocommit(TRUE);
                        $conexion->close();
                        return;
                    }
                }else{
                    $cambios_hechos = false;
                }
            }else{
                //Se regresa un error en caso de que la categoria con la que se desea registrar no exista
                //y se marca la transaccion como fallada
                echo "Ingresa una categoría existente";
                $conexion->autocommit(TRUE);
                $conexion->close();
                return;
            }
	} else {
            $cambios_hechos = false;            
	}
    }else{
        //Se regresa un error en caso de que algun parametro recibido este vacio
        //y se marca la transaccion como fallada
        echo "Debesde llenar todos los campos";
        $conexion->autocommit(TRUE);
        $conexion->close();
        return;
    }
        if($cambios_hechos){
            //Si las consultas y todo lo demas se ejecuta correctamente re regresa un "ok" para indicar que no hubo ningun problema.
            //Se marca la transaccion como exitosa.
            if ($conexion->commit()){
                echo "ok";
            }else{
                echo "Falló la consignación de la transacción.";
            }
        }else{                        
            /*
             * Se regresa un error en caso de que alguna consulta haya fallado
             * y se deshacen los cambios hechos, para evitar perdida de datos en la BD
             * 
             */
            $conexion->rollback();
            echo "Error en la transaccion";
        }    
    $conexion->autocommit(TRUE);
    $conexion->close();
?>