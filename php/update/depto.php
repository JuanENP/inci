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
        header("Location: ../index.html");
        die();
    }
?>

<script type="text/javascript">
    function Alerta()
    {
        alert("Actualizado correctamente");
        location.href="../../ht/departamentos.php";
    }
    function imprime(texto)
    {
        alert(texto);
        history.back();
    }
</script>

<?php
$old_id=$_POST['old_id'];
$idcat=$_POST['idcat'];
$nomcat=$_POST['nomcat'];

actualizar($idcat, $nomcat,$old_id);

    function actualizar($id,$nom,$id_viejo)
    {   
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        require("../../Acceso/global.php");
        //SIRVE PARA SELECCIONAR EL NOMBRE LA CATEGORIA QUE SE VA A ACTUALIZAR
        $sql="select * from depto where iddepto='$id'";
        $query= mysqli_query($con, $sql) or die();
        while($resul=mysqli_fetch_array($query))
        {
            $nombre_depto=$resul[1];
        }
        mysqli_autocommit($con, FALSE);
        if(!(mysqli_query($con,"update depto SET iddepto = '".$id."', nombre = '".$nom."' WHERE (iddepto = '".$id_viejo."');")))
        {
            mysqli_rollback($con);
            mysqli_autocommit($con, TRUE); 
            echo "<script> imprime('Datos incorrectos al actualizar el departamento, error línea 53, verifique con el administrador de sistemas'); </script>";
        }
        else
        {
            $nombre_host= gethostname();
            if(!(mysqli_query($con,"call inserta_bitacora_depto('Actualizado','$id','$nom', '$id_viejo', '$nombre_depto','$nombre_host')")))
            {
                mysqli_rollback($con);
                mysqli_autocommit($con, TRUE); 
                echo "<script> imprime('Datos incorrectos al insertar en bitacora categoría, error línea 58, verifique con el administrador de sistemas'); </script>";
            }
            else
            {
                mysqli_commit($con);
                mysqli_autocommit($con, TRUE);
                echo "<script> Alerta(); </script>";
            }
        }
    }
?>