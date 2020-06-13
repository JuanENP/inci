<?php
session_start();
    if (($_SESSION["name"]) && ($_SESSION["con"]))
    {
        $nombre=$_SESSION['name'];// numero del trabajador
        $contra=$_SESSION['con'];
        require("../../Acceso/global.php"); 

        if(!empty($_POST['contraActual']) && !empty($_POST['nuevaContra']) )
        {
            $contraActual=$_POST['contraActual'];
            $nuevaContra=$_POST['nuevaContra'];
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

    if($contraActual==$nuevaContra)
    {
        echo"<script language= javascript type= text/javascript> alert('No se actualizó su contraseña'); history.back();</script >";
    }
    else
    {
        $resultado=comprobarPasswordActual($nombre,$contraActual);
        if($resultado==1)
        {
            $actualizado=actualizarPassword($nombre,$nuevaContra);
            echo"<script language= javascript type= text/javascript> alert('Contraseña actualizada correctamente, inicie sesión nuevamente');location.href='../../index.html';</script >";

        }
        else
        {
            echo"<script language= javascript type= text/javascript> alert('Error de información'); history.back();</script >";
            exit();
        }
    }
    function comprobarPasswordActual($nomUsuario,$passwordActual)
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        require("../../Acceso/global.php"); 
        $sql="select User from mysql.user where User='$nomUsuario' and Password=PASSWORD('$passwordActual');";
        $query= mysqli_query($con, $sql) or die();
        $resul=mysqli_num_rows($query);
        if($resul>0)
        {   
            return 1;
        }
        else
        {
            return 0;
        }
    }
    function actualizarPassword($nomUsuario,$newPassword)
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        require("../../Acceso/global.php"); 
        $sql="ALTER USER '$nomUsuario'@'localhost' IDENTIFIED BY '$newPassword';";
        $query= mysqli_query($con, $sql) or die();
        return 0;
    }

?>
