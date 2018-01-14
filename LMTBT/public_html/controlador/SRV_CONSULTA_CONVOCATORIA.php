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
                case "consulta_especifica":
                    $consulta = $conexion->prepare("SELECT NOMBRE_TORNEO,FECHA_CIERRE_CONVOCATORIA FROM convocatoria WHERE ID_CONVOCATORIA = ?");
                    $consulta->bind_param("i",$_POST['id']);
                    if($consulta->execute()){
                        $res = $consulta->get_result();
			$info = $res->fetch_assoc();
                        echo "<h3 style='background-color:#333;color:#ffffff;width:93%;border-radius:3px;' id='nombre_convocatoria'>".$info["NOMBRE_TORNEO"]."</h3>".
                        "<h3 style='background-color:#333;color:#ffffff;width:93%;border-radius:3px;' id='fecha_cierre'>".$info["FECHA_CIERRE_CONVOCATORIA"]."</h3>";                      
                    } else {
                        echo "error";
                    }   
                break;
                case "recuperar_equipos_inscritos":                    
                    $consulta = $conexion->prepare("SELECT NOMBRE_EQUIPO,CUOTA,ID_ROSTER FROM rosters r INNER JOIN equipos e WHERE r.ID_CONVOCATORIA = ? AND r.ID_EQUIPO = e.ID_EQUIPO");
                    $consulta->bind_param("i",$_POST['id']);
                    if($consulta->execute()){
                        $res = $consulta->get_result();			
                        while($fila= $res->fetch_assoc()){
                            if($fila["CUOTA"] == "NO PAGADO"){
                                echo  "<tr id='" . $fila["ID_ROSTER"] . "'><td>" . $fila["NOMBRE_EQUIPO"] . "</td><td id='eventos".$fila["ID_ROSTER"]."'>".                           
                                "<a class='news' href='#body' id='pago' onclick='abrir_pantalla_para_poner_pago(" . $fila["ID_ROSTER"] . ")'><h5>¿Ya pago?</h5></a>".
                                "<a class='news' href='#body' id='expulsar' onclick='abrir_pantalla_para_expulsar(" . $fila["ID_ROSTER"] . ")'><h5>Expulsar</h5></a></td></tr>";
                            }else{
                                echo  "<tr id='" . $fila["ID_ROSTER"] . "'><td>" . $fila["NOMBRE_EQUIPO"] . "</td><td>".
                                      "<a class='news' href='#body'><h5>PAGADO</h5></a>"."</td></tr>";
                            }
                        }
                    } 
                break;
                case "poner_pagado":    
                    $cuota = "PAGADO";
                    $consulta = $conexion->prepare("UPDATE rosters SET CUOTA = ? WHERE ID_ROSTER = ?");
                    $consulta->bind_param("si",$cuota,$_POST['id']);
                    if($consulta->execute()){
                        echo "ok";
                    }else{
                        echo "Error del servidor intente mas tarde";
                    } 
                break;
                case "expulsar":    
                    $consulta = $conexion->prepare("UPDATE rosters SET ID_CONVOCATORIA = NULL WHERE ID_ROSTER = ?");
                    $consulta->bind_param("i",$_POST['id']);
                    if($consulta->execute()){
                        echo "ok";
                    }else{
                        echo "Error del servidor intente mas tarde";
                    } 
                break;
                case "consultar":
                    $fecha_actual = date('Y-m-d');
                    //$sql = "SELECT * FROM convocatoria WHERE FECHA_CIERRE_CONVOCATORIA <= '$fecha_actual' AND ID_CONVOCATORIA NOT IN (SELECT ID_CONVOCATORIA FROM roles_juego)";
                    $sql = "SELECT * FROM convocatoria WHERE ID_CONVOCATORIA NOT IN (SELECT ID_CONVOCATORIA FROM roles_juego)";
                    $resultado = $conexion->query($sql);                
                        while($fila = $resultado->fetch_assoc()){
                            /*echo "<tr id='" . $fila["ID_CONVOCATORIA"] . "'><td>" . $fila["NOMBRE_TORNEO"] . "</td><td>".$fila["FECHA_CIERRE_CONVOCATORIA"]."</td><td>".    
                            "<button class='btn-warning' onclick='abrirPantallaParaEditarConsulta(" . $fila["ID_CONVOCATORIA"] . ")'>Editar fecha</button>" . 
                            "<button class='btn-info' onclick='CREAR_ROL_JUEGOS(" .$fila["ID_CONVOCATORIA"] . ")'>Generar rol.</button>"."</td></tr>";*/
                            echo "<tr id='" . $fila["ID_CONVOCATORIA"] . "'><td>" . $fila["NOMBRE_TORNEO"] . "</td><td>".                           
                            "<a class='news' href='DETALLES_CONVOCATORIA.html' onclick='eviar_id_convocatoria(" . $fila["ID_CONVOCATORIA"] . ")'><h5>ver mas</h5></a></td></tr>";
                            //"<button class='btn-info' href='DETALLES_CONVOCATORIA.html' onclick='detallesConvocatoria(" . $fila["ID_CONVOCATORIA"] . ")'>ver mas</button>"."</td></tr>";
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
