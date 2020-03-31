<?php 
session_start();
?>
<script type="text/javascript">
    function Ya_Existe()
    {
        alert("Ese tipo ya existe");
        location.href="./../ht/tipoempleado.php";
    }
</script>

<script type="text/javascript">
    function Correcto()
    {
        alert("Guardado correctamente");
        location.href="./../ht/tipoempleado.php";
    }
</script>

<?php 
$descripcion=$_POST['nom'];

     //Aqui consulto si existe un departamento igual a la que se va a guardar
     require("../Acceso/global.php");  
     $ejecu="select * from tipo where descripcion = '$descripcion'";
     $codigo=mysqli_query($con,$ejecu);
     $consultar=mysqli_num_rows($codigo);
    
     if($consultar>0)
     {
        echo "<script> Ya_Existe(); </script>";
     }
     else
     {
         if(!(mysqli_query($con,"Insert into tipo values ('','$descripcion')")))
         {
         //Ocurrió algún error
         echo "<script type=\"text/javascript\">alert(\"Error\");</script>";
         die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
         }
         else
         {
         //Guardado correcto
         echo "<script> Correcto(); </script>";
         }
         mysqli_close($con);   

     }



?>
