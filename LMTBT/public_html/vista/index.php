<!--
Author: W3layouts
Author URL: http://w3layouts.com
License: Creative Commons Attribution 3.0 Unported
License URL: http://creativecommons.org/licenses/by/3.0/
-->
<!DOCTYPE HTML>
<html>
    <head>
        <title>Liga Municipal De Tocho Bandera de Tuxtla</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="keywords" content="Basketball Responsive web template, Bootstrap Web Templates, Flat Web Templates, Android Compatible web template,
              Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyErricsson, Motorola web design" />
        <script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
       <!-- <link href="css/bootstrap-3.1.1.min.css" rel="stylesheet" type="text/css">-->
       <link href='img/IF_LOGO.png' rel='shortcut icon' type='image/png'>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <!-- Custom Theme files -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="css/owl.carousel.css" rel="stylesheet" type="text/css" media="all" />
        <link href="css/style.css" rel='stylesheet' type='text/css' />
        
        <script type="text/javascript" src="../controlador/JS_LIBRERIAS_jquery.min.js"></script>
        <script type="text/javascript" src="../controlador/JS_PLANTILLA_move-top.js"></script>
        <script type="text/javascript" src="../controlador/JS_PLANTILLA_easing.js"></script>
        <script type="text/javascript" src="../controlador/JS_FELICITACIONES.js"></script>
        <script type="text/javascript" src="../controlador/JS_VALIDAR_ACCESO_USUARIO.js"></script>
        <script type="text/javascript" src="../controlador/JS_CONVOCATORIAS_VENCIDAS.js"></script>
        <script type="text/javascript" src="../controlador/JS_RECUPERAR_NOTICIAS.js"></script>
        <script type="text/javascript" src="../controlador/JS_CREAR_ROL_JUEGOS.js"></script>
        <script type='text/javascript' src='../controlador/JS_TORNEOS_ACTIVOS.js'></script>
        <script type="text/javascript" src="../controlador/JS_TABLA_POSICIONES.js"></script>	   
        <script type='text/javascript' src='../controlador/JS_SESION_INICIAR_CERRAR.js'></script>
        <script type="text/javascript" src="../controlador/JS_FUNCIONES.js"></script>
        <script type="text/javascript" src="../controlador/JS_ENVIAR_ID_NOTICIA.js"></script>
        <script type="text/javascript" src="../controlador/JS_ENVIAR_ID_CONVOCATORIA.js"></script>         
	
	   
       <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
        <script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
        <!--/script-->
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $(".scroll").click(function (event) {
                    event.preventDefault();
                    $('html,body').animate({scrollTop: $(this.hash).offset().top}, 900);
                });
            });
        </script>
        <script type="text/javascript" src="../controlador/JS_CUENTAS_LOGOUT.js"></script>
    </head>
    <body>
        <div id="alertaSucces" class="modal fade" role="dialog">            
        </div>        
        
        
        <!--Ventana emergente de confirmacion para terminar un torneo-->
        <div class="modal fade" id="ventanaConfirmacion" role="dialog">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Confirmación</h4>
                </div>
                <div class="modal-body">
                  ¿Estás seguro de que deseas terminar este torneo?
                </div>
                <div class="modal-footer">
                  <button id="botonConfirmacion" type="button" class="btn btn-success">Sí</button>
                  <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                </div>
              </div>
            </div>
         </div>
        <!--Ventana emergente de confirmacion para terminar un torneo-->
        
        <div class="container">
            <style type="text/css">
                @media(max-width: 550px){
                    .container{
                        width: 100%;
                        float: left;
                    }
                }
            </style>
            <div class="header" id="home">
                <div class="subhead white">
                    <nav class="navbar navbar-default" role="navigation">
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                            <a class="navbar-brand" href="index.php"><h1 style="font-size: 25px;">LMTB <span>Tuxtla</span></h1><img src="img/IF_LOGO.jpg" alt="TochoWeb" id="imglogoindex" style="height: 125px;width: 168px;"/></a>
                        </div><br><br>                        <!--/.navbar-header-->

                        <div class="collapse navbar-collapse pull-right" id="bs-example-navbar-collapse-1">
                            <ul class="nav navbar-nav">
                                <li id = "acceso_asignacion_horarios"></li>
                                <li id = "acceso_categorias_edicion"></li>                                
                                   <!--<a href='REGISTRO_COACH.html'>Registrar coach</a>-->
                                </li>
                                <li id = "acceso_convocatoria"></li> 
                                <li id = "acceso_cuentas_busqueda"></li>
                                <li id = "acceso_cuentas_detalles"></li>                                
                                <li id = "acceso_equipos_ver"></li>    
                                <li id = "acceso_estadisticas"></li>                                    
                                <li id = "acceso_crear_noticias"></li>
                                <li id = "acceso_registrar_jugador">
				<!--<a href='REGISTRAR_JUGADOR.html'>Registrar Jugador</a>-->
                                </li>
                               
                                
                                <li id = "acceso_roles_juego">
                                   <!--<a href='ROLES_JUEGO.html'>Roles de juego</a>-->
                                </li>    
                                <li id = "acceso_torneo_inscripcion"></li>
                                <li id = "acceso_registro_cuenta"></li>                                
                                <li id = "iniciar_cerrar_session"></li>
                            </ul>
                        </div>
                        <!--/.navbar-collapse-->
                        <!--/.navbar-->
                    </nav>
                </div>
            </div>
            <!--/start-banner-->
            <div class="banner">
                <div class="banner-inner">
                    <div class="callbacks_container">
                        <ul class="rslides callbacks callbacks1" id="slider4">
                            <li class="callbacks1_on" style="display: block; float: left; position: relative; opacity: 1; z-index: 2; transition: opacity 500ms ease-in-out;">
                                <div class="banner-info">
                                    <h3 id="titulo1">LA LIGA DE LOS VERDADEROS JUGADORES</h3>
                                </div>
                            </li>
                            <li class="" style="display: block; float: none; position: absolute; opacity: 0; z-index: 1; transition: opacity 500ms ease-in-out;">
                                <div class="banner-info">
                                    <h3 id="titulo2">CONVIERTETE EN EL MEJOR JUGADOR DEL TORNEO</h3>
                                </div>
                            </li>
                            <li class="" style="display: block; float: none; position: absolute; opacity: 0; z-index: 1; transition: opacity 500ms ease-in-out;">
                                <div class="banner-info">
                                     <h3 id="titulo3">SIGUE HASTA LA VICTORIA</h3>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <!--banner-Slider-->
                    <script src="../controlador/JS_PLANTILLA_responsiveslides.min.js"></script>
                    <script>
// You can also use "$(window).load(function() {"
$(function () {
    // Slideshow 4
    $("#slider4").responsiveSlides({
        auto: true,
        pager: true,
        nav: false,
        speed: 500,
        namespace: "callbacks",
        before: function () {
            $('.events').append("<li>before event fired.</li>");
        },
        after: function () {
            $('.events').append("<li>after event fired.</li>");
        }
    });

});
                    </script>
                </div>
            </div>
            <!--//end-banner-->
            <!--/start-main-->
            <div class="main-content">
                <!--/soccer-inner-->
                <div class="soccer-inner">
                    <!--/soccer-left-part-->
                    <div class="col-md-8 soccer-left-part">
                        <!--/about-->
                        <div class="about">
                            <h3 class="tittle">Calendario</h3>
                            <div class="sap_tabs">
                                <div id="horizontalTab" style="display: block; width: 100%; margin: 0px;">
                                    <ul class="resp-tabs-list">
                                        <li class="resp-tab-item grid1" aria-controls="tab_item-0" role="tab"><span>PROXIMO PARTIDO</span></li>
                                    </ul>
                                    <div class="resp-tabs-container">
                                        <div class="tab-1 resp-tab-content" aria-labelledby="tab_item-0">
                                            <div class="facts" style="background-image: url(img/RC_IF_TEAM_BG.jpeg)">
                                                <div class="tab_list">
                                                    <table id="ProximosPartidos">
                                                        <tbody>
                                                            <tr>
                                                                <td class="one">01 June 10:00</td>
                                                                <td class="one">Angel</td>
                                                                <td class="one">VS</td>
                                                                <td class="one">Jimmy</td>

                                                            </tr>
                                                            <tr>
                                                                <td class="one">01 June 19:00</td>
                                                                <td class="one">Pro Soccer</td>
                                                                <td class="one">VS</td>
                                                                <td class="one">Genoa</td>

                                                            </tr>
                                                            <tr>
                                                                <td class="one">01 June 12:00</td>
                                                                <td class="one">Atlanta</td>
                                                                <td class="one">VS</td>
                                                                <td class="one">Napoli</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="one">01 June 30:00</td>
                                                                <td class="one">Atlanta</td>
                                                                <td class="one">VS</td>
                                                                <td class="one">Fiorentina</td>

                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>                                     
                                        <script src="../controlador/JS_PLANTILLA_easyResponsiveTabs.js" type="text/javascript"></script>
                                        <script type="text/javascript">
$(document).ready(function () {
    $('#horizontalTab').easyResponsiveTabs({
        type: 'default', //Types: default, vertical, accordion
        width: 'auto', //auto or any width like 600px
        fit: true   // 100% fit in a container
    });
});
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--//about-->
                        <div class="about">
                            <br><h3 class="tittle">Tabla de Posiciones</h3>
                            <div class="facts" style="background-image: url(img/RC_IF_TEAM2_BG.jpeg)">
                                <a><h3>Estado del Torneo</h3></a>
                                <center>
                                    <button onclick="CargarConvocatorias(1)" class="btn btn-primary">En curso</button>
                                    <button onclick="CargarConvocatorias(2)" class="btn btn-success">Terminados</button>
                                    <button onclick="CargarConvocatorias(3)" class="btn btn-warning">Todos</button>
                                </center>
                                <br/>
                                <select id="Select_Torneos" class="form-control" onchange="CargarTablaPosiciones(this.value)">
                                    <option>Click en el Estado del Torneo</option>
                                </select><br>
                                <div>
                                    <table id="Div_Estadisticas" class="table"></table>
                                </div>
                            </div>
                        </div>
                        <!--/video-->
                        <div class="video">
                            <br><h3 class="tittle">Ultimo Video</h3>
                            <iframe width="560" height="315" src="https://www.youtube.com/embed/dsYQjgz3Z3E" frameborder="0" gesture="media" allow="encrypted-media" allowfullscreen></iframe>
                        </div>
                        <br>
                         <h3 class="tittle">Nuestras Noticias</h3>
                        <div id="apartadoNoticia">
                        </div>
                        <!--//video NOTICIAS        
                        <!-- 
                        
                        <div class="banner-slider">
                           
                            <div class="callbacks_container">
                                <ul class="rslides" id="slider3" style="height: 471px;overflow: auto">  
                                    
                                </ul>
                            </div>
                        </div>
                        -->
                        <!--banner Slider starts Here-->
                        <script src="../controlador/JS_PLANTILLA_responsiveslides.min.js"></script>
                        <script>
                            // You can also use "$(window).load(function() {"
                            $(function () {
                                // Slideshow 3
                                $("#slider3").responsiveSlides({
                                    auto: true,
                                    pager: false,
                                    nav: true,
                                    speed: 500,
                                    namespace: "callbacks",
                                    before: function () {
                                        $('.events').append("<li>before event fired.</li>");
                                    },
                                    after: function () {
                                        $('.events').append("<li>after event fired.</li>");
                                    }
                                });

                            });
                        </script>
                    </div>
                    <!--//soccer-left-part-->
                    <!--/soccer-right-part-->
                    <div class="col-md-4 soccer-right-part">

                         <!--Apartado para las convocatorias que ya se han vencido-->
                        <div id="apartado_convocatorias_lanzadas">                           
                        </div>
                        <!--Apartado para las convocatorias que ya se han vencido-->
                        <!--Apartado para llistar los torneos activos-->
                        <div id="apartado_torneos_activos">
                        </div>                         
                        <!--Apartado para listar los torneos activos-->

                        <!--Apartado Cumpleaños:inicia -->
                        <div class="modern" id="ApartadoCumpleanios">
                            <h4 class="side">Cumpleañeros</h4>
                            <div id="example1">
                                <div id="owl-demo" class="owl-carousel text-center"></div>
                            </div>
                            <script src="../controlador/JS_PLANTILLA_owl.carousel.js"></script> 
                        </div>
                        <br>
                        <!--Apartado Cumpleaños:termina -->
<!--Facebook-->
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = 'https://connect.facebook.net/es_LA/sdk.js#xfbml=1&version=v2.11';
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<!--facebook-->
<!--Youtube-->
<script src="https://apis.google.com/js/platform.js"></script>
<!--Youtube-->
                        <div class="connect">
                            <h4 class="side">PERMANECE CONECTADO</h4>
                            <ul class="stay">
                                <center><div class="fb-like" data-href="https://www.facebook.com/Liga-Municipal-De-Tocho-Bandera-de-Tuxtla-472623376268881/" data-width="300" data-layout="standard" data-action="like" data-size="small" data-show-faces="true" data-share="true"></div></center><br>
                                <li class="c5-element-facebook"><a target="_blank" href="https://www.facebook.com/Liga-Municipal-De-Tocho-Bandera-de-Tuxtla-472623376268881/"><span class="icon"></span><span class="text">Visita nuestra pagina en Facebook</span></a></li>
                                <li class="c5-element-twitter"><a target="_blank" href="https://roger930.tumblr.com/"><span class="fa fa-tumblr-square" style="font-size:25px;color:#FFFFFF"></span><span class="text">&nbsp;&nbsp;&nbsp;Visita nuestra pagina en Tumblr</span></a></li>
                                <div class="g-ytsubscribe" data-channelid="UCyt2LwpS9czwaM7E0j9DI0Q" data-layout="full" data-count="default"></div>
                                <li class="c5-element-gg"><a target="_blank" href="https://www.youtube.com/channel/UCyt2LwpS9czwaM7E0j9DI0Q/feed"><span class="fa fa-youtube-play" style="font-size:25px;color:#FFFFFF"></span><span class="text">&nbsp;&nbsp;&nbsp;Visita nuestra pagina en Youtube</span></a></li>
                                <li class="c5-element-twit"><a target="_blank" href="https://twitter.com/cristia72456408"><span class="fa fa-twitter" style="font-size:25px;color:#FFFFFF"></span><span class="text">&nbsp;&nbsp;&nbsp;Visita nuestra pagina en Twitter</span></a></li>
                                <li class="c5-element-insta"><a target="_blank" href="https://www.instagram.com/lmtb_tuxtla/"><span class="fa fa-instagram" style="font-size:25px;color:#FFFFFF"></span><span class="text">&nbsp;&nbsp;&nbsp;Visita nuestra pagina en Instagram</span></a></li>

                            </ul>
                        </div>
                        <!--//connect-->
                        <!--Contacto-->
                        <br>
                        <div class="connect">
                            <h4 class="side">CONTACTO</h4>
                            <ul class="stay">
                                <li class="c5-element-whtp"><center><span class="fa fa-at" style="font-size:25px;color:#FFFFFF"></span><span class="text">&nbsp;&nbsp;&nbsp;Correo: superbowlstore@hotmail.com</span></center></li>
                            </ul>
                        </div>
                        <!--Contacto-->
                    </div>
                    <!--//soccer-right-part-->
                    <div class="clearfix"> </div>
                </div>
            </div>
            <!--//soccer-inner-->
        </div>
        <!--/start-footer-section-->
        <!--/start-copyright-section-->
        <div class="copyright">
            <p>&copy; 2017 TochoWeb-Tuxtla. All Rights Reserved | Design by <a href="http://w3layouts.com/">W3layouts</a> </p>
        </div>


        <!--start-smoth-scrolling-->
        <script type="text/javascript">
            $(document).ready(function () {
                /*
                 var defaults = {
                 containerID: 'toTop', // fading element id
                 containerHoverID: 'toTopHover', // fading element hover id
                 scrollSpeed: 1200,
                 easingType: 'linear'
                 };
                 */

                $().UItoTop({easingType: 'easeOutQuart'});

            });
        </script>
        <a href="#home" id="toTop" class="scroll" style="display: block;"> <span id="toTopHover" style="opacity: 1;"> </span></a>

        <script type="text/javascript" src="../controlador/JS_LIBRERIAS_bootstrap-3.1.1.min.js"></script>
    </body>
</html>
