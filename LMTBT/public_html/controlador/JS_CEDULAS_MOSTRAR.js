//Función que guarda las variables recibidas del boton para crear un roster.
function guardar_cedula(id_equipo_1, id_equipo_2, id_rol_juego, id_convocatoria) {
    //Se utiliza sessionstorage para almacenar las variables y pasarlas a otra página.
    sessionStorage.setItem("id_equipo_1", id_equipo_1);
    sessionStorage.setItem("id_equipo_2", id_equipo_2);
    sessionStorage.setItem("id_rol_juego", id_rol_juego);
    sessionStorage.setItem("id_convocatoria", id_convocatoria);
    
    //Redireccionamos a la página donde se utilizarán las variables que guardaron en sessionstorage. 
    location.href = "../vista/CEDULAS_FORMULARIO.html";
}