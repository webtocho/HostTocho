<?php
    /*ESTE ARCHIVO ES DE JAVIER, Y LO USA EN TODOS SUS PHP.
      SI USTEDES LO QUIEREN USAR, DEBEN ENTENDERLO PRIMERO Y RESOLVER SUS DUDAS CON ÉL.*/
    
    date_default_timezone_set('America/Mexico_City');
    
    /**
     * Esta función se ejecuta automáticamente al terminar la ejecución del PHP.
     * Si se suscitó un error a nivel de PHP (como una variable no declarada), se lanza un error.
     */
    function checar_error(){
        $last_error = error_get_last();
        if ($last_error && ($last_error['type'] == E_ERROR && $last_error['type'] == E_NOTICE)) {
            //ob_end_clean();
            lanzar_error("Error de servidor.", false);
        }
    }
    register_shutdown_function('checar_error');
    
    /**
     * Abre la sesión si es necesario.
     */
    if(!isset($_SESSION)) { 
        session_start(); 
    } 
    
    /**
     * Permite lanzar un error de PHP, haciendo que se devuelva el error #500.
     * @param string $texto Un texto explicando el problema.
     * @param bool $die Si la ejecución de este PHP se cancela (se mata) después de lanzar el error.
     */
    function lanzar_error($texto, $die = true){
        //Hacemos que se devuelva el error 500.
        http_response_code(500);
        echo $texto;
        //Terminamos la ejecución del php si es necesario.
        if($die) die();
    }
    
    /**
     * Valida si el usuario que tenga su sesión iniciada en el momento es de cierto tipo.
     * De no ser así, se lanza un error y se ejecuta un 'die()'.
     * No devuelve nada.
     * 
     * EJEMPLOS:
     * validar_sesion_y_expulsar(["ADMINISTRADOR", "COACH"]);
     * validar_sesion_y_expulsar(["JUGADOR"]);
     * 
     * @param array $tipos Un arreglo con el o los tipos a validar.
     */
    function validar_sesion_y_expulsar($tipos = NULL){
        if (!empty($_SESSION["ID_USUARIO"]) && !empty($_SESSION["TIPO_USUARIO"])){
            if($tipos != NULL && is_array($tipos)){
                if(!in_array($_SESSION["TIPO_USUARIO"], $tipos)){
                    lanzar_error("Error de servidor (No tiene permiso de acceso).");
                }
            }
        } else {
            lanzar_error("Error de servidor (Sesión no iniciada).");
        }
    }
    
    /**
     * Permite saber si un archivo se subió a través del método POST.
     * @param type $campo Nombre del campo en POST.
     * @return boolean
     */
    function se_subio_archivo($campo){
        if(empty($_FILES[$campo]['tmp_name']) || !file_exists($_FILES[$campo]['tmp_name']) || !is_uploaded_file($_FILES[$campo]['tmp_name'])){
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * Permite leer una imagen y obtener su contenido listo para subirlo a la base de datos.
     * Cuando ejecuta esto, se da por entendido que el archivo se subió.
     * En caso de error se retorna 'false' y se lanza un error (el #500).
     * 
     * La imagen será devuelta en formato PNG (si el usuario la sube en otro formato, será convertida).
     * 
     * @param string $campo El nombre del campo mandado desde el método POST.
     * @param int $alto (Opcional). Si lo manda, la imagen se achicará y tendrá el alto especificado. 
     * @return boolean / file : 'false' si hubo algún error. El contenido del archivo en caso de éxito.
     */
    function leer_imagen($campo, $alto = NULL){
        //Revisamos si hubo algún error.
        switch ($_FILES[$campo]['error']){
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                lanzar_error("La imagen es demasiado pesada.", false);
                return false;
            case UPLOAD_ERR_PARTIAL:
                lanzar_error("La imagen no se subió completa. Intente de nuevo...", false);
                return false;
            case UPLOAD_ERR_OK:
                break;
            default:
                lanzar_error("Error de servidor.", false);
                return false;
        }
        
        if(getimagesize($_FILES[$campo]["tmp_name"]) === false){
            lanzar_error("El archivo del logotipo que ha elegido no es una imagen real.", false);
            return false;
        }
        
        if($alto != NULL){
            if(!is_int($alto)){
                lanzar_error("Error de servidor.", false);
                return false;
            }
            
            //Cargamos la imagen original.
            $fp = fopen($_FILES[$campo]['tmp_name'], 'r+');
            $img_original = imagecreatefromstring(fread($fp, filesize($_FILES[$campo]['tmp_name'])));
            fclose($fp);

            //Calculamos el ancho.
            $ancho = imagesx($img_original) / imagesy($img_original) * $alto;

            //Achicamos la imagen.
            $foto_achicada = imagecreatetruecolor($ancho, $alto);
            imagesavealpha($foto_achicada, true);
            imagecopyresampled($foto_achicada, $img_original, 0, 0, 0, 0, $ancho, $alto, imagesx($img_original), imagesy($img_original));

            //Gurardamos la imagen achicada en un archivo temporal.
            $nombre_img_tmp = uniqid() . ".png";
            imagepng($foto_achicada, $nombre_img_tmp);

            $resultado = file_get_contents($nombre_img_tmp);

            //Eliminamos la copia de la imagen achicada en nuestro servidor.
            unlink($nombre_img_tmp);

            return addslashes($resultado);
        } else {
            $nombre_img_tmp = uniqid() . ".png";
            imagepng(file_get_contents($_FILES[$campo]['tmp_name']), $nombre_img_tmp);
            $resultado = file_get_contents($nombre_img_tmp);
            unlink($nombre_img_tmp);
            return addslashes($resultado);
        }
    }
    
    /**
     * Elimina los espacios al principio y final de un string.
     * También corrige espacios múltiples en medio del mismo.
     * Ej. " Hola   mundo  " -> "Hola mundo"
     * 
     * @param sting $str La cadena a procesar.
     * @return string
     */
    function eliminar_espacios($str){
        $str = trim($str);
        $str = preg_replace('/\s+/', ' ',$str);
        return $str;
    }
    
    /**
     * Revisa si el usuario cumple una o varias condiciones.
     * Si $condicion contiene uno o varios '?', debe mandar los parámetros opcionales.
     * 
     * EJEMPLOS:
     * usuario_cumple_condicion($conexion, 4, "AND TIPO_USUARIO = 'COACH'");
     * usuario_cumple_condicion($conexion, $_POST['id'], "AND TIPO_USUARIO = 'JUGADOR' 
     *                          AND SEXO = ? AND NOMBRE LIKE ?", 'ss', ['F','Juan']);
     * 
     * @param type $mysqli Conexión a la base de datos.
     * @param type $id_usuario El id del ususario al que se le hace la comprobación.
     * @param type $condicion La o las condiciones (la cadena debe empezar con 'AND').
     * @param type $tipos_param (Opcional) Los tipos de los '?'. Ej: "iis".
     * @param type $parametros (Opcional) Un arreglo con los valores de los '?'.
     * @return boolean Si el usuario cumple la o las condiciones.
     */
    function usuario_cumple_condicion($mysqli, $id_usuario, $condicion, $tipos_param = null, $parametros = null){
        $query = "SELECT ID_USUARIO FROM usuarios WHERE ID_USUARIO = ? " . $condicion;
        $prm = array("i", $id_usuario);
        
        if($tipos_param !== null && $parametros !== null){
            $prm[0] .= $tipos_param;
            $prm = array_merge($prm, $parametros);
        }
        
        if(($consulta = $mysqli->prepare($query)) && $consulta->bind_param(...$prm) && $consulta->execute()){
            $res = $consulta->get_result();
            if($res->num_rows === 0){
                return false;
            } else {
                return true;
            }
        } else {
            lanzar_error("Error de servidor (" . __LINE__ . ")");
        }
    }
    
    function iniciar_transaccion($mysqli){
        $mysqli->autocommit(FALSE);
    }
    
    function cerrar_transaccion($mysqli, $guardar_cambios){
        if($guardar_cambios){
            if(!$mysqli->commit()){
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
        } else {
            if(!$mysqli->rollback()){
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
        }
        $mysqli->autocommit(TRUE);
    }
?>