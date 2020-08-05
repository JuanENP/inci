<?php
session_start();

    if (($_SESSION["name"]) && ($_SESSION["con"]))
    {
        $nombre=$_SESSION['name'];// numero del trabajador
        $contra=$_SESSION['con'];
        $opcion=0;
        require("../../Acceso/global.php");
        require('../insert/mail.php');
        //Guardar correo y actualizar contraseña
        if(!empty($_POST['mail'] && !empty($_POST['nuevaContra']) && !empty($_POST['confirmaContra'])))
        {   
            $revisarMail=revisarMail($_POST['mail']);
            $revisarPass=revisarPass($_POST['nuevaContra'],$_POST['confirmaContra']);
            $opcion=1;
            $mail= $_POST['mail'];
            $nuevaContra=$_POST['nuevaContra'];
            $confirmaContra=$_POST['nuevaContra'];
        }
        else
        {
            //Actualizar solo la contraseña
            if(!empty($_POST['nuevaContra']) && !empty($_POST['confirmaContra']) && empty($_POST['mail']))
            {
                $revisarPass=revisarPass($_POST['nuevaContra'],$_POST['confirmaContra']);
                $opcion=2;
                $nuevaContra=$_POST['nuevaContra'];
                $confirmaContra=$_POST['nuevaContra'];
            }
            else
            {
                //Guardar solo el correo
                if( !empty($_POST['mail']) && empty($_POST['nuevaContra']) && empty($_POST['confirmaContra']))
                {  
                    $revisarMail=revisarMail($_POST['mail']);
                    $opcion=3;
                    $mail=$_POST['mail'];
                }
            }
        }
    }
    else
    {
        header("Location: ../../index.php");
        die();
    }
    // echo"<script language= javascript type= text/javascript> alert('$opcion');history.back();</script>";

    if($opcion==0)
    {
        echo"<script language= javascript type= text/javascript> alert('Error, verifique con el administrador de sistemas');history.back();</script>";
        exit();
    }
    else
    {
        if($opcion==1)
        {  
            $guardarMail=guardarMail($mail,$nombre);
            $actualizarPassword=actualizarPassword($nombre,$nuevaContra);
            echo"<script language= javascript type= text/javascript> alert('Datos guardados, debe iniciar sesión nuevamente'); location.href='../../index.php';</script>";  
        }
        else
        {
            if($opcion==2)
            {
                $actualizarPassword=actualizarPassword($nombre,$nuevaContra);            
                echo"<script language= javascript type= text/javascript> alert('Contraseña actualizada correctamente, inicie sesión nuevamente'); location.href='../../index.php';</script>";    
            }
            else
            {
                if($opcion==3)
                {  
                    $guardarMail=guardarMail($mail,$nombre);
                    if(is_numeric($nombre))
                    {
                        echo"<script language= javascript type= text/javascript> alert('Correo electrónico guardado correctamente'); location.href='../../ht/repositorio.php';</script>";  
                    }
                    else
                    {   
                        echo"<script language= javascript type= text/javascript> alert('Correo electrónico guardado correctamente'); location.href='../../panel_control.php';</script>";  
                    }
                }
            }
        }
    }
     

    function actualizarPassword($nuevoUsuario,$newPassword)
    {
        global $con;
        $sql="ALTER USER '$nuevoUsuario'@'localhost' IDENTIFIED BY '$newPassword';";
        $query= mysqli_query($con, $sql);
        if($query)
        {
            return true;
        }
        else
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $línea='85';
            error($er1,$er2,$línea);
        }
    }

    function revisarMail($correo)
    {
        // filter_var regresa los datos filtrados
        $Sicorreo = filter_var($correo, FILTER_VALIDATE_EMAIL);
        // regresa false si no son válidos
        if ($Sicorreo != false) 
        {
            return true;
        } 
        else 
        {
            echo"<script language= javascript type= text/javascript> alert(' La dirección de correo electrónico no es válida ');history.back();</script>";
            exit();
        }
    }
    function revisarPass($newPass,$confirmaPass)
    {
        if($newPass==$confirmaPass)
        {
            if($newPass !== '9999')//si comprobar si la contraseña ingresada es diferente a la que dimos por defecto
            {
                return true;
            }
            else
            {
                echo"<script language= javascript type= text/javascript> alert('Debe registrar una contraseña diferente a la contraseña por defecto'); history.back();</script>";    
                exit();
            }
        }
        else
        {
            echo"<script language= javascript type= text/javascript> alert('Error, las contraseñas son diferentes');history.back();</script>";
            exit();   
        }
    }
     

?>