<?php 

session_start();
$turno=$_POST['ctl00$MainContent$txtTurno'];
$hora_entrada=$_POST['ctl00$MainContent$txtEntrada'];
$hora_salida=$_POST['ctl00$MainContent$txtSalida'];


     //Aqui consulto si existe una categoria igual a la que se va a guardar
     require("../Acceso/global.php");  
     $ejecu="select * from turno where turno = '$turno'";
     $codigo=mysqli_query($con,$ejecu);
     $consultar=mysqli_num_rows($codigo);
     echo $consultar;
     if($consultar>0)
     {
             echo"<script>alert('Datos ya registrados')</script>";
     }
     elseif ($consultar<=0) 
     {
         if(!(mysqli_query($con,"Insert into turno values ('$categoria','$nombre')")))
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
