
$(document).ready(function() {
    switch (comprobarSesion(["ADMINISTRADOR", "COACH"])) {
        case null:
            expulsar();
            return;
        case false:
            $('#contenido').html("<div class='alert alert-danger'>\n\
                                  <strong>Error:</strong> No tiene permiso de acceder a esta página. Será redireccionado en unos segundos.\n\
                                  </div>");
            setTimeout(function(){ expulsar(); }, 4000);
            return;
    }
    
    
});

function buscar(){
    $("#equipos").not(':first').remove();
    var tmp = $("#equipos").html();
    
    
}