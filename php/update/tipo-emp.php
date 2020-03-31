<?php 
session_start();
?>
<script type="text/javascript">
    function Correcto()
    {
        alert("Actualizado correctamente");
        location.href="./../ht/tipoempleado.php";
        //window.close();
        //Si quieres usar instrucciones php, salte del script y coloca la apertura y cierre de php, escribe dentro de ellas de forma normal
    }
</script>

<?php
   
    $id_anterior=$_POST['id'];
    $nuevo_tipo=$_POST['descripcion'];

    actualizar($nuevo_tipo,$id_anterior);

    function actualizar($nuevo,$id)
    {
        require("../../Acceso/global.php");
        $sql="update tipo SET  descripcion = '".$nuevo."' WHERE (idtipo = '".$id."');";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
        die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
        }
        else
        {
            echo "<script> Correcto(); </script>";
        }
    }
?>

