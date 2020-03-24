<?php 

session_start();
$categoria=$_POST['cat'];
$nombre=$_POST['nom'];

     //Aqui consulto si existe un departamento igual a la que se va a guardar
     require("../Acceso/global.php");  
     $ejecu="select * from depto where iddepto = '$categoria'";
     $codigo=mysqli_query($con,$ejecu);
     $consultar=mysqli_num_rows($codigo);
     echo $consultar;
     if($consultar>0)
     {
             echo"<script>alert('Datos ya registrados')</script>";
     }
     elseif ($consultar<=0) 
     {
         if(!(mysqli_query($con,"Insert into depto values ('$categoria','$nombre')")))
         {
         //Ocurrió algún error
         echo "<script type=\"text/javascript\">alert(\"Error\");</script>";
         die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
         }
         else
         {
         //Guardado correcto
         echo "<script type=\"text/javascript\">alert(\"Categoría guardada correctamente\");</script>";
         }
         mysqli_close($con);   

     }



?>
