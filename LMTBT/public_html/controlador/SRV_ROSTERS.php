<?php
    session_start();
    if(empty($_POST['fn'])){
        $_POST['fn'] = " ";
    }
    
    include("SRV_CONEXION.php");
    include("SRV_FUNCIONES.php");
    
    //PENDIENTE: Preguntar miembros máximos del roster.
    function validar_info_roster($mysqli, $id_categoria, $miembros, $numeros){
        $mb_length = count($miembros);
        $nm_length = count($numeros);

        if($mb_length != $nm_length){
            lanzar_error("Inconsistencia en los datos.");
        } else if ($mb_length != count(array_unique($miembros))){
            lanzar_error("Hay jugadores repetidos.");
        } else if ($nm_length != count(array_unique($numeros))){
            lanzar_error("Hay números de jugadores repetidos.");
        } else if ($mb_length < 5){
            lanzar_error("Hay muy pocos jugadores.");
        } else if ($mb_length > 20){
            lanzar_error("Hay demasiados jugadores (más de 20).");
        } else {
            unset($mb_length, $nm_length);
        }

        $query = "SELECT ID_USUARIO FROM usuarios WHERE ID_USUARIO = ? AND TIPO_USUARIO = 'JUGADOR' "
                . get_restricciones_categoria($mysqli, $id_categoria);
        if($consulta = $mysqli->prepare($query)){
            foreach ($miembros as $miembro){
                if($consulta->bind_param("i", $miembro) && $consulta->execute()){
                    if($consulta->get_result()->num_rows == 0){
                        lanzar_error("Al menos un jugador no pertenece a la categoría seleccionada.");
                    }
                } else {
                    lanzar_error("Error de servidor (" . __LINE__ . ")");
                }
            }
        } else {
            lanzar_error("Error de servidor (" . __LINE__ . ")");
        }

        foreach ($numeros as $num){
            //PENDIENTE: PREGUNTAR NÚMEROS MÁXIMO Y MÍNIMO
            if(!is_numeric($num) || $num < 0 || $num > 99){
                lanzar_error("Uno de los números de jugador es inválido ($num).");
            }
        }
    }
    
    $conexion = (new SRV_CONEXION())->getConnection() or lanzar_error("Error de servidor (" . __LINE__ . ")");
    validar_sesion_y_expulsar(["ADMINISTRADOR", "COACH"]);
    
    switch ($_POST['fn']){
        case "get":
            if(empty($_POST['id']) || !is_numeric($_POST['id'])){
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            
            $info_roster = array();
            
            $query = "SELECT ids.id_equipo, 
                            ids.id_categoria, 
                            nombre_equipo, 
                            nombre_categoria, 
                            nombre_torneo 
                     FROM   (SELECT id_equipo, 
                                    id_categoria, 
                                    id_convocatoria 
                             FROM   rosters 
                             WHERE  id_roster = ?) AS ids 
                            INNER JOIN equipos 
                                    ON ids.id_equipo = equipos.id_equipo 
                            INNER JOIN categorias 
                                    ON ids.id_categoria = categorias.id_categoria 
                            LEFT JOIN convocatoria 
                                   ON ids.id_convocatoria = convocatoria.id_convocatoria";
            if(($consulta = $conexion->prepare($query)) && $consulta->bind_param("i", $_POST['id']) && $consulta->execute()){
                $res = $consulta->get_result();
                if ($res->num_rows != 0){
                    $info_basica = $res->fetch_row();
                    validar_propiedad_equipo_coach($conexion, $info_basica[0]);
                    $info_roster['id_cat'] = $info_basica[1];
                    $info_roster['eq'] = $info_basica[2];
                    $info_roster['cat'] = $info_basica[3];
                    $info_roster['tor'] = $info_basica[4];
                } else {
                    lanzar_error("El roster ya no existe.");
                }
            } else {
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            
            //Similar al query de "mod".
            $query ="SELECT ros.id_equipo 
                     FROM   (SELECT id_equipo, 
                                    id_categoria, 
                                    id_convocatoria 
                             FROM   rosters 
                             WHERE  id_roster = ?) AS ros 
                            LEFT JOIN convocatoria 
                                   ON ros.id_convocatoria = convocatoria.id_convocatoria 
                     WHERE  fecha_fin_torneo IS NULL 
                             OR curdate() < date_sub(fecha_cierre_convocatoria, interval 7 day)";
            if(($consulta = $conexion->prepare($query)) && $consulta->bind_param("i", $_POST['id']) && $consulta->execute()){
                //Es editable
                $info_roster['es_ed'] = ($consulta->get_result()->num_rows == 1);
            } else {
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            
            $query = "SELECT ID_JUGADOR, NUMERO FROM participantes_rosters WHERE ID_ROSTER = ? ORDER BY NUMERO";
            if(($consulta = $conexion->prepare($query)) && $consulta->bind_param("i", $_POST['id']) && $consulta->execute()){
                $res = $consulta->get_result();
                $info_roster['mb'] = array();
                $info_roster['nm'] = array();
                while ($fila = $res->fetch_row()){
                    array_push($info_roster['mb'], $fila[0]);
                    array_push($info_roster['nm'], $fila[1]);
                }
            } else {
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            
            echo json_encode($info_roster);
            break;
        case "crear":
            if(empty($_POST['id_e']) || empty($_POST['id_ct']) || empty($_POST['mb']) || !is_array($_POST['mb']) ||
                    empty($_POST['nm']) || !is_array($_POST['nm'])){
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            
            validar_propiedad_equipo_coach($conexion, $_POST['id_e']);
            validar_info_roster($conexion, $_POST['id_ct'], $_POST['mb'], $_POST['nm']);
            
            iniciar_transaccion($conexion);
            
            $query = "INSERT INTO rosters (ID_ROSTER, ID_CONVOCATORIA, ID_EQUIPO, ID_CATEGORIA) VALUES (0, null, ?, ?)";
            if(($consulta = $conexion->prepare($query)) && $consulta->bind_param("ii", $_POST['id_e'], $_POST['id_ct']) && $consulta->execute()){
                $id_roster = $consulta->insert_id;
                
                $query = "INSERT INTO participantes_rosters (ID_ROSTER, ID_JUGADOR, NUMERO) VALUES (?, ?, ?)";
                if($consulta = $conexion->prepare($query)){
                    foreach ($_POST['mb'] as $i => $miembro){
                        if(!( $consulta->bind_param("iii", $id_roster, $miembro, $_POST['nm'][$i] ) && $consulta->execute())){
                            cerrar_transaccion($conexion, false);
                            lanzar_error("Error de servidor (" . __LINE__ . ")");
                        }
                    }
                    //Guardamos los cambios.
                    cerrar_transaccion($conexion, true);
                } else {
                    cerrar_transaccion($conexion, false);
                    lanzar_error("Error de servidor (" . __LINE__ . ")");
                }
            } else {
                cerrar_transaccion($conexion, false);
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            break;
        case "mod":
            if(empty($_POST['id_r']) || empty($_POST['mb']) || !is_array($_POST['mb']) ||
                    empty($_POST['nm']) || !is_array($_POST['nm'])){
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            
            $query ="SELECT ros.id_equipo, 
                            ros.id_categoria 
                     FROM   (SELECT id_equipo, 
                                    id_categoria, 
                                    id_convocatoria 
                             FROM   rosters 
                             WHERE  id_roster = ?) AS ros 
                            LEFT JOIN convocatoria 
                                   ON ros.id_convocatoria = convocatoria.id_convocatoria 
                     WHERE  fecha_fin_torneo IS NULL 
                             OR curdate() < date_sub(fecha_cierre_convocatoria, interval 7 day)";
            if(($consulta = $conexion->prepare($query)) && $consulta->bind_param("i", $_POST['id_r']) && $consulta->execute()){
                $res = $consulta->get_result();
                if ($res->num_rows != 0){
                    $tmp = $res->fetch_row();
                    validar_propiedad_equipo_coach($conexion, $tmp[0]);
                    validar_info_roster($conexion, $tmp[1], $_POST['mb'], $_POST['nm']);
                } else {
                    lanzar_error("El roster ya no puede ser editado. El límite era una semana antes del cierre de la convocatoria en la que participa.");
                }
            } else {
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            
            $mb_viejos = array(); //Miembros viejos
            $query = "SELECT ID_JUGADOR FROM participantes_rosters WHERE ID_ROSTER = ?";
            if(($consulta = $conexion->prepare($query)) && $consulta->bind_param("i", $_POST['id_r']) && $consulta->execute()){
                $res = $consulta->get_result();
                while ($fila = $res->fetch_row()){
                    array_push($mb_viejos, $fila[0]);
                }
            }
            
            iniciar_transaccion($conexion);
            
            //Eliminamos a los jugadores que abandonan el roster.
            $query = "DELETE FROM participantes_rosters WHERE ID_ROSTER = ? AND ID_JUGADOR = ?";
            if($consulta = $conexion->prepare($query)){
                foreach (array_diff($mb_viejos, $_POST['mb']) as $jugador) {
                    if (!($consulta->bind_param("ii", $_POST['id_r'], $jugador) && $consulta->execute())) {
                        cerrar_transaccion($conexion, false);
                        lanzar_error("Error de servidor (" . __LINE__ . ")");
                    }
                }
            } else {
                cerrar_transaccion($conexion, false);
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            
            //Actualizamos los números de los jugadores que se mantienen en el roster
            $query = "UPDATE participantes_rosters SET NUMERO = ? WHERE ID_ROSTER = ? AND ID_JUGADOR = ?";
            if($consulta = $conexion->prepare($query)){
                foreach (array_intersect($_POST['mb'], $mb_viejos) as $jugador) {
                    if (!($consulta->bind_param("iii", $_POST['nm'][array_search($jugador, $_POST['mb'])], $_POST['id_r'], $jugador) && $consulta->execute())) {
                        cerrar_transaccion($conexion, false);
                        lanzar_error("Error de servidor (" . __LINE__ . ")");
                    }
                }
            } else {
                cerrar_transaccion($conexion, false);
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            
            //Agregamos a los nuevos jugadores
            $query = "INSERT INTO participantes_rosters (ID_ROSTER, ID_JUGADOR, NUMERO) VALUES (?, ?, ?)";
            if($consulta = $conexion->prepare($query)){
                foreach (array_diff($_POST['mb'], $mb_viejos) as $jugador) {
                    if (!($consulta->bind_param("iii", $_POST['id_r'], $jugador, $_POST['nm'][array_search($jugador, $_POST['mb'])]) && $consulta->execute())) {
                        cerrar_transaccion($conexion, false);
                        lanzar_error("Error de servidor (" . __LINE__ . ")");
                    }
                }
            } else {
                cerrar_transaccion($conexion, false);
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            
            cerrar_transaccion($conexion, true);
            break;
        case "eli":
            if(empty($_POST['id']) || !is_numeric($_POST['id'])){
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            
            $query ="SELECT ros.id_equipo 
                     FROM   (SELECT id_equipo, 
                                    id_convocatoria 
                             FROM   rosters 
                             WHERE  id_roster = ?) AS ros 
                            LEFT JOIN convocatoria 
                                   ON ros.id_convocatoria = convocatoria.id_convocatoria 
                     WHERE  fecha_fin_torneo IS NULL 
                             OR curdate() < fecha_fin_torneo";
            if(($consulta = $conexion->prepare($query)) && $consulta->bind_param("i", $_POST['id']) && $consulta->execute()){
                $res = $consulta->get_result();
                if ($res->num_rows != 0){
                    $fila = $res->fetch_row();
                    validar_propiedad_equipo_coach($conexion, $fila[0]);
                } else {
                    lanzar_error("El torneo en el que este roster participa ya se terminó, y por lo tanto, no puede ser eliminado.");
                }
            } else {
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            
            unnset($query);
            $querys = array(
                    "DELETE FROM participantes_no_registrados WHERE ID_ROSTER = ?",
                    "DELETE FROM participantes_rosters WHERE ID_ROSTER = ?",
                    "DELETE FROM cedulas WHERE ID_ROSTER = ?",
                    "DELETE FROM rosters WHERE ID_ROSTER = ?");
            iniciar_transaccion($conexion);
            
            foreach ($querys as $query) {
                if(!($consulta = $conexion->prepare($query)) || !$consulta->bind_param("i", $_POST['id']) || !$consulta->execute()){
                    cerrar_transaccion($conexion, false);
                    lanzar_error("Error de servidor (" . __LINE__ . ")");
                }
            }
            
            cerrar_transaccion($conexion, true);
            break;
        default:
            lanzar_error("Error de servidor (" . __LINE__ . ")", false);
    }
?>