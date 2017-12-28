/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function() {
      $.post( "../controlador/SRV_GET_SESION.php", {tipos :["ADMINISTRADOR"]}, null, "text")
        .done(function(res){
            switch(parseInt(res)){
                case 0:
                    detallesConvocatoria();                    
                break;                
                default:
                expulsar();
                return;
            }
        })
        .fail(function() {
            expulsar();
        });
});
function detallesConvocatoria(){
   var id = sessionStorage.getItem("id_convocatoria");
   $.ajax({
        url: "../controlador/SRV_CONSULTA_CONVOCATORIA.php",
        data:{
            tipo:"consulta_especifica",
            id:id
        },
        type: "POST",
        datatype: "text",        
        success: function(resultado){            
            $('#info_convocatoria').empty();
            $('#info_convocatoria').append(resultado);
            $('#enventos').empty();
            $('#enventos').append('<a href="#body" id="gototop" class="desing">Ir Arriba</a>'+
                '<a href="#body" class="desing" onclick="abrirPantallaParaEditarConsulta('+id+')">   Cambiar fechas</a>'+
                '<a href="#body" class="desing" onclick="CREAR_ROL_JUEGOS('+id+')">   Generar rol</a>'+
                '<style type="text/css">'+
                    '@media(max-width: 550px){'+
                        '.desing{'+
                        'width: 100%;'+
                        'background-color: gray;'+
                        'color: #ffffff;'+
                        'padding: 10px;}}</style>');
                llenarTablaEquipos(id);
        },
        error: function(jqXHR, textStatus) {
           //alert("Error de ajax");
        }
    });
}
function llenarTablaEquipos(id){
    $.ajax({
        url: "../controlador/SRV_CONSULTA_CONVOCATORIA.php",
        data:{
            tipo:"recuperar_equipos_inscritos",
            id:id
        },
        type: "POST",
        datatype: "text",        
        success: function(resultado){            
            $('#contenidoTabla').empty();
            $('#contenidoTabla').append(resultado);
        },
        error: function(jqXHR, textStatus) {
           //alert("Error de ajax");
        }
    });
}
function abrirPantallaParaEditarConsulta(id){
    $('#tituloVentanaEmergente').empty();
    $('#tituloVentanaEmergente').append("Modificar fecha de cierre");   
    document.getElementById("formulario").onsubmit = function(){
        editarFecha(id);
        return false;
    };
    //Mostramos la ventana emergente
    $('#ventanaEmergente').modal();
}
function editarFecha(id){
    $('#ventanaEmergente').modal('hide');
    var nueva_fecha = document.getElementById("nueva_fecha").value;
    $.ajax({
        url: "../controlador/SRV_CONSULTA_CONVOCATORIA.php",
        data:{
            tipo:"modificar",
            nueva_fecha:nueva_fecha,
            id:id
        },
        type: "POST",
        datatype: "text",
        beforeSend: function(xhr){        
            $('#alertaSucces').empty();
            $('#alertaSucces').append('<center><img src="images/cargando_naranja.gif" alt="Flowers in Chania"></center>');
        },
        success: function(resultado) {
            if(resultado == "ok"){                               
                mostrarAlerta("Cambio realizado con exito","correcto");                
            }else{                            
                mostrarAlerta(resultado,"fallido"); 
            }       
        },
        error: function(jqXHR, textStatus) {           
           mostrarAlerta("Ha ocurrido un error al conectarse con el servidor. Intentelo de nuevo mas tarde.","fallido");
        }
    });
}
function abrir_pantalla_para_poner_pago(id){
    document.getElementById("botonConfirmacion").onclick = function(){
        poner_pagado(id);
    };
    $('#ventanaConfirmacion').modal();
}
function poner_pagado(id){      
    $('#ventanaConfirmacion').modal('hide');
    $.ajax({
        url: "../controlador/SRV_CONSULTA_CONVOCATORIA.php",
        data:{
            tipo:"poner_pagado",           
            id:id
        },
        type: "POST",
        datatype: "text",
        beforeSend: function(xhr){        
            $('#alertaSucces').empty();
            $('#alertaSucces').append('<center><img src="images/cargando_naranja.gif" alt="Flowers in Chania"></center>');
        },
        success: function(resultado) {
            if(resultado == "ok"){                               
                mostrarAlerta("Cambio realizado con exito","correcto");                 
                $('#eventos').empty();
                $('#eventos').append("<a class='news' href='#body'><h5>PAGADO</h5></a>");
            }else{                            
                mostrarAlerta(resultado,"fallido"); 
            }       
        },
        error: function(jqXHR, textStatus) {           
           mostrarAlerta("Ha ocurrido un error al conectarse con el servidor. Intentelo de nuevo mas tarde.","fallido");
        }
    });
}
function abrir_pantalla_para_expulsar(id){
    document.getElementById("botonConfirmacion").onclick = function(){
        expulsar(id);
    };
    $('#ventanaConfirmacion').modal();
}
function expulsar(id){      
    $('#ventanaConfirmacion').modal('hide');
    $.ajax({
        url: "../controlador/SRV_CONSULTA_CONVOCATORIA.php",
        data:{
            tipo:"expulsar",           
            id:id
        },
        type: "POST",
        datatype: "text",
        beforeSend: function(xhr){        
            $('#alertaSucces').empty();
            $('#alertaSucces').append('<center><img src="images/cargando_naranja.gif" alt="Flowers in Chania"></center>');
        },
        success: function(resultado) {
            if(resultado == "ok"){                               
                mostrarAlerta("Cambio realizado con exito","correcto");
                fila = document.getElementById(id);	                
                padre = fila.parentNode;
                padre.removeChild(fila);               
            }else{                            
                mostrarAlerta(resultado,"fallido"); 
            }       
        },
        error: function(jqXHR, textStatus) {           
           mostrarAlerta("Ha ocurrido un error al conectarse con el servidor. Intentelo de nuevo mas tarde.","fallido");
        }
    });
}