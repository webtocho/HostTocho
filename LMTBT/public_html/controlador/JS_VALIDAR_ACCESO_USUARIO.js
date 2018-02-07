/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function(){    
    ejecutar_recuperar_noticias();
    $.post( "../controlador/SRV_GET_SESION.php", {tipos :["ADMINISTRADOR","COACH","JUGADOR","FOTOGRAFO","CAPTURISTA"]}, null, "text")
        .done(function(res){
            switch(parseInt(res)){
                case 0:                    
                    ///////vaciamos los contenedores
                    $('#acceso_asignacion_horarios').empty();
                    $('#acceso_categorias_edicion').empty();                   
                    $('#acceso_convocatoria').empty();                        
                    $('#acceso_roster_publico').empty();                      
                    $('#acceso_cuentas_detalles').empty();  
                    $('#acceso_equipos_ver').empty();
                    $('#acceso_estadisticas').empty();
                    $('#acceso_crear_noticias').empty();                
                    $('#acceso_registro_cuenta').empty();
                    $('#acceso_roles_juego').empty();
                    $('#gestion_cuentas').empty();
                     
                    ///////Luego cargamos los contenedores con los apartados que debe ver este usuario
                    $('#acceso_asignacion_horarios').append("<a href='ASIGNACION_HORARIOS.html'>Horarios</a>");
                    $('#acceso_categorias_edicion').append("<a href='CATEGORIAS_EDICION.html'>Ver Categorias</a>");                    
                    $('#acceso_convocatoria').append("<a href='CONVOCATORIA.html'>Nuevo Torneo</a>");
                    $('#acceso_roster_publico').append("<a href='ROSTERS_PUBLICO.html'>Equipos de la Liga</a>");
                    $('#acceso_cuentas_detalles').append("<a href='CUENTAS_DETALLES.html'>Perfil</a>");
                    $('#acceso_equipos_ver').append("<a href='EQUIPOS_VER.html'>Ver Equipos</a>");
                    $('#acceso_estadisticas').append("<a href='ESTADISTICAS.html'>Estadisticas</a>");
                    $('#acceso_crear_noticias').append("<a href='NOTICIAS.html'>Nueva Noticia</a>");
                    $('#acceso_registro_cuenta').append("<a href='REGISTRO_CUENTA.html'>Registrar Cuenta</a>");
                    $('#acceso_roles_juego').append("<a href='ROLES_JUEGO.html'>Roles Juego</a>");
                    $('#gestion_cuentas').append("<a href='CUENTAS_GESTION.html'>Gestion Cuentas</a>");  
                    
                    mostrar_apartado_convocatorias_vencidas();
                    mostrar_apartado_torneos_activos();
                    cargar_tabla_convocatorias_vencidas();
                    cargar_tabla_torneos_activos();
                break;
                case 1:  
                    ///////vaciamos los contenedores
                    $('#gestion_cuentas').empty();
                    $('#acceso_asignacion_horarios').empty();                                                                           
                    $('#acceso_roster_publico').empty();                      
                    $('#acceso_cuentas_detalles').empty();  
                    $('#acceso_equipos_ver').empty();
                    $('#acceso_estadisticas').empty();                                   
                    //$('#acceso_registrar_jugador').empty();
                    $('#acceso_registro_cuenta').empty();
                    $('#acceso_roles_juego').empty();
                    $('#acceso_torneo_inscripcion').empty();                      
                    ///////Luego cargamos los contenedores con los apartados que debe ver este usuario                   
                    $('#acceso_asignacion_horarios').append("<a href='ASIGNACION_HORARIOS.html'>Horarios</a>");                    
                    $('#acceso_roster_publico').append("<a href='ROSTERS_PUBLICO.html'>Equipos de la Liga</a>");
                    $('#acceso_cuentas_detalles').append("<a href='CUENTAS_DETALLES.html'>Perfil</a>");
                    $('#acceso_equipos_ver').append("<a href='EQUIPOS_VER.html'>Mis Equipos</a>");                    
                    $('#acceso_estadisticas').append("<a href='ESTADISTICAS.html'>Estadisticas</a>");                  
                    //$('#acceso_registrar_jugador').append("<a href='REGISTRAR_JUGADOR.html'>Registrar jugador</a>");
                    $('#acceso_registro_cuenta').append("<a href='REGISTRO_CUENTA.html'>Registrar Jugador</a>");
                    //$('#acceso_roles_juego').append("<a href='ROLES_JUEGO.html'>Roles Juego</a>");   
                    $('#acceso_torneo_inscripcion').append("<a href='TORNEO_INSCRIPCION.html'>Inscripcion Torneo</a>");                     
                case 2: 
                    ///////vaciamos los contenedores
                    $('#gestion_cuentas').empty();
                    $('#acceso_asignacion_horarios').empty();                                                                           
                    $('#acceso_roster_publico').empty();                      
                    $('#acceso_cuentas_detalles').empty();                      
                    $('#acceso_estadisticas').empty();                                                       
                    $('#acceso_roles_juego').empty(); 
                    ///////Luego cargamos los contenedores con los apartados que debe ver este usuario
                    $('#acceso_asignacion_horarios').append("<a href='ASIGNACION_HORARIOS.html'>Horarios</a>");                    
                    $('#acceso_roster_publico').append("<a href='ROSTERS_PUBLICO.html'>Equipos de la Liga</a>");
                    $('#acceso_cuentas_detalles').append("<a href='CUENTAS_DETALLES.html'>Perfil</a>");                                   
                    $('#acceso_estadisticas').append("<a href='ESTADISTICAS.html'>Estadisticas</a>");                                     
                   // $('#acceso_roles_juego').append("<a href='ROLES_JUEGO.html'>Roles Juego</a>");                       
                break;
                case 3: 
                    ///////vaciamos los contenedores
                    $('#gestion_cuentas').empty();
                    $('#acceso_asignacion_horarios').empty();
                    $('#acceso_roster_publico').empty();                                                               
                    $('#acceso_estadisticas').empty();
                    $('#acceso_crear_noticias').empty();
                    ///////Luego cargamos los contenedores con los apartados que debe ver este usuario
                    $('#acceso_asignacion_horarios').append("<a href='ASIGNACION_HORARIOS.html'>Horarios</a>");    
                    $('#acceso_roster_publico').append("<a href='ROSTERS_PUBLICO.html'>Equipos de la Liga</a>");                                      
                    $('#acceso_estadisticas').append("<a href='ESTADISTICAS.html'>Estadisticas</a>");
                    $('#acceso_crear_noticias').append("<a href='NOTICIAS.html'>Nueva Noticia</a>");                   
                break;
                case 4:
                    ///////vaciamos los contenedores
                    $('#gestion_cuentas').empty();
                    $('#acceso_asignacion_horarios').empty();                                                                           
                    $('#acceso_roster_publico').empty();                      
                    $('#acceso_cuentas_detalles').empty();                      
                    $('#acceso_estadisticas').empty();                                                       
                    $('#acceso_roles_juego').empty();     
                    ///////Luego cargamos los contenedores con los apartados que debe ver este usuario
                    $('#acceso_asignacion_horarios').append("<a href='ASIGNACION_HORARIOS.html'>Horarios</a>");                    
                    $('#acceso_roster_publico').append("<a href='ROSTERS_PUBLICO.html'>Equipos de la Liga</a>");
                    $('#acceso_cuentas_detalles').append("<a href='CUENTAS_DETALLES.html'>Perfil</a>");                                   
                    $('#acceso_estadisticas').append("<a href='ESTADISTICAS.html'>Estadisticas</a>");                                     
                    $('#acceso_roles_juego').append("<a href='ROLES_JUEGO.html'>Roles Juego</a>");
                break;
                default:
                    ///////vaciamos los contenedores
                    $('#gestion_cuentas').empty();
                     $('#acceso_roster_publico').empty();          
                    $('#acceso_asignacion_horarios').empty();                                                                                                                  
                    $('#acceso_estadisticas').empty();                                                       
                    $('#acceso_roles_juego').empty();
                    $('#acceso_registro_cuenta').empty();
                    ///////Luego cargamos los contenedores con los apartados que debe ver este usuario
                      $('#acceso_roster_publico').append("<a href='ROSTERS_PUBLICO.html'>Equipos de la Liga</a>");
                    $('#acceso_asignacion_horarios').append("<a href='ASIGNACION_HORARIOS.html'>Horarios</a>");                                                                     
                    $('#acceso_estadisticas').append("<a href='ESTADISTICAS.html'>Estadisticas</a>");                                     
                   // $('#acceso_roles_juego').append("<a href='ROLES_JUEGO.html'>Roles juego</a>");
                    $('#acceso_registro_cuenta').append("<a href='REGISTRO_CUENTA.html'>Registrarse</a>");
                return;
            }
        })
        .fail(function() {
            ///////vaciamos los contenedores
            $('#gestion_cuentas').empty();
            $('#acceso_roster_publico').empty();          
            $('#acceso_asignacion_horarios').empty();                                                                                                                  
            $('#acceso_estadisticas').empty();                                                       
            $('#acceso_roles_juego').empty(); 
            $('#acceso_registro_cuenta').empty();
            ///////Luego cargamos los contenedores con los apartados que debe ver este usuario
             $('#acceso_roster_publico').append("<a href='ROSTERS_PUBLICO.html'>Equipos de la Liga</a>");
            $('#acceso_asignacion_horarios').append("<a href='ASIGNACION_HORARIOS.html'>Horarios</a>");                                                                     
            $('#acceso_estadisticas').append("<a href='ESTADISTICAS.html'>Estadisticas</a>");                                     
           // $('#acceso_roles_juego').append("<a href='ROLES_JUEGO.html'>Roles juego</a>");
            $('#acceso_registro_cuenta').append("<a href='REGISTRO_CUENTA.html'>Registrarse</a>");            
    });    
});
function mostrar_apartado_convocatorias_vencidas(){
    $('#apartado_convocatorias_lanzadas').append("<div class='top-news'>"+"<h4 class='side'>Convocatorias lanzadas</h4><div style='height: 140px;overflow: auto' id='convocatorias_lanzadas'><table class='table table-bordered table-hover'>"+
    "<thead><tr><th>Nombre torneo</th><th>Acciones</th></tr></thead><tbody id='contenidoTabla'></tbody></table></div></div>");
}
function mostrar_apartado_torneos_activos(){
    $('#apartado_torneos_activos').append("<div class='top-news'>"+"<h4 class='side'>Torneos activos</h4><div style='height: 140px;overflow: auto' id='torneos_activos'><table class='table table-bordered table-hover'>"+
    "<thead><tr><th>Nombre torneo</th><th>Acciones</th></tr></thead><tbody id='contenido_tabla_torneos'></tbody></table></div></div>");
}
