<?php    
    //Incluimos a la clase SRV_CONEXION(); para poder instanciarla
    include("SRV_CONEXION.php");     
    //Instanciamos a la clase SRV_CONEXION();
    $db = new SRV_CONEXION();
    //Recuperamos la conexion
    $conexion = $db->getConnection();        
    /*
     * Comprobamos si la sesion con la que se esta queriendo realizar una accion es la correcta
     * de lo contrario se expulsa sin poder realizar ninguna de las demas acciones si el usuario
     * es de tipo ADMINISTRADOR se le otorgan ciertos permisos como el de poder eliminar comentarios
     * que no sean de su agrado.
     */
    $debug=0;
    session_start();
    if(isset($_SESSION["TIPO_USUARIO"])) 
        if($_SESSION["TIPO_USUARIO"]=="ADMINISTRADOR")$debug=1;
        
        
    //Recuperamos el id de la noticia seleccionada
    $id = $_POST['id'];     
    //Declaramos un switch con todos los casos y los eventos con las cuenta una noticia
    switch($_POST['tipo']){	
        //Recuperamos la noticia que se ha seleccionado con ayuda del id de dicha noticia
        case "get":
            //Realizamos la consulta para recuperar el titulo y la descripcion de una noticia
            $sql = "SELECT * FROM noticias WHERE ID_NOTICIAS =".$id;
             if($resultado = $conexion->query($sql)){
                $contenido = $resultado->fetch_assoc();
                $noticia = array("TITULO", "CUERPO", "IMAGEN");
                $noticia[0]= "<h1>". $contenido["TITULO"] . "</h1>";
                $noticia[1]= "<p>Fecha de publicación: ". $contenido["FECHA_PUBLICACION"] ."</p><p>". $contenido["NOTICIA"] . "</p>";
                //Realizamos un segundo consulta a otra tabla para obtener las imagenes pertenecientes a una noticia
                 $sql2 = "SELECT * FROM multimedia WHERE ID_NOTICIAS = $id"; 
                  if($resultado2 = $conexion->query($sql2)){
                      $texto="<ol class='carousel-indicators'> "; 
                      $texto2="<div class='carousel-inner'>";                             
                      $cont=0;
                      //Almacenamos codigo html con las imagenes cargadas en un cadena para posteriormente regresarla como un json
                      while ($fila2 = $resultado2->fetch_assoc()){
                            $noticias["IMAGEN_NOTICIA"] = base64_encode($fila2["IMAGEN"]);
                            if($cont==0){ 
                                $texto.=" <li data-target='#myCarousel' data-slide-to='".$cont."' class='active'></li>";
                                $texto2 .=  "<div class='item active'><img   src='data:image/png;base64," . $noticias["IMAGEN_NOTICIA"] . "'  class='img-responsive'  style='max-width:auto; max-height: 900px;'></div>"; 
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
            /*
             * Elimina un comentario que se haya realizado en alguna noticia
             * dependieno de cual se haya seleccionado, esta accion solo la
             * puede llevar a cabo el administrador
             */
            case "eliminarComent":                
                 $sqlC = "DELETE  FROM comentarios WHERE ID_COMENTARIO = $id"; 
                 if($resultadoC = $conexion->query($sqlC)){}            
            break;
            /*
             * Registra un comentario a la base de datos
             * dicho comentario sera ligado a nuestra cuenta
             * para poder ser visualizado
             */
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
            /*
             *Recupera todos lo comentatios que se hayan realizado en una noticia
             * para poder visualizarlos en la parte inferior de la pagina en una seccion
             */
            case "cargarComentarios":
                //Se realiza la consulta a la BD
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
                                //Cargamos la informacion del usuario como su nombre y tipo de cuenta
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
                                //Cargamos la foto de perfil de la cuenta
                                if($usuario['FOTO_PERFIL']!=null){
                                     $imagen["IMAGEN"] = base64_encode($usuario["FOTO_PERFIL"]);
                                    echo "<div class='media-left'><img  class='img-rounded' style='width:60px' src='data:image/png;base64," . $imagen["IMAGEN"] . "'  ></div>";
                                
                                }else{
                                    echo "<div class='media-left'> <img src='../modelo/img/RC_IF_ANONIMO.png'  class='img-rounded' style='width:60px'></div>";
                                }
                                //Cargamos el comentario realizado por el usuario
                                echo "<div class='media-body'>";
                                echo "<p> ".strip_tags($row['COMENTARIO'])." </p>  </div></div>";                               
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