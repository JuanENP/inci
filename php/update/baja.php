<?php
session_start();
    if (($_SESSION["name"]) && ($_SESSION["con"]))
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        require("../../Acceso/global.php"); 
    }
    else
    {
        header("Location: ../index.php");
        die();
    }
?>

<?php
    date_default_timezone_set('America/Mexico_City');
    require("../../assets/js/alerts-justificacion.php");
    $id=$_GET['4Plkksd7'];
    actualizar($id);

    function actualizar($id)
    {   
        global $con;
        $hoy=date("Y-m-d");//la fecha actual
        $sql="UPDATE bajas SET fecha='$hoy', baja_definitiva='1' where idbajas=$id;";
        $query= mysqli_query($con, $sql);
        if($query)
        {
            echo "<script> imprime('BAJA DEFINITIVA aplicada correctamente. SE BORRARÁN TODOS LOS REGISTROS' +
            ' DE ESTE EMPLEADO DE FORMA AUTOMÁTICA AL TERMINAR LA ACTUAL QUINCENA.'); </script>";
        }
        else
        {
            echo "<script> imprime('Surgió un error, reintente.'); </script>";
        }
    }
?>