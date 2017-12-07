<?php       
    $conexion = new mysqli("localhost","id3551892_team","tochoweb", "id3551892_tochoweb"); 
    //Vemos si no nos pudimos conectar
    if($conexion->connect_error){
        header('Error interno del servidor: Imposible acceder a la base de datos.', true, 500);
    }            
            /////////////////////////////////////////////////////////////                       
            switch($_POST['tipo']){
                case "consultar":
                    $fecha_actual = date('Y-m-d');
                    $sql = "SELECT * FROM convocatoria WHERE FECHA_CIERRE_CONVOCATORIA <= '$fecha_actual' AND ID_CONVOCATORIA NOT IN (SELECT ID_CONVOCATORIA FROM roles_juego)";
                    $resultado = $conexion->query($sql);                
                        while($fila = $resultado->fetch_assoc()){
                            echo "<tr id='" . $fila["ID_CONVOCATORIA"] . "'><td>" . $fila["NOMBRE_TORNEO"] . "</td><td>".         
                            "<button class='btn-warning' onclick='abrirPantallaParaEditarConsulta(" . $fila["ID_CONVOCATORIA"] . ")'>cambiar</button>" . 
                            "<button class='btn-info' onclick='CREAR_ROL_JUEGOS(" .$fila["ID_CONVOCATORIA"] . ")'>Generar rol</button>"."</td></tr>";
                        }
                break;                                               
                    case "modificar":
                    $nueva_fecha = $_POST['nueva_fecha'];
                    $id = $_POST['id'];
                    //////////////////////////////////////
                    $nueva_fecha = strtotime($nueva_fecha); 
                    $nueva_fecha = date("Y-m-d", $nueva_fecha); 
                    $validar_nueva_fecha = explode('/',$_POST['nueva_fecha']);
                    if(count($validar_nueva_fecha) == 3){
                        if($validar_nueva_fecha[0] != "" && $validar_nueva_fecha[1] != "" && $validar_nueva_fecha[2] != ""){
                            if(checkdate($validar_nueva_fecha[0],$validar_nueva_fecha[1],$validar_nueva_fecha[2]) == true){
                                $sql = "UPDATE convocatoria SET FECHA_CIERRE_CONVOCATORIA='$nueva_fecha' WHERE ID_CONVOCATORIA = $id";
                                $resultado = $conexion->query($sql);  
                                if($resultado){
                                    echo "ok";
                                }else{
                                    echo "Error";
                                }    
                            }else{
                                echo "Ingresa una fecha valida";
                            }
                        }else{
                            echo "Ingresa un formato de fecha valido";
                        }
                    }else{
                        echo "Ingresa un formato de fecha valido";
                    }
                break;
            }
               //               
?>
