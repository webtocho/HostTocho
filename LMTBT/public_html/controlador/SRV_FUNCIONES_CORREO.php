<?php
    function enviarCorreoDeAceptacion($correo,$usuario,$contrasena){
        $mensaje = "Registro en http://lmtbtuxtla.com\n\n";
        $mensaje .= "Estos son tus datos de registro:\n";
        $mensaje .= "Usuario: ".$usuario."\n";
        $mensaje .= "Contraseña: ".$contrasena."\n\n";
        $asunto = "Gracias por registrarte con nosotros LIGA MUNICIPAL DE TOCHO BANDERA TUXTLA";
        $headers =  'MIME-Version: 1.0' . "\r\n"; 
        $headers .= 'From: Your name <america1234562005@hotmail.com>' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n"; 
        return mail($correo,$asunto,$mensaje,$headers);
    }
?>
   