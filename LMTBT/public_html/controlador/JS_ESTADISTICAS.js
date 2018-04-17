function CargarConvocatorias(estado) {
    //Funcion para cargar las convocatorias
    $.ajax({
        url: "../controlador/SRV_OBTENER_CONVOCATORIAS_TABLA_POSICIONES.php",
        data: {tipo: estado},
        type: "POST",
        dataType: 'text',
        success: function (resultado) {
            $('#Select_Torneos').empty();
            $('#Select_Torneos').append(resultado);
            $('#Select_Equipos').empty();
            $('#Estadisticas').empty();
            $('#PorPartidos').empty();
            $('#BotonGeneral').css("display", "none");
            $('#BotonGeneral').val(0);
            $('#BotonPartidos').css("display", "none");
            $('#BotonGeneral').val(0);
            $('#PorPartidos').css("display", "none");
        },
        error: function (jqXHR, textStatus) {
            alert("error obtener torneos");
        }
    });
}

function CargarEquipos(id_convocatoria) {
    //Funcion para cargar los rosters inscritos a una convocatoria
    $.ajax({
        url: "../controlador/SRV_ESTADISTICAS_OBTENER_EQUIPOS.php",
        data: {convocatoria: id_convocatoria},
        type: "POST",
        dataType: 'text',
        success: function (resultado) {
            $('#Select_Equipos').empty();
            $('#Select_Equipos').append(resultado);
            $('#Estadisticas').empty();
        },
        error: function (jqXHR, textStatus) {
            alert("error obtener equipos");
        }
    });
}

function CargarEstadisticas(id_roster) {
    //funcion para cargar las estadistcas de un roster
    $.ajax({
        url: "../controlador/SRV_ESTADISTICAS_OBTENER_RESULTADOS.php",
        data: {roster: id_roster},
        type: "POST",
        dataType: 'text',
        success: function (resultado) {
            $('#Estadisticas').empty();
            $('#Estadisticas').append(resultado);
            $('#PorPartidos').hide();
        },
        error: function (jqXHR, textStatus) {
            alert("error obtener Estadisticas");
        }
    });
}

function MostrarBotones(value) {
    //Funcion para mostrar botones para elegir por partido o general
    $('#BotonGeneral').val(value);
    $('#BotonGeneral').css("display", "block");
    $('#BotonPartidos').val(value);
    $('#BotonPartidos').css("display", "block");
    $('#Estadisticas').empty();
    $('#PorPartidos').hide();
}

function CargarPartidos(value) {
    //FUncion para cargar las estadisticas generales de un roster
    var convoc = $('#Select_Torneos').val();
    $.ajax({
        url: "../controlador/SRV_ESTADISTICAS_OBTENER_PARTIDOS.php",
        data: {roster: value,
            convocatoria: convoc},
        type: "POST",
        dataType: 'text',
        success: function (resultado) {
            $('#PorPartidos').empty();
            $('#PorPartidos').append(resultado);
            $('#PorPartidos').show();
            $('#Estadisticas').empty();
        },
        error: function (jqXHR, textStatus) {
            alert("error obtener Estadisticas");
        }
    });

}

function ResultadoPartido(value) {
    //Funcion para cargar las estadisticas de un roster (de un solo partido)
    var ros = $('#BotonPartidos').val();
    $.ajax({
        url: "../controlador/SRV_ESTADISTICAS_OBTENER_RESULTADOS_PARTIDO.php",
        data: {rol: value,
            roster: ros},
        type: "POST",
        dataType: 'text',
        success: function (resultado) {
            $('#Estadisticas').empty();
            $('#Estadisticas').append(resultado);
        },
        error: function (jqXHR, textStatus) {
            alert("error obtener Estadisticas");
        }
    });
}