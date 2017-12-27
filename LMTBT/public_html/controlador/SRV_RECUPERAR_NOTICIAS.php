<?php
    include("SRV_CONEXION.php");
    $db = new SRV_CONEXION();    
    $noticias = array();
    $bandera = true;
    $conexion = $db->getConnection();
    $sql = "SELECT * FROM noticias ORDER BY  ID_NOTICIAS DESC";
    if($resultado = $conexion->query($sql)){
        $html="";     
        while ($fila = $resultado->fetch_assoc()){
             $html=""; 
            
            $id_noticia = $fila["ID_NOTICIAS"];
            $noticia = "<li>";
            $sql2 = "SELECT * FROM multimedia WHERE ID_NOTICIAS = $id_noticia LIMIT 1";        
         
 
            $html.="<div class='panel panel-default'>";
                $html.="<div class='panel-heading' style='background-color: black;color: white;'>";
                   // $html.="<div class='container'>";
                        $html.="<a class='news' href='VER_NOTICIA.html'  onclick='enviarIdNoticia(".$id_noticia. ")'><h2>".$fila["TITULO"]."</h2></a>";      
                $html.="</div>";
            if($resultado2 = $conexion->query($sql2)){
                $fila2 = $resultado2->fetch_assoc(); 
               
                $noticias["IMAGEN_NOTICIA"] = base64_encode($fila2["IMAGEN"]);
                $html.="<div class='panel-body'>";     
                    $html.="<div class='media'>";
                        $html.=" <div class='media-left'>";
                            $html.=  "<div class='blog-img'>"."<img src='data:image/png;base64," . $noticias["IMAGEN_NOTICIA"] . "'  class='media-object' style='width:200px; ' alt=''/>"."</div>"; 
                        $html.="</div>";
            }
                    
                        $html.=" <div class='media-body'>";
                            $html.="<h4 class='media-heading'>".$fila["FECHA_PUBLICACION"]."</h4>";
                            $html.= "<p>" . substr($fila["NOTICIA"],0,300) . " ...</p>";
                        $html.= "</div>";
                    $html.= "</div>";
                $html.= "</div>";
            $html.=" </div><hr>";
            echo $html;
       
            }
    }
    $conexion->close();   
?>
