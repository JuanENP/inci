<?php
session_start();
    if (($_SESSION["name"]) && ($_SESSION["con"]))
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        if($_POST['old_id'] && $_POST['id-turno'] && $_POST['fec-ini'] && $_POST['fec-fin'])
        {
            $old_id=$_POST['old_id'];
            $idturno=$_POST['id-turno'];
            $f_i=$_POST['fec-ini'];
            $f_f=$_POST['fec-fin'];
        }
        else
        {
            header("Location: ../../ht/turnos.php");
        }  
    }
    else
    {
        header("Location: ../../index.html");
        die();
    }
?>
<script type="text/javascript">
    function Alerta()
    {
        alert("Turno modificado correctamente");
        location.href="../../ht/turnos.php";
    }
</script>

<?php
    actualizar($idturno, $f_i, $f_f, $old_id);

    function actualizar($id,$fi,$ff,$id_viejo)
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        require("../../Acceso/global.php");
        $sql="update turno SET idturno = '".$id."', entrada = '".$fi."', salida='".$ff."', t_horas=1 WHERE (idturno = '".$id_viejo."');";
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