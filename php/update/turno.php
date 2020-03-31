<script type="text/javascript">
    function Alerta()
    {
        alert("Correcto");
        location.href="../../ht/turno.php";
        //window.close();
        //Si quieres usar instrucciones php, salte del script y coloca la apertura y cierre de php, escribe dentro de ellas de forma normal
    }
</script>

<?php
session_start();
$old_id=$_POST['old_id'];
$idcat=$_POST['idcat'];
$nomcat=$_POST['nomcat'];
$nomcat2=$_POST['nomcat2'];

actualizar($idcat, $nomcat,$old_id);

    function actualizar($id,$nom,$id_viejo)
    {
        //update categoria SET categoria = 'CCCC1', nombre = 'ASISTENTE ADMINISTRATVO EN SALUD - A8' WHERE (categoria = 'CCCC');
        require("../../Acceso/global.php");
        //$sql="select * from categoria where categoria = '".$myid."'";
        $sql="update turno SET idturno = '".$idcat."', entrada = '".$nomcat."' salida = '".$nomcat2."' WHERE (idturno = '".$id_viejo."');";
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

