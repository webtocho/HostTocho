function felicitaciones(){
	$.ajax({
        url: "../controlador/SRV_FELICITACIONES_OBTENER.php",
        success: function (info) {
     		if(info==1){
                    $('#owl-demo').empty();
                    $('#owl-demo').append("<p>Hoy no hay cumpleañeros</p>");
     		}else{
                    $('#owl-demo').empty();
                    $('#owl-demo').append(info);
                    $(function () {
                                $("#owl-demo").owlCarousel({
                                    items: 1,
                                    lazyLoad: true,
                                    autoPlay: false,
                                    navigation: true,
                                    navigationText: true,
                                    pagination: false,
                                    responsiveBreakpoints: {
                                        portrait: {
                                            changePoint: 480,
                                            visibleItems: 2
                                        },
                                        landscape: {
                                            changePoint: 640,
                                            visibleItems: 2
                                        },
                                        tablet: {
                                            changePoint: 768,
                                            visibleItems: 3
                                        }
                                    }
                                });
                            });
            }  
        },
        error: function (jqXHR, textStatus) {
           alert("Error del servidor. Cumpleaños");
        }
    });
}

