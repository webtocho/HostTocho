<?php
    /* Devuelve la información de un usuario que tenga iniciada su sesión, en un JSON. 
       Si la sesión no está iniciada, los atributos del JSON serán nulos.
       Los atributos son: "id" y "tipo". */

    session_start();
    if (isset($_SESSION["ID_USUARIO"]) && isset($_SESSION["TIPO_USUARIO"])) {
        $infoUsuario = [
            "id" => $_SESSION["ID_USUARIO"],
            "tipo" => $_SESSION["TIPO_USUARIO"]
        ];
        
        echo json_encode($infoUsuario);
    } else {
        $infoUsuario = [
            "id" => NULL,
            "tipo" => NULL
        ];
        
        echo json_encode($infoUsuario);
    }
?>