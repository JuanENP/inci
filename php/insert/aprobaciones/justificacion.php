<?php
    if ((!empty($_POST["num"])) && (!empty($_POST["fec"])))
    {
        $num = $_POST['num'];
        $fecha=$_POST['fec'];
        $id_incidencia;//para guardar el id de incidencia que me arroja sql
        //ver si existe esa incidencia
        $sql="SELECT a.numero_trabajador, a.nombre, a.apellido_paterno, a.apellido_materno,f.entrada,b.fecha_entrada,f.salida,b.fecha_salida , b.quincena_quincena, b.id,c.idincidencia,c.clave_incidencia_clave_incidencia,c.descripcion, f.idturno
        FROM trabajador a
        INNER JOIN asistencia b on a.numero_trabajador = b.trabajador_trabajador and a.numero_trabajador='$num' and Cast(fecha_entrada As Date) ='$fecha'
        INNER JOIN quincena  x on b.quincena_quincena = x.idquincena  and  b.quincena_quincena = $quincena
        INNER JOIN incidencia c on  b.id = c.asistencia_asistencia  and (c.clave_incidencia_clave_incidencia = 01 or c.clave_incidencia_clave_incidencia = 02 or c.clave_incidencia_clave_incidencia = 03) 
        INNER JOIN acceso e on a.numero_trabajador=e.trabajador_trabajador
        INNER JOIN turno f on e.turno_turno = f.idturno";
        
        //obtener las filas del query
        $filas = obtenerFilas($sql);
        //Si el query está vacío
        if($filas==0)
        {
            mysqli_close($con);
            echo "<script> imprime('No hay una incidencia en la fecha $fecha para el número de trabajador $num en la QUINCENA ACTUAL'); </script>";  
        }
        else
        {   $query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
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
            INNER JOIN incidencia c on  b.id = c.asistencia_asistencia and b.quincena_quincena = $quincena
            INNER JOIN justificacion d on c.idincidencia = d.incidencia_incidencia and d.clave_justificacion_clave_justificacion= 09
            INNER JOIN acceso e on a.numero_trabajador=e.trabajador_trabajador
            INNER JOIN turno f on e.turno_turno = f.idturno";
            
            //obtener las filas del query2
            $filas2= obtenerFilas($sql2);
            if($filas2==0)
            {
                //contamos cuántas 09 posee el empleado en la tabla justificaciones
                $sql3="SELECT count(d.clave_justificacion_clave_justificacion)
                FROM trabajador a
                INNER JOIN asistencia b on a.numero_trabajador = b.trabajador_trabajador and a.numero_trabajador = '$num' 
                INNER JOIN incidencia c on  b.id = c.asistencia_asistencia and b.quincena_quincena = $quincena
                INNER JOIN justificacion d on c.idincidencia = d.incidencia_incidencia and fecha_inicio like '$anio' and d.clave_justificacion_clave_justificacion= 09
                INNER JOIN acceso e on a.numero_trabajador=e.trabajador_trabajador
                INNER JOIN turno f on e.turno_turno = f.idturno";
                $total=retornaAlgoDeBD(0,$sql3);
                //contamos cuántas 08 (omisiones justificadas) posee el empleado en la tabla justificacion
                $sql11="SELECT count(d.clave_justificacion_clave_justificacion) FROM trabajador a
                INNER JOIN asistencia b on a.numero_trabajador = b.trabajador_trabajador and a.numero_trabajador = $num 
                INNER JOIN incidencia c on  b.id = c.asistencia_asistencia and b.quincena_quincena = $quincena
                INNER JOIN justificacion d on c.idincidencia = d.incidencia_incidencia and fecha_inicio like '$anio' and d.clave_justificacion_clave_justificacion= 08
                INNER JOIN acceso e on a.numero_trabajador=e.trabajador_trabajador
                INNER JOIN turno f on e.turno_turno = f.idturno";  
                $totalOmisionesIn=retornaAlgoDeBD(0,$sql11);
                //sumar los totales y revisar que sean menores a dos
                $total_just_omis=$total+$totalOmisionesIn;
                //si el total de 09 y justifiaciones de omision es menor a 2 (significa que aún puede ingresar justificación)
                if($total_just_omis<2)
                {
                    //Si el sql2 no posee datos significa que esa incidencia no ha sido justificada y la podemos justificar
                    $sql4="INSERT INTO justificacion VALUES (NULL, '$fec_act', $id_incidencia, '09')";
                    if((mysqli_query($con, $sql4) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)))))
                    {
                        mysqli_close($con);
                        echo "<script> imprime('Justificación agregada correctamente'); </script>";
                    }
                    else
                    {
                        die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
                    }
                }
                else
                {
                    //Ya posee dos justificaciones en la quincena
                    mysqli_close($con);
                    echo "<script> imprime('Ya posee 2 justificaciones o  2 omisiones o 1 omisión+ 1 justificación. Sustento: Art. 46 CGT.'); </script>";
                }     
            }
            else
            {
                /*Si el sql2 SI posee datos significa que esa incidencia YA ha sido justificada y no se puede justificar
                dos veces la misma incidencia NUNCA
                */
                mysqli_close($con);
                echo "<script> imprime('Esta omisión ya fue justificada antes.'); </script>";
            }
        }
    }//Fin del if validar POST
    else
    {
        //Obligar al usuario a que meta todos los campos que se solicitan en aprobaciones.php
        $error="Faltan los siguientes datos:"."<br>";
        if (empty($_POST["num"])){$error.="Número de trabajador que exista."."<br>";}
        if (empty($_POST["fec"])){$error.="La fecha del retardo a justificar"."<br>";} 
        echo "<script> imprime('$error'); </script>";
    }
?>