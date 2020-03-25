<script type="text/javascript">
    function No_Existe(numero,fecha)
    {
        alert("No hay una incidencia en la fecha "+fecha+" para el número de trabajador "+numero);
        location.href="../../ht/aprobaciones.php";
    }

    function Ya(numero,fecha)
    {
        alert("Esta incidencia ya fue justificada antes");
        location.href="../../ht/aprobaciones.php";
    }

    function Correcto()
    {
        alert("Justificacion agregada Correctamente");
        location.href="../../ht/aprobaciones.php";
    }

    function Error()
    {
        alert("Algo salió mal");
        location.href="../../ht/aprobaciones.php";
    }

    function no()
    {
        alert("Ya posee 2 justificaciones o  2 omisiones o 1 omisión+ 1 justificación. Sustento: Art. 46 CGT");
        location.href="../../ht/aprobaciones.php";
        //window.close();
    }

    function noMaxComision(fecha1, fecha2)
    {
        alert("El periodo entre las fechas "+fecha1+" y "+fecha2+" es superior a 5 meses y medio. NO ES POSIBLE TENER UNA COMISIÓN QUE DURE ESE TIEMPO.");
        location.href="../../ht/aprobaciones.php";
    }

    function noComision(numero)
    {
        alert("El trabajador con número "+numero+ " Ya posee una comisión activa. NO ES POSIBLE TENER 2 COMISIONES A LA VEZ");
        location.href="../../ht/aprobaciones.php";
    }

    function siComision()
    {
        alert("la comisión se agregó correctamente");
        location.href="../../ht/aprobaciones.php";
    }

    function noOmision()
    {
        alert("Ya posee 2 omisiones o 2 faltas o 1 omisión + 1 justifiación");
        location.href="../../ht/aprobaciones.php";
    }

    function antesOmision()
    {
        alert("Esta omisión ya fue justificada antes.");
        location.href="../../ht/aprobaciones.php";
    }

    function omisionNoExiste(numero,fecha)
    {
        alert("No hay una omisión en la fecha "+fecha+" para el número de trabajador "+numero);
        location.href="../../ht/aprobaciones.php";
    }

    function omisionCorrecta()
    {
        alert("Omision justificada correctamente.");
        location.href="../../ht/aprobaciones.php";
    }

</script>

<?php
session_start();
    //******formatear a la zona horaria de la ciudad de México**********
    date_default_timezone_set('America/Mexico_City');
    require("../../Acceso/global.php");

    //obtener la fecha de hoy
    $fec_act=date("Y-m-d H:i:s"); 
    
    /*OBTENER LA QUINCENA ACTUAL EN LA QUE ESTAMOS*/
    $sql5="SELECT idquincena from quincena where validez=1";
    $query5=mysqli_query($con, $sql5) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
    $resul5=mysqli_fetch_array($query5);
    $quincena=$resul5[0];
    /*FIN DE OBTENER QUINCENA ACTUAL*/
        
    $operacion=$_POST['opcion'];
    if($operacion=="justificar")
    {
        $num = $_POST['num'];
        $fecha=$_POST['fec'];
        $id_incidencia;//para guardar el id de incidencia que me arroja sql

        //ver si existe esa incidencia
        $sql="SELECT a.numero_trabajador, a.nombre, a.apellido_paterno, a.apellido_materno,f.entrada,b.fecha_entrada,f.salida,b.fecha_salida , b.quincena_quincena, b.id,c.idincidencia,c.clave_incidencia_clave_incidencia,c.descripcion, f.idturno
        FROM trabajador a
        INNER JOIN asistencia b on a.numero_trabajador = b.trabajador_trabajador and a.numero_trabajador='$num' and Cast(fecha_entrada As Date) ='$fecha'
        INNER JOIN quincena  x on b.quincena_quincena = x.idquincena  and  b.quincena_quincena = 5
        INNER JOIN incidencia c on  b.id = c.asistencia_asistencia  and (c.clave_incidencia_clave_incidencia = 01 or c.clave_incidencia_clave_incidencia = 02 or c.clave_incidencia_clave_incidencia = 03) 
        INNER JOIN acceso e on a.numero_trabajador=e.trabajador_trabajador
        INNER JOIN turno f on e.turno_turno = f.idturno";

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
            $sql2="SELECT a.numero_trabajador, a.nombre, a.apellido_paterno, a.apellido_materno,f.entrada,b.fecha_entrada,f.salida,b.fecha_salida , b.quincena_quincena,c.clave_incidencia_clave_incidencia,d.clave_justificacion_clave_justificacion, f.idturno,b.id,c.asistencia_asistencia, c.idincidencia,d.incidencia_incidencia
            FROM trabajador a
            INNER JOIN asistencia b on a.numero_trabajador = b.trabajador_trabajador and a.numero_trabajador = '$num' and b.id=$id_asistencia
            INNER JOIN incidencia c on  b.id = c.asistencia_asistencia and b.quincena_quincena = 5
            INNER JOIN justificacion d on c.idincidencia = d.incidencia_incidencia and d.clave_justificacion_clave_justificacion= 09
            INNER JOIN acceso e on a.numero_trabajador=e.trabajador_trabajador
            INNER JOIN turno f on e.turno_turno = f.idturno";
            $query2= mysqli_query($con, $sql2) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
            //obtener las filas del query2
            $filas2= mysqli_num_rows($query2);
            if($filas2==0)
            {
                //contamos cuántas 09 posee el empleado en la tabla justificaciones
                $sql3="SELECT count(d.clave_justificacion_clave_justificacion)
                FROM trabajador a
                INNER JOIN asistencia b on a.numero_trabajador = b.trabajador_trabajador and a.numero_trabajador = '$num' 
                INNER JOIN incidencia c on  b.id = c.asistencia_asistencia and b.quincena_quincena = 5
                INNER JOIN justificacion d on c.idincidencia = d.incidencia_incidencia and d.clave_justificacion_clave_justificacion= 09
                INNER JOIN acceso e on a.numero_trabajador=e.trabajador_trabajador
                INNER JOIN turno f on e.turno_turno = f.idturno";
                $query3= mysqli_query($con, $sql3) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
                $resul3=mysqli_fetch_array($query3);
                $total=$resul3[0];

                //contamos cuántas 08 (omisiones justificadas) posee el empleado en la tabla justificaciones de incidencia
                $sql11="SELECT count(d.clave_justificacion_clave_justificacion) FROM trabajador a
                INNER JOIN asistencia b on a.numero_trabajador = b.trabajador_trabajador and a.numero_trabajador = $num 
                INNER JOIN incidencia c on  b.id = c.asistencia_asistencia and b.quincena_quincena = 5
                INNER JOIN justificacion d on c.idincidencia = d.incidencia_incidencia and d.clave_justificacion_clave_justificacion= 08
                INNER JOIN acceso e on a.numero_trabajador=e.trabajador_trabajador
                INNER JOIN turno f on e.turno_turno = f.idturno";  
                $query11= mysqli_query($con, $sql11) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
                $resul11=mysqli_fetch_array($query11);
                $totalOmisionesIn=$resul11[0];

                //contamos cuántas 08 (omisiones justificadas) posee el empleado en la tabla justificacion_omision
                $sql12="SELECT count(a.numero_trabajador) FROM trabajador a
                INNER JOIN omision b on a.numero_trabajador = b.trabajador_trabajador where a.numero_trabajador=$num and b.quincena = 5";
                $query12= mysqli_query($con, $sql12) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
                $resul12=mysqli_fetch_array($query12);
                $totalOmision2=$resul12[0]; 

                //sumar los totales y revisar que sean menores a dos
                $total_just_omis=$total+$totalOmisionesIn+$totalOmision2;

                //si el total de 09 y justifiaciones de omision es menor a 2 (significa que aún puede ingresar justificación)
                if($total_just_omis<2)
                {
                    //Si el sql2 no posee datos significa que esa incidencia no ha sido justificada y la podemos justificar
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
        /*2 omisiones por quincena o 
        una omisión + 1 retardo o 
        2 retardos (Art. 46 CGT)
        */
        //contamos cuántas 09 (retardos justificados) posee el empleado en la tabla justificaciones
        $sql6="SELECT count(d.clave_justificacion_clave_justificacion)
        FROM trabajador a
        INNER JOIN asistencia b on a.numero_trabajador = b.trabajador_trabajador and a.numero_trabajador = '$num' 
        INNER JOIN incidencia c on  b.id = c.asistencia_asistencia and b.quincena_quincena = 5
        INNER JOIN justificacion d on c.idincidencia = d.incidencia_incidencia and d.clave_justificacion_clave_justificacion= 09
        INNER JOIN acceso e on a.numero_trabajador=e.trabajador_trabajador
        INNER JOIN turno f on e.turno_turno = f.idturno";
        $query6= mysqli_query($con, $sql6) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
        $resul6=mysqli_fetch_array($query6);
        $totalRetardos=$resul6[0];

        //contamos cuántas 08 (omisiones justificadas) posee el empleado en la tabla justificaciones de incidencia
        $sql9="SELECT count(d.clave_justificacion_clave_justificacion) FROM trabajador a
        INNER JOIN asistencia b on a.numero_trabajador = b.trabajador_trabajador and a.numero_trabajador = $num 
        INNER JOIN incidencia c on  b.id = c.asistencia_asistencia and b.quincena_quincena = 5
        INNER JOIN justificacion d on c.idincidencia = d.incidencia_incidencia and d.clave_justificacion_clave_justificacion= 08
        INNER JOIN acceso e on a.numero_trabajador=e.trabajador_trabajador
        INNER JOIN turno f on e.turno_turno = f.idturno";  
        $query9= mysqli_query($con, $sql9) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
        $resul9=mysqli_fetch_array($query9);
        $totalOmisionesIncidencia=$resul9[0];

        //hacer la suma total de omisiones y justificaciones de retardos
        $totalOm=$totalRetardos+$totalOmisionesIncidencia;

        //si totalOm es menor que 2 significa que aún puede justificar su omisión, pero antes se debe buscar la omisión a justificar
        if($totalOm<2)
        {
            /*Ahora revisar si existe la omisión en la tabla incidencias; */
            $sql13="SELECT c.idincidencia  FROM trabajador a
            INNER JOIN asistencia b on a.numero_trabajador = b.trabajador_trabajador and a.numero_trabajador = $num and CAST(b.fecha_entrada AS DATE) >= '$fecha'
            INNER JOIN incidencia c on  b.id = c.asistencia_asistencia and b.quincena_quincena = 5 and c.clave_incidencia_clave_incidencia=18 
            INNER JOIN acceso e on a.numero_trabajador=e.trabajador_trabajador
            INNER JOIN turno f on e.turno_turno = f.idturno";
            
            $query13= mysqli_query($con, $sql13) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
            //obtener las filas del query
            $filas13 = mysqli_num_rows($query13);
            //Si el query está vacío significa que no existe esa omisión en la tabla incidencias
            if($filas13==0)
            {
                echo "<script> omisionNoExiste($num,'$fecha'); </script>";              
            }
            else
            {
                $resul13=mysqli_fetch_array($query13);
                $idomisionEncontrada=$resul13[0];//el id de la omision encontrada en el query13
                //ver si esa omisión ya está justificada
                $sql15="SELECT d.clave_justificacion_clave_justificacion FROM trabajador a
                INNER JOIN asistencia b on a.numero_trabajador = b.trabajador_trabajador and a.numero_trabajador = $num 
                INNER JOIN incidencia c on  b.id = c.asistencia_asistencia and b.quincena_quincena = 5
                INNER JOIN justificacion d on c.idincidencia = d.incidencia_incidencia and d.clave_justificacion_clave_justificacion= 08 and c.idincidencia = $idomisionEncontrada
                INNER JOIN acceso e on a.numero_trabajador=e.trabajador_trabajador";
                $query15= mysqli_query($con, $sql15) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
                //obtener las filas del query
                $filas15 = mysqli_num_rows($query15);
                //Si el query está vacío significa que no se ha justificado la omision, y se puede justificar
                if($filas15==0)
                {
                    //justificar omision
                    $sql16="INSERT INTO justificacion VALUES (NULL, '$fec_act', '$idomisionEncontrada', '08')";
                    if((mysqli_query($con, $sql16) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)))))
                    {
                        echo "<script> omisionCorrecta(); </script>";
                    }
                    else
                    {
                        die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
                    }
                }
                else
                {
                    //Esta omision ya fue justificada antes
                    echo "<script> antesOmision(); </script>";
                }
            }
        }
        else
        {
            //ya posee dos justificaciones o dos omisiones o 1 justificacion + 1 omision
            echo "<script> no(); </script>";
        }
    }//FIN DEL IF OMISIÓN

    if($operacion=="comision")
    {//comisión es la clave 17
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

        $date1= new DateTime($fecha);
        $date2= new DateTime($fechaf);
        //echo $num . ". feini: " . $fecha . ". fechafin: " . $fechaf . ". hora en: " . $hora_e . ". hora sal: " . $hora_s;
        /*Ver si ese empleado ya posee una comisión*/
        $sql7="SELECT * from especial where trabajador_trabajador=$num and validez=1 and clave_especial_clave_especial=89";
        $query7=mysqli_query($con, $sql7) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
        $resul7=mysqli_fetch_array($query7);
        $filas7= mysqli_num_rows($query7);
        /*Si el total de filas es 0 significa que el empleado no posee una comisión activa*/
        if($filas7==0)
        {
            //antes se debe verificar si se tuvo una comisión en en los últimos 6 meses

            //insertar la comisión
            $interval = $date1->diff($date2);
            $totDias=$interval->format('%a');//los días que durará la comisión
            //si el periodo de comisión es superior a 165 días (5 meses y medio)
            if($totDias>165)
            {
                echo "<script> noMaxComision('$fecha','$fechaf'); </script>";
            }
            else
            {
                //Insertar la comisión
                $sql8=" INSERT INTO especial VALUES (null, '$fecha', '$fechaf', '$hora_e', '$hora_s', '1', '$num', '89')";
                if((mysqli_query($con, $sql8) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)))))
                {
                    echo "<script> siComision(); </script>";
                }
                else
                {
                    die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
                }
            }
        }
        else
        {
            //El empleado ya posee una comisión activa y no puede tener 2 comisiones a la vez
            echo "<script> noComision($num); </script>";
        }

    }//FIN DEL IF COMISIÓN
?>