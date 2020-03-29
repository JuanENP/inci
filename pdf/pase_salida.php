<?php 
ob_start();
//******formatear a la zona horaria de la ciudad de México**********
date_default_timezone_set('America/Mexico_City');
$num  =$_SESSION['num'];
$nom  =$_SESSION['nom'];
$a_pat=$_SESSION['a_pat']; 
$a_mat=$_SESSION['a_mat'];  
$cat  =$_SESSION['cat'];  
$des  =$_SESSION['des']; 
$ent  =$_SESSION['ent']; 
$sal  =$_SESSION['sal']; 
$motivo  =$_SESSION['motivo']; 
$fecha =$_SESSION['fecha'];
$nombre=$nom." ".$a_pat. " ".$a_mat;
$horario=$ent."-".$sal;



?>
<html lang="es">
<head>
    <meta meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/normalize.css" />
    <link rel="stylesheet" href="../assets/css/formato-pase-salida.css">
    <title>Documento</title>
</head>
<body>
  
    <!-- DIV QUE SELECCIONA TODO -->
    <div class="contenedor">
       
       <div class="borde_superior">
            <div id="imagen_superior">
                <img src= "../images/pdf/logo.png" id="logo_superior">
            </div>
            <div id="p1">
                <b><p>HOSPITAL REGIONAL "B"</p>
                <p>"CENTENARIO DE LA REVOLUCIÓN MEXICANA"</p>
                <p>(ALTA ESPECIALIDAD)<p></b>
                <br> 
                <b> NO. EMPLEADO </b> <?php echo $num?> </p>
            </div> 
        </div>
        

        
        <div id="p3">
        <p>SE HA CONCEDIDO PERMISO A: <?php echo $nombre?>
        <p>PARA SALIR EL DÍA DE HOY, DE LAS 12:30 A LAS 14:30 HRS</p>
        <p>PARA: <?php echo $motivo?></p>
        <p>CATEGORÍA: <?php echo $des?> </p>
        <p>HORARIO: <?php echo $horario?> </p><br>
        </div>
     
        <div id="p4">
            <p>CUERNAVACA, MORELOS A <?php echo $fecha?> </p><br><br>
            <p >______________________________</p>
                    <b>AUTORIZACIÓN</b>
        </div>
        <br><br>
    </div>

</body>
</html>