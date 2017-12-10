<?php
    /* Complemento de comprobarSesion en JS_FUNCIONES.js */
    
    session_start();
    if (isset($_SESSION["ID_USUARIO"]) && isset($_SESSION["TIPO_USUARIO"])) {
        //Recuperamos el parámetro arreglo 'tipos' de POST, y hacemos que todos sus elementos sean mayúsculas.
        $tipos = array_map('strtoupper', json_decode($POST["tipos"]));
        
        //Si el tipo del usuario logueado está en el arreglo 'tipos', el usuario es del tipo correcto.
        if (in_array($_SESSION["TIPO_USUARIO"], $tipos)){
            return "si";
        } else {
            return "no";
        }
    } else {
        return "null";
    }
?>