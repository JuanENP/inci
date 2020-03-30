<?php
ob_start();
	// Cargamos la librería dompdf que hemos instalado en la carpeta dompdf
	require_once("../pdf/dompdf/autoload.inc.php");
	use Dompdf\Dompdf;
	$numero=$_POST['num'];
	$motivo =$_POST['motivo'];
	$fecha=$_POST['fecha'];
	$operacion=$_POST['formato'];

	require("../Acceso/global.php");  
		
	$sql="select  a.numero_trabajador,a.nombre,a.apellido_paterno,a.apellido_materno,a.categoria_categoria,b.nombre,d.entrada,d.salida from trabajador a
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
		$_SESSION['num']   = $num  ;
		$_SESSION['nom']   = $nom  ;
		$_SESSION['a_pat'] = $a_pat;
		$_SESSION['a_mat'] = $a_mat;
		$_SESSION['cat']   = $cat  ;
		$_SESSION['des']   = $des  ;
		$_SESSION['ent']   = $ent  ;
		$_SESSION['sal']   = $sal  ;
		$_SESSION['motivo']   = $motivo;
		$_SESSION['fecha']   = $fecha;
		$_SESSION['operacion']   = $operacion;
	
	
	}

	//pase de entrada
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

	//pase de salida
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

?>