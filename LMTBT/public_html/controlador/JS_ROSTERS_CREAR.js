var id_equipo;
var categoria = null;
var posibles_jugadores = [];
var jugadores_elegidos = [];

//Recibe una cadena y la devuelve de tal forma que tenga la primera letra mayúscula, y el resto sean minúsculas.
function capitalize(string) {
    return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}

$( document ).ready(function() {
    id_equipo = sessionStorage.getItem("id_equipo");
    var nombre_equipo = sessionStorage.getItem("nombre_equipo");
    
    try{
        sessionStorage.removeItem("id_equipo");
        sessionStorage.removeItem("nombre_equipo");
    } catch (i){}
    
    if(id_equipo === null || nombre_equipo === null){
        document.location.href = "EQUIPOS_VER.html";
        return;
    } else{
        $("#titulo").append(nombre_equipo);
    }
    
    //Al momento de cargar la página debemos confirmar que es esté accediento sea un COACH o un ADMIN.
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
                            tipo : "get_categorias_disponibles",
                            id_equipo : id_equipo
                        },
                        type: "POST",
                        dataType: 'json',
                        async: true,
                        success: function (categorias) {                            
                            if(categorias.hasOwnProperty('error')){
                                alert(categorias["error"]);
                                document.location.href = "EQUIPOS_VER.html";
                            } else {
                                var select = document.getElementById("seleccion_categoria");
                                $.each(categorias , function(index, i) {
                                    var option = document.createElement("option");
                                    option.value = i;
                                    option.text = capitalize(i);
                                    select.add(option);
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
    });
});

function elegirCategoria(){
    var select = document.getElementById("seleccion_categoria");
    if(categoria == null){
        select.remove(0);
        document.getElementById("busqueda_jugador").disabled = false;
        document.getElementById("seleccion_jugador").disabled = false;
    }
    
    document.getElementById("busqueda_jugador").value = "";
    $("#seleccion_jugador").html("<option value=\"-1\" selected=\"selected\">Sin resultados</option>");
    categoria = select.options[select.selectedIndex].value;
    //Esta línea elimina todo el contenido de la tabla, excepto la cabecera.
    $("#miembros").find("tr:gt(0)").remove();
    
    jugadores_elegidos = [];
    posibles_jugadores = [];
};

function agregarJugador(){
    var select = document.getElementById("seleccion_jugador");
    var tabla = document.getElementById("miembros");
    
    if(posibles_jugadores.length != 0 && (select.selectedIndex >= 0 && select.selectedIndex < posibles_jugadores.length) && posibles_jugadores.length == select.length){
        
        var fila = tabla.insertRow(-1); //"-1" indica que se inserte al final.
        var celda_nombre = fila.insertCell(0), celda_correo = fila.insertCell(1), celda_eliminar = fila.insertCell(2);
        celda_nombre.innerHTML = posibles_jugadores[select.selectedIndex][2];
        celda_correo.innerHTML = posibles_jugadores[select.selectedIndex][1];
        celda_eliminar.innerHTML = "<button title=\"Descartar\" onclick=\"eliminarJugador(this)\">X</button>" +
                                   "<button title=\"Subir de posición\" onclick=\"mover_arriba(this)\">▲</button>" +
                                   "<button title=\"Bajar de posición\" onclick=\"mover_abajo(this)\">▼</button>";
        
        jugadores_elegidos.push( posibles_jugadores[select.selectedIndex][0] );
        
        posibles_jugadores.splice(select.selectedIndex, 1);
        select.remove(select.selectedIndex);
        
        if(select.length == 0)
            $("#seleccion_jugador").html("<option value=\"-1\" selected=\"selected\">Sin resultados</option>");
    }
}

function eliminarJugador(boton){
    var index = $(boton).closest('td').parent()[0].sectionRowIndex;
    
    jugadores_elegidos.splice(index - 1, 1);
    document.getElementById("miembros").deleteRow(index);
}

function buscarJugadores(){
    var apellido = $.trim(document.getElementById("busqueda_jugador").value);
    document.getElementById("busqueda_jugador").value = apellido;
    
    if(apellido.length > 2 && categoria != null){
        var opciones = "<option value=\"-1\" selected=\"selected\">Sin resultados</option>";
        
        $.ajax({
            url: "../controlador/SRV_CONSULTAS.php",
            data: {
                tipo : "buscar_jugadores",
                criterio : apellido,
                categoria: categoria
            },
            type: "POST",
            dataType: 'json',
            async: true,
            success: function (jugadores) {
                if(Object.keys(jugadores).length > 0){
                    opciones = "";
                    posibles_jugadores = [];
                    var el_jugador_ya_esta_en_roster;
                    
                    $.each(jugadores , function( index, i ) {
                        
                        el_jugador_ya_esta_en_roster = false;
                        for (var j = 0; j < jugadores_elegidos.length; j++) {
                            if(i[0] == jugadores_elegidos[j]){
                                el_jugador_ya_esta_en_roster = true;
                                break;
                            }
                        }
                        
                        if(!el_jugador_ya_esta_en_roster){
                            posibles_jugadores.push(i);
                            opciones += "<option value=\"" + i[0] + "\">" + i[2] + "</option>";
                        }
                    });
                    
                    if(posibles_jugadores.length == 0)
                        opciones = "<option value=\"-1\" selected=\"selected\">Sin resultados</option>";
                }
            },
            complete: function (jqXHR, textStatus) {
                $("#seleccion_jugador").html(opciones);
            }
        });
    } else {
        $("#seleccion_jugador").html("<option value=\"-1\" selected=\"selected\">Sin resultados</option>");
    }
}

function crear(){
    //PENDIENTE: Saber cuántos jugadores como máximo y como mínimo debe tener el roster.
    if(categoria == null){
        alert("No ha seleccionado una categoría.");
        return;
    }
    
    $.ajax({
        url: "../controlador/SRV_CONSULTAS.php",
        data: {
            tipo : "crear_roster",
            id_equipo : id_equipo,
            categoria: categoria,
            jugadores: JSON.stringify(jugadores_elegidos)
        },
        type: "POST",
        dataType: 'text',
        async: false,
        success: function (respuesta) {
            if(respuesta == "ok"){
                //PENDIENTE
                alert("Roster creado con éxito.");
                document.location.href = "EQUIPOS_VER.html";
            } else {
                alert(respuesta);
            }
        },
        complete: function (jqXHR, textStatus) {
            
        }
    });
}

function mover_arriba(boton){
    var index = $(boton).closest('td').parent()[0].sectionRowIndex;
    
    if(index > 1){
        var row = $(boton).closest('tr');
        row.prev().before(row);
        
        index--;
        
        var aux = jugadores_elegidos[index];
        jugadores_elegidos[index] = jugadores_elegidos[index - 1];
        jugadores_elegidos[index - 1] = aux;
    }
}

function mover_abajo(boton){
    var index = $(boton).closest('td').parent()[0].sectionRowIndex;
    
    if(index > 0 && index < (document.getElementById("miembros").rows.length - 1)){
        var row = $(boton).closest('tr');
        row.next().after(row);
        
        index--;
        
        var aux = jugadores_elegidos[index];
        jugadores_elegidos[index] = jugadores_elegidos[index + 1];
        jugadores_elegidos[index + 1] = aux;
    }
}