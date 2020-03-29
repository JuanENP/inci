
<?php 
    ob_start();
    $num  =$_SESSION['num'];
    $nom  =$_SESSION['nom'];
    $a_pat=$_SESSION['a_pat']; 
    $a_mat=$_SESSION['a_mat'];  
    $cat  =$_SESSION['cat'];  
    $des  =$_SESSION['des']; 
    $nombre=$nom." ".$a_pat. " ".$a_mat;
    require("../Acceso/global.php");
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
    
    <p>Emiliano Zapata, Mor., a 18 de marzo de 2020</p>
    <b>Asunto: Justificación de Retardo</b>
    <br> 
    </div>

    <div id="p2">
    <b><p>C. Carlos J.Gutierrez</p>
    <p>Coordinador de Recursos Humanos</p>
    <p>Presente</p></b>
    <br><br> <br> 
    </div>
    <div id="p2-2">
    <p>Me permito solicitar su valiosa intervención para que se le justifique el retardo del día 17 de marzo 2020, del C. <b><?php echo $nombre?></b>, con No.
    de Empleado <?php echo $num?>,Categoría: <b><?php echo $des?></b>, para que no se afecte su salario y estímulos correspondientes. </p>
    <br>
    <p>Sin más por el momento, reciba un cordial saludo.</p>
    <br><br><br>
    </div>
    
    <div id="p3">
    
    <br> <br> <br>
    <h3>Atentamente</h3>
    <br> <br>
    <b>Ing. Hipolito Melchor Gutierrez</b>
    <b>Coordinador de Mantenimiento</b>
    <br> <br> <br><br> <br>
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