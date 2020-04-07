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
        $sql="update tipo SET  descripcion = '".$nuevo."' WHERE (idtipo = '".$id."');";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
        die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
        }
        else
        {
            echo "<script> Correcto(); </script>";
        }
    }
?>