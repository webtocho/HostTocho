<?php
    function enviarCorreoDeAceptacion($correo,$usuario,$contrasena){
        $mensaje = "Registro en http://lmtbtuxtla.com\n\n";
        $mensaje .= "Estos son tus datos de registro:\n";
        $mensaje .= "Usuario: ".$usuario."\n";
        $mensaje .= "Contraseña: ".$contrasena."\n\n";
        $asunto = "Gracias por registrarte con nosotros LIGA MUNICIPAL DE TOCHO BANDERA TUXTLA";
        $headers =  'MIME-Version: 1.0' . "\r\n"; 
        $headers .= 'From: LMTB TUXTLA <superbowlstore@hotmail.com>' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n"; 
        return mail($correo,$asunto,$mensaje,$headers);
    }
    function enviarCorreoRecuperacion($correo,$contrasena){
        $mensaje = "Registro en http://lmtbtuxtla.com\n\n";
        $mensaje .= "Contraseña nueva de recuperacion recuerda modificar los datos nuevamente:\n";       
        $mensaje .= "Nueva contraseña: ".$contrasena."\n\n";
        $asunto = "Correo de recuperacion LIGA MUNICIPAL DE TOCHO BANDERA TUXTLA";
        $headers =  'MIME-Version: 1.0' . "\r\n"; 
        $headers .= 'From: LMTB TUXTLA <superbowlstore@hotmail.com>' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n"; 
        return mail($correo,$asunto,$mensaje,$headers);
    }
    function generaPass(){
        //Se define una cadena de caractares. Te recomiendo que uses esta.
        $cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        //Obtenemos la longitud de la cadena de caracteres
        $longitudCadena=strlen($cadena);   
        //Se define la variable que va a contener la contraseña
        $pass = "";
        //Se define la longitud de la contraseña, en mi caso 10, pero puedes poner la longitud que quieras
        $longitudPass=10;    
        //Creamos la contraseña
        for($i=1 ; $i<=$longitudPass ; $i++){
            //Definimos numero aleatorio entre 0 y la longitud de la cadena de caracteres-1
            $pos=rand(0,$longitudCadena-1);    
            //Vamos formando la contraseña en cada iteraccion del bucle, añadiendo a la cadena $pass la letra correspondiente a la posicion $pos en la cadena de caracteres definida.
            $pass .= substr($cadena,$pos,1);
        }
        return $pass;
    }
?>
   