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
    $cambios_hechos = true;  
    /*
     * Comprobamos si la sesion con la que se esta queriendo realizar una accion es la correcta
     * de lo contrario se expulsa sin poder realizar ninguna de las demas acciones
     */
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
    //Se evalua que los datos de entrada no esten vacios de lo contrario se regresa un error
    if(empty($_POST['titulo_noticia']) == false && empty($_POST['descripcion']) == false && empty($_FILES['imagen_noticia']) == false){
        //se recupera la fecha actual para asignarlo como fecha de publicacion de la noticia
        $fecha_actual = date('Y-m-d');
        //Realizamos una consulta preparada para mayor seguridad, ya que se requieren almacenar datos en la BD
        //almacenamos el titulo de la noticia junto con su descripcion
        $consulta = $conexion->prepare('INSERT INTO noticias VALUES (0,?,?,?)');
        $texto =$_POST['descripcion'];
        $texto  =str_replace(array("\r\n", "\r", "\n"), "<br />", $texto );        
        $consulta->bind_param("sss",$_POST['titulo_noticia'],$texto,$fecha_actual);
        if($consulta->execute()){
            $cambios_hechos = true;
        } else {
            $cambios_hechos = false;
        }
        //Obtenemos el id con el que se registro la noticia
        $sql = "SELECT LAST_INSERT_ID()";
        if($resultado = $conexion->query($sql)){
            $fila = $resultado->fetch_assoc();
            $id_noticia = $fila["LAST_INSERT_ID()"];
        }else{
            $cambios_hechos = false;
        }
        //Recuperamos las imagenes pertenecientes a la noticia para poder almacenarlo en la BD
        $imagenes = $_FILES['imagen_noticia']['name'];
        $tam = count($imagenes);
        $i;
        //Realizamos el registro a la BD de las imagenes pertenecientes a un noticia con el id de dicha noticia
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
        //Se regresa un error en caso de que algun parametro este vacio
        echo "Debes de llenar todos los campos";
        $conexion->autocommit(TRUE);
        $conexion->close();
        return;
    }
        if($cambios_hechos){
            if ($conexion->commit()){
                //Se regresa "ok" en caso de que no haya habido un problema
                echo "ok";
            }else{
                echo "Falló la consignación de la transacción.";
            }
        }else{              
            //Se regresa un error en caso de que alguna consulta a la BD haya fallado
            $conexion->rollback();
            echo "Error en la transaccion";
        }    
    $conexion->autocommit(TRUE);
    $conexion->close();
?>