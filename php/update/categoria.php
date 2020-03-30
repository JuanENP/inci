<?php
session_start();
?>
<script type="text/javascript">
    function Alerta()
    {
        alert("Correcto");
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
        //update categoria SET categoria = 'CCCC1', nombre = 'ASISTENTE ADMINISTRATVO EN SALUD - A8' WHERE (categoria = 'CCCC');
        require("../../Acceso/global.php");
        //$sql="select * from categoria where categoria = '".$myid."'";
        $sql="update categoria SET idcategoria = '".$id."', nombre = '".$nom."' WHERE (idcategoria = '".$id_viejo."');";
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

