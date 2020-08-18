<?php
    //Revisar el orden de los días de acceso y sexta de los empleados que vienen hoy con sexta
    function orden_acceso_sexta()
    { 
        global $con;
        global $deben_hoy;
        global $deben_hoy_ultimo;
        global $observar_e;
        global $observar_s;

        $datos_sexta=tienenSexta();
        if($datos_sexta != null)
        {
            $cantidad=count($datos_sexta);
            for($j=0;$j<$cantidad;$j++)
            {
                //Obtener los datos de de todos los que tienen sexta
                $numero_empleado=$datos_sexta[$j][0];
                $t_dias_acceso=$datos_sexta[$j][1];
                $idacceso=$datos_sexta[$j][2];
                $idsexta=$datos_sexta[$j][3];
                $t_dias_sexta=$datos_sexta[$j][4];
                $validez=$datos_sexta[$j][5];
                $entrada=$datos_sexta[$j][6]; 
                $salida=$datos_sexta[$j][7]; 
                $acceso_sexta=$datos_sexta[$j][8]; 

                //1. Tiene sexta y total de dias de acceso = 3
                if($t_dias_acceso==3)
                {
                    //Actualizar validez a 1 y total de días de sexta a 0
                    $sql2="UPDATE sexta SET validez = 1, t_dias=0 WHERE (idsexta = $idsexta);";
                    $query2= mysqli_query($con, $sql2);
                    if(!$query2)
                    {
                        $er1=mysqli_errno($con);
                        $er2=mysqli_error($con);
                        $hacer='actualizar';
                        $tabla='sexta';
                        $línea='148';
                        error($er1,$er2,$hacer,$tabla,$línea);
                    }

                    //Actualizar total de días de acceso a 4
                    $sql3="UPDATE acceso SET t_dias=4 WHERE (idacceso = $idacceso)";
                    $query3= mysqli_query($con, $sql3);
                    if(!$query3)
                    {
                        $er1=mysqli_errno($con);
                        $er2=mysqli_error($con);
                        $hacer='actualizar';
                        $tabla='acceso';
                        $línea='160';
                        error($er1,$er2,$hacer,$tabla,$línea);
                    }
                }
            
                //Revisar el orden de acceso y sexta de acceso y sexta 
                if($acceso_sexta=="11100000000011")
                {
                    if($t_dias_sexta==2)
                    {
                        //Actualizar el total de dias de sexta a 3
                        $sql7="UPDATE sexta SET t_dias=3 WHERE (idsexta = $idsexta)";
                        $query7= mysqli_query($con, $sql7);
                        if(!$query7)
                        {
                            $er1=mysqli_errno($con);
                            $er2=mysqli_error($con);
                            $hacer='actualizar';
                            $tabla='sexta';
                            $línea='321';
                            error($er1,$er2,$hacer,$tabla,$línea);
                        }
                    } //fin if t_dias_sexta=2

                    if($t_dias_sexta==3)
                    { 
                        //Actualizar el total de dias de acceso a 0, validez de sexta y total de dias de sexta a 0
                        $sql5="UPDATE acceso SET t_dias=0 WHERE (idacceso = $idacceso)";
                        $query5= mysqli_query($con, $sql5);
                        if(!$query5)
                        {
                            $er1=mysqli_errno($con);
                            $er2=mysqli_error($con);
                            $hacer='actualizar';
                            $tabla='acceso';
                            $línea='293';
                            error($er1,$er2,$hacer,$tabla,$línea);
                        }
                    
                        $sql6="UPDATE sexta SET validez=0,t_dias=0 WHERE (idsexta = $idsexta)";
                        $query6= mysqli_query($con, $sql6);
                        if(!$query6)
                        {
                            $er1=mysqli_errno($con);
                            $er2=mysqli_error($con);
                            $hacer='actualizar';
                            $tabla='sexta';
                            $línea='305';
                            error($er1,$er2,$hacer,$tabla,$línea);
                        }
                    }//fin if $t_dias_sexta = 3

                    //Revisar si el empleado viene hoy y tiene sexta
                    $datos=viene_hoy_acceso_y_tiene_sexta($numero_empleado);
                    if($datos != null)
                    {
                        //Guardar en el arreglo
                        $deben_hoy[$deben_hoy_ultimo][0]=$numero_empleado;
                        $deben_hoy[$deben_hoy_ultimo][1]=$entrada;
                        $deben_hoy[$deben_hoy_ultimo][2]=$salida;
                        $deben_hoy[$deben_hoy_ultimo][3]=$observar_e;//valor de observar entrada sirve para guardarse en la tabla vienen_hoy
                        $deben_hoy[$deben_hoy_ultimo][4]=$observar_s;//valor de observar salida sirve para guardarse en la tabla vienen_hoy
                        $deben_hoy_ultimo++;
            
                        //Actualizar en Acceso-> total de dias + 1
                        $t_dias=$t_dias_acceso+1;
                        $sql4="UPDATE acceso SET t_dias=$t_dias WHERE (idacceso = $idacceso)";
                        $query4=mysqli_query($con, $sql4);
                        if(!$query4)
                        {
                            $er1=mysqli_errno($con);
                            $er2=mysqli_error($con);
                            $hacer='actualizar';
                            $tabla='acceso';
                            $línea='277';
                            error($er1,$er2,$hacer,$tabla,$línea);
                        } 
                    }
                }
                else //acceso_sexta diferente de 11100000000011
                {
                    //Revisar si el empleado viene hoy en acceso y tiene sexta
                    $datos=viene_hoy_acceso_y_tiene_sexta($numero_empleado);
                    if($datos != null)
                    {
                        //Guardar en el arreglo
                        $deben_hoy[$deben_hoy_ultimo][0]=$numero_empleado;
                        $deben_hoy[$deben_hoy_ultimo][1]=$entrada;
                        $deben_hoy[$deben_hoy_ultimo][2]=$salida;
                        $deben_hoy[$deben_hoy_ultimo][3]=$observar_e;//valor de observar entrada sirve para guardarse en la tabla vienen_hoy
                        $deben_hoy[$deben_hoy_ultimo][4]=$observar_s;//valor de observar salida sirve para guardarse en la tabla vienen_hoy
                        $deben_hoy_ultimo++;
                    
                        //Actualizar en Acceso-> total de dias + 1
                        $t_dias=$t_dias_acceso+1;
                        $sql4="UPDATE acceso SET t_dias=$t_dias WHERE (idacceso = $idacceso)";
                        $query4=mysqli_query($con, $sql4);
                        if(!$query4)
                        {
                            $er1=mysqli_errno($con);
                            $er2=mysqli_error($con);
                            $hacer='actualizar';
                            $tabla='acceso';
                            $línea='277';
                            error($er1,$er2,$hacer,$tabla,$línea);
                        } 
                    }
                    //Si el empleado trabaja martes y jueves(0101000) y despues lunes, miercoles y viernes (1010100)
                    if($acceso_sexta=="01010001010100")
                    {
                        if($t_dias_acceso==2)
                        {
                            //Actualizar el total de dias de acceso a 3
                            $sql7="UPDATE acceso SET t_dias=3 WHERE (idacceso = $idacceso)";
                            $query7= mysqli_query($con, $sql7);
                            if(!$query7)
                            {
                                $er1=mysqli_errno($con);
                                $er2=mysqli_error($con);
                                $hacer='actualizar';
                                $tabla='acceso';
                                $línea='363';
                                error($er1,$er2,$hacer,$tabla,$línea);
                            }
                        } //fin if t_dias_sexta=2

                        if($t_dias_sexta==3)
                        { 
                            //Actualizar el total de dias de acceso a 0, validez de sexta y total de dias de sexta a 0
                            $sql5="UPDATE acceso SET t_dias=0 WHERE (idacceso = $idacceso)";
                            $query5= mysqli_query($con, $sql5);
                            if(!$query5)
                            {
                                $er1=mysqli_errno($con);
                                $er2=mysqli_error($con);
                                $hacer='actualizar';
                                $tabla='acceso';
                                $línea='379';
                                error($er1,$er2,$hacer,$tabla,$línea);
                            }
                        
                            $sql6="UPDATE sexta SET validez=0,t_dias=0 WHERE (idsexta = $idsexta)";
                            $query6= mysqli_query($con, $sql6);
                            if(!$query6)
                            {
                                $er1=mysqli_errno($con);
                                $er2=mysqli_error($con);
                                $hacer='actualizar';
                                $tabla='sexta';
                                $línea='305';
                                error($er1,$er2,$hacer,$tabla,$línea);
                            }
                        }//fin if $t_dias_sexta = 3
                    }
                    else
                    {
                        if($t_dias_sexta==3)
                        { 
                            //Actualizar el total de dias de acceso a 0, validez de sexta y total de dias de sexta a 0
                            $sql5="UPDATE acceso SET t_dias=0 WHERE (idacceso = $idacceso)";
                            $query5= mysqli_query($con, $sql5);
                            if(!$query5)
                            {
                                $er1=mysqli_errno($con);
                                $er2=mysqli_error($con);
                                $hacer='actualizar';
                                $tabla='acceso';
                                $línea='293';
                                error($er1,$er2,$hacer,$tabla,$línea);
                            }
                        
                            $sql6="UPDATE sexta SET validez=0,t_dias=0 WHERE (idsexta = $idsexta)";
                            $query6= mysqli_query($con, $sql6);
                            if(!$query6)
                            {
                                $er1=mysqli_errno($con);
                                $er2=mysqli_error($con);
                                $hacer='actualizar';
                                $tabla='sexta';
                                $línea='305';
                                error($er1,$er2,$hacer,$tabla,$línea);
                            }
                        }//fin if $t_dias_sexta = 3

                        if($t_dias_sexta==2)
                        {
                            //Actualizar el total de dias de sexta a 3
                            $sql7="UPDATE sexta SET t_dias=3 WHERE (idsexta = $idsexta)";
                            $query7= mysqli_query($con, $sql7);
                            if(!$query7)
                            {
                                $er1=mysqli_errno($con);
                                $er2=mysqli_error($con);
                                $hacer='actualizar';
                                $tabla='sexta';
                                $línea='321';
                                error($er1,$er2,$hacer,$tabla,$línea);
                            }
                        } //fin if t_dias_sexta=2
                    }
                }
                // Revisar si vienen hoy en sexta,es decir si validez = 1 y dia actual = 1 en sexta
                $viene_hoy_por_sexta=viene_hoy_por_sexta($idsexta);
                if($viene_hoy_por_sexta !== null)
                {
                    //Agregar al empleado al arreglo
                    $deben_hoy[$deben_hoy_ultimo][0]=$numero_empleado;
                    $deben_hoy[$deben_hoy_ultimo][1]=$entrada;
                    $deben_hoy[$deben_hoy_ultimo][2]=$salida;
                    $deben_hoy[$deben_hoy_ultimo][3]=$observar_e;//valor de observar entrada sirve para guardarse en la tabla vienen_hoy
                    $deben_hoy[$deben_hoy_ultimo][4]=$observar_s;//valor de observar salida sirve para guardarse en la tabla vienen_hoy
                    $deben_hoy_ultimo++;
                    
                    //Actualizar en Sexta-> total de dias + 1
                    $t_dias_sexta=$t_dias_sexta+1;
                    $sql8="UPDATE sexta SET t_dias=$t_dias_sexta WHERE (idsexta = $idsexta)";
                    $query8= mysqli_query($con, $sql8);
                    if(!$query8)
                    {
                        $er1=mysqli_errno($con);
                        $er2=mysqli_error($con);
                        $hacer='actualizar';
                        $tabla='sexta';
                        $línea='350';
                        error($er1,$er2,$hacer,$tabla,$línea);
                    }
                } 
            }   
        }
    }
    
    //SELECCIONAR A TODOS LOS EMPLEADOS QUE VIENEN HOY Y SU TOTAL DE DÍAS SEA -1. ES DECIR, EL RESTO DE LOS QUE NO TIENEN SEXTA Y NO TRABAJAN SABADO, DOMINGO Y FESTIVO
    function vienen_hoy()
    {
        global $diaactual;
        global $deben_hoy;
        global $deben_hoy_ultimo;
        global $observar_e;
        global $observar_s;
        global $con;
        $sql="select * from trabajador t 
        inner join acceso a on a.trabajador_trabajador = t.numero_trabajador 
        inner join turno c on c.idturno=a.turno_turno 
        and not exists (select idaf from af d where d.idacceso=a.idacceso)
        and a.$diaactual=1 and a.t_dias=-1;";
        $query= mysqli_query($con, $sql);
        $filas=mysqli_num_rows($query);
        if($filas>0)
        {
            while($resul=mysqli_fetch_array($query))
            {
                $deben_hoy[$deben_hoy_ultimo][0]=$resul[0];//numero de empleado
                $deben_hoy[$deben_hoy_ultimo][1]=$resul[1];//hora entrada
                $deben_hoy[$deben_hoy_ultimo][2]=$resul[2];//hora salida
                $deben_hoy[$deben_hoy_ultimo][3]=$observar_e;
                $deben_hoy[$deben_hoy_ultimo][4]=$observar_s;
                $deben_hoy_ultimo++;
            }     
        }
    }

    function vienen_hoy_si_trabajan_sabado_domingo_festivo()
    {
        global $diaactual;
        global $deben_hoy;
        global $deben_hoy_ultimo;
        global $observar_e;
        global $observar_s;
        global $con;
        // Obtener a los que trabaja sábado, domingo y festivo (00000111), es decir a los que existen en la tabla af
        $sql=" select t.numero_trabajador,c.entrada,c.salida,b.sabado, b.domingo from trabajador t 
        inner join acceso a on a.trabajador_trabajador = t.numero_trabajador 
        inner join af b on b.idacceso=a.idacceso
        inner join turno c on c.idturno=a.turno_turno 
        and a.$diaactual=1 and a.t_dias=-1;";
        $query= mysqli_query($con, $sql);
        $filas=mysqli_num_rows($query);
        if($filas>0)
        {
            while($resul=mysqli_fetch_array($query))
            {   $sabado=$resul[3];
                $domingo=$resul[4];
                $DiasAcceso=verDiasAcceso($numero);
                if($diaactual=='domingo')
                {
                    //Revisar si en la tabla af domingo está en 0.
                    if($domingo=='0')
                    {
                        $deben_hoy[$deben_hoy_ultimo][0]=$resul[0];//numero de empleado
                        $deben_hoy[$deben_hoy_ultimo][1]=$resul[1];//hora entrada
                        $deben_hoy[$deben_hoy_ultimo][2]=$resul[2];//hora salida
                        $deben_hoy[$deben_hoy_ultimo][3]=$observar_e;
                        $deben_hoy[$deben_hoy_ultimo][4]=$observar_s;
                        $deben_hoy_ultimo++;
                    }
                }
                if($diaactual=='sabado')
                {
                    //Revisar si en la tabla af sábado está en 0.
                    if($sabado=='0')
                    {
                        $deben_hoy[$deben_hoy_ultimo][0]=$resul[0];//numero de empleado
                        $deben_hoy[$deben_hoy_ultimo][1]=$resul[1];//hora entrada
                        $deben_hoy[$deben_hoy_ultimo][2]=$resul[2];//hora salida
                        $deben_hoy[$deben_hoy_ultimo][3]=$observar_e;
                        $deben_hoy[$deben_hoy_ultimo][4]=$observar_s;
                        $deben_hoy_ultimo++;
                    }                
                }
            }     
        }
    }

    //Seleccionar a todos los empleados que tienen sexta 
    function tienenSexta()
    {
        global $con;
        $sexta=[];
        $aumenta=0;
        $sql1="select a.numero_trabajador, b.t_dias,b.idacceso,c.idsexta,
        c.t_dias,c.validez,d.entrada,d.salida,
        b.lunes,b.martes,b.miercoles,b.jueves,b.viernes,b.sabado,b.domingo,
        c.lunes,c.martes,c.miercoles,c.jueves,c.viernes,c.sabado,c.domingo
        from trabajador a 
        inner join acceso b on a.numero_trabajador=b.trabajador_trabajador
        inner join sexta c on a.numero_trabajador=c.trabajador_trabajador
        inner join turno d on b.turno_turno=d.idturno";
        $query1= mysqli_query($con, $sql1);
        $filas=mysqli_num_rows($query1);
        if($filas>0)
        {
            while($resul=mysqli_fetch_array($query1))
            {
                $sexta[$aumenta][0]=$resul[0]; 
                $sexta[$aumenta][1]=$resul[1];
                $sexta[$aumenta][2]=$resul[2];
                $sexta[$aumenta][3]=$resul[3];
                $sexta[$aumenta][4]=$resul[4];
                $sexta[$aumenta][5]=$resul[5];
                $sexta[$aumenta][6]=$resul[6];
                $sexta[$aumenta][7]=$resul[7];
                $sexta[$aumenta][8]=$resul[8].$resul[9].$resul[10].$resul[11].$resul[12].$resul[13].$resul[14].$resul[15].$resul[16].$resul[17].$resul[18].$resul[19].$resul[20].$resul[21];  
                $aumenta++;
            }
            return $sexta;
        }
        else
        {
            return null;
        }
    } 
    
    //Ver si el empleado viene hoy en acceso y tienen sexta
    function viene_hoy_acceso_y_tiene_sexta($numero_empleado)
    {
        global $diaactual;
        global $con;
        $sql1="select a.numero_trabajador
        from trabajador a 
        inner join acceso b on a.numero_trabajador=b.trabajador_trabajador
        inner join sexta c on a.numero_trabajador=c.trabajador_trabajador
        and b.$diaactual = 1 and b.t_dias<3
        and a.numero_trabajador='$numero_empleado';";
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

    //Ver si el empleado vienen hoy por su sexta
    function viene_hoy_por_sexta($idsexta)
    {
        global $diaactual;
        global $con;
        $sql1="SELECT t_dias  FROM sexta where idsexta=$idsexta and $diaactual=1 and validez=1;";
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

    function existeEnAF($numero)
    {   
        global $con;
        //Bucar el id de acceso del trabajador
        $sql="select a.idaf from af a inner join acceso b on b.idacceso=a.idacceso
        where b.trabajador_trabajador='$numero';";
        $query= mysqli_query($con, $sql); 
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

    function actualizaAFa1($dia,$idaf)
    {   
        global $con;
        //Actualizar a 1 si el trabajador debe descansar el 
        $sql2="UPDATE af SET $dia = '1' WHERE (idaf = '$idaf');";
        $query2= mysqli_query($con, $sql2); 
        if(!$query2)
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $hacer='actualizar';
            $tabla='af';
            $línea='111';
            error($er1,$er2,$hacer,$tabla,$línea);
        }
        else
        {
            return true;
        } 
    }

    function actualizaAFa0($dia,$idaf)
    {   
        global $con;
        //Actualizar a 0 si el dia festivo fue diferente de lunes o viernes
        $sql2="UPDATE af SET $dia = '0' WHERE (idaf = '$idaf');";
        $query2= mysqli_query($con, $sql2); 
        if(!$query2)
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $hacer='actualizar';
            $tabla='af';
            $línea='111';
            error($er1,$er2,$hacer,$tabla,$línea);
        }
        else
        {
            return true;
        } 
    }
    
    function revisarSiTieneTurnoOpcional($numero)
    {
        global $con;
        $sql="select idt_op from t_op where trabajador_trabajador = '".$numero."'";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
            die("<br>" . "Error línea 6 al consultar trabajador: " . mysqli_errno($con) . " : " . mysqli_error($con));
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

    function verDiasAcceso($numero)
    {
        global $con;
        $sql="select a.lunes, a.martes, a.miercoles,a.jueves,a.viernes,a.sabado, a.domingo,a.dia_festivo
        from trabajador t 
        inner join acceso a on a.trabajador_trabajador = t.numero_trabajador 
        where a.trabajador_trabajador='$numero';";
        $query= mysqli_query($con, $sql);
        $fila=mysqli_num_rows($query);
        if($fila==1)
        {
            $resul=mysqli_fetch_array($query);
            $diasAcceso=$resul[0].''.$resul[1].''.$resul[2].''.$resul[3].''.$resul[4].''.$resul[5].''.$resul[6].''.$resul[7];
            return $diasAcceso;
        }      
    }

    function verDiasAccesoParaDiaFestivo($numero)
    {
        global $con;
        $sql="select a.sabado, a.domingo
        from trabajador t 
        inner join acceso a on a.trabajador_trabajador = t.numero_trabajador 
        where a.trabajador_trabajador='$numero'
        and (a.lunes=0 and a.martes=0 and a.miercoles=0 and a.jueves=0 and a.viernes=0);";
        $query= mysqli_query($con, $sql);
        $fila=mysqli_num_rows($query);
        if($fila==1)
        {
            $resul=mysqli_fetch_array($query);
            return[$resul[0],$resul[1]];
        }
        else
        {
            null;
        }        
    }

    /*------------------------CHECAR TODO------------------------------------------*/
    //QUIENES DEBEN ASISTIR HOY SI TIENEN GUARDIA 
    function tiene_guardia()
    { 
        global $con;
        global $f_hoy;
        global $deben_hoy;
        global $deben_hoy_ultimo;        
        for($j=0;$j<$deben_hoy_ultimo;$j++)
        {   //--------Array deben_hoy---------------//
            $num_deben=$deben_hoy[$j][0];
            //-------------------------------------//    
            $sql="select c.trabajador_suplente from trabajador t 
            inner join acceso a on a.trabajador_trabajador= t.numero_trabajador 
            inner join turno b on b.idturno=a.turno_turno
            inner join guardias c on c.trabajador_solicitante=t.numero_trabajador 
            and c.fecha_guardia='$f_hoy' and c.trabajador_solicitante='$num_deben'";
            $query= mysqli_query($con, $sql);
            $filas=mysqli_num_rows($query);
            if($filas>0)
            {
                while($resul=mysqli_fetch_array($query))
                {
                    $suplente=$resul[0];
                    $deben_hoy[$j][0]=$suplente;
                }         
            }
        }
    }//FIN QUIENES DEBEN ASISTIR HOY SI TIENEN GUARDIA 
   
    //QUIEN TIENE COMISION
    function tiene_comision()
    {
        global $deben_hoy;
        global $deben_hoy_ultimo;
        global $con;
        for($j=0;$j<$deben_hoy_ultimo;$j++)
        { //--------Array deben_hoy---------------//
            $num_deben=$deben_hoy[$j][0];
            $hora_entrada_deben=$deben_hoy[$j][1];
            $hora_salida_deben=$deben_hoy[$j][2];
            $observar_e=$deben_hoy[$j][3];
            //-------------------------------------//
            $sql="SELECT a.empresa, a.idespecial,a.hora_entrada,a.hora_salida FROM especial a
            inner join trabajador b 
            on b.numero_trabajador = a.trabajador_trabajador 
            and a.validez = 1 and b.numero_trabajador='$num_deben' 
            and a.clave_especial_clave_especial='CS';";
            $query= mysqli_query($con, $sql);
            $filas=mysqli_num_rows($query);
            if($filas>0)
            { 
                while($resul=mysqli_fetch_array($query))
                { 
                    $empresa=$resul[0];
                    $idespecial=$resul[1];
                    $hora_entrada_comision=$resul[2];
                    $hora_salida_comision=$resul[3];

                    //Obtener la primera letra del nombre de la empresa
                    $primeraletra=substr("$empresa",0, 1);
                    
                    //Si la primera letra de la empresa es A significa que la comision es Aquí en Zapata (Comisión interna)
                    if ($primeraletra=="A")
                    {
                        //Seleccionar la hora de entrada y salida de la comision y cambiar en array deben_hoy en lugar de la anterior hora de entrada  
                        $hora_entrada_deben=$hora_entrada_comision;
                        $deben_hoy[$j][1]=$hora_entrada_deben;
                        $hora_salida_deben=$hora_salida_comision;
                        $deben_hoy[$j][2]=$hora_salida_deben;
                    }
                    else
                    {  
                        //Si la primera letra de la empresa es D significa que la comision tiene Destino otro hospital (Comisión externa)
                        if($primeraletra=="D")
                        {
                            //En el array deben_hoy formatear observar_e y observar_s a cero
                            $observar_e="0";
                            $deben_hoy[$j][3]=$observar_e;
                            $deben_hoy[$j][4]=$observar_e;
                        }
                    }  
                }//FIN DEL WHILE
            }//FIN DEL IF
        }//FIN DEL FOR
    }// FIN DE FUNCION TIENE_COMISION

    //QUIEN TIENE COMISION OFICIAL O PARTICIPACION EN CURSO DE CAPACITACION
    function comision_oficial_participacion_curso()
    {
        //Si participación en curso de capacitación, adiestramiento o especialización hoy ( clave 29).
        //Si Comisión oficial con o sin viáticos o que comprenda menos de un día (clave 61).
        global $deben_hoy;
        global $deben_hoy_ultimo;
        global $con;
        for($j=0;$j<$deben_hoy_ultimo;$j++)
        {
            //--------Array deben_hoy---------------//
            $num_deben=$deben_hoy[$j][0];
            $hora_entrada_deben=$deben_hoy[$j][1];
            $hora_salida_deben=$deben_hoy[$j][2];
            $observar_e=$deben_hoy[$j][3];
            //-------------------------------------//
            $sql="SELECT a.hora_entrada FROM especial a
            inner join trabajador b 
            on b.numero_trabajador = a.trabajador_trabajador 
            and a.validez = 1 and b.numero_trabajador='$num_deben' 
            and (a.clave_especial_clave_especial=29 or a.clave_especial_clave_especial=61);";
            $query= mysqli_query($con, $sql);
            $filas=mysqli_num_rows($query);
            if($filas>0)
            { 
                while($resul=mysqli_fetch_array($query))
                {
                    $hora_entrada_especial=$resul[0];

                    /*Ver si ese empleado tiene hora de entrada = 00:00:00, significa que el empleado no deberá asistir durante el tiempo 
                    de la capacitación o comisión*/
                    if($hora_entrada_especial=="00:00:00")
                    {
                        //En el array deben_hoy formatear observar_e y observar_s  a cero
                        $observar_e="0";
                        $deben_hoy[$j][3]=$observar_e;
                        $deben_hoy[$j][4]=$observar_e;
                    }
                    else
                    {
                        //Guardar la hora de entrada  y salida de especial  en el array deben hoy, en lugar de la anterior hora de entrada
                        $hora_entrada_deben=$hora_entrada_especial;
                        $deben_hoy[$j][1]=$hora_entrada_deben;
                        $hora_salida_deben=$hora_salida_comision;
                        $deben_hoy[$j][2]=$hora_salida_deben;
                    }       
                }//fin del while
            } //fin del if  
        }//fin del for
    }//FIN QUIEN TIENE COMISION OFICIAL O PARTICIPACION EN CURSO DE CAPACITACION
    //QUIEN TIENEN LICENCIAS O PERMISOS
    function licencias_permisos()
    {
        /*
            Si Permisos con goce de sueldo hasta por tres días hoy (clave 40).
            Si Permisos con goce de sueldo por antigüedad hoy (clave 41).
            Si Días por cuidados maternos hoy (clave 47).
            Si LICENCIA CON SUELDO POR BECA TOTAL O PARCIAL (clave 51).
            Si Incapacidad por gravidez hoy (clave 53).
            Si Incapacidad por accidente o riesgo profesional hoy (clave 54).
            Si Incapacidad médica por enfermedad no profesional (clave 55).
            Si tiene Inasistencia por acto cívico (clave 13).
            Si tiene comisión sindical equivalente a un día(clave 17)
            Si tiene licencia con sueldo por beca total o parcial (clave 51)
        */
        global $deben_hoy;
        global $deben_hoy_ultimo;
        global $con;
        for($j=0;$j<$deben_hoy_ultimo;$j++)
        {
            //--------Array deben_hoy---------------//
            $num_deben=$deben_hoy[$j][0];
            $hora_entrada_deben=$deben_hoy[$j][1];
            $observar_e=$deben_hoy[$j][3];
            //-------------------------------------//
            $sql="SELECT a.hora_entrada FROM especial a
            inner join trabajador b 
            on b.numero_trabajador = a.trabajador_trabajador 
            and a.validez = 1 and b.numero_trabajador='$num_deben' 
            and (a.clave_especial_clave_especial=13
            or a.clave_especial_clave_especial=17 
            or a.clave_especial_clave_especial=40 
            or a.clave_especial_clave_especial=41
            or a.clave_especial_clave_especial=47 
            or a.clave_especial_clave_especial=51 
            or a.clave_especial_clave_especial=53
            or a.clave_especial_clave_especial=54 
            or a.clave_especial_clave_especial=55
            or a.clave_especial_clave_especial=61)";
            $query= mysqli_query($con, $sql);
            $filas=mysqli_num_rows($query);
            //Si la consulta arroja 1 valor, significa que el empleado no asistirá 
            if($filas>0)
            { 
                //En el array deben_hoy formatear observar_e y observar_s a cero
                $observar_e="0";
                $deben_hoy[$j][3]=$observar_e;
                $deben_hoy[$j][4]=$observar_e;

            } //fin del if  
        }//fin del for
    }//FIN QUIEN TIENE LICENCIAS O PERMISOS

    //QUIEN TIENE TOLERANCIA DE LACTANCIA O ESTANCIA
    function lactancia_estancia()
    {
        /*
            SI tolerancia de lactancia hoy (clave 92).
            Si tolerancia de estancia hoy (clave 93).
        */
        global $deben_hoy;
        global $deben_hoy_ultimo;
        global $con;
        for($j=0;$j<$deben_hoy_ultimo;$j++)
        {
            //--------Array deben_hoy---------------//
            $num_deben=$deben_hoy[$j][0];
            $hora_entrada_deben=$deben_hoy[$j][1];
            //-------------------------------------//
            $sql="SELECT a.hora_entrada ,a.hora_salida FROM especial a
            inner join trabajador b 
            on b.numero_trabajador = a.trabajador_trabajador 
            and a.validez = 1 and b.numero_trabajador='$num_deben' 
            and (a.clave_especial_clave_especial=92 or a.clave_especial_clave_especial=93)";
            $query= mysqli_query($con, $sql);
            $filas=mysqli_num_rows($query);
            if($filas>0)
            { 
                while($resul=mysqli_fetch_array($query))
                {
                    $hora_entrada_especial=$resul[0];
                    $hora_salida_especial=$resul[1];
                    //Guardar la hora de entrada  y salida de especial  en el array deben hoy, en lugar de la anterior hora de entrada
                    $hora_entrada_deben=$hora_entrada_especial;
                    $deben_hoy[$j][1]=$hora_entrada_deben; 
                    $hora_salida_deben=$hora_salida_especial;
                    $deben_hoy[$j][2]=$hora_salida_deben;    
                }//fin del while
            } //fin del if  
        }//fin del for
    }//QUIEN TIENE TOLERANCIA DE LACTANCIA O ESTANCIA

    //QUIEN TIENE CUMPLEAÑOS U ONOMASTICO
    function cumple_ono()
    {
        global $deben_hoy;
        global $deben_hoy_ultimo;
        global $f_hoy;
        global $con;
        for($j=0;$j<$deben_hoy_ultimo;$j++)
        {
            //--------Array deben_hoy---------------//
            $num_deben=$deben_hoy[$j][0];
            $hora_entrada_deben=$deben_hoy[$j][1];
            $observar_e=$deben_hoy[$j][3];
            //-------------------------------------//

            $sql="select numero_trabajador from trabajador a
            inner join cumple_ono b
            on a.numero_trabajador=b.trabajador_trabajador
            and ((fecha_cumple='$f_hoy' and validez=0) 
            or (fecha_ono='$f_hoy' and validez=1))
            and numero_trabajador='$num_deben';";
            $query= mysqli_query($con, $sql);
            $filas=mysqli_num_rows($query);
            //Si la consulta arroja 1 valor, significa que el empleado no asistirá 
            if($filas>0)
            {   
                $resul=mysqli_fetch_array($query);
                //En el array deben_hoy formatear observar_e y observar_s  a cero
                $observar_e="0";
                $deben_hoy[$j][3]=$observar_e;
                $deben_hoy[$j][4]=$observar_e;
            } //fin del if  
        }//fin del for
    }//FIN QUIEN TIENE CUMPLEAÑOS U ONOMASTICO

    //QUIEN TIENE VACACIONES
    function vacaciones()
    {
        /*
			60 Vacaciones.
			62 Vacaciones por emanaciones radiactivas.
			63 Vacaciones extraordinarias por premios, estímulos y recompensas.
		*/
        global $deben_hoy;
        global $deben_hoy_ultimo;
        global $f_hoy;
        global $con;
        for($j=0;$j<$deben_hoy_ultimo;$j++)
        {
            //--------Array deben_hoy---------------//
            $num_deben=$deben_hoy[$j][0];
            $hora_entrada_deben=$deben_hoy[$j][1];
            $observar_e=$deben_hoy[$j][3];
            //-------------------------------------//
            //VACACIONES NORMALES
            $sql="SELECT idvacaciones FROM vacaciones b 
            INNER jOIN dias_vacaciones c on b.idvacaciones=c.vacaciones_vacaciones
            where c.dia >='$f_hoy'
            and c.tomado=0;";
            $query= mysqli_query($con, $sql);
            $filas=mysqli_num_rows($query);
            //Si la consulta arroja 1 valor, significa que el empleado no asistirá 
            if($filas>0)
            {   
                $resul=mysqli_fetch_array($query);
                //En el array deben_hoy formatear observar_e y observar_s  a cero
                $observar_e="0";
                $deben_hoy[$j][3]=$observar_e;
                $deben_hoy[$j][4]=$observar_e;
            } 

            //VACACIONES RADIO
            $sql="SELECT idvacaciones_radio FROM vacaciones_radio b 
            INNER jOIN dias_vacaciones_radio c on b.idvacaciones_radio=c.vacaciones_vacaciones
            where c.dia >='$f_hoy''
            and c.tomado=0;";
            $query= mysqli_query($con, $sql);
            $filas=mysqli_num_rows($query);
            //Si la consulta arroja 1 valor, significa que el empleado no asistirá 
            if($filas>0)
            {   
                $resul=mysqli_fetch_array($query);
                //En el array deben_hoy formatear observar_e y observar_s  a cero
                $observar_e="0";
                $deben_hoy[$j][3]=$observar_e;
                $deben_hoy[$j][4]=$observar_e;
            } 
            
            //VACACIONES EXTRAORDINARIAS
            $sql=" SELECT * FROM vacaciones_extraordinarias where dia ='$f_hoy'";
            $query= mysqli_query($con, $sql);
            $filas=mysqli_num_rows($query);
            //Si la consulta arroja 1 valor, significa que el empleado no asistirá 
            if($filas>0)
            {   
                $resul=mysqli_fetch_array($query);
                //En el array deben_hoy formatear observar_e y observar_s  a cero
                $observar_e="0";
                $deben_hoy[$j][3]=$observar_e;
                $deben_hoy[$j][4]=$observar_e;
            } //fin del if  
        }//fin del for
    }

    //QUIEN TIENE SUSPENSION
    function suspension()
    {
        global $deben_hoy;
        global $deben_hoy_ultimo;
        global $f_hoy;
        global $con;
        for($j=0;$j<$deben_hoy_ultimo;$j++)
        {
            //--------Array deben_hoy---------------//
            $num_deben=$deben_hoy[$j][0];
            $hora_entrada_deben=$deben_hoy[$j][1];
            $observar_e=$deben_hoy[$j][3];
            //-------------------------------------//
            //Si existe en la tabla suspension no deberá venir hoy el trabajador
            $sql="SELECT idbajas FROM bajas where trabajador_trabajador='$num_deben';";
            $query= mysqli_query($con, $sql);
            $filas=mysqli_num_rows($query);
            //Si la consulta arroja 1 valor, significa que el empleado no asistirá 
            if($filas>0)
            {   
                $resul=mysqli_fetch_array($query);
                //En el array deben_hoy formatear observar_e y observar_s  a cero
                $observar_e="0";
                $deben_hoy[$j][3]=$observar_e;
                $deben_hoy[$j][4]=$observar_e;
            } //fin del if  
        }//fin del for
    }
    /////////////////////////////////////////////////////
 
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

        $error="Error al $accion en la tabla $nomTabla. $err1 : $err2. Línea de error: $numLinea. Tarea vienen_hoy.";
        echo"<script> console.error('$error'); </script>";
        exit();
    }

    function existeEnTablaVienenHoy($numero)
    {   
        global $con;
        //Bucar el id de acceso del trabajador
        $sql="select idvienen_hoy from vienen_hoy where trabajador_trabajador='$numero';";
        $query= mysqli_query($con, $sql); 
        $fila=mysqli_num_rows($query);
        if($fila>0)
        {
            return true;
        }
        else
        {
            return null;
        }
    }

    //CHECAR QUIEN TIENE COMISION, LICENCIAS, PERMISOS
    function checar_todo()
    {
        global $deben_hoy;
        tiene_guardia();
        tiene_comision();
        comision_oficial_participacion_curso();
        licencias_permisos();
        lactancia_estancia();
        cumple_ono();
    }//FIN CHECAR QUIEN TIENE COMISION, LICENCIAS, PERMISOS

    //Guarda todos los empleados que deben venir hoy
    function guarda_deben_hoy()
    {
        global $con;
        global $deben_hoy;
        global $deben_hoy_ultimo;
        global $arregloPendientes;
        $contador=0;
        //Guardar a los pendientes
        $sql="select c.trabajador_trabajador,c.entrada,c.salida,c.observar_e,c.observar_s,c.t_op from pendiente a
        inner join asistencia b on a.idasistencia=b.id
        inner join vienen_hoy c on b.trabajador_trabajador=c.trabajador_trabajador;";
        $query= mysqli_query($con, $sql); 
        $fila=mysqli_num_rows($query);
        if($fila>0)
        {
            while($resul=mysqli_fetch_array($query))
            {
                $arregloPendientes[$contador][0]=$resul[0];
                $arregloPendientes[$contador][1]=$resul[1];
                $arregloPendientes[$contador][2]=$resul[2];
                $arregloPendientes[$contador][3]=$resul[3];
                $arregloPendientes[$contador][4]=$resul[4];
                $arregloPendientes[$contador][5]=$resul[5];
                $contador++;
            }
        }
        //Borrar todos los datos de vienen_hoy
        $sql1="TRUNCATE vienen_hoy;";
        $query1= mysqli_query($con, $sql1);
        if(!$query1)
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $hacer='eliminar todo';
            $tabla='vienen_hoy';
            $línea='571';
            error($er1,$er2,$hacer,$tabla,$línea);
        }
        //Obtener a los pendientes y guardar sus datos nuevamente
        $contador=count($arregloPendientes);
        for($i=0;$i<$contador;$i++)
        {
            $num=$arregloPendientes[$i][0];
            $entrada=$arregloPendientes[$i][1];
            $salida=$arregloPendientes[$i][2];
            $obs_e=$arregloPendientes[$i][3];
            $obs_s=$arregloPendientes[$i][4];
            $t_Opc=$arregloPendientes[$i][5];
            //Guardar en vienen hoy
            $siExiste=existeEnTablaVienenHoy($num);//sirve para evitar guardar el mismo trabajador
            if($siExiste !== true)
            {
                $sql2="INSERT INTO vienen_hoy VALUES ('','$num','$entrada','$salida',$obs_e,$obs_s,$t_Opc);";
                $query2= mysqli_query($con, $sql2);
                if(!$query2)
                {
                    $er1=mysqli_errno($con);
                    $er2=mysqli_error($con);
                    $hacer='insertar';
                    $tabla='vienen_hoy';
                    $línea='594';
                    error($er1,$er2,$hacer,$tabla,$línea);
                }
            }
        }
        
        //Obtener a las personas que deben venir hoy y guardarlos en la tabla vienen hoy
        for($j=0;$j<$deben_hoy_ultimo;$j++)
        {
            //--------Array deben_hoy---------------//
            $num_deben=$deben_hoy[$j][0];
            $hora_entrada_deben=$deben_hoy[$j][1];
            $hora_salida_deben=$deben_hoy[$j][2];
            $observar_e=$deben_hoy[$j][3];
            $observar_s=$deben_hoy[$j][4];
            //-------------------------------------//
            $t_Opc=revisarSiTieneTurnoOpcional($num_deben);
            if($t_Opc==true)
            {
                $t_Opc=1;
            }
            else
            {
                $t_Opc=0; 
            }
            $siExiste=existeEnTablaVienenHoy($num);
            if($siExiste !== true)
            {
                $sql2="INSERT INTO vienen_hoy VALUES ('','$num_deben', '$hora_entrada_deben','$hora_salida_deben',$observar_e,$observar_s,$t_Opc);";
                $query2= mysqli_query($con, $sql2);
                if(!$query2)
                {
                    $er1=mysqli_errno($con);
                    $er2=mysqli_error($con);
                    $hacer='insertar';
                    $tabla='vienen_hoy';
                    $línea='450';
                    error($er1,$er2,$hacer,$tabla,$línea);
                }
            }
        }
    }//Fin guarda_deben_hoy
?>