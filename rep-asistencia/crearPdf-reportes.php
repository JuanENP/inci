<?php
session_start();
ob_start();
date_default_timezone_set('America/Mexico_City'); 
set_time_limit(600);//Indica que son 600 segundos, es decir 10 minutos máximo para ejecutar todo el script
    if (($_SESSION["name"]) && ($_SESSION["con"]))
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
	
		$f_hoy=date("d-m-Y");//guardar la fecha actual
		$operacion=$_POST['id'];//Para saber que reporte quiero
		$salida="";//Sirve para almacenar los errores
    }
    else
    {
        header("Location: ../index.php");
        die();
    }

	// Cargamos la librería dompdf que hemos instalado en la carpeta dompdf
	require_once("../pdf/dompdf/autoload.inc.php");
	use Dompdf\Dompdf;
	
	$num = date("d", strtotime($f_hoy));
	$mes = array('ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE');
	$mes = $mes[(date('m', strtotime($f_hoy))*1)-1];
	$anio= date("Y", strtotime($f_hoy));
	$dia_mes=$num.' DE '.$mes;
	//Enviar informacion del hospital a los reportes//
	$info=informacionHospital();
	if($info!=null)
	{
		$_SESSION['clave'] = $info[0];
		$_SESSION['nombre'] = $info[1];
		$_SESSION['descripcion'] = $info[2];
	}
	
	//REPORTE DE UNICO QUINCENAL DE INCIDENCIAS 
	if($operacion=="unico")
	{  	
		if(!(empty($_POST['quincena'])))
		{
			$todo_quincena=$_POST['quincena'];
		}
		$separa=explode(' ',$todo_quincena);
		$quincena=$separa[0];//Número de quincena
		$f_ini=$separa[1];//fecha inicio de quincena
		$f_fin=$separa[2];//fecha fin de quincena
		$fila=array();
		$reporte=array();
		$ultimo_r=0;
		$c=-1;
		$arreglo=array();
		$contador=0;
		
		retardos();
		faltas();
		pulir();
	
		//Calcula la cantidad de filas del arreglo
		foreach($arreglo as $fila)
		{
			$contador++;
		}
		
		if($contador>0)
		{
			//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
			$_SESSION['fecha'] = $dia_mes;
			$_SESSION['quincena'] = $quincena;
			$_SESSION['anio'] = $anio;
			$_SESSION['rep'] = $arreglo;
			$_SESSION['c'] = $contador;
			$_SESSION['f_ini'] = $f_ini;
			$_SESSION['f_fin'] = $f_fin;
			$nomArchivo="unico.php";
			$nomPdf="Reporte-de-incidencias.pdf";
			imprimepdf($nomArchivo,$nomPdf);
		}
		else
		{
		   	echo "<script language='javascript'> alert('No hay incidencias'); history.back();</script>";
			exit();

		}
		
	} //fin if

	if($operacion=="vacaciones")
	{
		if(!(empty($_POST['opcion'])))
		{
			$div=$_POST['opcion'];
			$datos=array();
			$contador_d=0;
			$contador=0;

			if($div=="rango")
			{
				//Ver si la fecha de inicio no está vacia
				if (!(empty($_POST['f_ini'])))
				{
					$fec_ini=$_POST['f_ini'];
					$valor=explode('-',$fec_ini);
					if(strlen($valor[0])==4)
					{
						$f_ini=$fec_ini;
					}
					else
					{
						$salida.="El año de la fecha de inicio es incorrecto.";
					}
				}
				else
				{	
					$salida.=" Debe escribir una fecha de inicio.";
				}
				//Si la fecha de fin está vacia
				if (!(empty($_POST['f_fin'])))
				{
					$fec_fin=$_POST['f_fin'];
					$valor2=explode('-',$fec_fin);
					if(strlen($valor2[0])==4)
					{
						$f_fin=$fec_fin;
					}
					else
					{
						$salida.="El año de la fecha de fin es incorrecto.";
					}
				}
				else
				{   
					$salida.=" Debe escribir una fecha de fin.";
				}
				
				
				if(empty($salida))
				{
					buscarxfecha();
					//Calcula la cantidad de filas del arreglo
					foreach($datos as $fila)
					{
						$contador++;
					}
					//Si el arreglo tiene datos, imprimir el reporte
					if($contador>0)
					{
						$tipo="VACACIONES DEL $f_ini AL $f_fin DE TODOS LO EMPLEADOS ";
						//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
						$_SESSION['fecha'] = $dia_mes;
						$_SESSION['anio'] = $anio;
						$_SESSION['datos'] = $datos;
						$_SESSION['c_d'] = $contador;
						$_SESSION['tipo'] = $tipo;
						$nomArchivo="vacaciones.php";
						$nomPdf="Reporte-de-vacaciones.pdf";
						imprimepdf($nomArchivo,$nomPdf);
					}
					else
					{
						echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
						exit();
					}//Fin del else que revisa si el arreglo tiene datos
				}
				else
				{
					echo "<script language='javascript'> alert('$salida'); history.back();</script>";
					exit();
				}//fin del else que revisa si hubo algun error en los input
			}//fin del div rango
			if($div=="todos")
			{

				$todo_quincena=$_POST['quincena2'];
				$separa=explode(' ',$todo_quincena);
				$quincena=$separa[0];//Número de quincena
				
				$f_ini=$separa[1];//fecha inicio de quincena
				$f_fin=$separa[2];//fecha fin de quincena

				buscarxquincena();
				//Calcula la cantidad de filas del arreglo
				foreach($datos as $fila)
				{
					$contador++;
				}
				//Si el arreglo tiene datos, imprimir el reporte
				if($contador>0)
				{
					
					$tipo="VACACIONES DEL PERSONAL EN LA QUINCENA $quincena DEL $f_ini AL $f_fin";
					//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
					$_SESSION['fecha'] = $dia_mes;
					$_SESSION['anio'] = $anio;
					$_SESSION['datos'] = $datos;
					$_SESSION['c_d'] = $contador;
					$_SESSION['tipo'] = $tipo;
			
					$nomArchivo="vacaciones.php";
					$nomPdf="Reporte-de-vacaciones.pdf";
					imprimepdf($nomArchivo,$nomPdf);
				}
				else
				{
					echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
					exit();
				}//Fin del else que revisa si el arreglo tiene datos
				
			}//fin todos-q

			if($div=="numero")
			{
				//ver si el número de empleado no está vacio
				if (!(empty($_POST['num'])))
				{
					$num_v=$_POST['num'];
					//Ver si el empleado existe en la base de datos
					$respuesta=consultaNumEmpleado($num_v);
					if($respuesta==true)
					{
						$num=$_POST['num'];
					}
					else
					{
						$salida.=" Debe escribir un número de empleado que exista";
					}
				}
				else
				{   
					$salida.=" Debe escribir un número de empleado";
				}

				if(empty($salida))
				{
					buscarxnumero();
					//Calcula la cantidad de filas del arreglo
					foreach($datos as $fila)
					{
						$contador++;
					}
					//Si el arreglo tiene datos, imprimir el reporte
					if($contador>0)
					{
						$tipo='VACACIONES DEL EMPLEADO '.$num.' ';
						//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
						$_SESSION['fecha'] = $dia_mes;
						$_SESSION['anio'] = $anio;
						$_SESSION['datos'] = $datos;
						$_SESSION['c_d'] = $contador;
						$_SESSION['tipo'] = $tipo;
                       
						$nomArchivo="vacaciones.php";
						$nomPdf="Reporte-de-vacaciones.pdf";
						imprimepdf($nomArchivo,$nomPdf);
					
					}
					else
					{
						echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
						exit();
					}//Fin del else que revisa si el arreglo tiene datos
				}
				else
				{
					echo "<script language='javascript'> alert('$salida'); history.back();</script>";
					exit();
				}//fin del else que revisa si hubo algun error en los input
			}//fin div numero
		}
		else
		{
			echo "<script language='javascript'> alert('Seleccione una opción'); history.back();</script>";
			exit();
		}	
	
	} //fin if

	if($operacion=="asistencia")
	{
		//Ver si la fecha de inicio no está vacia
		if (!(empty($_POST['fecha'])))
		{
			$fec=$_POST['fecha'];
			$valor=explode('-',$fec);
			if(strlen($valor[0])==4)
			{
				$fecha=$fec;
			}
			else
			{
				$salida.="El año es incorrecto.";
			}
		}
		else
		{	
			$salida.=" Debe escribir una fecha.";

		}
		
		if(empty($salida))
		{
			$hoy=date("Y-m-d");
			$fecha_actual=strtotime($hoy);
			$fecha_elegida = strtotime($fecha); //fecha en yyyy-mm-dd

			if($fecha_elegida !== $fecha_actual)
			{
				$dato=array();
				$conta=0;
				$contar=0;
				$dias = array('domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado');
				$diaSemana=$dias[date('w', $fecha_elegida)];
				asistenciaxfecha();
			}
			else
			{
				$dato=array();
				$conta=0;
				$contar=0;
				vienen_hoy();
			}

			//Calcula la cantidad de filas del arreglo
			foreach($dato as $fila)
			{
				$contar++;
			}
			//Si el arreglo tiene datos, imprimir el reporte
			if($contar>0)
			{	
				$dia2 = date("d", strtotime($fecha));
				$meses = array('ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE');
				$mes2 = $meses[(date('m', strtotime($fecha))*1)-1];
				$anio2= date("Y", strtotime($fecha));
				$fecha_buscada=$dia2.' DE '.$mes2.' DEL '.$anio2;

				// $tipo="COMISIONES PRÓXIMAS A VENCER";
				//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
				$_SESSION['fecha'] = $fecha_buscada;
				$_SESSION['dato'] = $dato;
				$_SESSION['contar'] = $contar;
				// $_SESSION['tipo'] = $tipo;
				$nomArchivo="asistencia.php";
				$nomPdf="Reporte-asistencia.pdf";
				imprimepdf($nomArchivo,$nomPdf);	
			
			}
			else
			{
				echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
				exit();
			}//Fin del else que revisa si el arreglo tiene datos
		}
		else
		{
			echo "<script language='javascript'> alert('$salida'); location.href='../ht/reportes.php';</script>";
			exit();
		}//fin del else que revisa si hubo algun error en los input	
	}

	if($operacion=="comisionados")
	{

		$div=$_POST['opc'];
		$datos=array();
		$contador_d=0;
		$contador=0;
		if(!(empty($_POST['sub-opc'])))
		{
			$subOpcion=$_POST['sub-opc'];
		}

		if($div=="fora")//Foráneos
		{
			if($subOpcion=="activas")//Obtener las comisiones activas
			{
				activasFora();
				foreach($datos as $fila)
				{
					$contador++;
				}
				//Si el arreglo tiene datos, imprimir el reporte
				if($contador>0)
				{
					$tipo="COMISIONES ACTIVAS FORÁNEOS";
					//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
					$_SESSION['fecha'] = $dia_mes;
					$_SESSION['anio'] = $anio;
					$_SESSION['datos'] = $datos;
					$_SESSION['c_d'] = $contador;
					$_SESSION['tipo'] = $tipo;
					$nomArchivo="comisiones.php";
					$nomPdf="comisiones-foraneos-activ.pdf";
					imprimepdf($nomArchivo,$nomPdf);
				}
				else
				{
					echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
					exit();
				}//Fin del else que revisa si el arreglo tiene datos
			}
			else //Sino es obtener las comisiones inactivas
			{
				inactivasFora();
				foreach($datos as $fila)
				{
					$contador++;
				}
				//Si el arreglo tiene datos, imprimir el reporte
				if($contador>0)
				{
					$tipo="COMISIONES INACTIVAS FORÁNEOS";
					//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
					$_SESSION['fecha'] = $dia_mes;
					$_SESSION['anio'] = $anio;
					$_SESSION['datos'] = $datos;
					$_SESSION['c_d'] = $contador;
					$_SESSION['tipo'] = $tipo;
					$nomArchivo="comisiones.php";
					$nomPdf="comisiones-foraneos-inac.pdf";
					imprimepdf($nomArchivo,$nomPdf);
				}
				else
				{
					echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
					exit();
				}//Fin del else que revisa si el arreglo tiene datos
			}
		}//fin del div 

		if($div=="int")//internos
		{
			if($subOpcion=="activas")//Obtener las comisiones activas
			{
				activasInt();
				foreach($datos as $fila)
				{
					$contador++;
				}
				//Si el arreglo tiene datos, imprimir el reporte
				if($contador>0)
				{
					$tipo="COMISIONES INTERNAS ACTIVAS";
					//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
					$_SESSION['fecha'] = $dia_mes;
					$_SESSION['anio'] = $anio;
					$_SESSION['datos'] = $datos;
					$_SESSION['c_d'] = $contador;
					$_SESSION['tipo'] = $tipo;
					$nomArchivo="comisiones.php";
					$nomPdf="comisiones-internas-activ.pdf";
					imprimepdf($nomArchivo,$nomPdf);
				}
				else
				{
					echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
					exit();
				}//Fin del else que revisa si el arreglo tiene datos
			}
			else //Sino es obtener las comisiones inactivas
			{
				inactivasInt();
				foreach($datos as $fila)
				{
					$contador++;
				}
				//Si el arreglo tiene datos, imprimir el reporte
				if($contador>0)
				{
					$tipo="COMISIONES INTERNAS INACTIVAS";
					//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
					$_SESSION['fecha'] = $dia_mes;
					$_SESSION['anio'] = $anio;
					$_SESSION['datos'] = $datos;
					$_SESSION['c_d'] = $contador;
					$_SESSION['tipo'] = $tipo;
					$nomArchivo="comisiones.php";
					$nomPdf="comisiones-internas-inac.pdf";
					imprimepdf($nomArchivo,$nomPdf);
				}
				else
				{
					echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
					exit();
				}//Fin del else que revisa si el arreglo tiene datos
			}
		}//fin del div 

		if($div=="ext")//externos
		{
			if($subOpcion=="activas")//Obtener las comisiones activas
			{
				activasExt();
				foreach($datos as $fila)
				{
					$contador++;
				}
				//Si el arreglo tiene datos, imprimir el reporte
				if($contador>0)
				{
					$tipo="COMISIONES EXTERNAS ACTIVAS";
					//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
					$_SESSION['fecha'] = $dia_mes;
					$_SESSION['anio'] = $anio;
					$_SESSION['datos'] = $datos;
					$_SESSION['c_d'] = $contador;
					$_SESSION['tipo'] = $tipo;
					$nomArchivo="comisiones.php";
					$nomPdf="comisiones-externas-activ.pdf";
					imprimepdf($nomArchivo,$nomPdf);
				}
				else
				{
					echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
					exit();
				}//Fin del else que revisa si el arreglo tiene datos
			}
			else //Sino es obtener las comisiones inactivas
			{
				inactivasExt();
				foreach($datos as $fila)
				{
					$contador++;
				}
				//Si el arreglo tiene datos, imprimir el reporte
				if($contador>0)
				{
					$tipo="COMISIONES EXTERNAS INACTIVAS";
					//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
					$_SESSION['fecha'] = $dia_mes;
					$_SESSION['anio'] = $anio;
					$_SESSION['datos'] = $datos;
					$_SESSION['c_d'] = $contador;
					$_SESSION['tipo'] = $tipo;
					$nomArchivo="comisiones.php";
					$nomPdf="comisiones-externas-inac.pdf";
					imprimepdf($nomArchivo,$nomPdf);
				}
				else
				{
					echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
					exit();
				}//Fin del else que revisa si el arreglo tiene datos
			}
		}//fin del div 

		if($div=="vence")//Comisiones próximas a vencer
		{
			comisionesxvencer();
			foreach($datos as $fila)
			{
				$contador++;
			}
			//Si el arreglo tiene datos, imprimir el reporte
			if($contador>0)
			{
				$tipo="COMISIONES PRÓXIMAS A VENCER";
				//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
				$_SESSION['fecha'] = $dia_mes;
				$_SESSION['anio'] = $anio;
				$_SESSION['datos'] = $datos;
				$_SESSION['c_d'] = $contador;
				$_SESSION['tipo'] = $tipo;
				$nomArchivo="comisiones.php";
				$nomPdf="comisiones-proximas-a-vencer.pdf";
				imprimepdf($nomArchivo,$nomPdf);
			}
			else
			{
				echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
				exit();
			}//Fin del else que revisa si el arreglo tiene datos
		}//fin del div 
	} //fin if

	if($operacion=="vinieron")
	{
		if(!(empty($_POST['opcion-v'])))
		{
			$div=$_POST['opcion-v'];
			$datos=array();
			$contador_d=0;
			$contador=0;

			if($div=="rango-v")
			{
				//Ver si la fecha de inicio no está vacia
				if (!(empty($_POST['f_ini-v'])))
				{
					$fec_ini=$_POST['f_ini-v'];
					$valor=explode('-',$fec_ini);
					if(strlen($valor[0])==4)
					{
						$f_ini=$fec_ini;
					}
					else
					{
						$salida.="El año de la fecha de inicio es incorrecto.";
					}
				}
				else
				{	
					$salida.=" Debe escribir una fecha de inicio.";
				}
				//Si la fecha de fin está vacia
				if (!(empty($_POST['f_fin-v'])))
				{
					$fec_fin=$_POST['f_fin-v'];
					$valor2=explode('-',$fec_fin);
					if(strlen($valor2[0])==4)
					{
						$f_fin=$fec_fin;
					}
					else
					{
						$salida.="El año de la fecha de fin es incorrecto.";
					}
				}
				else
				{   
					$salida.=" Debe escribir una fecha de fin.";
				}
				
				if(empty($salida))
				{
					asistenciaXrangoPulida(1);
					//Calcula la cantidad de filas del arreglo
					foreach($datos as $fila)
					{
						$contador++;
					}
					//Si el arreglo tiene datos, imprimir el reporte
					if($contador>0)
					{
						$tipo="ASISTENCIA DEL PERSONAL DEL ";
						//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
						$_SESSION['fecha'] = $dia_mes;
						$_SESSION['anio'] = $anio;
						$_SESSION['datos'] = $datos;
						$_SESSION['c_d'] = $contador;
						$_SESSION['tipo'] = $tipo;
						$_SESSION['f_ini'] = $f_ini;
						$_SESSION['f_fin'] = $f_fin;

						$nomArchivo="Vinieron.php";
						$nomPdf="Vinieron.pdf";
						imprimepdf($nomArchivo,$nomPdf);
					}
					else
					{
						echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
						exit();
					}//Fin del else que revisa si el arreglo tiene datos
				}
				else
				{
					echo "<script language='javascript'> alert('$salida'); history.back();</script>";
					exit();
				}//fin del else que revisa si hubo algun error en los input
			}//fin del div rango

            if($div=="numero-v")
			{
                //ver si el número de empleado no está vacio
                if (!(empty($_POST['num-v'])))
				{
                    $num_v=$_POST['num-v'];
                    //Ver si el empleado existe en la base de datos
                    $respuesta=consultaNumEmpleado($num_v);
                    if($respuesta==true)
                    {
                        $num=$_POST['num-v'];
                    }
                    else
                    {
                        $salida.=" Debe escribir un número de empleado que exista";
                    }
				}
				else
				{   
					$salida.=" Debe escribir un número de empleado";
                }
                
				//Ver si la fecha de inicio no está vacia
				if (!(empty($_POST['ini-v'])))
				{
					$fec_ini=$_POST['ini-v'];
					$valor=explode('-',$fec_ini);
					if(strlen($valor[0])==4)
					{
						$f_ini=$fec_ini;
					}
					else
					{
						$salida.=" El año de la fecha de inicio es incorrecto.";
					}
				}
				else
				{	
					$salida.=" Debe escribir una fecha de inicio.";
				}
				//Si la fecha de fin está vacia
				if (!(empty($_POST['fin-v'])))
				{
					$fec_fin=$_POST['fin-v'];
					$valor2=explode('-',$fec_fin);
					if(strlen($valor2[0])==4)
					{
						$f_fin=$fec_fin;
					}
					else
					{
						$salida.="El año de la fecha de fin es incorrecto.";
					}
				}
				else
				{   
					$salida.=" Debe escribir una fecha de fin.";
				}
				
				if(empty($salida))
				{
					asistenciaXrangoPulida(2);
					//Calcula la cantidad de filas del arreglo
					foreach($datos as $fila)
					{
						$contador++;
					}
					//Si el arreglo tiene datos, imprimir el reporte
					if($contador>0)
					{
						$tipo='ASISTENCIA DEL EMPLEADO '.$num. ' DEL';
						//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
						$_SESSION['fecha'] = $dia_mes;
						$_SESSION['anio'] = $anio;
						$_SESSION['datos'] = $datos;
						$_SESSION['c_d'] = $contador;
						$_SESSION['tipo'] = $tipo;
						$_SESSION['f_ini'] = $f_ini;
						$_SESSION['f_fin'] = $f_fin;
                        $_SESSION['num'] = $num;
						$nomArchivo="vinieron.php";
						$nomPdf="Vinieron.pdf";
						imprimepdf($nomArchivo,$nomPdf);

					
					}
					else
					{
						echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
						exit();
					}//Fin del else que revisa si el arreglo tiene datos
				}
				else
				{
					echo "<script language='javascript'> alert('$salida'); history.back();</script>";
					exit();
				}//fin del else que revisa si hubo algun error en los input
			}//fin del div rango

            if($div=="quincena-v")
			{
				//Ver si la fecha de inicio no está vacia
				if (!(empty($_POST['quincena3'])))
				{
					$todo_quincena=$_POST['quincena3'];
					$separa=explode(' ',$todo_quincena);
					$quincena=$separa[0];//Número de quincena
					$f_ini=$separa[1];//fecha inicio de quincena
					$f_fin=$separa[2];//fecha fin de quincena
				}
				else
				{	
					$salida.=" Seleccione una opción.";
				}
				if(empty($salida))
				{
					asistenciaXrangoPulida(3);
					//Calcula la cantidad de filas del arreglo
					foreach($datos as $fila)
					{
						$contador++;
					}
					//Si el arreglo tiene datos, imprimir el reporte
					if($contador>0)
					{
						$tipo="ASISTENCIA DEL PERSONAL EN LA QUINCENA $quincena DEL";
						//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
						$_SESSION['fecha'] = $dia_mes;
						$_SESSION['anio'] = $anio;
						$_SESSION['datos'] = $datos;
						$_SESSION['c_d'] = $contador;
						$_SESSION['tipo'] = $tipo;
						$_SESSION['f_ini'] =$f_ini ;
						$_SESSION['f_fin'] = $f_fin;

						$nomArchivo="Vinieron.php";
						$nomPdf="Vinieron.pdf";
						imprimepdf($nomArchivo,$nomPdf);

					
					}
					else
					{
						echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
						exit();
					}//Fin del else que revisa si el arreglo tiene datos
				}
				else
				{
					echo "<script language='javascript'> alert('$salida); history.back();</script>";
					exit();
				}//fin del else que revisa si hubo algun error en los input
			}//fin del div rango

			//AQUI VAN LOS DEMAS DIVS
		}
		else
		{
			echo "<script language='javascript'> alert('Seleccione un opción'); history.back();</script>";
			exit();
		}	
	
	} //fin if

	if($operacion=="faltaron")
	{
		if(!(empty($_POST['opcion-f'])))
		{
			
			$div=$_POST['opcion-f'];
			$datos=array();
			$contador_d=0;
			$contador=0;

			if($div=="rango-f")
			{
				//Ver si la fecha de inicio no está vacia
				if (!(empty($_POST['ini-f'])))
				{
					$fec_ini=$_POST['ini-f'];
					$valor=explode('-',$fec_ini);
					if(strlen($valor[0])==4)
					{
						$f_ini=$fec_ini;
					}
					else
					{
						$salida.="El año de la fecha de inicio es incorrecto. ";
					}
				}
				else
				{	
					$salida.=" Debe escribir una fecha de inicio. ";
				}

				//Si la fecha de fin está vacia
				if (!(empty($_POST['fin-f'])))
				{
					$fec_fin=$_POST['fin-f'];
					$valor2=explode('-',$fec_fin);
					if(strlen($valor2[0])==4)
					{
						$f_fin=$fec_fin;
					}
					else
					{
						$salida.="El año de la fecha de fin es incorrecto. ";
					}
				}
				else
				{   
					$salida.=" Debe escribir una fecha de fin. ";
				}
				
				if(empty($salida))
				{
					faltasXrangoPulida(1);
					//Calcula la cantidad de filas del arreglo
					foreach($datos as $fila)
					{
						$contador++;
					}
					//Si el arreglo tiene datos, imprimir el reporte
					if($contador>0)
					{
						$tipo="PERSONAL CON FALTAS DEL ";
						//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
						$_SESSION['fecha'] = $dia_mes;
						$_SESSION['anio'] = $anio;
						$_SESSION['datos'] = $datos;
						$_SESSION['c_d'] = $contador;
						$_SESSION['tipo'] = $tipo;
						$_SESSION['f_ini'] = $f_ini;
						$_SESSION['f_fin'] = $f_fin;

						$nomArchivo="faltaron.php";
						$nomPdf="Reporte-de-faltas.pdf";
						imprimepdf($nomArchivo,$nomPdf);

					
					}
					else
					{
						echo "<script language='javascript'> alert('No hay datos'); location.href='../ht/reportes.php';</script>";
						exit();
					}//Fin del else que revisa si el arreglo tiene datos
				}
				else
				{
					echo "<script language='javascript'> alert('$salida'); history.back();</script>";
					exit();
				}//fin del else que revisa si hubo algun error en los input
			}//fin del div rango

            if($div=="numero-f")
			{
                //ver si el número de empleado no está vacio
                if (!(empty($_POST['num-f'])))
				{
                    $num_f=$_POST['num-f'];
                    //Ver si el empleado existe en la base de datos
                    $respuesta=consultaNumEmpleado($num_f);
                    if($respuesta==true)
                    {
                        $num=$_POST['num-f'];
                    }
                    else
                    {
                        $salida.=" Debe escribir un número de empleado que exista. ";
                    }
				}
				else
				{   
					$salida.=" Debe escribir un número de empleado. ";
                }
                
				//Ver si la fecha de inicio no está vacia
				if (!(empty($_POST['ini-f'])))
				{
					$fec_ini=$_POST['ini-f'];
					$valor=explode('-',$fec_ini);
					if(strlen($valor[0])==4)
					{
						$f_ini=$fec_ini;
					}
					else
					{
						$salida.=" El año de la fecha de inicio es incorrecto.";
					}
				}
				else
				{	
					$salida.=" Debe escribir una fecha de inicio.";
				}
				//Si la fecha de fin está vacia
				if (!(empty($_POST['fin-f'])))
				{
					$fec_fin=$_POST['fin-f'];
					$valor2=explode('-',$fec_fin);
					if(strlen($valor2[0])==4)
					{
						$f_fin=$fec_fin;
					}
					else
					{
						$salida.="El año de la fecha de fin es incorrecto.";
					}
				}
				else
				{   
					$salida.=" Debe escribir una fecha de fin.";
				}
				
				if(empty($salida))
				{
					faltasXrangoPulida(2);
					//Calcula la cantidad de filas del arreglo
					foreach($datos as $fila)
					{
						$contador++;
					}
					//Si el arreglo tiene datos, imprimir el reporte
					if($contador>0)
					{
						$tipo='FALTAS DEL EMPLEADO '.$num. ' DEL';
						//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
						$_SESSION['fecha'] = $dia_mes;
						$_SESSION['anio'] = $anio;
						$_SESSION['datos'] = $datos;
						$_SESSION['c_d'] = $contador;
						$_SESSION['tipo'] = $tipo;
						$_SESSION['f_ini'] = $f_ini;
						$_SESSION['f_fin'] = $f_fin;
                        $_SESSION['num'] = $num;
						$nomArchivo="faltaron.php";
						$nomPdf="Reporte-de-faltas.pdf";
						imprimepdf($nomArchivo,$nomPdf);
					
					}
					else
					{
						echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
						exit();
					}//Fin del else que revisa si el arreglo tiene datos
				}
				else
				{
					echo "<script language='javascript'> alert('$salida'); history.back();</script>";
					exit();
				}//fin del else que revisa si hubo algun error en los input
			}//fin del div rango

            if($div=="quincena-f")
			{
				//Ver si la fecha de inicio no está vacia
				if (!(empty($_POST['quincena4'])))
				{
					$todo_quincena=$_POST['quincena4'];
					$separa=explode(' ',$todo_quincena);
					$quincena=$separa[0];//Número de quincena
					$f_ini=$separa[1];//fecha inicio de quincena
					$f_fin=$separa[2];//fecha fin de quincena
				}
				else
				{	
					$salida.=" Seleccione una opción.";
				}

				if(empty($salida))
				{
					faltasXrangoPulida(3);
					//Calcula la cantidad de filas del arreglo
					foreach($datos as $fila)
					{
						$contador++;
					}
					//Si el arreglo tiene datos, imprimir el reporte
					if($contador>0)
					{
						$tipo="FALTAS DEL PERSONAL EN LA QUINCENA $quincena DEL";
						//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
						$_SESSION['fecha'] = $dia_mes;
						$_SESSION['anio'] = $anio;
						$_SESSION['datos'] = $datos;
						$_SESSION['c_d'] = $contador;
						$_SESSION['tipo'] = $tipo;
						$_SESSION['f_ini'] =$f_ini ;
						$_SESSION['f_fin'] = $f_fin;

						$nomArchivo="faltaron.php";
						$nomPdf="Reporte-de-faltas.pdf";
						imprimepdf($nomArchivo,$nomPdf);
					
					}
					else
					{
						echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
						exit();
					}//Fin del else que revisa si el arreglo tiene datos
				}
				else
				{
					echo "<script language='javascript'> alert('$salida); history.back();</script>";
					exit();
				}//fin del else que revisa si hubo algun error en los input
			}//fin del div rango

			//AQUI VAN LOS DEMAS DIVS

		}
		else
		{
			echo "<script language='javascript'> alert('Seleccione una opción'); history.back();</script>";
			exit();
		}	
	
	} //fin if

	if($operacion=="cumpleOno")
	{

		$datos=array();
		$contador_d=0;
		$contador=0;
		//Ver si la fecha de inicio no está vacia
		if (!(empty($_POST['ini-c'])))
		{
			$fec_ini=$_POST['ini-c'];
			$valor=explode('-',$fec_ini);
			if(strlen($valor[0])==4)
			{
				$f_ini=$fec_ini;
			}
			else
			{
				$salida.="El año de la fecha de inicio es incorrecto.";
			}
		}
		else
		{	
			$salida.=" Debe escribir una fecha de inicio.";
		}
		//Si la fecha de fin está vacia
		if (!(empty($_POST['fin-c'])))
		{
			$fec_fin=$_POST['fin-c'];
			$valor2=explode('-',$fec_fin);
			if(strlen($valor2[0])==4)
			{
				$f_fin=$fec_fin;
			}
			else
			{
				$salida.="El año de la fecha de fin es incorrecto.";
			}
		}
		else
		{   
			$salida.=" Debe escribir una fecha de fin.";
		}
		
		if(empty($salida))
		{
			cumpleOno();
			asort($datos);		
				
			//Calcula la cantidad de filas del arreglo
			foreach($datos as $fila)
			{
				$contador++;
			}
			//Si el arreglo tiene datos, imprimir el reporte
			if($contador>0)
			{
				$tipo="CUMPLEAÑOS U ONOMÁSTICOS DEL ";
				//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
				$_SESSION['fecha'] = $dia_mes;
				$_SESSION['anio'] = $anio;
				$_SESSION['datos'] = $datos;
				$_SESSION['c_d'] = $contador;
				$_SESSION['tipo'] = $tipo;
				$_SESSION['f_ini'] = $f_ini;
				$_SESSION['f_fin'] = $f_fin;

				$nomArchivo="cumpleOno.php";
				$nomPdf="Reporte-de-cumples-u-ono.pdf";
				imprimepdf($nomArchivo,$nomPdf);
			}
			else
			{
				echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
				exit();
			}//Fin del else que revisa si el arreglo tiene datos
		}
		else
		{
			echo "<script language='javascript'> alert('$salida'); history.back();</script>";
			exit();
		}//fin del else que revisa si hubo algun error en los input
	

	} //fin if

	if($operacion=="guardias")
	{
		if(!(empty($_POST['opcion-g'])))
		{
			$div=$_POST['opcion-g'];
			$datos=array();
			$contador_d=0;
			$contador=0;

			if($div=="rango-g")
			{
				//Ver si la fecha de inicio no está vacia
				if (!(empty($_POST['ini-g'])))
				{
					$fec_ini=$_POST['ini-g'];
					$valor=explode('-',$fec_ini);
					if(strlen($valor[0])==4)
					{
						$f_ini=$fec_ini;
					}
					else
					{
						$salida.=" El año de la fecha de inicio es incorrecto.";
					}
				}
				else
				{	
					$salida.=" Debe escribir una fecha de inicio.";
				}
				
				//Si la fecha de fin está vacia
				if (!(empty($_POST['fin-g'])))
				{
					$fec_fin=$_POST['fin-g'];
					$valor2=explode('-',$fec_fin);
					if(strlen($valor2[0])==4)
					{
						$f_fin=$fec_fin;
					}
					else
					{
						$salida.="El año de la fecha de fin es incorrecto.";
					}
				}
				else
				{   
					$salida.=" Debe escribir una fecha de fin.";
				}
				

				if(empty($salida))
				{
					buscarguardias(1);
					//Calcula la cantidad de filas del arfreglo
					foreach($datos as $fila)
					{
						$contador++;
					}
					//Si el arreglo tiene datos, imprimir el reporte
					if($contador>0)
					{ 
						$tipo="GUARDIAS DEL ";
						//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
						$_SESSION['fecha'] = $dia_mes;
						$_SESSION['anio'] = $anio;
						$_SESSION['datos'] = $datos;
						$_SESSION['c_d'] = $contador;
						$_SESSION['tipo'] = $tipo;
						$_SESSION['f_ini'] = $f_ini;
						$_SESSION['f_fin'] = $f_fin;
		
						$nomArchivo="guardias.php";
						$nomPdf="Reporte-de-guardias.pdf";
						imprimepdf($nomArchivo,$nomPdf);
					
					}
					else
					{
						echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
						exit();
					}//Fin del else que revisa si el arreglo tiene datos
				}
				else
				{
					echo "<script language='javascript'> alert('$salida'); history.back();</script>";
					exit();
				}//fin del else que revisa si hubo algun error en los input
			}//fin del div 

			if($div=="numero-g")
			{
                //ver si el número de empleado no está vacio
                if (!(empty($_POST['num-g'])))
				{
                    $num_v=$_POST['num-g'];
                    //Ver si el empleado existe en la base de datos
                    $respuesta=consultaNumEmpleado($num_v);
                    if($respuesta==true)
                    {
                        $num=$_POST['num-g'];
                    }
                    else
                    {
                        $salida.=" Debe escribir un número de empleado que exista";
                    }
				}
				else
				{   
					$salida.=" Debe escribir un número de empleado";
                }
                
				//Ver si la fecha de inicio no está vacia
				if (!(empty($_POST['in-g'])))
				{
					$fec_ini=$_POST['in-g'];
					$valor=explode('-',$fec_ini);
					if(strlen($valor[0])==4)
					{
						$f_ini=$fec_ini;
					}
					else
					{
						$salida.=" El año de la fecha de inicio es incorrecto.";
					}
				}
				else
				{	
					$salida.=" Debe escribir una fecha de inicio.";
				}

				//Si la fecha de fin está vacia
				if (!(empty($_POST['fi-g'])))
				{
					$fec_fin=$_POST['fi-g'];
					$valor2=explode('-',$fec_fin);
					if(strlen($valor2[0])==4)
					{
						$f_fin=$fec_fin;
					}
					else
					{
						$salida.="El año de la fecha de fin es incorrecto.";
					}
				}
				else
				{   
					$salida.=" Debe escribir una fecha de fin.";
				}
				
				if(empty($salida))
				{
					buscarguardias(2);
					//Calcula la cantidad de filas del arreglo
					foreach($datos as $fila)
					{
						$contador++;
					}
					//Si el arreglo tiene datos, imprimir el reporte
					if($contador>0)
					{
						$tipo='GUARDIAS DEL '.$num. ' DEL';
						//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
						$_SESSION['fecha'] = $dia_mes;
						$_SESSION['anio'] = $anio;
						$_SESSION['datos'] = $datos;
						$_SESSION['c_d'] = $contador;
						$_SESSION['tipo'] = $tipo;
						$_SESSION['f_ini'] = $f_ini;
						$_SESSION['f_fin'] = $f_fin;
                        $_SESSION['num'] = $num;
						$nomArchivo="guardias.php";
						$nomPdf="Reporte-de-guardias.pdf";
						imprimepdf($nomArchivo,$nomPdf);

					
					}
					else
					{
						echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
						exit();
					}//Fin del else que revisa si el arreglo tiene datos
				}
				else
				{
					echo "<script language='javascript'> alert('$salida'); history.back();</script>";
					exit();
				}//fin del else que revisa si hubo algun error en los input
			}//fin del div 


            if($div=="quincena-g")
			{
				//Ver si la fecha de inicio no está vacia
				if (!(empty($_POST['quin-g'])))
				{
					$todo_quincena=$_POST['quin-g'];
					$separa=explode(' ',$todo_quincena);
					$quincena=$separa[0];//Número de quincena
					$f_ini=$separa[1];//fecha inicio de quincena
					$f_fin=$separa[2];//fecha fin de quincena
				}
				else
				{	
					$salida.=" Seleccione una opción.";
				}
				if(empty($salida))
				{
					buscarguardias(3);
					//Calcula la cantidad de filas del arreglo
					foreach($datos as $fila)
					{
						$contador++;
					}
					//Si el arreglo tiene datos, imprimir el reporte
					if($contador>0)
					{
						$tipo="GUARDIAS EN LA QUINCENA $quincena DEL";
						//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
						$_SESSION['fecha'] = $dia_mes;
						$_SESSION['anio'] = $anio;
						$_SESSION['datos'] = $datos;
						$_SESSION['c_d'] = $contador;
						$_SESSION['tipo'] = $tipo;
						$_SESSION['f_ini'] =$f_ini ;
						$_SESSION['f_fin'] = $f_fin;

						$nomArchivo="guardias.php";
						$nomPdf="Reporte-de-guardias.pdf";
						imprimepdf($nomArchivo,$nomPdf);

					
					}
					else
					{
						echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
						exit();
					}//Fin del else que revisa si el arreglo tiene datos
				}
				else
				{
					echo "<script language='javascript'> alert('$salida); history.back();</script>";
					exit();
				}//fin del else que revisa si hubo algun error en los input
			}//fin del div 
		}
		else
		{
			echo "<script language='javascript'> alert('Seleccione una opción'); history.back();</script>";
			exit();
		}	
	
	} //fin if
	
	if($operacion=="sextas")
	{
		if(!(empty($_POST['opcion-s'])))
		{
			$div=$_POST['opcion-s'];
			$datos=array();
			$contador_d=0;
			$contador=0;

			if($div=="todos-sexta")
			{
				todosSexta();
				//Calcula la cantidad de filas del arreglo
				foreach($datos as $fila)
				{
					$contador++;
				}
				//Si el arreglo tiene datos, imprimir el reporte
				if($contador>0)
				{ 
					$tipo="TODOS LOS EMPLEADOS CON SEXTA";
					//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
					$_SESSION['fecha'] = $dia_mes;
					$_SESSION['anio'] = $anio;
					$_SESSION['datos'] = $datos;
					$_SESSION['c_d'] = $contador;
					$_SESSION['tipo'] = $tipo;
		
					$nomArchivo="todosSexta.php";
					$nomPdf="Reporte-de-sextas.pdf";
					imprimepdf($nomArchivo,$nomPdf);
				
				}
				else
				{
					echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
					exit();
				}//Fin del else que revisa si el arreglo tiene datos

			}//fin del div 

			if($div=="viene-sexta")
			{
				$hoy=date("Y-m-d");
				$fecha = strtotime($hoy); //fecha en yyyy-mm-dd
				$Sem = array('domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado');
				$dia=$Sem[date('w', $fecha)];
				vienenSexta();
				//Calcula la cantidad de filas del arreglo
				foreach($datos as $fila)
				{
					$contador++;
				}
				//Si el arreglo tiene datos, imprimir el reporte
				if($contador>0)
				{
					$tipo='EMPLEADOS QUE VIENEN POR SU SEXTA';
					//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
					$_SESSION['fecha'] = $dia_mes;
					$_SESSION['anio'] = $anio;
					$_SESSION['datos'] = $datos;
					$_SESSION['c_d'] = $contador;
					$_SESSION['tipo'] = $tipo;
					$nomArchivo="vienenSexta.php";
					$nomPdf="Reporte-de-sextas.pdf";
					imprimepdf($nomArchivo,$nomPdf);

				
				}
				else
				{
					echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
					exit();
				}//Fin del else que revisa si el arreglo tiene datos



			}//fin del div 
		}
		else
		{
			echo "<script language='javascript'> alert('Seleccione una opción'); history.back();</script>";
			exit();
		}	
	
	} //fin if

	if($operacion=="licencias")
	{
		if(!(empty($_POST['opcion-l'])))
		{
			$div=$_POST['opcion-l'];
			$datos=array();
			$contador_d=0;
			$contador=0;

			if($div=="noempiezan")
			{
				licenciasqueNoempiezan();
				//Calcula la cantidad de filas del arreglo
				foreach($datos as $fila)
				{
					$contador++;
				}
				//Si el arreglo tiene datos, imprimir el reporte
				if($contador>0)
				{ 
					$tipo="LICENCIAS Y PERMISOS QUE AÚN NO EMPIEZAN";
					//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
					$_SESSION['fecha'] = $dia_mes;
					$_SESSION['anio'] = $anio;
					$_SESSION['datos'] = $datos;
					$_SESSION['c_d'] = $contador;
					$_SESSION['tipo'] = $tipo;
		
					$nomArchivo="licencias.php";
					$nomPdf="Reporte-de-licencias-que-no-empiezan.pdf";
					imprimepdf($nomArchivo,$nomPdf);
				
				}
				else
				{
					echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
					exit();
				}//Fin del else que revisa si el arreglo tiene datos

			}//fin del div 

			if($div=="xvencer")
			{
				licenciasXvencer();
				//Calcula la cantidad de filas del arreglo
				foreach($datos as $fila)
				{
					$contador++;
				}
				//Si el arreglo tiene datos, imprimir el reporte
				if($contador>0)
				{
					$tipo="LICENCIAS Y PERMISOS POR VENCER";
					//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
					$_SESSION['fecha'] = $dia_mes;
					$_SESSION['anio'] = $anio;
					$_SESSION['datos'] = $datos;
					$_SESSION['c_d'] = $contador;
					$_SESSION['tipo'] = $tipo;
		
					$nomArchivo="licencias.php";
					$nomPdf="Reporte-de-licencias-por-vencer.pdf";
					imprimepdf($nomArchivo,$nomPdf);
				}
				else
				{
					echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
					exit();
				}//Fin del else que revisa si el arreglo tiene datos
			}//fin del div 

			if($div=="vencida")
			{
				licenciasVencidas();
				//Calcula la cantidad de filas del arreglo
				foreach($datos as $fila)
				{
					$contador++;
				}
				//Si el arreglo tiene datos, imprimir el reporte
				if($contador>0)
				{ 
					$tipo="LICENCIAS Y PERMISOS VENCIDOS";
					//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
					$_SESSION['fecha'] = $dia_mes;
					$_SESSION['anio'] = $anio;
					$_SESSION['datos'] = $datos;
					$_SESSION['c_d'] = $contador;
					$_SESSION['tipo'] = $tipo;
		
					$nomArchivo="licencias.php";
					$nomPdf="Reporte-de-licencias-vencidas.pdf";
					imprimepdf($nomArchivo,$nomPdf);
				
				}
				else
				{
					echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
					exit();
				}//Fin del else que revisa si el arreglo tiene datos

			}//fin del div 

			if($div=="activa")
			{
				licenciasActivas();
				//Calcula la cantidad de filas del arreglo
				foreach($datos as $fila)
				{
					$contador++;
				}
				//Si el arreglo tiene datos, imprimir el reporte
				if($contador>0)
				{ 
					$tipo="LICENCIAS Y PERMISOS ACTIVOS";
					//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
					$_SESSION['fecha'] = $dia_mes;
					$_SESSION['anio'] = $anio;
					$_SESSION['datos'] = $datos;
					$_SESSION['c_d'] = $contador;
					$_SESSION['tipo'] = $tipo;
		
					$nomArchivo="licencias.php";
					$nomPdf="Reporte-de-licencias-activas.pdf";
					imprimepdf($nomArchivo,$nomPdf);
				
				}
				else
				{
					echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
					exit();
				}//Fin del else que revisa si el arreglo tiene datos

			}//fin del div 

		}
		else
		{
			echo "<script language='javascript'> alert('Seleccione una opción'); history.back();</script>";
			exit();
		}	
	
	} //fin if

	if($operacion=="pases")
	{
		if(!(empty($_POST['opcion-p'])))
		{
			$div=$_POST['opcion-p'];
			$datos=array();
			$contador_d=0;
			$contador=0;

			if($div=="hoy")
			{
				pase_salida(1);
				//Calcula la cantidad de filas del arreglo
				foreach($datos as $fila)
				{
					$contador++;
				}
				//Si el arreglo tiene datos, imprimir el reporte
				if($contador>0)
				{ 
					$tipo="PASE DE SALIDA ";
					//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
					$_SESSION['fecha'] = $dia_mes;
					$_SESSION['anio'] = $anio;
					$_SESSION['datos'] = $datos;
					$_SESSION['c_d'] = $contador;
					$_SESSION['tipo'] = $tipo;
					$nomArchivo="pases.php";
					$nomPdf="Reporte-de-pases-salida-hoy.pdf";
					imprimepdf($nomArchivo,$nomPdf);
				
				}
				else
				{
					echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
					exit();
				}//Fin del else que revisa si el arreglo tiene datos

			}//fin del div 

			if($div=="vencido")
			{
				pase_salida(2);
				//Calcula la cantidad de filas del arreglo
				foreach($datos as $fila)
				{
					$contador++;
				}
				//Si el arreglo tiene datos, imprimir el reporte
				if($contador>0)
				{
					$tipo="PASE DE SALIDA VENCIDOS";
					//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
					$_SESSION['fecha'] = $dia_mes;
					$_SESSION['anio'] = $anio;
					$_SESSION['datos'] = $datos;
					$_SESSION['c_d'] = $contador;
					$_SESSION['tipo'] = $tipo;
		
					$nomArchivo="pases.php";
					$nomPdf="Reporte-de-pases-salida-venidos.pdf";
					imprimepdf($nomArchivo,$nomPdf);
				}
				else
				{
					echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
					exit();
				}//Fin del else que revisa si el arreglo tiene datos

			}//fin del div 

			if($div=="antes")
			{
				pase_salida(3);
				//Calcula la cantidad de filas del arreglo
				foreach($datos as $fila)
				{
					$contador++;
				}
				//Si el arreglo tiene datos, imprimir el reporte
				if($contador>0)
				{ 
					$tipo="PASE DE SALIDA SIN VENCER";
					//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
					$_SESSION['fecha'] = $dia_mes;
					$_SESSION['anio'] = $anio;
					$_SESSION['datos'] = $datos;
					$_SESSION['c_d'] = $contador;
					$_SESSION['tipo'] = $tipo;
		
					$nomArchivo="pases.php";
					$nomPdf="Reporte-pases-salida-sin-vencer.pdf";
					imprimepdf($nomArchivo,$nomPdf);
				
				}
				else
				{
					echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
					exit();
				}//Fin del else que revisa si el arreglo tiene datos

			}//fin del div 

		}
		else
		{
			echo "<script language='javascript'> alert('Seleccione una opción'); history.back();</script>";
			exit();
		}	
	
	} //fin if

	function imprimepdf($nomArchivo,$nomPdf)
	{
		//Ubicacion del formato de reporte unico de incidencias quincenal
		include($nomArchivo);

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
		$pdf->stream($nomPdf);			

	}

    function consultaNumEmpleado($x)
    {
        $nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
		$sql="Select numero_trabajador from trabajador where numero_trabajador='$x'";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);

		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{ 
            return false; 
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
		{
            return true;
		}

	}
	
	function retardos()
	{
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		global $quincena;
		global $fila;
		global $ultimo_r;
		global $reporte;
		
		//seleccionamos a los empleados que tinen incidencias sin justificar y que sean de base o comisionados foraneos
		$sql="SELECT a.numero_trabajador, CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,
		c.clave_incidencia_clave_incidencia, b.fecha_entrada,b.fecha_salida 
		FROM trabajador a
		inner join asistencia b on a.numero_trabajador=b.trabajador_trabajador
		inner join incidencia c on b.id=c.asistencia_asistencia
		where
		b.quincena_quincena=$quincena 
		and (a.tipo_tipo=2 || a.tipo_tipo=4)
		and (c.clave_incidencia_clave_incidencia='01' 
		or c.clave_incidencia_clave_incidencia='02'
		or c.clave_incidencia_clave_incidencia='03' 
		or c.clave_incidencia_clave_incidencia='05'
		or c.clave_incidencia_clave_incidencia='16'
		or c.clave_incidencia_clave_incidencia='18'
		or c.clave_incidencia_clave_incidencia='19' 
		or c.clave_incidencia_clave_incidencia='20'
		or c.clave_incidencia_clave_incidencia='25'
		or c.clave_incidencia_clave_incidencia='26'
		or c.clave_incidencia_clave_incidencia='27' 
		or c.clave_incidencia_clave_incidencia='28'
		or c.clave_incidencia_clave_incidencia='30'
		or c.clave_incidencia_clave_incidencia='31')
		and NOT EXISTS 
		(SELECT d.clave_justificacion_clave_justificacion 
		FROM justificacion d where c.idincidencia=d.incidencia_incidencia)
		order by 
		a.numero_trabajador,
		b.fecha_entrada,
		b.fecha_salida,
		c.clave_incidencia_clave_incidencia;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);
		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{  
			// echo "<script language='javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
			// exit();
		} 
		else
		{
			while($resul=mysqli_fetch_array($query))
			{
				$fila[0]=$resul[0];//numero
				$fila[1]=$resul[1];//nom
				$fila[2]=$resul[2];//clave 
				$entrada=$resul[3];//fecha-entrada
				$separar=explode(' ',$entrada);//Separar la fecha de entrada de la hora de entrada
				$f_entrada=$separar[0];//obtener solo la fecha de entrada
				$separar2=explode('-',$f_entrada);//Separar la fecha de entrada para obtener solo el día
				$dia_entrada=$separar2[2]; 
				if($dia_entrada=="00")//Si el día de la entrada está en ceros, significa que deberá guardarse el día de salida
				{
					$salida=$resul[4];//fecha-entrada
					$separar=explode(' ',$salida);//Separar la fecha de entrada de la hora de entrada
					$f_salida=$separar[0];//obtener solo la fecha de entrada
					$separar2=explode('-',$f_salida);//Separar la fecha de entrada para obtener solo el día
					$dia_salida=$separar2[2]; 
					$fila[3]=$dia_salida;//dia-salida
				}
				else
				{
					//Sino debera guardarse el día de entrada 
					$fila[3]=$dia_entrada;//dia-entrada
				}
				
				$reporte[$ultimo_r]=$fila;
				$ultimo_r++;
			}//Fin while
		}//fin else
	}

	function faltas()
	{
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		global $quincena;
		global $fila;
		global $ultimo_r;
		global $reporte;

		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
		$sql="SELECT a.numero_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,b.clave,b.fecha
		FROM trabajador a 
		INNER JOIN falta b on a.numero_trabajador=b.trabajador_trabajador 
		AND quincena=$quincena
		AND (a.tipo_tipo=2 || a.tipo_tipo=4)
		AND NOT EXISTS 
		(SELECT c.idjustificar_falta
		FROM justificar_falta c where b.idfalta=c.falta_falta)
		order by 
		a.numero_trabajador,
		b.fecha";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);

		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
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
			//echo "<br>" . "num: " . $resul[0] . "  Nombre: " . $resul[1] . "  Clave: " . $resul[2] . "  Dia: " . $separa[2];
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
					//echo "<br>" . $reporte[$c][2];
					$arreglo[$c][3]=$arreglo[$c][3] .','. $reporte[$j][3];
					//echo "<br>" . $reporte[$c][3];
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
	
	function buscarxfecha()
	{
		
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		global $f_ini;
		global $f_fin;
		global $datos;
		global $contador_d;
		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
		$sql="SELECT a.numero_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,c.periodo,c.dia
		FROM trabajador a 
		INNER JOIN vacaciones b on a.numero_trabajador=b.trabajador_trabajador
		INNER jOIN dias_vacaciones c on b.idvacaciones=c.vacaciones_vacaciones
		where c.dia >='$f_ini' and c.dia <='$f_fin'
		and c.tomado=1
		order by  a.numero_trabajador,c.dia;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);

		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
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

	}//fin function xfecha

	function buscarxquincena()
	{
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		global $f_ini;
		global $f_fin;
		global $datos;
		global $contador_d;
		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
		$sql="SELECT a.numero_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,c.periodo,c.dia
		FROM trabajador a 
		INNER JOIN vacaciones b on a.numero_trabajador=b.trabajador_trabajador
		INNER jOIN dias_vacaciones c on b.idvacaciones=c.vacaciones_vacaciones
		where c.dia >='$f_ini' and c.dia <='$f_fin'
		and c.tomado=1
		order by  a.numero_trabajador,c.dia;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);

		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
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
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		global $num;
		global $datos;
		global $contador_d;
		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
		$sql="SELECT a.numero_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,c.periodo,c.dia
		FROM trabajador a 
		INNER JOIN vacaciones b on a.numero_trabajador=b.trabajador_trabajador
		INNER jOIN dias_vacaciones c on b.idvacaciones=c.vacaciones_vacaciones
		where b.trabajador_trabajador='$num'
		and c.tomado=1
		order by  a.numero_trabajador,c.dia;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);

		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
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
	}//fin function xnumero

	function vienen_hoy()
	{					
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		global $dato;
		global $conta;
		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
		$sql="Select a.numero_trabajador, CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n, a.depto_depto,a.categoria_categoria,c.turno_turno,d.entrada, d.salida
		from trabajador a 
        inner join vienen_hoy b on a.numero_trabajador=b.trabajador_trabajador 
        inner join acceso c on a.numero_trabajador=c.trabajador_trabajador 
        inner join turno d on c.turno_turno=d.idturno
		order by a.depto_depto,a.categoria_categoria,d.entrada,d.salida,a.numero_trabajador;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);

		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
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
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		global $diaSemana;
		global $dato;
		global $conta;

		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
		$sql="Select a.numero_trabajador, CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria,b.turno_turno,c.entrada, c.salida
		from trabajador a
		inner join acceso b on a.numero_trabajador=b.trabajador_trabajador
        inner join turno c on b.turno_turno=c.idturno
		and b.$diaSemana =1 and b.t_dias<=3;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);

		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
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
				//echo "<br>" . "num: " . $resul[0] . "  Nombre: " . $resul[1] . "  Clave: " . $resul[2] . "  Dia: " . $separa[2];
			}
		}
	}

	function vienenxsexta()
	{				
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		global $diaSemana;
		global $dato;
		global $conta;
		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
		$sql="Select a.numero_trabajador, CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n, a.depto_depto,a.categoria_categoria,b.turno_turno,c.entrada, c.salida
		from trabajador a
		inner join sexta b on a.numero_trabajador=b.trabajador_trabajador
		inner join turno c on b.turno_turno=c.idturno
		and b.$diaSemana=1 and b.validez=1 and b.t_dias<=2;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);

		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
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
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
        
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
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
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
					 
				}//FIN DEL WHILE
			}//FIN DEL IF
		}//FIN DEL FOR
	}// FIN DE FUNCION TIENE_COMISION

    //QUIEN TIENE COMISION OFICIAL O PARTICIPACION EN CURSO DE CAPACITACION
    function comision_oficial_participacion_curso()
    {
        //Si participación en curso de capacitación, adiestramiento o especialización hoy ( clave 29).
        //Si Comisión oficial con o sin viáticos o que comprenda menos de un día (clave 61).
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
				//Se debe eliminar de la lista al empleado, ya que no se presentará
				unset($dato[$j]);
				$conta--; //Disminuirá el contador
				$dato=array_values($dato);//El arreglo deberá reordenarse
            } //fin del if  
        }//fin del for
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
            inner join cumple_ono b
            on a.numero_trabajador=b.trabajador_trabajador
            and ((fecha_cumple='$f_hoy' and validez=0) 
            or (fecha_ono='$f_hoy' and validez=1))
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
            } //fin del if  
        }//fin del for
    }//FIN QUIEN TIENE CUMPLEAÑOS U ONOMASTICO
	/////////////////////////////////////////////////////
	
	//QUIEN TIENE COMISIONES
	function activasFora()
	{
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		global $datos;
		global $contador_d;
		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
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

		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
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
	

	function inactivasFora()
	{	
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		global $datos;
		global $contador_d;
		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
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

		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
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

	function activasInt()
	{
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		global $datos;
		global $contador_d;
		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
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

		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
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
		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
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

		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
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
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		global $datos;
		global $contador_d;
		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
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

		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
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
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		global $datos;
		global $contador_d;
		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
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

		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
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
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
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
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		global $datos;
		global $contador_d;
		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
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

		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
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

    function asistenciaXrangoPulida($x)
	{
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
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
        foreach($datos as $fila)
        {
            $count++;
        }
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
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		global $datos;
		global $contador_d;
		global $f_ini;
		global $f_fin;
		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
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

		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
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
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		global $datos;
        global $contador_d;
        global $num;
        global $f_ini;
        global $f_fin;
		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
		$sql="		Select b.trabajador_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria,d.entrada,d.salida,b.fecha_entrada,b.fecha_salida
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

		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
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
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		global $datos;
        global $contador_d;
        global $quincena;
		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
		$sql="Select b.trabajador_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria,d.entrada,d.salida,b.fecha_entrada,b.fecha_salida
		from trabajador a 
		inner join asistencia b on a.numero_trabajador=b.trabajador_trabajador
		inner join acceso c on a.numero_trabajador=c.trabajador_trabajador
		inner join turno d on c.turno_turno=d.idturno and
		b.quincena_quincena=$quincena
		group by a.numero_trabajador,b.fecha_entrada,b.fecha_salida;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);

		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
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
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
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
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		global $datos;
		global $contador_d;
		global $f_ini;
		global $f_fin;
		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
		$sql="Select b.trabajador_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria,b.fecha
		from trabajador a 
		inner join falta b on a.numero_trabajador=b.trabajador_trabajador
		and ((CAST(b.fecha AS DATE) >= '$f_ini' and  CAST(b.fecha AS DATE) <='$f_fin'))
		and NOT EXISTS (SELECT e.falta_falta FROM justificar_falta e where b.idfalta=e.falta_falta)
		order by b.trabajador_trabajador,b.fecha;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);

		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
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
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		global $datos;
        global $contador_d;
        global $num;
        global $f_ini;
        global $f_fin;
		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
		$sql="Select b.trabajador_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria,b.fecha
		from trabajador a 
		inner join falta b on a.numero_trabajador=b.trabajador_trabajador
		and b.trabajador_trabajador='$num' 
		and ((CAST(b.fecha AS DATE) >= '$f_ini' and  CAST(b.fecha AS DATE) <='$f_fin'))
		and NOT EXISTS (SELECT e.falta_falta FROM justificar_falta e where b.idfalta=e.falta_falta)
		order by b.fecha;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);

		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
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
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		global $datos;
        global $contador_d;
        global $quincena;
		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
		$sql="Select b.trabajador_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria,b.fecha
		from trabajador a 
		inner join falta b on a.numero_trabajador=b.trabajador_trabajador
		and b.quincena=$quincena
		and NOT EXISTS (SELECT e.falta_falta FROM justificar_falta e where b.idfalta=e.falta_falta)
		order by b.trabajador_trabajador,b.fecha,a.depto_depto,a.categoria_categoria;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);

		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
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
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
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
		
		//Seleccionamos a los empleados que tienen cumpleaños
		$sql="Select b.trabajador_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria,b.fecha_cumple
		from trabajador a 
		inner join cumple_ono b on a.numero_trabajador=b.trabajador_trabajador
		and  ((MONTH(fecha_cumple) = $mes_ini AND DAY(fecha_cumple) >= $dia_ini) AND (MONTH(fecha_cumple) = $mes_fin AND DAY(fecha_cumple) <= $dia_fin))
		and validez=0";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);

		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
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

		//Seleccionamos a los empleados que tienen onomásticos
		$sql2="Select b.trabajador_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria,b.fecha_ono
		from trabajador a 
		inner join cumple_ono b on a.numero_trabajador=b.trabajador_trabajador
		and  ((MONTH(fecha_ono) = $mes_ini AND DAY(fecha_ono) >= $dia_ini) AND (MONTH(fecha_ono) = $mes_fin AND DAY(fecha_ono) <= $dia_fin))
		and validez=1";  
		$query2= mysqli_query($con, $sql2) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul2=mysqli_num_rows($query2);
	
		//si total es igual a cero significa que no hay datos
		if($resul2==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
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
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		global $datos;
        global $contador_d;
		global $f_ini;
		global $f_fin;
		//Seleccionamos a los empleados que tienen guardias en un rango de fechas
		$sql="Select b.trabajador_solicitante,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria,b.hora_entrada,b.hora_salida,b.fecha_guardia,b.trabajador_suplente
		from trabajador a 
		inner join guardias b on a.numero_trabajador=b.trabajador_solicitante
		and b.fecha_guardia  >= '$f_ini' and  b.fecha_guardia <='$f_fin'
		order by b.fecha_guardia;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);

		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
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

	function guardiasXnumero()
	{
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
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

		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
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
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		global $datos;
        global $contador_d;
		global $quincena;
		//Seleccionamos a los empleados que tienen guardias en un rango de fechas
		$sql="Select b.trabajador_solicitante,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria,b.hora_entrada,b.hora_salida,b.fecha_guardia,b.trabajador_suplente
		from trabajador a 
		inner join guardias b on a.numero_trabajador=b.trabajador_solicitante
		and quincena=$quincena
		order by b.fecha_guardia;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);

		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
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
		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
		$sql2="select CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n from
		trabajador a where a.numero_trabajador='$num_suplente';";  
		$query2= mysqli_query($con, $sql2) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul2=mysqli_num_rows($query2);
		//si total es igual a cero significa que no hay datos
		if($resul2==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
		{
			$resul2=mysqli_fetch_array($query2);
			return $resul2[0];
		}//fin else
	}

	//Quienes tienen sextas
	function todosSexta()
	{
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		global $datos;
        global $contador_d;
		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
		$sql="Select a.numero_trabajador, CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto, a.categoria_categoria,
		c.lunes,c.martes,c.miercoles,c.jueves,c.viernes,c.sabado,c.domingo,
		b.lunes,b.martes,b.miercoles,b.jueves,c.viernes,b.sabado,b.domingo
		from trabajador a
		inner join sexta b on a.numero_trabajador=b.trabajador_trabajador
		inner join acceso c on a.numero_trabajador=c.trabajador_trabajador
		order by a.numero_trabajador, categoria_categoria,depto_depto;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);

		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
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
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		global $datos;
		global $contador_d;
		global $dia;

		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
		$sql="Select a.numero_trabajador, CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n, a.categoria_categoria,a.depto_depto 
		from trabajador a
		inner join sexta b 
		where a.numero_trabajador=b.trabajador_trabajador
		and b.$dia=1 and b.validez=1 and b.t_dias<=2 
		order by a.numero_trabajador;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);

		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
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
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		global $datos;
        global $contador_d;
        
		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
		$sql="Select b.trabajador_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria, b.fecha_inicio,b.fecha_fin,b.hora_entrada,b.hora_salida,b.clave_especial_clave_especial,b.empresa,b.duracion
		from trabajador a 
		inner join especial b on a.numero_trabajador=b.trabajador_trabajador
		and  now()<b.fecha_inicio
		and (
		b.clave_especial_clave_especial='29'
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
		or b.clave_especial_clave_especial='LSGSS'
		)
		order by b.fecha_inicio,b.trabajador_trabajador,b.clave_especial_clave_especial;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);

		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
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
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		global $datos;
        global $contador_d;
        global $quincena;
		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
		$sql="Select b.trabajador_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria, b.fecha_inicio,b.fecha_fin,b.hora_entrada,b.hora_salida,b.clave_especial_clave_especial,b.empresa,b.duracion
		from trabajador a 
		inner join especial b on a.numero_trabajador=b.trabajador_trabajador
		and  b.validez=1
		and (
		b.clave_especial_clave_especial='29'
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
		or b.clave_especial_clave_especial='LSGSS'
		)
		order by b.fecha_inicio, b.trabajador_trabajador;
		";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);

		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
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
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		global $datos;
		global $contador_d;
		$total=0;
		licenciasActivas();
		foreach($datos as $fila)
		{
			$total++;
		}
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
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
		global $datos;
        global $contador_d;
        
		//Seleccionamos a los empleados que tienen faltas sin justificar en tal quincena 
		$sql="Select b.trabajador_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria, b.fecha_inicio,b.fecha_fin,b.hora_entrada,b.hora_salida,b.clave_especial_clave_especial,b.empresa,b.duracion,validez
		from trabajador a 
		inner join especial b on a.numero_trabajador=b.trabajador_trabajador
		and  b.fecha_fin <now() and validez=0
		and (
		b.clave_especial_clave_especial='29'
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
		or b.clave_especial_clave_especial='LSGSS'
		)
		order by b.clave_especial_clave_especial,b.fecha_inicio,b.fecha_fin,b.trabajador_trabajador;";  
		$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
		$resul=mysqli_num_rows($query);

		//si total es igual a cero significa que no hay datos
		if($resul==0)
		{  
			// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
		} 
		else
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
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
		require("../Acceso/global.php"); 
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
	
			//si total es igual a cero significa que no hay datos
			if($resul==0)
			{  
				// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
			} 
			else
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
		
			//si total es igual a cero significa que no hay datos
			if($resul==0)
			{  
				// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
			} 
			else
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
		if($x==3)
		{
			//Seleccionamos los pases de salidas que aun no vencen
			$sql="Select b.trabajador_trabajador,CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as n,a.depto_depto,a.categoria_categoria, b.fecha_uso
			from trabajador a 
			inner join pase_salida b on a.numero_trabajador=b.trabajador_trabajador
			and  '$dia'<b.fecha_uso
			order by b.fecha_uso, b.trabajador_trabajador;";  
			$query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
			$resul=mysqli_num_rows($query);
		
			//si total es igual a cero significa que no hay datos
			if($resul==0)
			{  
				// echo "<script type='text/javascript'> alert('No hay incidencias'); location.href='../ht/reportes.php';</script>";
			} 
			else
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
			return [$fila[0],$fila[1],$fila[2]];	
		} 
		else
		{
			return null;
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
<script type="text/javascript">
    function error(cadena)
    {
        alert(cadena);
        history.back();
	}
	
	function nodatos()
	{
		alert('No hay datos');
		history.back();
		exit();
	}

	function opcion()
	{
		alert('Seleccione una opción');
		history.back();
	}

	function imprime(texto)
	{
		alert(texto);
		history.back();
	}
</script>