<?php
    include("SRV_CONEXION.php");
    $db = new SRV_CONEXION();    
    $noticias = array();
    $bandera = true;
    $conexion = $db->getConnection();
    $linea = $_POST['fila'];
    $limit=5;
    
    $sql = "SELECT * FROM noticias ORDER BY ID_NOTICIAS DESC LIMIT ".$linea.",5";
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
            //pagination
            
            $sql = "SELECT * FROM noticias ORDER BY ID_NOTICIAS ";
            if($resultado = $conexion->query($sql)){
                
                $linea=floor(intval($linea)/$limit); 
                $rows = mysqli_num_rows($resultado);
                $htmlP="<ul class='pagination'>";
                $pagination = floor($rows/$limit);
                 $htmlP.="<li ><a   onclick='recuperar_noticias(0)' >inicio</a></li>";
                for($i=0; $i<=$pagination;$i++){
                    if($i==$linea)
                        $htmlP.="<li class='disabled'><a   onclick='recuperar_noticias(".$i. ")' >".($i+1)."</a></li>";
                    else
                         $htmlP.="<li><a   onclick='recuperar_noticias(".$i. ")' >".($i+1)."</a></li>";
                }
                $htmlP.="<li ><a   onclick='recuperar_noticias(".$pagination.")' >Final</a></li>";
                $htmlP.="</ul>";
                echo $htmlP;
            }
    }
    $conexion->close();  
    
    /*
  <li><a href='#'>1</a></li>
  <li><a href='#'>2</a></li>
  <li><a href='#'>3</a></li>
  <li class='disabled'><a href='#'>4</a></li>
  <li><a href='#'>5</a></li>
*/
?>
