<?php
    if(empty($_POST['fn'])){
        $_POST['fn'] = " ";
    }
    
    include("SRV_CONEXION.php");
    include("SRV_FUNCIONES.php");
    
    $conexion = (new SRV_CONEXION())->getConnection() or lanzar_error("Error de servidor (" . __LINE__ . ")");
    
    /**
     * Permite saber las enfermedades y alergias que padece cierto usuario.
     * @param mysqli $mysqli Conexión a la base de datos.
     * @param int $id_usr ID del usuario del que se desea obtener la información.
     * @param boolean $ids_o_nombres Si es verdadero, se devuelven los ID's de las enf. y alg. según sus respectivas tablas;
     *                               en caso contrario, solo los nombres.
     * @return Array Un arreglo unidimensional que contiene dos subarreglos ("en" y "al"). Cada uno contiene (con claves numéricas) los nombres o ID's.
     *               Si $ids_o_nombres es verdadero, cada subarreglo tendrá una clave extra 'otros', que corresponde a un arreglo que contiene las otras
     *               enfermedades o alergias como cadenas, debido a que no tienen ID's.
     *               "en" corresponde a enfermedades y "al" a alergias.
     */
    function get_padecimientos($mysqli, $id_usr, $ids_o_nombres){
        /** Cada elemento de $indicaciones contiene la información de un tipo de padecimiento en especifico: "alergia" o "enfermedad".
         * "subarreglo": El nombre del subarreglo correspondiente al tipo de padecimiento.
         * "tabla_BD": Nombre de la tabla de la base de datos en donde se almacenan los padecimientos que el usuario tiene.
         * "pkey_tabla_BD": Llave primaria de la tabla mencionada en la línea anterior.
         */
        $indicaciones = array(
            array("subarreglo" => "en", "tabla_BD" => "enfermedades", "pkey_tabla_BD" => "ID_ENFERMEDAD"),
            array("subarreglo" => "al", "tabla_BD" => "alergias", "pkey_tabla_BD" => "ID_ALERGIA") );
        
        //Respuesta de la función.
        $padecimientos = array($indicaciones[0]["subarreglo"] => array(), $indicaciones[1]["subarreglo"] => array());
        
        foreach($indicaciones as $aux){
            $query = "SELECT " . ($ids_o_nombres ? $aux["tabla_BD"] . "." . $aux["pkey_tabla_BD"] : "NOMBRE") . " FROM " . $aux["tabla_BD"] . "_usuarios INNER JOIN " . $aux["tabla_BD"] . " ON " . $aux["tabla_BD"] . "_usuarios." . $aux["pkey_tabla_BD"] . " = " . $aux["tabla_BD"] . "." . $aux["pkey_tabla_BD"] . " WHERE ID_USUARIO = ?";
            
            if(($consulta = $mysqli->prepare($query)) && $consulta->bind_param("i", $id_usr) && $consulta->execute()){
                $res = $consulta->get_result();
                while ($fila = $res->fetch_row()) {
                    //Se obtiene cada nombre o id de los padecimientos que el ususario sufre y se añaden al subarreglo correspondiente.
                    array_push($padecimientos[$aux["subarreglo"]], $fila[0]);
                }
            } else {
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            
            $query = "SELECT DATOS FROM otras_" . $aux["tabla_BD"] . " WHERE ID_USUARIO = ?";
            if(($consulta = $mysqli->prepare($query)) && $consulta->bind_param("i", $id_usr) && $consulta->execute()){
                $res = $consulta->get_result();
                
                //Se obtienen los otros padecimientos como cadenas y se añaden según $ids_o_nombres.
                if($ids_o_nombres){
                    $padecimientos[$aux["subarreglo"]]["otros"] = null;
                    if ($res->num_rows != 0){
                        $padecimientos[$aux["subarreglo"]]["otros"] = str_replace(",", ", ", $res->fetch_row()[0]);
                    }
                } else {
                    if ($res->num_rows != 0){
                        array_push($padecimientos[$aux["subarreglo"]], ...explode(",", $res->fetch_row()[0]));
                    }
                }
            } else {
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
        }
        
        /** EJEMPLOS DE SALIDA:
         * 
         * - Si $ids_o_nombres es falso.
         * [
         *   "en" => [
         *      0 => "Diabetes"
         *      1 => "Tos crónica"
         *      2 => "Psoriasis"
         *   ]
         *   "al" => [
         *      0 => "Polvo"
         *      1 => "Polen"
         *      2 => "Metronidazol"
         *   ]
         * ]
         * 
         * - Si $ids_o_nombres es verdadero.
         * [
         *   "en" => [
         *      0 => 2
         *      1 => 4
         *      "otros" => [0 => "Psoriasis"]
         *   ]
         *   "al" => [
         *      0 => 1
         *      1 => 3
         *      "otros" => [0 => "Metronidazol"]
         *   ]
         * ]
         */
        
        return $padecimientos;
    }
    
    switch ($_POST['fn']){
        case "get_info":
            /**
             * Permite obtener la información de una cuenta en específico.
             * Requiere el parámetro "id_c", que corresponde al ID de la cuenta.
             * 
             * 
             */
            validar_sesion_y_expulsar();
            
            if($_SESSION["TIPO_USUARIO"] == "COACH" || $_SESSION["TIPO_USUARIO"] == "ADMINISTRADOR"){
                if (empty($_POST['id_c'])) {
                    $_POST['id_c'] = $_SESSION["ID_USUARIO"];
                }
            } else {
                if(empty($_POST['id_c'])){
                   $_POST['id_c'] = $_SESSION["ID_USUARIO"];
                } else {
                    //Los jugadores, capturistas y fotógrafos no pueden ver la información de cuentas ajenas.
                    lanzar_error("Error de servidor (No tiene permiso).");
                }
            }
            
            $datosASeleccionar = "";
            do {
                if (!empty($_POST['id']) && boolval($_POST['id'])){
                    $datosASeleccionar .= "ID_USUARIO AS id";
                }
                if (!empty($_POST['nb']) && boolval($_POST['nb'])){
                    $datosASeleccionar .= (empty($datosASeleccionar) ? "" : ", ") . "NOMBRE, APELLIDO_PATERNO, APELLIDO_MATERNO";
                }
                if (!empty($_POST['nb_c']) && boolval($_POST['nb_c'])){
                    $datosASeleccionar .= (empty($datosASeleccionar) ? "" : ", ") . "concat(APELLIDO_PATERNO, ' ', APELLIDO_MATERNO, ' ', NOMBRE) AS nb_c";
                }
                if (!empty($_POST['tp']) && boolval($_POST['tp'])){
                    $datosASeleccionar .= (empty($datosASeleccionar) ? "" : ", ") . "TIPO_USUARIO AS tp";
                }
                if (!empty($_POST['cr']) && boolval($_POST['cr'])){
                    $datosASeleccionar .= (empty($datosASeleccionar) ? "" : ", ") . "CORREO AS cr";
                }
                if (!empty($_POST['rd']) && boolval($_POST['rd'])){
                    $datosASeleccionar .= (empty($datosASeleccionar) ? "" : ", ") . "FACEBOOK, INSTAGRAM, TWITTER";
                }
                if (!empty($_POST['nc']) && boolval($_POST['nc'])){
                    $datosASeleccionar .= (empty($datosASeleccionar) ? "" : ", ") . "FECHA_NACIMIENTO AS nc";
                }
                if (!empty($_POST['ed']) && boolval($_POST['ed'])){
                    $datosASeleccionar .= (empty($datosASeleccionar) ? "" : ", ") . "TRUNCATE(DATEDIFF(CURDATE(), FECHA_NACIMIENTO)/365,0) AS ed";
                }
                if (!empty($_POST['sx']) && boolval($_POST['sx'])){
                    $datosASeleccionar .= (empty($datosASeleccionar) ? "" : ", ") . "SEXO AS sx";
                }
                if (!empty($_POST['ot']) && boolval($_POST['ot'])){
                    $datosASeleccionar .= (empty($datosASeleccionar) ? "" : ", ") . "TIPO_SANGRE, TELEFONO";
                }
                if (!empty($_POST['ft']) && boolval($_POST['ft'])){
                    $datosASeleccionar .= (empty($datosASeleccionar) ? "" : ", ") . "FOTO_PERFIL AS ft";
                }
                
                if(empty($datosASeleccionar)){
                    $_POST['nb'] = $_POST['nb_c'] = $_POST['tp'] = $_POST['cr'] = $_POST['rd'] = $_POST['nc'] = $_POST['ed'] = $_POST['sx'] = $_POST['ot'] = $_POST['ft'] = true;
                }
            } while (empty($datosASeleccionar));
            
            //Creamos nuestra consulta preparada.
            $query = 'SELECT ' . $datosASeleccionar . ' FROM usuarios WHERE ID_USUARIO = ?';
            
            if(!is_array($_POST['id_c'])){
                if(($consulta = $conexion->prepare($query)) && $consulta->bind_param("i", $_POST['id_c']) && $consulta->execute()){
                    $res = $consulta->get_result();

                    if ($res->num_rows != 0) {
                        //Vamos a enviar la fila de la tabla como resultado.
                        $info_cuenta = $res->fetch_assoc();
                        //Si se incluyó la foto de perfil, la convertimos a un formato legible.
                        if (!empty($_POST['ft']) && boolval($_POST['ft']) && $info_cuenta["ft"] != null) {
                            $info_cuenta["ft"] = base64_encode($info_cuenta["ft"]);
                        }
                        
                        if (!empty($_POST['pd']) && boolval($_POST['pd'])) {
                            $info_cuenta = array_merge($info_cuenta, get_padecimientos($conexion, $_POST['id_c'], ($_POST['pd'] == "2")));
                        }
                        
                        if (!empty($_POST['all_pd']) && boolval($_POST['all_pd'])) {
                            $info_cuenta['all_en'] = array();
                            if($res = $conexion->query("SELECT ID_ENFERMEDAD, NOMBRE FROM enfermedades")){
                                while ($fila = $res->fetch_row()) {
                                    array_push($info_cuenta['all_en'], $fila);
                                }
                            } else {
                                lanzar_error("Error de servidor (" . __LINE__ . ")");
                            }
                            
                            $info_cuenta['all_al'] = array();
                            if($res = $conexion->query("SELECT ID_ALERGIA, NOMBRE FROM alergias")){
                                while ($fila = $res->fetch_row()) {
                                    array_push($info_cuenta['all_al'], $fila);
                                }
                            } else {
                                lanzar_error("Error de servidor (" . __LINE__ . ")");
                            }
                        }
                        
                        echo json_encode($info_cuenta);
                    } else {
                        lanzar_error("Error de servidor (El usuario ya no existe)", false);
                    }
                } else {
                    lanzar_error("Error de servidor (" . __LINE__ . ")", false);
                }
            } else if ($consulta = $conexion->prepare($query)){
                $info_cuentas = array();
                foreach ($_POST['id_c'] as $jug) {
                    if($consulta->bind_param("i", $jug) && $consulta->execute() && ($res = $consulta->get_result())){
                        if ($res->num_rows != 0){
                            $tmp = $res->fetch_assoc();
                        
                            if (!empty($_POST['ft']) && boolval($_POST['ft']) && $tmp["ft"] != null) {
                                $tmp["ft"] = base64_encode($tmp["ft"]);
                            }

                            array_push($info_cuentas, $tmp);
                        } else {
                            array_push($info_cuentas, null);
                        }
                    } else {
                        lanzar_error("Error de servidor (Uno de los usuarios ya no existe)", false);
                    }
                }
                echo json_encode($info_cuentas);
            } else {
                lanzar_error("Error de servidor (" . __LINE__ . ")", false);
            }
            
            break;
        case "buscar":
            validar_sesion_y_expulsar(["ADMINISTRADOR", "COACH"]);
            
            $query = "SELECT";
            $param = array("");
            
            if(!empty($_POST['pageNumber']) && !empty($_POST['pageSize'])){
                $query .= " ID_USUARIO, CONCAT(APELLIDO_PATERNO, ' ', APELLIDO_MATERNO), NOMBRE";
                
                if(!empty($_POST['i_c']) && boolval($_POST['i_c'])){
                    $query .= ", CORREO";
                }
                if(!empty($_POST['i_s']) && boolval($_POST['i_s'])){
                    $query .= ", SEXO";
                }
                if(!empty($_POST['i_e']) && boolval($_POST['i_e'])){
                    $query .= ", TRUNCATE(DATEDIFF(CURDATE(), FECHA_NACIMIENTO)/365,0)";
                }
                if(!empty($_POST['i_f']) && boolval($_POST['i_f'])){
                    $query .= ", FOTO_PERFIL";
                }
            } else {
                $query .= " COUNT(*)";
            }
            
            if(empty($_POST['tipo'])){
                lanzar_error("Error de servidor (" . __LINE__ . ")", false);
                break;
            }
            
            $query .= " FROM usuarios WHERE TIPO_USUARIO = ?";
            $param[0] .= "s";
            array_push($param, $_POST['tipo']);
            
            if(!empty($_POST['nb'])){
                $criterios = array_filter(explode(" ", str_replace("%", "", $_POST['nb'])));
                if(count($criterios) > 4){
                    lanzar_error("Error: Sólo puede escribir 4 palabras/sílabas como máximo, para buscar en el nombre.");
                }
                
                foreach($criterios as $aux){
                    $query .= " AND NOMBRE LIKE ?";
                    $param[0] .= "s";
                    $aux = "%" . $aux . "%";
                    array_push($param, $aux);
                }
                
                unset($criterios);
                unset($aux);
            }
            
            if(!empty($_POST['ap'])){
                $criterios = array_filter(explode(" ", str_replace("%", "", $_POST['ap'])));
                if(count($criterios) > 4){
                    lanzar_error("Error: Sólo puede escribir 4 palabras/sílabas como máximo, para buscar en los apellidos.");
                }
                
                foreach($criterios as $aux){
                    $query .= " AND (APELLIDO_PATERNO LIKE ? OR APELLIDO_MATERNO LIKE ?)";
                    $param[0] .= "ss";
                    $aux = "%" . $aux . "%";
                    array_push($param, $aux, $aux);
                }
                
                unset($criterios);
                unset($aux);
            }
            
            if($_POST['tipo'] == "JUGADOR"){
                if(!empty($_POST['sexo'])){
                    $query .= " AND SEXO = ?";
                    $param[0] .= "s";
                    array_push($param, $_POST['sexo']);
                }
                
                if(!empty($_POST['edad'])){
                    $primer_char = substr($_POST['edad'],0,1);
                    if($primer_char === "<" || $primer_char === ">"){
                        $query .= " AND DATEDIFF(CURDATE(), FECHA_NACIMIENTO)/365 " . $primer_char . " ?";
                        $param[0] .= "i";
                        array_push($param, substr($_POST['edad'],1));
                    } else {
                        $criterios = array_filter(explode("-", $_POST['edad']));
                        if(count($criterios) == 2){
                            $query .= " AND DATEDIFF(CURDATE(), FECHA_NACIMIENTO)/365 >= ? AND DATEDIFF(CURDATE(), FECHA_NACIMIENTO)/365 <= ?";
                            $param[0] .= "ii";
                            array_push($param, $criterios[0], $criterios[1]);
                        } else {
                            lanzar_error("Error de servidor (" . __LINE__ . ")", false);
                            break;
                        }
                    }
                }
                
                if(!empty($_POST['id_cat'])){
                    $query .= " " . get_restricciones_categoria($conexion, $_POST['id_cat']);
                }
            }
            
            if(!empty($_POST['pageNumber']) && !empty($_POST['pageSize'])){
                $_POST['pageNumber'] = intval($_POST['pageNumber']);
                $_POST['pageSize'] = intval($_POST['pageSize']);
                
                $query .= " LIMIT ?,?";
                $param[0] .= "ii";
                array_push($param, (($_POST['pageNumber'] - 1) * $_POST['pageSize']), $_POST['pageSize']);
            }
            
            if($consulta = $conexion->prepare($query)){
                if($consulta->bind_param(...$param) && $consulta->execute()){
                    $res = $consulta->get_result();
                    
                    if (strpos($query, 'COUNT(*)') !== false){
                        $fila = $res->fetch_row();
                        echo $fila[0];
                    } else {
                        $usuarios = array();
                    
                        $se_incluye_foto = (strpos($query, 'FOTO_PERFIL') !== false);
                        while ($fila = $res->fetch_row()) {
                            if($se_incluye_foto && $fila[count($fila) - 1] != null){
                                $fila[count($fila) - 1] = base64_encode($fila[count($fila) - 1]);
                            }
                            array_push($usuarios, $fila);
                        }

                        echo json_encode($usuarios);
                    }
                } else {
                    lanzar_error("Error de servidor (" . __LINE__ . ")", false);
                    break;
                }
            } else {
                lanzar_error("Error de servidor (" . __LINE__ . ")", false);
                break;
            }
            
            break;
        case "mod":
            validar_sesion_y_expulsar();
            
            if (empty($_POST['id'])) {
                $_POST['id'] = $_SESSION["ID_USUARIO"];
            } else {
                if ($_SESSION["TIPO_USUARIO"] != "ADMINISTRADOR") {
                    lanzar_error("Usted no tiene permiso de editar cuentas ajenas.");
                }
            }
            
            $parametros = array("");
	    $updates = "";
            
            if (!empty($_POST['ap_p'])) {
                $_POST['ap_p'] = preparar_nombre($_POST['ap_p']);
                if (preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ' -]{2,}$/", $_POST['ap_p'])){
                    $parametros[0] .= "s";
                    array_push($parametros, $_POST['ap_p']);
                    $updates .= "APELLIDO_PATERNO = ?";
                } else {
                    lanzar_error("El apellido paterno está mal escrito (sólo debe contener letras).");
                }
            }
            
            if (!empty($_POST['ap_m'])) {
                $_POST['ap_m'] = preparar_nombre($_POST['ap_m']);
                if (preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ' -]{2,}$/", $_POST['ap_m'])){
                    $parametros[0] .= "s";
                    array_push($parametros, $_POST['ap_m']);
                    $updates .= (empty($updates) ? "" : ", ") . "APELLIDO_MATERNO = ?";
                } else {
                    lanzar_error("El apellido materno está mal escrito (sólo debe contener letras).");
                }
            }
            
            if (!empty($_POST['nb'])) {
                $_POST['nb'] = preparar_nombre($_POST['nb']);
                if (preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ' -]{2,}$/", $_POST['nb'])){
                    $parametros[0] .= "s";
                    array_push($parametros, $_POST['nb']);
                    $updates .= (empty($updates) ? "" : ", ") . "NOMBRE = ?";
                } else {
                    lanzar_error("El nombre está mal escrito (sólo debe contener letras).");
                }
            }
            
            if (!empty($_POST['cr'])) {
                $_POST['cr'] = eliminar_espacios($_POST['cr']);
                if (preg_match("/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/", $_POST['cr'])){
                    $parametros[0] .= "s";
                    array_push($parametros, $_POST['cr']);
                    $updates .= (empty($updates) ? "" : ", ") . "CORREO = ?";
                } else {
                    lanzar_error("El correo electrónico es inválido.");
                }
            }
            
            if (!empty($_POST['ps'])) {
                if (preg_match("/^[a-zA-Z]\w{4,15}$/", $_POST['ps'])){
                    $parametros[0] .= "s";
                    array_push($parametros, $_POST['ps']);
                    $updates .= (empty($updates) ? "" : ", ") . "PASSWORD = ?";
                } else {
                    lanzar_error("La contraseña es inválida. Debe tener entre 5 y 15 caracteres; letras (no acentuadas, excluyendo la Ñ) y/o números.");
                }
            }
            
            if (se_subio_archivo("ft")){
                $foto = leer_imagen("ft", 150) or die();
                $updates .= (empty($updates) ? "" : ", ") . "FOTO_PERFIL = '" . $foto . "'";
            }
            
            $es_jugador = false;
            if($_POST['id'] == $_SESSION["ID_USUARIO"]){
                $es_jugador = ($_SESSION["TIPO_USUARIO"] == "JUGADOR");
            } else {
                $query = "SELECT TIPO_USUARIO FROM usuarios WHERE ID_USUARIO = ?";
                if(($consulta = $conexion->prepare($query)) && $consulta->bind_param("i", $_POST['id']) && $consulta->execute()){
                    $res = $consulta->get_result();
                    if ($res->num_rows != 0){
                        $es_jugador = ($res->fetch_row()[0] == "JUGADOR");
                    } else {
                        lanzar_error("La cuenta ya no existe.");
                    }
                } else {
                    lanzar_error("Error de servidor (" . __LINE__ . ")");
                }
            }
            
            if ($es_jugador){
                if (!empty($_POST['nc'])) {
                    if( ($d = DateTime::createFromFormat("Y-m-d", $_POST['nc'])) && ($d->format("Y-m-d") == $_POST['nc']) ){
                        $año_actual = intval(date("Y"));
                        $año_nacimiento = intval($d->format("Y"));
                        
                        //|| $año_nacimiento > ($año_actual - 3)
                        if($año_nacimiento < ($año_actual - 100)){
                            lanzar_error("Año de nacimiento inválido.");
                        } else {
                            $parametros[0] .= "s";
                            array_push($parametros, $_POST['nc']);
                            $updates .= (empty($updates) ? "" : ", ") . "FECHA_NACIMIENTO = ?";
                        }
                    } else {
                        lanzar_error("La fecha de nacimiento es inválida.");
                    }
                }
                
                if (!empty($_POST['tel'])) {
                    $_POST['tel'] = eliminar_espacios($_POST['tel']);
                    if (preg_match("/^[0-9]{10}$/", $_POST['tel'])){
                        $parametros[0] .= "s";
                        array_push($parametros, $_POST['tel']);
                        $updates .= (empty($updates) ? "" : ", ") . "TELEFONO = ?";
                    } else {
                        lanzar_error("El número de teléfono es incorrecto (Debe de ser 10 dígitos, sin símbolos. Ej: \"9611234567\").");
                    }
                }
                
                if (!empty($_POST['sg'])) {
                    $parametros[0] .= "s";
                    array_push($parametros, $_POST['sg']);
                    $updates .= (empty($updates) ? "" : ", ") . "TIPO_SANGRE = ?";
                }
                
                if (!empty($_POST['fb'])) {
                    $_POST['fb'] = eliminar_espacios($_POST['fb']);
                    if (preg_match("/(?:(?:http|https):\/\/)?(?:www.)?facebook.com\/(?:(?:\w)*#!\/)?(?:pages\/)?(?:[?\w\-]*\/)?(?:profile.php\?id=(\d.*))?(?:[\w\-\.]*)?/", $_POST['fb'], $coincidencias)){
                        if (substr($coincidencias[0], 0, 7) !== "http://" && substr($coincidencias[0], 0, 8) !== "https://"){
                            $coincidencias[0] = "https://" . $coincidencias[0];
                        }
                        
                        $parametros[0] .= "s";
                        array_push($parametros, $coincidencias[0]);
                        $updates .= (empty($updates) ? "" : ", ") . "FACEBOOK = ?";
                    } else {
                        lanzar_error("El enlace de Facebook no es válido.");
                    }
                }
                
                if (!empty($_POST['tw'])) {
                    $_POST['tw'] = eliminar_espacios($_POST['tw']);
                    if (preg_match("/(?:(?:http|https):\/\/)?(?:www\.)?twitter\.com\/(?:(?:\w)*#!\/)?(?:pages\/)?(?:[\w\-]*\/)*(?:[\w\-]*)/", $_POST['tw'], $coincidencias)){
                        if (substr($coincidencias[0], 0, 7) !== "http://" && substr($coincidencias[0], 0, 8) !== "https://"){
                            $coincidencias[0] = "https://" . $coincidencias[0];
                        }
                        
                        $parametros[0] .= "s";
                        array_push($parametros, $coincidencias[0]);
                        $updates .= (empty($updates) ? "" : ", ") . "TWITTER = ?";
                    } else {
                        lanzar_error("El enlace de Twitter no es válido.");
                    }
                }
                
                if (!empty($_POST['ig'])) {
                    $_POST['ig'] = eliminar_espacios($_POST['ig']);
                    if (preg_match("/(?:(?:http|https):\/\/)?(?:www.)?(?:instagram.com|instagr.am)\/(?:[A-Za-z0-9-_]+)/", $_POST['ig'], $coincidencias)){
                        if (substr($coincidencias[0], 0, 7) !== "http://" && substr($coincidencias[0], 0, 8) !== "https://"){
                            $coincidencias[0] = "https://" . $coincidencias[0];
                        }
                        
                        $parametros[0] .= "s";
                        array_push($parametros, $coincidencias[0]);
                        $updates .= (empty($updates) ? "" : ", ") . "INSTAGRAM = ?";
                    } else {
                        lanzar_error("El enlace de Instagram no es válido.");
                    }
                }
            }
            
            iniciar_transaccion($conexion);
            
            if(!empty($updates)){
                $updates = "UPDATE usuarios SET " . $updates . " WHERE ID_USUARIO = ?";
                $parametros[0] .= "i";
                array_push($parametros, $_POST['id']);
                if(($consulta = $conexion->prepare($updates)) && $consulta->bind_param(...$parametros) && $consulta->execute()){
                    unset($updates, $parametros);
                } else {
                    cerrar_transaccion($conexion, false);
                    lanzar_error("Error de servidor (" . __LINE__ . ")");
                }
            }
            
            //Bloque eliminado: Se asegura de que no existan 2 cuentas con el mismo nombre completo.
            /*if(!empty($_POST['ap_p']) || !empty($_POST['ap_m']) || !empty($_POST['nb'])){
                $query = "SELECT APELLIDO_PATERNO, APELLIDO_MATERNO, NOMBRE FROM usuarios WHERE ID_USUARIO = ?";
                $query_2 = "SELECT ID_USUARIO FROM usuarios WHERE ID_USUARIO != ? AND (APELLIDO_PATERNO LIKE ? AND APELLIDO_MATERNO LIKE ? AND NOMBRE LIKE ?)";
                
                if(($consulta = $conexion->prepare($query)) && $consulta->bind_param("i", $_POST['id']) && $consulta->execute()
                    && ($res = $consulta->get_result()) && ($fila = $res->fetch_row())
                    && ($consulta = $conexion->prepare($query_2)) && $consulta->bind_param("isss", $_POST['id'], $fila[0], $fila[1], $fila[2])
                    && $consulta->execute() && ($res = $consulta->get_result())){
                    
                    if ($res->num_rows != 0){
                        cerrar_transaccion($conexion, false);
                        lanzar_error("Ya existe otro usuario con el mismo nombre completo.");
                    }
                } else {
                    cerrar_transaccion($conexion, false);
                    lanzar_error("Error de servidor (" . __LINE__ . ")");
                }
            }*/
            
            if(!empty($_POST['cr'])){
                $query = "SELECT ID_USUARIO FROM usuarios WHERE ID_USUARIO != ? AND CORREO LIKE ?";
                if(($consulta = $conexion->prepare($query)) && $consulta->bind_param("is", $_POST['id'], $_POST['cr']) && $consulta->execute()){
                    if ($consulta->get_result()->num_rows != 0){
                        cerrar_transaccion($conexion, false);
                        lanzar_error("El correo electrónico ya está siendo ocupado por otra persona.");
                    }
                } else {
                    cerrar_transaccion($conexion, false);
                    lanzar_error("Error de servidor (" . __LINE__ . ")");
                }
            }
            
            /*Bloque eliminado: Se revisa si, después de cambiar la fecha de nacimiento, el jugador aún califica
              para todos los rosters en los que participa*/
            /*if(!empty($_POST['nc'])){
                //Seleccionamos los ID's de las categorías de los rosters activos donde participa el usuario.
                $query = "SELECT DISTINCT ros.id_categoria 
                            FROM   (SELECT rosters.id_categoria, 
                                           rosters.id_convocatoria 
                                    FROM   rosters 
                                           INNER JOIN participantes_rosters 
                                                   ON rosters.id_roster = participantes_rosters.id_roster 
                                    WHERE  id_jugador = ?) AS ros 
                                   LEFT JOIN convocatoria AS con 
                                          ON ros.id_convocatoria = con.id_convocatoria 
                            WHERE  con.fecha_fin_torneo IS NULL 
                                    OR curdate() <= fecha_fin_torneo";
                
                if(($consulta = $conexion->prepare($query)) && $consulta->bind_param("i", $_POST['id']) && $consulta->execute()){
                    $res = $consulta->get_result();
                    while ($fila = $res->fetch_row()) {
                        $restricciones = get_restricciones_categoria($mysqli, $fila[0]);
                        if(empty($restricciones)){ continue; }
                        
                        $query = "SELECT ID_USUARIO FROM usuarios WHERE ID_USUARIO = ? AND TIPO_USUARIO = 'JUGADOR' "
                                    . $restricciones;
                        if(($consulta = $conexion->prepare($query)) && $consulta->bind_param("i", $_POST['id']) && $consulta->execute()){
                            if ($consulta->get_result()->num_rows != 0){
                                cerrar_transaccion($conexion, false);
                                lanzar_error("Es parte de uno o varios rosters vigentes. Los cambios no se pueden aplicar porque dejaría de aplicar en ellos.");
                            }
                        } else {
                            cerrar_transaccion($conexion, false);
                            lanzar_error("Error de servidor (" . __LINE__ . ")");
                        }
                    }
                } else {
                    cerrar_transaccion($conexion, false);
                    lanzar_error("Error de servidor (" . __LINE__ . ")");
                }
            }*/
            
            if(isset($_POST['en']) && isset($_POST['al'])){
                $_POST['en'] = json_decode($_POST['en']);
                $_POST['al'] = json_decode($_POST['al']);
                if(!is_array($_POST['en']) || !is_array($_POST['al'])){
                    cerrar_transaccion($conexion, false);
                    lanzar_error("Error de servidor (" . __LINE__ . ")");
                }
                
                $padecimientos = get_padecimientos($conexion, $_POST['id'], true);
                
                $query = "INSERT INTO enfermedades_usuarios (ID_USUARIO, ID_ENFERMEDAD) VALUES (?,?)";
                $consulta = $conexion->prepare($query) or lanzar_error("Error de servidor (" . __LINE__ . ")");
                foreach (array_diff($_POST['en'], $padecimientos['en']) as $enf) {
                    if(!$consulta->bind_param("ii", $_POST['id'], $enf) || !$consulta->execute()){
                        cerrar_transaccion($conexion, false);
                        lanzar_error("Error de servidor (" . __LINE__ . ")");
                    }
                }
                
                $query = "DELETE FROM enfermedades_usuarios WHERE ID_USUARIO = ? AND ID_ENFERMEDAD = ?";
                $consulta = $conexion->prepare($query) or lanzar_error("Error de servidor (" . __LINE__ . ")");
                foreach (array_diff($padecimientos['en'], $_POST['en']) as $enf) {
                    if(!$consulta->bind_param("ii", $_POST['id'], $enf) || !$consulta->execute()){
                        cerrar_transaccion($conexion, false);
                        lanzar_error("Error de servidor (" . __LINE__ . ")");
                    }
                }
                
                $query = "INSERT INTO alergias_usuarios (ID_USUARIO, ID_ALERGIA) VALUES (?,?)";
                $consulta = $conexion->prepare($query) or lanzar_error("Error de servidor (" . __LINE__ . ")");
                foreach (array_diff($_POST['al'], $padecimientos['al']) as $alg) {
                    if(!$consulta->bind_param("ii", $_POST['id'], $alg) || !$consulta->execute()){
                        cerrar_transaccion($conexion, false);
                        lanzar_error("Error de servidor (" . __LINE__ . ")");
                    }
                }
                
                $query = "DELETE FROM alergias_usuarios WHERE ID_USUARIO = ? AND ID_ALERGIA = ?";
                $consulta = $conexion->prepare($query) or lanzar_error("Error de servidor (" . __LINE__ . ")");
                foreach (array_diff($padecimientos['al'], $_POST['al']) as $alg) {
                    if(!$consulta->bind_param("ii", $_POST['id'], $alg) || !$consulta->execute()){
                        cerrar_transaccion($conexion, false);
                        lanzar_error("Error de servidor (" . __LINE__ . ")");
                    }
                }
            }
            
            function procesar_otros_padecimientos($ot){
                $lista = array_unique(array_filter( array_map("preparar_oracion", preg_split( "/[.|,|;]/", $ot )), function($val){ return !empty($val); } ));
                if(count($lista) == 0){ 
                    lanzar_error("No se encontró ninguna enfermedad extra en el campo correspondiente.", false);
                    return false;
                }
                
                foreach ($lista as $aux) {
                    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ' -]{2,}$/", $aux)){
                        lanzar_error("La enfermedad \"$aux\" está mal escrita (Sólo se admiten letras).", false);
                        return false;
                    }
                }
                
                return implode(",", $lista);
            }
            
            if(isset($_POST['ot_en'])){
                $_POST['ot_en'] = eliminar_espacios($_POST['ot_en']);
                
                if(empty($_POST['ot_en'])){
                    $query = "DELETE FROM otras_enfermedades WHERE ID_USUARIO = ?";
                    if(!($consulta = $conexion->prepare($query)) || !$consulta->bind_param("i", $_POST['id']) || !$consulta->execute()){
                        cerrar_transaccion($conexion, false);
                        lanzar_error("Error de servidor (" . __LINE__ . ")");
                    }
                } else {
                    $_POST['ot_en'] = procesar_otros_padecimientos($_POST['ot_en']);
                    if($_POST['ot_en'] == false){ cerrar_transaccion($conexion, false); die(); }
                    
                    $query = "INSERT INTO otras_enfermedades (ID_USUARIO, DATOS) VALUES (?,?) ON DUPLICATE KEY UPDATE DATOS = ?";
                    if(!($consulta = $conexion->prepare($query)) || !$consulta->bind_param("iss", $_POST['id'], $_POST['ot_en'], $_POST['ot_en']) || !$consulta->execute()){
                        cerrar_transaccion($conexion, false);
                        lanzar_error("Error de servidor (" . __LINE__ . ")");
                    }
                }
            }
            
            if(isset($_POST['ot_al'])){
                $_POST['ot_al'] = eliminar_espacios($_POST['ot_al']);
                
                if(empty($_POST['ot_al'])){
                    $query = "DELETE FROM otras_alergias WHERE ID_USUARIO = ?";
                    if(!($consulta = $conexion->prepare($query)) || !$consulta->bind_param("i", $_POST['id']) || !$consulta->execute()){
                        cerrar_transaccion($conexion, false);
                        lanzar_error("Error de servidor (" . __LINE__ . ")");
                    }
                } else {
                    $_POST['ot_al'] = procesar_otros_padecimientos($_POST['ot_al']);
                    if($_POST['ot_al'] == false){ cerrar_transaccion($conexion, false); die(); }
                    
                    $query = "INSERT INTO otras_alergias (ID_USUARIO, DATOS) VALUES (?,?) ON DUPLICATE KEY UPDATE DATOS = ?";
                    if(!($consulta = $conexion->prepare($query)) || !$consulta->bind_param("iss", $_POST['id'], $_POST['ot_al'], $_POST['ot_al']) || !$consulta->execute()){
                        cerrar_transaccion($conexion, false);
                        lanzar_error("Error de servidor (" . __LINE__ . ")");
                    }
                }
            }
            
            cerrar_transaccion($conexion, true);
            break;
        case "borrar_ft":
            if(empty($_POST['id'])){
                lanzar_error("No se envió el parámetro.");
            }
            
            if ($_SESSION["TIPO_USUARIO"] != "ADMINISTRADOR") {
                lanzar_error("Usted no tiene permiso para realizar esta acción.");
            }
            
            $query = "UPDATE usuarios SET FOTO_PERFIL = NULL WHERE ID_USUARIO = ?";
            if(!(($consulta = $conexion->prepare($query)) && $consulta->bind_param("i", $_POST['id']) && $consulta->execute())){
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            break;
        case "borrar":
            if(empty($_POST['id'])){
                lanzar_error("No se envió el parámetro.");
            }
            
            if ($_SESSION["TIPO_USUARIO"] != "ADMINISTRADOR") {
                lanzar_error("Usted no tiene permiso para realizar esta acción.");
            }
            
            $query = "select TIPO_USUARIO from usuarios WHERE ID_USUARIO = ?";
            if(($consulta = $conexion->prepare($query)) && $consulta->bind_param("i", $_POST['id']) && $consulta->execute()){
                $res = $consulta->get_result();
                if($res->num_rows == 0){ lanzar_error("El usuario ya no existe."); } else {
                    switch ($res->fetch_row()[0]){
                        case "ADMINISTRADOR":
                            lanzar_error("Su cuenta no puede ser eliminada, sólo puede transferir su propiedad a otra persona.");
                            break;
                        case "COACH":
                            $query = "SELECT COUNT(*) FROM equipos WHERE ID_COACH = ?";
                            if(($consulta = $conexion->prepare($query)) && $consulta->bind_param("i", $_POST['id']) && $consulta->execute() && ($res = $consulta->get_result())){
                                if($res->fetch_row()[0] != 0){
                                    lanzar_error("Este coach está dirigiendo equipos. Primero debe borrar sus equipos o transferirles la propiedad a otros coaches.");
                                }
                            } else {
                                lanzar_error("Error de servidor (" . __LINE__ . ")");
                            }
                            break;
                    }
                }
            } else {
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            
            $query = "DELETE FROM usuarios WHERE ID_USUARIO = ?";
            if(($consulta = $conexion->prepare($query)) && $consulta->bind_param("i", $_POST['id']) && $consulta->execute()){
                if($consulta->affected_rows == 0){
                    lanzar_error("El usuario ya no existe.");
                }
            } else {
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            break;
        default:
            lanzar_error("Error de servidor (" . __LINE__ . ")", false);
    }
    
    $conexion->close();
?>