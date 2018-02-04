$(document).ready(function() {
    
    $.post( "../controlador/SRV_GET_SESION.php", {tipos :["ADMINISTRADOR"]}, null, "text")
        .done(function(res) {
            switch(parseInt(res)){
                case 0:
                    var frame = document.getElementById("busqueda");
                    var onload = setInterval(function() {
                        var frameDoc = frame.contentDocument || frame.contentWindow.document;
                        if(frameDoc.readyState == 'complete' || frameDoc.readyState == 'interactive') {
                            clearInterval(onload);
                            frame.contentWindow.inicializar("TODOS", "Ver detalles", "irADetalles");
                            frame.contentWindow.cambiarCategoria(res["id_cat"]);
                        }
                    }, 500);
                    frame.src = 'CUENTAS_BUSQUEDA.html';
                    break;
                default:
                    $('#contenido').html("<div class='alert alert-danger'>\n\
                                  <strong>Error:</strong> No tiene permiso de acceder a esta página. Será redireccionado en unos segundos.\n\
                                  </div>");
                    setTimeout(function(){ expulsar(); }, 4000);
                    return;
            }
        })
        .fail(function() {
            expulsar();
        });
});

function irADetalles(idUsuario){
    sessionStorage.setItem("CUENTAS_DETALLES", idUsuario);
    var win = window.open("CUENTAS_DETALLES.html");
    win.focus();
}