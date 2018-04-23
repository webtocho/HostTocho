/**
 * Cargamos todo lo que el usuario puede ver en el inicio, dependiendo
 * del tipo de usuario con el que cuente podra ver ciertas cosas
 */
$(document).ready(function(){    
    //Llamamos a la funcion ejecutar_recuperar_noticias(); que nos recuperara todas la noticias
    //las cuales pueden ver todos los tipos de usuario incluyendo la sesion de invitado
    ejecutar_recuperar_noticias();
    //Realizamos la peticion al servidor para comprobar que tipo de usuario se encuentra logueado
    $.post( "../controlador/SRV_SESION_GET.php", {tipos :["ADMINISTRADOR","COACH","JUGADOR","FOTOGRAFO","CAPTURISTA"]}, null, "text")
        .done(function(res){
            switch(parseInt(res)){
                //En caso de que el usuario sea un administrador
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
                    $('#acceso_asignacion_horarios').append("<a href='HORARIOS_ASIGNACION.html'>Horarios</a>");
                    $('#acceso_categorias_edicion').append("<a href='CATEGORIAS_EDICION.html'>Ver categorías</a>");                    
                    $('#acceso_convocatoria').append("<a href='CONVOCATORIA.html'>Nuevo torneo</a>");
                    $('#acceso_roster_publico').append("<a href='ROSTERS_PUBLICO.html'>Equipos de la liga</a>");
                    $('#acceso_cuentas_detalles').append("<a href='CUENTAS_DETALLES.html'>Mi perfil</a>");
                    $('#acceso_equipos_ver').append("<a href='EQUIPOS_VER.html'>Gestión de equipos</a>");
                    $('#acceso_estadisticas').append("<a href='ESTADISTICAS.html'>Estadísticas</a>");
                    $('#acceso_crear_noticias').append("<a href='NOTICIAS.html'>Nueva noticia</a>");
                    $('#acceso_registro_cuenta').append("<a href='CUENTAS_REGISTRO.html'>Registrar cuenta</a>");
                    $('#acceso_roles_juego').append("<a href='ROLES_JUEGO.html'>Roles de juego</a>");
                    $('#gestion_cuentas').append("<a href='CUENTAS_GESTION.html'>Gestión de cuentas</a>");  
                    
                    mostrar_apartado_convocatorias_vencidas();
                    mostrar_apartado_torneos_activos();
                    cargar_tabla_convocatorias_vencidas();
                    cargar_tabla_torneos_activos();
                break;
                //En caso de que el usuario sea un coach
                case 1:  
                    ///////vaciamos los contenedores
                    $('#gestion_cuentas').empty();
                    $('#acceso_asignacion_horarios').empty();                                                                           
                    $('#acceso_roster_publico').empty();                      
                    $('#acceso_cuentas_detalles').empty();  
                    $('#acceso_equipos_ver').empty();
                    $('#acceso_estadisticas').empty();                                                       
                    $('#acceso_registro_cuenta').empty();
                    $('#acceso_roles_juego').empty();
                    $('#acceso_torneo_inscripcion').empty();                      
                    ///////Luego cargamos los contenedores con los apartados que debe ver este usuario                   
                    $('#acceso_asignacion_horarios').append("<a href='HORARIOS_ASIGNACION.html'>Horarios</a>");                    
                    $('#acceso_roster_publico').append("<a href='ROSTERS_PUBLICO.html'>Equipos de la liga</a>");
                    $('#acceso_cuentas_detalles').append("<a href='CUENTAS_DETALLES.html'>Perfil</a>");
                    $('#acceso_equipos_ver').append("<a href='EQUIPOS_VER.html'>Mis equipos</a>");                    
                    $('#acceso_estadisticas').append("<a href='ESTADISTICAS.html'>Estadísticas</a>");                                     
                    $('#acceso_registro_cuenta').append("<a href='CUENTAS_REGISTRO.html'>Registrar jugador</a>");                    
                    $('#acceso_torneo_inscripcion').append("<a href='TORNEO_INSCRIPCION.html'>Inscripción torneo</a>");                     
                break;
                //En caso de que el usuario sea un jugador
                case 2: 
                    ///////vaciamos los contenedores
                    $('#gestion_cuentas').empty();
                    $('#acceso_asignacion_horarios').empty();                                                                           
                    $('#acceso_roster_publico').empty();                      
                    $('#acceso_cuentas_detalles').empty();                      
                    $('#acceso_estadisticas').empty();                                                       
                    $('#acceso_roles_juego').empty(); 
                    ///////Luego cargamos los contenedores con los apartados que debe ver este usuario
                    $('#acceso_asignacion_horarios').append("<a href='HORARIOS_ASIGNACION.html'>Horarios</a>");                    
                    $('#acceso_roster_publico').append("<a href='ROSTERS_PUBLICO.html'>Equipos de la liga</a>");
                    $('#acceso_cuentas_detalles').append("<a href='CUENTAS_DETALLES.html'>Perfil</a>");                                   
                    $('#acceso_estadisticas').append("<a href='ESTADISTICAS.html'>Estadísticas</a>");                                                                 
                break;
                //En caso de que el usuario sea un fotografo
                case 3: 
                    ///////vaciamos los contenedores
                    $('#gestion_cuentas').empty();
                    $('#acceso_asignacion_horarios').empty();
                    $('#acceso_roster_publico').empty();                                                               
                    $('#acceso_estadisticas').empty();
                    $('#acceso_crear_noticias').empty();
                    ///////Luego cargamos los contenedores con los apartados que debe ver este usuario
                    $('#acceso_asignacion_horarios').append("<a href='HORARIOS_ASIGNACION.html'>Horarios</a>");    
                    $('#acceso_roster_publico').append("<a href='ROSTERS_PUBLICO.html'>Equipos de la liga</a>");                                      
                    $('#acceso_estadisticas').append("<a href='ESTADISTICAS.html'>Estadísticas</a>");
                    $('#acceso_crear_noticias').append("<a href='NOTICIAS.html'>Nueva noticia</a>");                   
                break;
                //En caso de que el usuario sea un capturista
                case 4:
                    ///////vaciamos los contenedores
                    $('#gestion_cuentas').empty();
                    $('#acceso_asignacion_horarios').empty();                                                                           
                    $('#acceso_roster_publico').empty();                      
                    $('#acceso_cuentas_detalles').empty();                      
                    $('#acceso_estadisticas').empty();                                                       
                    $('#acceso_roles_juego').empty();     
                    ///////Luego cargamos los contenedores con los apartados que debe ver este usuario
                    $('#acceso_asignacion_horarios').append("<a href='HORARIOS_ASIGNACION.html'>Horarios</a>");                    
                    $('#acceso_roster_publico').append("<a href='ROSTERS_PUBLICO.html'>Equipos de la liga</a>");
                    $('#acceso_cuentas_detalles').append("<a href='CUENTAS_DETALLES.html'>Perfil</a>");                                   
                    $('#acceso_estadisticas').append("<a href='ESTADISTICAS.html'>Estadísticas</a>");                                     
                    $('#acceso_roles_juego').append("<a href='ROLES_JUEGO.html'>Roles de juego</a>");
                break;
                //En caso de que no tenga ninguna cuenta registrada
                default:
                    ///////vaciamos los contenedores
                    $('#gestion_cuentas').empty();
                     $('#acceso_roster_publico').empty();          
                    $('#acceso_asignacion_horarios').empty();                                                                                                                  
                    $('#acceso_estadisticas').empty();                                                       
                    $('#acceso_roles_juego').empty();
                    $('#acceso_registro_cuenta').empty();
                    ///////Luego cargamos los contenedores con los apartados que debe ver este usuario
                    $('#acceso_roster_publico').append("<a href='ROSTERS_PUBLICO.html'>Equipos de la liga</a>");
                    $('#acceso_asignacion_horarios').append("<a href='HORARIOS_ASIGNACION.html'>Horarios</a>");                                                                     
                    $('#acceso_estadisticas').append("<a href='ESTADISTICAS.html'>Estadísticas</a>");                                                       
                    $('#acceso_registro_cuenta').append("<a href='CUENTAS_REGISTRO.html'>Registrarse</a>");
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
             $('#acceso_roster_publico').append("<a href='ROSTERS_PUBLICO.html'>Equipos de la liga</a>");
            $('#acceso_asignacion_horarios').append("<a href='HORARIOS_ASIGNACION.html'>Horarios</a>");                                                                     
            $('#acceso_estadisticas').append("<a href='ESTADISTICAS.html'>Estadísticas</a>");                                                
            $('#acceso_registro_cuenta').append("<a href='CUENTAS_REGISTRO.html'>Registrarse</a>");            
    });    
});
/**
 * Carga el apartado y la tabla en donde se visualizaran las convocatorias lanzadas al inicio
 */
function mostrar_apartado_convocatorias_vencidas(){
    $('#apartado_convocatorias_lanzadas').append("<div class='top-news'>"+"<h4 class='side'>Convocatorias lanzadas</h4><div style='height: 140px;overflow: auto' id='convocatorias_lanzadas'><table class='table table-bordered table-hover'>"+
    "<thead><tr><th>Nombre torneo</th><th>Acciones</th></tr></thead><tbody id='contenidoTabla'></tbody></table></div></div>");
}
/**
 * Carga el apartado y la tabla en donde se visualizaran los torneos activos al inicio
 */
function mostrar_apartado_torneos_activos(){
    $('#apartado_torneos_activos').append("<div class='top-news'>"+"<h4 class='side'>Torneos activos</h4><div style='height: 140px;overflow: auto' id='torneos_activos'><table class='table table-bordered table-hover'>"+
    "<thead><tr><th>Nombre torneo</th><th>Acciones</th></tr></thead><tbody id='contenido_tabla_torneos'></tbody></table></div></div>");
}
