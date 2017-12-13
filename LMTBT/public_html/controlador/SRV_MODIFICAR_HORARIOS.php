<?php
    session_start();
    
    include("SRV_CONEXION.php");
    $db = new SRV_CONEXION();
    $sql;
    $accion = $_POST["accion"];
    
    
    switch($accion) {
       
        case "getData":
            $ide = $_POST["id"];
            
            $sql ="SELECT equipos.NOMBRE_EQUIPO,eq.NOMBRE_EQUIPO as NAME,FECHA,HORA,CAMPO FROM roles_juego INNER JOIN equipos ON roles_juego.ID_EQUIPO_1 = equipos.ID_EQUIPO INNER JOIN equipos as eq ON roles_juego.ID_EQUIPO_2 = eq.ID_EQUIPO WHERE ID_ROL_JUEGO = ".$ide;
           
             $db->setQuery($sql);
            $result=$db->getResult();
            
            if($result!= null){
               
                    $data = json_encode($result,JSON_UNESCAPED_UNICODE);
                        echo $data;
                        
                    
            }else{
                echo "Fail";
            }
            break;
            
        case "update":
            $fecha = $_POST["fecha"];
            $hora = $_POST["hora"];
            $campo = $_POST["campo"];
            $id = $_POST["id"];
             $sql ="UPDATE roles_juego SET FECHA = '$fecha',HORA ='$hora',CAMPO ='$campo' WHERE ID_ROL_JUEGO ='$id'";
              $db->setQuery($sql);
            $result=$db->ExecuteQuery();
            if(result){
                echo "ok";
            }
            else{
                echo "Fail";
            }
              
            break;
            
            

    
   }
    
    ?> 


  
    
   