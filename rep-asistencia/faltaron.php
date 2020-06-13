<?php 
ob_start();
    if (($_SESSION["name"]) && ($_SESSION["con"]))
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        require("../Acceso/global.php"); 
        $fecha=$_SESSION['fecha'];
        $anio=$_SESSION['anio'];
        $tipo=$_SESSION['tipo'];
        if($_SESSION['c_d']>0)
        {
            //---Informacion del hospital------//
            $clave=$_SESSION['clave'];
            $nombre=$_SESSION['nombre'];
            $descripcion=$_SESSION['descripcion'];
            //--------------------------------//
            $contador=$_SESSION['c_d'];
            $reporte=$_SESSION['datos'];
            if(!empty($_SESSION['f_ini']))
            {
                $f_ini=$_SESSION['f_ini'];
            }
            if(!empty($_SESSION['f_fin']))
            {
                $f_fin=$_SESSION['f_fin'];
            }
 
            
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
        <link rel="stylesheet" href="../assets/css/formato-reportes.css">
        <link rel="stylesheet" href="../assets/css/reportes.css">
        <link rel="stylesheet" href="../assets/css/normalize.css" />
        <title>Reporte de incidencias</title>
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
     
            <p>REPORTE DE <?php echo $tipo.' '.$f_ini.' AL ' .$f_fin?></p>

        </div>
        <div id="p3">
            <p>CLAVE DE ADSCRIPCIÓN: <u> <?php echo$clave?> </u>   DESCRIPCIÓN: <u> <?php echo$descripcion ?> </u></p>    
            <p>FECHA: <u> <?php echo$fecha?>   </u>  AÑO: <u> <?php echo$anio ?></p>   

        </div>
        <table class="tabla_datos">
            <thead>
                <tr>
                    <td>N.E.</td>
                    <td>NOMBRE</td>
                    <td>DEPTO</td>
                    <td>CATEGORÍA</td>
                    <td>FECHA</td>
                </tr>
            </thead>
            <tbody>
                <?php        

                   for($i=0;$i<$contador;$i++)
                    {  
                        echo"<tr>";
                        echo "<td>".$reporte[$i][0]."</td>";
                        echo "<td>".$reporte[$i][1]."</td>";
                        echo "<td>".$reporte[$i][2]."</td>";
                        echo "<td>".$reporte[$i][3]."</td>";
                        echo "<td>".$reporte[$i][4]."</td>";
                        echo"</tr>";
                    }
                ?>
            </tbody>
        </table>
    </body>
</html>