<?php 
    ob_start();
    if (($_SESSION["name"]) && ($_SESSION["con"]))
	{
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
        require("../Acceso/global.php");
        $dia_mes  =$_SESSION['dia_mes'];
        $municipio =$_SESSION['municipio'];
        $estado=$_SESSION['estado']; 
        $abrev_estado=$_SESSION['abrevia_estado'];
        $fecha  =$_SESSION['fecha']; 
        $num  =$_SESSION['num'];
        $nom  =$_SESSION['nom'];
        $a_pat=$_SESSION['a_pat']; 
        $a_mat=$_SESSION['a_mat'];  
        $cat  =$_SESSION['cat'];  
        $des  =$_SESSION['des']; 
        $nombre=$nom." ".$a_pat. " ".$a_mat;
        $operacion =$_SESSION['operacion'];
        if($operacion==1)
        {
            $omision="entrada";
        }
        if($operacion==2)
        {
            $omision="salida";
        }
    }
	else
	{
		header("Location: ../index.html");
		die();
	}
    

?>

<html lang="es">
<head>
    <meta meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/formato-omision.css">
    <link rel="stylesheet" href="../assets/css/normalize.css" />
    <title>Document</title>
</head>
<body>
    <div >
    <img src= "../images/pdf/superior.jpg" class="logo_superior">
    </div>
    <div id="p1">
    <p>Dirección</p>
    <p>Subdirección Administrativa</p>
    <p>Coordinación de Mantenimiento</p>
    <br>
    <b>OFICIO No. 043/140/CM/0107/2020</b>
    <!-- <p>Emiliano Zapata, Mor., a 18 de marzo de 2020</p> -->
    <p><?php echo $municipio.', '.$abrev_estado.', a '.$dia_mes?></p>
    <b>Asunto: Justificación de Omisión de <?php echo $omision?></b>
    <br> 
    </div>

    <div id="p2">
    <b>
    <p>C. Carlos J.Gutierrez</p>
    <p>Coordinador de Recursos Humanos</p>
    <p>Presente</p>
    </b>
    <br> 
    </div>
    <div id="p2-2">
    <p>Me permito solicitar su valiosa intervención para que le justifique la omisión de entrada del día <?php echo $fecha?>, del C. <b><?php echo $nombre?></b>, 
    Categoría: <b><?php echo $des?></b>, No. Empleado <b><?php echo $num?></b>, en virtud de que no se afecte su salario y estímulos correspondientes. 
    <br><br> 
    Agradeciendo de antemano su atención al respecto, aprovecho la ocasión para enviarle un cordial saludo.</p>
    <br><br>
    </di>
    
    <div id="p3">
    <h3>Atentamente</h3>
    <br><br>
    <p><b>Ing. Hipolito Melchor Gutierrez</b></p>
    <p><b>Coordinador de Mantenimiento</b></p>
    <br><br>
    </div>
    
    <div id="p4">
    <p><b>c.c.Minutario</b></p>
    <p><b>HMG/vmq.</b></p>
    <br>
    </div>

    <div id="p5">
    <p>Av. Universidad No.40, Col.Palo Escrito, Municipio de Emiliano Zapata, Morelos</p>
    <p>C.P. 62760, Teléfono: 01(777)10 11 400 extensión 40035</p>
    <p>Correo: hipolito.melchor@issste.gob.mx</p>
    </div>

    <div>
    <img src= "../images/pdf/inferior.jpg" class="logo_inferior">
    </div>
</body>
</html>