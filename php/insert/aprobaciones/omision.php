<?php
    if ((!empty($_POST["num"])) && (!empty($_POST["fec"])) && (!empty($_POST["eOs"])))
    {
        $num = $_POST['num'];
        $fecha=$_POST['fec'];
        $entradaOsalida=$_POST['eOs'];
        //validar la fecha
        $fec_format=explode('-',$fecha);
        if(strlen($fec_format[0])>4)
        {
            echo "<script> imprime('Formato de año incorrecto. Verifique...'); </script>";
            exit();
        }
        else
        {
            /*2 omisiones por quincena o 
            una omisión + 1 retardo o 
            2 retardos (Art. 46 CGT)
            */
            //contamos cuántas 09 (retardos justificados) posee el empleado en la tabla justificacion
            $sql6="SELECT count(d.clave_justificacion_clave_justificacion)
            FROM trabajador a
            INNER JOIN asistencia b on a.numero_trabajador = b.trabajador_trabajador and a.numero_trabajador = '$num' 
            INNER JOIN incidencia c on  b.id = c.asistencia_asistencia and b.quincena_quincena = $quincena
            INNER JOIN justificacion d on c.idincidencia = d.incidencia_incidencia and fecha_inicio like '$anio' and d.clave_justificacion_clave_justificacion= 09
            INNER JOIN acceso e on a.numero_trabajador=e.trabajador_trabajador
            INNER JOIN turno f on e.turno_turno = f.idturno";
            $totalRetardos=retornaAlgoDeBD(0,$sql6);
            //contamos cuántas 08 (omisiones justificadas) posee el empleado en la tabla justificacion
            $sql9="SELECT count(d.clave_justificacion_clave_justificacion) FROM trabajador a
            INNER JOIN asistencia b on a.numero_trabajador = b.trabajador_trabajador and a.numero_trabajador = $num 
            INNER JOIN incidencia c on  b.id = c.asistencia_asistencia and b.quincena_quincena = $quincena
            INNER JOIN justificacion d on c.idincidencia = d.incidencia_incidencia and fecha_inicio like '$anio' and d.clave_justificacion_clave_justificacion= 08
            INNER JOIN acceso e on a.numero_trabajador=e.trabajador_trabajador
            INNER JOIN turno f on e.turno_turno = f.idturno";  
            $totalOmisionesIncidencia=retornaAlgoDeBD(0,$sql9);
            //hacer la suma total de omisiones y justificaciones de retardos
            $totalOm=$totalRetardos+$totalOmisionesIncidencia;
            //si totalOm es menor que 2 significa que aún puede justificar su omisión, pero antes se debe buscar la omisión a justificar
            if($totalOm<2)
            {
                /*Ahora revisar si existe la omisión en la tabla incidencias; */
                $sql13="SELECT c.idincidencia  FROM trabajador a
                INNER JOIN asistencia b on a.numero_trabajador = b.trabajador_trabajador and a.numero_trabajador = $num and CAST(b.fecha_entrada AS DATE) >= '$fecha'
                INNER JOIN incidencia c on  b.id = c.asistencia_asistencia and b.quincena_quincena = $quincena and c.clave_incidencia_clave_incidencia=18 
                INNER JOIN acceso e on a.numero_trabajador=e.trabajador_trabajador
                INNER JOIN turno f on e.turno_turno = f.idturno";
                $filas13 = obtenerFilas($sql13);
                //Si el query está vacío significa que no existe esa omisión en la tabla incidencias
                if($filas13==0)
                {
                    mysqli_close($con);
                    echo "<script> imprime('No hay una incidencia en la fecha $fecha para el número de trabajador $num en la QUINCENA ACTUAL'); </script>";              
                }
                else
                {
                    $idomisionEncontrada=retornaAlgoDeBD(0,$sql13);//el id de la omision encontrada en el query13
                    //ver si esa omisión ya está justificada
                    $sql15="SELECT d.clave_justificacion_clave_justificacion FROM trabajador a
                    INNER JOIN asistencia b on a.numero_trabajador = b.trabajador_trabajador and a.numero_trabajador = $num 
                    INNER JOIN incidencia c on  b.id = c.asistencia_asistencia and b.quincena_quincena = $quincena
                    INNER JOIN justificacion d on c.idincidencia = d.incidencia_incidencia and d.clave_justificacion_clave_justificacion= 08 and c.idincidencia = $idomisionEncontrada
                    INNER JOIN acceso e on a.numero_trabajador=e.trabajador_trabajador";
                    //obtener las filas del query
                    $filas15 = obtenerFilas($sql15);
                    //Si el query está vacío significa que no se ha justificado la omision, y se puede justificar
                    if($filas15==0)
                    {
                        //justificar omision
                        $sql16="INSERT INTO justificacion VALUES (NULL, '$fec_act', '$idomisionEncontrada', '08')";
                        if((mysqli_query($con, $sql16) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)))))
                        {
                            mysqli_close($con);
                            echo "<script> imprime('Omisión justificada correctamente'); </script>";
                        }
                        else
                        {
                            die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
                        }
                    }
                    else
                    {
                        //Esta omision ya fue justificada antes
                        mysqli_close($con);
                        echo "<script> imprime('Esta omisión ya fue justificada antes.'); </script>";
                    }
                }
            }
            else
            {
                //ya posee dos justificaciones o dos omisiones o 1 justificacion + 1 omision
                mysqli_close($con);
                echo "<script> imprime('Ya posee 2 justificaciones o  2 omisiones o 1 omisión+ 1 justificación. Sustento: Art. 46 CGT.'); </script>";
            }
        }//Fin del else de validacion fecha
        
    }//fin de if $_SESSION
    else
    {
        //Obligar al usuario a que meta todos los campos que se solicitan en aprobaciones.php
        $error="Faltan los siguientes datos:"."<br>";
        if (empty($_POST["num"])){$error.="Número de trabajador que exista."."<br>";}
        if (empty($_POST["fec"])){$error.="La fecha de inicio de la omisión a justificar."."<br>";} 
        if (empty($_POST["eOs"])){$error.="Si es una omisión de entrada o de salida."."<br>";}
        echo "<script> imprime('$error'); </script>";
    }
?>