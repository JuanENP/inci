<?php
    date_default_timezone_set('America/Mexico_City');
    set_time_limit(600);//Indica que son 600 segundos, es decir 10 minutos máximo para ejecutar todo el script 
    $nombre="biometric";
    $contra="5_w**/pQxcmk.";
    require("../Acceso/global.php");
    $f_hoy=date("Y-m-d");//guardar la fecha actual
    /*  ARTÍCULO 25. CAUSAS DE BAJA DEL TRABAJADOR
        II. Por acumular seis faltas, sin aviso ni causa justificada, en treinta días hábiles;
    */
    faltasPorMes();
    function faltasPorMes()
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
                $numero=$resul[0];
                //Revisar que el empleado no exista en la tabla baja
                $yaEstaRegistrado=revisarSiYaEstaRegistradoEnBaja($numero);
                if($yaEstaRegistrado !== true)
                {
                    $totalFaltas=contarfaltasMes($numero);
                    // Seis faltas distribuidas en 30 días hábiles ameritan dar de baja al empleado.
                    if($totalFaltas >= 6) 
                    {  
                        $realizado=insertarBaja($totalFaltas,'dias distribuidos en 30 días hábiles',$numero);
                    }
                }
            }
        }
    }

    function obtenerTipoDeEmpleado($numero)
    {
        global $con;
        $sql1="SELECT tipo_tipo from trabajador where numero_trabajador='$numero';";
        $query1= mysqli_query($con, $sql1);
        $filas=mysqli_num_rows($query1);
        if($filas==1)
        {
            $resul=mysqli_fetch_array($query1);
            return $resul[0];
        }
    }

    function quincenaActual()
    {
        global $f_hoy;
        global $con;
        $sql1="select * from quincena where validez=1;";
        $query1= mysqli_query($con, $sql1);
        $filas=mysqli_num_rows($query1);
        if($query1)
        {
            if($filas==1)
            {
                $resul=mysqli_fetch_array($query1);
                return[ $resul[0],$resul[1],$resul[2]];
            }
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

    function contarfaltasMes($numero)
    {
        global $f_hoy;
        global $con;
        $totalFaltas=0;
        $diasHabiles=calcular30diasHabiles();
        $contador=count($diasHabiles);
        //Revisar si las fechas de las faltas fueron en los 30 días hábiles
        for($i=0;$i<$contador;$i++)
        {
            $fecha=$diasHabiles[$i][0];
            $siFalta=faltaEnFechaEspecifica($fecha,$numero);//Revisar si el empleado tiene falta en un día hábil
            if($siFalta == 1) //Si tiene una falta solo aumentar 1
            {
                $totalFaltas+=1;
            }  
            if($siFalta == 2) //Si tiene dos faltas el mismo días
            {
                $totalFaltas+=2;
            }    
            if($siFalta == 4) //Si tiene 4 faltas el mismo días
            {
                $totalFaltas+=4;
            }       
        }
        return $totalFaltas;
    }

    function faltaEnFechaEspecifica($fecha,$numero)
    {
        global $con;
        $sql1="select fecha from falta a where trabajador_trabajador='$numero' and fecha='$fecha' and not exists (SELECT b.idjustificar_falta FROM justificar_falta b where b.falta_falta = a.idfalta);";
        $query1= mysqli_query($con, $sql1);
        if($query1)
        {
            $filas=mysqli_num_rows($query1);
            if($filas==1)
            {
               return 1;
            }
            else
            {
                if($filas==2)
                {
                   return 2;
                }
                else
                {
                    if($filas==4)
                    {
                       return 4;
                    }
                    return 0;
                }
            }
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

    function calcular30diasHabiles()
    {
        global $con;
        global $f_hoy;
        $feriado=false; 
        $mod_dia=$f_hoy;
        $diasFeriados=array();//para guardar los días feriados de mi BD
        $pos=0;
        $diashabiles=array();//para guardar los días hábiles: no sabado y domingo ni días festivos
        $contador=0; //contador solo para cuando genero el arreglo de dias para ser pulido a días habiles
        $sql="SELECT fecha from dia_festivo";
        $query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
        $filas = mysqli_num_rows($query);//obtener las filas del query
        //Si el query no está vacío
        if($filas>0)
        {
            while($resul=mysqli_fetch_array($query))
            { 
                $diasFeriados[$pos]=$resul[0];//Guardar el día feriado correspondiente en el array
                $pos++;//aumentar la posición del array
            }
        }
    
        for($i=0;$i<50;$i++) //Son 50 días porque no puede haber 50 días festivos en un mes
        {
            $diaIngles= date("l",strtotime($mod_dia));//El día en inglés que cae al sumarle 1 día a la fecha de inicio
            // $fechaCompleta= date("Y-m-d",$mod_dia);//El día completo que cae al sumarle 1 día a la fecha de inicio
            $feriado=false;
            //Buscar si fechaCompleta está en el array o no
            for($j=0;$j<$pos;$j++)
            {
                if($mod_dia==$diasFeriados[$j])
                {
                    $feriado=true;
                    $j=$pos-1;//romper y salir el cucle
                }//fin if for j<pos
            }//fin del for que evalua el array
            if(($diaIngles=="Monday" || $diaIngles=="Tuesday" || $diaIngles=="Wednesday" || $diaIngles=="Thursday" || $diaIngles=="Friday") && $feriado==false)
            {
                $diashabiles[$contador][0]=$mod_dia;
                $contador++;
                if($contador==30) //30 días hábiles
                {
                    $i=50;//sirve para romper el bucle principal
                }
            }//fin del if
            $mod_dia = strtotime($mod_dia."- 1 days");//sumar 1 día
            $mod_dia=date("Y-m-d",$mod_dia);//formatea la fecha y la vuelve a una fecha normal
        }//fin del for i<50
        return  $diashabiles;
    } 

    function revisarSiYaEstaRegistradoEnBaja($numero)
    {
        global $con;
        $sql1="SELECT idbajas from bajas where trabajador_trabajador='$numero';";
        $query1= mysqli_query($con, $sql1);
        $filas=mysqli_num_rows($query1);
        if($filas==1)
        {
            return true;
        }
        else
        {
            return null;
        }
    }

    function insertarBaja($t_dias,$motivo,$numEmpleado)
    {
        global $f_hoy;
        global $con;
        $dato=quincenaActual(); 
        $quincena=$dato[0];
        $sql1="INSERT INTO bajas (fecha, t_dias, motivo, trabajador_trabajador, quincena,baja_definitiva) VALUES ('$f_hoy', $t_dias, '$motivo', '$numEmpleado','$quincena','0');";
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