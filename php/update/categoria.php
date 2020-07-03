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
        header("Location: ../../index.php");
        die();
    }
?>
<script type="text/javascript">
    function Correcto()
    {
        alert("Categoría modificada correctamente");
        location.href="../../ht/categoria.php";
        //window.close();
    }
</script>

<?php
$old_id=$_POST['old_id'];
$old_nom=$_POST['old_nom'];
$idcat=$_POST['idcat'];
$nomcat=$_POST['nomcat'];

actualizar($idcat, $nomcat,$old_id,$old_nom);

    function actualizar($id,$nom,$id_viejo,$nom_viejo)
    {
        global $con;
        mysqli_autocommit($con, FALSE);

        if(!(mysqli_query($con,"update categoria SET idcategoria = '$id', nombre = '$nom' WHERE (idcategoria = '$id_viejo')")))
        {
            echo "Error en bitácora categoria.".mysqli_errno($con) . ": " . mysql_error($con) . " history.back();";
            mysqli_rollback($con);
            mysqli_autocommit($con, TRUE); 
        }
        else
        {
            $nombre_host= gethostname();
            if(!(mysqli_query($con,"call inserta_bitacora_categoria('Actualizado','$id','$nom', '$id_viejo', '$nom_viejo','$nombre_host')")))
            {
                echo "Error en bitácora categoria.".mysqli_errno($con) . ": " . mysql_error($con) . " history.back();";
                mysqli_rollback($con);
                mysqli_autocommit($con, TRUE); 
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