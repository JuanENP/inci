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
        $sql="select * from depto where iddepto='$id'";
        $query= mysqli_query($con, $sql) or die();
        while($resul=mysqli_fetch_array($query))
        {
            $nombre_depto=$resul[1];
        }

        $sql="update depto SET iddepto = '".$id."', nombre = '".$nom."' WHERE (iddepto = '".$id_viejo."');";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
          die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
        }
        else
        {
            $nombre_host= gethostname();
            $sql="call inserta_bitacora_depto('Actualizado','$id','$nom', '$id_viejo', '$nombre_depto','$nombre_host')";
            $query= mysqli_query($con, $sql) or die();
         
            echo "<script> Alerta(); </script>";
        }
    }
?>