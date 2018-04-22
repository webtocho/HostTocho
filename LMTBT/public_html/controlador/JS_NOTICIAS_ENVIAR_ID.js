/**
 * permite acceder a un objeto storage del lado del cliente para poder enviar datos entre paginas
 * especificamente la pagina de inicio comparte datos con la de NOTICIAS_VER.html y reedirecciona
 * a la pagina anteriormente mencionada.
 * @param {string} id es el identificador de la noticia, para saber cual se ha seleccionado.
 */
function enviarIdNoticia(id){
    sessionStorage.setItem("idNoticia", id);
    location.href ="../vista/NOTICIAS_VER.html";
}