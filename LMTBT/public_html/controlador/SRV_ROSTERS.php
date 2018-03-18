<?php
    /**
     * Este PHP contiene todas las funciones relacionadas con la gestión de los rosters.
     * Cuando lo mande a llamar, incluya el parámetro "fn", cuyo valor sea el nombre de la función a ejecutar.
     */
     
    if(empty($_POST['fn'])){
        $_POST['fn'] = " ";
    }
    
    include("SRV_CONEXION.php");
    include("SRV_FUNCIONES.php");
    
    /**
     * Valida que un roster sea válido.
     * No devuelve nada, pero si se encuentra un error, el flujo del código se detiene y se lanza el error 500.
     * 
     * Los arreglos $miembros y $numeros deben de tener el mismo tamaño.
     * 
     * @param mysqli $mysqli Conexión con la base de datos.
     * @param int $id_categoria El ID de la categoría del roster.
     * @param array $miembros Un arreglo unidimensional con los ID's de las cuentas de los jugadores que participan en el roster.
     * @param array $numeros Un arreglo unidimensional con los números que cada jugador tiene en el roster.
     */
    function validar_info_roster($mysqli, $id_categoria, $miembros, $numeros){
        //La cantidad de jugadores miembros.
        $mb_length = count($miembros);
        //La cantidad de elementos en el arreglo $numeros (que debería ser la misma que la del arreglo $miembros).
        $nm_length = count($numeros);
        
        //Se hacen las validaciones básicas.
        if($mb_length != $nm_length){
            lanzar_error("Inconsistencia en los datos.");
        } else if ($mb_length != count(array_unique($miembros))){
            lanzar_error("Hay jugadores repetidos.");
        } else if ($nm_length != count(array_unique($numeros))){
            lanzar_error("Hay números de jugadores repetidos.");
        } else if ($mb_length < 5){
            lanzar_error("Hay muy pocos jugadores.");
        } else if ($mb_length > 30){
            lanzar_error("Hay demasiados jugadores (más de 30).");
        } else {
            //Como no salió ninguno de los errores de arriba, nos deshacemos de las variables que ya no usaremos más.
            unset($mb_length, $nm_length);
        }
        
        //Nos aseguramos que todos los miembros del roster apliquen a la categoría del roster. Por ejemplo, una mujer no puede estar en un roster varonil.
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
        
        //Validamos que los números de los miembros sean numéricos (no cadenas, ni booleanos) y que estén entre 1 y 99.
        foreach ($numeros as $num){
            if(!is_numeric($num) || $num < 0 || $num > 99){
                lanzar_error("Uno de los números de jugador es inválido ($num).");
            }
        }
        
        //Si se llega a este punto, quiere decir que el roster es válido.
    }
    
    $conexion = (new SRV_CONEXION())->getConnection() or lanzar_error("Error de servidor (" . __LINE__ . ")");
    validar_sesion_y_expulsar(["ADMINISTRADOR", "COACH"]);
    
    // Se ejecuta una función de acuerdo al parámetro 'fn'.
    switch ($_POST['fn']){
        case "get":
            /**
             * Permite obtner la información de un roster en específico.
             * Recibe como único parámetro (obligatorio): "id", que es el ID del roster del que se desea obtener la información.
             * 
             * Devuelve un arreglo con claves, las cuales son:
             * - "id_cat": El ID de la categoría del roster.
             * - "eq": El nombre del equipo al que el roster pertenece.
             * - "cat": El nombre de la categoria del roster (varonil, femenil, etc.).
             * - "tor": El nombre del torneo en el que el roster participa, si es que está inscrito (si no, es nulo).
             * - "es_ed": Nos dice si un roster puede ser editado y en qué circunstancias. Puede valer true, false, null y string (de una fecha en formato AAAA-MM-DD).
             *            Para saber cómo interpretarlo, lea los comentarios del switch donde se le asigna un valor.
             * - "mb": Un subarreglo unidimensional que contiene los ID's de las cuentas de los jugadores que participan en el roster.
             * - "nm": Un subarreglo unidimensional que contiene los números que los jugadores tienen en el roster. Es del mismo tamaño que "mb". 
             */
            //Se valida que el parámetro obligatorio haya sido enviado.
            if(empty($_POST['id']) || !is_numeric($_POST['id'])){
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            
            //Se declara el arreglo resultante de la función.
            $info_roster = array();
            
            $query = "SELECT ids.id_equipo, 
                             ids.id_categoria, 
                             nombre_equipo, 
                             nombre_categoria, 
                             nombre_torneo, 
                             (fecha_lim_edicion IS NOT NULL AND curdate() > fecha_lim_edicion),
                             case  
                                when (fecha_fin_torneo IS NULL OR curdate() < date_sub(fecha_cierre_convocatoria, interval 7 day)) then '1' 
                                when (fecha_lim_edicion IS NOT NULL AND fecha_lim_edicion <= fecha_fin_torneo AND curdate() <= fecha_lim_edicion) then fecha_lim_edicion
                                when (curdate() <= fecha_fin_torneo) then '2'
                                else '3'
                             end
                     FROM   (SELECT id_equipo, 
                                    id_categoria, 
                                    id_convocatoria, 
                                    fecha_lim_edicion
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
                    
                    //Si el usuario que ejecutó esta función es un coach, se valida que el equipo al que le quiere crear un roster sea suyo.
                    validar_propiedad_equipo_coach($conexion, $info_basica[0]);
                    
                    //Se guardan los primeros datos en el arreglo resultante.
                    $info_roster['id_cat'] = $info_basica[1];
                    $info_roster['eq'] = $info_basica[2];
                    $info_roster['cat'] = $info_basica[3];
                    $info_roster['tor'] = $info_basica[4];
                    
                    //Eliminamos la fecha límite para ediciones especiales, si ya expiró.
                    if(boolval($info_basica[5])){
                        $query = "UPDATE rosters SET fecha_lim_edicion = NULL WHERE id_roster = ?";
                        if(!(($consulta = $conexion->prepare($query)) && $consulta->bind_param("i", $_POST['id']) && $consulta->execute())){
                            lanzar_error("Error de servidor (" . __LINE__ . ")");
                        }
                    }
                    
                    //Analizamos si el roster puede ser editado.
                    switch ($info_basica[6]){
                        case "1":
                            /* Puede ser editado, porque no está inscrito en un torneo o porque no ha pasado el límite para poder editar
                               (una semana antes del inicio del torneo).*/
                            $info_roster['es_ed'] = true;
                            break;
                        case "2":
                            // No puede ser editado, pero como el torneo sigue activo, el administrador puede dar un permiso especial para poder hacerlo.
                            $info_roster['es_ed'] = false;
                            break;
                        case "3":
                            // No puede ser editado, porque el torneo en el que participa ya ha terminado.
                            $info_roster['es_ed'] = NULL;
                            break;
                        default:
                            /* Se puede editar (en condiciones normales no se podría) porque el administrador ha dado un permiso especial
                               (hasta cierta fecha). En este caso, mandamos la fecha límite. */
                            $info_roster['es_ed'] = $info_basica[6];
                    }
                } else {
                    lanzar_error("El roster ya no existe.");
                }
            } else {
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            
            //Consutamos los jugadores que participan en el roster, sus números y los guardamos en el arreglo resultante.
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
            
            //Devolemos el arreglo con toda la información del roster.
            echo json_encode($info_roster);
            break;
        case "crear":
            /**
             * Permite inscribir un roster; el cual, pertenece a un equipo y está dentro de una categoría (varonil, femenil, etc.).
             * 
             * Parámetros (todos obligatorios):
             * - "id_e": El ID del equipo al que el este nuevo roster va a pertenecer.
             * - "id_ct": El ID de la categoría del roster.
             * - "mb": Un arreglo unidimensional con los ID's de las cuentas de los jugadores que participarán en el nuevo roster.
             * - "nm": Un arreglo del mismo tamaño que "mb", el cual indica el número de cada jugador que participa en el roster.
             */
            
            //Comprobamos que todos los parámetros hayan sido enviados en la petición.
            if(empty($_POST['id_e']) || empty($_POST['id_ct']) || empty($_POST['mb']) || !is_array($_POST['mb']) ||
                    empty($_POST['nm']) || !is_array($_POST['nm'])){
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            
            //Nos aseguramos de que el coach (si es que uno está ejecutando esta función) no esté intentando crearle un roster a un equipo que no dirige.
            validar_propiedad_equipo_coach($conexion, $_POST['id_e']);
            //Validamos toda la información del nuevo roster.
            validar_info_roster($conexion, $_POST['id_ct'], $_POST['mb'], $_POST['nm']);
            
            iniciar_transaccion($conexion);
            
            /* El roster se guarda en 2 etapas:
             * 1.- Guardando toda la información del rosters (salvo los miembros y sus números) en una tabla de la BD.
             * 2.- Guardando los miembros del roster y sus números en otra tabla.
             */
            $query = "INSERT INTO rosters (ID_ROSTER, ID_CONVOCATORIA, ID_EQUIPO, ID_CATEGORIA) VALUES (0, null, ?, ?)";
            if(($consulta = $conexion->prepare($query)) && $consulta->bind_param("ii", $_POST['id_e'], $_POST['id_ct']) && $consulta->execute()){
                //Obtenemos el ID del roster recién creado.
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
            /**
             * Permite modificar los participantes de un roster y sus números.
             * 
             * Parámetros (todos obligatorios):
             * - "id_r": El ID del roster a modificar.
             * - "mb": Un arreglo unidimensional con los ID de las cuentas de jugador que participan en el roster.
             * - "nm": Un arreglo del mismo tamaño que "mb" con los números de cada jugador participante.
             * 
             * "mb" y "nm" deben tener cambios respecto a la versión actual del roster: cambios en los números, nuevos jugadores, jugadores eliminados, etc.
             */
            if(empty($_POST['id_r']) || empty($_POST['mb']) || !is_array($_POST['mb']) ||
                    empty($_POST['nm']) || !is_array($_POST['nm'])){
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            
            //Con este query, se comprueba si el roster puede ser editado y obtenemos un par de datos del mismo.
            $query ="SELECT ros.id_equipo, 
                            ros.id_categoria 
                     FROM   (SELECT id_equipo, 
                                    id_categoria, 
                                    id_convocatoria, 
                                    fecha_lim_edicion
                             FROM   rosters 
                             WHERE  id_roster = ?) AS ros 
                            LEFT JOIN convocatoria 
                                   ON ros.id_convocatoria = convocatoria.id_convocatoria 
                     WHERE  fecha_fin_torneo IS NULL";
            if($_SESSION["TIPO_USUARIO"] == "ADMINISTRADOR"){
                $query .= "  OR curdate() <= fecha_fin_torneo";
            } else {
                $query .= "  OR curdate() < date_sub(fecha_cierre_convocatoria, interval 7 day)
                             OR (fecha_lim_edicion IS NOT NULL AND fecha_lim_edicion <= fecha_fin_torneo AND curdate() <= fecha_lim_edicion)";
            }
            
            if(($consulta = $conexion->prepare($query)) && $consulta->bind_param("i", $_POST['id_r']) && $consulta->execute()){
                $res = $consulta->get_result();
                if ($res->num_rows != 0){
                    //El equipo puede ser editado.
                    
                    //$tmp almacena los datos que obtuvimos del query y nos permite hacer las dos siguientes validaciones.
                    $tmp = $res->fetch_row();
                    
                    validar_propiedad_equipo_coach($conexion, $tmp[0]);
                    validar_info_roster($conexion, $tmp[1], $_POST['mb'], $_POST['nm']);
                } else {
                    lanzar_error("El roster ya no puede ser editado.");
                }
            } else {
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            
            //Obtenemos la lista de los ID's de los jugadores del roster actuales (sin haberle hecho cambios aún) y la almacenamos en $mb_viejos (miembros viejos).
            $mb_viejos = array();
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
            
            //Eliminamos a los jugadores cuyas cuentas hayan sido eliminadas.
            $query = "DELETE FROM participantes_rosters WHERE ID_ROSTER = ? AND ID_JUGADOR IS NULL";
            if(!(($consulta = $conexion->prepare($query)) && $consulta->bind_param("i", $_POST['id_r']) && $consulta->execute())){
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            
            //Actualizamos los números de los jugadores que se mantienen en el roster.
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
            
            //Agregamos a los nuevos jugadores al roster.
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
            
            //Guardamos los cambios efectuados.
            cerrar_transaccion($conexion, true);
            break;
        case "eli":
            /**
             * Permite eliminar un roster; siempre y cuando, no esté inscrito en un torneo, y si lo está, que el torneo aún esté vigente.
             * 
             * Parámetro obligatorio:
             * - "id": El ID del roster a eliminar.
             */
            //Nos aseguramos de que el parámetro haya sido enviado desde la petición.
            if(empty($_POST['id']) || !is_numeric($_POST['id'])){
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            
            //Este query nos permite saber si el roster puede ser eliminado.
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
                    //El query puede ser eliminado.
                    $fila = $res->fetch_row();
                    /* Validamos que, en caso de que el usuario que ejecute está función sea un coach, no esté trantando de eliminar
                       un roster de un equipo que no dirige. */
                    validar_propiedad_equipo_coach($conexion, $fila[0]);
                } else {
                    lanzar_error("El torneo en el que este roster participa ya se terminó, y por lo tanto, no puede ser eliminado.");
                }
            } else {
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            
            //Efectuamos la eliminación en la base de datos.
            $query = "DELETE FROM rosters WHERE ID_ROSTER = ?";
            if(!($consulta = $conexion->prepare($query)) || !$consulta->bind_param("i", $_POST['id']) || !$consulta->execute()){
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            break;
        case "per":
            /**
             * Nota: Esta función sólo puede ser ejecutada mientras el torneo donde este roster esté inscrito, esté activo.
             *       Y además, se haya pasado el límite para editar (una semana antes del inicio del torneo).
             * 
             * Añade, modifica o elimina el permiso especial para que el coach pueda modificar el roster fuera del tiempo normalmente permitido.
             * 
             * Parámetros:
             * - "id" (obligatorio): El ID del roster sobre el que se aplicará la función. 
             * - "tmp" (opcional): La cantidad de días límite (a partir del día en el que se ejecute la función) para que
             *                     el coach edite el roster de forma especial. Es un string, puede valer entre 1 y 7.
             *                     Nótese que el 1er día corresponde a 'hoy', por lo que, por ejemplo, un "2" significa
             *                     "hoy y mañana". Si lo manda, el permiso se agrega o modifica.
             *                     Si no lo manda, el permito actual (si es que existe) es revocado. "tmp" significa "tiempo".
             * 
             * En caso de que agregue un permiso, o lo modifique, se devolverá la fecha límite que el coach tiene para hacer los cambios.
             */
            validar_sesion_y_expulsar(["ADMINISTRADOR"]);
            
            if(!empty($_POST['tmp'])){
                //Obtenemos la cantidad de días que el permiso va a durar, contando el día actual.
                $_POST['tmp'] = intval($_POST['tmp']);
                
                if($_POST['tmp'] >= 1 && $_POST['tmp'] <= 7){
                    //Este query nos permite saber cuándo acaba el torneo en que el roster está inscrito.
                    $query =   "SELECT fecha_fin_torneo 
                                FROM   (SELECT id_convocatoria 
                                        FROM   rosters 
                                        WHERE  id_roster = ?) AS ros 
                                       LEFT JOIN convocatoria 
                                              ON ros.id_convocatoria = convocatoria.id_convocatoria";
                    
                    if(($consulta = $conexion->prepare($query)) && $consulta->bind_param("i", $_POST['id']) && $consulta->execute()){
                        $res = $consulta->get_result();
                        if ($res->num_rows != 0){
                            //Obtenemos la fecha en el que el torneo termina.
                            $fecha_fin_torneo = $res->fetch_row()[0];
                            
                            if(is_null($fecha_fin_torneo)){
                                lanzar_error("El roster no está inscrito en ningún torneo. Por lo que puede ser editado sin necesidad de dar permiso.");
                            }
                            
                            //date("Y-m-d") devuelve la fecha actual.
                            if(date("Y-m-d") > $fecha_fin_torneo){
                                lanzar_error("El torneo en el que este roster participa ya se terminó, por lo que no puede ser editado, ni siquiera con permiso.");
                            }
                            
                            //Convertimos el número de días límite en una fecha límite.
                            $_POST['tmp'] = date("Y-m-d", strtotime(date("Y-m-d") . " + " . --$_POST['tmp'] . " days"));
                            //Si la fecha límite es posterior al fin del torneo, la ajustamos.
                            if($_POST['tmp'] > $fecha_fin_torneo){
                                $_POST['tmp'] = $fecha_fin_torneo;
                            }
                            
                            //Mandamos la fecha límite a JavaScript, para que la muestre en pantalla.
                            echo $_POST['tmp'];
                        } else {
                            lanzar_error("El roster ya no existe.");
                        }
                    }
                } else {
                    //Se mandó un valor inválido en el parámetro "tmp".
                    lanzar_error("Error de servidor (" . __LINE__ . ")");
                }
            } else {
                $_POST['tmp'] = null;
            }
            
            //Se aplican los cambios en la base de datos.
            $query = "UPDATE rosters SET fecha_lim_edicion = ? WHERE id_roster = ?";
            if(!(($consulta = $conexion->prepare($query)) && $consulta->bind_param("si", $_POST['tmp'], $_POST['id']) && $consulta->execute())){
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            break;
        default:
            lanzar_error("Error de servidor (" . __LINE__ . ")", false);
    }
?>