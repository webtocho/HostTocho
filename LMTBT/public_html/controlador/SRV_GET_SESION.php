<?php
    /*  CÓMO LLAMAR A ESTE PHP: 
        $.post( "../controlador/SRV_GET_SESION.php", {tipos :["ADMINISTRADOR", "COACH"]}, null, "text")
        .done(function(res) {
            switch(parseInt(res)){
                case 0:
                    //Es un administrador
                    break;
                case 1:
                    //Es un coach
                    break;
                default:
                    //Es un usuario que no es ni coach, ni administrador.
                    return;
            }
        })
        .fail(function() {
            //No se ha iniciado sesión
        });*/
    
    session_start();
    include("SRV_FUNCIONES.php");
    
    if (isset($_SESSION["ID_USUARIO"]) && isset($_SESSION["TIPO_USUARIO"])) {
        //Recuperamos el parámetro arreglo 'tipos' de POST, y hacemos que todos sus elementos sean mayúsculas.
        $tipos = array_map('strtoupper', $_POST["tipos"]);
        
        //Si el tipo del usuario logueado está en el arreglo 'tipos', el usuario es del tipo correcto.
        if (in_array($_SESSION["TIPO_USUARIO"], $tipos)){
            echo array_search($_SESSION["TIPO_USUARIO"], $tipos);
        } else {
            echo "-1";
        }
    } else {
        lanzar_error("No ha iniciado sesión", false);
    }
?>