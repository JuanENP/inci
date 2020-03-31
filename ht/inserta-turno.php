<?php 

session_start();
$turno=$_POST['turno'];
$hora_ent=$_POST['entrada'];
$hora_sal=$_POST['salida'];


     //Aqui consulto si existe una categoria igual a la que se va a guardar
     require("../Acceso/global.php");  
     $ejecu="select * from turno where idturno = '$turno'";
     $codigo=mysqli_query($con,$ejecu);
     $consultar=mysqli_num_rows($codigo);
     echo $consultar;
     if($consultar>0)
     {
             echo"<script>alert('Datos ya registrados')</script>";
     }
     else
     {
         //FALTA AGREGAR EL TOTAL DE HORAS EN LUGAR DE 0
         if(!(mysqli_query($con,"Insert into turno values ('$turno','$hora_ent','$hora_sal',0)")))
         {
            //Ocurrió algún error
            echo "<script type=\"text/javascript\">alert(\"Error\");</script>";
            die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
         }
         else
         {
            //Guardado correcto
            echo "<script type=\"text/javascript\">alert(\"Turno guardado correctamente\");</script>";
         }
         mysqli_close($con);   

     }



?>
