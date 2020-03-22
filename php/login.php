<?php
session_start();
require("../Acceso/global.php");  

 $nombre= $_POST['txtusuario'];
 $contra= $_POST['txtpassword'];

$ejecu=mysqli_query($con,"select * from usuario where Usuario= '$nombre' and Password=$contra");
$resul=mysqli_num_rows($ejecu);

 if($resul==true) //si encontró algún dato en la tabla
    {
      if(is_numeric($nombre))
      {
        $_SESSION['num_emp']=$nombre;
        mysqli_close($con);
        header("Location: ./../ht/repositorio.php");  
         
      }
      else
        {
          $_SESSION['name']=$nombre;
          mysqli_close($con);
          header("Location: ../panel_control.html"); 
        }
    }
    else 
    {
      if ($resul1==false) //si no encontró algo en la tabla usuarios_alumnos ni administrador significa que el usuario no existe
      {
        mysqli_close($con);
        header("Location: ../index.html");
      } 
    }
  ?>
</body>
</html>
 




