<?php 
session_start();
    if (($_SESSION["name"]) && ($_SESSION["con"]))
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
    }
    else
    {
        header("Location: ../index.php");
        die();
    }
?>

<script type="text/javascript">
    function Correcto()
    {
        alert("Actualizado correctamente");
        location.href="../../ht/tipoempleado.php";
    }

    function imprime(texto)
    {
        alert(texto);
        history.back();
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
        $resul=mysqli_fetch_array($query);
        $nombre_tipo=$resul[1];

        mysqli_autocommit($con, FALSE);
        if(!(mysqli_query($con,"update tipo SET  descripcion = '".$nuevo."' WHERE (idtipo = '".$id."')")))
        {
            mysqli_rollback($con);
            mysqli_autocommit($con, TRUE); 
            echo "<script> imprime('Datos incorrectos al actualizar el tipo de empleado, error línea 51, verifique con el administrador de sistemas'); </script>";
        }
        else
        {   $nombre_host= gethostname();
            //GUARDAR EN LA BITACORA DE TIPO
            if(!(mysqli_query($con,"call inserta_bitacora_tipo('Actualizado','$nuevo', '$nombre_tipo','$nombre_host')")))
            {
                mysqli_rollback($con);
                mysqli_autocommit($con, TRUE); 
                echo "<script> imprime('Datos incorrectos al insertar en bitacora tipo de empleado, error línea 60, verifique con el administrador de sistemas'); </script>";
            }
            else
            {
                mysqli_commit($con);
                mysqli_autocommit($con, TRUE);
                echo "<script> Correcto(); </script>";
            }
        }
    }
?>