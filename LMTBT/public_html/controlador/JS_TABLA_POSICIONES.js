function CargarTablaPosiciones(id_convocatoria) {
    //Función para cargar la tabla de posiciones
    $.ajax({
        url: "../controlador/SRV_TABLA_POSICIONES_OBTENER.php",
        data: {convocatoria: id_convocatoria},
        type: "POST",
        dataType: 'text',
        success: function (resultado) {
            if (resultado == 1) {
                alert("No se han encontrado datos o ha ocurrido un error\nIntente de nuevo mas tarde");
            } else {
                $('#Div_Estadisticas').empty();
                $('#Div_Estadisticas').append(resultado);
                $(function () {
                    $('#horizontalTab').easyResponsiveTabs({
                        type: 'default', //Types: default, vertical, accordion
                        width: 'auto', //auto or any width like 600px
                        fit: true   // 100% fit in a container
                    });
                });
            }
        }
    });

}

function CargarConvocatorias(estado) {
    //Función para cargar las convocatorias segun su estado
    $.ajax({
        url: "../controlador/SRV_CONVOCATORIAS_OBTENER_TABLA_POSICIONES.php",
        data: {tipo: estado},
        type: "POST",
        dataType: 'text',
        success: function (resultado) {
            if (resultado == 1) {
                $('#Select_Torneos').empty();
                $('#Select_Torneos').append("No hay torneos...");
            } else {
                $('#Select_Torneos').empty();
                $('#Select_Torneos').append(resultado);
            }
        }
    });
}

function ActualizarEstadisticas(id_convocatoria) {
    //Esta funcion actualiza los datos de las estadisticas
    $.ajax({
        url: "../controlador/SRV_TABLA_POSICIONES_ACTUALIZAR.php",
        data: {convocatoria: id_convocatoria},
        type: "POST",
        dataType: 'text',
        success: function (resultado) {
            //alert(resultado);
        }
    });
}