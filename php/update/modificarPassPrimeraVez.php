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
            $correo=$_POST['mail'];
            // filter_var regresa los datos filtrados
            $correo = filter_var($correo, FILTER_VALIDATE_EMAIL);
            // regresa false si no son válidos
            if ($correo !== false) 
            {
                $mail=$_POST['mail'];
                if(is_numeric($_POST['nuevaContra']) && is_numeric($_POST['confirmaContra']))
                {
                    
                    $nuevaContra=$_POST['nuevaContra'];
                    $confirmaContra=$_POST['confirmaContra'];
                    if($nuevaContra==$confirmaContra)
                    {
                        $opcion=1;
                    }
                    else
                    {
                        echo"<script language= javascript type= text/javascript> alert('Error. LA NUEVA CONTRASEÑA Y LA CONFIRMACIÓN DE LA MISMA SON DIFERENTES.');history.back();</script>";
                        exit();   
                    }
                    
                }
                else
                {
                    echo"<script language= javascript type= text/javascript> alert('La contraseñas deben ser numéricas');history.back();</script>";
                    exit();
                }
                
            } 
            else 
            {
                echo"<script language= javascript type= text/javascript> alert(' La dirección de correo electrónico no es válida ');history.back();</script>";
                exit();
            }
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
                echo"<script language= javascript type= text/javascript> alert('La contraseñas deben ser numéricas');history.back();</script>";
                exit();
            }
            
        }

        if(empty($_POST['nuevaContra']) && empty($_POST['confirmaContra']) && !empty($_POST['mail']))
        {  
            $correo=$_POST['mail'];
            // filter_var regresa los datos filtrados
            $Sicorreo = filter_var($correo, FILTER_VALIDATE_EMAIL);
            // regresa false si no son válidos
           if ($Sicorreo != false) 
            {
                $mail=$_POST['mail'];
                $opcion=3;
            } 
            else 
            {
                echo"<script language= javascript type= text/javascript> alert(' La dirección de correo electrónico no es válida ');history.back();</script>";
                exit();
            }
        }
    }
    else
    {
        header("Location: ../../index.php");
        die();
    } 
    
    if($opcion==0)
    {
        echo"<script language= javascript type= text/javascript> alert('ERROR. NO DEBE DEJAR NINGÚN CAMPO VACÍO.');history.back();</script>";
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
        $query= mysqli_query($con, $sql) or die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
        return 0;
    }
?>