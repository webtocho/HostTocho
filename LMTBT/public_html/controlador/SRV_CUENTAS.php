<?php
    if(empty($_POST['fn'])){
        $_POST['fn'] = " ";
    }
    
    include("SRV_CONEXION.php");
    include("SRV_FUNCIONES.php");
    
    $conexion = (new SRV_CONEXION())->getConnection() or lanzar_error("Error de servidor (" . __LINE__ . ")");
    
    function get_padecimientos($mysqli, $id_usr, $tipo_dato){
        $indicaciones = array( array("en","enfermedades","ID_ENFERMEDAD"), array("al","alergias","ID_ALERGIA") );
        $padecimientos = array($indicaciones[0][0] => array(), $indicaciones[1][0] => array());
        
        foreach($indicaciones as $aux){
            $query = "SELECT " . ($tipo_dato ? $aux[1] . "." . $aux[2] : "NOMBRE") . " FROM " . $aux[1] . "_usuarios INNER JOIN " . $aux[1] . " ON " . $aux[1] . "_usuarios." . $aux[2] . " = " . $aux[1] . "." . $aux[2] . " WHERE ID_USUARIO = ?";
            
            if(($consulta = $mysqli->prepare($query)) && $consulta->bind_param("i", $id_usr) && $consulta->execute()){
                $res = $consulta->get_result();
                while ($fila = $res->fetch_row()) {
                    array_push($padecimientos[$aux[0]], $fila[0]);
                }
            } else {
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            
            $query = "SELECT DATOS FROM otras_" . $aux[1] . " WHERE ID_USUARIO = ?";
            if(($consulta = $mysqli->prepare($query)) && $consulta->bind_param("i", $id_usr) && $consulta->execute()){
                $res = $consulta->get_result();
                
                if($tipo_dato){
                    $padecimientos[$aux[0]]['otros'] = null;
                    if ($res->num_rows != 0){
                        $padecimientos[$aux[0]]['otros'] = str_replace(",", ", ", $res->fetch_row()[0]);
                    }
                } else {
                    if ($res->num_rows != 0){
                        array_push($padecimientos[$aux[0]], ...explode(",", $res->fetch_row()[0]));
                    }
                }
            } else {
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
        }
        
        return $padecimientos;
    }
    
    switch ($_POST['fn']){
        case "get_info":            
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
                        
                        if($año_nacimiento < ($año_actual - 50) || $año_nacimiento > ($año_actual - 8)){
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
                
                if (se_subio_archivo("ft")){
                    $foto = leer_imagen("ft", 150) or die();
                    $updates .= (empty($updates) ? "" : ", ") . "FOTO_PERFIL = '" . $foto . "'";
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
            
            if(!empty($_POST['ap_p']) || !empty($_POST['ap_m']) || !empty($_POST['nb'])){
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
            }
            
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
            
            if(!empty($_POST['nc'])){
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
                        $query = "SELECT ID_USUARIO FROM usuarios WHERE ID_USUARIO = ? AND TIPO_USUARIO = 'JUGADOR' "
                                    . get_restricciones_categoria($mysqli, $fila[0]);
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
            }
            
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
        default:
            lanzar_error("Error de servidor (" . __LINE__ . ")", false);
    }
    
    $conexion->close();
?>