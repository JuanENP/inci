<?php 
session_start();
    if (($_SESSION["name"]) && ($_SESSION["con"]))
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
    }
    else
    {
        header("Location: ../index.html");
        die();
    }
?>

<script type="text/javascript">
    function Correcto()
    {
        alert("Actualizado correctamente");
        location.href="../../ht/tipoempleado.php";
    }
</script>

<?php
    $id_anterior=$_POST['id'];
    $nuevo_tipo=$_POST['descripcion'];

    actualizar($nuevo_tipo,$id_anterior);

    function actualizar($nuevo,$id)
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        require("../../Acceso/global.php"); 
         //SIRVE PARA SELECCIONAR EL  NOMBRE DEL TIPO DE EMPLEADO QUE SE VA A ACTUALIZAR
         $sql="select * from tipo where idtipo='$id'";
         $query= mysqli_query($con, $sql) or die();
         while($resul=mysqli_fetch_array($query))
         {
             $nombre_tipo=$resul[1];
         }

        $sql="update tipo SET  descripcion = '".$nuevo."' WHERE (idtipo = '".$id."');";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
        die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
        }
        else
        {
            $nombre_host= gethostname();
            $sql="call inserta_bitacora_tipo('Actualizado','-','$nuevo', '$id', '$nombre_tipo','$nombre_host')";
            $query= mysqli_query($con, $sql) or die();
            echo "<script> Correcto(); </script>";
        }
    }
?>