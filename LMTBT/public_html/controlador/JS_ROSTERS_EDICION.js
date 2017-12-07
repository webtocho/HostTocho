var id_equipo;
var id_roster;
var posibles_jugadores = [];
var jugadores_elegidos = [];
//Las opciones que se hallan en cada fila de la tabla.
var opciones = "<button title=\"Descartar\" onclick=\"eliminarJugador(this)\">X</button>" +
               "<button title=\"Subir de posición\" onclick=\"mover_arriba(this)\">▲</button>" +
               "<button title=\"Bajar de posición\" onclick=\"mover_abajo(this)\">▼</button>";

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
                                        var celda_numero = fila.insertCell(0), celda_nombre = fila.insertCell(1), celda_correo = fila.insertCell(2), celda_opciones = fila.insertCell(3);
                                        celda_numero.innerHTML = i["NUMERO"];
                                        celda_nombre.innerHTML = i["APELLIDO_PATERNO"] + " " + i["APELLIDO_MATERNO"] + " " + i["NOMBRE"];
                                        celda_correo.innerHTML = i["CORREO"];
                                        celda_opciones.innerHTML = opciones;
                                        
                                        jugadores_elegidos.push( [i["ID_JUGADOR"], true] );
                                    });
                                }
                                
                                document.getElementById("busqueda_jugador").disabled = false;
                                document.getElementById("seleccion_jugador").disabled = false;
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
                categoria: $("#categoria").html()
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
                            if(i[0] == jugadores_elegidos[j][0]){
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

function agregarJugador(){
    var select = document.getElementById("seleccion_jugador");
    var tabla = document.getElementById("miembros");
    
    if(posibles_jugadores.length != 0 && (select.selectedIndex >= 0 && select.selectedIndex < posibles_jugadores.length) && posibles_jugadores.length == select.length){
        
        var fila = tabla.insertRow(-1); //"-1" indica que se inserte al final.
        fila.style = "color: green;";
        var celda_numero = fila.insertCell(0), celda_nombre = fila.insertCell(1), celda_correo = fila.insertCell(2), celda_opciones = fila.insertCell(3);
        celda_numero.innerHTML = "?";
        celda_nombre.innerHTML = posibles_jugadores[select.selectedIndex][2];
        celda_correo.innerHTML = posibles_jugadores[select.selectedIndex][1];
        celda_opciones.innerHTML = opciones;
        
        jugadores_elegidos.push( [posibles_jugadores[select.selectedIndex][0], null] );
        
        posibles_jugadores.splice(select.selectedIndex, 1);
        select.remove(select.selectedIndex);
        
        if(select.length == 0)
            $("#seleccion_jugador").html("<option value=\"-1\" selected=\"selected\">Sin resultados</option>");
        
        actualizarNum();
    }
}

function eliminarJugador(boton){
    var index = $(boton).closest('td').parent()[0].sectionRowIndex;
    if(jugadores_elegidos[index - 1][1] == null){
        jugadores_elegidos.splice(index - 1, 1);
        document.getElementById("miembros").deleteRow(index);
    } else if(jugadores_elegidos[index - 1][1]) {
        document.getElementById("miembros").rows[index].style = "color: lightgray;";
        boton.innerHTML = "↻";
        boton.title = "Restaurar";
        jugadores_elegidos[index - 1][1] = false;
    } else {
        document.getElementById("miembros").rows[index].style = "color: black;";
        boton.innerHTML = "X";
        boton.title = "Descartar";
        jugadores_elegidos[index - 1][1] = true;
    }
    actualizarNum();
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
        
        actualizarNum();
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
        
        actualizarNum();
    }
}

function actualizarNum(){
    var num = 0;
    
    if(jugadores_elegidos.length != (document.getElementById("miembros").rows.length - 1)){
        alert("Error: inconsistencia de los datos internos de la página.");
        document.location.href = "EQUIPOS_VER.html";
    }
    
    $.each(document.getElementById("miembros").rows , function( index, fila ) {
        if(index != 0){
            if(jugadores_elegidos[index - 1][1] == null){
                fila.cells[0].innerHTML = ++num;
            } else if (jugadores_elegidos[index - 1][1]){
                fila.cells[0].innerHTML = ++num;
            } else {
                fila.cells[0].innerHTML = "N/A";
            }
        }
    });
}

function guardar(){
    var seleccion = [];
    
    $.each(jugadores_elegidos, function( index, i ) {
        if(i[1] == null){
            seleccion.push(i[0]);
        } else if (i[1]){
            seleccion.push(i[0]);
        }
    });
    
    $.ajax({
        url: "../controlador/SRV_CONSULTAS.php",
        data: {
            tipo : "editar_roster",
            id_equipo : id_equipo,
            id_roster : id_roster,
            categoria: $("#categoria").html(),
            jugadores: JSON.stringify(seleccion)
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
                alert("Roster editado con éxito.");
                document.location.href = "EQUIPOS_VER.html";
            } else {
                alert(respuesta);
            }
        },
        complete: function (jqXHR, textStatus) {
            $("#contenido *").prop( "disabled", false );
        }
    });
}