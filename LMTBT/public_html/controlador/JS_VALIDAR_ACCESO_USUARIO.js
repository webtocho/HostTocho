/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
document.write("<script type='text/javascript' src='../controlador/JS_CONVOCATORIAS_VENCIDAS.js'></script>");
document.write("<script type='text/javascript' src='../controlador/JS_TORNEOS_ACTIVOS.js'></script>");
document.write("<script type='text/javascript' src='../controlador/JS_REGISTRO_NOTICIAS.js'></script>");
$(document).ready(function(){   
    acceso_crear_noticias();
    acceso_asignacion_horarios();
    iniciar_cerrar_session();
    acceso_registrar_jugador();
    acceso_convocatoria();
    acceso_torneo_coach();    
    ejecutar_recuperar_noticias();
});
function iniciar_cerrar_session() {    
            $.ajax({
                        url: "../controlador/SRV_LOGIN_LOGOUT.php",
                        data: {"tipo": "iniciar_cerrar_session"},
                        type: "POST",
                        datatype: "text",
                        beforeSend: function (xhr) {
                        },
                        success: function (respuesta) {
                                if (respuesta === "ok") {
                                            $('#iniciar_cerrar_session').empty();
                                            $('#acceso_perfiles_usuarios').empty();
                                            $('#iniciar_cerrar_session').append("<a onclick='cerrarSesion()'> Cerrar sesion </a>");
                                            $('#acceso_perfiles_usuarios').append("<a href='CUENTAS_DETALLES.html'>Perfil</a>");
                                } else {
                                            $('#iniciar_cerrar_session').empty();
                                            $('#iniciar_cerrar_session').append("<a href='CUENTAS_LOGIN.html'> Ingresar </a>");                                            
                                }
                        },
                        error: function (jqXHR, textStatus) {
                        }
            });
}

function acceso_registrar_jugador() {
            $.ajax({
                        url: "../controlador/SRV_CONSULTAS.php",
                        data: {"tipo": "acceso_registrar_jugador"},
                        type: "POST",
                        datatype: "text",
                        beforeSend: function (xhr) {
                        },
                        success: function (respuesta) {
                                if (respuesta === "ok") {
                                            $('#acceso_registrar_jugador').empty();
                                            $('#acceso_edicion_equipos').empty();
                                            $('#acceso_registrar_jugador').append("<a href='REGISTRAR_JUGADOR.html'>Registrar Jugador</a>");
                                            $('#acceso_edicion_equipos').append("<a href='EQUIPOS_EDICION.html'>Edicion equipos</a>");
                                            boton_registrar_jugador();
                                } else {
                                            $('#iniciar_cerrar_session').empty();
                                            $('#iniciar_cerrar_session').append("<a href='CUENTAS_LOGIN.html'> Ingresar </a>");
                                }
                        },
                        error: function (jqXHR, textStatus) {
                        }
            });
}
function acceso_convocatoria() {
            $.ajax({
                        url: "../controlador/SRV_CONSULTAS.php",
                        data: {"tipo": "acceso_convocatoria"},
                        type: "POST",
                        datatype: "text",
                        beforeSend: function (xhr) {
                        },
                        success: function (respuesta) {
                                if (respuesta === "ok") {
                                            $('#acceso_convocatoria').empty();
                                            $('#acceso_convocatoria').append("<a href='CONVOCATORIA.html'>Convocatoria</a>");
                                            mostrar_apartado_convocatorias_vencidas();
                                            mostrar_apartado_torneos_activos();
                                            acceso_select_registrar_jugador();                                            
                                            cargar_tabla_convocatorias_vencidas();
                                            cargar_tabla_torneos_activos();
                                }
                        },
                        error: function (jqXHR, textStatus) {
                        }
            });
}
function acceso_torneo_coach() {
            $.ajax({
                        url: "../controlador/SRV_CONSULTAS.php",
                        data: {"tipo": "acceso_torneo_coach"},
                        type: "POST",
                        datatype: "text",
                        beforeSend: function (xhr) {
                        },
                        success: function (respuesta) {
                                if (respuesta === "ok") {
                                            $('#acceso_convocatoria').empty();
                                            $('#acceso_convocatoria').append("<a href='TORNEO_INSCRIPCION.html'>Inscripcion Torneo</a>");
                                }
                        },
                        error: function (jqXHR, textStatus) {
                        }
            });
}
function acceso_select_registrar_jugador() {
            $.ajax({
                        url: "../controlador/SRV_CONSULTAS.php",
                        data: {"tipo": "acceso_select_registrar_jugador"},
                        type: "POST",
                        datatype: "text",
                        beforeSend: function (xhr) {
                        },
                        success: function (respuesta) {
                                if (respuesta === "ok") {
                                            $('#acceso_select_registrar_jugador').empty();
                                            $('#acceso_select_registrar_jugador').append("<select  class='browser-default' id='lista_coach'></select>");
                                            lista_coach();
                                }
                        },
                        error: function (jqXHR, textStatus) {
                        }
            });
}
function lista_coach(){
            $.ajax({
                        url: "../controlador/SRV_CONSULTAS.php",
                        data: {"tipo": "lista_coach"},
                        type: "POST",
                        datatype: "text",
                        beforeSend: function(xhr) {},
                        success: function(respuesta) {
                                //console.log(respuesta);
                                var jugadores = JSON.parse(respuesta || {});
                                var listaJugadores = "<option value=''>Coach Registrador</option>";
                                jQuery.each(jugadores, function(i, val) {
                                        listaJugadores += "<option value='" + val.ID_USUARIO + "'>" + val.NOMBRE + " " + val.APELLIDO_PATERNO + " " + val.APELLIDO_MATERNO + "</option>";
                                });
                                $('#lista_coach').html(listaJugadores);                                
                        },
                        error: function(jqXHR, textStatus) {}
            });
}
function mostrar_apartado_convocatorias_vencidas(){
    $('#apartado_convocatorias_lanzadas').append("<div class='top-news'>"+"<h4 class='side'>Convocatorias lanzadas</h4><div style='height: 140px;overflow: auto'><table class='table table-bordered table-hover'>"+
    "<thead><tr><th>Nombre torneo</th><th>Fecha cierre</th><th>Que desea hacer</th></tr></thead><tbody id='contenidoTabla'></tbody></table></div></div>");
}
function mostrar_apartado_torneos_activos(){
    $('#apartado_torneos_activos').append("<div class='top-news'>"+"<h4 class='side'>Torneos activos</h4><div style='height: 140px;overflow: auto'><table class='table table-bordered table-hover'>"+
    "<thead><tr><th>Nombre torneo</th><th>Acciones</th></tr></thead><tbody id='contenido_tabla_torneos'></tbody></table></div></div>");
}
function boton_registrar_jugador(){
           $.ajax({
                       url: "../controlador/SRV_CONSULTAS.php",
                       data: {"tipo": "validar_registro_jugador"},
                       type: "POST",
                       datatype: "text",
                       beforeSend: function (xhr) {
                       },
                       success: function (respuesta) {
                               if (respuesta === "ok") {
                                           $('#boton_registrar_jugador').empty();
                                           $('#boton_registrar_jugador').html("<center><button class='btn waves-effect waves-light' type='submit' name='action' onclick='administrador_registra_jugador()'>Guardar</button></center>");
                               }else if(respuesta === "no"){
                                           $('#boton_registrar_jugador').empty();
                                           $('#boton_registrar_jugador').html("<center><button class='btn waves-effect waves-light' type='submit' name='action' onclick='coach_registra_jugador()'>Guardar</button></center>");
                               }
                       },
                       error: function (jqXHR, textStatus) {
                       }
           });
}
function acceso_crear_noticias(){    
           $.ajax({
                       url: "../controlador/SRV_CONSULTAS.php",
                       data: {"tipo": "acceso_crear_noticias"},
                       type: "POST",
                       datatype: "text",
                       beforeSend: function (xhr) {
                       },
                       success: function (respuesta) {   
                           if(respuesta == "ok"){
                                    $('#acceso_crear_noticias').empty();
                                    $('#acceso_crear_noticias').append("<a href='NOTICIAS.html'>Crear noticias</a>");
                                }
                       },
                       error: function (jqXHR, textStatus) {
                           //alert("Erro al ejecutar");
                       }
           });
  
}

function acceso_asignacion_horarios(){    
           $.ajax({
                       url: "../controlador/SRV_CONSULTAS.php",
                       data: {"tipo": "acceso_asignacion_horarios"},
                       type: "POST",
                       datatype: "text",
                       beforeSend: function (xhr) {
                       },
                       success: function (respuesta) {   
                           if(respuesta == "ok"){
                                    $('#acceso_asignacion_horarios').empty();
                                    $('#acceso_asignacion_horarios').append("<a href='ASIGNACION_HORARIOS.html'>Asignar Horarios</a>");
                                }
                            else{
                                $('#acceso_asignacion_horarios').empty();
                                    $('#acceso_asignacion_horarios').append("<a href='ASIGNACION_HORARIOS.html'>Ver Horarios</a>");
                            }
                       },
                       error: function (jqXHR, textStatus) {
                           //alert("Erro al ejecutar");
                       }
           });
  
}
