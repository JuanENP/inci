<?php
    $f_hoy=date("Y-m-d");//guardar la fecha actual
    /*  ARTÍCULO 25. CAUSAS DE BAJA DEL TRABAJADOR
        I. Por faltar más de cuatro días consecutivos a sus labores sin causa justificada; para estos efectos, 
        los días de descanso normal de la trabajadora o del trabajador y los establecidos en el Artículo 50 de estas Condiciones no serán tomados en cuenta;
    */
    faltasPorQuincena();
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
                $numero=$resul[0];
                //Revisar que el empleado no exista en la tabla baja
                $yaEstaRegistrado=revisarSiYaEstaRegistradoEnBaja($numero);
                if($yaEstaRegistrado !== true)
                {
                    //Revisar si su tipo de empleado es de base
                    $tipoEmp=obtenerTipoDeEmpleado($numero);
                    if($tipoEmp == 2)
                    {
                        //Revisar si el empleado si tiene 5 faltas consecutivas en la quincena guardarlo en la tabla baja, si la función arroja 1 deberá ser dado de baja
                        $totalFaltas=revisarSiDarDeBajaAlTrabajador($numero);
                        if($totalFaltas >= 5) 
                        {  echo $numero;
                            $realizado=insertarBaja($totalFaltas,'dias consecutivos en la quincena',$numero); 
                        }  
                    }
                }
            }
        }
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
        //Obtener la quincena actual
        $sql1="select idquincena from quincena where validez=1;";
        $query1= mysqli_query($con, $sql1);
        $filas=mysqli_num_rows($query1);
        if($query1)
        {
            if($filas==1)
            {
                $resul=mysqli_fetch_array($query1);
                return $resul[0];
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

    function revisarSiDarDeBajaAlTrabajador($numero)
    {
        global $f_hoy;
        global $con;
        $arregloFaltas=array();
        $c=0;
        $aumentaSiConsecutivo=1;
        $quincena=quincenaActual();
        //Obtener todas las faltas del trabajador
        $sql1="select fecha from falta a where quincena=$quincena and trabajador_trabajador='$numero'
        and not exists (SELECT b.idjustificar_falta FROM justificar_falta b where b.falta_falta = a.idfalta);";
        $query1= mysqli_query($con, $sql1);
        $filas=mysqli_num_rows($query1);
        if($filas>0)
        {
            while($resul=mysqli_fetch_array($query1))
            {   
                $arregloFaltas[$c][0]=$resul[0];
                $c++;
            }
        } 
        
        $totalArregloFaltas=count($arregloFaltas);   
        //Saber si son días consecutivos
        for($i=0;$i<$totalArregloFaltas;$i++)
        {   
            $pivote=$arregloFaltas[$i][0]; 
            $nuevoPivote=date("Y-m-d",strtotime($pivote."+ 1 days"));
            for($j=$i+1;$j<$c;$j++)
            { 
                if($nuevoPivote==$arregloFaltas[$j][0])
                {
                    $aumentaSiConsecutivo++;
                    $j=$c;
                }
            } 
        }
        return $aumentaSiConsecutivo;
    }

    function insertarBaja($t_dias,$motivo,$numEmpleado)
    {
        global $f_hoy;
        global $con;
        $quincena=quincenaActual();
        $sql1="INSERT INTO bajas (fecha, t_dias, motivo, trabajador_trabajador, quincena, baja_definitiva) VALUES ('$f_hoy', $t_dias, '$motivo', '$numEmpleado','$quincena','0');";
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