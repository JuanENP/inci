<?php
	function consultaNumEmpleado($num)
	{
		global $con;
		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
		$sql="Select numero_trabajador from trabajador where numero_trabajador='$num'";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{ 
			return false; 
		} 
		else
		{
			return true;
		}
	}
	// -------------------Funciones necesarias para el reporte de incidencias------------	
    function incidencias()
    {
		global $con;
		global $quincena;
		global $fila;
		global $ultimo_r;
		global $reporte;
		$anioActual=date('Y');
		//seleccionamos a los empleados que tinen incidencias sin justificar y que sean de base
		$sql="SELECT a.numero_trabajador, CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,
		c.clave_incidencia_clave_incidencia, b.fecha_entrada,b.fecha_salida FROM trabajador a
		inner join asistencia b on a.numero_trabajador=b.trabajador_trabajador and (b.fecha_entrada like '%$anioActual%' or b.fecha_salida like '%$anioActual%')
		inner join incidencia c on b.id=c.asistencia_asistencia
		where b.quincena_quincena=$quincena and a.tipo_tipo=2
		and (c.clave_incidencia_clave_incidencia='01' or c.clave_incidencia_clave_incidencia='02'or c.clave_incidencia_clave_incidencia='03' or c.clave_incidencia_clave_incidencia='04' 
		or c.clave_incidencia_clave_incidencia='05' or c.clave_incidencia_clave_incidencia='07' or c.clave_incidencia_clave_incidencia='16' or c.clave_incidencia_clave_incidencia='18'
		or c.clave_incidencia_clave_incidencia='19' or c.clave_incidencia_clave_incidencia='20' or c.clave_incidencia_clave_incidencia='24'
		or c.clave_incidencia_clave_incidencia='25' or c.clave_incidencia_clave_incidencia='26' or c.clave_incidencia_clave_incidencia='27' 
		or c.clave_incidencia_clave_incidencia='28' or c.clave_incidencia_clave_incidencia='30' or c.clave_incidencia_clave_incidencia='31')
		and NOT EXISTS (SELECT d.clave_justificacion_clave_justificacion FROM justificacion d where c.idincidencia=d.incidencia_incidencia);";
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		//si total es igual a cero significa que no hay datos
		if($resul>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
				$fila[0]=$resul[0];//numero
				$fila[1]=$resul[1];//nom
				$fila[2]=$resul[2];//clave 
				//resul3 fecha entrada
				//resul4 fecha salida
				/*Clave 16 omision de salida de asistencia con jornada discontinua
				Clave 18, omisión de entrada en turno normal
				Clave 19 omision de salida en jornada continua
				Clave 20, omisión de entrada y salida en turno opcional*/
				if($fila[2] !== '16' && $fila[2] !== '18' && $fila[2] !== '19' && $fila[2] !== '20')
				{
					$separar=explode(' ',$resul[3]);//Separar la fecha de entrada de la hora de entrada
					$fecha=$separar[0];
					$separar2=explode('-',$fecha);//Separar la fecha de entrada para obtener solo el día          
					$dia=$separar2[2];
				}
				else
				{	
					//Omision de entrada y salida al turno opcional
					if($fila[2] == '20')
					{
						if($resul[3]=='')//Si la fecha de entrada está vacia revisar la fecha de salida
						{
							$dia=revisarHorario($fila[0],'',$resul[4]);
						}
						else //si la fecha de salida está vacía revisar la fecha de entrada
						{
							$dia=revisarHorario($fila[0],$resul[3],'');
						}
					}
					else
					{
						//Omision de salida
						if($fila[2] == '16' || $fila[2] == '19')
						{
							$dia=revisarHorario($fila[0],$resul[3],'');
						}
						//Omision de entrada
						if($fila[2] == '18')
						{
							$dia=revisarHorario($fila[0],'',$resul[4]);
						}
					}
				}
				//Se guardará el día de la incidencia
				$fila[3]=$dia;
				$reporte[$ultimo_r]=$fila;
				$ultimo_r++;
			}//Fin while
		}
    }

    function revisarHorario($numero,$fechaEntrada,$fechaSalida)
	{
		global $con;
		$sql="select entrada, salida,t_horas from acceso inner join turno where turno_turno=idturno and trabajador_trabajador='$numero';";
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		//si total es igual a cero significa que no hay datos
		if($resul>0)
		{  
		  while($resul=mysqli_fetch_array($query))
			{	
                $horaEntrada=$resul[0];
                $horaSalida=$resul[1];
                $t_horas=$resul[2];
                if($fechaEntrada=='')
				{
                    $dia=obtenerDiaIncidencia($fechaSalida, $horaSalida,$t_horas,'entrada');
                    return $dia;
                }
				else
				{
					if($fechaSalida=='')
					{
                        $dia=obtenerDiaIncidencia($fechaEntrada,$horaEntrada,$t_horas,'salida');
                        return $dia;
					}
				}
			}
		}
    }

    function obtenerDiaIncidencia($fechaSalidaOEntrada,$horaEntOSal,$t_horas,$TipoOmision)
    {
        if($TipoOmision=='salida') //Si tiene una omision de salida es necesaria la fecha de entrada
        {
            //Separar la fecha de entrada
            $separa=explode(' ',$fechaSalidaOEntrada);
            $fecha=$separa[0];
            //Agregar la fecha en la que entró el empleado y la hora de entrada del turno
            $horario=$fecha.' '.$horaEntOSal;
            //Separar el total de horas
            $separaHora=explode(':',$t_horas);
            if($separaHora[0]=='00')
            {
                $hora=24;
            }
            else
            {
                $hora=(int)$separaHora[0];
            }
            if($separaHora[1]=='59')
            {
                $hora++;
                $minuto=0;
            }
            else
            {
                $minuto=(int)$separaHora[1];
            }
            $dia=SumarHoraFecha($horario,$hora,$minuto);
            return $dia;
        }
        else //Sino tienen omision de entrada y por lo tanto es necesario su fecha de salida
        {
            //Separar la fecha de salida
            $separa=explode(' ',$fechaSalidaOEntrada);
            $fecha=$separa[0];
            //Agregar la fecha en la que salió el empleado y la hora de salida del turno
            $horario=$fecha.' '.$horaEntOSal;
            //Revisar si el empleado trabajó horas extra 
            $t_horas_extra=RestarHoras($fechaSalidaOEntrada,$horario);
            //Sumar el total de horas del turno y tambien las horas extra en caso de que tenga
            $totalHoras=SumResMinutosHoras(1,$t_horas, $t_horas_extra);
            $separaHora=explode(':',$totalHoras);
            if($separaHora[0]=='00')
            {
                $hora=24;
            }
            else
            {
                $hora=(int)$separaHora[0];
            }
            if($separaHora[1]=='59')
            {
                $hora++;
                $minuto=0;
            }
            else
            {
                $minuto=(int)$separaHora[1];
            }
            $dia=RestaHoraFecha($fechaSalidaOEntrada,$hora,$minuto);
            return $dia;
        }
    }

    function RestarHoras($horaini,$horafin)
    {
        $f1 = new DateTime($horaini);
        $f2 = new DateTime($horafin);
        $d = $f1->diff($f2);
        return $d->format('%H hour %I minutes %S second');
    }

    function RestaHoraFecha($fecha,$horas, $minutos)
    { 
        $nuevafecha = strtotime($fecha."-$horas hour $minutos minute");
        // $diaFormateado= date("Y-m-d H:i:s",$nuevafecha);
        $diaFormateado= date("d",$nuevafecha);
        return $diaFormateado;
    }

    function SumarHoraFecha($fecha,$horas, $minutos)
    { 
        $nuevafecha = strtotime($fecha."+$horas hour $minutos minute");
        // $diaFormateado= date("Y-m-d H:i:s",$nuevafecha);
        $diaFormateado= date("d",$nuevafecha);
        return $diaFormateado;
    }
    
    function SumResMinutosHoras($operacion,$horario, $minutosASumaroRestar)
    {
        /*Operacion=1->Sumar
            Operacion=2->Restar
        */
        if($operacion==1)
        {
            $date = new DateTime($horario);
            $date->modify("+$minutosASumaroRestar");
            $horaFormateada=$date->format('H:i:s');
            return $horaFormateada;
        }
        else
        {
            if($operacion==2)
            {
              $date = new DateTime($horario);
              $date->modify("-$minutosASumaroRestar");
              $horaFormateada=$date->format('Y-d-m H:i:s');
              return $horaFormateada;
            }
            else
            {
                echo "Parametro *operacion=$operacion* de la función SumarMinutosHoras no admitido";
                exit();
            }
        }
    }

	function cumpleOno_clave14cica()
    {
		global $con;
		global $f_ini;
		global $f_fin;
		global $fila;
		global $ultimo_r;
		global $reporte;
		//Separar las fechas de inicio y fin de la quincena actual
		$separaF_ini=explode('-',$f_ini);
		$separaF_fin=explode('-',$f_fin);
		//seleccionamos a los empleados que tomaron su fecha de cumpleaños en esta quincena
		$sql="select a.numero_trabajador, CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n, b.fecha_cumple from trabajador a 
		inner join cumple_ono b on numero_trabajador=trabajador_trabajador and a.tipo_tipo=2
		and b.validez_tomado=1 and b.validez=0 and (MONTH(fecha_cumple) >= $separaF_ini[1] AND DAY(fecha_cumple) >= $separaF_ini[2])  and (MONTH(fecha_cumple) <= $separaF_fin[1] AND DAY(fecha_cumple) <= $separaF_fin[2]);";
		$query= mysqli_query($con, $sql) or die("<br>" . "Error al seleccionar a los que tuvieron cumpleaños en esta quincena: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
       	if($resul>0)
       	{  
           	while($resul=mysqli_fetch_array($query))
           	{
                $fila[0]=$resul[0];//numero
                $fila[1]=$resul[1];//nom
                $fila[2]='14';//clave 
			   	//Se guardará el día de la incidencia
			   	$separar=explode('-',$resul[2]);//Separar la fecha para obtener solo el día          
			   	$fila[3]=$separar[2];//día
               	$reporte[$ultimo_r]=$fila;
               	$ultimo_r++;
            }//Fin while
		}
		//seleccionamos a los empleados que tomaron su fecha de onomástico en esta quincena
		$sql="select a.numero_trabajador, CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n, b.fecha_ono from trabajador a 
		inner join cumple_ono b on numero_trabajador=trabajador_trabajador and a.tipo_tipo=2
		and b.validez_tomado=1 and b.validez=1 and (MONTH(fecha_ono) >= $separaF_ini[1] AND DAY(fecha_ono) >= $separaF_ini[2])  and (MONTH(fecha_ono) <= $separaF_fin[1] AND DAY(fecha_ono) <= $separaF_fin[2]);";
		$query= mysqli_query($con, $sql) or die("<br>" . "Error al seleccionar a los que tuvieron cumpleaños en esta quincena: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
       	if($resul>0)
       	{  
           	while($resul=mysqli_fetch_array($query))
           	{
                $fila[0]=$resul[0];//numero
                $fila[1]=$resul[1];//nom
                $fila[2]='14';//clave 
			   	//Se guardará el día de la incidencia
			   	$separar2=explode('-',$resul[2]);//Separar la fecha para obtener solo el día          
			   	$fila[3]=$separar2[2];//dia
               	$reporte[$ultimo_r]=$fila;
               	$ultimo_r++;
            }//Fin while
        }//fin else
	}
	
	function faltas()
	{
		global $con;
		global $quincena;
		global $fila;
		global $ultimo_r;
		global $reporte;
		$anioActual=date('Y');
		/*	
			CLAVES:
			10 Día no laborado.
			11 Inasistencia al turno opcional o percepción adicional.
		*/
		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
		$sql="SELECT a.numero_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,b.clave,b.fecha
		FROM trabajador a 
		INNER JOIN falta b on a.numero_trabajador=b.trabajador_trabajador AND b.fecha like '%$anioActual%'
		AND quincena=$quincena AND a.tipo_tipo=2
		AND NOT EXISTS (SELECT c.idjustificar_falta FROM justificar_falta c where b.idfalta=c.falta_falta) order by a.numero_trabajador,b.fecha";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		if($resul>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
				$fila[0]=$resul[0];//numero
				$fila[1]=$resul[1];//nombre
				$fila[2]=$resul[2];//clave
				$fecha=$resul[3];
				$separa=explode('-',$fecha);//Separar la fecha de entrada para obtener solo el día
				$fila[3]=$separa[2];//dia
				$reporte[$ultimo_r]=$fila;
				$ultimo_r++;
			}
		}
	}

	function justificaciones()
	{
		global $con;
		global $quincena;
		global $fila;
		global $ultimo_r;
		global $reporte;
		$anioActual=date('Y');
		$sql="select a.numero_trabajador, CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,
		d.clave_justificacion_clave_justificacion, b.fecha_entrada,b.fecha_salida 
		from trabajador a
		inner join asistencia b on a.numero_trabajador = b.trabajador_trabajador and (b.fecha_entrada like '%$anioActual%' or b.fecha_salida like '%$anioActual%') and quincena_quincena=$quincena
		inner join incidencia c on b.id=c.asistencia_asistencia
		inner join justificacion d on c.idincidencia=d.incidencia_incidencia  
		where (d.clave_justificacion_clave_justificacion=08 or d.clave_justificacion_clave_justificacion=09) and a.tipo_tipo=2;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		//si total es igual a cero significa que no hay datos
		if($resul>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
				$fila[0]=$resul[0];//numero
				$fila[1]=$resul[1];//nombre
				$fila[2]=$resul[2];//clave
				//$resul[3]; //fecha entrada
				if($resul[3]=='')//Si la fecha de entrada está vacia revisar la fecha de salida
				{
					$dia=revisarHorario($fila[0],'',$resul[4]);
				}
				else //si la fecha de salida está vacía revisar la fecha de entrada
				{
					$dia=revisarHorario($fila[0],$resul[3],'');
				}
				$fila[3]=$dia;//dia
				$reporte[$ultimo_r]=$fila;
				$ultimo_r++;
			}
		}
	}

	function especiales()
	{
		global $con;
		global $f_ini;
		global $f_fin;
		global $fila;
		global $ultimo_r;
		global $reporte;
        $anioActual=date('Y');
        //Separar las fechas de inicio y fin de la quincena actual
        $separaF_ini=explode('-',$f_ini);
        $f_ini=$anioActual.'-'.$separaF_ini[1].'-'.$separaF_ini[2];
        $separaF_fin=explode('-',$f_fin);
        $f_fin=$anioActual.'-'.$separaF_fin[1].'-'.$separaF_fin[2];
		$sql="select a.numero_trabajador, CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,b.clave_especial_clave_especial, b.fecha_inicio,b.fecha_fin 
		from trabajador a inner join especial b 
		on a.numero_trabajador = b.trabajador_trabajador and ((b.fecha_inicio >= '$f_ini' or b.fecha_inicio <='$f_ini') and (b.fecha_fin >= '$f_fin' or  b.fecha_inicio <='$f_fin') )
		where a.tipo_tipo=2 and (clave_especial_clave_especial='12' 
		or clave_especial_clave_especial='13' 
		or clave_especial_clave_especial='17'
		or clave_especial_clave_especial='29'
		or clave_especial_clave_especial='40'
		or clave_especial_clave_especial='41'
		or clave_especial_clave_especial='47'
		or clave_especial_clave_especial='53'
		or clave_especial_clave_especial='54'
		or clave_especial_clave_especial='55'
		or clave_especial_clave_especial='61'
		or clave_especial_clave_especial='92'
		or clave_especial_clave_especial='93');";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$fechaSalida=mysqli_num_rows($query);
		//si total es igual a cero significa que no hay datos
		if($fila>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
                $num=$resul[0];//numero
                $nom=$resul[1];//nombre
                $clave=$resul[2];
                $f_ini_especial=$resul[3];
                $f_fin_especial=$resul[4];
                //llamar a la función, $dias es un array con los días del rango
                $dias=obtenDiasDeRango($f_ini,$f_fin);
                //ver el array
                foreach($dias as $fecha)
                {
                    if(($fecha >= $f_ini_especial) && ($fecha <= $f_fin_especial))
                    {
                        $fila[0]=$num;//numero
                        $fila[1]=$nom;//nombre
                        $fila[2]=$clave;//clave
                        $separaEspacio=explode(' ',$fecha);//Separar la fecha para obtener solo la fecha y no la hora
                        $separaGuion=explode('-',$separaEspacio[0]);//Separar la fecha para obtener solo el día
                        $dia=$separaGuion[2];
                        $fila[3]=$dia;//dia
                        $reporte[$ultimo_r]=$fila;
				        $ultimo_r++;
                    }
                }
 			}
		}
    }
    
    function obtenDiasDeRango($rango1, $rango2)
    {
        /*
            Recibe dos parametros tipo string que tengan el formato YYYY-MM-DD y
            devuelve un array con todos los días entre esas dos fechas (rangos) dadas.

            Ejemplo de llamada: $dias=obtenDiasDeRango("2020-08-01","2020-08-15");
        */

        //array a retornar
        $devolverFechas=array();
        //insertar la primera fecha
        $devolverFechas[0]=$rango1;

        $mod_dia=$rango1;

        $diaFinaldeRango=strtotime($rango2);

        for($i=1;$i<15;$i++)
        {
            $mod_dia = strtotime($mod_dia."+ 1 days");//sumar 1 día
            if($mod_dia==$diaFinaldeRango)
            {
                //guardar el último día del rango
                $devolverFechas[$i]=$rango2;
                $i=15;//romper el bucle
            }
            else
            {
                $devolverFechas[$i]=date("Y-m-d",$mod_dia);
                //para que se le sume 1 día
                $mod_dia=date("Y-m-d",$mod_dia);
            }
        }
        return $devolverFechas;
	}
	
	function vacaciones()
	{
		/*
			60 Vacaciones.
			62 Vacaciones por emanaciones radiactivas.
			63 Vacaciones extraordinarias por premios, estímulos y recompensas.
		*/
		global $con;
		global $f_ini;
		global $f_fin;
		global $fila;
		global $ultimo_r;
		global $reporte;
        $anioActual=date('Y');
        //Separar las fechas de inicio y fin de la quincena actual
        $separaF_ini=explode('-',$f_ini);
        $f_ini=$anioActual.'-'.$separaF_ini[1].'-'.$separaF_ini[2];
        $separaF_fin=explode('-',$f_fin);
		$f_fin=$anioActual.'-'.$separaF_fin[1].'-'.$separaF_fin[2];
		//Vacaciones normales clave 60 cica
		$sql=" SELECT a.numero_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,c.periodo,c.dia
		FROM trabajador a 
		INNER JOIN vacaciones b on a.numero_trabajador=b.trabajador_trabajador and a.tipo_tipo=2
		INNER jOIN dias_vacaciones c on b.idvacaciones=c.vacaciones_vacaciones
		where c.dia >='$f_ini' and c.dia <='$f_fin'
		and c.tomado=1 order by  a.numero_trabajador,c.dia;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$fechaSalida=mysqli_num_rows($query);
		if($fila>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
                $num=$resul[0];//numero
                $nom=$resul[1];//nombre
				$clave='60';
				$fecha=$resul[3];
                $fila[0]=$num;//numero
                $fila[1]=$nom;//nombre
                $fila[2]=$clave;//clave
                $separaEspacio=explode(' ',$fecha);//Separar la fecha para obtener solo la fecha y no la hora
                $separaGuion=explode('-',$separaEspacio[0]);//Separar la fecha para obtener solo el día
                $dia=$separaGuion[2];
                $fila[3]=$dia;//dia
                $reporte[$ultimo_r]=$fila;
				$ultimo_r++;
 			}
		}
		//Vacaciones emanaciones radioactivas clave 62 cica
		$sql="SELECT a.numero_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,c.periodo,c.dia
		FROM trabajador a 
		INNER JOIN vacaciones_radio b on a.numero_trabajador=b.trabajador_trabajador and a.tipo_tipo=2
		INNER jOIN dias_vacaciones_radio c on b.idvacaciones_radio=c.vacaciones_vacaciones
		where c.dia >='$f_ini' and c.dia <='$f_fin'
		and c.tomado=1 order by  a.numero_trabajador,c.dia;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$fechaSalida=mysqli_num_rows($query);
		//si total es igual a cero significa que no hay datos
		if($fila>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
                $num=$resul[0];//numero
                $nom=$resul[1];//nombre
				$clave='62';
				$fecha=$resul[3];
                $fila[0]=$num;//numero
                $fila[1]=$nom;//nombre
                $fila[2]=$clave;//clave
                $separaEspacio=explode(' ',$fecha);//Separar la fecha para obtener solo la fecha y no la hora
                $separaGuion=explode('-',$separaEspacio[0]);//Separar la fecha para obtener solo el día
                $dia=$separaGuion[2];
                $fila[3]=$dia;//dia
                $reporte[$ultimo_r]=$fila;
				$ultimo_r++;
 			}
		}
		//Vacaciones extraordinarias clave 63 cica
		$sql="SELECT a.numero_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,b.dia
		FROM trabajador a 
		INNER jOIN vacaciones_extraordinarias b on a.numero_trabajador=b.trabajador_trabajador and a.tipo_tipo=2
		and b.dia >='$f_ini' and b.dia <='$f_fin'
		and b.tomado=1 order by a.numero_trabajador,b.dia;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$fechaSalida=mysqli_num_rows($query);
		//si total es igual a cero significa que no hay datos
		if($fila>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
                $num=$resul[0];//numero
                $nom=$resul[1];//nombre
				$clave='63';
				$fecha=$resul[2];
                $fila[0]=$num;//numero
                $fila[1]=$nom;//nombre
                $fila[2]=$clave;//clave
                $separaEspacio=explode(' ',$fecha);//Separar la fecha para obtener solo la fecha y no la hora
                $separaGuion=explode('-',$separaEspacio[0]);//Separar la fecha para obtener solo el día
                $dia=$separaGuion[2];
                $fila[3]=$dia;//dia
                $reporte[$ultimo_r]=$fila;
				$ultimo_r++;
 			}
		}
	}

	function suspensiones_y_bajas()
	{
		/*
			80 Suspensión en nómina por renuncia.
			81 Suspensión en nómina por defunción.
			82 Suspensión en nómina por cese.
			83 Suspensión en nómina por licencia médica definitiva.
			84 Suspensión en nómina por pensión.
			85 Suspensión en nómina por jubilación.
			86 Suspensión temporal en nómina por sanción administrativa.
			87 Suspensión en nómina por abandono de empleo sin justificación.
			88 Suspensión en nómina por término de interinato.
			89 Suspensión en nómina por término de comisión.
		*/
		global $con;
		global $f_ini;
		global $f_fin;
		global $fila;
		global $ultimo_r;
		global $reporte;
        $anioActual=date('Y');
        //Separar las fechas de inicio y fin de la quincena actual
        $separaF_ini=explode('-',$f_ini);
        $f_ini=$anioActual.'-'.$separaF_ini[1].'-'.$separaF_ini[2];
        $separaF_fin=explode('-',$f_fin);
        $f_fin=$anioActual.'-'.$separaF_fin[1].'-'.$separaF_fin[2];
		$sql="select a.numero_trabajador, CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,b.clave, b.fecha
		from trabajador a inner join estimulos b 
		on a.numero_trabajador = b.trabajador_trabajador and (fecha>='$f_ini' AND fecha<='$f_fin') and a.tipo_tipo=2;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$fechaSalida=mysqli_num_rows($query);
		if($fila>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
                $fila[0]=$resul[0];//numero
                $fila[1]=$resul[1];//nombre
                $fila[2]=$resul[2];//clave
                $fecha=$resul[3];
                $separaGuion=explode('-',$fecha);//Separar la fecha para obtener solo el día
                $dia=$separaGuion[2];
                $fila[3]=$dia;//dia
                $reporte[$ultimo_r]=$fila;
				$ultimo_r++;                
 			}
		}
	}

	function sin_der_estimulo_desempeño_cica78()
	{
		/*
			Clave 78, sin derecho a estímulo de desempeño.
		*/
		global $con;
		global $f_ini;
		global $f_fin;
		global $fila;
		global $ultimo_r;
		global $reporte;
        $anioActual=date('Y');
        //Separar las fechas de inicio y fin de la quincena actual
        $separaF_ini=explode('-',$f_ini);
        $f_ini=$anioActual.'-'.$separaF_ini[1].'-'.$separaF_ini[2];
        $separaF_fin=explode('-',$f_fin);
        $f_fin=$anioActual.'-'.$separaF_fin[1].'-'.$separaF_fin[2];
		$sql="select a.numero_trabajador, CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,b.clave, b.fecha
		from trabajador a inner join estimulos b 
		on a.numero_trabajador = b.trabajador_trabajador and and a.tipo_tipo=2 and (fecha>='$f_ini' AND fecha<='$f_fin')
        and (clave='75' or clave='78');";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$fechaSalida=mysqli_num_rows($query);
		//si total es igual a cero significa que no hay datos
		if($fila>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
                $fila[0]=$resul[0];//numero
                $fila[1]=$resul[1];//nombre
                $fila[2]=$resul[2];//clave
                $fecha=$resul[3];
                $separaGuion=explode('-',$fecha);//Separar la fecha para obtener solo el día
                $dia=$separaGuion[2];
                $fila[3]=$dia;//dia
                $reporte[$ultimo_r]=$fila;
				$ultimo_r++;                
 			}
		}
	}
	
	function pulir()
	{
		global $reporte;
		global $ultimo_r;
		global $arreglo;
		global $c;
		//ordenar el arreglo
		sort($reporte);
		for($i=0;$i<$ultimo_r;$i++)
		{ //echo"<br>"."valor de i dentro del for: ".$i;
			$pivote=$reporte[$i][0];
			$c++;
			$arreglo[$c][0]=$pivote;
			$arreglo[$c][1]=$reporte[$i][1];
			$arreglo[$c][2]=$reporte[$i][2];
			$arreglo[$c][3]=$reporte[$i][3];
			
			for($j=$i+1;$j<$ultimo_r;$j++)
			{
				//echo"<br>"."valor de j dentro del for: ".$j;
				if($pivote==$reporte[$j][0])
				{
					//echo "<br>";
					$arreglo[$c][2]=$arreglo[$c][2] .','. $reporte[$j][2];
					$arreglo[$c][3]=$arreglo[$c][3] .','. $reporte[$j][3];
					if($j==($ultimo_r-1))//Para romper el bucle principal en cuanto se alcance la última posición $reporte
					{
						$i=$ultimo_r-1;
					}
				}
				else
				{
					$i=$j-1;
					//echo"<br>"."valor de i dentro del else: ".$i;
					$j=$ultimo_r;
				}
			}
		}
	}
	//-----------------------------------------------------------------------------------
	function error($er1,$er2,$accion,$nomTabla,$numLinea,$posibleError)
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

		$error="Error al $accion en la tabla $nomTabla. $err1 : $err2. $posibleError. ";
		echo"<script> alert('$error'); </script>";
		exit();
	}
	
	function informacionHospital()
	{
		global $con;
		global $datos;
		global $contador_d;
		//Seleccionamos a los empleados que tienen pase de salida hoy
		$sql="SELECT * FROM hospital;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		//si total es igual a cero significa que no hay datos
		if($resul==1)
		{  
			$fila=mysqli_fetch_array($query);
			return [$fila[0],$fila[1],$fila[2]];	
		} 
		else
		{
			return null;
		}
	}
	//-------------------------------------------------------------------------------------
	//Reporte de vacaciones
	function buscarxfecha()
	{
		global $con;
		global $f_ini;
		global $f_fin;
		global $datos;
		global $contador_d;
		$sql="SELECT a.numero_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,c.periodo,c.dia
		FROM trabajador a 
		INNER JOIN vacaciones b on a.numero_trabajador=b.trabajador_trabajador
		INNER jOIN dias_vacaciones c on b.idvacaciones=c.vacaciones_vacaciones
		where c.dia >='$f_ini' and c.dia <='$f_fin'
		and c.tomado=1
		order by  a.numero_trabajador,c.dia;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		if($resul>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
				$datos[$contador_d][0]=$resul[0];//numero
				$datos[$contador_d][1]=$resul[1];//nombre
				$datos[$contador_d][2]="60";//Clave	
				$datos[$contador_d][3]=$resul[2];//Periodo
				$datos[$contador_d][4]=$resul[3];//fecha
				$datos[$contador_d][5]=1;//t_dias
				$contador_d++;
			}
		}

	}

	function buscarxquincena()
	{
		global $con;
		global $f_ini;
		global $f_fin;
		global $datos;
		global $contador_d;
		$sql="SELECT a.numero_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,c.periodo,c.dia
		FROM trabajador a 
		INNER JOIN vacaciones b on a.numero_trabajador=b.trabajador_trabajador
		INNER jOIN dias_vacaciones c on b.idvacaciones=c.vacaciones_vacaciones
		where c.dia >='$f_ini' and c.dia <='$f_fin'
		and c.tomado=1
		order by  a.numero_trabajador,c.dia;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		if($resul>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
				$datos[$contador_d][0]=$resul[0];//numero
				$datos[$contador_d][1]=$resul[1];//nombre
				$datos[$contador_d][2]="60";//Clave	
				$datos[$contador_d][3]=$resul[2];//Periodo
				$datos[$contador_d][4]=$resul[3];//fecha
				$datos[$contador_d][5]=1;//t_dias
				$contador_d++;
			}
		}		
	}//fin function xquincena

	function buscarxnumero()
	{
		global $con;
		global $num;
		global $datos;
		global $contador_d;
		$sql="SELECT a.numero_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,c.periodo,c.dia
		FROM trabajador a 
		INNER JOIN vacaciones b on a.numero_trabajador=b.trabajador_trabajador
		INNER jOIN dias_vacaciones c on b.idvacaciones=c.vacaciones_vacaciones
		where b.trabajador_trabajador='$num'
		and c.tomado=1
		order by a.numero_trabajador,c.dia;";  
		$query= mysqli_query($con, $sql);
		$filas=mysqli_num_rows($query);
		if($filas>0)
		{
			while($resul=mysqli_fetch_array($query))
			{
				$datos[$contador_d][0]=$resul[0];//numero
				$datos[$contador_d][1]=$resul[1];//nombre
				$datos[$contador_d][2]="60";//Clave	
				$datos[$contador_d][3]=$resul[2];//Periodo
				$datos[$contador_d][4]=$resul[3];//fecha
				$datos[$contador_d][5]=1;//t_dias
				$contador_d++;
			}
		}
		else
		{
			echo "<script>imprime('No hay datos')</script>"; 
		}
		
	}//fin function xnumero

	function pulirVacaciones()
	{
		global $datos;
		global $contador_d;
		global $arreglo;
		global $c;
		//ordenar el arreglo
		sort($datos);
		for($i=0;$i<$contador_d;$i++)
		{ //echo"<br>"."valor de i dentro del for: ".$i;
			$pivote=$datos[$i][0];
			$c++;
			$arreglo[$c][0]=$pivote;
			$arreglo[$c][1]=$datos[$i][1];
			$arreglo[$c][2]=$datos[$i][2];
			$arreglo[$c][3]=$datos[$i][3];
			
			for($j=$i+1;$j<$contador_d;$j++)
			{
				//echo"<br>"."valor de j dentro del for: ".$j;
				if($pivote==$datos[$j][0])
				{
					//echo "<br>";
					$arreglo[$c][2]=$arreglo[$c][2] .','. $datos[$j][2];
					//echo "<br>" . $datos[$c][2];
					$arreglo[$c][3]=$arreglo[$c][3] .','. $datos[$j][3];
					//echo "<br>" . $datos[$c][3];
					if($j==($contador_d-1))//Para romper el bucle principal en cuanto se alcance la última posición $datos
					{
						$i=$contador_d-1;
					}
				}
				else
				{ 
					$i=$j-1;
					//echo"<br>"."valor de i dentro del else: ".$i;
					$j=$contador_d;
				}
			}
		}
	}

	function vienen_hoy()
	{					
		global $con;
		global $dato;
		global $conta;
		$sql="Select a.numero_trabajador, CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n, a.depto_depto,a.categoria_categoria,c.turno_turno,d.entrada, d.salida
		from trabajador a 
        inner join vienen_hoy b on a.numero_trabajador=b.trabajador_trabajador 
        inner join acceso c on a.numero_trabajador=c.trabajador_trabajador 
        inner join turno d on c.turno_turno=d.idturno
		order by a.depto_depto,a.categoria_categoria,d.entrada,d.salida,a.numero_trabajador;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		if($resul>0)
		{
			while($resul=mysqli_fetch_array($query))
			{
				$dato[$conta][0]=$resul[0];//numero trabajador
				$dato[$conta][1]=$resul[1];//nombre
				$dato[$conta][2]=$resul[2];//depto
				$dato[$conta][3]=$resul[3];//categoria
				$dato[$conta][4]=$resul[4];//turno
				$dato[$conta][5]=$resul[5];//hora entrada
				$dato[$conta][6]=$resul[6];//hora salida
				$conta++;
			}
		}
	}	

	function vienenxfestivo() 
	{
		global $con;
		global $diaSemana;
		global $dato;
		global $conta;
		$sql="Select a.numero_trabajador, CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria,b.turno_turno,c.entrada, c.salida
		from trabajador a
		inner join acceso b on a.numero_trabajador=b.trabajador_trabajador
        inner join turno c on b.turno_turno=c.idturno
		and b.$diaSemana =1 and b.t_dias<=3;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		if($resul>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
				$dato[$conta][0]=$resul[0];//numero trabajador
				$dato[$conta][1]=$resul[1];//nombre
				$dato[$conta][2]=$resul[2];//depto
				$dato[$conta][3]=$resul[3];//categoria
				$dato[$conta][4]=$resul[4];//turno
				$dato[$conta][5]=$resul[5];//hora entrada
				$dato[$conta][6]=$resul[6];//hora salida
				$conta++;
			}
		}
	}

	function vienenxacceso() 
	{
		global $con;
		global $diaSemana;
		global $dato;
		global $conta;
		$sql="Select a.numero_trabajador, CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria,b.turno_turno,c.entrada, c.salida
		from trabajador a
		inner join acceso b on a.numero_trabajador=b.trabajador_trabajador
        inner join turno c on b.turno_turno=c.idturno
		and b.$diaSemana =1 and b.t_dias<=3;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		if($resul>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
				$dato[$conta][0]=$resul[0];//numero trabajador
				$dato[$conta][1]=$resul[1];//nombre
				$dato[$conta][2]=$resul[2];//depto
				$dato[$conta][3]=$resul[3];//categoria
				$dato[$conta][4]=$resul[4];//turno
				$dato[$conta][5]=$resul[5];//hora entrada
				$dato[$conta][6]=$resul[6];//hora salida
				$conta++;
			}
		}
	}

	function vienenxsexta()
	{				
		global $con;
		global $diaSemana;
		global $dato;
		global $conta;
		$sql="Select a.numero_trabajador, CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n, a.depto_depto,a.categoria_categoria,b.turno_turno,c.entrada, c.salida
		from trabajador a
		inner join sexta b on a.numero_trabajador=b.trabajador_trabajador
		inner join turno c on b.turno_turno=c.idturno
		and b.$diaSemana=1 and b.validez=1 and b.t_dias<=3;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		if($resul>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
				$dato[$conta][0]=$resul[0];//numero trabajador
				$dato[$conta][1]=$resul[1];//nombre
				$dato[$conta][2]=$resul[2];//depto
				$dato[$conta][3]=$resul[3];//categoria
				$dato[$conta][4]=$resul[4];//turno
				$dato[$conta][5]=$resul[5];//hora entrada
				$dato[$conta][6]=$resul[6];//hora salida
				$conta++;
			}
		}
	}

	function asistenciaxfecha()
	{
		global $con;
		global $fecha_elegida;
		global $diaSemana;
		$sql="Select iddia_festivo from dia_festivo where fecha='$fecha_elegida';";
        $query= mysqli_query($con, $sql);
        $fila=mysqli_num_rows($query);
        if($fila==1) //Si hoy es día festivo, tienen que venir los de día festivo
        {

		}
		else
		{

		}
		vienenxacceso();
		vienenxsexta();
		global $dato;	
        tiene_guardia();
        tiene_comision();
        comision_oficial_participacion_curso();
        licencias_permisos();
       	cumple_ono();
	}

	//QUIENES DEBEN ASISTIR HOY SI TIENEN GUARDIA 
	function tiene_guardia()
    { 
        global $f_hoy;
        global $dato;
		global $conta;
		global $fecha;
		global $con;        
        for($j=0;$j<$conta;$j++)
        { //--------Array deben_hoy---------------//
            $num_deben=$dato[$j][0];
            //-------------------------------------//
            $sql="select c.trabajador_suplente, CONCAT(t.nombre, ' ', t.apellido_paterno, ' ', t.apellido_materno) as n from trabajador t 
            inner join guardias c 
		    on c.fecha_guardia='$fecha'
            and c.trabajador_suplente=t.numero_trabajador and c.trabajador_solicitante='$num_deben';";
            $query= mysqli_query($con, $sql);
            $filas=mysqli_num_rows($query);
            if($filas>0)
            {
                while($resul=mysqli_fetch_array($query))
                {
					 $dato[$j][0]=$resul[0];
					 $dato[$j][1]=$resul[1];
                }         
            }
        }
	}//FIN QUIENES DEBEN ASISTIR HOY SI TIENEN GUARDIA 
	
	//QUIEN TIENE COMISION
	function tiene_comision()
	{
		global $dato;
		global $conta;
		global $con;
		for($j=0;$j<$conta;$j++)
		{ 	//--------Array deben_hoy---------------//
			$num_deben=$dato[$j][0];
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
					//Obtener la primera letra del nombre de la empresa
					$primeraletra=substr("$empresa",0, 1);
					// echo "Tiene comisión" ."$num_deben"." ".$primeraletra ."<br>";
					//Si la primera letra de la empresa es A significa que la comision es Aquí en Zapata (Comisión interna)
					if($primeraletra=="D")
					{
						//Se debe eliminar de la lista al empleado, ya que no se presentará
						unset($dato[$j]);
						$conta--; //Disminuirá el contador
						$dato=array_values($dato);//El arreglo deberá reordenarse
					}
				}
			}
		}
	}// FIN DE FUNCION TIENE_COMISION

    //QUIEN TIENE COMISION OFICIAL O PARTICIPACION EN CURSO DE CAPACITACION
    function comision_oficial_participacion_curso()
    {
        //Si participación en curso de capacitación, adiestramiento o especialización hoy ( clave 29).
        //Si Comisión oficial con o sin viáticos o que comprenda menos de un día (clave 61).
		global $dato;
		global $conta;
		global $con;
        for($j=0;$j<$conta;$j++)
        {
            //--------Array deben_hoy---------------//
            $num_deben=$dato[$j][0];
            //-------------------------------------//
            $sql="SELECT a.hora_entrada,a.hora_salida  FROM especial a
            inner join trabajador b 
            on b.numero_trabajador = a.trabajador_trabajador 
            and a.validez = 1 and b.numero_trabajador='$num_deben' 
            and (a.clave_especial_clave_especial='29' or a.clave_especial_clave_especial='61');";
            $query= mysqli_query($con, $sql);
            $filas=mysqli_num_rows($query);
            if($filas>0)
            { 
                while($resul=mysqli_fetch_array($query))
                {
					$hora_entrada_especial=$resul[0];
					$hora_salida_especial=$resul[0];
                    /*Ver si ese empleado tiene hora de entrada = 00:00:00, significa que el empleado no deberá asistir durante el tiempo 
                    de la capacitación o comisión*/
                    if($hora_entrada_especial=="00:00:00" && $hora_entrada_especial=="00:00:00")
                    {
						//Se debe eliminar de la lista al empleado, ya que no se presentará
						unset($dato[$j]);
						$conta--; //Disminuirá el contador
						$dato=array_values($dato);//El arreglo deberá reordenarse
                    }      
                }
            } 
        }
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
		global $dato;
		global $conta;
		global $con;
        for($j=0;$j<$conta;$j++)
        {
            //--------Array deben_hoy---------------//
            $num_deben=$dato[$j][0];
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
            or a.clave_especial_clave_especial=54 or a.clave_especial_clave_especial=55
            or a.clave_especial_clave_especial=60 or a.clave_especial_clave_especial=62
            or a.clave_especial_clave_especial=63)";
            $query= mysqli_query($con, $sql);
            $filas=mysqli_num_rows($query);
            //Si la consulta arroja 1 valor, significa que el empleado no asistirá 
            if($filas>0)
            { 
				//Se debe eliminar de la lista al empleado, ya que no se presentará
				unset($dato[$j]);
				$conta--; //Disminuirá el contador
				$dato=array_values($dato);//El arreglo deberá reordenarse
            }
        }
    }//FIN QUIEN TIENE LICENCIAS O PERMISOS

   //QUIEN TIENE CUMPLEAÑOS U ONOMASTICO
    function cumple_ono()
    {   
		global $f_hoy;
		global $dato;
		global $conta;
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
        for($j=0;$j<$conta;$j++)
        {
            //--------Array deben_hoy---------------//
            $num_deben=$dato[$j][0];
            //-------------------------------------//
            $sql="select numero_trabajador from trabajador a
            inner join cumple_ono b on a.numero_trabajador=b.trabajador_trabajador
            and ((fecha_cumple='$f_hoy' and validez=0) or (fecha_ono='$f_hoy' and validez=1))
            and numero_trabajador='$num_deben';";
            $query= mysqli_query($con, $sql);
            $filas=mysqli_num_rows($query);
            //Si la consulta arroja 1 valor, significa que el empleado no asistirá 
            if($filas>0)
            { 	while($resul=mysqli_fetch_array($query))
                {
					//Se debe eliminar de la lista al empleado, ya que no se presentará
					unset($dato[$j]);
					$conta--; //Disminuirá el contador
					$dato=array_values($dato);//El arreglo deberá reordenarse
				}
            } 
        }
    }//FIN QUIEN TIENE CUMPLEAÑOS U ONOMASTICO
	/////////////////////////////////////////////////////
	
	//QUIEN TIENE COMISIONES
	function activasFora()
	{
		global $con;
		global $datos;
		global $contador_d;
		$sql="Select a.numero_trabajador, CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria,a.genero,b.fecha_inicio,b.fecha_fin,
		b.hora_entrada,b.hora_salida,b.empresa,b.duracion 
		from trabajador a inner join especial b
		on a.numero_trabajador=b.trabajador_trabajador
		and b.clave_especial_clave_especial='CS'
		and a.tipo_tipo=4 and b.validez=1
		group by a.numero_trabajador,a.genero,a.depto_depto,a.depto_depto,a.categoria_categoria,b.fecha_inicio,b.fecha_fin,
		b.hora_entrada,b.hora_salida,b.empresa,b.duracion; ";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		if($resul>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
				$datos[$contador_d][0]=$resul[0];//numero
				$datos[$contador_d][1]=$resul[1];//nombre
				$datos[$contador_d][2]=$resul[2];//depto	
				$datos[$contador_d][3]=$resul[3];//categoria
				$datos[$contador_d][4]=$resul[4];//genero
				$datos[$contador_d][5]=$resul[5];//fecha inicio
				$datos[$contador_d][6]=$resul[6];//fecha fin
				$datos[$contador_d][7]=$resul[7];//hora entrada
				$datos[$contador_d][8]=$resul[8];//hora salida
				$emp=$resul[9];
				$resultado = substr($emp, 1);//quitar la primera letra de la empresa
				$datos[$contador_d][9]=$resultado;//empresa
				$datos[$contador_d][10]=$resul[10];//duracion
				$contador_d++;
			}
		}
	}

	function inactivasFora()
	{	
		global $con;
		global $datos;
		global $contador_d;
		$sql="Select a.numero_trabajador, CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria,a.genero,b.fecha_inicio,b.fecha_fin,
		b.hora_entrada,b.hora_salida,b.empresa,b.duracion 
		from trabajador a inner join especial b
		on a.numero_trabajador=b.trabajador_trabajador
		and b.clave_especial_clave_especial='CS' 
		and a.tipo_tipo=4 and b.validez=0
		group by a.numero_trabajador,a.genero,a.depto_depto,a.depto_depto,a.categoria_categoria,b.fecha_inicio,b.fecha_fin,
		b.hora_entrada,b.hora_salida,b.empresa,b.duracion; ";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		if($resul>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
				$datos[$contador_d][0]=$resul[0];//numero
				$datos[$contador_d][1]=$resul[1];//nombre
				$datos[$contador_d][2]=$resul[2];//depto	
				$datos[$contador_d][3]=$resul[3];//categoria
				$datos[$contador_d][4]=$resul[4];//genero
				$datos[$contador_d][5]=$resul[5];//fecha inicio
				$datos[$contador_d][6]=$resul[6];//fecha fin
				$datos[$contador_d][7]=$resul[7];//hora entrada
				$datos[$contador_d][8]=$resul[8];//hora salida
				$emp=$resul[9];
				$resultado = substr($emp, 1);//quitar la primera letra de la empresa
				$datos[$contador_d][9]=$resultado;//empresa
				$datos[$contador_d][10]=$resul[10];//duracion
				$contador_d++;
			}
		}
	}

	function activasInt()
	{
		global $con;
		global $datos;
		global $contador_d;
		$sql="Select a.numero_trabajador, CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria,a.genero,b.fecha_inicio,b.fecha_fin,
		b.hora_entrada,b.hora_salida,b.empresa,b.duracion 
		from trabajador a inner join especial b
		on a.numero_trabajador=b.trabajador_trabajador
		and b.clave_especial_clave_especial='CS'
		and a.tipo_tipo!=4 and b.validez=1
		and b.empresa='AZAPATA'
		group by a.numero_trabajador,a.genero,a.depto_depto,a.depto_depto,a.categoria_categoria,b.fecha_inicio,b.fecha_fin,
		b.hora_entrada,b.hora_salida,b.empresa,b.duracion;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		if($resul>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{			
				$datos[$contador_d][0]=$resul[0];//numero
				$datos[$contador_d][1]=$resul[1];//nombre
				$datos[$contador_d][2]=$resul[2];//depto	
				$datos[$contador_d][3]=$resul[3];//categoria
				$datos[$contador_d][4]=$resul[4];//genero
				$datos[$contador_d][5]=$resul[5];//fecha inicio
				$datos[$contador_d][6]=$resul[6];//fecha fin
				$datos[$contador_d][7]=$resul[7];//hora entrada
				$datos[$contador_d][8]=$resul[8];//hora salida
				$emp=$resul[9];
				$resultado = substr($emp, 1);//quitar la primera letra de la empresa
				$datos[$contador_d][9]=$resultado;//empresa
				$datos[$contador_d][10]=$resul[10];//duracion
				$contador_d++;
			}
		}
	}//fin function 

	function inactivasInt()
	{	
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		global $datos;
		global $contador_d;
		$sql="Select a.numero_trabajador, CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria,a.genero,b.fecha_inicio,b.fecha_fin,
		b.hora_entrada,b.hora_salida,b.empresa,b.duracion 
		from trabajador a inner join especial b
		on a.numero_trabajador=b.trabajador_trabajador
		and b.clave_especial_clave_especial='CS' 
		and a.tipo_tipo!=4 and b.validez=0
		and b.empresa='AZAPATA'
		group by a.numero_trabajador,a.genero,a.depto_depto,a.depto_depto,a.categoria_categoria,b.fecha_inicio,b.fecha_fin,
		b.hora_entrada,b.hora_salida,b.empresa,b.duracion;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		if($resul>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
				$datos[$contador_d][0]=$resul[0];//numero
				$datos[$contador_d][1]=$resul[1];//nombre
				$datos[$contador_d][2]=$resul[2];//depto	
				$datos[$contador_d][3]=$resul[3];//categoria
				$datos[$contador_d][4]=$resul[4];//genero
				$datos[$contador_d][5]=$resul[5];//fecha inicio
				$datos[$contador_d][6]=$resul[6];//fecha fin
				$datos[$contador_d][7]=$resul[7];//hora entrada
				$datos[$contador_d][8]=$resul[8];//hora salida
				$emp=$resul[9];
				$resultado = substr($emp, 1);//quitar la primera letra de la empresa
				$datos[$contador_d][9]=$resultado;//empresa
				$datos[$contador_d][10]=$resul[10];//duracion
				$contador_d++;
			}
		}
	}//fin function 

	function inactivasExt()
	{
		global $con;
		global $datos;
		global $contador_d;
		$sql="Select a.numero_trabajador, CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria,a.genero,b.fecha_inicio,b.fecha_fin,
		b.hora_entrada,b.hora_salida,b.empresa,b.duracion 
		from trabajador a inner join especial b
		on a.numero_trabajador=b.trabajador_trabajador 
		and b.clave_especial_clave_especial='CS'
		and a.tipo_tipo!=4 and b.validez=0
		and b.empresa regexp '^D.'
		group by a.numero_trabajador,a.genero,a.depto_depto,a.depto_depto,a.categoria_categoria,b.fecha_inicio,b.fecha_fin,
		b.hora_entrada,b.hora_salida,b.empresa,b.duracion;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		if($resul>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
				$datos[$contador_d][0]=$resul[0];//numero
				$datos[$contador_d][1]=$resul[1];//nombre
				$datos[$contador_d][2]=$resul[2];//depto	
				$datos[$contador_d][3]=$resul[3];//categoria
				$datos[$contador_d][4]=$resul[4];//genero
				$datos[$contador_d][5]=$resul[5];//fecha inicio
				$datos[$contador_d][6]=$resul[6];//fecha fin
				$datos[$contador_d][7]=$resul[7];//hora entrada
				$datos[$contador_d][8]=$resul[8];//hora salida
				$emp=$resul[9];
				$resultado = substr($emp, 1);//quitar la primera letra de la empresa
				$datos[$contador_d][9]=$resultado;//empresa
				$datos[$contador_d][10]=$resul[10];//duracion
				$contador_d++;
			}
		}
	}//fin function 

	function activasExt()
	{
		global $con;
		global $datos;
		global $contador_d;
		$sql="Select a.numero_trabajador, CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria,a.genero,b.fecha_inicio,b.fecha_fin,
		b.hora_entrada,b.hora_salida,b.empresa,b.duracion 
		from trabajador a inner join especial b
		on a.numero_trabajador=b.trabajador_trabajador 
		and b.clave_especial_clave_especial='CS'
		and a.tipo_tipo!=4 and b.validez=1
		and b.empresa regexp '^D.'
		group by a.numero_trabajador,a.genero,a.depto_depto,a.depto_depto,a.categoria_categoria,b.fecha_inicio,b.fecha_fin,
		b.hora_entrada,b.hora_salida,b.empresa,b.duracion;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		if($resul>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
				$datos[$contador_d][0]=$resul[0];//numero
				$datos[$contador_d][1]=$resul[1];//nombre
				$datos[$contador_d][2]=$resul[2];//depto	
				$datos[$contador_d][3]=$resul[3];//categoria
				$datos[$contador_d][4]=$resul[4];//genero
				$datos[$contador_d][5]=$resul[5];//fecha inicio
				$datos[$contador_d][6]=$resul[6];//fecha fin
				$datos[$contador_d][7]=$resul[7];//hora entrada
				$datos[$contador_d][8]=$resul[8];//hora salida
				$emp=$resul[9];
				$resultado = substr($emp, 1);//quitar la primera letra de la empresa
				$datos[$contador_d][9]=$resultado;//empresa
				$datos[$contador_d][10]=$resul[10];//duracion
				$contador_d++;
			}
		}
	}//fin function 

	function comisionesxvencer()
	{
		global $con;
		global $datos;
		global $contador_d;
		$total=0;
		comisionesActivas();
		foreach($datos as $fila)
		{
			$total++;
		}
		for($i=0;$i<$total;$i++)
		{
			$fec=$datos[$i][6];
			$duracion=calcularDuracionEntreDosFechas(1,$fec,"");
			//Si la duracion es mayor a 7 dias, aun no está proxima a vencer
			if($duracion>7)
			{
				//Se debe eliminar de la lista al empleado, ya que no se presentará
				unset($datos[$i]);
				$contador_d--; //Disminuirá el contador
				$datos=array_values($datos);//El arreglo deberá reordenarse
			}
		}
	}
	
	function comisionesActivas()
	{
		global $con;
		global $datos;
		global $contador_d;
		$sql="Select a.numero_trabajador, CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria,a.genero,b.fecha_inicio,b.fecha_fin,
		b.hora_entrada,b.hora_salida,b.empresa,b.duracion 
		from trabajador a inner join especial b
		on a.numero_trabajador=b.trabajador_trabajador 
		and b.clave_especial_clave_especial='CS'
		and b.validez=1
		group by a.numero_trabajador,a.genero,a.depto_depto,a.depto_depto,a.categoria_categoria,b.fecha_inicio,b.fecha_fin,
		b.hora_entrada,b.hora_salida,b.empresa,b.duracion;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		if($resul>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
				$datos[$contador_d][0]=$resul[0];//numero
				$datos[$contador_d][1]=$resul[1];//nombre
				$datos[$contador_d][2]=$resul[2];//depto	
				$datos[$contador_d][3]=$resul[3];//categoria
				$datos[$contador_d][4]=$resul[4];//genero
				$datos[$contador_d][5]=$resul[5];//fecha inicio
				$datos[$contador_d][6]=$resul[6];//fecha fin
				$datos[$contador_d][7]=$resul[7];//hora entrada
				$datos[$contador_d][8]=$resul[8];//hora salida
				$emp=$resul[9];
				$resultado = substr($emp, 1);//quitar la primera letra de la empresa
				$datos[$contador_d][9]=$resultado;//empresa
				$datos[$contador_d][10]=$resul[10];//duracion
				$contador_d++;
			}
		}
	}

    function asistenciaXrangoPulida($x)
	{
		global $con;
		global $datos;
		global $contador_d;
		//ordenar el arreglo
        // sort($datos);
        if($x==1)
        {
            asistenciaxrangos(); //Depende de que opcion se selecicone en el botón vinieron
        }
        else
        {
            if($x==2)
            {
                asistenciaXnumYfecha();
            }
            else
            {
                if($x==3)
                {
                    asistenciaXquincena();
                }
            }
        }
		$count=0;
		$count=count($datos);
        for($i=0;$i<$count;$i++)
        {
            $entrada=$datos[$i][4];
            $salida=$datos[$i][5];	
            $fecha_entrada=$datos[$i][6];
            $fecha_salida=$datos[$i][7];
            $f_ent=explode(' ',$fecha_entrada);
            $entrada=$f_ent[0].' '.$entrada;//fecha de entrada más la hora de entrada
            $f_sal=explode(' ',$fecha_salida);
            $salida=$f_sal[0].' '.$salida;//Fecha de salia más la hora salida
            if($fecha_salida>$salida)
            {
                $tiempo= minutosTranscurridos($salida, $fecha_salida);
                $datos[$i][8]=$tiempo.' minutos';//tiempo extra
            }
            if($fecha_entrada>$entrada)
            {
                $tiempo= minutosTranscurridos($entrada, $fecha_entrada);
                $datos[$i][9]=$tiempo.' minutos';//llegada tarde
            }
            if($fecha_salida<$salida)
            {
                $tiempo= minutosTranscurridos($salida, $fecha_salida);
                $datos[$i][10]=$tiempo.' minutos';//tiempo antes
            }
        }
	}
    
    //SIRVEN PARA OBTENER A QUIENES VINIERON, ES DECIR LAS ASISTENCIAS 
	function asistenciaxrangos()
	{
		global $con;
		global $datos;
		global $contador_d;
		global $f_ini;
		global $f_fin;
		$sql="Select b.trabajador_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria,d.entrada,d.salida,b.fecha_entrada,b.fecha_salida
		from trabajador a 
		inner join asistencia b on a.numero_trabajador=b.trabajador_trabajador
		inner join acceso c on a.numero_trabajador=c.trabajador_trabajador
		inner join turno d on c.turno_turno=d.idturno and
		((CAST(b.fecha_entrada AS DATE) >= '$f_ini' and  CAST(b.fecha_entrada AS DATE) <='$f_fin')
		or (CAST(b.fecha_salida AS DATE) >= '$f_ini' and  CAST(b.fecha_salida AS DATE) <='$f_fin'))
		group by a.numero_trabajador,b.fecha_entrada,b.fecha_salida;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		if($resul>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
				$datos[$contador_d][0]=$resul[0];//numero
				$datos[$contador_d][1]=$resul[1];//nombre
				$datos[$contador_d][2]=$resul[2];//depto
                $datos[$contador_d][3]=$resul[3];//categoria
                $datos[$contador_d][4]=$resul[4];//hora entrada
                $datos[$contador_d][5]=$resul[5];//hora salida
				$datos[$contador_d][6]=$resul[6];//fecha entrada
				$datos[$contador_d][7]=$resul[7];//fechasalida	
				$datos[$contador_d][8]="0";//tiempo extra
				$datos[$contador_d][9]="0";//llegada tarde
				$datos[$contador_d][10]="0";//tiempo antes
				$contador_d++;
			}
		}
	}

    function asistenciaXnumYfecha()
	{
		global $con;
		global $datos;
        global $contador_d;
        global $num;
        global $f_ini;
        global $f_fin;
		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
		$sql="Select b.trabajador_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria,d.entrada,d.salida,b.fecha_entrada,b.fecha_salida
		from trabajador a 
		inner join asistencia b on a.numero_trabajador=b.trabajador_trabajador
		inner join acceso c on a.numero_trabajador=c.trabajador_trabajador
		inner join turno d on c.turno_turno=d.idturno and
        b.trabajador_trabajador='$num' and
		((CAST(b.fecha_entrada AS DATE) >= '$f_ini' and  CAST(b.fecha_entrada AS DATE) <='$f_fin')
		or (CAST(b.fecha_salida AS DATE) >= '$f_ini' and  CAST(b.fecha_salida AS DATE) <='$f_fin'))
		group by b.fecha_entrada,b.fecha_salida;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		if($resul>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
				$datos[$contador_d][0]=$resul[0];//numero
				$datos[$contador_d][1]=$resul[1];//nombre
				$datos[$contador_d][2]=$resul[2];//depto
                $datos[$contador_d][3]=$resul[3];//categoria
                $datos[$contador_d][4]=$resul[4];//hora entrada
                $datos[$contador_d][5]=$resul[5];//hora salida
				$datos[$contador_d][6]=$resul[6];//fecha entrada
				$datos[$contador_d][7]=$resul[7];//fechasalida	
				$datos[$contador_d][8]="0";//tiempo extra
				$datos[$contador_d][9]="0";//llegada tarde
				$datos[$contador_d][10]="0";//tiempo antes
				$contador_d++;
			}
		}
	}

    function asistenciaXquincena()
	{
		global $con;
		global $datos;
        global $contador_d;
        global $quincena;
		$sql="Select b.trabajador_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria,d.entrada,d.salida,b.fecha_entrada,b.fecha_salida
		from trabajador a 
		inner join asistencia b on a.numero_trabajador=b.trabajador_trabajador
		inner join acceso c on a.numero_trabajador=c.trabajador_trabajador
		inner join turno d on c.turno_turno=d.idturno and
		b.quincena_quincena=$quincena
		group by a.numero_trabajador,b.fecha_entrada,b.fecha_salida;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		if($resul>0)
		{
			while($resul=mysqli_fetch_array($query))
			{
				$datos[$contador_d][0]=$resul[0];//numero
				$datos[$contador_d][1]=$resul[1];//nombre
				$datos[$contador_d][2]=$resul[2];//depto
                $datos[$contador_d][3]=$resul[3];//categoria
                $datos[$contador_d][4]=$resul[4];//hora entrada
                $datos[$contador_d][5]=$resul[5];//hora salida
				$datos[$contador_d][6]=$resul[6];//fecha entrada
				$datos[$contador_d][7]=$resul[7];//fechasalida	
				$datos[$contador_d][8]="0";//tiempo extra
				$datos[$contador_d][9]="0";//llegada tarde
				$datos[$contador_d][10]="0";//tiempo antes
				$contador_d++;
			}
		}
	}

	//SIRVEN PARA OBTENER A QUIENES FALTARON
	function faltasXrangoPulida($x)
	{
		global $con;
		global $datos;
		global $contador_d;
		//ordenar el arreglo
        // sort($datos);
        if($x==1)
        {
            faltasxrangos(); //Depende de que opcion se selecicone en el botón vinieron
        }
        else
        {
            if($x==2)
            {
                faltasXnumYfecha();
            }
            else
            {
                if($x==3)
                {
                    faltasXquincena();
                }
            }
        }
	}
    
	function faltasxrangos()
	{
		global $con;
		global $datos;
		global $contador_d;
		global $f_ini;
		global $f_fin;
		$sql="Select b.trabajador_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria,b.fecha
		from trabajador a 
		inner join falta b on a.numero_trabajador=b.trabajador_trabajador
		and ((CAST(b.fecha AS DATE) >= '$f_ini' and  CAST(b.fecha AS DATE) <='$f_fin'))
		and NOT EXISTS (SELECT e.falta_falta FROM justificar_falta e where b.idfalta=e.falta_falta)
		order by b.trabajador_trabajador,b.fecha;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		if($resul>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
				$datos[$contador_d][0]=$resul[0];//numero
				$datos[$contador_d][1]=$resul[1];//nombre
				$datos[$contador_d][2]=$resul[2];//depto
                $datos[$contador_d][3]=$resul[3];//categoria
				$datos[$contador_d][4]=$resul[4];//fecha 	
				$contador_d++;
			}
		}
	}

    function faltasXnumYfecha()
	{
		global $con;
		global $datos;
        global $contador_d;
        global $num;
        global $f_ini;
        global $f_fin;
		$sql="Select b.trabajador_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria,b.fecha
		from trabajador a 
		inner join falta b on a.numero_trabajador=b.trabajador_trabajador
		and b.trabajador_trabajador='$num' 
		and ((CAST(b.fecha AS DATE) >= '$f_ini' and  CAST(b.fecha AS DATE) <='$f_fin'))
		and NOT EXISTS (SELECT e.falta_falta FROM justificar_falta e where b.idfalta=e.falta_falta)
		order by b.fecha;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		if($resul>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
				$datos[$contador_d][0]=$resul[0];//numero
				$datos[$contador_d][1]=$resul[1];//nombre
				$datos[$contador_d][2]=$resul[2];//depto
                $datos[$contador_d][3]=$resul[3];//categoria
				$datos[$contador_d][4]=$resul[4];//fecha
				$contador_d++;
			}
		}
	}

    function faltasXquincena()
	{
		global $con;
		global $datos;
        global $contador_d;
        global $quincena;
		$sql="Select b.trabajador_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria,b.fecha
		from trabajador a 
		inner join falta b on a.numero_trabajador=b.trabajador_trabajador
		and b.quincena=$quincena
		and NOT EXISTS (SELECT e.falta_falta FROM justificar_falta e where b.idfalta=e.falta_falta)
		order by b.trabajador_trabajador,b.fecha,a.depto_depto,a.categoria_categoria;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		if($resul>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
				$datos[$contador_d][0]=$resul[0];//numero
				$datos[$contador_d][1]=$resul[1];//nombre
				$datos[$contador_d][2]=$resul[2];//depto
                $datos[$contador_d][3]=$resul[3];//categoria
				$datos[$contador_d][4]=$resul[4];//fecha
				$contador_d++;
			}
		}
	}

	//Quienes tienen cumpleaños u onomásticos
	function cumpleOno()
	{
		global $con; 
		global $datos;
        global $contador_d;
		global $f_ini;
		global $f_fin;
		$separa=explode('-',$f_ini);
		$anio_ini=$separa[0];
		$mes_ini=$separa[1];
		$dia_ini=$separa[2];
		$separar=explode('-',$f_fin);
		$mes_fin=$separar[1];
		$dia_fin=$separar[2];
		//Seleccionamos a los empleados que tienen cumpleaños ene un rango de fechas
		$sql="Select b.trabajador_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria,b.fecha_cumple
		from trabajador a 
		inner join cumple_ono b on a.numero_trabajador=b.trabajador_trabajador
		and  ((MONTH(fecha_cumple) = $mes_ini AND DAY(fecha_cumple) >= $dia_ini) AND (MONTH(fecha_cumple) = $mes_fin AND DAY(fecha_cumple) <= $dia_fin))
		and validez=0";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		if($resul>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
				$datos[$contador_d][0]=$resul[0];//numero
				$datos[$contador_d][1]=$resul[1];//nombre
				$datos[$contador_d][2]=$resul[2];//depto
				$datos[$contador_d][3]=$resul[3];//categoria
				$fecha_cum=$resul[4];//fecha cumpleaños
				$separa=explode('-',$fecha_cum);
				$datos[$contador_d][4]=$anio_ini.'-'.$separa[1].'-'.$separa[2];//fecha
				$contador_d++;
			}
		}
		//Seleccionamos a los empleados que tienen onomástico en un rango de fechas
		$sql2="Select b.trabajador_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria,b.fecha_ono
		from trabajador a 
		inner join cumple_ono b on a.numero_trabajador=b.trabajador_trabajador
		and  ((MONTH(fecha_ono) = $mes_ini AND DAY(fecha_ono) >= $dia_ini) AND (MONTH(fecha_ono) = $mes_fin AND DAY(fecha_ono) <= $dia_fin))
		and validez=1";  
		$query2= mysqli_query($con, $sql2) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul2=mysqli_num_rows($query2);
		if($resul2>0)
		{  
			while($resul2=mysqli_fetch_array($query2))
			{			
				$datos[$contador_d][0]=$resul2[0];//numero
				$datos[$contador_d][1]=$resul2[1];//nombre
				$datos[$contador_d][2]=$resul2[2];//depto
				$datos[$contador_d][3]=$resul2[3];//categoria
				$fecha_ono=$resul2[4];//fecha
				$separa=explode('-',$fecha_ono);
				$datos[$contador_d][4]=$anio_ini.'-'.$separa[1].'-'.$separa[2];//fecha
				$contador_d++;
			}
		}
	}

	//Quienes tienen guardias
	function buscarguardias($x)
	{
		if($x==1)
		{
			guardiasXrangos();
		}
		else
		{
			if($x==2)
			{
				guardiasXnumero();
			}
			else
			{
				if($x==3)
				{
					guardiasXquincena();
				}
			}
		}
	}
	
	function guardiasXrangos()
	{
		global $con;
		global $datos;
        global $contador_d;
		global $f_ini;
		global $f_fin;
		$sql="Select b.trabajador_solicitante,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria,b.hora_entrada,b.hora_salida,b.fecha_guardia,b.trabajador_suplente
		from trabajador a 
		inner join guardias b on a.numero_trabajador=b.trabajador_solicitante
		and b.fecha_guardia  >= '$f_ini' and  b.fecha_guardia <='$f_fin'
		order by b.fecha_guardia;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		if($resul>0)
		{
			while($resul=mysqli_fetch_array($query))
			{
				$datos[$contador_d][0]=$resul[0];//trabajador solicitante
				$datos[$contador_d][1]=$resul[1];//nombre
				$datos[$contador_d][2]=$resul[2];//depto
                $datos[$contador_d][3]=$resul[3];//categoria
				$datos[$contador_d][4]=$resul[4];//hora entrada
				$datos[$contador_d][5]=$resul[5];//hora salida
				$datos[$contador_d][6]=$resul[6];//fecha guardia
				$datos[$contador_d][7]=$resul[7];//trabajador suplente
				$nom_suplente=infoSuplente($resul[7]);
				$datos[$contador_d][8]=$nom_suplente;//falta el nombre completo del suplente
				$contador_d++;
			}
		}
	}

	function guardiasXnumero()
	{
		global $con;
		global $datos;
        global $contador_d;
		global $quincena;
		global $f_ini;
		global $f_fin;
		global $num;
		//Seleccionamos a los empleados que tienen guardias en un rango de fechas
		$sql="Select b.trabajador_solicitante,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria,b.hora_entrada,b.hora_salida,b.fecha_guardia,b.trabajador_suplente
		from trabajador a 
		inner join guardias b on a.numero_trabajador=b.trabajador_solicitante
		and b.trabajador_solicitante='$num' 
		and b.fecha_guardia  >= '$f_ini' and  b.fecha_guardia <='$f_fin'
		order by b.fecha_guardia;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		if($resul>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{			
				$datos[$contador_d][0]=$resul[0];//trabajador solicitante
				$datos[$contador_d][1]=$resul[1];//nombre
				$datos[$contador_d][2]=$resul[2];//depto
                $datos[$contador_d][3]=$resul[3];//categoria
				$datos[$contador_d][4]=$resul[4];//hora entrada
				$datos[$contador_d][5]=$resul[5];//hora salida
				$datos[$contador_d][6]=$resul[6];//fecha guardia
				$datos[$contador_d][7]=$resul[7];//trabajador suplente
				$nom_suplente=infoSuplente($resul[7]);
				$datos[$contador_d][8]=$nom_suplente;//falta el nombre completo del suplente
				$contador_d++;
			}
		}//fin else
	}

	function guardiasXquincena()
	{
		global $con;
		global $datos;
        global $contador_d;
		global $quincena;
		//Seleccionamos a los empleados que tienen guardias por quincena
		$sql="Select b.trabajador_solicitante,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria,b.hora_entrada,b.hora_salida,b.fecha_guardia,b.trabajador_suplente
		from trabajador a 
		inner join guardias b on a.numero_trabajador=b.trabajador_solicitante
		and quincena=$quincena
		order by b.fecha_guardia;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		if($resul>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
				$datos[$contador_d][0]=$resul[0];//trabajador solicitante
				$datos[$contador_d][1]=$resul[1];//nombre
				$datos[$contador_d][2]=$resul[2];//depto
                $datos[$contador_d][3]=$resul[3];//categoria
				$datos[$contador_d][4]=$resul[4];//hora entrada
				$datos[$contador_d][5]=$resul[5];//hora salida
				$datos[$contador_d][6]=$resul[6];//fecha guardia
				$datos[$contador_d][7]=$resul[7];//trabajador suplente
				$num_suplente=$resul[7];
				$nom_suplente=infoSuplente($num_suplente);
				$datos[$contador_d][8]=$nom_suplente;// nombre completo del suplente
				$contador_d++;
			}
		}//fin else
	}

	function infoSuplente($num_suplente)
	{
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		$sql2="select CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n from
		trabajador a where a.numero_trabajador='$num_suplente';";  
		$query2= mysqli_query($con, $sql2) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul2=mysqli_num_rows($query2);
		if($resul2==1)
		{  
			$resul2=mysqli_fetch_array($query2);
			return $resul2[0];
		}//fin else
	}

	//Quienes tienen sextas
	function todosSexta()
	{
		global $con; 
		global $datos;
        global $contador_d;
		$sql="Select a.numero_trabajador, CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto, a.categoria_categoria,
		c.lunes,c.martes,c.miercoles,c.jueves,c.viernes,c.sabado,c.domingo,
		b.lunes,b.martes,b.miercoles,b.jueves,c.viernes,b.sabado,b.domingo
		from trabajador a
		inner join sexta b on a.numero_trabajador=b.trabajador_trabajador
		inner join acceso c on a.numero_trabajador=c.trabajador_trabajador
		order by a.numero_trabajador, categoria_categoria,depto_depto;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		if($resul>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
				$datos[$contador_d][0]=$resul[0];//numero
				$datos[$contador_d][1]=$resul[1];//nombre
				$datos[$contador_d][2]=$resul[2];//depto
                $datos[$contador_d][3]=$resul[3];//categoria
				$datos[$contador_d][4]=$resul[4];//lunes acceso
				$datos[$contador_d][5]=$resul[5];//martes acceso
				$datos[$contador_d][6]=$resul[6];//miercoles acceso
				$datos[$contador_d][7]=$resul[7];//jueves acceso
                $datos[$contador_d][8]=$resul[8];//viernes acceso
				$datos[$contador_d][9]=$resul[9];//sabado acceso
				$datos[$contador_d][10]=$resul[10];//domingo acceso
				$datos[$contador_d][11]=$resul[11];//lunes sexta
				$datos[$contador_d][12]=$resul[12];//martes sexta
                $datos[$contador_d][13]=$resul[13];//miercoles sexta
				$datos[$contador_d][14]=$resul[14];//jueves sexta
				$datos[$contador_d][15]=$resul[15];//viernes sexta
				$datos[$contador_d][16]=$resul[16];//sabado sexta
				$datos[$contador_d][17]=$resul[17];////domingo sexta
				$contador_d++;
			}
		}
	}

	function vienenSexta()
	{
		global $con;
		global $datos;
		global $contador_d;
		global $dia;
		$sql="Select a.numero_trabajador, CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n, a.categoria_categoria,a.depto_depto 
		from trabajador a
		inner join sexta b 
		where a.numero_trabajador=b.trabajador_trabajador
		and b.$dia=1 and b.validez=1 and b.t_dias<=2 
		order by a.numero_trabajador;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		if($resul>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
				$datos[$contador_d][0]=$resul[0];//numero
				$datos[$contador_d][1]=$resul[1];//nombre
				$datos[$contador_d][2]=$resul[2];//numero
				$datos[$contador_d][3]=$resul[3];//nombre
				$contador_d;
				
			}
		}
	}

	//Quienes tienen licencias
	function licenciasqueNoempiezan()
	{
		global $con;
		global $datos;
        global $contador_d;
		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
		$sql="Select b.trabajador_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria, b.fecha_inicio,b.fecha_fin,b.hora_entrada,b.hora_salida,b.clave_especial_clave_especial,b.empresa,b.duracion
		from trabajador a 
		inner join especial b on a.numero_trabajador=b.trabajador_trabajador
		and  now()<b.fecha_inicio
		and (b.clave_especial_clave_especial='29'
		or b.clave_especial_clave_especial='40'
		or b.clave_especial_clave_especial='41'
		or b.clave_especial_clave_especial='47'
		or b.clave_especial_clave_especial='48'
		or b.clave_especial_clave_especial='49'
		or b.clave_especial_clave_especial='51'
		or b.clave_especial_clave_especial='53'
		or b.clave_especial_clave_especial='54'
		or b.clave_especial_clave_especial='55'
		or b.clave_especial_clave_especial='62'
		or b.clave_especial_clave_especial='92'
		or b.clave_especial_clave_especial='93'
		or b.clave_especial_clave_especial='LSG'
		or b.clave_especial_clave_especial='LSGSS')
		order by b.fecha_inicio,b.trabajador_trabajador,b.clave_especial_clave_especial;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		if($resul>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
				$datos[$contador_d][0]=$resul[0];//numero
				$datos[$contador_d][1]=$resul[1];//nombre
				$datos[$contador_d][2]=$resul[2];//depto
                $datos[$contador_d][3]=$resul[3];//categoria
				$datos[$contador_d][4]=$resul[4];//fecha inicio
				$datos[$contador_d][5]=$resul[5];//fecha fin
				$datos[$contador_d][6]=$resul[6];//hora entrada
				$datos[$contador_d][7]=$resul[7];//hora salida
                $datos[$contador_d][8]=$resul[8];//clave
				$emp=$resul[9];
				$resultado = substr($emp, 1);//quitar la primera letra de la empresa
				$datos[$contador_d][9]=$resultado;//empresa
				$datos[$contador_d][10]=$resul[10];//duracion
				$contador_d++;
			}
		}
	}

	function licenciasActivas()
	{
		global $con;
		global $datos;
        global $contador_d;
        global $quincena;
		$sql="Select b.trabajador_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria, b.fecha_inicio,b.fecha_fin,b.hora_entrada,b.hora_salida,b.clave_especial_clave_especial,b.empresa,b.duracion
		from trabajador a 
		inner join especial b on a.numero_trabajador=b.trabajador_trabajador
		and  b.validez=1
		and (b.clave_especial_clave_especial='29'
		or b.clave_especial_clave_especial='40'
		or b.clave_especial_clave_especial='41'
		or b.clave_especial_clave_especial='47'
		or b.clave_especial_clave_especial='48'
		or b.clave_especial_clave_especial='49'
		or b.clave_especial_clave_especial='51'
		or b.clave_especial_clave_especial='53'
		or b.clave_especial_clave_especial='54'
		or b.clave_especial_clave_especial='55'
		or b.clave_especial_clave_especial='62'
		or b.clave_especial_clave_especial='92'
		or b.clave_especial_clave_especial='93'
		or b.clave_especial_clave_especial='LSG'
		or b.clave_especial_clave_especial='LSGSS')
		order by b.fecha_inicio, b.trabajador_trabajador;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		if($resul>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
			
				$datos[$contador_d][0]=$resul[0];//numero
				$datos[$contador_d][1]=$resul[1];//nombre
				$datos[$contador_d][2]=$resul[2];//depto
                $datos[$contador_d][3]=$resul[3];//categoria
				$datos[$contador_d][4]=$resul[4];//fecha inicio
				$datos[$contador_d][5]=$resul[5];//fecha fin
				$datos[$contador_d][6]=$resul[6];//hora entrada
				$datos[$contador_d][7]=$resul[7];//hora salida
                $datos[$contador_d][8]=$resul[8];//clave
				$datos[$contador_d][9]=$resul[9];//duracion
				$contador_d++;
			}
		}
	}

	function licenciasXvencer()
	{
		global $con;
		global $datos;
		global $contador_d;
		$total=0;
		licenciasActivas();
		$total=count($datos);
		for($i=0;$i<$total;$i++)
		{
			$fec=$datos[$i][5];
			$duracion=calcularDuracionEntreDosFechas(1,$fec,"");
			//Si la duracion es mayor a 7 dias, aun no está proxima a vencer
			if($duracion>7)
			{
				//Se debe eliminar de la lista al empleado, ya que no se presentará
				unset($datos[$i]);
				$contador_d--; //Disminuirá el contador
				$datos=array_values($datos);//El arreglo deberá reordenarse
			}
		}
	}

	function licenciasVencidas()
	{
		global $con;
		global $datos;
        global $contador_d;
 		$sql="Select b.trabajador_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria, b.fecha_inicio,b.fecha_fin,b.hora_entrada,b.hora_salida,b.clave_especial_clave_especial,b.empresa,b.duracion,validez
		from trabajador a 
		inner join especial b on a.numero_trabajador=b.trabajador_trabajador
		and  b.fecha_fin <now() and validez=0
		and (b.clave_especial_clave_especial='29'
		or b.clave_especial_clave_especial='40'
		or b.clave_especial_clave_especial='41'
		or b.clave_especial_clave_especial='47'
		or b.clave_especial_clave_especial='48'
		or b.clave_especial_clave_especial='49'
		or b.clave_especial_clave_especial='51'
		or b.clave_especial_clave_especial='53'
		or b.clave_especial_clave_especial='54'
		or b.clave_especial_clave_especial='55'
		or b.clave_especial_clave_especial='62'
		or b.clave_especial_clave_especial='92'
		or b.clave_especial_clave_especial='93'
		or b.clave_especial_clave_especial='LSG'
		or b.clave_especial_clave_especial='LSGSS')
		order by b.clave_especial_clave_especial,b.fecha_inicio,b.fecha_fin,b.trabajador_trabajador;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		if($resul>0)
		{  
			while($resul=mysqli_fetch_array($query))
			{
				$datos[$contador_d][0]=$resul[0];//numero
				$datos[$contador_d][1]=$resul[1];//nombre
				$datos[$contador_d][2]=$resul[2];//depto
                $datos[$contador_d][3]=$resul[3];//categoria
				$datos[$contador_d][4]=$resul[4];//fecha inicio
				$datos[$contador_d][5]=$resul[5];//fecha fin
				$datos[$contador_d][6]=$resul[6];//hora entrada
				$datos[$contador_d][7]=$resul[7];//hora salida
                $datos[$contador_d][8]=$resul[8];//clave
				$datos[$contador_d][9]=$resul[9];//duracion
				$contador_d++;
			}
		}
	}

	function pase_salida($x)
	{
		global $con;
		global $datos;
		global $contador_d;
		$dia=date("Y-m-d");//guardar la fecha actual
		if($x==1)
		{
			//Seleccionamos a los empleados que tienen pase de salida hoy
			$sql="Select b.trabajador_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria, b.fecha_uso
			from trabajador a 
			inner join pase_salida b on a.numero_trabajador=b.trabajador_trabajador
			and  b.fecha_uso='$dia'
			order by b.fecha_uso, b.trabajador_trabajador;";  
			$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
			$resul=mysqli_num_rows($query);
			if($resul>0)
			{  
				while($resul=mysqli_fetch_array($query))
				{
				
					$datos[$contador_d][0]=$resul[0];//numero
					$datos[$contador_d][1]=$resul[1];//nombre
					$datos[$contador_d][2]=$resul[2];//depto
					$datos[$contador_d][3]=$resul[3];//categoria
					$datos[$contador_d][4]=$resul[4];//fecha
					$contador_d;	
				}
			}
		}
		if($x==2)
		{
			//Seleccionamos los pases de salida vencidos
			$sql="Select b.trabajador_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria, b.fecha_uso
			from trabajador a 
			inner join pase_salida b on a.numero_trabajador=b.trabajador_trabajador
			and  b.fecha_uso<'$dia'
			order by b.fecha_uso, b.trabajador_trabajador;";  
			$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
			$resul=mysqli_num_rows($query);
			if($resul>0)
			{  
				while($resul=mysqli_fetch_array($query))
				{
					$datos[$contador_d][0]=$resul[0];//numero
					$datos[$contador_d][1]=$resul[1];//nombre
					$datos[$contador_d][2]=$resul[2];//depto
					$datos[$contador_d][3]=$resul[3];//categoria
					$datos[$contador_d][4]=$resul[4];//fecha
					$contador_d;
				}
			}
			
		}
	}

    /*Obtiene los minutos transcurridos entre dos fechas*/
    function minutosTranscurridos($fecha_i,$fecha_f)
    {
        $minutos = (strtotime($fecha_i)-strtotime($fecha_f))/60;
        $minutos = abs($minutos); $minutos = floor($minutos);
        return $minutos;
	}
	
	function calcularDuracionEntreDosFechas($tipo, $fecha_inicio, $fecha_final)
    {
        /*Tipo=0 Se compararán las dos fechas elegidas
          Tipo=1 Se comparará la $fecha_inicio con el día de hoy
		*/
        if($tipo==0)
        {
            $date1= new DateTime($fecha_inicio);
            $date2= new DateTime($fecha_final);
            $interval = $date1->diff($date2);
            $totDias=$interval->format('%a');
            return $totDias+1;
        }
        else
        {
            if($tipo==1)
            {
                $today=date("Y-m-d"); 
                $date1= new DateTime($today);
                $date2= new DateTime($fecha_inicio);
                $interval = $date1->diff($date2);
                $totDias=$interval->format('%a');
                return $totDias;
            }
            else
            {
                echo "Parámetro *tipo* no válido en función calcularDuracionEntreDosFechas";
            }
        }
	}//fin de calcularDuracionEntreDosFechas	
?>