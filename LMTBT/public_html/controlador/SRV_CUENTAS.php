<?php
    if(empty($_POST['fn'])){
        $_POST['fn'] = " ";
    }
    
    include("SRV_CONEXION.php");
    include("SRV_FUNCIONES.php");
    
    $conexion = (new SRV_CONEXION())->getConnection() or lanzar_error("Error de servidor (" . __LINE__ . ")");
    
    switch ($_POST['fn']){
        case "get_info":            
            validar_sesion_y_expulsar();
            
            if($_SESSION["TIPO_USUARIO"] == "COACH" || $_SESSION["TIPO_USUARIO"] == "ADMINISTRADOR"){
                if (empty($_POST['idCuenta'])) {
                    $_POST['idCuenta'] = $_SESSION["ID_USUARIO"];
                }
            } else {
                if(empty($_POST['idCuenta'])){
                   $_POST['idCuenta'] = $_SESSION["ID_USUARIO"];
                } else {
                    //Los jugadores, capturistas y fotógrafos no pueden ver la información de cuentas ajenas.
                    lanzar_error("Error de servidor (No tiene permiso).");
                }
            }
            
            $datosASeleccionar = "";
            if (isset($_POST['id']) && boolval($_POST['id'])){
                $datosASeleccionar .= "ID_USUARIO";
            }
            if (isset($_POST['nombre']) && boolval($_POST['nombre'])){
                $datosASeleccionar .= (empty($datosASeleccionar) ? "" : ", ") . "NOMBRE, APELLIDO_PATERNO, APELLIDO_MATERNO";
            }
            if (isset($_POST['tipo_usuario']) && boolval($_POST['tipo_usuario'])){
                $datosASeleccionar .= (empty($datosASeleccionar) ? "" : ", ") . "TIPO_USUARIO";
            }
            if (isset($_POST['correo']) && boolval($_POST['correo'])){
                $datosASeleccionar .= (empty($datosASeleccionar) ? "" : ", ") . "CORREO";
            }
            if (isset($_POST['redes']) && boolval($_POST['redes'])){
                    $datosASeleccionar .= (empty($datosASeleccionar) ? "" : ", ") . "FACEBOOK, INSTAGRAM, TWITTER";
            }
            if (isset($_POST['otros']) && boolval($_POST['otros'])){
                $datosASeleccionar .= (empty($datosASeleccionar) ? "" : ", ") . "FECHA_NACIMIENTO, SEXO, TIPO_SANGRE, TELEFONO";
            }
            if (isset($_POST['foto']) && boolval($_POST['foto'])){
                $datosASeleccionar .= (empty($datosASeleccionar) ? "" : ", ") . "FOTO_PERFIL";
            }
            if (empty($datosASeleccionar))
                $datosASeleccionar .= "*";
            
            //Creamos nuestra consulta preparada.
            $consulta = $conexion->prepare('SELECT ' . $datosASeleccionar . ' FROM usuarios WHERE ID_USUARIO = ?');
            
            if ($consulta->bind_param("i", $_POST['idCuenta']) && $consulta->execute()) {
                $res = $consulta->get_result();

                if ($res->num_rows == 1) {
                    //Vamos a enviar la fila de la tabla como resultado.
                    $info_cuenta = $res->fetch_assoc();
                    //Si se incluyó la foto de perfil, la convertimos a un formato legible.
                    if (!empty($info_cuenta["FOTO_PERFIL"])) {
                        $info_cuenta["FOTO_PERFIL"] = base64_encode($info_cuenta["FOTO_PERFIL"]);
                    }
                    
                    //Eliminamos datos comprometedores.
                    unset($info_cuenta['PASSWORD']);
                    
                    echo json_encode($info_cuenta);
                } else {
                    lanzar_error("Error de servidor (El usuario ya no existe)", false);
                }
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
            } else if ($_SESSION["TIPO_USUARIO"] == "COACH" && $_POST['tipo'] != "JUGADOR"){
                lanzar_error("Error de servidor (Los coaches sólo pueden buscar jugadores)", false);
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
        default:
            lanzar_error("Error de servidor (" . __LINE__ . ")", false);
    }
    
    $conexion->close();
?>