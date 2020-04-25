<?php
session_start();
    if (($_SESSION["name"]) && ($_SESSION["con"]))
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        require("../Acceso/global.php"); 
    }
    else
    {
        header("Location: ../index.html");
        die();
    }
?>

<script type="text/javascript">
    function Ya_Existe()
    {
        alert("Ese departamento ya existe");
        location.href="../ht/departamentos.php";
    }
</script>

<script type="text/javascript">
    function Correcto()
    {
        alert("Guardado correctamente");
        location.href="../ht/departamentos.php";
    }
</script>


<?php 
    $nombre=$_SESSION['name'];
    $contra=$_SESSION['con'];
  
    $depto=$_POST['depto'];
    $nom=$_POST['nom'];

     //Aqui consulto si existe un departamento igual a la que se va a guardar
      
     $ejecu="select * from depto where iddepto = '$depto'";
     $codigo=mysqli_query($con,$ejecu);
     $consultar=mysqli_num_rows($codigo);

     if($consultar>0)
     {
        echo "<script> Ya_Existe(); </script>";
     }
     elseif ($consultar<=0) 
     {
         if(!(mysqli_query($con,"Insert into depto values ('$depto','$nom')")))
         {
         //Ocurrió algún error
         echo "<script type=\"text/javascript\">alert(\"Error\");</script>";
         die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
         }
         else
         {
         
        $nombre_host= gethostname();
        $sql="call inserta_bitacora_depto('Guardado','$depto','$nom','-', '-','$nombre_host')";
        $query= mysqli_query($con, $sql) or die();
           
         //Guardado correcto
         echo "<script> Correcto(); </script>";
         }
         mysqli_close($con);   

     }



?>
