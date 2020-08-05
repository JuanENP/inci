<?php
    date_default_timezone_set('America/Mexico_City');
    set_time_limit(600);//Indica que son 600 segundos, es decir 10 minutos máximo para ejecutar todo el script 
    $nombre="biometric";
    $contra="5_w**/pQxcmk.";
    require("../Acceso/global.php");
    
    $anio=date("Y");//solo el año actual
    $datos=array();
    $fila=array();
    $sql="SELECT idespecial, fecha_inicio, fecha_fin, validez from especial where (year(fecha_inicio)=$anio or year(fecha_fin)=$anio)";
    $query= mysqli_query($con, $sql);
    if($query)
    {
        $filas=mysqli_num_rows($query);
        if($filas>0)
        {
            $today=date("Y-m-d");
            $fecha_hoy=strtotime($today);
            $pos=0;

            while($nfila=mysqli_fetch_array($query))
            {
                $fila[0]=$nfila[0];
                $fila[1]=$nfila[1];
                $fila[2]=$nfila[2];
                $fila[3]=$nfila[3];
                $datos[$pos]=$fila;
                $pos++;
            }
            //fin while
        }
        //fin if filas
    }
    //fin if query
    $tam=count($datos);

    /*Evaluar cada par de fechas de cada fila (recorreremos el array) para saber si están o no con validez 1 */
    for($i=0;$i<$tam;$i++)
    {
        $rango=RevisarFechas($datos[$i][1],$datos[$i][2]);
        if($rango==1)
        {
            if($datos[$i][3]==0)
            {
                //Actualizar la validez dependiendo del valor que tenga
                updateEspecial(1,$datos[$i][0]);
            }
        }
        else
        {
            if($datos[$i][3]==1)
            {
                //Actualizar la validez dependiendo del valor que tenga
                updateEspecial(0,$datos[$i][0]);
            }
        }
    }
    
    function RevisarFechas($fechaInicio, $fechaFinal)
    {
        /*
            revisa si la fecha actual está dentro de la fecha de inicio y fecha final 
            return 0 indica que la validez será 0
            return 1 indica que la validez será 1    
        */
        global $fecha_hoy;
        $fecha_in = strtotime($fechaInicio);
        $fecha_fi = strtotime($fechaFinal);
        
        if($fecha_hoy>$fecha_in && $fecha_hoy>$fecha_fi)
        {
            return 0;
        }

        if($fecha_hoy>=$fecha_in && $fecha_hoy<=$fecha_fi)
        {
            return 1;
        }
    }//fin de RevisarFechas

    function updateEspecial($validez,$idEspecial)
    {
        global $con;
        $sql="UPDATE especial SET validez = '$validez' WHERE (idespecial = '$idEspecial')";
        $query=mysqli_query($con, $sql) or die();
    }
?>