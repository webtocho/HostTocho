<?php
    session_start();
    include("SRV_CONEXION.php");
    $conn = new SRV_CONEXION();
    $db = $conn->getConnection();
    date_default_timezone_set('America/Mexico_City');
    $sql;
    
    $accion = $_POST["accion"];
   
    
    switch($accion) {
        case "getCategorias":
            
            $result= $db->query("SELECT * FROM categorias");
            if($result == null){
                echo "0";
            }
            else {
                if(mysqli_num_rows($result)<=0 ){
                    echo "1";
                }
                else{
                     while($row = mysqli_fetch_array($result) ){
                         echo "<option value='".$row['ID_CATEGORIA']."'>".$row['NOMBRE_CATEGORIA']."</option>";
                     
                     }
                }
            
            }
            
            break;
        
        case "getTorneo":
            $categoria =  $_POST["categoria"];
            $fecha_actual = date('Y-m-d');
            $sql= "SELECT NOMBRE_TORNEO,ID_CONVOCATORIA FROM convocatoria where ID_CATEGORIA = '$categoria' and FECHA_CIERRE_CONVOCATORIA >= '$fecha_actual'";
            
            $result=$db->query($sql);
            
            if($result== null){
                
                echo "0";
            }else{
                if(mysqli_num_rows($result)<=0 ){
                           echo "1";
                       }
                       else{
                            while($row = mysqli_fetch_array($result) ){
                                
                                 echo "<option value='".$row['ID_CONVOCATORIA']."'>".$row['NOMBRE_TORNEO']."</option>";

                            }
                           }
               
            }
            
            break;
        case "getEquipos":
            
            if (isset($_SESSION["ID_USUARIO"])) {
                if ($_SESSION["TIPO_USUARIO"]=='COACH'){
                    
                    $iduser = $_SESSION["ID_USUARIO"];
                    $sql= "SELECT NOMBRE_EQUIPO,ID_EQUIPO FROM equipos WHERE ID_COACH = '$iduser'";
                    $result=$db->query($sql);
                    if($result == null){
                         echo  "0";
                    }else{
                       if(mysqli_num_rows($result)<=0 ){
                           echo "1";
                       }
                       else{
                            while($row = mysqli_fetch_array($result) ){
                                
                                 echo "<option value='".$row['ID_EQUIPO']."'>".$row['NOMBRE_EQUIPO']."</option>";

                            }
                           }
                       }
                    }
                
                else{
                    echo "2";
                }
                
            }else{
                echo "3";
            }
            break;
            
        case "setTorneo":
            $id_conv= $_POST["id_conv"];
            $id_equi = $_POST["id_equi"];
            $categ = $_POST["categoria"];
           
           // $sql= "UPDATE rosters SET ID_CONVOCATORIA = '$id_conv' WHERE ID_EQUIPO ='$id_equi' AND ID_CATEGORIA = '$categ' AND ID_CONVOCATORIA IS NULL";
            $result=$db->prepare("UPDATE rosters SET ID_CONVOCATORIA = ? WHERE ID_EQUIPO = ? AND ID_CATEGORIA = ? AND ID_CONVOCATORIA IS NULL");
            $result->bind_param("iii",$id_conv,$id_equi,$categ);
            $result->execute();
            if($resultado==null){
                echo "0";
            }else if($resultado==false){
                 echo  "1";
            }else{
                echo "2";
            }
            break;
    
    }
    
    


  
    
    ?>