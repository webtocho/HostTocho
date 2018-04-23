<?php
//Incluimos a la clase SRV_CONEXION(); para poder instanciarla
include("SRV_CONEXION.php");
//Instanciamos a la clase SRV_CONEXION();
$db = new SRV_CONEXION();
//Recuperamos la conexion
$conexion = $db->getConnection();
/*
 * Comprobamos si la sesion con la que se esta queriendo realizar una accion es la correcta
 * en este caso solo el administrador, de lo contrario se expulsa sin poder realizar 
 * ninguna de las demas acciones
 */
session_start();
if (isset($_SESSION['ID_USUARIO']) && isset($_SESSION["TIPO_USUARIO"])) {
        if($_SESSION["TIPO_USUARIO"] != "ADMINISTRADOR"){
            echo "error";
            $conexion->close();
            return;
        }
} else {
        echo "error";
        $conexion->close();
        return;
}

//Declaramos un switch con todos los casos y los eventos con las cuenta un torneo activo
 switch($_POST['tipo']){
     /*
      * Recupera todos los torneos que se encuentren activos para
      * poder ser visualizados en una tabla en la pagina de inicio
      * y poder decidir si terminarlos o no, esto solo lo puede ver
      * y hacer el admnistrador     
      */
     case "consultar_torneos_activos":        
        $sql = "SELECT *FROM convocatoria WHERE ESTADO = 'ACTIVO'";
        $resultado = $conexion->query($sql);
        while ($fila = $resultado->fetch_assoc()) {
            echo "<tr id='fila_" . $fila["ID_CONVOCATORIA"] . "'><td>" . $fila["NOMBRE_TORNEO"] . "</td><td>" .
            "<button class='btn-warning' onclick='abrir_pantalla_para_terminar_torneo(" . $fila["ID_CONVOCATORIA"] . ")'>terminar</button>". "</td></tr>";
        }
     break;
     /*
      * Realiza una consulta a la BD para dar por termindo
      * un torneo el cual se encuentre activo y se obtiene
      * a los tres primeros lugares y se publica una noticia
      * anunciando a los ganadores
     */
     case "terminar_torneo":        
        //Se declara el inicio de una transaccion
        $conexion->autocommit(FALSE);        
        $id = $_POST['id'];
	$cambios_hechos = true;
        $id_equipo_ganador_torneo = array();
        $categoria_torneo;
	$imagen_torneo;
        //Obtenemos la imagen del torneo para generar la noticia y mostrarla en el inicio
	$sql = "SELECT *FROM convocatoria WHERE ID_CONVOCATORIA = $id";
        if ($resultado = $conexion->query($sql)){
            $fila = $resultado->fetch_assoc();
            $categoria_torneo = $fila["ID_CATEGORIA"];
            $imagen_torneo = addslashes($fila["IMAGEN_ANUNCIO"]);
            //El torneo se pasa a inactivo para indicar que ya termino.
            $sql = "UPDATE convocatoria SET ESTADO = 'INACTIVO' WHERE ID_CONVOCATORIA = $id";
            $resultado = $conexion->query($sql);
            if ($resultado){      
                    //Obtenemos las primeras tres posiciones de la tabla ordenada las cuales pertenecen a los equipos ganadores
                    $sql = "SELECT *FROM tabla_posiciones WHERE ID_CONVOCATORIA = $id ORDER BY PUNTOS_FAVOR DESC LIMIT 0,3";                   
                    if ($resultado = $conexion->query($sql)){                       
                        while ($fila = $resultado->fetch_assoc()) {                         				
				$id_equipo_ganador_torneo[] = $fila["ID_EQUIPO"];                                				                          
                        }
			$nombres_equipos = array();
			$contador = 0;
                        //Recuperamos la informacion de los equipos ganadores
                        foreach($id_equipo_ganador_torneo as $valor){
                            $sql = "SELECT *FROM equipos WHERE ID_EQUIPO = $valor";
                            if ($resultado = $conexion->query($sql)){
				$fila = $resultado->fetch_assoc();
				$nombres_equipos[] = $fila["NOMBRE_EQUIPO"];
                                $contador++;                               
                            } else {
				$cambios_hechos = false;				
                            }
			}                                    
			if ($contador == 3) {
                            //Recuperamos la categoria a la que pertenece el torneo que se ha concluido
                            $sql = "SELECT *FROM categorias WHERE ID_CATEGORIA = $categoria_torneo";
                            if ($resultado = $conexion->query($sql)){
                                $fila = $resultado->fetch_assoc();
                                $categoria_torneo = $fila["NOMBRE_CATEGORIA"];                            
                                $titulo_noticia = "Ganadores categoria " . $categoria_torneo;
                                $descripcion = "Primer lugar para " . $nombres_equipos[0] . "\nSegundo lugar para " . $nombres_equipos[1] . "\nTercer lugar para " . $nombres_equipos[2];                                
                                $fecha_actual = date('Y-m-d');
                                //Se crea y registra la noticia para dar a conocer a los primeros tres lugares del torneo
                                //y asi poder visualizarlo en el inicio de la pagina
                                $sql = "INSERT INTO noticias VALUES(0,'$titulo_noticia','$descripcion','$fecha_actual')";
                                if ($resultado = $conexion->query($sql)){
                                    $sql = "SELECT LAST_INSERT_ID()";
                                    if($resultado = $conexion->query($sql)){
                                        $fila = $resultado->fetch_assoc();
                                        $id_noticia = $fila["LAST_INSERT_ID()"];
                                        $sql = "INSERT INTO multimedia VALUES (0,'$imagen_torneo','$id_noticia')";
                                        if ($resultado = $conexion->query($sql)){                                        
                                        }else{
                                            //Se toma como fallo al realizar una consulta
                                            $cambios_hechos = false;
                                        }
                                    }else{
                                        //Se toma como fallo al realizar una consulta
                                        $cambios_hechos = false;
                                    }
                                } else {
                                    //Se toma como fallo al realizar una consulta
                                    $cambios_hechos = false;                                    
                                }
                            }else{
                                //Se toma como fallo al realizar una consulta
                                $cambios_hechos = false;
                            }
			} else {
                            //Se regresa un error en el caso de que no haya suficientes equipos inscritos en un torneo
                            $cambios_hechos = false;
                            echo "El numero de equipos no es valido -> ";
			}
                    }else {
                        //Se toma como fallo al realizar una consulta
                        $cambios_hechos = false;			
                    }		
            } else {	
                //Se toma como fallo al realizar una consulta
		$cambios_hechos = false;
            }
	} else {
            //Se toma como fallo al realizar una consulta
            $cambios_hechos = false;
	}
    if ($cambios_hechos) {
        if ($conexion->commit()) {
            //Se retorna "ok" en caso de que todo se haya ejecutado correctamente
            echo "ok";
	} else {            
            echo "Falló la consignación de la transacción.";
	}
    } else {
        //Se retorna un error y se deshacen los cambios ya realizados en la BD
        $conexion->rollback();
	echo "Error en la transaccion";
    }
    $conexion->autocommit(TRUE);
    break;  
 }
 $conexion->close();
?>