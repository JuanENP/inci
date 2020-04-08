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
</script>

<script type="text/javascript">
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
    $ejecu="select * from categoria where idcategoria = '$categoria'";
    $codigo=mysqli_query($con,$ejecu);
    $consultar=mysqli_num_rows($codigo);

    if($consultar>0)
    {
       echo "<script> Ya_Existe(); </script>";
    }
    elseif ($consultar<=0) 
    {
        if(!(mysqli_query($con,"Insert into categoria values ('$categoria','$nom')")))
        {
        //Ocurrió algún error
        echo "<script type=\"text/javascript\">alert(\"Error\");</script>";
        die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
        }
        else
        {
        //Guardado correcto
        echo "<script> Correcto(); </script>";
        }
        mysqli_close($con);   
    }
?>