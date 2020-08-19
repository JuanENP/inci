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
                $resul[0],$resul[1],$resul[2],$resul[3],$resul[4],$resul[5],$resul[6],$resul[7],$resul[8],
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
        {   $fila=mysqli_num_rows($query);
            if($fila==1)
            {
                $resul=mysqli_fetch_array($query);
                //retornar este array
                return
                [ $resul[1],$resul[2],$resul[0],$resul[3]];//fecha cumple, fecha ono, id, validez
            }
            else
            {
                return null;
            }
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
            die("<br>" . "Error, línea 54: " . mysqli_errno($con) . " : " . mysqli_error($con).", no hay datos en la tabla acceso, verifique con el administrador de sistemas.");
        }
        else
        {   $fila=mysqli_num_rows($query);
            if($fila==1)
            {
                $resul=mysqli_fetch_array($query);
                //retornar este array
                return
                [ $resul[1],$resul[2],$resul[3],$resul[4],$resul[5],$resul[6],$resul[7],$resul[8],$resul[9],$resul[0] ];
            }
            else
            {
                return "";
            }
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
            die("<br>" . "Error, línea 80: " . mysqli_errno($con) . " : " . mysqli_error($con).", no hay datos en la tabla tiempo de servicio, verifique con el administrador de sistemas.");
        }
        else
        { 
            $fila=mysqli_num_rows($query);
            if($fila==1)
            {
                $resul=mysqli_fetch_array($query);
                //retornar este array
                return
                [ $resul[1],$resul[0] ];
            }
            else
            {
                return null;
            }
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
            die("<br>" . "Error, línea 107: " . mysqli_errno($con) . " : " . mysqli_error($con).", no hay genero registrado en la tabla trabajador, verifique con el administrador de sistemas.");
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
            $fila=mysqli_num_rows($query);
            if($fila==1)
            {
                $resul=mysqli_fetch_array($query);
                return $resul[0];
            }
            else
            {
                return "";
            }
        }
        else
        {
            die("<br>" . "Error, línea 127: " . mysqli_errno($con) . " : " . mysqli_error($con).", no hay descripcion del tipo de trabajador, verifique con el administrador de sistemas.");
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
            $fila=mysqli_num_rows($query);
            if($fila==1)
            {
                $resul=mysqli_fetch_array($query);
                return $resul[0];
            }
            else
            {
                return "";
            }
        }
        else
        {
            die("<br>" . "Error, línea 145: " . mysqli_errno($con) . " : " . mysqli_error($con).", no hay descripcion del tipo de trabajador, verifique con el administrador de sistemas.");
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
             die("<br>" . "Error, línea 177: " . mysqli_errno($con) . " : " . mysqli_error($con).", no hay datos en tabla especial, verifique con el administrador de sistemas");
        }
        else
        { 
            $fila=mysqli_num_rows($query);
            if($fila==1)
            {
                $resul=mysqli_fetch_array($query);
                return
                [ $resul[0],$resul[1],$resul[2],$resul[3],$resul[4],$resul[5],$resul[6],$resul[7],$resul[8],$resul[9] ];
    
            }
            else
            {
                return "";
            }
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
        //Sirve para registar un usuario trabajador
        global $con;
        $contrasenaUsuario='9999';
        $sql1="CREATE USER '$nombreUsuario'@'localhost' IDENTIFIED BY '$contrasenaUsuario';"; 
        $sql2="GRANT INSERT, UPDATE ON checada6.mail TO '$nombreUsuario'@'localhost';"; 
        $sql3="GRANT SELECT ON checada6.* TO '$nombreUsuario'@'localhost';"; 
        $sql4="GRANT CREATE USER ON *.* TO  '$nombreUsuario'@'localhost';"; 
        $sql5="GRANT ALL PRIVILEGES ON mysql.user TO '$nombreUsuario'@'localhost';"; 
        $sqlFlush="FLUSH PRIVILEGES;";
        // mysqli_autocommit($con, FALSE);//quitar el autocommit hasta que todo haya resultado correcto
        if(!(mysqli_query($con,$sql1)))
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
            if(!(mysqli_query($con,$sql2)))
            {
                $error="error in grant 1 ".mysqli_errno($con) . ": " . mysqli_error($con);
                echo "<script> error('$error'); </script>";
                mysqli_rollback($con);
                mysqli_autocommit($con, TRUE); 
            }
            else
            {
                if(!(mysqli_query($con,$sql3)))
                {
                    $error="error in grant 2 ".mysqli_errno($con) . ": " . mysqli_error($con);
                    echo "<script> error('$error'); </script>";
                    mysqli_rollback($con);
                    mysqli_autocommit($con, TRUE); 
                }
                else
                {
                    if(!(mysqli_query($con,$sql3)))
                    {
                        $error="error in grant 3 ".mysqli_errno($con) . ": " . mysqli_error($con);
                        echo "<script> error('$error'); </script>";
                        mysqli_rollback($con);
                        mysqli_autocommit($con, TRUE); 
                    }
                    else
                    {  
                        if(!(mysqli_query($con,$sql4)))
                        {
                            $error="error in grant 4 ".mysqli_errno($con) . ": " . mysqli_error($con);
                            echo "<script> error('$error'); </script>";
                            mysqli_rollback($con);
                            mysqli_autocommit($con, TRUE); 
                        }
                        else
                        { 
                            if(!(mysqli_query($con,$sqlFlush)))
                            {
                                $error="error in flush".mysqli_errno($con) . ": " . mysqli_error($con);
                                echo "<script> error('$error'); </script>";
                                mysqli_rollback($con);
                                mysqli_autocommit($con, TRUE);
                            }
                            else
                            {
                               /* mysqli_commit($con);
                                mysqli_autocommit($con, TRUE);*/
                                return true;
                            }
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
            die("<br>" . "Error, línea 331: " . mysqli_errno($con) . " : " . mysqli_error($con).", no hay datos en la tabla cumple u onomástico, verifique con el aministrador de sistemas.");
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

    function buscarSiExisteNip($nip)
    {
        global $con;
        $sql="select nip from trabajador where nip=$nip;"; 
        $query=mysqli_query($con,$sql);
        if(!$query)
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $línea='356';
            error($er1,$er2,$línea);
            mysqli_rollback($con);
            mysqli_autocommit($con, TRUE); 
        }
        else
        {
            $fila=mysqli_num_rows($query);
            if($fila==1)
            {
                return true;
            }
            else
            {
                return null;
            }
        } 
    }

    function consultaTurnoOpcional($myid)
    {
        global $con;
        $sql="select idt_op from t_op where trabajador_trabajador = '".$myid."'";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
            die("<br>" . "Error línea 384 al consultar trabajador: " . mysqli_errno($con) . " : " . mysqli_error($con));
        }
        else
        { 
            $fila=mysqli_num_rows($query);
            if($fila==1)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }

    function validaTurnoOpcional($numero, $categoria, $t_horas, $acceso)
    {
        global $con;
        $categorias=array();
        $contador=0;
        $posibleTurnoOpcional=0;
        $error='';
        //Seleccionar las categorias que pueden tener turno opcional
        if($acceso=="11111000")
        {
            $sql="SELECT categoria_categoria FROM t_opcional;"; 
            $query=mysqli_query($con,$sql);
            $fila=mysqli_num_rows($query);
            if($fila>0)
            {
                while($resul=mysqli_fetch_array($query))
                {   
                    $categorias[$contador]=$resul[0];
                    $contador++;
                }
            }
            /*
            $sql="SELECT entrada,salida,t_horas FROM turno where idturno='$turno';"; 
            $mysql=mysqli_query($con,$sql);
            $fila=mysqli_num_rows($query);
            if($fila==1)
            {
                $resul=mysqli_fetch_array($query);
               echo $entrada=$resul[0];
                $salida=$resul[1];
               echo $t_horas=$resul[2];
            }
            */
            for($i=0;$i<$contador;$i++)
            {
                if($categoria==$categorias[$i])
                {
                    $posibleTurnoOpcional=1;
                    $i=$contador;
                }
            }

            if($posibleTurnoOpcional==1)
            {
                if($t_horas !== '09:00:00') 
                {
                    $error.="Para poder tener turno opcional es necesario tener un horario con un total de 09:00:00 horas. No un turno con $t_horas horas. ";
                }
            }
            else
            {
                $error.="La categoría $categoria seleccionada no puede tener turno opcional. ";
            }
        }
        else
        {
            $error.="Para tener turno opcional los días de trabajo deben ser de lunes a viernes. ";
        }
        if (!empty($error))
        {
            echo"<script>error('$error'); </script>";
            exit();
        }
        else
        {
            return true;
        }
    }

    function insertaTurnoOpcional($numero)
    {
        global $con;
        $sql="INSERT INTO t_op (trabajador_trabajador) VALUES ('$numero');"; 
        if(!(mysqli_query($con,$sql)))
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $línea='478';
            error($er1,$er2,$línea);
            mysqli_rollback($con);
            mysqli_autocommit($con, TRUE); 
        }
        else
        {
            return true;
        } 
    }

    function insertaEnAF($idacceso)
    {   /*
            Se guardar en af (acceso festivo) si el empleado trabaja sabado, domingo y dia festivo y tiene un turno de 12 hrs (nota, aquí no se valida)
            Si el festivo cae en lunes debe guardarse 0 en domingo, si el festivo cae viernes debe guardarse 0 en sabado 
        */
        global $con;
        $sql="INSERT INTO af (sabado, domingo, idacceso) VALUES ('1', '1', '$idacceso');"; 
        if(!(mysqli_query($con,$sql)))
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $línea='500';
            error($er1,$er2,$línea);
            mysqli_rollback($con);
            mysqli_autocommit($con, TRUE); 
        }
        else
        {
            return true;
        } 
    }
    
    function buscarEnAF($numero)
    {
        global $con;
        $sql="select a.idaf from af a inner join acceso b where b.trabajador_trabajador='$numero';"; 
        $query=mysqli_query($con,$sql);
        if(!$query)
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $línea='519';
            error($er1,$er2,$línea);
            mysqli_rollback($con);
            mysqli_autocommit($con, TRUE); 
        }
        else
        {
            $fila=mysqli_num_rows($query);
            if($fila==1)
            {
                $resul=mysqli_fetch_array($query);
                return $resul[0];
            }
            else
            {
                return null;
            }
        } 
    }

    function eliminarEnAF($idaf)
    {
        global $con;
        $sql="DELETE FROM af WHERE (idaf = '$idaf');"; 
        $query=mysqli_query($con,$sql);
        if(!$query)
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $línea='548';
            error($er1,$er2,$línea);
            mysqli_rollback($con);
            mysqli_autocommit($con, TRUE); 
        }
        else
        {
          return true;
        } 
    }
?>
