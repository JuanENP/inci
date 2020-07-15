<?php 
ob_start();
    if (($_SESSION["name"]) && ($_SESSION["con"]))
	{
		$nombre=$_SESSION['name'];
		$contra=$_SESSION['con'];
        require("../Acceso/global.php");
        $hoy  =$_SESSION['dia_mes'];
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
	}
	else
	{
		header("Location: ../index.php");
		die();
	}
?>

<html lang="es">
    <head>
        <meta meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../assets/css/formato-retardo.css">
        <link rel="stylesheet" href="../assets/css/normalize.css" />
        <title>Document</title>
    </head>
    <body>
        <div>
        <img src= "../images/pdf/superior.jpg"  class="logo_superior">
        </div>
        <div id="p1">
        <b>
        <p>Dirección</p>
        <p>Subdirección Administrativa</p>
        <p>Coordinación de Mantenimiento</p>
        <p> OFICIO No. 043/140/CM/0107/2020</p>
        </b>
        <!-- <p>Emiliano Zapata, Mor., a 18 de marzo de 2020</p> -->
        <p><?php echo $municipio.', '.$abrev_estado.', a '.$hoy ?></p>
        <b>Asunto: Justificación de Retardo</b>
        <br> 
        </div>

        <div id="p2">
        <p><b>C. Carlos J.Gutierrez</b></p>
        <p><b>Coordinador de Recursos Humanos</b></p>
        <p><b>Presente</b></p></b>
        <br><br> <br> 
        </div>
        
        <div id="p2-2">
        <p>Me permito solicitar su valiosa intervención para que se le justifique el retardo del día <?php echo $fecha?>, del C. <b><?php echo $nombre?></b>, con No.
        de Empleado <b><?php echo $num?></b>, Categoría: <b><?php echo $des?></b>, para que no se afecte su salario y estímulos correspondientes. </p>
        <br>
        <p>Sin más por el momento, reciba un cordial saludo.</p>
        <br><br><br>
        </div>
        
        <div id="p3">
        
        <br><br><br>
        <h3>Atentamente</h3>
        <br> <br>
        <b>Ing. Hipolito Melchor Gutierrez</b><br>
        <b>Coordinador de Mantenimiento</b>
        <br><br><br>
        </div>
        
        <div id="p4">
        <b>c.c.Minutario</b>
        <b>HMG/mgg.</b>
        <br> 
        </div>

        <div id="p5">
        <p>Av. Universidad No.40, Col.Palo Escrito, Municipio de Emiliano Zapata, Morelos</p>
        <p>C.P. 62760, Teléfono: 01(777)10 11 400 extensión 40035</p>
        <p>Correo: hipolito.melchor@issste.gob.mx</p>
        </div>

        <div id="logo_inferior">
        <img src= "../images/pdf/inferior.jpg" class="logo_inferior">
        </div>
    </body>
</html>