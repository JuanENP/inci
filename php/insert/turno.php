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
    function RestarHoras($horaini,$horafin)
    {
        $f1 = new DateTime($horaini);
        $f2 = new DateTime($horafin);
        $d = $f1->diff($f2);
        return $d->format('%H:%I:%S');
    }

    $turno=$_POST['turno'];
    $hora_ent=$_POST['entrada'];
    $hora_sal=$_POST['salida'];
    //Aqui consulto si existe un turno igual a la que se va a guardar
    $ejecu="select * from turno where idturno = '$turno'";
    $codigo=mysqli_query($con,$ejecu);
    $consultar=mysqli_num_rows($codigo);
    if($consultar>0)
    {
        echo "<script> Ya_Existe(); </script>";
    }
    else
    {
        $total_tiempo = RestarHoras($hora_ent,$hora_sal);
        mysqli_autocommit($con, FALSE);
        $nombre_host= gethostname();
        if(!(mysqli_query($con,"Insert into turno values ('$turno','$hora_ent','$hora_sal','$total_tiempo')")))
        {
            mysqli_rollback($con);
            mysqli_autocommit($con, TRUE); 
            echo "alert('Datos incorrectos del turno'); history.back();";
         }
        else
        { 
            if(!(mysqli_query($con,"call inserta_bitacora_turno('Guardado','$turno','$hora_ent','$hora_sal','$total_tiempo','-','', '', '', '$nombre_host')")))
            {
                mysqli_rollback($con);
                mysqli_autocommit($con, TRUE); 
                echo "alert('Datos incorrectos en bitacora turno); history.back();";
            }
            else
            {
                mysqli_commit($con);
                mysqli_autocommit($con, TRUE);
                //Guardado correcto
                echo "<script> Correcto(); </script>";
            }
        }
        mysqli_close($con); 
        
    }
   
?>