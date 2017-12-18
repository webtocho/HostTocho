<?php
    require 'SRV_CONEXION.php';
    session_start();
     $conn = new SRV_CONEXION();

    
    $db = $conn->getConnection();
    
    
    $sql;
    $accion = $_POST["accion"];
    switch($accion) {
        case "getTable":
                     
                         
                            $idtorneo = $_POST["id"];
                            $sql ="SELECT ID_ROL_JUEGO,ID_CONVOCATORIA,equipos.NOMBRE_EQUIPO,eq.NOMBRE_EQUIPO as NAME,FECHA,HORA,CAMPO FROM roles_juego INNER JOIN equipos ON roles_juego.ID_EQUIPO_1 = equipos.ID_EQUIPO INNER JOIN equipos as eq ON roles_juego.ID_EQUIPO_2 = eq.ID_EQUIPO WHERE ID_CONVOCATORIA =".$idtorneo;
                            $result=$db->query($sql);
                            if($result != null ){
                                if(mysqli_num_rows($result)<=0 ){
                                echo "Failx";
                                }
                                else{
                                    if (isset($_SESSION["ID_USUARIO"])) {
                                        if ($_SESSION["TIPO_USUARIO"]=='CAPTURISTA' || $_SESSION["TIPO_USUARIO"]=='ADMINISTRADOR'){
                                    
                                            while($row = mysqli_fetch_array($result) ){
                                                echo "<tr><td>".$row['NOMBRE_EQUIPO']."</td><td></td><td>".$row['NAME']."</td><td>".$row['FECHA']."</td><td>".$row['HORA']."</td><td>".$row['CAMPO']."</td><td><a class='btn btn-floating  edit' onclick='editTable()' id='".$row['ID_ROL_JUEGO']."'><i class='material-icons' >edit</i></a></td>"."</tr>";

                                            }
                                        }
                                        else{
                                             while($row = mysqli_fetch_array($result) ){
                                                echo "<tr><td>".$row['NOMBRE_EQUIPO']."</td><td></td><td>".$row['NAME']."</td><td>".$row['FECHA']."</td><td>".$row['HORA']."</td><td>".$row['CAMPO']."</tr>";

                                            }
                                        }
                                    }
                                    else{
                                        while($row = mysqli_fetch_array($result) ){
                                                echo "<tr><td>".$row['NOMBRE_EQUIPO']."</td><td></td><td>".$row['NAME']."</td><td>".$row['FECHA']."</td><td>".$row['HORA']."</td><td>".$row['CAMPO']."</tr>";

                                            }
                                    }

                                }

                            }
                            else{
                               echo  "Fail";
                            }

                        

                        
                        
                     
                    break;
        case "getTorneo":
            
            $sql = "SELECT * FROM convocatoria WHERE ESTADO = 'ACTIVO'";
            $result=$db->query($sql);
             if($result != null ){
                                if(mysqli_num_rows($result)<=0 ){
                                echo "Failx";
                                }
                                else{
                                    while($row = mysqli_fetch_array($result) ){
                                        echo "<option value='".$row['ID_CONVOCATORIA']."'>".$row['NOMBRE_TORNEO']."</option>";

                                    }

                                }

                            }
                            
            break;
        
         
     }
    ?> 


  
    
   
