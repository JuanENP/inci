<script type="text/javascript">
    function No_Existe(numero,fecha)
    {
        alert("No hay una incidencia en la fecha "+fecha+" para el número de trabajador "+numero);
        location.href="../../ht/aprobaciones.php";
    }
</script>

<script type="text/javascript">
    function Ya(numero,fecha)
    {
        alert("Esta incidencia ya fue justificada antes");
        location.href="../../ht/aprobaciones.php";
    }
</script>

<script type="text/javascript">
    function Correcto()
    {
        alert("Correcto");
        location.href="../../ht/aprobaciones.php";
    }
</script>

<script type="text/javascript">
    function Error()
    {
        alert("Algo salió mal");
        location.href="../../ht/aprobaciones.php";
    }
</script>

<script type="text/javascript">
    function no()
    {
        alert("Ya posee 2 justificaciones. Sustento: Art. 46 CGT");
        location.href="../../ht/aprobaciones.php";
        //window.close();
        //Si quieres usar instrucciones php, salte del script y coloca la apertura y cierre de php, escribe dentro de ellas de forma normal
    }
</script>

<?php
session_start();
    //******formatear a la zona horaria de la ciudad de México**********
    date_default_timezone_set('America/Mexico_City');
    require("../../Acceso/global.php");

    /*OBTENER LA QUINCENA ACTUAL EN LA QUE ESTAMOS*/
    $sql5="SELECT id from quincena where validez=1";
    $query5=mysqli_query($con, $sql5) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
    $resul5=mysqli_fetch_array($query5);
    $quincena=$resul5[0];
    /*FIN DE OBTENER QUINCENA ACTUAL*/
        
    $operacion=$_POST['opcion'];
    if($operacion=="justificar")
    {
        $num = $_POST['num'];
        $fecha=$_POST['fec'];
        $id_incidencia;//paara guardar el id de incidencia que me arroja sql

        //ver si existe esa incidencia
        $sql="  SELECT a.numero_trabajador, a.nombre, a.apellido_paterno, a.apellido_materno,f.entrada,b.fecha_entrada,f.salida,b.fecha_salida , b.quincena_id, b.id,c.id,c.clave_id,c.descripcion, f.turno
        FROM trabajador a
        INNER JOIN asistencia b on a.numero_trabajador = b.trabajador_Numero_trabajador and a.numero_trabajador='$num' and Cast(fecha_entrada As Date) ='$fecha'
        INNER JOIN quincena  x on b.quincena_id = x.id  and  b.quincena_id = 5
        INNER JOIN incidencia c on  b.id = c.asistencia_id  and (c.clave_id = 01 or c.clave_id = 02 or c.clave_id = 03) 
        INNER JOIN acceso e on a.acceso_idacceso = e.idacceso 
        INNER JOIN turno f on e.turno_turno = f.turno";

        $query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
        //obtener las filas del query
        $filas = mysqli_num_rows($query);
        //Si el query está vacío
        if($filas==0)
        {
            echo "<script> No_Existe($num,'$fecha'); </script>";
        }
        else
        {
            while($resul2=mysqli_fetch_array($query))
            {
                $id_asistencia=$resul2[9];
                $id_incidencia=$resul2[10];
            }
            /*Si el query tiene datos
            Ver si esa clave ya está justificada
            */
            $sql2="SELECT a.numero_trabajador, a.nombre, a.apellido_paterno, a.apellido_materno,f.entrada,b.fecha_entrada,f.salida,b.fecha_salida , b.quincena_id,c.clave_id,d.clave_justificacion_id, f.turno,b.id,c.asistencia_id, c.id,d.incidencia_id
            FROM trabajador a
            INNER JOIN asistencia b on a.numero_trabajador = b.trabajador_Numero_trabajador and a.numero_trabajador = '$num' and b.id=$id_asistencia
            INNER JOIN incidencia c on  b.id = c.asistencia_id and b.quincena_id = 5
            INNER JOIN justificacion d on c.id = d.incidencia_id and d.clave_justificacion_id= 09
            INNER JOIN acceso e on a.acceso_idacceso = e.idacceso
            INNER JOIN turno f on e.turno_turno = f.turno";
            $query2= mysqli_query($con, $sql2) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
            //obtener las filas del query2
            $filas2= mysqli_num_rows($query2);
            if($filas2==0)
            {
                //contamos cuántas 09 posee el empleado en la tabla justificaciones
                $sql3="SELECT count(d.clave_justificacion_id)
                FROM trabajador a
                INNER JOIN asistencia b on a.numero_trabajador = b.trabajador_Numero_trabajador and a.numero_trabajador = '$num' 
                INNER JOIN incidencia c on  b.id = c.asistencia_id and b.quincena_id = 5
                INNER JOIN justificacion d on c.id = d.incidencia_id and d.clave_justificacion_id= 09
                INNER JOIN acceso e on a.acceso_idacceso = e.idacceso
                INNER JOIN turno f on e.turno_turno = f.turno";
                $query3= mysqli_query($con, $sql3) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
                $resul3=mysqli_fetch_array($query3);
                $total=$resul3[0];
                //si el total de 09 es menor a 2 (significa que aún puede ingresar justificación)
                if($total<2)
                {
                    //Si el sql2 no posee datos significa que esa incidencia no ha sido justificada y la podemos justificar
                    //obtener la fecha de hoy
                    $fec_act=date("Y-m-d H:i:s"); 
                    $sql4="INSERT INTO justificacion VALUES (NULL, '$fec_act', $id_incidencia, '09')";
                    if((mysqli_query($con, $sql4) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)))))
                    {
                        echo "<script> Correcto(); </script>";
                    }
                    else
                    {
                        die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
                    }
                }
                else
                {
                    //Ya posee dos justificaciones en la quincena
                    echo "<script> no(); </script>";
                }     
            }
            else
            {
                /*Si el sql2 SI posee datos significa que esa incidencia YA ha sido justificada y no se puede justificar
                dos veces la misma incidencia NUNCA
                */
                echo "<script> Ya(); </script>";
            }
        }
        mysqli_close($con);
    }//FIN DEL IF JUSTIFICAR

    if($operacion=="omision")
    {
        $num = $_POST['num'];
        $fecha=$_POST['fec'];
        echo "OMISIÓN";
        echo "$quincena";
        /*2 omisiones por quincena o 
        una omisión + 1 retardo o 
        2 retardos (Art. 46 CGT)

        just omisi=2
        just retar=0
        
        total=2
        */
        //contamos cuántas 09 (retardos justificados) posee el empleado en la tabla justificaciones
        $sql6="SELECT count(d.clave_justificacion_id)
        FROM trabajador a
        INNER JOIN asistencia b on a.numero_trabajador = b.trabajador_Numero_trabajador and a.numero_trabajador = '$num' 
        INNER JOIN incidencia c on  b.id = c.asistencia_id and b.quincena_id = 5
        INNER JOIN justificacion d on c.id = d.incidencia_id and d.clave_justificacion_id= 09
        INNER JOIN acceso e on a.acceso_idacceso = e.idacceso
        INNER JOIN turno f on e.turno_turno = f.turno";
        $query6= mysqli_query($con, $sql6) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
        $resul6=mysqli_fetch_array($query6);
        $totalRetardos=$resul6[0];

        //contamos cuántas 08 (omisiones justificadas) posee el empleado en la tabla justificaciones
        
    }//FIN DEL IF OMISIÓN

    if($operacion=="comision")
    {
        echo "comisiones";
        /*numero
            fecha inicio
            fecha de fin

            validez
        */
        $num = $_POST['num'];//el número del trabajador
        $fecha=$_POST['fec'];//la fecha de inicio
        $fechaf=$_POST['fecf'];//la fecha de fin
        $hora_e=$_POST['he'];
        $hora_s=$_POST['hs'];
        $clave_especial=45;
        /*la validez siempre se debe de buscar si es 0 o 1 dependiendo de las fechas de inicio y fin*/
        $validez=0;
        //echo $num . ". feini: " . $fecha . ". fechafin: " . $fechaf . ". hora en: " . $hora_e . ". hora sal: " . $hora_s;
        
    }
?>