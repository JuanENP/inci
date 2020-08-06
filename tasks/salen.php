<?php
    date_default_timezone_set('America/Mexico_City'); 
    set_time_limit(600);//Indica que son 600 segundos, es decir 10 minutos máximo para ejecutar todo el script
    $nombre="biometric";
    $contra="5_w**/pQxcmk.";
    require("../Acceso/global.php");
    $f_hoy=date("Y-m-d");//guardar la fecha actual sin la hora y en un orden especifico para comparar (Año, mes,dia)
    $quincena=quincenaActual();
    $fec_act=date("Y-m-d H:i:s"); //la fecha y hora actual
    $dias = array("domingo","lunes","martes","miercoles","jueves","viernes","sabado");//obtener el día de hoy
    $diaactual=$dias[date("w")];//guardar el día actual para su posterior uso
    function quincenaActual()
    {
        global $con;
        $sql="SELECT idquincena FROM quincena where validez = 1";
        $query= mysqli_query($con, $sql);
        if($query)
        {
            $filas=mysqli_num_rows($query);
            if($filas==1)
            {   
                $resul=mysqli_fetch_array($query);
                return $resul[0];
            }
        }
    }

    revisionAsistencia();
    function revisionAsistencia()
    {
        global $f_hoy;
        $totalVienen=0;
        $totalChecaronSalida=0;
        $omisionEntradaNormal='18';//Clave 18 omisión de entrada en el registro de asistencia a la jornada laboral.
        $omisionSalidaNormal='19';//Clave 19 omisión de salida en el registro de asistencia a la jornada laboral continua.
        $faltaTurnoOpcional='11';
        $faltaTurnoNormal='10';
        //Obtener a los empleados que vienen hoy.
        $vienen_hoy=vienen_hoy();
        if(!empty($vienen_hoy))
        {
            $totalVienen=count($vienen_hoy);
        }
        //Obtener a los que ya checaron su salida
        $checaronSalida=quienes_asistieron_hoy();
        if(!empty($vienen_hoy))
        {
            $totalChecaronSalida=count($checaronSalida);
        }
        if($totalVienen > 0)
        {
            for($i=0;$i<$totalVienen;$i++)
            {
                $idvienen=$vienen_hoy[$i][0];
                $numero=$vienen_hoy[$i][1];
                $h_entrada=$vienen_hoy[$i][2];
                $h_salida=$vienen_hoy[$i][3];
                $obs_e=$vienen_hoy[$i][4];
                $obs_s=$vienen_hoy[$i][5];
                $t_opc=$vienen_hoy[$i][6];
                if($totalChecaronSalida > 0)
                {
                    for($j=0;$j<$totalChecaronSalida;$j++)
                    {  
                        $num=$checaronSalida[$j][2];
                        if($numero==$num)
                        {
                            $idasistencia=$checaronSalida[$j][0];
                            $fecSalida=$checaronSalida[$j][1];
                            actualizaObservarSalidaEnVienenHoy($idvienen);
                            $aparecePendiente=revisarSiAparecePendiente($idasistencia);
                            if($aparecePendiente==1)//1 significa que si aparece en la tabla pendiente
                            {
                                eliminarAsistenciaPendiente($idasistencia);
                            }
                            //Concatener la fecha de hoy con su hora de salida 
                            $hora_salida=$f_hoy . ' ' . $h_salida;
                            //Revisar si salieron antes de su hora de salida, calcular el total de minutos antes y marcar incidencias 
                            minA($h_salida, $fecSalida, $idasistencia,$numero);
                            //Revisar si en viene hoy tiene observar_e=-1, observar_s=-0
                            $siOmiEnt=verSiTieneOmisionEntrada($numero);
                            if($siOmiEnt==true)
                            {
                                inserta('','',$omisionEntradaNormal,$idasistencia); 
                                //Actualizar observar_e=0;
                                actualizaObservarEntradaEnVienenHoy($idvienen);
                            }
                        }
                    }
                }
                 //Si observar_e=0 y  observar_s=-1
                if($obs_e==0 && $obs_s==-1)
                {
                    $idasistencia=ObtenerIdAsistenciaSiSoloChecoEntrada($numero);
                    //Revisar si la hora de salida ya pasó
                    $comparaHora=comparaHoraActConHoraSalida($numero,$h_entrada,$h_salida);
                    if($comparaHora==true) //Si es verdadero la hora de salida ya pasó
                    {
                        //Actualizar observar_s a 0.
                        actualizaObservarSalidaEnVienenHoy($idvienen);
                        //Eliminarlo si aparece en pendiente
                        $aparecePendiente=revisarSiAparecePendiente($idasistencia);
                        if($aparecePendiente==1)//1 significa que si aparece en la tabla pendiente
                        {
                            eliminarAsistenciaPendiente($idasistencia);
                        }
                        //Ver si tiene turno opcional
                        $tOpc=VerEnVienenHoySiTieneTOpcional($numero);
                        if($tOpc==true)
                        {   
                            insertaFalta($numero,$faltaTurnoOpcional);
                            inserta('','', $omisionSalidaNormal,$idasistencia);
                        }
                        else
                        {   
                            //Validar la jornada del trabajador para saber cuantas faltas tendrá
                            inserta('','', $omisionSalidaNormal,$idasistencia);
                        }
                    }
                    else //Si la hora de salida no ha pasado
                    {
                        $aparecePendiente=revisarSiAparecePendiente($idasistencia);
                        if($aparecePendiente==0)//0 significa que no aparece en la tabla pendiente
                        {
                            //Guardar el id de asistencia en la tabla pendiente para que sea analizado más tarde.
                            insertaAsistenciaPendiente($idasistencia);
                        }
                    }
                }
                if($obs_e==-1 && $obs_s==-1)
                {
                    //Revisar si la hora de salida ya pasó
                    $comparaHora=comparaHoraActConHoraSalida($numero,$h_entrada,$h_salida);
                    if($comparaHora==true) //Si es verdadero la hora de salida ya pasó
                    {
                        //Actualizar observar_s a 0 y observar_s a 0.
                        actualizaObservarEntradaEnVienenHoy($idvienen);
                        actualizaObservarSalidaEnVienenHoy($idvienen);
                        //Revisar si tiene turno opcional
                        $tOpc=VerEnVienenHoySiTieneTOpcional($numero);
                        if($tOpc==true)
                        {
                            insertaFalta($numero,$faltaTurnoNormal);
                            insertaFalta($numero,$faltaTurnoOpcional);
                        }
                        else
                        {
                            //Validar la jornada del trabajador para saber cuantas faltas tendrá
                            $jornada=revisarJornada($numero);
                            if($jornada==0)
                            {
                                insertaFalta($numero,$faltaTurnoNormal);
                            }
                            else
                            {
                                if($jornada==1 || $jornada==2)
                                {
                                    insertaFalta($numero,$faltaTurnoNormal);
                                    insertaFalta($numero,$faltaTurnoNormal);
                                }
                                else
                                {
                                    if($jornada==3)
                                    {
                                        insertaFalta($numero,$faltaTurnoNormal);
                                        insertaFalta($numero,$faltaTurnoNormal);
                                        insertaFalta($numero,$faltaTurnoNormal);
                                        insertaFalta($numero,$faltaTurnoNormal);
                                    }
                                } 
                            } 
                        }
                    }
                }
            }
        }
    }

    function revisarJornada($numero)
    {
        global $con;
        $jornada=0;
        $sql="select lunes,martes,miercoles,jueves,viernes,sabado,domingo,dia_festivo,t_horas
        from trabajador 
        inner join acceso on trabajador_trabajador = numero_trabajador
        inner join turno on idturno=turno_turno
        where trabajador_trabajador='$numero';";
        $query= mysqli_query($con, $sql);
        $fila=mysqli_num_rows($query);
        if($fila==1)
        {
            $resul=mysqli_fetch_array($query);
            // $lu=$resul[0]; $ma=$resul[1]; $mi=$resul[2]; $ju=$resul[3]; $vi=$resul[4]; $sa=$resul[5]; $do=$resul[6]; $fes=$resul[7]; $t_horas=$resul[8];
            $sexta=$resul[0].$resul[1].$resul[2].$resul[3].$resul[4].$resul[5].$resul[6].$resul[7];
            $t_horas=$resul[8];
            
        }
        //Buscar si el empleado exite en la tabla sexta
        $sql="select idsexta from sexta where trabajador_trabajador='$numero';";
        $query= mysqli_query($con, $sql);
        $fila=mysqli_num_rows($query);
        if($fila==1)
        {
            $tieneSexta=1;  //Si tiene sexta
        }
        else
        {
            $tieneSexta=0;  //No tiene sexta
        }
        /*Revisar si trabaja en jornada nocturna acumulada: once horas (Nota: esto no lo valido -> no excediendo de las ocho de la mañana)
        1. Lunes, miércoles y viernes;
        2. Martes, jueves y sábado;
        3. Miércoles, viernes y domingo;
        4. Lunes, miércoles y sábado;
        5. Martes, jueves y domingo;
        6. Lunes, jueves y sábado; o
        7. Martes, viernes y domingos.
        El personal que labore jornada nocturna y esté contratado con jornada de ocho horas:
        lunes, miercoles y viernes.
        Los de sexta o turno discontinuo*/
        if(($t_horas=='11:00:00') && ($sexta=='10101000' || $sexta=='01010100'|| $sexta=='00101010' || $sexta=='10100100' || $sexta=='01010010' || $sexta=='10010100'|| $sexta=='01001010'))
        {
            $jornada=1;
        }
        else
        {
            if(($tieneSexta==1) || (($sexta=='10101000' )&& $t_horas=='08:00:00'))
            {
                $jornada=1;
            }
        }
        //o si trabaja sábado, domingo y día festivo y su total de horas es de 12:00:00.
        if($t_horas=='12:00:00' && $sexta='00000111')
        {
            $jornada=2;
        }
        //o si trabaja sábado o domingo y festivo de 24:00:00 horas
        if(($t_horas=='00:00:00' || $t_horas=='23:59:00') && ($sexta='00000101' || $sexta='00000011'))
        {
            $jornada=3;
        }
        return  $jornada;
    }

    function ObtenerIdAsistenciaSiSoloChecoEntrada($numero)
    {
        global $con;
        global $quincena;
        global $f_hoy;
        $sql="select id from asistencia where quincena_quincena=$quincena and fecha_entrada like '$f_hoy%' and fecha_salida is null and trabajador_trabajador='$numero';";
        $query= mysqli_query($con, $sql);
        if($query)
        {
            $filas=mysqli_num_rows($query);
            if($filas>0)
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

    function vienen_hoy()
    {
        global $con;
        $asisten_hoy=array();
        $aumenta=0;
        $sql="SELECT * FROM vienen_hoy where not(observar_e=0 and observar_s=0);";
        $query= mysqli_query($con, $sql);
        if($query)
        {
            $filas=mysqli_num_rows($query);
            if($filas>0)
            {   
                while($resul=mysqli_fetch_array($query))
                {
                    $asisten_hoy[$aumenta][0]=$resul[0]; //idvienen
                    $asisten_hoy[$aumenta][1]=$resul[1]; //numero
                    $asisten_hoy[$aumenta][2]=$resul[2]; //h entrada
                    $asisten_hoy[$aumenta][3]=$resul[3]; //h salida
                    $asisten_hoy[$aumenta][4]=$resul[4]; //observar_e
                    $asisten_hoy[$aumenta][5]=$resul[5]; //observar_s
                    $asisten_hoy[$aumenta][6]=$resul[6]; //t_op
                    $aumenta++;
                }
                return $asisten_hoy;
            }
            else
            {
                return null;
            }
        }
    }

    function quienes_asistieron_hoy()
    {   //Quienes checaron su salida
        global $con;
        global $f_hoy;
        global $quincena;
        $posUltimoRegistroAsistencia=6;
        $asisten_hoy=array();
        $aumenta=0;
        //Seleccionar los datos de salida de los trabajadores  que estan pendientes de checar salida
        $sql3="select a.id,a.fecha_salida,a.trabajador_trabajador from asistencia a inner join pendiente b where idasistencia=id and fecha_salida is not null;";
        $query3= mysqli_query($con, $sql3);
        $filas=mysqli_num_rows($query3);
        if($filas>0)
        {    
            while($resul3=mysqli_fetch_array($query3))
            {

                $asisten_hoy[$aumenta][0]=$resul3[0];// id asistencia
                $asisten_hoy[$aumenta][1]=$resul3[1];//fecha_salida
                $asisten_hoy[$aumenta][2]=$resul3[2];//numero
                $aumenta++;
                
            } 
        }
        //seleccionar el valor del ultimo id seleccionado
        $sql="SELECT Valor FROM _posicion where idposicion=$posUltimoRegistroAsistencia";
        $query= mysqli_query($con, $sql);
        if($query)
        {
            $filas=mysqli_num_rows($query);
            if($filas==1)
            { 
                $resul=mysqli_fetch_array($query);
                $valor=$resul[0];
            }
        }
            //Seleccionar a todos los que checaron su salida el día de hoy
        $sql2="select id,fecha_salida,trabajador_trabajador  from asistencia where id > $valor and quincena_quincena=$quincena and fecha_salida like '$f_hoy%';";
        $query2= mysqli_query($con, $sql2);
        if($query)
        {
            $filas=mysqli_num_rows($query2);
            if($filas > 0)
            {    
                while($resul2=mysqli_fetch_array($query2))
                {
                    $asisten_hoy[$aumenta][0]=$resul2[0];// id asistencia
                    $asisten_hoy[$aumenta][1]=$resul2[1];//fecha_salida
                    $asisten_hoy[$aumenta][2]=$resul2[2];//numero
                    $posicion=$resul2[0];
                    $aumenta++;
                } 
                //Actualizar en posicion el ultimo id seleccionado
                $sql2="UPDATE _posicion SET Valor = $posicion  WHERE (idposicion = $posUltimoRegistroAsistencia)";
                $query2= mysqli_query($con, $sql2);
                if(!$query2)
                {
                    $er1=mysqli_errno($con);
                    $er2=mysqli_error($con);
                    $hacer='actualizar';
                    $tabla='_posicion';
                    $línea='68';
                    error($er1,$er2,$hacer,$tabla,$línea);
                }   
            }
        }  
        return $asisten_hoy;
    }

    function actualizaObservarSalidaEnVienenHoy($idvienen)
    {
        global $con;
        $sql2="UPDATE vienen_hoy SET observar_s = 0 WHERE (idvienen_hoy = $idvienen);";
        $query2= mysqli_query($con, $sql2);
        if(!$query2)
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $hacer='actualizar';
            $tabla='vienen hoy';
            $línea='107';
            error($er1,$er2,$hacer,$tabla,$línea);
        }
    }

    function actualizaObservarEntradaEnVienenHoy($idvienen)
    {
        global $con;
        $sql2="UPDATE vienen_hoy SET observar_e = 0 WHERE (idvienen_hoy = $idvienen);";
        $query2= mysqli_query($con, $sql2);
        if(!$query2)
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $hacer='actualizar';
            $tabla='vienen hoy';
            $línea='107';
            error($er1,$er2,$hacer,$tabla,$línea);
        }
    }

    function revisarSiAparecePendiente($idasistencia)
    {   
        global $con;
        $sql="SELECT idpendiente FROM pendiente where idasistencia=$idasistencia;";
        $query= mysqli_query($con, $sql);
        $filas=mysqli_num_rows($query);
        //Si el empleado no tiene un pase de salida se marcará una incidencia
        if($filas==0)
        {  
            return 0;
        }
        else
        {
            return 1;
        }
    }

    function eliminarAsistenciaPendiente($idasistencia)
    {   
        global $con;
        $sql="DELETE FROM pendiente WHERE (idasistencia = '$idasistencia');";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $hacer='eliminar';
            $tabla='pendiente';
            $línea='414';
            error($er1,$er2,$hacer,$tabla,$línea);
        }  
    }

    function minutosTranscurridos($fecha_i,$fecha_f)
    {
        $minutos = (strtotime($fecha_i)-strtotime($fecha_f))/60;
        $minutos = abs($minutos); $minutos = floor($minutos);
        return $minutos;
    }

    function minA($fechaO, $fechaS, $id_asis,$numero)
    { 
        global $con;
        $fechaOriginal=strtotime($fechaO);
        $fechaSale=strtotime($fechaS);
        $minMin=1;
        $minMax=120;
        $minMaximoTopc=60;
        $claveRegistrarAntesTnormal='25'; //'25', 'REGISTRAR ANTES DE LA HORA DE SALIDA EN LA JORNADA LABORAL. '
        $claveRegistrarAntesTopc='27'; //'27', 'REGISTRAR ANTES DE LA HORA DE SALIDA DEL TURNO OPCIONAL O PERCEPCIÓN ADICIONAL.  '
        $claveFaltaTopc='11'; //Clave 11 Inasistencia al turno opcional o percepción adicional.
        //Ver si el trabajador salió antes
        if($fechaSale<$fechaOriginal)
        {
            $mt=minutosTranscurridos($fechaO, $fechaS);
            $t_opc=VerEnVienenHoySiTieneTOpcional($numero);
            if(($mt >= $minMin) && ($mt<=$minMax))  //Si el total de minutos está entre 1 y 120, es decir máximo 2 horas
            {
                $tiene=pase_salida($numero);//buscar si el empleado tiene pase de salida
                if($tiene !== true) //Si no tiene pase  marcar incidencias
                {
                    if($t_opc==0) //Si t_op = 0, significa que no tiene turno opcional
                    {
                        inserta($mt,'antes',$claveRegistrarAntesTnormal,$id_asis);  
                    }
                    else //Si t_op = 1, significa que si tiene turno opcional
                    {
                       
                        if($mt < $minMaximoTopc )
                        {
                            inserta($mt,'antes',$claveRegistrarAntesTopc,$id_asis); 
                        }
                        else
                        {
                            if($mt == $minMaximoTopc)
                            {
                                insertaFalta($numero,$claveFaltaTopc); 
                            }
                            else
                            {
                                inserta($mt,'antes',$claveRegistrarAntesTnormal,$id_asis);  
                                insertaFalta($numero,$claveFaltaTopc);  
                            }
                        }
                    }
                }
            }
            else
            {
                if($t_opc==0) //Si t_op = 0, significa que no tiene turno opcional
                {
                    inserta($mt,'antes',$claveRegistrarAntesTnormal,$id_asis);
                }
                else //Si t_op = 1, significa que si tiene turno opcional
                {
                    inserta($mt,'antes',$claveRegistrarAntesTnormal,$id_asis); 
                    insertaFalta($numero,$claveFaltaTopc); 
                }
            }
        }
    }

    function insertaFalta($numero, $clave)
    {   
        global $con;
        global $f_hoy;
        global $quincena;
        $sql="INSERT INTO falta VALUES ('','$f_hoy', '$quincena', '$numero','$clave')";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $hacer='insertar';
            $tabla='falta';
            $línea='355';
            error($er1,$er2,$hacer,$tabla,$línea);
        }  
    }
    
    function insertaAsistenciaPendiente($idasistencia)
    {   
        global $con;
        $sql="INSERT INTO pendiente (idasistencia) VALUES ('$idasistencia');";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $hacer='insertar';
            $tabla='pendiente';
            $línea='446';
            error($er1,$er2,$hacer,$tabla,$línea);
        }  
    }

    function pase_salida($num)
    {
        global $f_hoy;
        global $nombre;
        global $contra;
        require("../Acceso/global.php");  
        //Buscar si el empleado tiene un pase de salida
        $sql1="SELECT idpase_salida FROM pase_salida where trabajador_trabajador='$num' and fecha_uso='$f_hoy'";
        $query1= mysqli_query($con, $sql1);
        $filas1=mysqli_num_rows($query1);
        //Si el empleado no tiene un pase de salida se marcará una incidencia
        if($filas1==0)
        {  
            return false;
        }
    }

    /*Inserta datos en la tabla incidencia y la bitacora incidencias*/ 
    function inserta($mt, $ma_d, $inc,$id_asis) //$mt=minutos,$ma_d= palabra antes o despues, $inc=clave d ela incidencias, $id_asis=id de asistencia
    { 
        global $con;
        //Si mt está vacio no se debe insertar algo en descripcion
        if($mt=='')
        {
            $mt="-";
            $query=mysqli_query($con,"insert into incidencia values(' ', '$mt', '$inc', $id_asis);");
            if(!$query)
            {
                $er1=mysqli_errno($con);
                $er2=mysqli_error($con);
                $hacer='insertar';
                $tabla='incidencia';
                $línea='154';
                error($er1,$er2,$hacer,$tabla,$línea);
            }
        }
        else
        {   //ma_d guarda la palabra antes o despues
            $mt=$mt . " minutos " . $ma_d;
            $query=mysqli_query($con,"insert into incidencia values(' ', '$mt', '$inc', $id_asis);");
            if(!$query)
            {
                $er1=mysqli_errno($con);
                $er2=mysqli_error($con);
                $hacer='insertar';
                $tabla='incidencia';
                $línea='168';
                error($er1,$er2,$hacer,$tabla,$línea);
            }
        }    
    }

    function VerEnVienenHoySiTieneTOpcional($num)
    {
        global $con;
        //Buscar si el empleado tiene un pase de salida
        $sql1="SELECT t_op FROM vienen_hoy where trabajador_trabajador='$num';";
        $query1= mysqli_query($con, $sql1);
        $filas1=mysqli_num_rows($query1);
        //Si t_op==1
        if($filas1==1)
        {  
            $resul=mysqli_fetch_array($query1);
            if($resul[0]==0)
            {
                return 0;//Si es 0 no no tiene turno opcional
            }
            else
            {
                return 1; //Si es uno si tiene turno opcional
            }
        }
    }

    function verSiTieneOmisionEntrada($numero)
    {
        global $con;
        global $f_hoy;
        //Seleccionar a todos los empleados de vienen hoy que siguen con -1
        $sql="SELECT idvienen_hoy FROM vienen_hoy where trabajador_trabajador='$numero' and  observar_e='-1' and observar_s=0;";
        $query= mysqli_query($con, $sql);
        $filas=mysqli_num_rows($query);
        if($filas==1)
        {   
            return true;
        }
        else
        {
            return null;
        }
    }
    
    function comparaHoraActConHoraSalida($numero,$horaEntrada,$horaSalida)
    {   
        global $con;
        global $fec_act;//fecha y hora
        global $f_hoy; //solo fecha
        $t_horas=RestarHoras($horaEntrada,$horaSalida);
        if($t_horas=='00:00:00')
        {
            $t_horas='24:00:00';  
        }
        $fechaEntrada= $f_hoy.' '.$horaEntrada; //servirá para sumarle el total de horas y calcular si ya pasó la hora de salida del trabajador
        $fechaAuxiliar  = strtotime ( "$t_horas" , strtotime ( $fechaEntrada) ) ;  
        $fechaSalida   = date ( 'Y-m-d H:i:s' , $fechaAuxiliar );
        
        if($fec_act>$fechaSalida)
        {
            echo $fec_act.'>'.$fechaSalida;
            return true;
        }
        else
        {   echo $fec_act.'menor'.$fechaSalida;
            return null;
        }
    }

    function RestarHoras($horaini,$horafin)
    {
        $f1 = new DateTime($horaini);
        $f2 = new DateTime($horafin);
        $d = $f1->diff($f2);
        return $d->format('%H hour %I minutes %S second');
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

        $error="Error al $accion en la tabla $nomTabla. $err1 : $err2. Línea de error: $numLinea. Tarea asisten.";
        echo"<script> console.error('$error'); </script>";
        exit();
    }
?>
