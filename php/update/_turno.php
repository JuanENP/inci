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
    $total_tiempo = RestarHoras($f_i,$f_f);
    actualizar($idturno, $f_i, $f_f, $old_id,$total_tiempo);

    function actualizar($id,$fi,$ff,$id_viejo,$total_horas)
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        require("../../Acceso/global.php");
        mysqli_autocommit($con, FALSE);
        $nombre_host= gethostname();
        if(!(mysqli_query($con,"update turno SET idturno = '".$id."', entrada = '".$fi."', salida='".$ff."', t_horas='$total_horas' WHERE (idturno = '".$id_viejo."');")))
        {
            mysqli_rollback($con);
            mysqli_autocommit($con, TRUE);
            echo "alert('Datos incorrectos del turno'); history.back();";
        }
        else
        {     
            $ejecu="select * from turno where idturno = '$id_viejo'";
            $codigo=mysqli_query($con,$ejecu);
            $resul=mysqli_num_rows($codigo);
            $id_anterior=$resul[0];
            $fi_anterior=$resul[1];
            $ff_anterior=$resul[2];
            $t_horas_anterior=$resul[3];

            if(!(mysqli_query($con,"call inserta_bitacora_turno('Actualizado','$id','$fi','$ff','$total_horas','$id_anterior','$fi_anterior', '$ff_anterior', '$t_horas_anterior', '$nombre_host')")))
            {
                mysqli_rollback($con);
                mysqli_autocommit($con, TRUE); 
                echo "alert('Datos incorrectos en bitacora turno); history.back();";
            }
            else
            {
                mysqli_commit($con);
                mysqli_autocommit($con, TRUE);
                echo "<script> Alerta(); </script>";
            }
        }
    }

    function RestarHoras($horaini,$horafin)
    {
        $f1 = new DateTime($horaini);
        $f2 = new DateTime($horafin);
        $d = $f1->diff($f2);
        return $d->format('%H:%I:%S');
    }
 
    

    
?>