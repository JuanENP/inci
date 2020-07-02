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
        alert("Esta categoría ya existe");
        location.href="./../ht/categoria.php";
    }

    function Correcto()
    {
        alert("Guardado correctamente");
        location.href="./../ht/categoria.php";
    }
</script>

<?php 
    $categoria=$_POST['cat'];
    $nom=$_POST['nom'];
    
    //Aqui consulto si existe una categoria igual a la que se va a guardar
    $nombre=$_SESSION['name'];
    $contra=$_SESSION['con'];

    require("../Acceso/global.php");  
    $ejecu="select * from categoria where idcategoria = '$categoria'";
    $codigo=mysqli_query($con,$ejecu);
    $consultar=mysqli_num_rows($codigo);
    if($consultar>0)
    {
       echo "<script> Ya_Existe(); </script>";
    }
    else
    {   mysqli_autocommit($con, FALSE);
        if(!(mysqli_query($con,"Insert into categoria values ('$categoria','$nom')")))
        {
            mysqli_rollback($con);
            mysqli_autocommit($con, TRUE); 
            echo "alert('Datos incorrectos en la categoría); history.back();";
        }
        else
        {
            //Guardado correcto
            $nombre_host= gethostname();
            if(!(mysqli_query($con,"call inserta_bitacora_categoria('Guardado','$categoria','$nom','-', '-','$nombre_host')")))
            {
                mysqli_rollback($con);
                mysqli_autocommit($con, TRUE); 
                echo "alert('Datos incorrectos en bitacora tiempo de servicio'); history.back();";
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