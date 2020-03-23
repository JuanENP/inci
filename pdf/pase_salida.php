<?php 
ob_start();
$num  =$_SESSION['num'];
$nom  =$_SESSION['nom'];
$a_pat=$_SESSION['a_pat']; 
$a_mat=$_SESSION['a_mat'];  
$cat  =$_SESSION['cat'];  
$des  =$_SESSION['des']; 
$ent  =$_SESSION['ent']; 
$sal  =$_SESSION['sal']; 
$nombre=$nom." ".$a_pat. " ".$a_mat;
$horario=$ent."-".$sal;

?>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/formatos.css">
    <title>Document</title>
</head>
<body>
    <!-- DIV QUE SELECCIONA TODO -->
    <div class="contenedor">
        <div class="borde_superior">
            <div id="logo_superior">
                <img src= "../images/pdf/logo.png">
            </div>
            <div id="p1">
                <b>HOSPITAL REGIONAL "B"</b>
                <b>"CENTENARIO DE LA REVOLUCIÓN MEXICANA"</b>
                <b>(ALTA ESPECIALIDAD)</b>
                <br> 
            </div> 
        </div>
        
        <div id="p2">
        <p> NO. EMPLEADO <?php echo $num?> </p>
        </div>
        
        <div id="p3">
        <p>SE HA CONCEDIDO PERMISO A: <?php echo $nombre?></p>
        <p>PARA SALIR EL DÍA DE HOY, DE LAS 12:30 A LAS 14:30 HRS</p>
        <p>PARA: <?php echo $nombre?></p>
        <p>CATEGORÍA: <?php echo $des?> </p>
        <p>HORARIO: <?php echo $horario?> </p><br>
        </div>

        <div id="p4">
            <p>CUERNAVACA, MORELOS A 10 DE FEBRERO DEL 2020</p><br><br>
            <p>______________________________</p>
                    <b>AUTORIZACIÓN</b>
        </div>
    </div>

</body>
</html>