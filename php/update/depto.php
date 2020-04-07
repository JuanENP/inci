<?php
session_start();
$nombre=$_SESSION['name'];
$contra=$_SESSION['con'];
require("../Acceso/global.php");  
                            
//si la variable de sesión no existe, entonces no es posible entrar al panel. 
//Lo redirigimos al index.html para que inicie sesión
if($nombre==null || $nombre=='')
{
    header("Location: ../index.html");
    die();
}
?>

<script type="text/javascript">
    function Alerta()
    {
        alert("Modificado correctamente");
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
        $sql="update depto SET iddepto = '".$id."', nombre = '".$nom."' WHERE (iddepto = '".$id_viejo."');";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
          die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
        }
        else
        {
            echo "<script> Alerta(); </script>";
        }
    }
?>