<?php
    /**
     * Este PHP contiene todas las funciones relacionadas con la gestión de las cuentas.
     * Cuando lo mande a llamar, incluya el parámetro "fn", cuyo valor sea el nombre de la función a ejecutar.
     */
    
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
        
        //Primero capturamos las enfermedades y luego, las alergias.
        foreach($indicaciones as $aux){
            //Creamos un query para obtener los nombres o ID's de los padecimientos predefinidos.
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
            
            //Creamos un query para obtener los otros padecimientos.
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
    
    //Se ejecuta una función de acuerdo al parámetro 'fn'.
    switch ($_POST['fn']){
        case "get_info":
            /**
             * Permite obtener la información de una cuenta.
             * 
             * Parámetro principal:
             * - "id_c" (Opcional): El ID de la cuenta de la que se va a extraer la información. Si lo omite, se devuelve la información del usuario actual.
             *          También puede ser un arrego de ID's, para obtener la información de varias cuentas al mismo tiempo.
             * 
             * Parámetros que indican qué información en específo desea obtener (todos son opcionales):
             * - "id": El ID de la cuenta, útil cuando no manda el parámetro "id_c".
             * - "nb": El nombre, apellido paterno y apellido materno.
             * - "nb_c": El nombre completo.
             * - "tp": El tipo de cuenta ("COACH", "JUGADOR", etc.).
             * - "cr": Correo electrónico.
             * - "rd": (Redes sociales) Enlaces a los perfiles de Facebook, Twitter e Instagram.
             * - "nc": Fecha de nacimiento, en el formato "AAAA-MM-DD".
             * - "ed": Edad.
             * - "sx": Sexo ("M" para masculino y "F" para femenino). 
             * - "ot": (Otros datos) Tipo de sangre y número de teléfono.
             * - "ft": Foto de perfil (en formato PNG y encriptado en base_64).
             * Si desea obtener un dato de estos, en su petición envíe el respectivo parámetro con un valor mayor a 0.
             * Por ejemplo: {"ed" : 1}. Omita de su petición los datos de los que no está inreresado.
             * Si no envía ninguno de estos parámetros, se da por entendido que los desea consultar todos.
             * 
             * Parámetros válidos sólo cuando se pide la información de un único usuario:
             * - "all_pd": Todos los padecimientos predefinidos. Al pedirlo, el arreglo resultante tendrá dos subarreglos bidimensionales ("all_en" y "all_al").
             *      Donde en ambos, cada fila, representa un padecimiento distinto; y sus columnas numéricas son el ID y el nombre.
             * - "pd": Todos los padecimiento que sufre el usuario. Si vale 1, los padecimientos predefinidos se expresan en nombres; si vale 2, en ID's.
             *      El arreglo resultante tendrá dos subarreglos llamados "en" y "al" (Ver la función "getPadecimientos").
             * 
             * Ejemplo de petición que sólo pide el nombre completo y el tipo de cuenta: {fn : "get_info", nb : 1, nb_c : 1}
             * 
             * --------------------------------------------------
             * Retorna un arreglo de una o dos dimesiones (según "id_c"). Si es de dos, cada fila corresponde a un usuario diferente.
             * En el arreglo unidimensional o fila del arreglo bidimensional, se encuentran los valores asociados por claves.
             * 
             * En el caso de los parámetros que devuelven sólo un dato (como "nb_c" y "ed"), la clave es la misma que el nombre del parámetro:
             * {"nb" => "Carlos García Juan", "ed" => 18}
             * 
             * En el caso de los parámetros que devuelven varios datos ("nb", "rd" y "ot"), las claves tienen otros nombres. Ejemplo:
             * {"NOMBRE" => "Juan", "APELLIDO_PATERNO" => "Carlos", "APELLIDO_MATERNO" => "García", "FACEBOOK" => "www.facebook.com/juan",
             * "TWITTER" => "www.twitter.com/juan", "INSTAGRAM" => "www.instagram.com/juan", "TIPO_SANGRE" => "O+", "TELEFONO" => "9611001234"}
             */
            validar_sesion_y_expulsar();
            
            //Se ve si se desea obtener la información de una cuenta ajena a la del usuario actual.
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
            
            //Se arma la parte del query donde se especifican los datos a recuperar, según los parámetros.  
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
                
                //Si no se envía ninguno de estos parámetros, se entiende que todos se desean consultar.
                if(empty($datosASeleccionar)){
                    $_POST['nb'] = $_POST['nb_c'] = $_POST['tp'] = $_POST['cr'] = $_POST['rd'] = $_POST['nc'] = $_POST['ed'] = $_POST['sx'] = $_POST['ot'] = $_POST['ft'] = true;
                }
            } while (empty($datosASeleccionar));
            
            //Se crea la consulta, usando la parte que acabamos de crear.
            $query = 'SELECT ' . $datosASeleccionar . ' FROM usuarios WHERE ID_USUARIO = ?';
            
            if(!is_array($_POST['id_c'])){
                //Si sólo se pidió la información de una cuenta...
                
                if(($consulta = $conexion->prepare($query)) && $consulta->bind_param("i", $_POST['id_c']) && $consulta->execute()){
                    $res = $consulta->get_result();

                    if ($res->num_rows != 0) {
                        //Vamos a enviar la fila de la tabla como resultado.
                        $info_cuenta = $res->fetch_assoc();
                        //Si se incluyó la foto de perfil, la convertimos a un formato legible.
                        if (!empty($_POST['ft']) && boolval($_POST['ft']) && $info_cuenta["ft"] != null) {
                            $info_cuenta["ft"] = base64_encode($info_cuenta["ft"]);
                        }
                        
                        //Se devuelven la información de los padecimientos si la petición así lo indica.
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
                //Si se pidió la información de varias cuentas...
                $info_cuentas = array();
                //Se devuelve la información por cada cuenta.
                foreach ($_POST['id_c'] as $cuenta) {
                    if($consulta->bind_param("i", $cuenta) && $consulta->execute() && ($res = $consulta->get_result())){
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
            /**
             * Permite buscar entre las cuentas y obtener la información básica de los resultados.
             * 
             * Parámetros generales:
             * - "tipo" (obligatorio): Qué tipo de cuenta se va a filtrar.
             * - "pageNumber" y "pageSize" (opcionales): Parámetros puestos automáticamente por el plugin "pagination.js".
             *      Indican el número y tamaño de la página (ya que una petición devuelve una página en específico).
             * 
             * Parámetros de búsqueda generales (opcionales):
             * - "nb": Indica sílabas a palabras para filtrar según el nombre.
             * - "ap": Indica sílabas a palabras para filtrar según los apellidos.
             * 
             * Parámetros de búsqueda exclusivos al se filtran jugadores (opcionales):
             * - "sexo": Indica con que sexo filtrar ("M" o "F"). 
             * - "edad": Indica con que edad filtrar ("<18" (menor de 18), ">35 (mayor a 35)", "18-35" (entre 18 y 35), etc.).
             * - "id_cat": Indica con el ID de una categoría para filtrar a los jugadores. Por ejemplo: "buscar aquellos aptos para jugar en la categoría varonil".
             * 
             * Parámetros para indicar qué información extra se deses incluir de los jugadores encontrados (opcionales). Para pedir uno en específico, dele un valor != 0:
             * - "i_c": Correo electrónico.
             * - "i_s": Sexo o género
             * - "i_e": Edad
             * - "i_f": Foto de perfil (en formato PNG y encriptado en base_64).
             * 
             * ------------------------------------------------
             * Si "pageNumber" y "pageSize" se mandaron, se devuelve un arreglo bidimensional donde cada fila corresponde a un usuario que ha sido encontrado. Las columnas
             * correponden a los datos y son númericas (no son claves).
             * Los datos/columnas que siempre se mandan son ID, apellidos y nombre; el resto corresponden a los datos extras señalados en los parámetros.
             * 
             *  Si "pageNumber" y "pageSize" no se mandaron, se devuelve el número total de resultados que la búsqueda arroja.
             */
            
            validar_sesion_y_expulsar(["ADMINISTRADOR", "COACH"]);
            //Se inicializa el query que se va ir armando poco a poco.
            $query = "SELECT";
            //Este arreglo incluye todos los parámetros para el query preparado.
            $parametros_query = array("");
            
            if(!empty($_POST['pageNumber']) && !empty($_POST['pageSize'])){
                //Si los parámetros de página se mandan, se devuelven los datos de las cuentas encontradas en la búsqueda.
                
                //Agregamos los datos por defecto que devuelve la búsqueda.
                $query .= " ID_USUARIO, CONCAT(APELLIDO_PATERNO, ' ', APELLIDO_MATERNO), NOMBRE";
                
                //Agregamos los datos extras exclusivos para los jugadores, que se devuelven según los parámetros opcionales.
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
                //Si los parámetros de página se mandan, sólo se quiere saber cuántas cuentas arroja la búsqueda.
                $query .= " COUNT(*)";
            }
            
            //Revismos si se mandó el parámetro obligatorio "tipo".
            if(empty($_POST['tipo'])){
                lanzar_error("Error de servidor (" . __LINE__ . ")", false);
                break;
            }
            
            //Agregamos el filtro del tipo de cuenta en el query.
            $query .= " FROM usuarios WHERE TIPO_USUARIO = ?";
            $parametros_query[0] .= "s";
            array_push($parametros_query, $_POST['tipo']);
            
            //Agregamos el filtro en el nombre, si es que se mandó.
            if(!empty($_POST['nb'])){
                //$criterios es un arreglo que almacena cada sílaba o palabra con la que hará el filtrado.
                $criterios = array_filter(explode(" ", str_replace("%", "", $_POST['nb'])));
                
                if(count($criterios) > 4){
                    lanzar_error("Error: Sólo puede escribir 4 palabras/sílabas como máximo, para buscar en el nombre.");
                }
                
                foreach($criterios as $aux){
                    //Se agrega al query el filtro de cada palabra/sílaba.
                    $query .= " AND NOMBRE LIKE ?";
                    $parametros_query[0] .= "s";
                    $aux = "%" . $aux . "%";
                    array_push($parametros_query, $aux);
                }
                
                unset($criterios);
                unset($aux);
            }
            
            //Agregamos el filtro en los apellidos, si es que se mandó.
            if(!empty($_POST['ap'])){
                $criterios = array_filter(explode(" ", str_replace("%", "", $_POST['ap'])));
                if(count($criterios) > 4){
                    lanzar_error("Error: Sólo puede escribir 4 palabras/sílabas como máximo, para buscar en los apellidos.");
                }
                
                foreach($criterios as $aux){
                    $query .= " AND (APELLIDO_PATERNO LIKE ? OR APELLIDO_MATERNO LIKE ?)";
                    $parametros_query[0] .= "ss";
                    $aux = "%" . $aux . "%";
                    array_push($parametros_query, $aux, $aux);
                }
                
                unset($criterios);
                unset($aux);
            }
            
            if($_POST['tipo'] == "JUGADOR"){
                //Si se buscan jugadores, se agregan sus filtros exclusivos.
                
                //Agregamos el filtro en el sexo, si es que se mandó.
                if(!empty($_POST['sexo'])){
                    $query .= " AND SEXO = ?";
                    $parametros_query[0] .= "s";
                    array_push($parametros_query, $_POST['sexo']);
                }
                
                //Agregamos el filtro en la edad, si es que se mandó.
                if(!empty($_POST['edad'])){
                    $primer_char = substr($_POST['edad'],0,1);
                    if($primer_char === "<" || $primer_char === ">"){
                        $query .= " AND DATEDIFF(CURDATE(), FECHA_NACIMIENTO)/365 " . $primer_char . " ?";
                        $parametros_query[0] .= "i";
                        array_push($parametros_query, substr($_POST['edad'],1));
                    } else {
                        $criterios = array_filter(explode("-", $_POST['edad']));
                        if(count($criterios) == 2){
                            $query .= " AND DATEDIFF(CURDATE(), FECHA_NACIMIENTO)/365 >= ? AND DATEDIFF(CURDATE(), FECHA_NACIMIENTO)/365 <= ?";
                            $parametros_query[0] .= "ii";
                            array_push($parametros_query, $criterios[0], $criterios[1]);
                        } else {
                            lanzar_error("Error de servidor (" . __LINE__ . ")", false);
                            break;
                        }
                    }
                }
                
                //Agregamos el filtro en la categoría, si es que se mandó.
                if(!empty($_POST['id_cat'])){
                    $query .= " " . get_restricciones_categoria($conexion, $_POST['id_cat']);
                }
            }
            
            if(!empty($_POST['pageNumber']) && !empty($_POST['pageSize'])){
                //Si se mandaron los parámetros de página, se agregan al query.
                $_POST['pageNumber'] = intval($_POST['pageNumber']);
                $_POST['pageSize'] = intval($_POST['pageSize']);
                
                $query .= " LIMIT ?,?";
                $parametros_query[0] .= "ii";
                array_push($parametros_query, (($_POST['pageNumber'] - 1) * $_POST['pageSize']), $_POST['pageSize']);
            }
            
            //Se ejecuta el query.
            if($consulta = $conexion->prepare($query)){
                if($consulta->bind_param(...$parametros_query) && $consulta->execute()){
                    $res = $consulta->get_result();
                    
                    if (strpos($query, 'COUNT(*)') !== false){
                        //Si se buscó el número de resultados, se devuelve como un número.
                        $fila = $res->fetch_row();
                        echo $fila[0];
                    } else {
                        //Si se buscaron los datos de los jugadores encontrados, se devuelve como un arreglo bidimensional.
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
            /**
             * Permite modificar la información de una cuenta.
             * 
             * El parámetro principal es "id", que corresponde al ID de la cuenta que desea modificar.
             * Sólo es mandado cuando el administrador desea modificar los datos de otra cuenta.
             * En cualquier otro caso (que alguien quiera modificar su propia cuenta), este parámetro se omite.
             * 
             * Parámetros opcionales que indican los nuevos valores que tendrán los campos (si desea mantener uno, simplemente omita su respectivo parámetro):
             * - "ap_p", "ap_m" y "nb" para el apellido paterno, apellido materno y nombre, respectivamente.
             * - "cr" para el correo electrónico (no podrán coexistir 2 cuentas con el mismo correo).
             * - "ps" para la contraseña (debe tener cierto nivel de seguridad).
             * - "ft" para la foto de perfil (tiene que tener uno de los formatos más populares).
             * Parámetros exclusivos al modificar la cuenta de un jugador (de las mismas características que los anteriores):
             * - "nc" para la fecha de nacimiento (formato "AAAA-MM-DD").
             * - "tel" para el número de teléfono (que debe ser de 10 dígitos).
             * - "sg" para el tipo de sangre.
             * - "fb" para el link al perfil de Facebook.
             * - "tw" para el link al perfil de Twitter.
             * - "ig" para el link al perfil de Instagram.
             * - "en" y "al" para las enfermedades y alergias predefinidas. Ambos son arreglos de ID's.
             * - "ot_al" y "ot_en" Para las otras enfermedades y alergias. Son cadenas donde cada padecimiento está separado por comas, puntos y/o puntos y comas.
             * 
             * Si todas la modificiación se efectúa correctamente no se devuelve nada.
             */
            validar_sesion_y_expulsar();
            
            if (empty($_POST['id'])) {
                //Si no se mandó el parámetro "id", se va a modificar la cuenta del usuario que esté logueado.
                $_POST['id'] = $_SESSION["ID_USUARIO"];
            } else {
                //Si se quiere modificar otra cuenta, nos sersioramos que el usuario logueado sea administrador.
                if ($_SESSION["TIPO_USUARIO"] != "ADMINISTRADOR") {
                    lanzar_error("Usted no tiene permiso de editar cuentas ajenas.");
                }
            }
            
            //Estos parámetros del query corresponden a los parámetros de este método: http://php.net/manual/es/mysqli-stmt.bind-param.php
            $parametros_query = array("");
	    //Esta cadena almacena las columnas de la tabla de usuarios que van a ser modificadas.
            $updates = "";
            
            /* Se prepara el query para modificar los campos generales (que aplican a todo tipo de usuarios), según los parámetros enviados en la petición.
               También se validan. */
            
            if (!empty($_POST['ap_p'])) {
                $_POST['ap_p'] = preparar_nombre($_POST['ap_p']);
                if (preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ' -]{2,}$/", $_POST['ap_p'])){
                    $parametros_query[0] .= "s";
                    array_push($parametros_query, $_POST['ap_p']);
                    $updates .= "APELLIDO_PATERNO = ?";
                } else {
                    lanzar_error("El apellido paterno está mal escrito (sólo debe contener letras).");
                }
            }
            
            if (!empty($_POST['ap_m'])) {
                $_POST['ap_m'] = preparar_nombre($_POST['ap_m']);
                if (preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ' -]{2,}$/", $_POST['ap_m'])){
                    $parametros_query[0] .= "s";
                    array_push($parametros_query, $_POST['ap_m']);
                    $updates .= (empty($updates) ? "" : ", ") . "APELLIDO_MATERNO = ?";
                } else {
                    lanzar_error("El apellido materno está mal escrito (sólo debe contener letras).");
                }
            }
            
            if (!empty($_POST['nb'])) {
                $_POST['nb'] = preparar_nombre($_POST['nb']);
                if (preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ' -]{2,}$/", $_POST['nb'])){
                    $parametros_query[0] .= "s";
                    array_push($parametros_query, $_POST['nb']);
                    $updates .= (empty($updates) ? "" : ", ") . "NOMBRE = ?";
                } else {
                    lanzar_error("El nombre está mal escrito (sólo debe contener letras).");
                }
            }
            
            if (!empty($_POST['cr'])) {
                $_POST['cr'] = eliminar_espacios($_POST['cr']);
                if (preg_match("/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/", $_POST['cr'])){
                    $parametros_query[0] .= "s";
                    array_push($parametros_query, $_POST['cr']);
                    $updates .= (empty($updates) ? "" : ", ") . "CORREO = ?";
                } else {
                    lanzar_error("El correo electrónico es inválido.");
                }
            }
            
            if (!empty($_POST['ps'])) {
                if (preg_match("/^[a-zA-Z]\w{4,15}$/", $_POST['ps'])){
                    $parametros_query[0] .= "s";
                    array_push($parametros_query, $_POST['ps']);
                    $updates .= (empty($updates) ? "" : ", ") . "PASSWORD = ?";
                } else {
                    lanzar_error("La contraseña es inválida. Debe tener entre 5 y 15 caracteres; letras (no acentuadas, excluyendo la Ñ) y/o números.");
                }
            }
            
            if (se_subio_archivo("ft")){
                $foto = leer_imagen("ft", 150) or die();
                $updates .= (empty($updates) ? "" : ", ") . "FOTO_PERFIL = '" . $foto . "'";
            }
            
            //Creamos una variable que nos indica si el usuario al que se le desea modificar la cuenta es un jugador.
            $es_jugador = false;
            if($_POST['id'] == $_SESSION["ID_USUARIO"]){
                $es_jugador = ($_SESSION["TIPO_USUARIO"] == "JUGADOR");
            } else {
                //En caso de que el administrador quiera modificar la cuenta de alguien más, hacemos una consulta para saber si es jugador.
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
                /* Si es jugador, validamos y agregamos al query, los nuevos datos exclusivos de este tipo de cuenta (según los parámetros de la petición). */
                
                if (!empty($_POST['nc'])) {
                    if( ($d = DateTime::createFromFormat("Y-m-d", $_POST['nc'])) && ($d->format("Y-m-d") == $_POST['nc']) ){
                        $año_actual = intval(date("Y"));
                        $año_nacimiento = intval($d->format("Y"));
                        
                        if($año_nacimiento < ($año_actual - 100)){
                            lanzar_error("Año de nacimiento inválido.");
                        } else {
                            $parametros_query[0] .= "s";
                            array_push($parametros_query, $_POST['nc']);
                            $updates .= (empty($updates) ? "" : ", ") . "FECHA_NACIMIENTO = ?";
                        }
                    } else {
                        lanzar_error("La fecha de nacimiento es inválida.");
                    }
                }
                
                if (!empty($_POST['tel'])) {
                    $_POST['tel'] = eliminar_espacios($_POST['tel']);
                    if (preg_match("/^[0-9]{10}$/", $_POST['tel'])){
                        $parametros_query[0] .= "s";
                        array_push($parametros_query, $_POST['tel']);
                        $updates .= (empty($updates) ? "" : ", ") . "TELEFONO = ?";
                    } else {
                        lanzar_error("El número de teléfono es incorrecto (Debe de ser 10 dígitos, sin símbolos. Ej: \"9611234567\").");
                    }
                }
                
                if (!empty($_POST['sg'])) {
                    $parametros_query[0] .= "s";
                    array_push($parametros_query, $_POST['sg']);
                    $updates .= (empty($updates) ? "" : ", ") . "TIPO_SANGRE = ?";
                }
                
                if (!empty($_POST['fb'])) {
                    $_POST['fb'] = eliminar_espacios($_POST['fb']);
                    if (preg_match("/(?:(?:http|https):\/\/)?(?:www.)?facebook.com\/(?:(?:\w)*#!\/)?(?:pages\/)?(?:[?\w\-]*\/)?(?:profile.php\?id=(\d.*))?(?:[\w\-\.]*)?/", $_POST['fb'], $coincidencias)){
                        if (substr($coincidencias[0], 0, 7) !== "http://" && substr($coincidencias[0], 0, 8) !== "https://"){
                            $coincidencias[0] = "https://" . $coincidencias[0];
                        }
                        
                        $parametros_query[0] .= "s";
                        array_push($parametros_query, $coincidencias[0]);
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
                        
                        $parametros_query[0] .= "s";
                        array_push($parametros_query, $coincidencias[0]);
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
                        
                        $parametros_query[0] .= "s";
                        array_push($parametros_query, $coincidencias[0]);
                        $updates .= (empty($updates) ? "" : ", ") . "INSTAGRAM = ?";
                    } else {
                        lanzar_error("El enlace de Instagram no es válido.");
                    }
                }
            }
            
            iniciar_transaccion($conexion);
            
            if(!empty($updates)){
                /* Si al menos se modificó un campo (excluyendo los de los padecimientos), ejecutamos el query que hemos estado armando en las líneas anteriores.  */ 
                $updates = "UPDATE usuarios SET " . $updates . " WHERE ID_USUARIO = ?";
                
                //Le damos valor al último "?" del query con el ID de la cuenta a modificar.
                $parametros_query[0] .= "i";
                array_push($parametros_query, $_POST['id']);
                
                if(($consulta = $conexion->prepare($updates)) && $consulta->bind_param(...$parametros_query) && $consulta->execute()){
                    unset($updates, $parametros_query);
                } else {
                    cerrar_transaccion($conexion, false);
                    lanzar_error("Error de servidor (" . __LINE__ . ")");
                }
            }
            
            // Bloque eliminado: Se asegura de que no existan 2 cuentas con el mismo nombre completo.
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
            
            //Nos aseguramos de que el nuevo correo (en caso de existir) no esté ocupado por otra cuenta.
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
            
            //Si se mandaron los parámetros de los padecimientos predeterminados, hacemos las modificaciones correspondientes.
            if(isset($_POST['en']) && isset($_POST['al'])){
                $_POST['en'] = json_decode($_POST['en']);
                $_POST['al'] = json_decode($_POST['al']);
                if(!is_array($_POST['en']) || !is_array($_POST['al'])){
                    cerrar_transaccion($conexion, false);
                    lanzar_error("Error de servidor (" . __LINE__ . ")");
                }
                
                //Obtenemos los arreglos de los padecimientos actuales (antes de ser modificados).
                $padecimientos = get_padecimientos($conexion, $_POST['id'], true);
                
                //Añadimos los registros de las nuevas enfermedades (que están en la nuevo arreglo de enf. y que no están en la vieja).
                $query = "INSERT INTO enfermedades_usuarios (ID_USUARIO, ID_ENFERMEDAD) VALUES (?,?)";
                $consulta = $conexion->prepare($query) or lanzar_error("Error de servidor (" . __LINE__ . ")");
                foreach (array_diff($_POST['en'], $padecimientos['en']) as $enf) {
                    if(!$consulta->bind_param("ii", $_POST['id'], $enf) || !$consulta->execute()){
                        cerrar_transaccion($conexion, false);
                        lanzar_error("Error de servidor (" . __LINE__ . ")");
                    }
                }
                
                //Eliminamos los registros de las enfermedades que ya no se padecen (que están en el viejo arreglo de enf. y que no están en la nueva).
                $query = "DELETE FROM enfermedades_usuarios WHERE ID_USUARIO = ? AND ID_ENFERMEDAD = ?";
                $consulta = $conexion->prepare($query) or lanzar_error("Error de servidor (" . __LINE__ . ")");
                foreach (array_diff($padecimientos['en'], $_POST['en']) as $enf) {
                    if(!$consulta->bind_param("ii", $_POST['id'], $enf) || !$consulta->execute()){
                        cerrar_transaccion($conexion, false);
                        lanzar_error("Error de servidor (" . __LINE__ . ")");
                    }
                }
                
                //Añadimos los registros de las nuevas alergias.
                $query = "INSERT INTO alergias_usuarios (ID_USUARIO, ID_ALERGIA) VALUES (?,?)";
                $consulta = $conexion->prepare($query) or lanzar_error("Error de servidor (" . __LINE__ . ")");
                foreach (array_diff($_POST['al'], $padecimientos['al']) as $alg) {
                    if(!$consulta->bind_param("ii", $_POST['id'], $alg) || !$consulta->execute()){
                        cerrar_transaccion($conexion, false);
                        lanzar_error("Error de servidor (" . __LINE__ . ")");
                    }
                }
                
                //Eliminamos los registros de las alergias que ya no se padecen.
                $query = "DELETE FROM alergias_usuarios WHERE ID_USUARIO = ? AND ID_ALERGIA = ?";
                $consulta = $conexion->prepare($query) or lanzar_error("Error de servidor (" . __LINE__ . ")");
                foreach (array_diff($padecimientos['al'], $_POST['al']) as $alg) {
                    if(!$consulta->bind_param("ii", $_POST['id'], $alg) || !$consulta->execute()){
                        cerrar_transaccion($conexion, false);
                        lanzar_error("Error de servidor (" . __LINE__ . ")");
                    }
                }
            }
            
            /**
             * Prepara un string de otras enfermedades o alergias para ser insertado en la base de datos.
             * Ejemplo de entrada: "   Miopía  ;  epilepsia  . rotacitis   ".
             * Ejemplo de salida: "Miopía, epilepsia, rotacitis"
             * 
             * @param string $ot String de padecimientos que puede contener espacios de más, separados por comas, puntos y puntos y comas.
             * @return string String de otros padecimientos separados por comas. Se retorna false si la cadena de entrada no tuviese sentido o tuviera errores.
             */
            function procesar_otros_padecimientos($ot){
                $lista_de_padecimientos = array_unique(array_filter( array_map("preparar_oracion", preg_split( "/[.|,|;]/", $ot )), function($val){ return !empty($val); } ));
                if(count($lista_de_padecimientos) == 0){ 
                    lanzar_error("No se encontró ninguna enfermedad extra en el campo correspondiente.", false);
                    return false;
                }
                
                foreach ($lista_de_padecimientos as $padecimiento) {
                    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ' -]{2,}$/", $padecimiento)){
                        lanzar_error("La enfermedad o alergia \"$padecimiento\" está mal escrita (Sólo se admiten letras).", false);
                        return false;
                    }
                }
                
                return implode(",", $lista_de_padecimientos);
            }
            
            //Se actualizan las otras enfermedades
            if(isset($_POST['ot_en'])){
                $_POST['ot_en'] = eliminar_espacios($_POST['ot_en']);
                
                if(empty($_POST['ot_en'])){
                    //Si no manda el parámetro, eliminamos el campo de "otras_enfermedades" en su totalidad.
                    $query = "DELETE FROM otras_enfermedades WHERE ID_USUARIO = ?";
                    if(!($consulta = $conexion->prepare($query)) || !$consulta->bind_param("i", $_POST['id']) || !$consulta->execute()){
                        cerrar_transaccion($conexion, false);
                        lanzar_error("Error de servidor (" . __LINE__ . ")");
                    }
                } else {
                    //Se procesa el valor del parámetro y se almacena en la base de datos.
                    $_POST['ot_en'] = procesar_otros_padecimientos($_POST['ot_en']);
                    if($_POST['ot_en'] == false){ cerrar_transaccion($conexion, false); die(); }
                    
                    $query = "INSERT INTO otras_enfermedades (ID_USUARIO, DATOS) VALUES (?,?) ON DUPLICATE KEY UPDATE DATOS = ?";
                    if(!($consulta = $conexion->prepare($query)) || !$consulta->bind_param("iss", $_POST['id'], $_POST['ot_en'], $_POST['ot_en']) || !$consulta->execute()){
                        cerrar_transaccion($conexion, false);
                        lanzar_error("Error de servidor (" . __LINE__ . ")");
                    }
                }
            }
            
            //Se actualizan las otras alergias
            if(isset($_POST['ot_al'])){
                $_POST['ot_al'] = eliminar_espacios($_POST['ot_al']);
                
                if(empty($_POST['ot_al'])){
                    //Si no manda el parámetro, eliminamos el campo de "otras_alergias" en su totalidad.
                    $query = "DELETE FROM otras_alergias WHERE ID_USUARIO = ?";
                    if(!($consulta = $conexion->prepare($query)) || !$consulta->bind_param("i", $_POST['id']) || !$consulta->execute()){
                        cerrar_transaccion($conexion, false);
                        lanzar_error("Error de servidor (" . __LINE__ . ")");
                    }
                } else {
                    //Se procesa el valor del parámetro y se almacena en la base de datos.
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
            /**
             * Permite borrar la foto de perfil de una cuenta cualquiera.
             * Esta función sólo puede ser ejecutada por un administrador.
             * 
             * Parámetro obligatorio:
             * - "id": El ID de la cuenta a la que se le va a borrar la foto.
             * 
             * En caso de éxito, no devuelve nada.
             */
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
            /**
             * Permite eliminar una cuenta.
             * Esta función sólo puede ser ejecutada por un administrador.
             * 
             * Parámetro obligatorio:
             * - "id": El ID de la cuenta a eliminar.
             * 
             * No se pueden eliminar:
             * - Cuentas de tipo administrador.
             * - Cuentas de los coaches que están dirigiendo equipos.
             * 
             * En caso de éxito, no se devuelve nada.
             */
            if(empty($_POST['id'])){
                lanzar_error("No se envió el parámetro.");
            }
            
            if ($_SESSION["TIPO_USUARIO"] != "ADMINISTRADOR") {
                lanzar_error("Usted no tiene permiso para realizar esta acción.");
            }
            
            //Se valida que la cuenta seleccionada pueda ser eliminada.
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
            
            //Se elimina la cuenta en la base de datos.
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