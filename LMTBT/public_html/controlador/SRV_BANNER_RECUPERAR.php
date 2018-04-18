<?php
    include("SRV_CONEXION.php");
    $db = new SRV_CONEXION();    
    //este arreglo se retorna como json y contiene, la imagen de banner y los 3 titulos de noticia que se mostraran
     $noticia = array("TITULO","TITULO 2","TITULO 3", "IMAGEN_NOTICIA");
    $conexion = $db->getConnection(); 
    $sql = "SELECT * FROM noticias ORDER BY ID_NOTICIAS DESC LIMIT 3";
    $index=0;
    //seleccionamos las 3 noticias mas recientes
    if($resultado = $conexion->query($sql)){
        if(mysqli_num_rows($resultado)>=3)
            while( ($fila = $resultado->fetch_assoc()) &&  $index<3){
            $id_noticia = $fila["ID_NOTICIAS"];
            $noticia[$index] ="<a class='h3' href='NOTICIAS_VER.html'  onclick='enviarIdNoticia(".$id_noticia. ")'><h3>". $fila["TITULO"]."<h3></a>";
            

             if($index==0){// si es la primer noticia, obtenemos una de sus imagenes para mostrar como banner
                $sql2 = "SELECT * FROM multimedia WHERE ID_NOTICIAS = $id_noticia LIMIT 1";        
                if($resultado2 = $conexion->query($sql2)){
                    $fila2 = $resultado2->fetch_assoc(); 
                    //asignamos la imgen del banner
                    $noticia[3] = base64_encode($fila2["IMAGEN"]);
                    $noticia[3] =  "data:image/png;base64,".$noticia[3];
                }
             }
             $index++;        
            }else{//si no existen mas de 3 noticias se asigna el banner predefinido
                $noticia[3]="../vista/img/RC_IF_BANNER.jpg";
                
               
                
            }
    
    }
     echo json_encode($noticia);
    $conexion->close();  
    
?>
