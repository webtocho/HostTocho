<?php
    include("SRV_CONEXION.php");
    $db = new SRV_CONEXION();    
    $noticias = array();
    $bandera = true;
    $conexion = $db->getConnection();
    $sql = "SELECT * FROM noticias";
    if($resultado = $conexion->query($sql)){
        while ($fila = $resultado->fetch_assoc()){
            $id_noticia = $fila["ID_NOTICIAS"];
            $noticia = "<li>";
            $sql2 = "SELECT * FROM multimedia WHERE ID_NOTICIAS = $id_noticia LIMIT 1";        
            if($resultado2 = $conexion->query($sql2)){
                    $fila2 = $resultado2->fetch_assoc();
                //while ($fila2 = $resultado2->fetch_assoc()){
                    $noticias["IMAGEN_NOTICIA"] = base64_encode($fila2["IMAGEN"]);
                    $noticia .=  "<div class='blog-img'>"."<img src='data:image/png;base64," . $noticias["IMAGEN_NOTICIA"] . "'class='img-responsive' alt=''/>"."</div>"; 
                //}
            }
            $noticia .= "<div class='blog-info'><a class='news' href='VER_NOTICIA.html'  onclick='enviarIdNoticia(".$id_noticia. ")'>" . $fila["TITULO"] . "</a>" .
            "<p>" . $fila["NOTICIA"] . "</p>" .
            "<div class='bog_post_info infoPost'><span class='datePost'><a href='#' class='post_date'>".$fila["FECHA_PUBLICACION"]."</a></span>" .
            "<span class='commentPost'><a class='icon-comment-1' title='Comments - 2' href='#'><i class='glyphicon glyphicon-comment'></i>2</a></span>" .
            "<span class='likePost'><i class='glyphicon glyphicon-heart'></i><a class='icon-heart' title='Likes - 4' href='#'>4</a></span>" .
            "<div class='clearfix'></div></div></div></li>";
            echo $noticia;
        }
    }
    $conexion->close();   
?>