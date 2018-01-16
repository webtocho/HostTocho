<?php
    session_start();
    include("SRV_CONEXION.php");
     
    $db = new SRV_CONEXION();    
    $conexion = $db->getConnection();
    
    $debug=0;
    if(isset($_SESSION["TIPO_USUARIO"])) 
        if($_SESSION["TIPO_USUARIO"]=="ADMINISTRADOR")$debug=1;

     
    $id = $_POST['id'];
    
    switch($_POST['tipo']){	
        case "get":
            $sql = "SELECT * FROM noticias WHERE ID_NOTICIAS =".$id;
             if($resultado = $conexion->query($sql)){
                 $contenido = $resultado->fetch_assoc();
                 $noticia = array("TITULO", "CUERPO", "IMAGEN");
                $noticia[0]= "<h1>". $contenido["TITULO"] . "</h1>";
                $noticia[1]= "<p>Fecha de publicación: ". $contenido["FECHA_PUBLICACION"] ."</p><p>". $contenido["NOTICIA"] . "</p>";
                
                 $sql2 = "SELECT * FROM multimedia WHERE ID_NOTICIAS = $id"; 
                  if($resultado2 = $conexion->query($sql2)){
                      $texto="<ol class='carousel-indicators'> "; 
                      $texto2="<div class='carousel-inner'>";
        
                     
                      $cont=0;
                      while ($fila2 = $resultado2->fetch_assoc()){
                            $noticias["IMAGEN_NOTICIA"] = base64_encode($fila2["IMAGEN"]);
                            if($cont==0){ 
                                 $texto.=" <li data-target='#myCarousel' data-slide-to='".$cont."' class='active'></li>";
                                $texto2 .=  "<div class='item active'><img src='data:image/png;base64," . $noticias["IMAGEN_NOTICIA"] . "'  class='img-responsive'  style='max-width:auto; max-height: 900px;'></div>"; 
                            } else{ 
                                $texto.=" <li data-target='#myCarousel' data-slide-to='".$cont."' ></li>";
                                $texto2 .=  "<div class='item'><img src='data:image/png;base64," . $noticias["IMAGEN_NOTICIA"] . "' class='img-responsive' style='max-width:auto; max-height: 900px;'></div>"; 
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
            case "eliminarComent":
                
                 $sqlC = "DELETE  FROM comentarios WHERE ID_COMENTARIO = $id"; 
                 if($resultadoC = $conexion->query($sqlC)){}
            
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
                                
                                echo "<div class='panel panel-default'>";
                                echo"<div class='panel-heading' style='background-color: black;color: white;'>";    
                                    echo "<h4 class='media-heading'><p>".$usuario['NOMBRE']." ".$usuario['APELLIDO_PATERNO']." &nbsp &nbsp &nbsp &nbsp".$usuario['TIPO_USUARIO']."</p></h4>";
                                    if($debug=="1"){
                                        echo"<p class='text-right'>
                                            <button type='button' class='btn btn-danger btn-sm' onclick=eliminarComentario('".strip_tags($row['ID_COMENTARIO'])."')>
                                            <span class='glyphicon glyphicon-remove'></span> Remove 
                                            </button>
                                            </p>";
                                    } 
                                echo"</div>";
                                echo"<div class='panel-body'>";   
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
                                echo "<p> ".$row['COMENTARIO']." </p>  </div></div>";                               
                                echo "</div>";
                                echo "</div>";
                            }
                        }
                    }
                   }else{ }//eror
               break;
        
    }   
    $conexion->close();
    

       
?>