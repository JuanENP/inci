<?php
session_start();
    if (($_SESSION["name"]) && ($_SESSION["con"]))
    {
        $nombre=$_SESSION['name'];// numero del trabajador
        $contra=$_SESSION['con'];
        require("../../Acceso/global.php"); 
        $salida='';
        if(!empty($_POST['contraActual']) && !empty($_POST['nuevaContra']) && !empty($_POST['email']))
        {
            $contraActual=$_POST['contraActual'];
            $nuevaContra=$_POST['nuevaContra'];
            $mail=$_POST['email'];
            // filter_var regresa los datos filtrados
            $correo = filter_var($mail, FILTER_VALIDATE_EMAIL);
            if ($correo == false) 
            {
                $salida.='La dirección de correo electronico es incorrecta.';
            }            
        }
        else
        {        
            echo"<script language= javascript type= text/javascript> alert('Campos vacíos'); history.back();</script >";
            exit();
        }
    }
    else
    {
        header("Location: ../../index.html");
        die();
    }


    if(empty($salida))
    {
        $resultado=comprobarPasswordActual($nombre,$contraActual);
        $passActualizada=actualizarPassword($nombre,$nuevaContra);
        $mailActualizado=actualizarMail($mail,$nombre);
        echo"<script language= javascript type= text/javascript> alert('Datos actualizados correctamente, inicie sesión nuevamente');location.href='../../index.html';</script >";
    }
    else
    {
        echo"<script language= javascript type= text/javascript> alert('$salida'); history.back();</script >";
        exit();
    }

    function comprobarPasswordActual($nomUsuario,$passwordActual)
    {
        global $con;
        $sql="select User from mysql.user where User='$nomUsuario' and Password=PASSWORD('$passwordActual');";
        $query= mysqli_query($con, $sql) or die();
        $resul=mysqli_num_rows($query);
        if($resul>0)
        {   
            return 0;
        }
        else
        {
            echo"<script language= javascript type= text/javascript> alert('Datos incorrectos'); history.back();</script >";
            exit();
        }
    }

    function actualizarPassword($nomUsuario,$newPassword)
    {
        global $con;
        $sql="ALTER USER '$nomUsuario'@'localhost' IDENTIFIED BY '$newPassword';";
        if(mysqli_query($con, $sql))
        {
            return 0;
        }
        else
        {
            echo mysqli_errno($con).": ".mysqli_error($con);
            exit();
        }
    }
    function actualizarMail($mail,$nomUsuario)
    {
        global $con;
    
        $sql="UPDATE mail SET mail = '$mail' WHERE trabajador_trabajador='$nomUsuario';";
        if(mysqli_query($con, $sql))
        {
            return 0;
        }
        else
        {
            echo mysqli_errno($con).": ".mysqli_error($con);
            exit();
        }
    }

?>
