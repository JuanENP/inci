<?php
session_start();
require("../Acceso/global.php");  

 $nombre= $_POST['txtusuario'];
 $contra= $_POST['txtpassword'];

$ejecu=mysqli_query($con,"select * from usuario where Usuario= \"$nombre\" and Password=$contra");
$resul=mysqli_num_rows($ejecu);

 if($resul==true) //si encontró algún dato en la tabla usuarios_alumnos
   {
        $_SESSION['name']=$nombre;
        mysqli_close($con);
        header("Location: ../panel_control.php");   
    }
    else 
    {
      if ($resul1==false) //si no encontró algo significa que el usuario no existe
      {
        mysqli_close($con);
        header("Location: ../index.html");
      } 
    }
  ?>
</body>
</html>
 




