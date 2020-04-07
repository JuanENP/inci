<?php
session_start();  

  $nombre= $_POST['txtusuario'];
  $contra= $_POST['txtpassword'];
  include("../Acceso/global.php");

  $ejecu=mysqli_query($con,"SELECT user FROM mysql.user WHERE user = '$nombre' AND password = PASSWORD('$contra')");
  $resul=mysqli_num_rows($ejecu);

 if($resul==1) //si encontró algún dato en la tabla
  {
    $resul5=mysqli_fetch_array($ejecu);
    $us=$resul5[0];
    if(is_numeric($us))
    {
      $_SESSION['num_emp']=$nombre;
      mysqli_close($con);
      header("Location: ./../ht/repositorio.php");  
       
    }
    else
    {
      $_SESSION['name']=$nombre;
      $_SESSION['con']=$contra;
      mysqli_close($con);
      header("Location: ../panel_control.php"); 
    }
  }
  else 
  {
    //echo $us;
    mysqli_close($con);
    header("Location: ../index.html");
  }
?>
 




