/* este metodo se encarga de ejecutar los metodos recuperar_baner y recuperar_noticias
 */
function ejecutar_recuperar_noticias() {
    recuperar_baner();
    recuperar_noticias(0);
}

/*obtenemos el banner que se muestra en el index, se obtiene la imagen de la noticia mas reciente
 * y los titulos de las 3 noticias mas recientes
 * si no existen mas de 3 noticias en la bd, se carga un banner predefinido y titulos predefinos
 */
function recuperar_baner() {
    $.ajax({
        url: "../controlador/SRV_BANNER_RECUPERAR.php",
        data: {},
        type: "POST",
        datatype: "text",
        beforeSend: function (xhr) {
            $('#titulo1').empty();
            $('#titulo2').empty();
            $('#titulo3').empty();
            $('.banner').css('background-image', 'url(../modelo/img/RC_IF_BANNER.gif)');
        },
        success: function (respuesta) {
            contenido = JSON.parse(respuesta);
            $('#titulo1').append(contenido[0]);
            $('#titulo2').append(contenido[1]);
            $('#titulo3').append(contenido[2]);
            $('.banner').css('background-image', 'url(' + contenido[3] + ')');
        },
        error: function (jqXHR, textStatus) {
        }
    });


}
/*
 * recibe el valor de la paginacion que se cliquea en la pagina y se obtienen las noticias
 * 
 */
function recuperar_noticias(linea) {
    var fila = parseInt(linea) * 5;
    $.ajax({
        url: "../controlador/SRV_NOTICIAS_RECUPERAR.php",
        data: {fila: fila},
        type: "POST",
        datatype: "text",
        beforeSend: function (xhr) {
            $('#apartadoNoticia').empty();
            $('#apartadoNoticia').append("<center><img src='../modelo/img/RC_IF_CARGANDO.gif' ></center>");
        },
        success: function (respuesta) {            
            $('#apartadoNoticia').empty();
            $('#apartadoNoticia').append(respuesta);

        },
        error: function (jqXHR, textStatus) {
        }
    });
}