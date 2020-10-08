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
		if(!empty($_POST['id']))
		{
			$operacion=$_POST['id'];//Para saber que reporte quiero
		}
		else
		{
			echo "<script language='javascript'> alert('Elija una opción'); history.back();</script>";
			exit();	
		}
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
	//Archivo que contiene todas las funciones necesarias
	require("funcionesNecesarias.php"); 
	$num = date("d", strtotime($f_hoy));
	$mes = array('ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE');
	$mes = $mes[(date('m', strtotime($f_hoy))*1)-1];
	$anio= date("Y", strtotime($f_hoy));
	$dia_mes=$num.' DE '.$mes;
	//Enviar informacion del hospital a los reportes
	$info=informacionHospital();
	if($info != null)
	{
		$_SESSION['clave'] = $info[0];
		$_SESSION['nombre'] = $info[1];
		$_SESSION['descripcion'] = $info[2];
	}
	else
	{
		$salida="No hay información del hospital en la base de datos, verifique con el administrador de sistemas.";
        echo "<script type=\"text/javascript\">alert('$salida'); history.back(); </script>";
	}
	//Reporte único quincenal de incidencias
	if($operacion=="unico")
	{  	
		if(!(empty($_POST['quincena'])))
		{
			$todo_quincena=$_POST['quincena'];
		}
		//Separar los datos de lo que se recibe en quincena
		$separa=explode(' ',$todo_quincena);
		$quincena=$separa[0];//Número de quincena seleccionado
		$f_ini=$separa[1];//fecha inicio de quincena
		$f_fin=$separa[2];//fecha fin de quincena
		$fila=array();
		$reporte=array();
		$ultimo_r=0;
		$c=-1;
		$arreglo=array();//arreglo con los datos del reporte
		$contador=0;
		incidencias();
		cumpleOno_clave14cica();
		faltas();
		justificaciones();
		especiales();
		vacaciones();
		suspensiones_y_bajas();
		sin_der_estimulo_desempeño_cica78();
		pulir();
		$contador=count($arreglo);
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
	}

	if($operacion=="unico-comisionados")
	{
		if(!(empty($_POST['opcion'])))
		{
			$div=$_POST['opcion'];
			$datos=array();
			$contador_d=0;
			$contador=0;
			require('funcionesNecesariasRepForaneos.php');
			if($div=="quincena-c")
			{
				if(!(empty($_POST['quincena'])))
				{
					$todo_quincena=$_POST['quincena'];
				}
				//Separar los datos de lo que se recibe en quincena
				$separa=explode(' ',$todo_quincena);
				$quincena=$separa[0];//Número de quincena seleccionado
				$f_ini=$separa[1];//fecha inicio de quincena
				$f_fin=$separa[2];//fecha fin de quincena
				$fila=array();//Sirve para guardar a los que tienen incidnecias
				$reporte=array();
				$ultimo_r=0;
				$c=-1;
				$arreglo=array();//arreglo con los datos del reporte
				$contador=0;
				
				incidencias_f();
				cumpleOno_clave14cica_f();
				faltas_f();
				justificaciones_f();
				especiales_f();
				vacaciones_f();
				suspensiones_y_bajas_f();
				sin_der_estimulo_desempeño_cica78_f();
				pulir_f();
				$contador=count($arreglo);
				//Si el arreglo tiene datos, imprimir el reporte
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
					$nomArchivo="unico-foraneos.php";
					$nomPdf="Reporte-unico-foraneos.pdf";
					imprimepdf($nomArchivo,$nomPdf);
				}
				else
				{
					echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
					exit();
				}//Fin del else que revisa si el arreglo tiene datos
			}

			if($div=="numero-c")
			{
				//ver si el número de empleado no está vacio
				if (!(empty($_POST['numero'])))
				{
					$num=$_POST['numero'];
					//Ver si el empleado existe en la base de datos
					$respuesta=consultaNumEmpleado($num);
					if($respuesta==true)
					{
						$numero=$num;
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
					if(!(empty($_POST['quincena'])))
					{
						$todo_quincena=$_POST['quincena'];
					}
					//Separar los datos de lo que se recibe en quincena
					$separa=explode(' ',$todo_quincena);
					$quincena=$separa[0];//Número de quincena seleccionado
					$f_ini=$separa[1];//fecha inicio de quincena
					$f_fin=$separa[2];//fecha fin de quincena
					$fila=array();//Sirve para guardar a los que tienen incidnecias
					$reporte=array();
					$ultimo_r=0;
					$c=-1;
					$arreglo=array();//arreglo con los datos del reporte
					$contador=0;
					incidencias_trabajador();
					cumpleOno_clave14cica_trabajador();
					faltas_trabajador();
					justificaciones_trabajador();
					especiales_trabajador();
					vacaciones_trabajador();
					suspensiones_y_bajas_trabajador();
					sin_der_estimulo_desempeño_cica78_trabajador();
					pulir_trabajador();
					$contador=count($arreglo);
					//Si el arreglo tiene datos, imprimir el reporte
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
						$nomArchivo="unico-foraneos.php";
						$nomPdf="Reporte-unico-trabajador-foraneo.pdf";
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
			}
			
		}
		else
		{
			echo "<script language='javascript'> alert('Seleccione una opción'); history.back();</script>";
			exit();
		}	
	} 

	if($operacion=="estimulos")
	{
		if(!(empty($_POST['opcion'])))
		{
			$div=$_POST['opcion'];
			$arregloEmpleados=array();
			$contador=0;
			if(!(empty($_POST['todoQuincena'])))
			{
				$todo_quincena=$_POST['todoQuincena'];
			}
			//Separar los datos de lo que se recibe en quincena
			$separa=explode(' ',$todo_quincena);
			$quincena=$separa[0];//Número de quincena seleccionado
			$f_ini=$separa[1];//fecha inicio de quincena
			$f_fin=$separa[2];//fecha fin de quincena
			
			require('funcionesNecesariasRepEstimulos.php');
			if($div=="mot-punt")
			{
				derecho_puntualidad();
				
				$contador=count($arregloEmpleados);
				//Si el arreglo tiene datos, imprimir el reporte
				if($contador>0)
				{
					//VARIABLES QUE SE ENVIARÁN AL HTML DEL PDF
					$_SESSION['fecha'] = $dia_mes;
					$_SESSION['quincena'] = $quincena;
					$_SESSION['anio'] = $anio;
					$_SESSION['rep'] = $arregloEmpleados;
					$_SESSION['contador'] = $contador;
					$_SESSION['f_ini'] = $f_ini;
					$_SESSION['f_fin'] = $f_fin;
					$nomArchivo="estimulos.php";
					$nomPdf="Reporte-empleados-con-derecho-estimulos.pdf";
					imprimepdf($nomArchivo,$nomPdf);
				}
				else
				{
					echo "<script language='javascript'> alert('No hay datos'); history.back();</script>";
					exit();
				}//Fin del else que revisa si el arreglo tiene datos
				
			}

			if($div=="numero-c")
			{
				//ver si el número de empleado no está vacio
				if (!(empty($_POST['numero'])))
				{
					$num=$_POST['numero'];
					//Ver si el empleado existe en la base de datos
					$respuesta=consultaNumEmpleado($num);
					if($respuesta==true)
					{
						$numero=$num;
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
					if(!(empty($_POST['quincena'])))
					{
						$todo_quincena=$_POST['quincena'];
					}
					//Separar los datos de lo que se recibe en quincena
					$separa=explode(' ',$todo_quincena);
					$quincena=$separa[0];//Número de quincena seleccionado
					$f_ini=$separa[1];//fecha inicio de quincena
					$f_fin=$separa[2];//fecha fin de quincena
					$fila=array();//Sirve para guardar a los que tienen incidnecias
					$reporte=array();
					$ultimo_r=0;
					$c=-1;
					$arreglo=array();//arreglo con los datos del reporte
					$contador=0;
					incidencias_trabajador();
					cumpleOno_clave14cica_trabajador();
					faltas_trabajador();
					justificaciones_trabajador();
					especiales_trabajador();
					vacaciones_trabajador();
					suspensiones_y_bajas_trabajador();
					sin_der_estimulo_desempeño_cica78_trabajador();
					pulir_trabajador();
					$contador=count($arreglo);
					//Si el arreglo tiene datos, imprimir el reporte
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
						$nomArchivo="unico-foraneos.php";
						$nomPdf="Reporte-unico-trabajador-foraneo.pdf";
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
			}
			
		}
		else
		{
			echo "<script language='javascript'> alert('Seleccione una opción'); history.back();</script>";
			exit();
		}	
	} 

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
					$contador=count($datos);
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
				pulirVacaciones();
				$contador=count($datos);
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
					$contador=count($datos);
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
	
	} 

	if($operacion=="asistencia")
	{
		//Ver si la fecha de inicio no está vacia
		if (!(empty($_POST['fecha'])))
		{
			$fecha=$_POST['fecha'];
			$valor=explode('-',$fecha);
			if(strlen($valor[0]) !== 4)
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
			$contar=count($dato);
			//Si el arreglo tiene datos, imprimir el reporte
			if($contar>0)
			{	
				$dia2 = date("d", strtotime($fecha));
				$meses = array('ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE');
				$mes2 = $meses[(date('m', strtotime($fecha))*1)-1];
				$anio2= date("Y", strtotime($fecha));
				$fecha_buscada=$dia2.' DE '.$mes2.' DEL '.$anio2;
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
	}  

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
	
	}  

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
	
	}  

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
	

	}  

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
	
	}  
	
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
	
	}  

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
	
	}  

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
		}
		else
		{
			echo "<script language='javascript'> alert('Seleccione una opción'); history.back();</script>";
			exit();
		}	
	
	}  

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