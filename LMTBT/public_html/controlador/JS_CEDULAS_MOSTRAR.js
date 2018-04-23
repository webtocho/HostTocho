//fucion que guarda las variables recibidos del boton para crear un roster
function guardar_cedula(id_equipo_1, id_equipo_2, id_rol_juego, id_convocatoria) {
    //se utiliza sessionstorage para almacenar las variables y pasarlas a otro javascript
    sessionStorage.setItem("id_equipo_1", id_equipo_1);
    sessionStorage.setItem("id_equipo_2", id_equipo_2);
    sessionStorage.setItem("id_rol_juego", id_rol_juego);
    sessionStorage.setItem("id_convocatoria", id_convocatoria);
//redireccionamos a la pagina donde se utilizaran las variables que guarsaron los sessionstorage 
    location.href = "../vista/CEDULAS_FORMULARIO.html";
}