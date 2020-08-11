<?php
    /*
        Clave 08 justifica cualquier cualquier omisión de e/s de cualquier jornada o turno
        Justifica estas claves:
        16 Omisión de salida del registro de asistencia a la jornada laboral discontinua.
        -18 Omisión de entrada en el registro de asistencia a la jornada laboral.
        -19 Omisión de salida en el registro de asistencia a la jornada laboral continua.
        -20 Omisión de entrada y/o salida al turno opcional o percepción adicional en el registro de asistencia.
        22 Omisión de salida (salida alimentos) y/o entrada (regreso alimentos) en el registro de asistencia.
        25 Registrar antes de la hora de salida en la jornada laboral.
    */
    if ((!empty($_POST["num"])) && (!empty($_POST["fec"])))
    {
        $num = $_POST['num'];
        $fecha=$_POST['fec'];

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
            $sql6="SELECT count(d.clave_justificacion_clave_justificacion) FROM trabajador a
            INNER JOIN asistencia b on a.numero_trabajador=b.trabajador_trabajador and a.numero_trabajador = '$num'
            INNER JOIN incidencia c on b.id = c.asistencia_asistencia and b.quincena_quincena = $quincena
            INNER JOIN justificacion d on c.idincidencia = d.incidencia_incidencia and fecha_inicio like '$anio%' 
            and d.clave_justificacion_clave_justificacion= 09";
            $totalRetardos=retornaAlgoDeBD(0,$sql6);

            //contamos cuántas 08 (omisiones justificadas) posee el empleado en la tabla justificacion
            $sql9="SELECT count(d.clave_justificacion_clave_justificacion) FROM trabajador a
            INNER JOIN asistencia b on a.numero_trabajador=b.trabajador_trabajador and a.numero_trabajador = $num
            INNER JOIN incidencia c on b.id = c.asistencia_asistencia and b.quincena_quincena = $quincena
            INNER JOIN justificacion d on c.idincidencia = d.incidencia_incidencia and fecha_inicio like '$anio%' 
            and d.clave_justificacion_clave_justificacion= 08";  
            $totalOmisionesIncidencia=retornaAlgoDeBD(0,$sql9);
            //hacer la suma total de omisiones y justificaciones de retardos
            $totalOm=$totalRetardos+$totalOmisionesIncidencia;
            //si totalOm es menor que 2 significa que aún puede justificar su omisión, pero antes se debe buscar la omisión a justificar
            if($totalOm<2)
            {
                /*Ahora revisar si existe la omisión/omisiones en la tabla incidencias; */
                $sql13="SELECT c.idincidencia  FROM trabajador a
                INNER JOIN asistencia b on a.numero_trabajador=b.trabajador_trabajador and a.numero_trabajador = '$num' and (b.fecha_entrada like '$fecha%' or b.fecha_salida like '$fecha%')
                INNER JOIN incidencia c on b.id = c.asistencia_asistencia and b.quincena_quincena = $quincena
                and (c.clave_incidencia_clave_incidencia=16 or c.clave_incidencia_clave_incidencia=18
                or c.clave_incidencia_clave_incidencia=19 or c.clave_incidencia_clave_incidencia=20
                or c.clave_incidencia_clave_incidencia=22 or c.clave_incidencia_clave_incidencia=25)";

                $filas13 = obtenerFilas($sql13);
                //Si el query está vacío significa que no existe esa omisión en la tabla incidencias
                if($filas13==0)
                {
                    mysqli_close($con);
                    echo "<script> imprime('No hay una incidencia en la fecha $fecha para el número de trabajador $num en la QUINCENA ACTUAL'); </script>";              
                }
                else
                {
                    $tot_datos=1;//solo 1 dato
                    //el id de la omision encontrada en el sql13
                    if($filas13==1){$idomisionEncontrada=retornaAlgoDeBD(0,$sql13);}
                    //los id de las omisiones encontrada en el sql13
                    if($filas13>1){$idomisionEncontrada=retornaAlgoDeBD(1,$sql13);$tot_datos=2;}//dos o más datos

                    /*idomisionEncontrada puede ser una variable simple o un array*/
                    
                    /*ver si esa omisión ya está justificada con idomisionEncontrada siendo variable*/
                    if($tot_datos==1)
                    {
                        $sql15="SELECT idjustificacion from justificacion where incidencia_incidencia=$idomisionEncontrada";
                        //obtener las filas del query
                        $filas15 = obtenerFilas($sql15);
                        //Si el query está vacío significa que no se ha justificado la omision, y se puede justificar
                        if($filas15==0)
                        {
                            //justificar omision
                            $sql16="INSERT INTO justificacion VALUES (NULL, '$fec_act', '$idomisionEncontrada', '08')";
                            $ok= "<script> imprime('Omisión justificada correctamente'); </script>";
                            $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                            $correcto=insertaEnBD($sql16,"-",$error,0);
                            insertaEnBitacoraJustificacion($ok,"Guardado",$correcto,$fec_act,$idomisionEncontrada,"08","-","-","-","-",0);
                        }
                        else
                        {
                            //Esta omision ya fue justificada antes
                            mysqli_close($con);
                            echo "<script> imprime('Esta omisión ya fue justificada antes.'); </script>";
                        }
                    }
                    //fin tot_datos==1

                    /*ver si esas omisiones ya están justificadas con idomisionEncontrada siendo array, por conveniencia,
                    solo veremos si el primer id de omisión del array ya está justificado, pues significará que anteriormente
                    ya se justificaron junto a las demas omisiones*/
                    if($tot_datos==2)
                    {
                        $idABuscar=$idomisionEncontrada[0];
                        $sql="SELECT idjustificacion from justificacion where incidencia_incidencia=$idABuscar";
                        //obtener las filas del query
                        $filas = obtenerFilas($sql);
                        //si no está justificada
                        if($filas==0)
                        {
                            //tamano del array
                            $tam=count($idomisionEncontrada);
                            for($i=0;$i<$tam;$i++)
                            {
                                $incid_a_just=$idomisionEncontrada[$i];
                                //justificar cada incidencia
                                $sql="INSERT INTO justificacion VALUES (NULL, '$fec_act', '$incid_a_just', '08')";
                                $ok= "<script> imprime('Omisión justificada correctamente'); </script>";
                                $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                                $correcto=insertaEnBD($sql,"-",$error,0);
                                $inserta=insertaEnBitacoraJustificacion($ok,"Guardado",$correcto,$fec_act,$idomisionEncontrada,"08","-","-","-","-",1);
                            }
                            //si sale del bucle significa que se justificaron las incidencias
                            echo $ok;
                        }
                        else
                        {
                            mysqli_close($con);
                            echo "<script> imprime('Esta omisión ya fue justificada antes.'); </script>";
                        }
                    }
                    
                }
            }
            else
            {
                //ya posee dos justificaciones o dos omisiones o 1 justificacion + 1 omision
                mysqli_close($con);
                echo "<script> imprime('Ya posee $totalRetardos justificaciones de retardo y $totalOmisionesIncidencia omisiones en la quincena. Solo se permiten 2 justificaciones de omision o 2 justificaciones de retardo o 1 justificacion de omisión + 1 justificación de retardo. Sustento: Art. 46 de las CGT.'); </script>";
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