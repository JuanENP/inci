<?php
    date_default_timezone_set('America/Mexico_City'); 
    set_time_limit(600);//Indica que son 600 segundos, es decir 10 minutos máximo para ejecutar todo el script
    $deben_hoy_ultimo=0;
    $deben_hoy=[];

    //OBTENER QUE DÍA ES HOY
    $dias = array("domingo","lunes","martes","miercoles","jueves","viernes","sabado");
    //echo "HOY ES ".$dias[date("w")] . "<br>";//esto es solo para ver si el día es correcto
    $diaactual=$dias[date("w")];//guardar el día actual para su posterior uso
    $f_hoy=date("Y-m-d");//guardar la fecha actual

    $nombre="biometric";
    $contra="5_w**/pQxcmk.";
    require("../Acceso/global.php");

    //TODOS LOS EMPLEADOS QUE TIENEN SEXTA VENGAN O NO VENGAN HOY
    function sexta()
    {
        $sexta=[];
        $aumenta=0;
        $nombre="biometric";
        $contra="5_w**/pQxcmk.";
        require("../Acceso/global.php");
        $sql1="select a.numero_trabajador, b.t_dias,b.idacceso,c.idsexta,
        c.t_dias,c.validez,d.entrada,d.salida
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
                $aumenta++;
            }
            return $sexta;
        }
        else
        {
            return null;
        }
    } // FIN TODOS LOS EMPLEADOS QUE TIENEN SEXTA VENGAN O NO VENGAN HOY
    
    //SI EL EMPLEADO VIENE HOY EN ACCESO Y TIENE SEXTA
    function viene_hoy_y_tiene_sexta($numero_empleado)
    {
        global $diaactual;
        $nombre="biometric";
        $contra="5_w**/pQxcmk.";
        require("../Acceso/global.php");
        $sql1="select a.numero_trabajador,
        b.lunes,b.martes,b.miercoles,b.jueves,b.viernes,b.sabado,b.domingo,
        c.lunes,c.martes,c.miercoles,c.jueves,c.viernes,c.sabado,c.domingo,
        b.t_dias,c.t_dias,c.validez,d.entrada,b.idacceso,c.idsexta,d.salida
        from trabajador a 
        inner join acceso b on a.numero_trabajador=b.trabajador_trabajador
        inner join sexta c on a.numero_trabajador=c.trabajador_trabajador
        inner join turno d on b.turno_turno=d.idturno
        and b.$diaactual = 1
        and a.numero_trabajador='$numero_empleado'";
        $query1= mysqli_query($con, $sql1);
        $filas=mysqli_num_rows($query1);
        if($filas>0)
        {
            $resul=mysqli_fetch_array($query1);
            $num_empleado=$resul[0]; 
            $acceso_sexta=$resul[1].$resul[2].$resul[3].$resul[4].$resul[5].$resul[6].$resul[7].$resul[8].$resul[9].$resul[10].$resul[11].$resul[12].$resul[13].$resul[14];  
            $t_dias_acceso=$resul[15];
            $t_dias_sexta=$resul[16];
            $validez=$resul[17];
            $entrada=$resul[18];
            $idacceso=$resul[19];
            $idsexta=$resul[20];
            $salida=$resul[21];
            return[$num_empleado,$acceso_sexta,$t_dias_acceso,$t_dias_sexta,$validez,$entrada,$idacceso,$idsexta,$salida];
        }
        else
        {
            return null;
        }
    }//FIN SI EL EMPLEADO VIENE HOY EN ACCESO Y TIENE SEXTA


    //SI EL EMPLEADO VIENE HOY POR SU SEXTA 
    function viene_hoy_por_sexta($idsexta)
    {
        global $diaactual;
        $nombre="biometric";
        $contra="5_w**/pQxcmk.";
        require("../Acceso/global.php");
        $sql1="SELECT t_dias  FROM sexta where idsexta=$idsexta and $diaactual=1 and validez=1;";
        $query1= mysqli_query($con, $sql1);
        $filas=mysqli_num_rows($query1);
        if($filas>0)
        { 
            return true;
        }
        else
        { 
            return null;
        }
    }//FIN SI EL EMPLEADO VIENE HOY POR SU SEXTA

    orden_acceso_sexta();
   
    //REVISAR EL ORDEN DE LOS DÍAS DE ACCESO Y SEXTA DE LOS EMPLEADOS QUE VIENEN HOY
    function orden_acceso_sexta()
    { 
        $nombre="biometric";
        $contra="5_w**/pQxcmk.";
        require("../Acceso/global.php");
        global $deben_hoy;
        global $deben_hoy_ultimo;
        $cantidad=0;
        $datos_sexta=sexta();
        if($datos_sexta!=null)
        {
            foreach($datos_sexta as $elemento)
            {
                $cantidad++;
            }
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
                //1. TIENE SEXTA Y  TOTAL DE DÍAS DE ACCESO = 3
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
                    $sql3="UPDATE acceso SET  t_dias=4 WHERE (idacceso = $idacceso)";
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
                    
                }//FIN 1. TIENE SEXTA Y  TOTAL DE DÍAS DE ACCESO = 3

                //1.2 BUSCAR SI EL EMPLEADO VIENE HOY Y TIENE SEXTA
                $datos=viene_hoy_y_tiene_sexta($numero_empleado);
                if($datos!=null)
                {
                    //Datos que terminan con v_s = vienen hoy en acceso y tienen sexta
                    $numero_empleado_v_s=$datos[0];
                    $t_dias_acceso_v_s=$datos[2];
                    $t_dias_sexta_v_s=$datos[3];
                    $entrada_v_s=$datos[5]; //hora de entrada
                    $idacceso_v_s=$datos[6];
                    $idsexta_v_s=$datos[7];
                    $salida_v_s=$datos[8];
                    $observar_e=-1;//-1 Significa que el empleado debe venir
                    $observar_s=-1;
                    //Si el orden de acceso y sexta 
                    if($datos[1]=="11100000000011")
                    {
                        //4.SI TOTAL DE DIAS DE SEXTA = 2
                        if($t_dias_sexta_v_s==2)
                        {
                            //Actualizar el total de dias de sexta a 3
                            $sql7="UPDATE sexta SET t_dias=3 WHERE (idsexta = $idsexta_v_s)";
                            $query7= mysqli_query($con, $sql7);
                            if(!$query7)
                            {
                                $er1=mysqli_errno($con);
                                $er2=mysqli_error($con);
                                $hacer='actualizar';
                                $tabla='sexta';
                                $línea='195';
                                error($er1,$er2,$hacer,$tabla,$línea);
                            }
                        }

                        //3. SI TOTAL DE DIAS DE SEXTA = 3
                        if($t_dias_sexta_v_s==3)
                        { 
                            //Actualizar el total de dias de acceso a 0 y validez de sexta y total de dias de sexta a 0
                            $sql5="UPDATE acceso SET t_dias=0 WHERE (idacceso = $idacceso_v_s)";
                            $query5= mysqli_query($con, $sql5);
                            if(!$query5)
                            {
                                $er1=mysqli_errno($con);
                                $er2=mysqli_error($con);
                                $hacer='actualizar';
                                $tabla='acceso';
                                $línea='212';
                                error($er1,$er2,$hacer,$tabla,$línea);
                            }
                            $sql6="UPDATE sexta SET validez=0,t_dias=0 WHERE (idsexta = $idsexta_v_s)";
                            $query6= mysqli_query($con, $sql6);
                            if(!$query6)
                            {
                                $er1=mysqli_errno($con);
                                $er2=mysqli_error($con);
                                $hacer='actualizar';
                                $tabla='sexta';
                                $línea='223';
                                error($er1,$er2,$hacer,$tabla,$línea);
                            }
                        }

                        //2. QUIENES VIENEN HOY Y TOTAL DE DÍAS DE ACCESO < 3
                        if($t_dias_acceso_v_s < 3)
                        {
                            //Guardar en el arreglo
                            $deben_hoy[$deben_hoy_ultimo][0]=$numero_empleado_v_s; 
                            $deben_hoy[$deben_hoy_ultimo][1]=$entrada_v_s;
                            $deben_hoy[$deben_hoy_ultimo][2]=$salida_v_s;
                            $deben_hoy[$deben_hoy_ultimo][3]=$observar_e;//valor de observar entrada sirve para guardarse en la tabla vienen_hoy
                            $deben_hoy[$deben_hoy_ultimo][4]=$observar_s;//valor de observar salida sirve para guardarse en la tabla vienen_hoy
                            $deben_hoy_ultimo++;
                            
                            //Actualizar en Acceso-> total de dias + 1
                            $t_dias_v_s=$t_dias_acceso_v_s+1;
                            $sql4="UPDATE acceso SET t_dias=$t_dias_v_s WHERE (idacceso = $idacceso_v_s)";
                            $query4= mysqli_query($con, $sql4);
                            if(!$query4)
                            {
                                $er1=mysqli_errno($con);
                                $er2=mysqli_error($con);
                                $hacer='actualizar';
                                $tabla='acceso';
                                $línea='249';
                                error($er1,$er2,$hacer,$tabla,$línea);
                            }
                        }
                    }
                    else
                    {
                            //2. QUIENES VIENEN HOY Y TOTAL DE DÍAS DE ACCESO < 3
                        if($t_dias_acceso_v_s < 3)
                        {
                            //Guardar en el arreglo
                            $deben_hoy[$deben_hoy_ultimo][0]=$numero_empleado_v_s; 
                            $deben_hoy[$deben_hoy_ultimo][1]=$entrada_v_s;
                            $deben_hoy[$deben_hoy_ultimo][2]=$salida_v_s;
                            $deben_hoy[$deben_hoy_ultimo][3]=$observar_e;//valor de observar entrada sirve para guardarse en la tabla vienen_hoy
                            $deben_hoy[$deben_hoy_ultimo][4]=$observar_s;//valor de observar salida sirve para guardarse en la tabla vienen_hoy
                            $deben_hoy_ultimo++;
                            
                            //Actualizar en Acceso-> total de dias + 1
                            $t_dias_v_s=$t_dias_acceso_v_s+1;
                            $sql4="UPDATE acceso SET t_dias=$t_dias_v_s WHERE (idacceso = $idacceso_v_s)";
                            $query4= mysqli_query($con, $sql4);
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
                        //3. SI TOTAL DE DIAS DE SEXTA = 3
                        if($t_dias_sexta_v_s==3)
                        { 
                            //Actualizar el total de dias de acceso a 0 y validez de sexta y total de dias de sexta a 0
                            $sql5="UPDATE acceso SET t_dias=0 WHERE (idacceso = $idacceso_v_s)";
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

                            $sql6="UPDATE sexta SET validez=0,t_dias=0 WHERE (idsexta = $idsexta_v_s)";
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
                        }
                            //4.SI TOTAL DE DIAS DE SEXTA = 2
                        if($t_dias_sexta_v_s==2)
                        {
                            //Actualizar el total de dias de sexta a 3
                            $sql7="UPDATE sexta SET t_dias=3 WHERE (idsexta = $idsexta_v_s)";
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
                        }
                    }//Fin else 
                }//Fin del if vienen_hoy_y_tienen_sexta

                //5.SI VIENEN HOY EN SEXTA, ES DECIR VALIDEZ = 1 Y DIA ACTUAL = 1
                $datos2=viene_hoy_por_sexta($idsexta);
                if($datos2=="true")
                {
                    //Agregar al empleado al arreglo
                    $deben_hoy[$deben_hoy_ultimo][0]=$numero_empleado;
                    $deben_hoy[$deben_hoy_ultimo][1]=$entrada;
                    $deben_hoy[$deben_hoy_ultimo][2]=$salida_v_s;
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
    }//FIN REVISAR EL ORDEN DE LOS DÍAS DE ACCESO Y SEXTA DE LOS EMPLEADOS QUE VIENEN HOY

    //SELECCIONAR A TODOS LOS EMPLEADOS QUE VIENEN HOY Y SU TOTAL DE DÍAS SEA -1
    //ES DECIR, EL RESTO DE LOS QUE NO TIENEN SEXTA
    vienen_hoy();
    function vienen_hoy()
    {
        global $diaactual;
        global $deben_hoy;
        global $deben_hoy_ultimo;
        $nombre="biometric";
        $contra="5_w**/pQxcmk.";
        require("../Acceso/global.php");
        $observar_e=-1;//-1 Significa que el empleado debe venir
        $observar_s=-1;
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
                $deben_hoy[$deben_hoy_ultimo][2]=$resul[2];//hora salia
                $deben_hoy[$deben_hoy_ultimo][3]=$observar_e;//valor de observar entrada sirve para guardarse en la tabla vienen_hoy
                $deben_hoy[$deben_hoy_ultimo][4]=$observar_s;//valor de observar salida sirve para guardarse en la tabla vienen_hoy
                $deben_hoy_ultimo++;
            }//fin while        
        }//fin 
    }//FIN SELECCIONAR A TODOS LOS EMPLEADOS QUE VIENEN HOY Y SU TOTAL DE DÍAS SEA -1
    
    checar_todo();
    //6.CHECAR QUIEN TIENE COMISION, LICENCIAS, PERMISOS
    function checar_todo()
    {
        global $deben_hoy;
        tiene_guardia();
        tiene_comision();
        comision_oficial_participacion_curso();
        licencias_permisos();
        lactancia_estancia();
        cumple_ono();
    }//FIN 6.CHECAR QUIEN TIENE COMISION, LICENCIAS, PERMISOS

            ///////////////////CHECAR TODO/////////////////////////////////
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
        { //--------Array deben_hoy---------------//
            $num_deben=$deben_hoy[$j][0];
            //$hora_entrada_deben=$deben_hoy[$j][1];
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
                    // echo "Tiene comisión" ."$num_deben"." ".$primeraletra ."<br>";
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
                    {  //Si la primera letra de la empresa es D significa que la comision tiene Destino otro hospital (Comisión externa)
                        if($primeraletra=="D")
                        {
                            //En el array deben_hoy formatear observar_e y observar_s  a cero
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

    //Guarda todos los empleados que deben venir hoy
    guarda_deben_hoy();
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
            $sql2="INSERT INTO vienen_hoy VALUES ('','$num_deben', '$hora_entrada_deben','$hora_salida_deben',$observar_e,$observar_s);";
            $query2= mysqli_query($con, $sql2);
            if(!$query2)
            {
                $er1=mysqli_errno($con);
                $er2=mysqli_error($con);
                $hacer='insertar';
                $tabla='vienen_hoy';
                $línea='719';
                error($er1,$er2,$hacer,$tabla,$línea);
            }
        }// fin for
    }//Fin guarda_deben_hoy


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