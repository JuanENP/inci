<?php
	// -------------------Funciones necesarias para el reporte de incidencias de comisionados foráneos------------	
    function incidencias_f()
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
		where b.quincena_quincena=$quincena and a.tipo_tipo=4
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

	function cumpleOno_clave14cica_f()
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
		inner join cumple_ono b on numero_trabajador=trabajador_trabajador and a.tipo_tipo=4
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
		inner join cumple_ono b on numero_trabajador=trabajador_trabajador and a.tipo_tipo=4
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
	
	function faltas_f()
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
		AND quincena=$quincena AND a.tipo_tipo=4
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

	function justificaciones_f()
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
		where (d.clave_justificacion_clave_justificacion=08 or d.clave_justificacion_clave_justificacion=09) and a.tipo_tipo=4;";  
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

	function especiales_f()
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
		where a.tipo_tipo=4 and (clave_especial_clave_especial='12' 
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
    	
	function vacaciones_f()
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
		$sql="SELECT a.numero_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,c.periodo,c.dia
		FROM trabajador a 
		INNER JOIN vacaciones b on a.numero_trabajador=b.trabajador_trabajador and a.tipo_tipo=4
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
		INNER JOIN vacaciones_radio b on a.numero_trabajador=b.trabajador_trabajador and a.tipo_tipo=4
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
		INNER jOIN vacaciones_extraordinarias b on a.numero_trabajador=b.trabajador_trabajador and a.tipo_tipo=4
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

	function suspensiones_y_bajas_f()
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
		on a.numero_trabajador = b.trabajador_trabajador and (fecha>='$f_ini' AND fecha<='$f_fin') and a.tipo_tipo=4;";  
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

	function sin_der_estimulo_desempeño_cica78_f()
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
		on a.numero_trabajador = b.trabajador_trabajador and a.tipo_tipo=4 and (fecha>='$f_ini' AND fecha<='$f_fin')
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
	
	function pulir_f()
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
	//------------------------------------------------------------------------------------------
    //------------------Reporte único de incidencias por trabajador y quincena actual-----------
    function incidencias_trabajador()
    {
		global $con;
        global $quincena;
        global $numero;
		global $fila;
		global $ultimo_r;
		global $reporte;
		$anioActual=date('Y');
		//seleccionamos a los empleados que tinen incidencias sin justificar y que sean de base
		$sql="SELECT a.numero_trabajador, CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,
		c.clave_incidencia_clave_incidencia, b.fecha_entrada,b.fecha_salida FROM trabajador a
		inner join asistencia b on a.numero_trabajador=b.trabajador_trabajador and (b.fecha_entrada like '%$anioActual%' or b.fecha_salida like '%$anioActual%')
		inner join incidencia c on b.id=c.asistencia_asistencia
		where b.quincena_quincena=$quincena and a.tipo_tipo=4 and a.numero_trabajador='$numero'
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

	function cumpleOno_clave14cica_trabajador()
    {
        global $con;
		global $f_ini;
        global $f_fin;
        global $numero;
		global $fila;
		global $ultimo_r;
		global $reporte;
		//Separar las fechas de inicio y fin de la quincena actual
		$separaF_ini=explode('-',$f_ini);
		$separaF_fin=explode('-',$f_fin);
		//seleccionamos a los empleados que tomaron su fecha de cumpleaños en esta quincena
		$sql="select a.numero_trabajador, CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n, b.fecha_cumple from trabajador a 
		inner join cumple_ono b on numero_trabajador=trabajador_trabajador and a.tipo_tipo=4 and a.numero_trabajador='$numero'
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
		inner join cumple_ono b on numero_trabajador=trabajador_trabajador and a.tipo_tipo=4 and a.numero_trabajador='$numero'
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
	
	function faltas_trabajador()
	{
        global $con;
        global $numero;
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
		AND quincena=$quincena AND a.tipo_tipo=4 and a.numero_trabajador='$numero'
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

	function justificaciones_trabajador()
	{
        global $con;
        global $numero;
		global $quincena;
		global $fila;
		global $ultimo_r;
		global $reporte;
		$anioActual=date('Y');
		$sql="select a.numero_trabajador, CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,
		d.clave_justificacion_clave_justificacion, b.fecha_entrada,b.fecha_salida 
		from trabajador a
		inner join asistencia b on a.numero_trabajador = b.trabajador_trabajador and a.numero_trabajador='$numero' and (b.fecha_entrada like '%$anioActual%' or b.fecha_salida like '%$anioActual%') and quincena_quincena=$quincena
		inner join incidencia c on b.id=c.asistencia_asistencia
		inner join justificacion d on c.idincidencia=d.incidencia_incidencia  
		where (d.clave_justificacion_clave_justificacion=08 or d.clave_justificacion_clave_justificacion=09) and a.tipo_tipo=4;";  
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

	function especiales_trabajador()
	{
		global $con;
		global $f_ini;
        global $f_fin;
        global $numero;
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
        where a.tipo_tipo=4 and a.numero_trabajador='$numero'
        and (clave_especial_clave_especial='12' 
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
    	
	function vacaciones_trabajador()
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
        global $numero;
		global $ultimo_r;
		global $reporte;
        $anioActual=date('Y');
        //Separar las fechas de inicio y fin de la quincena actual
        $separaF_ini=explode('-',$f_ini);
        $f_ini=$anioActual.'-'.$separaF_ini[1].'-'.$separaF_ini[2];
        $separaF_fin=explode('-',$f_fin);
		$f_fin=$anioActual.'-'.$separaF_fin[1].'-'.$separaF_fin[2];
		//Vacaciones normales clave 60 cica
		$sql="SELECT a.numero_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,c.periodo,c.dia
		FROM trabajador a 
		INNER JOIN vacaciones b on a.numero_trabajador=b.trabajador_trabajador and a.tipo_tipo=4 and a.numero_trabajador='$numero'
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
		INNER JOIN vacaciones_radio b on a.numero_trabajador=b.trabajador_trabajador and a.tipo_tipo=4 and a.numero_trabajador='$numero'
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
		INNER jOIN vacaciones_extraordinarias b on a.numero_trabajador=b.trabajador_trabajador and a.tipo_tipo=4 and a.numero_trabajador='$numero'
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

	function suspensiones_y_bajas_trabajador()
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
        global $numero;
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
		on a.numero_trabajador = b.trabajador_trabajador and (fecha>='$f_ini' AND fecha<='$f_fin') and a.tipo_tipo=4 and a.numero_trabajador='$numero';";  
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

	function sin_der_estimulo_desempeño_cica78_trabajador()
	{
        /*
            Clave 75 Estímulo al trabajador del mes.
            Clave 78, sin derecho a estímulo de desempeño.
		*/
		global $con;
		global $f_ini;
        global $f_fin;
        global $numero;
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
		on a.numero_trabajador = b.trabajador_trabajador and a.tipo_tipo=4 and a.numero_trabajador='$numero' and (fecha>='$f_ini' AND fecha<='$f_fin')
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
	
	function pulir_trabajador()
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
?>