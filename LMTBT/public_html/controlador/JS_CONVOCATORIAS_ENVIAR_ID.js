/**
 * permite acceder a un objeto storage del lado del cliente para poder enviar datos entre paginas
 * especificamente la pagina de inicio comparte datos con la de DETALLES_CONVOCATORIA.html y reedirecciona
 * a la pagina anteriormente mencionada.
 * @param {string} id es el identificador de la convocatoria, para saber cual se ha seleccionado.
 */
function eviar_id_convocatoria(id){
    sessionStorage.setItem("id_convocatoria", id);
    location.href ="../vista/NOTICIAS_VER.html";
}
