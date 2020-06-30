<?php
session_start();
  error_reporting(0);//desactivar advertencias que solo aparecen cuando el usuario intenta abrir este archivo sin antes loguearse
  $nombre= $_POST['txtusuario'];
  $contra= $_POST['txtpassword'];
  include("Acceso/global.php");
  $verCampo='';//Ver el campo de correo y/o contraseña
  $ejecu=mysqli_query($con,"SELECT user FROM mysql.user WHERE User = '$nombre' AND password = PASSWORD('$contra')") or die();
  $resul=mysqli_num_rows($ejecu);

  if($resul==1) //si encontró algún dato en la tabla
  {
    $resul=mysqli_fetch_array($ejecu);
    $us=$resul[0];//usuario
    if(is_numeric($us))//Es un empleado
    {
      //$_SESSION['num_emp']=$nombre;
      $_SESSION['name']=$nombre;
      $_SESSION['con']=$contra;
    
      //Ver si el empleado tiene registrado un correo electronico 
      $ejecu2=mysqli_query($con,"SELECT idmail FROM mail WHERE trabajador_trabajador = '$nombre';");
      $resul2=mysqli_num_rows($ejecu2);
      if($resul2>=1)//Si el empleado no tiene un correo deberá aparecerle una ventana para registrar su correo     
      {
        $verCampo='';
      }
      else
      {
        $verCampo.='correo.'; 
      }
      //Ver si el empleado inicia sesión con la contraseña por defecto
      $ejecu3=mysqli_query($con,"SELECT user FROM mysql.user WHERE user = '$nombre' AND password = PASSWORD('9999'); ");
      $resul3=mysqli_num_rows($ejecu3);
      if($resul3>=1)//Si el empleado inicia sesión con la contraseña por defecto deberá aparecerle una ventana para registrar su nueva contraseña   
      {
        $verCampo.='pass';//password
      }
      
      if(empty($verCampo))
      {
        header("Location: ../ht/repositorio.php");  
      }
      else
      {
        $_SESSION['verCampo']=$verCampo;
        header("Location: ../mail/primer_acceso.php"); 
      }
       
    }
    else//Es un jefe o coordinador de algún depto
    {
      $_SESSION['name']=$nombre;
      $_SESSION['con']=$contra;
      //Ver si el jefe tiene registrado un correo electronico 
      $ejecu2=mysqli_query($con,"SELECT idmail FROM mail WHERE trabajador_trabajador = '$nombre';");
      $resul2=mysqli_num_rows($ejecu2);
      if($resul2>=1)//Si el empleado tiene un correo
      {
        $verCampo='';
      }
      else //sino deberá aparecerle una ventana para registrar su correo     
      {
        $verCampo.='correo.';
      }

      if(empty($verCampo))
      {
        header("Location: ../panel_control.php"); 
      }
      else
      {
        $_SESSION['verCampo']=$verCampo;
        header("Location: ../mail/primer_acceso.php");  
      }
      
    }
  }
  else 
  {
    //echo $us;
    mysqli_close($con);
    header("Location: ../index.php");
  }
?>