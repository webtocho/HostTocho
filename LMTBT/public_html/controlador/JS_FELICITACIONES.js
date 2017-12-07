function felicitaciones(){
	$.ajax({
        url: "../controlador/SRV_FELICITACIONES_OBTENER.php",
        success: function (info) {
     		if(info==1){
                    //hoy no hay cumpleañeros
     		}else{
                    $('#Div_Felicitaciones').empty();
                    $('#Div_Felicitaciones').append(info);
     		}
            
        },
        error: function (jqXHR, textStatus) {
            console.log("Error en el Servidor");
        }
    });
}