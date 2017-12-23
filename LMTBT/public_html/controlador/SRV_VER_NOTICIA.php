<?php
    session_start();
    include("SRV_CONEXION.php");
     
    $db = new SRV_CONEXION();    
    $conexion = $db->getConnection();
    
    $id = $_POST['id'];
    
    switch($_POST['tipo']){	
        case "get":
            $sql = "SELECT * FROM noticias WHERE ID_NOTICIAS =".$id;
             if($resultado = $conexion->query($sql)){
                 $contenido = $resultado->fetch_assoc();
                 $noticia = array("TITULO", "CUERPO", "IMAGEN");
                $noticia[0]= "<h1>". $contenido["TITULO"] . "</h1>";
                $noticia[1]= "<p>". $contenido["NOTICIA"] . "</p>";
                
                 $sql2 = "SELECT * FROM multimedia WHERE ID_NOTICIAS = $id"; 
                  if($resultado2 = $conexion->query($sql2)){
                      $texto="<ol class='carousel-indicators'> "; 
                      $texto2="<div class='carousel-inner'>";
        
                     
                      $cont=0;
                      while ($fila2 = $resultado2->fetch_assoc()){
                            $noticias["IMAGEN_NOTICIA"] = base64_encode($fila2["IMAGEN"]);
                            if($cont==0){ 
                                 $texto.=" <li data-target='#myCarousel' data-slide-to=".$cont." class='active'></li>";
                                $texto2 .=  "<div class='item active'><img src='data:image/png;base64," . $noticias["IMAGEN_NOTICIA"] . "' ></div>"; 
                            } else{ 
                                $texto.=" <li data-target='#myCarousel' data-slide-to=".$cont." ></li>";
                                $texto2 .=  "<div class='item'><img src='data:image/png;base64," . $noticias["IMAGEN_NOTICIA"] . "'></div>"; 
                            }
                           
                        $cont++;
                            
                      }
                      $texto.="</ol>";
                      $texto2.="</div>";
                      $noticia[2]=  $texto . $texto2 ;
                  }
                echo json_encode($noticia); 
             }
            break;
            case "comentar":
                 
                if (isset($_SESSION["ID_USUARIO"])){
                    $texto=$_POST['texto']; 
                     $result = $conexion->prepare("INSERT INTO comentarios(ID_NOTICIA,ID_USUARIO,COMENTARIO) VALUES (?,?,?) ");
                     $result->bind_param("iis",$id,$_SESSION["ID_USUARIO"],$texto);
                        if ($result->execute()) {
                            echo "1";
                        } else {
                            echo "0";//$result->error;
                        }              
                      
                }
                
            break;  
            case "cargarComentarios":
                $query = "SELECT * FROM comentarios WHERE ID_NOTICIA = ". $id ;
                $result = $conexion->query($query);
                  if($result){
                    if(mysqli_num_rows($result)<=0){// validamos que la consulta contenga informacion
                         echo "";
                    }
                    else{
                        while($row = mysqli_fetch_array($result)){// recorremos el arreglo de la consulta
                            $query2 = "SELECT * FROM usuarios WHERE ID_USUARIO = ".$row['ID_USUARIO'];
                            $result2 = $conexion->query($query2);
                            if($result2){
                                $usuario =mysqli_fetch_array($result2);
                                echo "<div class='media'> ";
           
                            //imagen
                         
                                if($usuario['FOTO_PERFIL']!=null){
                                     $imagen["IMAGEN"] = base64_encode($usuario["FOTO_PERFIL"]);
                                    echo "<div class='media-left'><img class='media-object' style='width:60px' src='data:image/png;base64," . $imagen["IMAGEN"] . "'  ></div>";
                                
                                }else{
                                    echo "<div class='media-left'> <img src='../vista/img/RC_IF_ANONIMO.png' class='media-object' style='width:60px'></div>";
                                }
                                // comentario
                                echo "<div class='media-body'>";
                                echo "<h4 class='media-heading'>".$usuario['NOMBRE']."</h4>";
                                echo "<p> ".$row['COMENTARIO']." </p>  </div></div>";
                            }
                             
                                 
                        }
                    }
                   }else{ }//eror
                
                

   

  
   
    

                
               break;
        
    }   
    $conexion->close();
    
    /*
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
    }*/
       
?>