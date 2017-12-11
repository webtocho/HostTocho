function CargarConvocatorias(estado){
	$.ajax({
		url: "../controlador/SRV_OBTENER_CONVOCATORIAS_TABLA_POSICIONES.php",
		data: {tipo : estado },
        type: "POST",
        dataType: 'text',
		 success: function(resultado){
		 	$('#Select_Torneos').empty();
		 	$('#Select_Torneos').append(resultado);
                        $('#Select_Equipos').empty();
                        $('#Estadisticas').empty();
		 },
		 error: function (jqXHR, textStatus) {
            alert("error obtener torneos");
         }
	});
}

function CargarEquipos(id_convocatoria){
	$.ajax({
		url: "../controlador/SRV_ESTADISTICAS_OBTENER_EQUIPOS.php",
		data: {convocatoria : id_convocatoria },
        type: "POST",
        dataType: 'text',
		 success: function(resultado){
		 	$('#Select_Equipos').empty();
		 	$('#Select_Equipos').append(resultado);
                        $('#Estadisticas').empty();
		 },
		 error: function (jqXHR, textStatus) {
            alert("error obtener equipos");
         }
	});
}

function CargarEstadisticas(id_roster){
	$.ajax({
		url: "../controlador/SRV_ESTADISTICAS_OBTENER_RESULTADOS.php",
		data: {roster : id_roster },
        type: "POST",
        dataType: 'text',
		 success: function(resultado){
		 	$('#Estadisticas').empty();
		 	$('#Estadisticas').append(resultado);
		 },
		 error: function (jqXHR, textStatus) {
            alert("error obtener Estadisticas");
         }
	});
}