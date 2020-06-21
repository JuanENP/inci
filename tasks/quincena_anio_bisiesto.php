<?php
    date_default_timezone_set('America/Mexico_City');
    $nombre="biometric";
    $contra="5_w**/pQxcmk.";
    require("../Acceso/global.php");
    $month = date('Y-m');
    $aux = date('Y-m-d', strtotime("{$month} + 1 month"));
    $last_day = date('d', strtotime("{$aux} - 1 day"));

    echo "<br>" . "El último día del mes es: {$last_day}";
    if($last_day==29)
    {
        $sql="select fecha_fin from quincena where idquincena = 4";
        $query= mysqli_query($con, $sql) or die();

        $resul=mysqli_fetch_array($query);
        $f_fin_feb=$resul[0];//YYYY-MM-dd
        $porcion_fecha_feb=explode("-",$f_fin_feb);//genera un array de 3 posiciones: [0]=YYYY [1]=MM [2]=dd
        $dia_final_feb=$porcion_fecha_feb[2];
        if($dia_final_feb==28)
        {
            //actualizar la fecha_fin de la quincena 4 a 0000-02-29 si es un año bisiesto
            $sql="UPDATE quincena SET fecha_fin = '0000-02-29' WHERE idquincena = 4";
            $query= mysqli_query($con, $sql) or die();
        }
        
    }


?>