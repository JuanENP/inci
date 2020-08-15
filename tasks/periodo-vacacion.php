<?php
    /*
        Ejecutar el 1 de enero y el 1 de julio
    */
    date_default_timezone_set('America/Mexico_City');
    set_time_limit(1200);//Indica que son 1200 segundos, es decir 20 minutos máximo para ejecutar todo el script
    $nombre="biometric";
    $contra="5_w**/pQxcmk.";
    require("../Acceso/global.php");

    $p1=0;
    $p2=0;
    $fecha_hoy=date("Y-m-d");//la fecha de hoy
    $anio=date("Y");//actual year
    $f_ini_p1=$anio.'-01-01';//fecha de inicio del periodo 1
    $f_fin_p1=$anio.'-06-30';//fecha de fin del periodo 1
    $fecha_ac = strtotime($fecha_hoy);
    $fecha_in_p1 = strtotime($f_ini_p1);
    $fecha_fin_p1 = strtotime($f_fin_p1);
    /*
        Si la fecha actual está entre enero y junio, el periodo 1 estará activo (es decir, en 1). 
        Sino estará en 0 y periodo 2 estará en 1
    */
    if($fecha_ac >= $fecha_in_p1 && $fecha_ac <= $fecha_fin_p1 )
    {   
        $p1=1; 
    }
    else 
    { 
        $p2=1; 
    }

    $ids=array();
    $sql="SELECT idvacaciones FROM vacaciones";
    $query= mysqli_query($con, $sql) or die();
    $fila=mysqli_num_rows($query);
    if($fila>0)
    {
        //actualizar los periodos de todos los trabajadores de la tabla vacaciones
        for($i=0;$i<$fila;$i++)
        {
            $renglon = mysqli_fetch_array($query);
            $ID=$renglon[0];
            $ids[$i]=$ID;
        }

        $tot=count($ids);
        for($i=0;$i<$tot;$i++)
        {
            $miID=$ids[$i];
            $sql="UPDATE vacaciones SET val_p1 = '$p1', val_p2 = '$p2'  WHERE idvacaciones = $miID";
            $query= mysqli_query($con, $sql) or die();
        }

        echo "ok";
    }
    else
    {
        exit();
    }
?>