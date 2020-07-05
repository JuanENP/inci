<?php 
ob_start();
    if (($_SESSION["name"]) && ($_SESSION["con"]))
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        require("../Acceso/global.php"); 
        $fecha=$_SESSION['fecha'];
        if($_SESSION['contar']>0)
        {
            //---Informacion del hospital------//
            $clave=$_SESSION['clave'];
            $nombre=$_SESSION['nombre'];
            $descripcion=$_SESSION['descripcion'];
            //--------------------------------//
            $contador=$_SESSION['contar'];
            $reporte=$_SESSION['dato'];   
        }
        
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
        <link rel="stylesheet" href="../assets/css/formato-reportes.css">
        <link rel="stylesheet" href="../assets/css/reportes.css">
        <link rel="stylesheet" href="../assets/css/normalize.css" />
        <title>Reporte de asistencia</title>
    </head>
    <body>
        <div class="borde_superior">
            <div>
                <img src= "../images/pdf/logo-cuadrado.jpg" class="logo_superior">
            </div>
            <div id="p1">
                <p><?php echo$nombre?></p>
                <p>COORDINACIÓN DE RECURSOS HUMANOS</p>
                <p>JEFATURA DE INCIDENCIAS</p> 
            </div> 
        </div>
        <div id="p2">
            <p>LISTADO DEL PERSONAL QUE DEBE ASISTIR EL DÍA <?php echo$fecha ?></p>
        </div>
        <div id="p3">  
            <p>CLAVE DE ADSCRIPCIÓN: <u> <?php echo$clave?> </u>   DESCRIPCIÓN: <u> <?php echo$descripcion ?> </u></p>    

        </div>
        <table class="tabla_datos">
            <thead>
                <tr>
                    <td>N.E.</td>
                    <td>NOMBRE</td>
                    <td>DEPTO</td>
                    <td>CATEGORÍA</td>
                    <td>TURNO</td>
                    <td>ENTRADA</td>
                    <td>SALIDA</td>
                </tr>
            </thead>
            <tbody>
                
                <?php        
                    for($i=0;$i<$contador;$i++)
                    {   echo"<tr>";
                        echo "<td>".$reporte[$i][0]."</td>";
                        echo "<td>".$reporte[$i][1]."</td>";
                        echo "<td>".$reporte[$i][2]."</td>";
                        echo "<td>".$reporte[$i][3]."</td>";
                        echo "<td>".$reporte[$i][4]."</td>";
                        echo "<td>".$reporte[$i][5]."</td>";
                        echo "<td>".$reporte[$i][6]."</td>";
                        echo"</tr>";
                    }
                ?>
            </tbody>
        </table>
    </body>
</html>