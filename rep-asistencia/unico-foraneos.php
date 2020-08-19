<?php 
ob_start();
    if (($_SESSION["name"]) && ($_SESSION["con"]))
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        require("../Acceso/global.php"); 
        $fecha=$_SESSION['fecha'];
        $quincena=$_SESSION['quincena'];
        $anio=$_SESSION['anio'];
        if($_SESSION['c']>0)
        {
            //---Informacion del hospital------//
            $clave=$_SESSION['clave'];
            $nombre=$_SESSION['nombre'];
            $descripcion=$_SESSION['descripcion'];
            //--------------------------------//
            $contador=$_SESSION['c'];
            $reporte=$_SESSION['rep'];
            $f_ini=$_SESSION['f_ini'];
            $f_fin=$_SESSION['f_fin'];
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
        <title>Reporte Unico de incidencias</title>
    </head>
    <body>
        <div class="borde_superior">
            <div>
                <img src= "../images/pdf/logo-cuadrado.jpg" class="logo_superior">
            </div>
            <div id="p1">
                <p><?php echo$nombre?></p>
                <p>COORDINACION DE RECURSOS HUMANOS</p>
                <p>JEFATURA DE INCIDENCIAS</p>
            </div> 
        </div>
        <div id="p2">
            <p>REPORTE ÚNICO QUINCENAL DE INCIDENCIAS COMISIONADOS FORÁNEOS</p>
        </div>
        <div id="p3">
            <p>CLAVE DE ADSCRIPCIÓN: <u> <?php echo$clave?> </u>   DESCRIPCIÓN: <u> <?php echo$descripcion ?> </u></p>    
            <p>FECHA: <u> <?php echo$fecha?>   </u>  AÑO: <u> <?php echo$anio ?>  </u> QUINCENA: <u> <?php echo $quincena?> </u> </p>  
            <!-- <p>PERÍODO: <u><?php  ' DEL '.$f_ini.' AL ' .$f_fin ?> </u></p> -->
        </div>
        <table class="tabla_datos">
            <thead>
                <tr>
                    <td>N.E.</td>
                    <td>NOMBRE</td>
                    <td>CLAVE</td>
                    <td>DIAS</td>
                    <td>T. DIAS</td>
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
                        $t_dias=count(explode(",",$reporte[$i][3]));
                        echo "<td>".$t_dias."</td>";
                        echo"</tr>";
                    }
                ?>
            </tbody>
        </table>
    </body>
</html>