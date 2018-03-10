<?php
    /**
     * Este PHP contiene todas las funciones relacionadas con la gestión de los equipos.
     * Cuando lo mande a llamar, incluya el parámetro "fn", cuyo valor sea el nombre de la función a ejecutar.
     */

    if(empty($_POST['fn'])){
        $_POST['fn'] = " ";
    }
    
    include("SRV_CONEXION.php");
    include("SRV_FUNCIONES.php");
    
    $conexion = (new SRV_CONEXION())->getConnection() or lanzar_error("Error de servidor (" . __LINE__ . ")");
    validar_sesion_y_expulsar(["ADMINISTRADOR", "COACH"]);
    
    /**
     * Revisa si el nombre del equipo es válido.
     * En caso de que no lo sea, el flujo del código se detiene y se lanza el error 500.
     * 
     * @param mysqli $mysqli Conexión a la base de datos.
     * @param string $key_nombre Llave del nombre a evaluar, en el arreglo $_POST.
     */
    function validar_nombre_equipo($mysqli, $key_nombre){
        $_POST[$key_nombre] = preparar_oracion($_POST[$key_nombre]);
        if(!preg_match("/^[a-zA-Z0-9áéíóúÁÉÍÓÚüÜñÑ '-]{2,}$/", $_POST[$key_nombre])){
            lanzar_error("Nombre inválido.");
        }

        //Comprobamos que no exista otro equipo con el mismo nombre.
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
    
    // Se ejecuta una función de acuerdo al parámetro 'fn'.
    switch ($_POST['fn']){
        case "num":
            /**
             * Permite obtener el número de equipo registrados.
             * - Si el usuario logueado es administrador, se cuentan todos los equipos.
             * - Si el usuario logueado es coach, se cuentan sólo los equipo que dirige.
             * 
             * Si desea filtrar los equipos por sus nombres (hacer una búsqueda), agregue el parámetro
             * "sr", con las palabras o sílabas que se usarán para hacer el filtrado (3 como máximo).
             * Por ejemplo: {fn : "num", sr : "toros"}
             */
            //Se inicializa el query y los parámteros del mismo.
            $query = "SELECT COUNT(*) FROM equipos";
            $parametros_query = array("");
            
            if ($_SESSION["TIPO_USUARIO"] == "COACH") {
                //Si es coach, filtramos los equipos que dirige.
                $query .= " WHERE ID_COACH = ?";
                $parametros_query[0] .= "i";
                array_push($parametros_query, $_SESSION["ID_USUARIO"]);
            }
            
            if(!empty($_POST['sr'])){
                //Si se añadieron términos de búsqueda, los agregamos al query.
                
                //$criterios es una lista con las sílabas y/o palabras a buscar.
                $criterios = array_filter(explode(" ", eliminar_espacios(str_replace("%", "", $_POST['sr']))));
                if(count($criterios) > 3){
                    lanzar_error("Error: Sólo puede escribir 3 palabras/sílabas como máximo, para filtrar el nombre.");
                }
                
                foreach($criterios as $aux){
                    //Cada palabra o sílaba se agrega al query.
                    if (!empty($parametros_query[0])){ $query .= " AND";
                    } else { $query .= " WHERE"; }
                    
                    $query .= " NOMBRE_EQUIPO LIKE ?";
                    $parametros_query[0] .= "s";
                    $aux = "%" . $aux . "%";
                    array_push($parametros_query, $aux);
                }
                
                //Eliminamos variables que ya no serán usadas.
                unset($criterios, $aux);
            }
            
            //Ejecutamos el query que cuenta cuántos equipos hay, según los filtros que hayamos puesto y respondemos con el número.
            if(($consulta = $conexion->prepare($query)) && (!empty($parametros_query[0]) ? $consulta->bind_param(...$parametros_query) : true) && $consulta->execute()){
                $res = $consulta->get_result();
                $fila = $res->fetch_row();
                echo $fila[0];
            } else {
                lanzar_error("Error de servidor (" . __LINE__ . ")", false);
            }
            break;
        case "get":
            /**
             * Permite obtener la información de un equipo.
             * Parámetro obligatorio: "id", el ID del equipo del que se desea obtener la información.
             * 
             * Parámteros opcionales (indican qué datos en específico desea obtener, si deseea obtener uno, mándelo en la petición y dele un valor > 0):
             * - "nb_e": El nombre del equipo.
             * - "id_c": El ID de la cuenta del coach que dirige el equipo.
             * - "nb_c": El nombre del coach que dirige el equipo.
             * - "lg": El logotipo del equipo, en formato PNG y encriptado en base_64.
             * - "r_act": Un arreglo bidimensional con los roster activos* que tiene el equipo cuyas columnas son ID del roster y nombre de categoría.
             * - "cat_d": Un arreglo bidimensional con las categorías de los rosters que se pueden crear en el equipo actualmente, sus columnas son ID y nombre de la cat..
             * 
             * * Un roster activo es aquel que no está inscrito en un torneo, o que está inscrito en un torneo que aún no ha terminado.
             * 
             * Ejemplo de salida, suponiendo que se solicitan todos los datos:
                {  
                   "id_c" : 213,
                   "nb_e" : "Longhorns",
                   "nb_c" : "DAMIAN CRUZ DARIO ",
                   "lg": "*Logo en base_64*",
                   "r_act":[  
                      [  8, "Mixto"  ],
                      [  10, "Femenil"  ],
                      [  12, "Liebres"  ]
                   ],
                   "cat_d":[  
                      [  1, "Varonil"   ],
                      [  4, "Más de 40"  ],
                      [  5, "Heavy weight"  ],
                      [  6, "Rabbit"  ]
                   ]
                }
             */
            //Nos aseguramos de que haya mandado el parámetro obligatorio en la petición.
            if(!isset($_POST['id'])){
                lanzar_error("Error de servidor (" . __LINE__ . ")", false);
            }
            
            //Si el usuario es coach, debemos comprobar que el ID del equipo corresponda a un equipo que realmente le pertenece.
            validar_propiedad_equipo_coach($conexion, $_POST['id']);
            
            //Arreglo con claves que será el resultado de esta función.
            $info_equipo = array();
            
            //Se arma el query para obtener los datos principales del equipo, según lo señalado en los parámetros.
            $datosASeleccionar = "";
            if(!empty($_POST['id_c'])){ $datosASeleccionar .= "ID_COACH"; }
            if(!empty($_POST['nb_e'])){ $datosASeleccionar .= (empty($datosASeleccionar) ? "" : ", ") . "NOMBRE_EQUIPO"; }
            if(!empty($_POST['nb_c'])){ $datosASeleccionar .= (empty($datosASeleccionar) ? "" : ", ") . "APELLIDO_PATERNO, APELLIDO_MATERNO, NOMBRE"; }
            if(!empty($_POST['lg'])){ $datosASeleccionar .= (empty($datosASeleccionar) ? "" : ", ") . "LOGOTIPO_EQUIPO"; }
            $query = "SELECT " . $datosASeleccionar . " FROM equipos INNER JOIN usuarios ON equipos.ID_COACH = usuarios.ID_USUARIO WHERE ID_EQUIPO = ?";
            
            //Ejecutamos el query recién creado y metemos los datos que haya regresado en el arreglo resultante de esta función.
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
                //Si el usuario desea los datos asociados a "r_act" y/o "cat_d", preparamos el query que se va usar para recuperar ambos datos.
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
            
            if(!empty($_POST['r_act'])){
                //Si se solicitan los rosters activos, se ejecuta el query recién creado y se meten sus resultados al arreglo resultante ($info_equipo).
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
            
            if(!empty($_POST['cat_d'])){
                //Si se solicitan las categorías disponibles...
                
                //Lista de todas las categorías.
                $categorias = array();
                //Lista de las categorías que el equipo ya ocupó (teniendo rosters activos con dichas categorías).
                $categorias_ocupadas = array();
                
                //Obtenemos la lista de rosters activos, y de ella extraemos, las categorías de esos rosters (que vienen a ser, categorías ocupadas).
                if(($consulta = $conexion->prepare($query)) && $consulta->bind_param("i", $_POST['id']) && $consulta->execute()){
                    $res = $consulta->get_result();
                    while ($fila = $res->fetch_row()) {
                        //Llenamos la lista de categorías ocupadas.
                        array_push($categorias_ocupadas, $fila[1]);
                    }
                } else {
                    lanzar_error("Error de servidor (" . __LINE__ . ")");
                }
                
                //Obtenemos la lista de todas las categorías (con nombres y ID's).
                if(($consulta = $conexion->prepare("SELECT ID_CATEGORIA, NOMBRE_CATEGORIA FROM categorias")) && $consulta->execute()){
                    $res = $consulta->get_result();
                    while ($fila = $res->fetch_row()) {
                        array_push($categorias, $fila);
                    }
                } else {
                    lanzar_error("Error de servidor (" . __LINE__ . ")");
                }
                
                /* Obtenemos la lista de categorías disponibles (con ID's y nombres) y la agregamos al arreglo resultante ($info_equipo).
                   Para hacer esto, vemos qué categorías no están en la lista de categorías ocupadas. */
                $info_equipo["cat_d"] = array();
                foreach ($categorias as $cat){
                    if(!in_array($cat[1], $categorias_ocupadas)){
                        array_push($info_equipo["cat_d"], $cat);
                    }
                }
            }
            
            //Devolvemos el arreglo resultante.
            echo json_encode($info_equipo);
            break;
        case "bus":
            /**
             * Permite buscar entre los equipos y obtener la información básica de los resultados.
             * Si el usuario logueado es coach, se busca sólo entre los equipo que dirige.
             * 
             * Parámetros:
             * - "sr" (opcional): Mándelo si desea filtrar los equipos por sus nombres; contiene
             *      las palabras y/o sílabas que se usarán para hacer el filtrado (3 como máximo).
             * - "pageNumber" y "pageSize" (Obligatorios): Parámetros puestos automáticamente por el plugin "pagination.js".
             *      Indican el número y tamaño de la página (ya que una petición devuelve una página en específico).
             * 
             * Devuelve:
             * Un arreglo bidimensional donde cada fila pertenece a un resultado de la búsqueda y las columnas son: ID, nombre y logotipo.
             */
            //Inicializamos el query y sus parámetros.
            $query = "SELECT ID_EQUIPO, NOMBRE_EQUIPO, LOGOTIPO_EQUIPO FROM equipos";
            $parametros_query = array("");
            //Esta variable indica si el query tiene la palabra reservada "WHERE".
            $el_query_tiene_where = false;
            
            if ($_SESSION["TIPO_USUARIO"] == "COACH") {
                //Si el usuario logueado es coach, indicamos en el query que sólo se van a buscar entre los equipos que dirige.
                $query .= " WHERE ID_COACH = ?";
                $parametros_query[0] .= "i";
                array_push($parametros_query, $_SESSION["ID_USUARIO"]);
                $el_query_tiene_where = true;
            }
            
            if(!empty($_POST['sr'])){
                //Si se mandaron palabras o sílabas para filtrar los equipos, los agregamos al query.
                
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
                    
                    //Se agrega cada palabra/sílaba para buscar, en el query.
                    $query .= " NOMBRE_EQUIPO LIKE ?";
                    $parametros_query[0] .= "s";
                    $aux = "%" . $aux . "%";
                    array_push($parametros_query, $aux);
                }
                
                unset($criterios, $aux);
            }
            
            if(!empty($_POST['pageNumber']) && !empty($_POST['pageSize'])){
                //Agregamos la información para obtener sólo una página de la búsqueda.
                $_POST['pageNumber'] = intval($_POST['pageNumber']);
                $_POST['pageSize'] = intval($_POST['pageSize']);
                
                $query .= " LIMIT ?,?";
                
                $parametros_query[0] .= "ii";
                array_push($parametros_query, (($_POST['pageNumber'] - 1) * $_POST['pageSize']), $_POST['pageSize']);
            }
            
            //Se ejecuta el query.
            if(($consulta = $conexion->prepare($query)) && $consulta->bind_param(...$parametros_query) && $consulta->execute()){
                $res = $consulta->get_result();
                $equipos = array();
                
                while ($fila = $res->fetch_row()){
                    //Recolectamos cada resultado.
                    $fila[2] = base64_encode($fila[2]);
                    array_push($equipos, $fila);
                }
                
                //Devolvemos el arreglo resultante con los resultados de esta página.
                echo json_encode($equipos);
            } else {
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            break;
        case "crear":
            /**
             * Permite crear un equipo.
             * 
             * Parámetros (todos obligatorios):
             * - "id": El ID de la cuenta del coach que va a dirigir el equipo.
             * - "nb": El nombre del equipo.
             * - "lg": El logotipo del equipo, una archivo en formato común para imágenes (como PNG y JPEG).
             */
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
            
            //Validamos que el nombre sea válido.
            validar_nombre_equipo($conexion, 'nb');
            //Se transforma el logotipo para que pueda ser guardado en la base de datos.
            $logotipo = leer_imagen("lg", 150) or die();
            //Inicializamos el query que insertará el equipo.
            $query = "INSERT INTO equipos (ID_EQUIPO, ID_COACH, NOMBRE_EQUIPO, LOGOTIPO_EQUIPO) VALUES (0, ?, ?, '" . $logotipo . "')";
            //Ejecutamos el query y con ello damos por finalizada la insercción.
            if (!(($consulta = $conexion->prepare($query)) && $consulta->bind_param("is", $_POST['id'], $_POST['nb']) && $consulta->execute())){
                lanzar_error("Error de servidor (" . __LINE__ . ")", false);
            }
            break;
        case "mod":
            /**
             * Permite modificar la información de un equipo.
             * 
             * Parámetro obligatorio:
             * - "id_e": El ID del equipo cuyos datos se van a modificar.
             * 
             * Parámetros (son opcionales, pero al menos uno debe ser mandado):
             * - "id": El ID de la cuenta del coach que va a dirigir el equipo a partir de ahora.
             * - "nb": El nuevo nombre del equipo.
             * - "lg": El nuevo logotipo del equipo.
             */
            //Comprobamos que se hayan mandado los parámetros suficientes.
            if(empty($_POST['id_e']) || (empty($_POST['nb']) && !se_subio_archivo("lg") && empty($_POST['id_c']))){
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            
            //Si el usuario es coach, debemos comprobar que el id corresponda a un equipo que realmente le pertenece.
            validar_propiedad_equipo_coach($conexion, $_POST['id_e']);
            
            //Inicializamos el query de actualización y sus parámetros.
            $query = "UPDATE equipos SET";
            $parametros_query = array("");
            
            if(!empty($_POST['nb'])){
                //Si se mandó un nuevo nombre de equipo, lo validamos y agregamos al query.
                validar_nombre_equipo($conexion, 'nb');
                $query .= " NOMBRE_EQUIPO = ?";
                $parametros_query[0] .= "s";
                array_push($parametros_query, $_POST['nb']);
            }
            if(se_subio_archivo("lg")){
                //Si se mandó un nuevo logotipo, lo transformamos a una forma que entienda la base de datos y lo agregamos al query.
                $logotipo = leer_imagen("lg", 150) or die();
                $query .= (!empty($parametros_query[0]) ? "," : "") . " LOGOTIPO_EQUIPO = '" . $logotipo . "'";
            }
            if(!empty($_POST['id_c'])){
                //Si se manda el ID de un nuevo dirigente del equipo, comprobamos que sea coach y lo agregamos al query.
                if(!usuario_cumple_condicion($conexion, $_POST['id_c'], "AND TIPO_USUARIO = 'COACH'")){
                    //El nuevo dueño de este equipo no es un coach.
                    lanzar_error("Error de servidor (" . __LINE__ . ")");
                }
                
                $query .= (!empty($parametros_query[0]) ? "," : "") . " ID_COACH = ?";
                $parametros_query[0] .= "i";
                array_push($parametros_query, $_POST['id_c']);
            }
            
            //Terminamos de armar el query.
            $query .= " WHERE ID_EQUIPO = ?";
            $parametros_query[0] .= "i";
            array_push($parametros_query, $_POST['id_e']);
            
            //Ejecutamos el query y damos por finalizada la modificación.
            if(!(($consulta = $conexion->prepare($query)) && $consulta->bind_param(...$parametros_query) && $consulta->execute())){
                lanzar_error("Error de servidor (" . __LINE__ . ")");
            }
            break;
        default:
            lanzar_error("Error de servidor (" . __LINE__ . ")", false);
    }
    
    $conexion->close();
?>