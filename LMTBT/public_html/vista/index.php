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
        <link href="css/bootstrap-3.1.1.min.css" rel="stylesheet" type="text/css">
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
    <body onload="felicitaciones()">
        <div id="alertaSucces" class="modal fade" role="dialog">            
        </div>
        <script>
        $(function () {
            $("#nueva_fecha").datepicker();
        });
        </script>
        <!--Ventana emergente para modificar la fecha de la convocatoria-->
        <div id="ventanaEmergente" class="modal fade" role="dialog">
            <div class="modal-dialog">
              <!--Contenido de la ventana emergente-->
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 id="tituloVentanaEmergente" class="modal-title">Titulo</h4>
                </div>
                <div class="modal-body">
                    <form class="form-group" id="formulario">
                        <label for="nueva_fecha">Nueva fecha:</label>
                        <input type="text" class="form-control" id="nueva_fecha" maxlength="30" required>
                    </form>
                </div>
                <div class="modal-footer">
                    <input type="submit" form="formulario" class="btn btn-default" value="Guardar">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
              </div>
            </div>
        </div>
        <!--Ventana emergente para modificar la fecha de la convocatoria-->
        
        
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
                            <a class="navbar-brand" href="index.php"><h1>LMTB <span>Tuxtla</span></h1> </a>
                        </div><br>
                        <!--/.navbar-header-->

                        <div class="collapse navbar-collapse pull-right" id="bs-example-navbar-collapse-1">
                            <ul class="nav navbar-nav">
                                <!--<li><a href="informacion.html">Informacion</a></li>
                                <!-- <li class="dropdown">
                                    <a href="calendario.html" class="dropdown-toggle" data-toggle="dropdown">Calendario<b class="caret"></b></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="calendario.html">Hoy</a></li>
                                        <li class="divider"></li>
                                        <li><a href="calendario.html">Semana</a></li>
                                        <li class="divider"></li>
                                        <li><a href="calendario.html">Mes</a></li>
                                        <li class="divider"></li>
                                    </ul>
                                </li>-->                                
                               <!-- <li class="dropdown">
                                    <a href="equipos.html" class="dropdown-toggle" data-toggle="dropdown">Equipos<b class="caret"></b></a>
                                    <ul class="dropdown-menu multi-column columns-2">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <ul class="multi-column-dropdown">
                                                    <li><a href="typo.html">Varonil</a></li>
                                                    <li class="divider"></li>
                                                    <li><a href="index.php">Femenil</a></li>
                                                    <li class="divider"></li>
                                                    <li><a href="blog.html">Mixto</a></li>
                                                    <li class="divider"></li>
                                                    <li><a href="typo.html">Otro</a></li>
                                                    <li class="divider"></li>
                                                    <li><a href="typo.html">Otro</a></li>
                                                </ul>
                                            </div>
                                            <!--<div class="col-sm-6">
                                                    <ul class="multi-column-dropdown">
                                                       <li><a href="#">Features</a></li>
                                                            <li class="divider"></li>
                                                            <li><a href="#"> Movies</a></li>
                                                        <li class="divider"></li>
                                                            <li><a href="#">Sports</a></li>
                                                            <li class="divider"></li>
                                                            <li><a href="#">Reviews</a></li>
                                                            <li class="divider"></li>
                                                            <li><a href="#">Account</a></li>
                                                    </ul>
                                            </div>
                                        </div>
                                    </ul>
                                </li>-->   
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
                                <li id = "acceso_registro_cuenta"></li>
                                
                                <li id = "acceso_roles_juego">
                                   <!--<a href='ROLES_JUEGO.html'>Roles de juego</a>-->
                                </li>    
                                <li id = "acceso_torneo_inscripcion"></li>                                
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
                                    <h3>LA LIGA DE LOS VERDADEROS JUGADORES</h3>
                                </div>
                            </li>
                            <li class="" style="display: block; float: none; position: absolute; opacity: 0; z-index: 1; transition: opacity 500ms ease-in-out;">
                                <div class="banner-info">
                                    <h3>CONVIERTETE EN EL MEJOR JUGADOR DEL TORNEO</h3>
                                </div>
                            </li>
                            <li class="" style="display: block; float: none; position: absolute; opacity: 0; z-index: 1; transition: opacity 500ms ease-in-out;">
                                <div class="banner-info">
                                    <h3>SIGUE HASTA LA VICTORIA</h3>
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
                                        <li class="resp-tab-item grid2" aria-controls="tab_item-1" role="tab"><span>HORARIO DE ENTRENAMIENTO</span></li>
                                    </ul>
                                    <div class="resp-tabs-container">
                                        <div class="tab-1 resp-tab-content" aria-labelledby="tab_item-0">
                                            <div class="facts">
                                                <div class="tab_list">
                                                    <table>
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
                                        <div class="tab-1 resp-tab-content" aria-labelledby="tab_item-1">
                                            <div class="facts">
                                                <div class="tab_list">
                                                    <table>
                                                        <tbody>
                                                            <tr>
                                                                <td class="two">Sunday 07:00 - 10:00</td>
                                                                <td class="two">Workout</td>

                                                            </tr>
                                                            <tr>
                                                                <td class="two">Sunday 14:00 - 18:00</td>
                                                                <td class="two">Aerobic</td>


                                                            </tr>
                                                            <tr>
                                                                <td class="two">Monday 07:00 - 10:00</td>
                                                                <td class="two">Swimming</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="two">Wednesday 07:00 - 10:00</td>
                                                                <td class="two">Traning Strategy</td>


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
                            <div class="facts">
                                <a><h3>Estado del Torneo</h3></a>
                                <center>
                                    <button onclick="CargarConvocatorias(1)">En curso</button>
                                    <button onclick="CargarConvocatorias(2)">Terminados</button>
                                    <button onclick="CargarConvocatorias(3)">Todos</button>
                                </center>
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
                        <!--//video-->
                        <div class="banner-slider">
                            <h3 class="tittle">Nuestras Noticias</h3>
                            <div class="callbacks_container">
                                <ul class="rslides" id="slider3" style="height: 471px;overflow: auto">                                   
                                </ul>
                            </div>
                        </div>
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
                        <div class="modern">
                            <h4 class="side">Cumpleañeros</h4>
                            <br>
                            <div id="example1">
                                <div id="owl-demo" class="owl-carousel text-center"></div>
                            </div>
                            <script src="../controlador/JS_PLANTILLA_owl.carousel.js"></script> 
                        </div>
                        <br>
                        <!--Apartado Cumpleaños:termina -->
                       
                        <div class="connect">
                            <h4 class="side">PERMANECE CONECTADO</h4>
                            <ul class="stay">
                                <li class="c5-element-facebook"><a href="https://www.facebook.com/Liga-Municipal-De-Tocho-Bandera-de-Tuxtla-472623376268881/"><span class="icon"></span><h5>700</h5><span class="text">Seguidores</span></a></li>
                                <li class="c5-element-twitter"><a href="#"><span class="icon1"></span><h5>201</h5><span class="text">Seguidores</span></a></li>
                                <li class="c5-element-gg"><a href="https://www.youtube.com/channel/UCyt2LwpS9czwaM7E0j9DI0Q/feed"><span class="fa fa-youtube""></span><h5>111</h5><span class="text">Seguidores</span></a></li>

                            </ul>
                        </div>
                        <!--//connect-->
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
