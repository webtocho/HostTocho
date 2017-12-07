
function CargarEstadisticas(id_convocatoria){
	$.ajax({
		url:"../controlador/SRV_OBTENER_ESTADISTICAS.php",
		data: {convocatoria :id_convocatoria },
        type: "POST",
        dataType: 'text',
        success: function (resultado) {
            if(resultado==1) {
                alert("No se han encontrado datos o ha ocurrido un error\nIntente de nuevo mas tarde");
            }
            else  { 
                $('#Div_Estadisticas').empty();
                $('#Div_Estadisticas').append(resultado);
            }
        }
	});

}

function CargarConvocatorias(estado){
	$.ajax({
	url: "../controlador/SRV_OBTENER_CONVOCATORIAS_ESTADISTICAS.php",
	data: {tipo : estado },
        type: "POST",
        dataType: 'text',
		 success: function(resultado){
		 	if(resultado==1){
		 		$('#Select_Torneos').empty();
		 		$('#Select_Torneos').append("No hay torneos...");
		 	}
		 	else{
		 		$('#Select_Torneos').empty();
		 		$('#Select_Torneos').append(resultado);
		 	}
		 }
	});
}

function ActualizarEstadisticas(id_convocatoria){
	$.ajax({
		 url: "../controlador/SRV_ACTUALIZAR_ESTADISTICAS.php",
		 data: {convocatoria :id_convocatoria },
         type: "POST",
         dataType: 'text',
		 success: function(resultado){
		 	//alert("Se han actualizado las estadisticas");
		 	alert(resultado);
		 }
	});
}