<?php
    session_start();
    if(empty($_POST['fn'])){
        $_POST['fn'] = " ";
    }
    
    include("SRV_CONEXION.php");
    include("SRV_FUNCIONES.php");
    
    $conexion = (new SRV_CONEXION())->getConnection() or lanzar_error("Error de servidor (" . __LINE__ . ")");
    validar_sesion_y_expulsar(["ADMINISTRADOR", "COACH"]);
    
    /**
     * Revisa si el nombre del equipo es válido.
     * 
     * @param mysqli $mysqli Conexión a la base de datos.
     * @param string $key_nombre Llave del nombre a evaluar, en el arreglo $_POST.
     */
    function validar_nombre_equipo($mysqli, $key_nombre){
        $_POST[$key_nombre] = ucfirst(strtolower(eliminar_espacios($_POST[$key_nombre])));
        if(!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ '-]{2,}$/", $_POST[$key_nombre])){
            lanzar_error("Nombre inválido.");
        }

        $query = "SELECT ID_EQUIPO FROM equipos WHERE NOMBRE_EQUIPO like ?";
        if(($consulta = $mysqli->prepare($query)) && $consulta->bind_param("s", $_POST[$key_nombre]) && $consulta->execute()){
            $res = $consulta->get_result();
            if($res->num_rows != 0){
                lanzar_error("Ya existe un equipo con el nombre que especificó.");
            }
        } else {
            lanzar_error("Error de servidor (" . __LINE__ . ")");
        }
    }

    switch ($_POST['fn']){
        case "num":
            $query = "SELECT COUNT(*) FROM equipos";
            $param = array("");
            
            if ($_SESSION["TIPO_USUARIO"] == "COACH") {
                $query .= " WHERE ID_COACH = ?";
                $param[0] .= "i";
                array_push($param, $_SESSION["ID_USUARIO"]);
            }
            
            if(!empty($_POST['sr'])){
                $criterios = array_filter(explode(" ", eliminar_espacios(str_replace("%", "", $_POST['sr']))));
                if(count($criterios) > 3){
                    lanzar_error("Error: Sólo puede escribir 3 palabras/sílabas como máximo, para filtrar el nombre.");
                }
                
                foreach($criterios as $aux){
                    if (!empty($param[0])){ $query .= " AND";
                    } else { $query .= " WHERE"; }
                    
                    $query .= " NOMBRE_EQUIPO LIKE ?";
                    $param[0] .= "s";
                    $aux = "%" . $aux . "%";
                    array_push($param, $aux);
                }
                
                unset($criterios);
                unset($aux);
            }
            
            if(($consulta = $conexion->prepare($query)) && (!empty($param[0]) ? $consulta->bind_param(...$param) : true) && $consulta->execute()){
                $res = $consulta->get_result();
                $fila = $res->fetch_row();
                echo $fila[0];
            } else {
                lanzar_error("Error de servidor (" . __LINE__ . ")", false);
            }
            break;
        case "get":
            //Si no manda el ID.
            if(!isset($_POST['id'])){
                lanzar_error("Error de servidor (" . __LINE__ . ")", false);
            }
            
            //Si el usuario es coach, debemos comprobar que el id corresponda a un equipo que realmente le pertenece.
            validar_propiedad_equipo_coach($conexion, $_POST['id']);
            
            $datosASeleccionar = "";
            if(!empty($_POST['id_c'])){ $datosASeleccionar .= "ID_COACH"; }
            if(!empty($_POST['nb_e'])){ $datosASeleccionar .= (empty($datosASeleccionar) ? "" : ", ") . "NOMBRE_EQUIPO"; }
            if(!empty($_POST['nb_c'])){ $datosASeleccionar .= (empty($datosASeleccionar) ? "" : ", ") . "APELLIDO_PATERNO, APELLIDO_MATERNO, NOMBRE"; }
            if(!empty($_POST['lg'])){ $datosASeleccionar .= (empty($datosASeleccionar) ? "" : ", ") . "LOGOTIPO_EQUIPO"; }
            
            $info_equipo = array();
            
            $query = "SELECT " . $datosASeleccionar . " FROM equipos INNER JOIN usuarios ON equipos.ID_COACH = usuarios.ID_USUARIO WHERE ID_EQUIPO = ?";
            if(!empty($datosASeleccionar) && ($consulta = $conexion->prepare($query)) && $consulta->bind_param("i", $_POST['id']) && $consulta->execute()){
                $res = $consulta->get_result();
                if ($res->num_rows != 0){
                    $fila = $res->fetch_assoc();
                    
                    if(!empty($_POST['id_c'])){ $info_equipo["id_c"] = $fila["ID_COACH"]; }
                    if(!empty($_POST['nb_e'])){ $info_equipo["nb_e"] = $fila["NOMBRE_EQUIPO"]; }
                    if(!empty($_POST['nb_c'])){ $info_equipo["nb_c"] = $fila["APELLIDO_PATERNO"] . " " . $fila["APELLIDO_MATERNO"] . " " . $fila["NOMBRE"]; }
                    if(!empty($_POST['lg'])){ $info_equipo["lg"] = base64_encode($fila["LOGOTIPO_EQUIPO"]); }
                } else {
                    lanzar_error("Error de servidor (El equipo ya no existe)");
                }
            } else {
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            
            if(!empty($_POST['r_act']) || !empty($_POST['cat_d'])){
                //Este query es usado si se solicita la información de los rosters.
                $query =   "SELECT DISTINCT ros.id_roster, 
                                            ros.nombre_categoria 
                            FROM   (SELECT id_roster, 
                                           nombre_categoria, 
                                           id_convocatoria 
                                    FROM   rosters 
                                           INNER JOIN categorias 
                                                   ON rosters.id_categoria = categorias.id_categoria 
                                    WHERE  id_equipo = ?) AS ros 
                                    LEFT JOIN convocatoria AS con 
                                           ON ros.id_convocatoria = con.id_convocatoria 
                            WHERE  con.fecha_fin_torneo IS NULL 
                                   OR curdate() <= fecha_fin_torneo";
            }
            
            //Rosters activos
            if(!empty($_POST['r_act'])){
                /*Vamos a seleccionar los rosters creados que no están ligados a una torneo,
                  o están ligados a un torneo que aún no ha terminado*/
                if(($consulta = $conexion->prepare($query)) && $consulta->bind_param("i", $_POST['id']) && $consulta->execute()){
                    $res = $consulta->get_result();
                    $info_equipo["r_act"] = array();

                    while ($fila = $res->fetch_row()) {
                        array_push($info_equipo["r_act"], $fila);
                    }
                } else {
                    lanzar_error("Error de servidor (" . __LINE__ . ")");
                }
            }
            
            //Categorías disponible para crear rosters.
            if(!empty($_POST['cat_d'])){
                $categorias = array();
                $categorias_ocupadas = array();
                
                if(($consulta = $conexion->prepare($query)) && $consulta->bind_param("i", $_POST['id']) && $consulta->execute()){
                    $res = $consulta->get_result();
                    while ($fila = $res->fetch_row()) {
                        array_push($categorias_ocupadas, $fila[1]);
                    }
                } else {
                    lanzar_error("Error de servidor (" . __LINE__ . ")");
                }
                
                if(($consulta = $conexion->prepare("SELECT ID_CATEGORIA, NOMBRE_CATEGORIA FROM categorias")) && $consulta->execute()){
                    $res = $consulta->get_result();
                    while ($fila = $res->fetch_row()) {
                        array_push($categorias, $fila);
                    }
                } else {
                    lanzar_error("Error de servidor (" . __LINE__ . ")");
                }
                
                $info_equipo["cat_d"] = array();
                foreach ($categorias as $cat){
                    if(!in_array($cat[1], $categorias_ocupadas)){
                        array_push($info_equipo["cat_d"], $cat);
                    }
                }
            }
            
            echo json_encode($info_equipo);
            break;
        case "bus":
            $query = "SELECT ID_EQUIPO, NOMBRE_EQUIPO, LOGOTIPO_EQUIPO FROM equipos";
            $param = array("");
            $el_query_tiene_where = false;
            
            if ($_SESSION["TIPO_USUARIO"] == "COACH") {
                $query .= " WHERE ID_COACH = ?";
                $param[0] .= "i";
                array_push($param, $_SESSION["ID_USUARIO"]);
                $el_query_tiene_where = true;
            }
            
            if(!empty($_POST['sr'])){
                $criterios = array_filter(explode(" ", eliminar_espacios(str_replace("%", "", $_POST['sr']))));
                if(count($criterios) > 3){
                    lanzar_error("Error: Sólo puede escribir 3 palabras/sílabas como máximo, para filtrar el nombre.");
                }
                
                foreach($criterios as $aux){
                    if ($el_query_tiene_where){
                        $query .= " AND";
                    } else {
                        $query .= " WHERE";
                        $el_query_tiene_where = true;
                    }
                    
                    $query .= " NOMBRE_EQUIPO LIKE ?";
                    $param[0] .= "s";
                    $aux = "%" . $aux . "%";
                    array_push($param, $aux);
                }
                
                unset($criterios);
                unset($aux);
            }
            
            if(!empty($_POST['pageNumber']) && !empty($_POST['pageSize'])){
                $_POST['pageNumber'] = intval($_POST['pageNumber']);
                $_POST['pageSize'] = intval($_POST['pageSize']);
                
                $query .= " LIMIT ?,?";
                
                $param[0] .= "ii";
                array_push($param, (($_POST['pageNumber'] - 1) * $_POST['pageSize']), $_POST['pageSize']);
            }
            
            if(($consulta = $conexion->prepare($query)) && $consulta->bind_param(...$param) && $consulta->execute()){
                $res = $consulta->get_result();
                $equipos = array();
                
                while ($fila = $res->fetch_row()){
                    $fila[2] = base64_encode($fila[2]);
                    array_push($equipos, $fila);
                }
                
                echo json_encode($equipos);
            } else {
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            break;
        case "crear":
            //Revisamos si se mandaron todos los parámetros.
            if(empty($_POST['id']) || empty($_POST['nb']) || !se_subio_archivo("lg")){
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            
            if ($_SESSION["TIPO_USUARIO"] == "COACH"){
                if($_SESSION["ID_USUARIO"] != $_POST['id']){
                    //Un coach está tratando de crear un equipo a nombre de otro usuario.
                    lanzar_error("Error de servidor (" . __LINE__ . ")");
                }
            } else {
                if(!usuario_cumple_condicion($conexion, $_POST['id'], "AND TIPO_USUARIO = 'COACH'")){
                    //El admin está tratando de crear un equipo a nombre de un usuario que no es un coach.
                    lanzar_error("Error de servidor (" . __LINE__ . ")");
                }
            }
            
            validar_nombre_equipo($conexion, 'nb');
            
            $logotipo = leer_imagen("lg", 150) or die();
            $query = "INSERT INTO equipos (ID_EQUIPO, ID_COACH, NOMBRE_EQUIPO, LOGOTIPO_EQUIPO) VALUES (0, ?, ?, '" . $logotipo . "')";
            
            if (!(($consulta = $conexion->prepare($query)) && $consulta->bind_param("is", $_POST['id'], $_POST['nb']) && $consulta->execute())){
                lanzar_error("Error de servidor (" . __LINE__ . ")", false);
            }
            break;
        case "mod":
            if(empty($_POST['id_e']) || (empty($_POST['nb']) && !se_subio_archivo("lg") && empty($_POST['id_c']))){
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            
            //Si el usuario es coach, debemos comprobar que el id corresponda a un equipo que realmente le pertenece.
            validar_propiedad_equipo_coach($conexion, $_POST['id_e']);
            
            $query = "UPDATE equipos SET";
            $param = array("");
            
            if(!empty($_POST['nb'])){
                validar_nombre_equipo($conexion, 'nb');
                $query .= " NOMBRE_EQUIPO = ?";
                $param[0] .= "s";
                array_push($param, $_POST['nb']);
            }
            if(se_subio_archivo("lg")){
                $logotipo = leer_imagen("lg", 150) or die();
                $query .= (!empty($param[0]) ? "," : "") . " LOGOTIPO_EQUIPO = '" . $logotipo . "'";
            }
            if(!empty($_POST['id_c'])){
                if(!usuario_cumple_condicion($conexion, $_POST['id_c'], "AND TIPO_USUARIO = 'COACH'")){
                    //El nuevo dueño de este equipo no es un coach.
                    lanzar_error("Error de servidor (" . __LINE__ . ")");
                }
                
                $query .= (!empty($param[0]) ? "," : "") . " ID_COACH = ?";
                $param[0] .= "i";
                array_push($param, $_POST['id_c']);
            }
            
            $query .= " WHERE ID_EQUIPO = ?";
            $param[0] .= "i";
            array_push($param, $_POST['id_e']);
            
            if(!(($consulta = $conexion->prepare($query)) && $consulta->bind_param(...$param) && $consulta->execute())){
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            break;
        default:
            lanzar_error("Error de servidor (" . __LINE__ . ")", false);
    }
    
    $conexion->close();
?>