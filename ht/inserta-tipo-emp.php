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
        header("Location: ../index.php");
        die();
    }
?>
<script type="text/javascript">
    function Ya_Existe()
    {
        alert("Ese tipo de empleado ya existe");
        location.href="../ht/tipoempleado.php";
    }

    function Correcto()
    {
        alert("Guardado correctamente");
        location.href="../ht/tipoempleado.php";
    }
</script>

<?php 
    $descripcion=$_POST['nom'];

     //Aqui consulto si existe un departamento igual a la que se va a guardar 
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
         {  //Nota la bitacora tipo se guarda automaticamente
            // $nombre_host= gethostname();
            // $sql="call inserta_bitacora_tipo('Guardado','-','$descripcion','-', '-','$nombre_host')";
            // $query= mysqli_query($con, $sql) or die();
            //Guardado correcto
            echo "<script> Correcto(); </script>";
         }
         mysqli_close($con);   
     }
?>