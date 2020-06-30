<?php
session_start();
ob_start();
date_default_timezone_set('America/Mexico_City'); 
//set_time_limit(600);//Indica que son 600 segundos, es decir 10 minutos máximo para ejecutar todo el script
	if (($_SESSION["name"]) && ($_SESSION["con"]))
	{
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php");
		$f_hoy=date("Y-m-d");//guardar la fecha actual
	}
	else
	{
		header("Location: ../index.php");
		die();
	}
	//NUMERO DE TRABAJADOR
	$numero=$nombre;
	// Cargamos la librería dompdf que hemos instalado en la carpeta dompdf
	require_once("../pdf/dompdf/autoload.inc.php");
	use Dompdf\Dompdf;

	if(empty($_POST['formato']))
	{
		echo "<script language='javascript'> alert('Seleccione un opción'); history.back();</script>";
		exit();
	}
	$operacion=$_POST['formato'];

	//OPCION PASE DE SALIDA, REVISION DE INPUTS
	if($operacion=="4")
	{
		if(empty($_POST['motivo']) || empty($_POST['fecha']))
		{
			echo "<script language='javascript'> alert('No deje campos vacíos.'); history.back();</script>";
			exit();
		}
		else
		{
			$motivo =$_POST['motivo'];
			$fecha=$_POST['fecha'];
			$revisar=RevisarFechas($fecha,2);//Sirve para revisar si la fecha es menor o igual a hoy
			$separa=explode('-',$fecha);
			$anio_solicita=$separa[0];
			if(strlen($anio_solicita)==4)
			{
				$anio_actual=date("Y");
				$anio_solicita = strtotime($anio_solicita);
				$anio_actual= strtotime($anio_actual);
				if($anio_solicita==$anio_actual)
				{
					$num = date("d", strtotime($fecha));
					$mes = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
					$mes = $mes[(date('m', strtotime($fecha))*1)-1];
					$anio= date("Y", strtotime($fecha));
					$fecha=$num.' de '.$mes .' del '.$anio;	
					//----------------------------------------------------//
					$_SESSION['fecha'] = $fecha;
					$_SESSION['motivo']   = $motivo;
					$_SESSION['fecha']   = $fecha;

				}
				else
				{
					echo "<script language='javascript'> alert('Debe elegir el año actual.'); history.back();</script>";
					exit();
				}	
			}
			else
			{
				echo "<script language='javascript'> alert('El año debe tener cuatro dígitos.'); history.back();</script>";
				exit();	
			}
		}
	}
	else
	{
		if(empty($_POST['f-justifica']))
		{
			echo "<script language='javascript'> alert('Seleccione una fecha.'); history.back();</script>";
			exit();
		}
		else
		{
			$fecha=$_POST['f-justifica'];
			$revisar=RevisarFechas($fecha,1);//Sirve para revisar si la fecha es menor o igual a hoy
			$separa=explode('-',$fecha);
			$anio_solicita=$separa[0];
			if(strlen($anio_solicita)==4)
			{
				$anio_actual=date("Y");
				$anio_solicita = strtotime($anio_solicita);
				$anio_actual= strtotime($anio_actual);
				if($anio_solicita==$anio_actual)
				{  
					$num = date("d", strtotime($fecha));
					$mes = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
					$mes = $mes[(date('m', strtotime($fecha))*1)-1];
					$anio= date("Y", strtotime($fecha));
					$fecha=$num.' de '.$mes .' del '.$anio;	
					//----------------------------------------------------//
					$_SESSION['fecha'] = $fecha;

				}
				else
				{
					echo "<script language='javascript'> alert('Debe elegir el año actual.'); history.back();</script>";
					exit();
				}	
			}
			else
			{
				echo "<script language='javascript'> alert('El año debe tener cuatro dígitos.'); history.back();</script>";
				exit();	
			}
		}

	}

	//----Obtener la fecha de hoy para el documento------//
	$num = date("d", strtotime($f_hoy));
	$mes = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
	$mes = $mes[(date('m', strtotime($f_hoy))*1)-1];
	$anio= date("Y", strtotime($f_hoy));
	$dia_mes=$num.' de '.$mes .' del '.$anio;	
	//----------------------------------------------------//
	// OBTENER LA INFORMACION DEL TRABAJADOR
	$sql="select a.numero_trabajador,a.nombre,a.apellido_paterno,a.apellido_materno,a.categoria_categoria,b.nombre,d.entrada,d.salida from trabajador a
	inner join categoria b on b.idcategoria = a.categoria_categoria and a.numero_trabajador = '$numero'
	inner join acceso c on a.numero_trabajador= c.trabajador_trabajador
	inner join turno d on c.turno_turno = d.idturno "; 
	$query= mysqli_query($con, $sql);
	if(!$query)
	{
		die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
	}
	else
	{
		$resul=mysqli_fetch_array($query);
	
		$num=$resul[0];
		$nom =$resul[1];
		$a_pat =$resul[2];
		$a_mat =$resul[3];
		$cat =$resul[4];
		$des =$resul[5];//descripcion de la categoria
		$ent=$resul[6];
		$sal=$resul[7];
		
		
		//   VARIABLES QUE DESEO ENVIAR A LOS FORMATOS
		//Enviar informacion del hospital a los reportes//
		$info=informacionHospital();
		if($info!=null)
		{
			$_SESSION['municipio'] = $info[0];
			$_SESSION['estado'] = $info[1];
			$_SESSION['abrevia_estado'] = $info[2];
		}
	
		$_SESSION['dia_mes'] = $dia_mes  ;
		$_SESSION['num']   = $num  ;
		$_SESSION['nom']   = $nom  ;
		$_SESSION['a_pat'] = $a_pat;
		$_SESSION['a_mat'] = $a_mat;
		$_SESSION['cat']   = $cat  ;
		$_SESSION['des']   = $des  ;
		$_SESSION['ent']   = $ent  ;
		$_SESSION['sal']   = $sal  ;
		$_SESSION['operacion']   = $operacion;
		//CALCULAR DE QUE HORA A QUE HORA PUDEDE SALIR UN EMPLEADO SI PIDE UN PASE DE SALIDA(2 HORAS ANTES DE SU SALIDA)
		$posibleHoraSalida=date("H:i:s", strtotime( $sal." - 2 hour"));
		$_SESSION['posibleHoraSalida'] = $posibleHoraSalida;
	
	
	}

	//omision de entrada
	if($operacion=="1")
	{  
		/*OBTENER LA QUINCENA ACTUAL EN LA QUE ESTAMOS*/
		$sql5="SELECT idquincena from quincena where validez=1";
		$query5=mysqli_query($con, $sql5) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul5=mysqli_fetch_array($query5);
		$quincena=$resul5[0];
		/*FIN DE OBTENER QUINCENA ACTUAL*/

		//contamos cuántas 09 (retardos justificados) posee el empleado en la tabla justificaciones
		$sql6="SELECT count(d.clave_justificacion_clave_justificacion ='09')
		FROM trabajador a
		INNER JOIN asistencia b on a.numero_trabajador = b.trabajador_trabajador and a.numero_trabajador = '$num' 
		INNER JOIN incidencia c on  b.id = c.asistencia_asistencia and b.quincena_quincena = $quincena
		INNER JOIN justificacion d on c.idincidencia = d.incidencia_incidencia and d.clave_justificacion_clave_justificacion= '09'
		INNER JOIN acceso e on a.numero_trabajador=e.trabajador_trabajador
		INNER JOIN turno f on e.turno_turno = f.idturno";
		$query6= mysqli_query($con, $sql6) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul6=mysqli_fetch_array($query6);
		$totalRetardos=$resul6[0];

		//contamos cuántas 08 (omisiones justificadas) posee el empleado en la tabla justificaciones de incidencia
		$sql9="SELECT count(d.clave_justificacion_clave_justificacion = '08') FROM trabajador a
		INNER JOIN asistencia b on a.numero_trabajador = b.trabajador_trabajador and a.numero_trabajador = $num 
		INNER JOIN incidencia c on  b.id = c.asistencia_asistencia and b.quincena_quincena = $quincena
		INNER JOIN justificacion d on c.idincidencia = d.incidencia_incidencia and d.clave_justificacion_clave_justificacion= '08'
		INNER JOIN acceso e on a.numero_trabajador=e.trabajador_trabajador
		INNER JOIN turno f on e.turno_turno = f.idturno";  
		$query9= mysqli_query($con, $sql9) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul9=mysqli_fetch_array($query9);
		$totalOmisionesIncidencia=$resul9[0];

		$total =  $totalRetardos +$totalOmisionesIncidencia;
		
		
		//si totalOm es menor que 2 significa que aún puede justificar su omisión
		if($total>=2)
		{  
			echo "<script type='text/javascript'> alert('Ya excedió el límite de omisiones o retardos'); location.href='../ht/repositorio.php';</script>";
		} 
		else
		{
			//Ubicacion del formato
			include("../pdf/omision.php");

			// Instanciamos un objeto de la clase DOMPDF.
			$pdf = new DOMPDF();
			
			// Definimos el tamaño y orientación del papel que queremos.
			$pdf->set_paper("letter", "portrait");
			//$pdf->set_paper(array(0,0,104,250));
			
			// Cargamos el contenido HTML.
			$pdf->load_html(ob_get_clean());
			
			// Renderizamos el documento PDF.
			$pdf->render();
			
			// Enviamos el fichero PDF al navegador.
			$pdf->stream('omision_entrada.pdf');
		}

	} //fin if-1

	//omision
	if($operacion=="2")
	{   
		/*OBTENER LA QUINCENA ACTUAL EN LA QUE ESTAMOS*/
		$sql5="SELECT idquincena from quincena where validez=1";
		$query5=mysqli_query($con, $sql5) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul5=mysqli_fetch_array($query5);
		$quincena=$resul5[0];
		/*FIN DE OBTENER QUINCENA ACTUAL*/

		//contamos cuántas 09 (retardos justificados) posee el empleado en la tabla justificaciones
		$sql6="SELECT count(d.clave_justificacion_clave_justificacion)
		FROM trabajador a
		INNER JOIN asistencia b on a.numero_trabajador = b.trabajador_trabajador and a.numero_trabajador = '$num' 
		INNER JOIN incidencia c on  b.id = c.asistencia_asistencia and b.quincena_quincena = $quincena
		INNER JOIN justificacion d on c.idincidencia = d.incidencia_incidencia and d.clave_justificacion_clave_justificacion= 09
		INNER JOIN acceso e on a.numero_trabajador=e.trabajador_trabajador
		INNER JOIN turno f on e.turno_turno = f.idturno";
		$query6= mysqli_query($con, $sql6) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul6=mysqli_fetch_array($query6);
		$totalRetardos=$resul6[0];

		//contamos cuántas 08 (omisiones justificadas) posee el empleado en la tabla justificaciones de incidencia
		$sql9="SELECT count(d.clave_justificacion_clave_justificacion) FROM trabajador a
		INNER JOIN asistencia b on a.numero_trabajador = b.trabajador_trabajador and a.numero_trabajador = $num 
		INNER JOIN incidencia c on  b.id = c.asistencia_asistencia and b.quincena_quincena = $quincena
		INNER JOIN justificacion d on c.idincidencia = d.incidencia_incidencia and d.clave_justificacion_clave_justificacion= 08
		INNER JOIN acceso e on a.numero_trabajador=e.trabajador_trabajador
		INNER JOIN turno f on e.turno_turno = f.idturno";  
		$query9= mysqli_query($con, $sql9) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul9=mysqli_fetch_array($query9);
		$totalOmisionesIncidencia=$resul9[0];

		$total =  $totalRetardos +$totalOmisionesIncidencia;
		//si totalOm es menor que 2 significa que aún puede justificar su omisión
		if($total>=2)
		{  
			echo "<script type='text/javascript'> alert('Ya excedió el límite de omisiones o retardos'); location.href='../ht/repositorio.php';</script>";
		}
		else
		{
			//Ubicacion del formato
			include("../pdf/omision.php");

			// Instanciamos un objeto de la clase DOMPDF.
			$pdf = new DOMPDF();
			
			// Definimos el tamaño y orientación del papel que queremos.
			$pdf->set_paper("letter", "portrait");
			//$pdf->set_paper(array(0,0,104,250));
			
			// Cargamos el contenido HTML.
			$pdf->load_html(ob_get_clean());
			
			// Renderizamos el documento PDF.
			$pdf->render();
			
			// Enviamos el fichero PDF al navegador.
			$pdf->stream('omision_salida.pdf');
		}

	} //fin if-2

	//retardo
	if($operacion=="3")
	{ 	    /*OBTENER LA QUINCENA ACTUAL EN LA QUE ESTAMOS*/
		$sql5="SELECT idquincena from quincena where validez=1";
		$query5=mysqli_query($con, $sql5) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul5=mysqli_fetch_array($query5);
		$quincena=$resul5[0];
		/*FIN DE OBTENER QUINCENA ACTUAL*/

		//contamos cuántas 09 (retardos justificados) posee el empleado en la tabla justificaciones
		$sql6="SELECT count(d.clave_justificacion_clave_justificacion)
		FROM trabajador a
		INNER JOIN asistencia b on a.numero_trabajador = b.trabajador_trabajador and a.numero_trabajador = '$num' 
		INNER JOIN incidencia c on  b.id = c.asistencia_asistencia and b.quincena_quincena = $quincena
		INNER JOIN justificacion d on c.idincidencia = d.incidencia_incidencia and d.clave_justificacion_clave_justificacion= 09
		INNER JOIN acceso e on a.numero_trabajador=e.trabajador_trabajador
		INNER JOIN turno f on e.turno_turno = f.idturno";
		$query6= mysqli_query($con, $sql6) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul6=mysqli_fetch_array($query6);
		$totalRetardos=$resul6[0];

		//contamos cuántas 08 (omisiones justificadas) posee el empleado en la tabla justificaciones de incidencia
		$sql9="SELECT count(d.clave_justificacion_clave_justificacion) FROM trabajador a
		INNER JOIN asistencia b on a.numero_trabajador = b.trabajador_trabajador and a.numero_trabajador = $num 
		INNER JOIN incidencia c on  b.id = c.asistencia_asistencia and b.quincena_quincena = $quincena
		INNER JOIN justificacion d on c.idincidencia = d.incidencia_incidencia and d.clave_justificacion_clave_justificacion= 08
		INNER JOIN acceso e on a.numero_trabajador=e.trabajador_trabajador
		INNER JOIN turno f on e.turno_turno = f.idturno";  
		$query9= mysqli_query($con, $sql9) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul9=mysqli_fetch_array($query9);
		$totalOmisionesIncidencia=$resul9[0];

		$total =  $totalRetardos +$totalOmisionesIncidencia;
		//si totalOm es menor que 2 significa que aún puede justificar su omisión
		if($total>=2)
		{  
			echo "<script type='text/javascript'> alert('Ya excedió el límite de omisiones o retardos'); location.href='../ht/repositorio.php';</script>";
		}
		else
		{
			//Ubicacion del formato
			include("../pdf/retardo.php");

			// Instanciamos un objeto de la clase DOMPDF.
			$pdf = new DOMPDF();
			
			// Definimos el tamaño y orientación del papel que queremos.
			$pdf->set_paper("letter", "portrait");
			//$pdf->set_paper(array(0,0,104,250));
			
			// Cargamos el contenido HTML.
			$pdf->load_html(ob_get_clean());
			
			// Renderizamos el documento PDF.
			$pdf->render();
			
			// Enviamos el fichero PDF al navegador.
			$pdf->stream('justifica_retardo.pdf');
		}

	} //fin if-3

	//pase de salida
	if($operacion=="4")
	{
		/*OBTENER LA QUINCENA ACTUAL EN LA QUE ESTAMOS*/
		$sql5="SELECT idquincena, fecha_inicio, fecha_fin from quincena where validez=1";
		$query5=mysqli_query($con, $sql5) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul5=mysqli_fetch_array($query5);
		$quincena=$resul5[0];
		$f_inicio=$resul5[1];
		$f_fin=$resul5[2];
		$anio_actual=date("Y");
		/*FIN DE OBTENER QUINCENA ACTUAL*/

		//contamos cuántos PS  posee el empleado en la tabla especiales
		$sql9="SELECT count(clave_especial_clave_especial='PS') from especial where trabajador_trabajador='$num' and clave_especial_clave_especial='PS' and fecha_inicio>='2020-03-16' and fecha_fin<='2020-03-31'";  
		$query9= mysqli_query($con, $sql9) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul9=mysqli_fetch_array($query9);
		$pase_salida=$resul9[0];

		if($pase_salida==1)
		{  
			echo "<script type='text/javascript'> alert('Ya excedió el límite de pases de salida en la quincena'); location.href='../ht/repositorio.php';</script>";
			
		}
		else
		{

		//Ubicacion del formato
			include("../pdf/pase_salida.php");

			// Instanciamos un objeto de la clase DOMPDF.
			$pdf = new DOMPDF();
			
			// Definimos el tamaño y orientación del papel que queremos.
			$pdf->set_paper("letter", "portrait");
			//$pdf->set_paper(array(0,0,104,250));
			
			// Cargamos el contenido HTML.
			$pdf->load_html(ob_get_clean());
			
			// Renderizamos el documento PDF.
			$pdf->render();
			
			// Enviamos el fichero PDF al navegador.
			$pdf->stream('pase_salida.pdf');

		}                                             
	} //fin if-4


	function informacionHospital()
	{
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		global $datos;
		global $contador_d;

		//Seleccionamos a los empleados que tienen pase de salida hoy
		$sql="SELECT * FROM hospital;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
	
		//si total es igual a cero significa que no hay datos
		if($resul>0)
		{  
			$fila=mysqli_fetch_array($query);
			return [$fila[3],$fila[4],$fila[5]];	
		} 
		else
		{
			return null;
		}
	}
	
	function file_get_contents_curl($url) 
	{
		$crl = curl_init();
		$timeout = 5;
		curl_setopt($crl, CURLOPT_URL, $url);
		curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
		$ret = curl_exec($crl);
		curl_close($crl);
		return $ret;
	}

	function RevisarFechas($fechaInicio,$op)
    {
        $today=date("Y-m-d");
        $fecha_hoy=strtotime($today);
        $fecha_in = strtotime($fechaInicio);
		if($op==1)
		{
			if(($fecha_in<=$fecha_hoy))
			{
				return 0;//todo correcto
			}
			else
			{
				echo "<script> alert('La fecha debe ser menor o igual a hoy'); history.back(); </script>"; 
				exit(); 
			}
		}
		else
		{
			if($op==2)
			{
				if($fecha_in>=$fecha_hoy) 
				{
					return 0;//todo correcto
				}
				else
				{
					echo "<script> alert('La fecha debe ser mayor o igual a hoy'); history.back(); </script>"; 
					exit(); 
				}
			}
		}
    }//fin de RevisarFechas

?>