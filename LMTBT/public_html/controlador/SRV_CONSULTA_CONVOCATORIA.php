<?php   
    date_default_timezone_set('America/Mexico_City');
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
            /////////////////////////////////////////////////////////////                       
            switch($_POST['tipo']){
                case "consultar":
                    $fecha_actual = date('Y-m-d');
                    //$sql = "SELECT * FROM convocatoria WHERE FECHA_CIERRE_CONVOCATORIA <= '$fecha_actual' AND ID_CONVOCATORIA NOT IN (SELECT ID_CONVOCATORIA FROM roles_juego)";
                    $sql = "SELECT * FROM convocatoria WHERE ID_CONVOCATORIA NOT IN (SELECT ID_CONVOCATORIA FROM roles_juego)";
                    $resultado = $conexion->query($sql);                
                        while($fila = $resultado->fetch_assoc()){
                            echo "<tr id='" . $fila["ID_CONVOCATORIA"] . "'><td>" . $fila["NOMBRE_TORNEO"] . "</td><td>".$fila["FECHA_CIERRE_CONVOCATORIA"]."</td><td>".    
                            "<button class='btn-warning' onclick='abrirPantallaParaEditarConsulta(" . $fila["ID_CONVOCATORIA"] . ")'>Editar fecha</button>" . 
                            "<button class='btn-info' onclick='CREAR_ROL_JUEGOS(" .$fila["ID_CONVOCATORIA"] . ")'>Generar rol.</button>"."</td></tr>";
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
                                $consulta = $conexion->prepare("UPDATE convocatoria SET FECHA_CIERRE_CONVOCATORIA = ? WHERE ID_CONVOCATORIA = ?");
                                $consulta->bind_param("si",$nueva_fecha,$id);
                                if($consulta->execute()){
                                    echo "ok";
                                } else {
                                    echo "error";
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
            $conexion->close(); 
               //               
?>
