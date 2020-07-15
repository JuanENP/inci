<?php
    date_default_timezone_set('America/Mexico_City');
    set_time_limit(600);//Indica que son 600 segundos, es decir 10 minutos máximo para ejecutar todo el script 
    $nombre="biometric";
    $contra="5_w**/pQxcmk.";
    require("../Acceso/global.php");

   //OBTENER QUE DÍA ES HOY
   $dias = array("domingo","lunes","martes","miercoles","jueves","viernes","sabado");
   //echo "HOY ES ".$dias[date("w")] . "<br>";//esto es solo para ver si el día es correcto
   $diaactual=$dias[date("w")];//guardar el día actual para su posterior uso
   $f_hoy=date("Y-m-d");//guardar la fecha actual

    faltasPorQuincena();
    /*  ARTÍCULO 25. CAUSAS DE BAJA DEL TRABAJADOR
        I. Por faltar más de cuatro días consecutivos a sus labores sin causa justificada; para estos efectos, 
        los días de descanso normal de la trabajadora o del trabajador y los establecidos en el Artículo 50 de estas Condiciones no serán tomados en cuenta;
        II. Por acumular seis faltas, sin aviso ni causa justificada, en treinta días hábiles;
    */
    function faltasPorQuincena()
   {
        global $f_hoy;
        global $con;

        $sql1="select numero_trabajador from trabajador;";
        $query1= mysqli_query($con, $sql1);
        $filas=mysqli_num_rows($query1);
        if($filas>0)
        {
            while($resul=mysqli_fetch_array($query1))
            {
                $num=$resul[0];
                $totalFaltas=contarfaltasQuincena($num);
                if($totalFaltas !== null)
                {  
                    // cinco faltas distribuidas en una quincena ameritan dar de baja al empleado.
                    //pendiente saber a que trabajdores son los dioses y no se le debe perjudicar con las faltas
                    if($totalFaltas == 5) 
                    {
                        echo '<br>'.$num.'  '.$totalFaltas.'<br>';
                        $realizado=insertarBaja($totalFaltas,'quincena',$num);
                    }
                }
                
            }
        }
    }

    function quincenaActual()
    {
        global $f_hoy;
        global $con;
        $sql1="select * from quincena where validez=1;";
        $query1= mysqli_query($con, $sql1);
        $filas=mysqli_num_rows($query1);
        if($filas==1)
        {
            $resul=mysqli_fetch_array($query1);
            return[ $resul[0],$resul[1],$resul[2]];
        }
        else
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $hacer='buscar la quincena actual, es decir que tenga validez 1, ';
            $tabla='quincena';
            $línea='50';
            error($er1,$er2,$hacer,$tabla,$línea);
        }
    }

    function contarfaltasQuincena($numEmpleado)
    {
        global $f_hoy;
        global $con;
        $quincena=quincenaActual();
        if($quincena !== null)
        { 
            $sql1="select count(idfalta) from falta where quincena=$quincena[0] and trabajador_trabajador='$numEmpleado'
            and not exists (SELECT b.idjustificar_falta FROM justificar_falta b where b.falta_falta = a.idfalta);";
            $query1= mysqli_query($con, $sql1) or die();
            $filas=mysqli_num_rows($query1);
            if($filas==1)
            {
                $resul=mysqli_fetch_array($query1);
                return $resul[0];
            }
            else
            {
                return null;
            }
        }
    }

    function insertarBaja($t_dias,$motivo,$numEmpleado)
    {
        global $f_hoy;
        global $con;
        $quincena=quincenaActual();
        $sql1="INSERT INTO bajas (fecha, t_dias, motivo, trabajador_trabajador, quincena, dias_para_baja, baja_definitiva) VALUES ('$f_hoy', $t_dias, '$motivo', '$numEmpleado','$quincena[0]','','NO');";
        $query1= mysqli_query($con, $sql1);
        if($query1)
        {
            return true;
        }
        else
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $hacer='insertar';
            $tabla='bajas';
            $línea='99';
            error($er1,$er2,$hacer,$tabla,$línea);
        }
    }

    function error($er1,$er2,$accion,$nomTabla,$numLinea)
    {
        $error="";
        $err1="$er1";
        $err2="$er2";
        //Hacer UN EXPLODE DE ERR2
        $divide=explode("'",$err2);
        $tamDivide=count($divide);//saber el tamaño del array
        if($tamDivide>0)//si el array posee datos
        {
            $err2="";
            for($i=0;$i<$tamDivide;$i++)
            {
                $err2.=$divide[$i];
            }
        }

        $error="Error al $accion en la tabla $nomTabla. $err1 : $err2. Línea de error: $numLinea. Tarea contador faltas quincena.";
        echo"<script> console.error('$error'); </script>";
        exit();
    }

?>