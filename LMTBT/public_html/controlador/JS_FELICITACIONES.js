$(document).ready(function () {
    felicitaciones();
    NextGames();
});

function felicitaciones() {
    //Funcion para mostrar a los cumpleañeros del dia en la pagina principal
    $.ajax({
        url: "../controlador/SRV_FELICITACIONES_OBTENER.php",
        success: function (info) {
            if (info == 1) {
                $('#ApartadoCumpleanios').hide();
            } else {
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
                                visibleItems: 1
                            },
                            landscape: {
                                changePoint: 640,
                                visibleItems: 1
                            },
                            tablet: {
                                changePoint: 768,
                                visibleItems: 1
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

function NextGames() {
    //Funcion para cargar los siguientes 5 partidos (mas cercanos sin jugar aun) en la pagina principal
    $.ajax({
        url: "../controlador/SRV_PROXIMOS_PARTIDOS.php",
        success: function (res) {
            $('#ProximosPartidos').empty();
            $('#ProximosPartidos').append(res);
        },
        error: function (jqXHR, textStatus) {
            alert("Ha ocurrido un error al obtener la informacion solicitada.\n Intentelo de nuevo mas tarde");
        }
    });
}
