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
        header("Location: ../../index.html");
        die();
    }
?>

<script type="text/javascript">
    function Ya_Existe()
    {
        alert("Esta turno ya existe, verifique");
        history.back();
        
    }
</script>

<script type="text/javascript">
    function Correcto()
    {
        alert("Turno guardado correctamente");
        location.href="../../ht/turnos.php";
    }
</script>

<?php 
    $turno=$_POST['turno'];
    $hora_ent=$_POST['entrada'];
    $hora_sal=$_POST['salida'];
    //Aqui consulto si existe una categoria igual a la que se va a guardar
    $ejecu="select * from turno where idturno = '$turno'";
    $codigo=mysqli_query($con,$ejecu);
    $consultar=mysqli_num_rows($codigo);
    if($consultar>0)
    {
        echo "<script> Ya_Existe(); </script>";
    }
    else
    {
        //FALTA AGREGAR EL TOTAL DE HORAS EN LUGAR DE 0
        if(!(mysqli_query($con,"Insert into turno values ('$turno','$hora_ent','$hora_sal',0)")))
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