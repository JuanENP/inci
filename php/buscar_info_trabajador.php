<?php
    date_default_timezone_set('America/Mexico_City');
    function consultaTrabajador($myid)
    {
        global $con;
        $sql="select * from trabajador where numero_trabajador = '".$myid."'";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
            die("<br>" . "Error línea 11 al consultar trabajador: " . mysqli_errno($con) . " : " . mysqli_error($con));
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
            die("<br>" . "Error, línea 31: " . mysqli_errno($con) . " : " . mysqli_error($con).", no hay datos en la tabla cumple u onomástico, verifique con el aministrador de sistemas.");
        }
        else
        { 
            $resul=mysqli_fetch_array($query);
            //retornar este array
            return
            [ $resul[1],$resul[2],$resul[0]];
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
        $sql="select * from tiempo_servicio where trabajador_trabajador = '".$myid."'";
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
        $filas=mysqli_num_rows($query);
        if($filas>0)
        {
            $fila=mysqli_fetch_array($query);
            return $fila[0];
        }
        else
        {
            die("<br>" . "Error, línea 105: " . mysqli_errno($con) . " : " . mysqli_error($con).", no hay descripcion del tipo de trabajador, verifique con el administrador de sistemas.");
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
             die("<br>" . "Error, línea 123: " . mysqli_errno($con) . " : " . mysqli_error($con).", no hay datos en tabla especial, verifique con el administrador de sistemas");
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
        $sql="create user '$nombreUsuario'@'localhost' identified by '$contrasenaUsuario'"; 
        $sql2="grant all privileges on checada6.* to '$nombreUsuario'@localhost"; 
        $sql3="grant all privileges on mysql.user to '$nombreUsuario'@localhost";
        $sqlFlush="flush privileges";//despues del 2 y del 3

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
            $tamDivide=count($divide);//saber el tama�o del array
            if($tamDivide>0)//si el array posee datos
            {
                $err2="";
                for($i=0;$i<$tamDivide;$i++)
                {
                    $err2.=$divide[$i];
                }
            }

            $error="Error in create. $err1 : $err2. Este error suele surgir cuando el usuario que intenta registrar ya existe, verifique. En caso de que no sea ese el problema contacte al administrador. L�neas de error: 16, 41 y 42.";
            echo "<script> imprime('$error'); </script>";
            mysqli_rollback($con);
            mysqli_autocommit($con, TRUE); 
            return false;
        }
        else
        {
            if(!(mysqli_query($con,$sql2)))
            {
                echo "error in grant 1 ".mysqli_errno($con) . ": " . mysqli_error($con);
                mysqli_rollback($con);
                mysqli_autocommit($con, TRUE); 
                return false;
            }
            else
            {
                if(!(mysqli_query($con,$sqlFlush)))
                {
                    echo "error in flush 1 ".mysqli_errno($con) . ": " . mysqli_error($con);
                    mysqli_rollback($con);
                    mysqli_autocommit($con, TRUE);
                    return false; 
                }
                else
                {
                    if(!(mysqli_query($con,$sql3)))
                    {
                        echo "error in grant 2".mysqli_errno($con) . ": " . mysqli_error($con);
                        mysqli_rollback($con);
                        mysqli_autocommit($con, TRUE); 
                        return false;
                    }
                    else
                    {
                        if(!(mysqli_query($con,$sqlFlush)))
                        {
                            echo "error in flush 2 ".mysqli_errno($con) . ": " . mysqli_error($con);
                            mysqli_rollback($con);
                            mysqli_autocommit($con, TRUE); 
                            return false;
                        }
                        else
                        {
                            mysqli_commit($con);
                            mysqli_autocommit($con, TRUE);
                            return true;
                        }
                    }
                }
            }
        }
    }

    function tieneSexta($myid)
    {
        global $con;
        //Consultar todo de la tabla acceso
        $sql="SELECT *  FROM sexta WHERE trabajador_trabajador='".$myid."'";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
            //die("<br>" . "Error, línea 261: " . mysqli_errno($con) . " : " . mysqli_error($con).", no hay datos en tabla sexta, verifique con el administrador de sistemas");
            return false;
        }
        else
        { 
            $fila=mysqli_num_rows($query);
            if($fila==1)
            {
                $resul=mysqli_fetch_array($query);
                //retornar este array
                return
                [ $resul[0],$resul[1],$resul[2],$resul[3],$resul[4],$resul[5],$resul[6],$resul[7],$resul[8],$resul[9],$resul[10],$resul[11],$resul[12]];    
            }
            else
            {
                return false;
            }
        }
    }
?>
