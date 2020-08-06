<?php
    date_default_timezone_set('America/Mexico_City'); 
    set_time_limit(600);//Indica que son 600 segundos, es decir 10 minutos máximo para ejecutar todo el script
    $deben_hoy_ultimo=0;
    $deben_hoy=[];
    $observar_e=-1;//-1 Significa que el empleado debe venir
    $observar_s=-1;

    //OBTENER QUE DÍA ES HOY
    $dias = array("domingo","lunes","martes","miercoles","jueves","viernes","sabado");
    //echo "HOY ES ".$dias[date("w")] . "<br>";//esto es solo para ver si el día es correcto
    $diaactual=$dias[date("w")];//guardar el día actual para su posterior uso
    // $diaactual='sabado';
    $f_hoy=date("Y-m-d");//guardar la fecha actual

    $nombre="biometric";
    $contra="5_w**/pQxcmk.";
    require("../Acceso/global.php");

    verSiHoyEsDiaFestivo();
    function verSiHoyEsDiaFestivo()
    {
        global $con;
        global $f_hoy;
        global $diaactual;
        global $deben_hoy;
        global $deben_hoy_ultimo;                   
        global $observar_e;
        global $observar_s;
        $descando="";
        //Ver si el día de hoy está registrado en la tabla día festivo
        $sql="Select iddia_festivo from dia_festivo where fecha='$f_hoy';";
        $query= mysqli_query($con, $sql);
        $fila=mysqli_num_rows($query);
        if($fila==1) //Si hoy es día festivo
        {
            //Seleccionar a todos los que trabajan en día festivo
            $sql="select t.numero_trabajador,c.entrada,c.salida,c.t_horas from trabajador t 
            inner join acceso a on a.trabajador_trabajador = t.numero_trabajador 
            inner join turno c on c.idturno=a.turno_turno 
            and dia_festivo=1 and a.t_dias=-1;";
            $query= mysqli_query($con, $sql);
            $fila=mysqli_num_rows($query);
            if($fila>0)
            {
                while($resul=mysqli_fetch_array($query))
                {
                    $numero=$resul[0];
                    $t_horas=$resul[3];

                    $DiasAcceso=verDiasAccesoParaDiaFestivo($numero);
                    if($DiasAcceso !== null)
                    {
                        $sabado=$DiasAcceso[0];
                        $domingo=$DiasAcceso[1];

                        /*  Sábados y dias festivos
                            t_horas=23:59:00
                            No trabajarán cuando el día festivo sea viernes o domingo
                        */
                        if($sabado==1 && $domingo==0 && ($t_horas=='23:59:00' || $t_horas=='00:00:00') && ($diaactual !== 'viernes' || $diaactual !== 'domingo' ))
                        {
                            $deben_hoy[$deben_hoy_ultimo][0]=$resul[0];    
                            $deben_hoy[$deben_hoy_ultimo][1]=$resul[1]; 
                            $deben_hoy[$deben_hoy_ultimo][2]=$resul[2]; 
                            $deben_hoy[$deben_hoy_ultimo][3]=$observar_e;
                            $deben_hoy[$deben_hoy_ultimo][4]=$observar_s;
                            $deben_hoy_ultimo++;
                        }

                        /*
                            Domingos y dias festivos
                            t_horas=23:59:00
                            No trabajarán cuando el día festivo sea sábado o lunes
                        */
                        if($sabado==0 && $domingo==1 && ($t_horas=='23:59:00' || $t_horas=='00:00:00') && ($diaactual !== 'sabado' || $diaactual !== 'lunes' ))
                        {
                            $deben_hoy[$deben_hoy_ultimo][0]=$resul[0];    
                            $deben_hoy[$deben_hoy_ultimo][1]=$resul[1]; 
                            $deben_hoy[$deben_hoy_ultimo][2]=$resul[2]; 
                            $deben_hoy[$deben_hoy_ultimo][3]=$observar_e;
                            $deben_hoy[$deben_hoy_ultimo][4]=$observar_s;
                            $deben_hoy_ultimo++;
                        }
                        /*  
                            Sábados, domingos y días festivos
                            1.La jornada será diurna o nocturna
                            t_horas=12:00:00
                            Deben laborarse todos los sábados y domingos del año, así como los días que se indican en el Artículo 50
                            Cuando el día festivo sea lunes, se descansará en domingo.
                            Cuando el festivo sea viernes, se descansará el sábado.
                        */
                        if($sabado==1 && $domingo==1 && $t_horas=='12:00:00')  
                        {
                            $deben_hoy[$deben_hoy_ultimo][0]=$resul[0];    
                            $deben_hoy[$deben_hoy_ultimo][1]=$resul[1]; 
                            $deben_hoy[$deben_hoy_ultimo][2]=$resul[2]; 
                            $deben_hoy[$deben_hoy_ultimo][3]=$observar_e;
                            $deben_hoy[$deben_hoy_ultimo][4]=$observar_s;
                            $deben_hoy_ultimo++;

                            if($diaactual == 'lunes')
                            {
                                $actualizado=actualizaAF($resul[0],'domingo');
                            }
                            else
                            {
                                if($diaactual == 'viernes')
                                {
                                    $actualizado=actualizaAF($resul[0],'sabado');
                                }
                            }
                        }
                        /*
                            Sábados, domingos y días festivos
                            2.El personal contratado con jornada de
                            t_horas=08:00:00
                            deberá laborar tres días consecutivos cuando el día festivo corresponda a lunes o viernes, así como los días señalados en el Artículo 50
                        */
                        if($sabado==1 && $domingo==1 && $t_horas=='08:00:00') 
                        {
                            $deben_hoy[$deben_hoy_ultimo][0]=$resul[0];    
                            $deben_hoy[$deben_hoy_ultimo][1]=$resul[1]; 
                            $deben_hoy[$deben_hoy_ultimo][2]=$resul[2]; 
                            $deben_hoy[$deben_hoy_ultimo][3]=$observar_e;
                            $deben_hoy[$deben_hoy_ultimo][4]=$observar_s;
                            $deben_hoy_ultimo++;
                        }
                    }
                }
                checar_todo();
                guarda_deben_hoy();
            }
        }
        else //Sino es día festivo
        {
            orden_acceso_sexta();
            vienen_hoy();
            checar_todo();
            guarda_deben_hoy();
        }
    }

    function actualizaAF($numero,$dia)
    {   global $con;
        /*
            Actualizar en af si el festivo cae en lunes, guardar 0 en domingo o si el festivo cae viernes debe guardarse 0 en sabado 
        */
        //Bucar el id de acceso del trabajador
        $sql="select a.idaf from af a  inner join acceso b where b.trabajador_trabajador='$numero';";
        $query= mysqli_query($con, $sql); 
        if(!$query)
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $hacer='buscar';
            $tabla='af';
            $línea='156';
            error($er1,$er2,$hacer,$tabla,$línea);
        }
        else
        {
            $fila=mysqli_num_rows($query);
            if($fila==1)
            {
                $resul=mysqli_fetch_array($query);
                $resul[0];
            }
        } 
        //Actualizar en posicion el ultimo id seleccionado
        $sql2="UPDATE af SET $dia = '0' WHERE (idaf = '$resul[0]');";
        $query2= mysqli_query($con, $sql2); 
        if(!$query2)
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $hacer='actualizar';
            $tabla='af';
            $línea='172';
            error($er1,$er2,$hacer,$tabla,$línea);
        }
        else
        {
            return true;
        } 
    }
    
/*
    for($i=0; $i<$deben_hoy_ultimo; $i++)
    {
        echo  $deben_hoy[$i][0].' ';
        echo  $deben_hoy[$i][1].' ';
        echo  $deben_hoy[$i][2].' ';
        echo  $deben_hoy[$i][3].' ';
        echo  $deben_hoy[$i][4].' ';
        echo '<br>';
    }*/
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
    //SELECCIONAR A TODOS LOS EMPLEADOS QUE VIENEN HOY Y SU TOTAL DE DÍAS SEA -1. ES DECIR, EL RESTO DE LOS QUE NO TIENEN SEXTA
    function vienen_hoy()
    {
        global $diaactual;
        global $deben_hoy;
        global $deben_hoy_ultimo;
        global $observar_e;
        global $observar_s;
        global $con;
        $sql="select t.numero_trabajador,c.entrada,c.salida from trabajador t 
        inner join acceso a on a.trabajador_trabajador = t.numero_trabajador 
        inner join turno c on c.idturno=a.turno_turno 
        and a.$diaactual=1 and a.t_dias=-1";
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
        global $deben_hoy;
        global $deben_hoy_ultimo;
        $nombre="biometric";
        $contra="5_w**/pQxcmk.";
        require("../Acceso/global.php");
        //Borrar todos los datos de vienen_hoy
        $sql1="TRUNCATE vienen_hoy;";
        $query1= mysqli_query($con, $sql1);
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
        }// fin for
    }//Fin guarda_deben_hoy

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

    //Seleccionar a todos los empleados que tienen sexta 
    function tienenSexta()
    {
        $sexta=[];
        $aumenta=0;
        $nombre="biometric";
        $contra="5_w**/pQxcmk.";
        require("../Acceso/global.php");
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
        $nombre="biometric";
        $contra="5_w**/pQxcmk.";
        require("../Acceso/global.php");
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
        $nombre="biometric";
        $contra="5_w**/pQxcmk.";
        require("../Acceso/global.php");
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

    /*------------------------CHECAR TODO------------------------------------------*/
    //QUIENES DEBEN ASISTIR HOY SI TIENEN GUARDIA 
    function tiene_guardia()
    { 
        global $f_hoy;
        global $deben_hoy;
        global $deben_hoy_ultimo;
        $nombre="biometric";
        $contra="5_w**/pQxcmk.";
        require("../Acceso/global.php");
        
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
        $nombre="biometric";
        $contra="5_w**/pQxcmk.";
        require("../Acceso/global.php");
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
        $nombre="biometric";
        $contra="5_w**/pQxcmk.";
        require("../Acceso/global.php");
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
        Si Incapacidad por gravidez hoy (clave 53).
        Si Incapacidad por accidente o riesgo profesional hoy (clave 54).
        Si Incapacidad médica por enfermedad no profesional (clave 55).
        Si tiene vacaciones hoy (clave 60).
        Si vacaciones por emanaciones radiactivas hoy (clave 62).
        Si vacaciones extraordinarias por premios, estímulos y recompensas (clave 63).
        Si tiene Inasistencia por acto cívico (clave 13).
        Si tiene comisión sindical equivalente a un día(clave 17)
        Si tiene licencia con sueldo por beca total o parcial (clave 51)
        */
        global $deben_hoy;
        global $deben_hoy_ultimo;
        $nombre="biometric";
        $contra="5_w**/pQxcmk.";
        require("../Acceso/global.php");
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
            and 
            (a.clave_especial_clave_especial=13
            or a.clave_especial_clave_especial=17 
            or a.clave_especial_clave_especial=40 
            or a.clave_especial_clave_especial=41
            or a.clave_especial_clave_especial=47 
            or a.clave_especial_clave_especial=51
            or a.clave_especial_clave_especial=53
            or a.clave_especial_clave_especial=54 or a.clave_especial_clave_especial=55
            or a.clave_especial_clave_especial=60 or a.clave_especial_clave_especial=62
            or a.clave_especial_clave_especial=63)";
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
        $nombre="biometric";
        $contra="5_w**/pQxcmk.";
        require("../Acceso/global.php");
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
        $nombre="biometric";
        $contra="5_w**/pQxcmk.";
        require("../Acceso/global.php");
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
?>