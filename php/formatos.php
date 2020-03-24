<?php
session_start();
$numero=$_POST['num'];
echo $numero;

$operacion=$_POST['formato'];
if($operacion=="1")
{
     require("../Acceso/global.php");  
    
     $sql="select a.nombre,a.apellido_paterno,a.apellido_materno,a.categoria_categoria,b.nombre,d.entrada,d.salida from trabajador a inner join categoria b on b.categoria = a.categoria_categoria and a.numero_trabajador = '$numero'
     inner join acceso c on acceso_acceso = c.idacceso
     inner join turno d on c.turno_turno = d.idturno "; 
     $query= mysqli_query($con, $sql);
     if(!$query)
     {
       die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
     }
     else
     {
       $resul=mysqli_fetch_array($query);
           $nom   =$resul[1];
           $a_pat =$resul[2];
           $a_mat =$resul[3];
           $cat  =$resul[4];
           $des  =$resul[5];

           
     }
                                
    ///////////////////////////////////////////////PDF///////////////////////////////////

    
            $time=time();               ////Fecha/////
            $fecha=date("d-m-Y",$time);/////Actual///
    
            require('../fpdf/fpdf.php');
    
                class PDF extends FPDF
                {
                    // Cabecera de página
                function Header()
                {
                    // Logo                     largo,altura
                    $this->Image('../images/pdf/superior.jpg',10,8,200,25);
                    // Arial bold 15
                    $this->SetFont('Arial','',15);
                    // Movernos a la derecha
                    $this->Cell(80);
                    // Título
                    $this->Ln(19);
                    //          largo,alto celda
                    $this->Cell(590,20,'Dirección
                    Subdirección Administrativa
                    Coordinación de Mantenimiento
                    OFICIO No. 043/140/CM/0107/2020
                    Emiliano Zapata, Mor. , a '.$fecha.'
                    Asunto: Justificación de Omisión de Entrada',0,0,'C');
                    // Salto de línea
                    $this->Ln(20);
                }
    
                // Pie de página
                function Footer()
                {
                    // Logo                     largo,altura
                    $this->Image('../images/pdf/inferior.jpg',100 ,200, 105 , 48);
                    // Arial bold 15
                    // Posición: a 1,5 cm del final
                    $this->SetY(-15);
                    // Arial italic 8
                    $this->SetFont('Arial','I',8);
                    // Número de página
                    $this->Cell(0,10,utf8_decode('Av. Universidad No.40, Col. Palo Escrito, Municipio de Emiliano Zapata, Morelos C.P. 62760. Télefono:01(777)10 11 400 extensión 40035 Correo:hipolito.melchor@issste.gob.mx ').$this->PageNo().'/{nb}',0,0,'C');
                }
                }
    
                $pdf = new PDF();
                $pdf->AliasNbPages();
                $pdf->AddPage();
                $pdf->SetFont('Arial','U',16);
                //$pdf->Cell(40,10,utf8_decode('¡Hola, Mundo!'));
                $pdf->SetTextColor(9,9,162);
                $pdf->Cell(40,10,utf8_decode('C.Carlos J. Gutiérrez Nájera
                Coordinador de Recursos Humanos
                Presente. ' . $fecha),0,1);//La fecha actual
                $pdf->SetTextColor(0,0,0);
                $pdf->SetFont('Arial','B',16);
                $pdf->Ln();
    
                $pdf->SetTextColor(184,33,6);
                $pdf->SetFont('Arial','U',16);
                $pdf->Cell(40,10,utf8_decode('DATOS PERSONALES'),0,1);
                $pdf->SetFont('Arial','B',16);
                $pdf->SetTextColor(0,0,0);
                $pdf->Ln();
                $pdf->Cell(40,10,utf8_decode('Nombre y firma: '.$nom.' '. $a_pat.' '. $a_mat.' '.$cat.' '.$des),0,1);
                $pdf->Ln();
    
                $pdf->SetTextColor(184,33,6);
                $pdf->SetFont('Arial','U',16);
                $pdf->Cell(40,10,utf8_decode('ESCOLARIDAD'),0,1);
                $pdf->SetFont('Arial','B',16);
                $pdf->SetTextColor(0,0,0);
                $pdf->Ln();
              
    
                $pdf->SetTextColor(184,33,6);
                $pdf->SetFont('Arial','U',16);
                $pdf->Cell(40,10,utf8_decode('DATOS DE LA ACTIVIDAD'),0,1);
                $pdf->SetFont('Arial','B',16);
                $pdf->SetTextColor(0,0,0);
               
                ob_end_clean();
                $pdf->Output();
                $fin=1;	
                echo "<script>var res=alert('PDF GENERADO')</script>";
        

}//fin if(1)

?>