$( document ).ready(function() {
    //Al momento de cargar la página debemos confirmar que es esté accediento sea un COACH o un ADMIN.
    $.ajax({
        url: "../controlador/SRV_GET_SESION.php",
        type: "POST",
        dataType: 'json',
        async: true,
        beforeSend: function (xhr) {
            
        },
        success: function (resultado) {
            if(resultado["id"] == null || resultado["tipo"] == null){
                //Si el usuario no ha iniciado sesión, lo redireccionamos.
                window.location.replace("index.php");
            } else if(resultado["id"] != null && resultado["tipo"] != null){
                if(resultado["tipo"].toUpperCase() == "COACH" || resultado["tipo"].toUpperCase() == "ADMINISTRADOR"){
                    $.ajax({
                        url: "../controlador/SRV_CONSULTAS.php",
                        data: {
                            tipo : "get_equipos",
                        },
                        type: "POST",
                        dataType: 'json',
                        async: true,
                        success: function (equipos) {
                            if(equipos.hasOwnProperty('error')){
                                alert(equipos["error"]);
                            } else {
                                $("#cargando").remove();
                                $.each(equipos , function( index, i ) {
                                    // En la posición 0 viene el ID; en la 1, el nombre; y en la 2, el logotipo. 
                                    $("#contenido").append("<div style=\"border: 2px solid #000000;\">" 
                                    + "<img src=\"data:image/png;base64," + i[2] +"\"/>"
                                    + i[1] + "<button onclick=\"irAVerDetalles(" + i[0] + ")\">Ver detalles</button>"
                                    + "</div>");
                                });
                            }
                        },
                        error: function (jqXHR, textStatus) {
                            alert("Se produjo un error (" + textStatus + "), inténtelo de nuevo.");
                        }
                    });
                } else {
                    $('#contenido').html("<h2>Su cuenta no tiene acceso a esta página.</h2>");
                }
            } else {
                console.log("SRV_GET_SESION.php no funciona como debería.");
            }
        },
        error: function (jqXHR, textStatus) {
            alert("Se produjo un error (" + textStatus + "), inténtelo de nuevo.");
        },
        complete: function (jqXHR, textStatus) {
            
        }
    });
});

function irAVerDetalles(id){
    //Este método permite guardar una variable de sesión del lado del cliente.
    sessionStorage.setItem("id_equipo", parseInt(id));
    document.location.href = "EQUIPOS_DETALLES.html";
}