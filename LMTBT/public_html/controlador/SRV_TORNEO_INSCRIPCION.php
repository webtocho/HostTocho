<?php
    session_start();
   define('SQL_HOST', "localhost");
 define('SQL_DATABASE', "id3551892_tochoweb");
 define('SQL_USER', "id3551892_team");
 define('SQL_PASSWORD', "tochoweb");
    include("SRV_CONEXION.php");
    $db = new SRV_CONEXION();
    $sql;
    $accion = $_POST["accion"];
    
    
    switch($accion) {
        case "getTorneo":
            $categoria =  $_POST["categoria"];
            
            $sql= "SELECT NOMBRE_TORNEO,ID_CONVOCATORIA FROM convocatoria where CATEGORIA = '$categoria'";
            $db->setQuery($sql);
            $resultado=$db->getResult();
            
            if($resultado != null){
                $data = json_encode($resultado,JSON_UNESCAPED_UNICODE);
                echo $data;
            }else{
               echo  "Fail";
            }
            
            break;
        case "getEquipos":
            
            if (isset($_SESSION["ID_USUARIO"])) {
                if ($_SESSION["TIPO_USUARIO"]=='COACH'){
                    $iduser = $_SESSION["ID_USUARIO"];
                    $sql= "SELECT NOMBRE_EQUIPO,ID_EQUIPO FROM equipos WHERE ID_COACH = '$iduser'";
                    $db->setQuery($sql);
                    $resultado=$db->getResult();
                    if($resultado != null){

                        $data = json_encode($resultado,JSON_UNESCAPED_UNICODE);
                        echo $data;
                    }else{
                       echo  "Fail";
                    }
                }
                else{
                    echo "!Type";
                }
                
            }else{
                echo "!Session";
            }
            
            
            
            break;
        case "setTorneo":
            $id_conv= $_POST["id_conv"];
            $id_equi = $_POST["id_equi"];
            $categ = $_POST["categoria"];
           
            $sql= "UPDATE rosters SET ID_CONVOCATORIA = '$id_conv' WHERE ID_EQUIPO ='$id_equi' AND CATEGORIA = '$categ'";
           
            $db->setQuery($sql);
            $resultado=$db->getResult(); 
            if($resultado==true){
                echo "ok";
            }else{
                 echo  "Fail";
            }
            break;
    
    }
    
    


  
    
    ?>