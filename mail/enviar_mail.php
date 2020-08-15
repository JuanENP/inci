<?php
    $nombre="biometric";
    $contra="5_w**/pQxcmk.";
    require("../Acceso/global.php"); 
    
    if(!empty($_POST['usuario']))
    {         
        $usuario=$_POST['usuario'];
        $ok=existeUsuario($usuario);
    }
    else
    {
        echo"<script language= javascript type= text/javascript> alert('Ingrese un usuario'); history.back();</script>";
        exit();
    }

    if ($usuario)
    {   
        $nuevaContra=generarCodigo(4);
        $mail=revisarMail($usuario,$nuevaContra);
        if(!empty($mail))
        {
            $from = "ISSSTE";
            $destino=$mail;
            $desde="From: ISSSTE";
            $asunto='Recuperar contraseña';
            $mensaje='Ingrese la  contraseña:'. $nuevaContra.' para ingresar nuevamente a su cuenta';
            ini_set( 'display_errors', 1 );
            error_reporting( E_ALL );
            $headers = "From:" . $from;
            $correcto= mail($destino,$asunto,$mensaje, $headers);
            if (!$correcto) 
            {
                $errorMessage = error_get_last()['message'];
            }
            else
            {
                echo"<script language= javascript type= text/javascript> alert('Le hemos enviado un correo electrónico para recuperar su cuenta.'); history.go(-2); </script>";
            }
        }
        else
        {
            echo"<script language= javascript type= text/javascript> alert('Error, no tiene un correo electrónico registrado.'); history.go(-2); </script>";
            exit();
        }
    }
    
    function existeUsuario($nomUser)
    {
        $nombre="biometric";
        $contra="5_w**/pQxcmk.";
        require("../Acceso/global.php"); 
        $sql="select User from mysql.user where User='$nomUser'";
        $query= mysqli_query($con, $sql); 
        $resul=mysqli_num_rows($query);
        if($resul==0)
        {   
            echo"<script language= javascript type= text/javascript> alert('NO EXISTE');history.back();</script>";
            exit();
        }
        else
        {
            return 1;
        }

    }

    function actualizarPassword($nomUser,$newPassword)
    {
        $nombre="biometric";
        $contra="5_w**/pQxcmk.";
        require("../Acceso/global.php"); 
        $sql="ALTER USER '$nomUser'@'localhost' IDENTIFIED BY '$newPassword';";
        $query= mysqli_query($con, $sql) or die();
        return 0;
    }

    function revisarMail($nomUser,$newPassword)
    {
        $nombre="biometric";
        $contra="5_w**/pQxcmk.";
        require("../Acceso/global.php"); 
        $sql="SELECT mail FROM mail where trabajador_trabajador='$nomUser'";
        $query= mysqli_query($con, $sql) or die();
        $resul=mysqli_num_rows($query);
        if($resul>0)
        {   
            $resul1=mysqli_fetch_array($query);
            $correo=$resul1[0];
            return $correo;
        }
        
    }

    function generarCodigo($longitud) 
    {
        $key = '';
        $pattern = '1234567890';
        $max = strlen($pattern)-1;
        for($i=0;$i < $longitud;$i++) $key .= $pattern{mt_rand(0,$max)};
        return $key;
    }  
?>