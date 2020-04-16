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
    function Correcto()
    {
        alert("Modificado correctamente");
        location.href="../../ht/categoria.php";
        //window.close();
        //Si quieres usar instrucciones php, salte del script y coloca la apertura y cierre de php, escribe dentro de ellas de forma normal
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
        $sql="select * from categoria where idcategoria='$id'";
        $query= mysqli_query($con, $sql) or die();
        $resul=mysqli_fetch_array($query);
        $nombre_cat=$resul[1];

        mysqli_autocommit($con, FALSE);
        if(!(mysqli_query($con,"update categoria SET idcategoria = '".$id."', nombre = '".$nom."' WHERE (idcategoria = '".$id_viejo."')")))
        {
            mysqli_rollback($con);
            mysqli_autocommit($con, TRUE); 
            echo "alert('Datos incorrectos en la categoría); history.back();";
        }
        else
        {
            $nombre_host= gethostname();
            if(!(mysqli_query($con,"call inserta_bitacora_categoria('Actualizado','$id','$nom', '$id_viejo', '$nombre_cat','$nombre_host')")))
            {
                mysqli_rollback($con);
                mysqli_autocommit($con, TRUE); 
                echo "alert('Datos incorrectos en bitacora tiempo de servicio); history.back();";
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