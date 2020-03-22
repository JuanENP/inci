
<?php 
ob_start();
$num  =$_SESSION['num'];
$nom  =$_SESSION['nom'];
$a_pat=$_SESSION['a_pat']; 
$a_mat=$_SESSION['a_mat'];  
$cat  =$_SESSION['cat'];  
$des  =$_SESSION['des']; 
$nombre=$nom." ".$a_pat. " ".$a_mat;

?>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/formatos.css">
    <title>Document</title>
</head>
<body>
    <div id="logo_superior">
    <img src= "../images/pdf/superior.jpg">
    </div>
    <div id="p1">
    <p>Dirección</p>
    <p>Subdirección Administrativa</p>
    <p>Coordinación de Mantenimiento</p>
    <b>OFICIO No. 043/140/CM/0107/2020</b>
    <p>Emiliano Zapata, Mor., a 18 de marzo de 2020</p>
    <b>Asunto: Justificación de Omisión Entrada</b>
    <br> 
    </div>

    <div id="p2">
    <p>C.Carlos J.Gutierrez</p>
    <p>Coordinador de Recursos Humanos</p>
    <p>Presente</p>
    <br> 
    <p>Me permito solicitar su valiosa intervención para que le justifique la omisión de entrada del día 17 de marzo 2020, del C. <b><?php echo $nombre?></b>, </p>
    <p>Categoría: <b><?php echo $des?></b>, No. Empleado <?php echo $num?>, en virtud </p>
    <p>de que no se afecte su salario y estímulos correspondientes. </p>
    <br> 
    </div>
    
    <div id="p3">
    <p>Agradeciendo de antemano su atención al respecto, aprovecho la ocasión para enviarle un cordial saludo.</p>
    <br> <br> <br>
    <h3>Atentamente</h3>
    <br> <br>
    <b>Ing. Hipolito Melchor Gutierrez</b>
    <b>Coordinador de Mantenimiento</b>
    <br> <br>
    <b>OFICIO No.043/140/CM/0107/2020</b>
    <p>Emiliano Zapata, Mor., a 18 de marzo de 2020</p>
    <b>Asunto: Justificación de Omisión de Entrada</b>
    </div>
    
    <div id="p4">
    <b>c.c.Minutario</b>
    <b>HMG/vmq.</b>
    <br> 
    </div>

    <div id="p5">
    <p>Av. Universidad No.40, Col.Palo Escrito, Municipio de Emiliano Zapata, Morelos</p>
    <p>C.P. 62760, Teléfono: 01(777)10 11 400 extensión 40035</p>
    <p>Correo: hipolito.melchor@issste.gob.mx</p>
    </div>

    <div id="logo_inferior">
    <img src= "../images/pdf/leona_vicario.jpg">
    </div>
</body>
</html>