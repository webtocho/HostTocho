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
        <link href="css/owl.carousel.css" rel="stylesheet" type="text/css" media="all" />
        <link href="css/style.css" rel='stylesheet' type='text/css' />
        <script type="text/javascript" src="../controlador/JS_LIBRERIAS_jquery.min.js"></script>
        <script type="text/javascript" src="../controlador/JS_PLANTILLA_move-top.js"></script>
        <script type="text/javascript" src="../controlador/JS_PLANTILLA_easing.js"></script>
        <script type="text/javascript" src="../controlador/JS_FELICITACIONES.js"></script>
       <script type="text/javascript" src="../controlador/JS_VALIDAR_ACCESO_USUARIO.js"></script>
       <script type="text/javascript" src="../controlador/JS_CONVOCATORIAS_VENCIDAS.js"></script>
       <script type="text/javascript" src="../controlador/JS_REGISTRO_NOTICIAS.js"></script>
       <script type="text/javascript" src="../controlador/JS_CREAR_ROL_JUEGOS.js"></script>
       <script type='text/javascript' src='../controlador/JS_TORNEOS_ACTIVOS.js'></script>
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
                            <a class="navbar-brand" href="index.php"><h1>TochoWEB <span>Tuxtla</span></h1> </a>
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
                                <li><a href="ESTADISTICAS.html">Estadisticas</a></li>
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
                               <li>
                                   <a href='REGISTRO_COACH.html'>Registrar coach</a>
                               </li>
                               <li id ="acceso_edicion_equipos"></li>
                               <li>
                                   <a href='ROLES_JUEGO.html'>Roles de juego</a>
                               </li>                               
                               <li id = "acceso_registrar_jugador"></li>
                                <li id = "acceso_convocatoria"></li>
                                <li id = "acceso_torneo_coach"></li>
                                <li id = "acceso_crear_noticias"></li>
                                 <li id = "acceso_asignacion_horarios"></li>
                                 <li id = "acceso_perfiles_usuarios"></li>
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
                                    <p>Lorem ipsum dolor sit amet</p>
                                </div>
                            </li>
                            <li class="" style="display: block; float: none; position: absolute; opacity: 0; z-index: 1; transition: opacity 500ms ease-in-out;">
                                <div class="banner-info">
                                    <h3>CONVIERTETE EN EL MEJOR JUGADOR DEL TORNEO</h3>
                                    <p>Lorem ipsum dolor sit amet</p>
                                </div>
                            </li>
                            <li class="" style="display: block; float: none; position: absolute; opacity: 0; z-index: 1; transition: opacity 500ms ease-in-out;">
                                <div class="banner-info">
                                    <h3>SIGUE HASTA LA VICTORIA</h3>
                                    <p>Lorem ipsum dolor sit amet</p>
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
                                        <li class="resp-tab-item grid3" aria-controls="tab_item-1" role="tab"><span>TABLA DE LA LIGA</span></li>
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
                                        <div class="tab-1 resp-tab-content" aria-labelledby="tab_item-2">
                                            <div class="facts">
                                                <div class="tab_list">
                                                    <table>
                                                        <thead>
                                                            <tr>
                                                                <th>Team</th>
                                                                <th>W</th>
                                                                <th>D</th>
                                                                <th>L</th>
                                                                <th>Point</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>1.Juventus</td>
                                                                <td>1</td>
                                                                <td>3</td>
                                                                <td>5</td>
                                                                <td>9</td>
                                                            </tr>
                                                            <tr>
                                                                <td>3. Atlanta</td>
                                                                <td>0</td>
                                                                <td>1</td>
                                                                <td>4</td>
                                                                <td>6</td>
                                                            </tr>
                                                            <tr>
                                                                <td>3. Juventus</td>
                                                                <td>7</td>
                                                                <td>6</td>
                                                                <td>4</td>
                                                                <td>7</td>
                                                            </tr>
                                                            <tr>
                                                                <td>4. Pro Soccer</td>
                                                                <td>12</td>
                                                                <td>7</td>
                                                                <td>9</td>
                                                                <td>20</td>
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
                        <!--/players-->
                        <div class="players">
                            <h3 class="tittle">Nuestros Jugadores</h3>
                            <ul id="flexiselDemo3">
                                <li>
                                    <div class="biseller-column">
                                        <a class="lightbox" href="#one">
                                            <img src="images/s1.jpg" alt=""/>
                                        </a>
                                        <div class="lightbox-target" id="one">
                                            <img src="images/s1.jpg" alt=""/>
                                            <a class="lightbox-close" href="#"> </a>

                                            <div class="clearfix"> </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="biseller-column">
                                        <a class="lightbox" href="#two">
                                            <img src="images/s3.jpg" alt=""/>
                                        </a>
                                        <div class="lightbox-target" id="two">
                                            <img src="images/s3.jpg" alt=""/>
                                            <a class="lightbox-close" href="#"> </a>

                                            <div class="clearfix"> </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="biseller-column">
                                        <a class="lightbox" href="#four">
                                            <img src="images/s2.jpg" alt=""/>
                                        </a>
                                        <div class="lightbox-target" id="four">
                                            <img src="images/s2.jpg" alt=""/>
                                            <a class="lightbox-close" href="#"> </a>

                                            <div class="clearfix"> </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="biseller-column">
                                        <a class="lightbox" href="#five">
                                            <img src="images/s1.jpg" alt=""/>
                                        </a>
                                        <div class="lightbox-target" id="five">
                                            <img src="images/s1.jpg" alt=""/>
                                            <a class="lightbox-close" href="#"> </a>

                                            <div class="clearfix"> </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="biseller-column">
                                        <a class="lightbox" href="#six">
                                            <img src="images/s2.jpg" alt=""/>
                                        </a>
                                        <div class="lightbox-target" id="six">
                                            <img src="images/s2.jpg" alt=""/>
                                            <a class="lightbox-close" href="#"> </a>

                                            <div class="clearfix"> </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="biseller-column">
                                        <a class="lightbox" href="#seven">
                                            <img src="images/s1.jpg" alt=""/>
                                        </a>
                                        <div class="lightbox-target" id="seven">
                                            <img src="images/s1.jpg" alt=""/>
                                            <a class="lightbox-close" href="#"> </a>

                                            <div class="clearfix"> </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="biseller-column">
                                        <a class="lightbox" href="#eight">
                                            <img src="images/s4.jpg" alt=""/>
                                        </a>
                                        <div class="lightbox-target" id="eight">
                                            <img src="images/s4.jpg" alt=""/>
                                            <a class="lightbox-close" href="#"> </a>

                                            <div class="clearfix"> </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <!--//players-->
                        <script type="text/javascript">
                            $(window).load(function () {
                                $("#flexiselDemo3").flexisel({
                                    visibleItems: 3,
                                    animationSpeed: 1000,
                                    autoPlay: true,
                                    autoPlaySpeed: 3000,
                                    pauseOnHover: true,
                                    enableResponsiveBreakpoints: true,
                                    responsiveBreakpoints: {
                                        portrait: {
                                            changePoint: 480,
                                            visibleItems: 3
                                        },
                                        landscape: {
                                            changePoint: 640,
                                            visibleItems: 3
                                        },
                                        tablet: {
                                            changePoint: 768,
                                            visibleItems: 3
                                        }
                                    }
                                });

                            });
                        </script>
                        <script type="text/javascript" src="../controlador/JS_PLANTILLA_jquery.flexisel.js"></script>
                        <!--//players-->
                        <!--/video-->
                        <div class="video">
                            <h3 class="tittle">Ultimo Video</h3>
                            <iframe src="https://player.vimeo.com/video/75045253?color=ff9933&title=0&byline=0&portrait=0"></iframe>
                        </div>
                        <!--//video-->
                        <div class="banner-slider">
                            <h3 class="tittle">Nuestras Noticias</h3>
                            <div class="callbacks_container">
                                <ul class="rslides" id="slider3" style="height: 471px;overflow: auto">
                                    <li>
                                        <div class="blog-img">
                                            <img src="images/2pp.jpg" class="img-responsive" alt="" />
                                        </div>
                                        <div class="blog-info">
                                            <a class="news" href="#"> Halcones gana el torneo 2017</a>
                                            <p>Despues de mucho esfuerzo y una gran jordana: lo consiguieron. </p>
                                            <div class="bog_post_info infoPost">
                                                <span class="datePost"><a href="#" class="post_date">Sep 30, 2017</a></span>
                                                <span class="commentPost"><a class="icon-comment-1" title="Comments - 2" href="#"><i class="glyphicon glyphicon-comment"></i>2</a></span>
                                                <span class="likePost"><i class="glyphicon glyphicon-heart"></i><a class="icon-heart" title="Likes - 4" href="#">4</a></span>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="blog-img">
                                            <img src="images/4.jpg" class="img-responsive" alt="" />
                                        </div>
                                        <div class="blog-info">
                                            <a class="news" href="#"> Se retira el jugador que hizo campeon a "Los Tigres"</a>
                                            <p>Despues de 3 campeonatos,39 partidos y una lesion, Martin dejara de jugar. </p>
                                            <div class="bog_post_info infoPost">
                                                <span class="datePost"><a href="#" class="post_date">Sep 30, 2017</a></span>
                                                <span class="commentPost"><a class="icon-comment-1" title="Comments - 2" href="#"><i class="glyphicon glyphicon-comment"></i>2</a></span>
                                                <span class="likePost"><i class="glyphicon glyphicon-heart"></i><a class="icon-heart" title="Likes - 4" href="#">4</a></span>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                    </li>                                    
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
                        <div id="apartado_convocatorias_vencidas">                           
                        </div>
                        <!--Apartado para las convocatorias que ya se han vencido-->
                        <!--Apartado para llistar los torneos activos-->
                        <div id="apartado_torneos_activos">
                        </div>                         
                        <!--Apartado para listar los torneos activos-->
                        <div class="modern">
                            <h4 class="side">Nuevos Jugadores</h4>
                            <div id="example1">
                                <div id="owl-demo" class="owl-carousel text-center">
                                    <div class="item">

                                        <img class="img-responsive lot" src="images/p2.jpeg" alt=""/>
                                    </div>
                                    <div class="item">

                                        <img class="img-responsive lot" src="images/p1.jpeg" alt=""/>
                                    </div>
                                    <div class="item">

                                        <img class="img-responsive lot" src="images/p2.jpeg" alt=""/>
                                    </div>
                                    <div class="item">

                                        <img class="img-responsive lot" src="images/p1.jpeg" alt=""/>
                                    </div>
                                    <div class="item">

                                        <img class="img-responsive lot" src="images/p2.jpeg" alt=""/>
                                    </div>
                                    <div class="item">

                                        <img class="img-responsive lot" src="images/p1.jpeg" alt=""/>
                                    </div>
                                    <div class="item">

                                        <img class="img-responsive lot" src="images/p2.jpeg" alt=""/>
                                    </div>
                                    <div class="item">

                                        <img class="img-responsive lot" src="images/p1.jpeg" alt=""/>
                                    </div>
                                </div>
                            </div>
                            <!-- requried-jsfiles-for owl -->
                            <script src="../controlador/JS_PLANTILLA_owl.carousel.js"></script>
                            <script>
                            $(document).ready(function () {
                                $("#owl-demo").owlCarousel({
                                    items: 1,
                                    lazyLoad: true,
                                    autoPlay: false,
                                    navigation: true,
                                    navigationText: true,
                                    pagination: false,
                                    responsiveBreakpoints: {
                                        portrait: {
                                            changePoint: 480,
                                            visibleItems: 2
                                        },
                                        landscape: {
                                            changePoint: 640,
                                            visibleItems: 2
                                        },
                                        tablet: {
                                            changePoint: 768,
                                            visibleItems: 3
                                        }
                                    }
                                });
                            });
                            </script>
                            <!-- //requried-jsfiles-for owl -->
                        </div>
                        <!--//accordation_menu-->
                        <div class="list_vertical">
                            <section class="accordation_menu">
                                <div>
                                    <input id="label-1" name="lida" type="radio" checked="">
                                    <label for="label-1" id="item1"><i class="ferme"> </i>Noticias Populares<i class="icon-plus-sign i-right1"></i><i class="icon-minus-sign i-right2"></i></label>
                                    <div class="content" id="a1">
                                        <div class="scrollbar" id="style-2">
                                            <div class="force-overflow">
                                                <div class="popular-post-grids">
                                                    <div class="popular-post-grid">
                                                        <div class="post-img">
                                                            <a href="single.html"><img src="images/f1.jpg" alt=""></a>
                                                        </div>
                                                        <div class="post-text">
                                                            <a class="pp-title" href="single.html"> Jaime anota 5 puntos y gana el campeonato</a>
                                                            <p>08 Nov <a class="span_link" href="#"><span class="glyphicon glyphicon-comment"></span>3 </a><a class="span_link" href="#"><span class="glyphicon glyphicon-eye-open"></span>56 </a></p>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                    <div class="popular-post-grid">
                                                        <div class="post-img">
                                                            <a href="single.html"><img src="images/f2.jpg" alt=""></a>
                                                        </div>
                                                        <div class="post-text">
                                                            <a class="pp-title" href="single.html"> Francisco se desmalla en medio partido</a>
                                                            <p>08 Nov <a class="span_link" href="#"><span class="glyphicon glyphicon-comment"></span>2 </a><a class="span_link" href="#"><span class="glyphicon glyphicon-eye-open"></span>56 </a></p>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                    <div class="popular-post-grid">
                                                        <div class="post-img">
                                                            <a href="single.html"><img src="images/f3.jpg" alt=""></a>
                                                        </div>
                                                        <div class="post-text">
                                                            <a class="pp-title" href="single.html">Julian se hace presente en semifinal femenil</a>
                                                            <p>07 Nov <a class="span_link" href="#"><span class="glyphicon glyphicon-comment"></span>0 </a><a class="span_link" href="#"><span class="glyphicon glyphicon-eye-open"></span>56 </a></p>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                    <div class="popular-post-grid">
                                                        <div class="post-img">
                                                            <a href="single.html"><img src="images/f4.jpg" alt=""></a>
                                                        </div>
                                                        <div class="post-text">
                                                            <a class="pp-title" href="single.html">Ser o no ser: el dilema del jugador mas solicitado</a>
                                                            <p>09 Nov<a class="span_link" href="#"><span class="glyphicon glyphicon-comment"></span>1 </a><a class="span_link" href="#"><span class="glyphicon glyphicon-eye-open"></span>56 </a></p>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <input id="label-2" name="lida" type="radio">
                                    <label for="label-2" id="item2"><i class="icon-leaf" id="i2"></i>Noticias Recientes<i class="icon-plus-sign i-right1"></i><i class="icon-minus-sign i-right2"></i></label>
                                    <div class="content" id="a2">
                                        <div class="scrollbar" id="style-2">
                                            <div class="force-overflow">
                                                <div class="popular-post-grids">
                                                    <div class="popular-post-grid">
                                                        <div class="post-img">
                                                            <a href="single.html"><img src="images/f4.jpg" alt=""></a>
                                                        </div>
                                                        <div class="post-text">
                                                            <a class="pp-title" href="single.html"> Jaime anota 5 puntos y gana el campeonato</a>
                                                            <p>08 Nov <a class="span_link" href="#"><span class="glyphicon glyphicon-comment"></span>3 </a><a class="span_link" href="#"><span class="glyphicon glyphicon-eye-open"></span>56 </a></p>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                    <div class="popular-post-grid">
                                                        <div class="post-img">
                                                            <a href="single.html"><img src="images/f3.jpg" alt=""></a>
                                                        </div>
                                                        <div class="post-text">
                                                            <a class="pp-title" href="single.html"> Francisco se desmalla en medio partido</a>
                                                            <p>08 Nov <a class="span_link" href="#"><span class="glyphicon glyphicon-comment"></span>2 </a><a class="span_link" href="#"><span class="glyphicon glyphicon-eye-open"></span>56 </a></p>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                    <div class="popular-post-grid">
                                                        <div class="post-img">
                                                            <a href="single.html"><img src="images/f1.jpg" alt=""></a>
                                                        </div>
                                                        <div class="post-text">
                                                            <a class="pp-title" href="single.html">Julian se hace presente en semifinal femenil</a>
                                                            <p>08 Nov <a class="span_link" href="#"><span class="glyphicon glyphicon-comment"></span>0 </a><a class="span_link" href="#"><span class="glyphicon glyphicon-eye-open"></span>56 </a></p>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                    <div class="popular-post-grid">
                                                        <div class="post-img">
                                                            <a href="single.html"><img src="images/f2.jpg" alt=""></a>
                                                        </div>
                                                        <div class="post-text">
                                                            <a class="pp-title" href="single.html">Ser o no ser: el dilema del jugador mas solicitado</a>
                                                            <p>O9 Nov <a class="span_link" href="#"><span class="glyphicon glyphicon-comment"></span>1 </a><a class="span_link" href="#"><span class="glyphicon glyphicon-eye-open"></span>56 </a></p>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <input id="label-3" name="lida" type="radio">
                                    <label for="label-3" id="item3"><i class="icon-trophy" id="i3"></i>Comentarios<i class="icon-plus-sign i-right1"></i><i class="icon-minus-sign i-right2"></i></label>
                                    <div class="content" id="a3">
                                        <div class="scrollbar" id="style-2">
                                            <div class="force-overflow">
                                                <div class="response">
                                                    <div class="media response-info">
                                                        <div class="media-left response-text-left">
                                                            <a href="#">
                                                                <img class="media-object" src="img/RC_IF_ICONS_1.png" alt="">
                                                            </a>
                                                            <h5><a href="#">Charlie</a></h5>
                                                        </div>
                                                        <div class="media-body response-text-right">
                                                            <p>Esta super interesante el torneo, nunca antes me habia interesado por algun deporte pero ahora ya me es importante seguir los partidos. Gracias por esta pagina! Saludos XD.</p>
                                                            <ul>
                                                                <li>Noviembre 08, 2017</li>
                                                                <li><a href="single.html">Reply</a></li>
                                                            </ul>
                                                        </div>
                                                        <div class="clearfix"> </div>
                                                    </div>
                                                    <div class="media response-info">
                                                        <div class="media-left response-text-left">
                                                            <a href="#">
                                                                <img class="media-object" src="img/RC_IF_ICONS_1.png" alt="">
                                                            </a>
                                                            <h5><a href="#">User</a></h5>
                                                        </div>
                                                        <div class="media-body response-text-right">
                                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit,There are many variations of passages of Lorem Ipsum available,
                                                                sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                                                            <ul>
                                                                <li>MARCH 26, 2015</li>
                                                                <li><a href="single.html">Reply</a></li>
                                                            </ul>
                                                        </div>
                                                        <div class="clearfix"> </div>
                                                    </div>
                                                    <div class="media response-info">
                                                        <div class="media-left response-text-left">
                                                            <a href="#">
                                                                <img class="media-object" src="img/RC_IF_ICONS_1.png" alt="">
                                                            </a>
                                                            <h5><a href="#">User</a></h5>
                                                        </div>
                                                        <div class="media-body response-text-right">
                                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit,There are many variations of passages of Lorem Ipsum available,
                                                                sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                                                            <ul>
                                                                <li>MAY 25, 2015</li>
                                                                <li><a href="single.html">Reply</a></li>
                                                            </ul>
                                                        </div>
                                                        <div class="clearfix"> </div>
                                                    </div>
                                                    <div class="media response-info">
                                                        <div class="media-left response-text-left">
                                                            <a href="#">
                                                                <img class="media-object" src="img/RC_IF_ICONS_1.png" alt="">
                                                            </a>
                                                            <h5><a href="#">User</a></h5>
                                                        </div>
                                                        <div class="media-body response-text-right">
                                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit,There are many variations of passages of Lorem Ipsum available,
                                                                sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                                                            <ul>
                                                                <li>FEB 13, 2015</li>
                                                                <li><a href="single.html">Reply</a></li>
                                                            </ul>
                                                        </div>
                                                        <div class="clearfix"> </div>
                                                    </div>
                                                    <div class="media response-info">
                                                        <div class="media-left response-text-left">
                                                            <a href="#">
                                                                <img class="media-object" src="img/RC_IF_ICONS_1.png" alt="">
                                                            </a>
                                                            <h5><a href="#">User</a></h5>
                                                        </div>
                                                        <div class="media-body response-text-right">
                                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit,There are many variations of passages of Lorem Ipsum available,
                                                                sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                                                            <ul>
                                                                <li>JAN 28, 2015</li>
                                                                <li><a href="single.html">Reply</a></li>
                                                            </ul>
                                                        </div>
                                                        <div class="clearfix"> </div>
                                                    </div>
                                                    <div class="media response-info">
                                                        <div class="media-left response-text-left">
                                                            <a href="#">
                                                                <img class="media-object" src="img/RC_IF_ICONS_1.png" alt="">
                                                            </a>
                                                            <h5><a href="#">User</a></h5>
                                                        </div>
                                                        <div class="media-body response-text-right">
                                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit,There are many variations of passages of Lorem Ipsum available,
                                                                sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                                                            <ul>
                                                                <li>APR 18, 2015</li>
                                                                <li><a href="single.html">Reply</a></li>
                                                            </ul>
                                                        </div>
                                                        <div class="clearfix"> </div>
                                                    </div>
                                                    <div class="media response-info">
                                                        <div class="media-left response-text-left">
                                                            <a href="#">
                                                                <img class="media-object" src="img/RC_IF_ICONS_1.png" alt="">
                                                            </a>
                                                            <h5><a href="#">User</a></h5>
                                                        </div>
                                                        <div class="media-body response-text-right">
                                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit,There are many variations of passages of Lorem Ipsum available,
                                                                sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                                                            <ul>
                                                                <li>DEC 25, 2014</li>
                                                                <li><a href="single.html">Reply</a></li>
                                                            </ul>
                                                        </div>
                                                        <div class="clearfix"> </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <!--Esta es la seccion para felicitar a los cumpleañeros-->
                                    <input id="label-4" name="lida" type="radio">
                                    <label for="label-4" id="item4"><i class="icon-trophy" id="i4"></i>Cumpleañeros<i class="icon-plus-sign i-right1"></i><i class="icon-minus-sign i-right2"></i></label>
                                    <div class="content" id="a4">
                                        <div class="scrollbar" id="style-2">
                                            <div class="force-overflow">
                                                <div class="response">
                                                    <div class="media response-info">
                                                        <div class="media-left response-text-left">
                                                            <a href="#">
                                                                <img class="media-object" src="img/CUMPLE_ICON.png" alt="" style="max-width: 50px; max-height: 50px" >
                                                            </a> 
                                                        </div>
                                                        <div id="Div_Felicitaciones" class="media-body response-text-left"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                        <!--//accordation_menu-->
                        <!--/top-news-->
                        <div class="top-news">
                            <h4 class="side">Top PARTIDOS</h4>
                            <div class="top-inner">
                                <div class="top-text">
                                            <!-- <a href="single.html"><img src="images/side.jpg" class="img-responsive" alt=""/></a> -->
                                    <h5 class="top"><a href="single.html">El partido del mes: Aguilas vs Halcones/a></h5>
                                    <p>01 Nov <a class="span_link" href="#"><span class="glyphicon glyphicon-comment"></span>0 </a><a class="span_link" href="#"><span class="glyphicon glyphicon-eye-open"></span>56 </a></p>
                                </div>
                                <div class="top-text two">
                                       <!-- <a href="single.html"><img src="images/side2.jpg" class="img-responsive" alt=""/></a> -->
                                    <h5 class="top"><a href="single.html">Clasico de clasico: IDS vs BIOMEDICA</a></h5>
                                    <p>12 Nov <a class="span_link" href="#"><span class="glyphicon glyphicon-comment"></span>0 </a><a class="span_link" href="#"><span class="glyphicon glyphicon-eye-open"></span>56 </a></p>

                                </div>
                            </div>
                        </div>
                        <!--//top-news-->
                        <div class="connect">
                            <h4 class="side">PERMANECE CONECTADO</h4>
                            <ul class="stay">
                                <li class="c5-element-facebook"><a href="#"><span class="icon"></span><h5>700</h5><span class="text">Seguidores</span></a></li>
                                <li class="c5-element-twitter"><a href="#"><span class="icon1"></span><h5>201</h5><span class="text">Seguidores</span></a></li>
                                <li class="c5-element-gg"><a href="#"><span class="icon2"></span><h5>111</h5><span class="text">Seguidores</span></a></li>

                            </ul>
                        </div>
                        <!--//connect-->
                    </div>
                    <!--//soccer-right-part-->
                    <div class="clearfix"> </div>
                </div>
                <div class="time-bg">
                    <h4>IDS <span>Vs </span> BIOMEDICA</h4>
                    <p id="demo"></p>

                    <script>
                        var myVar = setInterval(function () {
                            myTimer()
                        }, 1000);

                        function myTimer() {
                            var d = new Date();
                            document.getElementById("demo").innerHTML = d.toLocaleTimeString();
                        }
                    </script>
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
