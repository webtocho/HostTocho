<?php 
	require 'SRV_CONEXION.php';
        require 'SRV_FUNCIONES_CORREO.php';
	$db = new SRV_CONEXION();
	$conexion = $db->getConnection();
        if(empty($_POST['correo_recuperar']) == false){
            if (filter_var($_POST['correo_recuperar'], FILTER_VALIDATE_EMAIL)){
                $consulta = $conexion->prepare("SELECT CORREO FROM usuarios WHERE CORREO = ?");
                $consulta->bind_param("s", $_POST['correo_recuperar']);
                if ($consulta->execute()){
                    $res = $consulta->get_result();
                    $info = $res->fetch_assoc();                                        
                    if(empty($info['CORREO']) == false){
                        $nueva_password = generaPass();
                        $consulta = $conexion->prepare("UPDATE usuarios SET PASSWORD = ? WHERE CORREO = ?");
                        $consulta->bind_param("ss",$nueva_password,$_POST['correo_recuperar']);
                        if ($consulta->execute()){
                            if(enviarCorreoRecuperacion($_POST['correo_recuperar'],$nueva_password)){
                                echo "ok";
                            }else{
                                echo "Error al enviar el correo de recuperacion";
                            }
                        }else{
                            echo "Error al tratar de generar codigo";
                        }
                    }else{
                        echo "El correo ingresado no esta ligado a niguna cuenta";
                    }
                }else{
                    echo "Error al realizar la consulta";
                }
            }else{
                echo "El correo ingresado es invalido";
            }
        }else{
            echo "Debes de ingresar el correo que deseas recuperar";
        }
	$conexion->close();
?>