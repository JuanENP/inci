<?php
    date_default_timezone_set('America/Mexico_City');
    function consultaTrabajador($myid)
    {
        global $con;
        $sql="select * from trabajador where numero_trabajador = '".$myid."'";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
            die("<br>" . "Error línea 6 al consultar trabajador: " . mysqli_errno($con) . " : " . mysqli_error($con));
        }
        else
        { 
            $resul=mysqli_fetch_array($query);
            //retornar este array
            return
            [
                $resul[0],$resul[1],$resul[2],$resul[3],$resul[4],$resul[5],$resul[6],$resul[7]
            ];
        }
    }

    function consultaCumple($myid)
    {
        global $con;
        //Consultar todo de la tabla cumpleaños de tal trabajador
        $sql="select * from cumple_ono where trabajador_trabajador = '".$myid."'";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
            die("<br>" . "Error, línea 27: " . mysqli_errno($con) . " : " . mysqli_error($con).", no hay datos en la tabla cumple u onomástico, verifique con el aministrador de sistemas.");
        }
        else
        { 
            $resul=mysqli_fetch_array($query);
            //retornar este array
            return
            [ $resul[1],$resul[2],$resul[0],$resul[3]];//fecha cumple, fecha ono, id, validez
        }
    
    }

    function consultaAcceso($myid)
    {
        global $con;
        //Consultar todo de la tabla acceso
        $sql="select * from acceso where trabajador_trabajador = '".$myid."'";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
             die("<br>" . "Error, línea 47: " . mysqli_errno($con) . " : " . mysqli_error($con).", no hay datos en la tabla acceso, verifique con el administrador de sistemas.");
        }
        else
        { 
            $resul=mysqli_fetch_array($query);
            //retornar este array
            return
            [ $resul[1],$resul[2],$resul[3],$resul[4],$resul[5],$resul[6],$resul[7],$resul[8],$resul[9] ];
        }
    }

    function consultaTServicio($myid)
    {
        global $con;
        //Consultar todo de la tabla acceso
        $sql="select * from tiempo_servicio where trabajador_trabajador = '$myid'";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
            die("<br>" . "Error, línea 66: " . mysqli_errno($con) . " : " . mysqli_error($con).", no hay datos en la tabla tiempo de servicio, verifique con el administrador de sistemas.");
        }
        else
        { 
            $resul=mysqli_fetch_array($query);
            //retornar este array
            return
            [ $resul[1],$resul[0] ];
        }
    }

    function consultaGenero($myid)
    {
        global $con;
        //Consultar todo de la tabla acceso
        $sql="select genero from trabajador where numero_trabajador = '".$myid."'";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
             die("<br>" . "Error, línea 85: " . mysqli_errno($con) . " : " . mysqli_error($con).", no hay genero registrado en la tabla trabajador, verifique con el administrador de sistemas.");
        }
        else
        { 
            $resul=mysqli_fetch_array($query);
            //retornar este array
            return
            [ $resul[0]];
            
        }
    }

    function describeTipoTrabajador($myid,$numTipo)
    {
        global $con;
        // OBTENER LA DESCRIPCION DEL TIPO DE EMPLEADO PARA VERLO EN LA BITACORA
        $sql="SELECT descripcion  FROM trabajador inner join tipo on idtipo=tipo_tipo and tipo_tipo=$numTipo;";
        $query= mysqli_query($con, $sql);
        if($query)
        {
            $fila=mysqli_fetch_array($query);
            return $fila[0];
        }
        else
        {
            die("<br>" . "Error, línea 105: " . mysqli_errno($con) . " : " . mysqli_error($con).", no hay descripcion del tipo de trabajador, verifique con el administrador de sistemas.");
        }
    }

    function describeTipoEmpleado($numTipo)
    {
        global $con;
        // OBTENER LA DESCRIPCION DEL TIPO DE EMPLEADO PARA VERLO EN LA BITACORA
        $sql="select descripcion from tipo where idtipo=$numTipo;";
        $query= mysqli_query($con, $sql);
        if($query)
        {
            $fila=mysqli_fetch_array($query);
            return $fila[0];
        }
        else
        {
            die("<br>" . "Error, línea 122: " . mysqli_errno($con) . " : " . mysqli_error($con).", no hay descripcion del tipo de trabajador, verifique con el administrador de sistemas.");
        }
    }

    function consultaEspecial($myid)
    {
        global $con;
        //Consultar todo de la tabla acceso
        $sql="SELECT *  FROM especial WHERE trabajador_trabajador='".$myid."'";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
             die("<br>" . "Error, línea 139: " . mysqli_errno($con) . " : " . mysqli_error($con).", no hay datos en tabla especial, verifique con el administrador de sistemas");
        }
        else
        { 
            $resul=mysqli_fetch_array($query);
            //retornar este array
            return
            [ $resul[0],$resul[1],$resul[2],$resul[3],$resul[4],$resul[5],$resul[6],$resul[7],$resul[8],$resul[9] ];
        }
    }

    //Función que verifica si el trabajador es mayor de edad
    function esMayorEdad($fecha_nac)
    {   
        $fecha_actual=date('Y-m-d');
        $fecha_nacimiento = new DateTime($fecha_nac);
        $hoy = new DateTime($fecha_actual);
        $interval= $hoy->diff($fecha_nacimiento);
        $totalAnios=$interval->format('%y');
        if($totalAnios>=18)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function compararAnio($anioAlta)
    {  
        $anioActual=date('Y');
        $actual=strtotime($anioActual);
        $alta= strtotime($anioAlta);
        if($alta<=$actual)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function insertaUsuario($nombreUsuario)
    {
        global $con;
        $contrasenaUsuario='9999';
        $sql="CREATE USER '$nombreUsuario'@'localhost' IDENTIFIED BY '$contrasenaUsuario';"; 
        $sql2="GRANT ALL PRIVILEGES ON *.* TO '$nombreUsuario'@'localhost' WITH GRANT OPTION;"; 
        //$sqlFlush="FLUSH PRIVILEGES;";//despues del 2 

        mysqli_autocommit($con, FALSE);//quitar el autocommit hasta que todo haya resultado correcto
        if(!(mysqli_query($con,$sql)))
        {
            $error="";
            $er1=mysqli_errno($con);
            $err1="$er1";
            $er2=mysqli_error($con);
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

            $error="Error in create. $err1 : $err2. Este error suele surgir cuando el usuario que intenta registrar ya existe, verifique. En caso de que no sea ese el problema contacte al administrador. Línea de error:680.";
            echo "<script> error('$error'); </script>";
            mysqli_rollback($con);
            mysqli_autocommit($con, TRUE); 
        }
        else
        {
           /* if(!(mysqli_query($con,$sqlFlush)))
            {
                $error="error in flush 1 ".mysqli_errno($con) . ": " . mysqli_error($con);
                echo "<script> error('$error'); </script>";
                mysqli_rollback($con);
                mysqli_autocommit($con, TRUE);
                return false;
            }
            else
            */
            {
                if(!(mysqli_query($con,$sql2)))
                {
                    $error="error in grant 1 ".mysqli_errno($con) . ": " . mysqli_error($con);
                    echo "<script> error('$error'); </script>";
                    mysqli_rollback($con);
                    mysqli_autocommit($con, TRUE); 
                }
                else
                {
                   /* if(!(mysqli_query($con,$sqlFlush)))
                    {
                        $error="error in flush 2 ".mysqli_errno($con) . ": " . mysqli_error($con);
                        echo "<script> error('$error'); </script>";
                        mysqli_rollback($con);
                        mysqli_autocommit($con, TRUE);
                    }
                    else*/
                    {
                        //mysqli_commit($con);
                        //mysqli_autocommit($con, TRUE);
                        return true;
                    }
                }
            }
        }
    }

    function tieneSexta($myid)
    {
        global $con;
        //Consultar todo de la tabla acceso
        $sql="SELECT * FROM sexta WHERE trabajador_trabajador='".$myid."'";
        $query= mysqli_query($con, $sql);
        $fila=mysqli_num_rows($query);
        if($fila==0)
        {
            return false;
        }
        else
        { 
            $resul=mysqli_fetch_array($query);
            //retornar este array
            return
            [ $resul[0],$resul[1],$resul[2],$resul[3],$resul[4],$resul[5],$resul[6],$resul[7],$resul[8],$resul[9],$resul[10],$resul[11]];    

        }
    }

    function consultaMail($myid)
    {
        global $con;
        //Consultar todo de la tabla cumpleaños de tal trabajador
        $sql="select idmail from mail where trabajador_trabajador = '".$myid."'";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
            die("<br>" . "Error, línea 283: " . mysqli_errno($con) . " : " . mysqli_error($con).", no hay datos en la tabla cumple u onomástico, verifique con el aministrador de sistemas.");
        }
        else
        { 
            $filas=mysqli_num_rows($query);
            if($filas==1)
            {
                $resul=mysqli_fetch_array($query);
                //retornar este array
                return $resul[0];
            }
            else
            {
                return false;
            }
            
        }
    
    }

?>
