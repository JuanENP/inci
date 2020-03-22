<?php
ob_start();

// Cargamos la librería dompdf que hemos instalado en la carpeta dompdf
require_once("../pdf/dompdf/autoload.inc.php");
use Dompdf\Dompdf;
$numero=$_POST['num'];
require("../Acceso/global.php");  
    
$sql="select  a.numero_trabajador,a.nombre,a.apellido_paterno,a.apellido_materno,a.categoria_Categoria,b.nombre,d.entrada,d.salida from trabajador a inner join categoria b on b.categoria = a.categoria_Categoria and a.numero_trabajador = '15321030'
inner join acceso c on acceso_idacceso = c.idacceso
inner join turno d on c.turno_turno = d.turno "; 
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
	  $des =$resul[5];
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
  
}


$operacion=$_POST['formato'];
if($operacion=="1")
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
	$pdf->stream('reportePdf.pdf');

} //fin if-1

if($operacion=="2")
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
	$pdf->stream('reportePdf.pdf');

} //fin if-2

if($operacion=="3")
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
	$pdf->stream('reportePdf.pdf');

} //fin if-3
function file_get_contents_curl($url) {
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