<?php
include("SRV_CONEXION.php");
$db = new SRV_CONEXION();
$conexion = $db->getConnection();
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
 switch($_POST['tipo']){
     case "consultar_torneos_activos":        
        $sql = "SELECT *FROM convocatoria WHERE ESTADO = 'ACTIVO'";
        $resultado = $conexion->query($sql);
        while ($fila = $resultado->fetch_assoc()) {
            echo "<tr id='fila_" . $fila["ID_CONVOCATORIA"] . "'><td>" . $fila["NOMBRE_TORNEO"] . "</td><td>" .
            "<button class='btn-warning' onclick='abrir_pantalla_para_terminar_torneo(" . $fila["ID_CONVOCATORIA"] . ")'>terminar</button>". "</td></tr>";
        }
     break;
     case "terminar_torneo":        
        $conexion->autocommit(FALSE);        
        $id = $_POST['id'];
	$cambios_hechos = true;
        $id_equipo_ganador_torneo = array();
        $categoria_torneo;
	$imagen_torneo;
	$sql = "SELECT *FROM convocatoria WHERE ID_CONVOCATORIA = $id";
        if ($resultado = $conexion->query($sql)){
            $fila = $resultado->fetch_assoc();
            $categoria_torneo = $fila["ID_CATEGORIA"];
            $imagen_torneo = addslashes($fila["IMAGEN_ANUNCIO"]);
            $sql = "UPDATE convocatoria SET ESTADO = 'INACTIVO' WHERE ID_CONVOCATORIA = $id";
            $resultado = $conexion->query($sql);
            if ($resultado){                
                    $sql = "SELECT *FROM tabla_posiciones WHERE ID_CONVOCATORIA = $id ORDER BY PUNTOS_FAVOR DESC LIMIT 0,3";
                    if ($resultado = $conexion->query($sql)){                       
                        while ($fila = $resultado->fetch_assoc()) {                         				
				$id_equipo_ganador_torneo[] = $fila["ID_EQUIPO"];                                				                          
                        }
			$nombres_equipos = array();
			$contador = 0;
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
                            $sql = "SELECT *FROM categorias WHERE ID_CATEGORIA = $categoria_torneo";
                            if ($resultado = $conexion->query($sql)){
                                $fila = $resultado->fetch_assoc();
                                $categoria_torneo = $fila["NOMBRE_CATEGORIA"];                            
                                $titulo_noticia = "Ganadores categoria " . $categoria_torneo;
                                $descripcion = "Primer lugar para " . $nombres_equipos[0] . "\nSegundo lugar para " . $nombres_equipos[1] . "\nTercer lugar para " . $nombres_equipos[2];
                                //$sql = "INSERT INTO noticias VALUES(0,'$imagen_torneo','$titulo_noticia','$descripcion')";
                                $fecha_actual = date('Y-m-d');
                                $sql = "INSERT INTO noticias VALUES(0,'$titulo_noticia','$descripcion','$fecha_actual')";
                                if ($resultado = $conexion->query($sql)){
                                    $sql = "SELECT LAST_INSERT_ID()";
                                    if($resultado = $conexion->query($sql)){
                                        $fila = $resultado->fetch_assoc();
                                        $id_noticia = $fila["LAST_INSERT_ID()"];
                                        $sql = "INSERT INTO multimedia VALUES (0,'$imagen_torneo','$id_noticia')";
                                        if ($resultado = $conexion->query($sql)){                                        
                                        }else{
                                            $cambios_hechos = false;
                                        }
                                    }else{
                                        $cambios_hechos = false;
                                    }
                                } else {
                                    $cambios_hechos = false;                                    
                                }
                            }else{
                                $cambios_hechos = false;
                            }
			} else {
                            $cambios_hechos = false;
                            echo "El numero de equipos no es valido -> ";
			}
                    }else {
                        $cambios_hechos = false;			
                    }		
            } else {		
		$cambios_hechos = false;
            }
	} else {
            $cambios_hechos = false;
	}
    if ($cambios_hechos) {
        if ($conexion->commit()) {
            echo "ok";
	} else {
            echo "Falló la consignación de la transacción.";
	}
    } else {
        $conexion->rollback();
	echo "Error en la transaccion";
    }
    $conexion->autocommit(TRUE);
    break;  
 }
 $conexion->close();
?>