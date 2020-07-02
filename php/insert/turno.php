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
        header("Location: ../../index.php");
        die();
    }
    
    function RestarHoras($horaini,$horafin)
    {
        $f1 = new DateTime($horaini);
        $f2 = new DateTime($horafin);
        $d = $f1->diff($f2);
        return $d->format('%H:%I:%S');
    }

    function insertaEnBitacoraTurno($operacion,$idturno_new,$entrada_new,
    $salida_new, $thoras_new, $idturno_old,$entrada_old, $salida_old, $thoras_old)
    {
        global $con;
        $host=gethostname();
        //GUARDAR EN LA BITACORA DE turno
        if(!(mysqli_query($con,"call inserta_bitacora_turno('$operacion','$idturno_new','$entrada_new',
        '$salida_new', '$thoras_new', '$idturno_old','$entrada_old', '$salida_old', '$thoras_old','$host')")))
        {
            echo mysqli_errno($con) . ": " . mysqli_error($con) . "\n";
            return 1;//algo salió mal
        }
        else
        {
            return 0; //ok
        }
    }//FIN de insertaEnBitacoraJustificarFalta

    $turno=$_POST['turno'];
    $hora_ent=$_POST['entrada'];
    $hora_sal=$_POST['salida'];
    //Aqui consulto si existe un turno igual a la que se va a guardar
    $ejecu="select * from turno where idturno = '$turno'";
    $codigo=mysqli_query($con,$ejecu);
    $consultar=mysqli_num_rows($codigo);
    if($consultar>0)
    {
        echo "<script> alert('Este turno ya existe, verifique.'); history.back(); </script>";
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
            $valor=insertaEnBitacoraTurno("Guardado",$turno,$hora_ent,
            $hora_sal, $total_tiempo, "-","-", "-", "-");
            if($valor==1)
            {
                mysqli_rollback($con);
                mysqli_autocommit($con, TRUE);
                echo "<script> alert('Datos incorrectos en la bitácora, reintente...'); history.back(); </script>";
            }
            else
            {
                mysqli_commit($con);
                mysqli_autocommit($con, TRUE);
                //Guardado correcto
                echo "<script> alert('Turno guardado correctamente'); history.back(); </script>";
            }
        }
        mysqli_close($con); 
    }
?>