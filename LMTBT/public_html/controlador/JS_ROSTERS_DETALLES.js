var id_equipo;
var id_roster;

$( document ).ready(function() {
    //Desde EQUIPOS_VER se nos manda el id del equipo a consultar.
    id_equipo = sessionStorage.getItem("id_equipo");
    id_roster = sessionStorage.getItem("id_roster");
    var nombre_equipo = sessionStorage.getItem("nombre_equipo");
    
    try{
        sessionStorage.removeItem("id_equipo");
        sessionStorage.removeItem("id_roster");
        sessionStorage.removeItem("nombre_equipo");
    } catch (i){}
    
    if(id_equipo === null || id_roster == null || nombre_equipo === null){
        document.location.href = "EQUIPOS_VER.html";
        return;
    } else{
        $("#nombre_equipo").html(nombre_equipo);
    }
    
    $.ajax({
        url: "../controlador/SRV_GET_SESION.php",
        type: "POST",
        dataType: 'json',
        async: false,
        success: function (resultado) {
            if(resultado["id"] == null || resultado["tipo"] == null){
                //Si el usuario no ha iniciado sesión, lo redireccionamos.
                window.location.replace("index.php");
            } else if(resultado["id"] != null && resultado["tipo"] != null){
                if(resultado["tipo"].toUpperCase() == "COACH" || resultado["tipo"].toUpperCase() == "ADMINISTRADOR"){
                    $.ajax({
                        url: "../controlador/SRV_CONSULTAS.php",
                        data: {
                            tipo : "get_roster",
                            id_equipo : id_equipo,
                            id_roster : id_roster
                        },
                        type: "POST",
                        dataType: 'json',
                        async: true,
                        success: function (roster) {
                            if(roster.hasOwnProperty('error')){
                                alert(roster["error"]);
                                document.location.href = "EQUIPOS_VER.html";
                            } else {
                                $("#categoria").html(roster["CATEGORIA"]);
                                
                                if(Object.keys(roster["MIEMBROS"]).length > 0){
                                    $.each(roster["MIEMBROS"] , function( index, i ) {
                                        var fila = document.getElementById("miembros").insertRow(-1); //"-1" indica que se inserte al final.
                                        var celda_numero = fila.insertCell(0), celda_nombre = fila.insertCell(1), celda_correo = fila.insertCell(2);
                                        celda_numero.innerHTML = i["NUMERO"];
                                        celda_nombre.innerHTML = i["APELLIDO_PATERNO"] + " " + i["APELLIDO_MATERNO"] + " " + i["NOMBRE"];
                                        celda_correo.innerHTML = i["CORREO"];
                                    });
                                }
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
        }
    });
});

function eliminar(){
    if(!confirm("¿Está seguro que desea eliminar el roster completo?"))
        return;
    
    $.ajax({
        url: "../controlador/SRV_CONSULTAS.php",
        data: {
            tipo : "eliminar_roster",
            id_equipo : id_equipo,
            id_roster : id_roster,
            categoria : $("#categoria").html()
        },
        type: "POST",
        dataType: 'text',
        async: false,
        beforeSend: function (xhr) {
            $("#contenido *").prop( "disabled", true );
        },
        success: function (respuesta) {
            if(respuesta == "ok"){
                //PENDIENTE
                alert("Roster eliminado con éxito.");
                document.location.href = "EQUIPOS_VER.html";
            } else if (respuesta == "?"){
                if(confirm("Advertencia: Este roster está siendo partícipe de un torneo.\n¿Está realmente seguro de que desea eliminarlo?")){
                    $.ajax({
                        url: "../controlador/SRV_CONSULTAS.php",
                        data: {
                            tipo : "eliminar_roster",
                            id_equipo : id_equipo,
                            id_roster : id_roster,
                            categoria : $("#categoria").html(),
                            confirmacion : "1"
                        },
                        type: "POST",
                        dataType: 'text',
                        async: false,
                        success: function (respuesta) {
                            if(respuesta == "ok"){
                                //PENDIENTE
                                alert("Roster eliminado con éxito.");
                                document.location.href = "EQUIPOS_VER.html";
                            } else {
                                alert(respuesta);
                            }
                        }
                    });
                }
            } else {
                alert(respuesta);
            }
        },
        complete: function (jqXHR, textStatus) {
            $("#contenido *").prop( "disabled", false );
        }
    });
}

function irAPaginaDeEdicion(){
    sessionStorage.setItem("id_equipo", id_equipo);
    sessionStorage.setItem("nombre_equipo", $("#nombre_equipo").html());
    sessionStorage.setItem("id_roster", id_roster);
    document.location.href = "ROSTERS_EDICION.html";
}