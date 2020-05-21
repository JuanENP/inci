<?php
session_start();
    if (($_SESSION["name"]) && ($_SESSION["con"]))
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        require("../../Acceso/global.php"); 
    }
    else
    {
        header("Location: ../../index.html");
        die();
    }
?>
<?php
    require("../../assets/js/alerts-justificacion.php");
    //******formatear a la zona horaria de la ciudad de México**********
    date_default_timezone_set('America/Mexico_City');
    //obtener la fecha de hoy
    $fec_act=date("Y-m-d H:i:s"); //la fecha actual
    $anio=date("Y");//solo el año actual 
    
    /*OBTENER LA QUINCENA ACTUAL EN LA QUE ESTAMOS*/
    $sql5="SELECT idquincena from quincena where validez=1";
    $query5=mysqli_query($con, $sql5) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
    $resul5=mysqli_fetch_array($query5);
    $quincena=$resul5[0];
    /*FIN DE OBTENER QUINCENA ACTUAL*/
    if(empty($_POST["opcion"]))
    {
        echo "Por favor, diríjase a la sección Aprobaciones para que esta página se ejecute normalmente: " . "<a href='/ht/aprobaciones.php'>IR AHORA</a>";
        exit();//terminar el script
    }
    $operacion=$_POST['opcion'];
    if($operacion=="justificar")
    {
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

            $query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
            //obtener las filas del query
            $filas = mysqli_num_rows($query);
            //Si el query está vacío
            if($filas==0)
            {
                mysqli_close($con);
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
                INNER JOIN incidencia c on  b.id = c.asistencia_asistencia and b.quincena_quincena = $quincena
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
                    INNER JOIN incidencia c on  b.id = c.asistencia_asistencia and b.quincena_quincena = $quincena
                    INNER JOIN justificacion d on c.idincidencia = d.incidencia_incidencia and d.clave_justificacion_clave_justificacion= 09
                    INNER JOIN acceso e on a.numero_trabajador=e.trabajador_trabajador
                    INNER JOIN turno f on e.turno_turno = f.idturno";
                    $query3= mysqli_query($con, $sql3) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
                    $resul3=mysqli_fetch_array($query3);
                    $total=$resul3[0];

                    //contamos cuántas 08 (omisiones justificadas) posee el empleado en la tabla justificaciones de incidencia
                    $sql11="SELECT count(d.clave_justificacion_clave_justificacion) FROM trabajador a
                    INNER JOIN asistencia b on a.numero_trabajador = b.trabajador_trabajador and a.numero_trabajador = $num 
                    INNER JOIN incidencia c on  b.id = c.asistencia_asistencia and b.quincena_quincena = $quincena
                    INNER JOIN justificacion d on c.idincidencia = d.incidencia_incidencia and d.clave_justificacion_clave_justificacion= 08
                    INNER JOIN acceso e on a.numero_trabajador=e.trabajador_trabajador
                    INNER JOIN turno f on e.turno_turno = f.idturno";  
                    $query11= mysqli_query($con, $sql11) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
                    $resul11=mysqli_fetch_array($query11);
                    $totalOmisionesIn=$resul11[0];

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
                        mysqli_close($con);
                        echo "<script> no(); </script>";
                    }     
                }
                else
                {
                    /*Si el sql2 SI posee datos significa que esa incidencia YA ha sido justificada y no se puede justificar
                    dos veces la misma incidencia NUNCA
                    */
                    mysqli_close($con);
                    echo "<script> Ya(); </script>";
                }
            }
        }//Fin del if validar POST
        else
        {
            //Obligar al usuario a que meta todos los campos que se solicitan en aprobaciones.php
            echo "<script> imprime('NO debe dejar NINGÚN campo VACÍO. Verifique...'); </script>";
        }
    }//FIN DEL IF JUSTIFICAR

    if($operacion=="omision")
    {
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
            }
            else
            {
                /*2 omisiones por quincena o 
                una omisión + 1 retardo o 
                2 retardos (Art. 46 CGT)
                */
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
                    
                    $query13= mysqli_query($con, $sql13) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
                    //obtener las filas del query
                    $filas13 = mysqli_num_rows($query13);
                    //Si el query está vacío significa que no existe esa omisión en la tabla incidencias
                    if($filas13==0)
                    {
                        mysqli_close($con);
                        echo "<script> omisionNoExiste($num,'$fecha'); </script>";              
                    }
                    else
                    {
                        $resul13=mysqli_fetch_array($query13);
                        $idomisionEncontrada=$resul13[0];//el id de la omision encontrada en el query13
                        //ver si esa omisión ya está justificada
                        $sql15="SELECT d.clave_justificacion_clave_justificacion FROM trabajador a
                        INNER JOIN asistencia b on a.numero_trabajador = b.trabajador_trabajador and a.numero_trabajador = $num 
                        INNER JOIN incidencia c on  b.id = c.asistencia_asistencia and b.quincena_quincena = $quincena
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
                                mysqli_close($con);
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
                            mysqli_close($con);
                            echo "<script> antesOmision(); </script>";
                        }
                    }
                }
                else
                {
                    //ya posee dos justificaciones o dos omisiones o 1 justificacion + 1 omision
                    mysqli_close($con);
                    echo "<script> no(); </script>";
                }
            }//Fin del else de validacion fecha
            
        }//fin de if $_SESSION
        else
        {
            //Obligar al usuario a que meta todos los campos que se solicitan en aprobaciones.php
            echo "<script> imprime('NO debe dejar NINGÚN campo VACÍO. Verifique...'); </script>";
        }
    }//FIN DEL IF OMISIÓN

    if($operacion=="comision")
    {
        //es interna (guardar AnombreEmpresa) o externa (DnombreEmpresa)
        //oficial menor a 1 dia (clave 61)
        //comisión equiv. a un día es la clave 17; 
        //la comision de mayor tiempo se maneja como comision sindical se le podría poner CS: audio 69 minuto 13, 
        /*numero
            fecha inicio
            fecha de fin
            validez

            falta CICA 61 Comisión oficial con o sin viáticos o que comprenda menos de un día.
        */
        if ((!empty($_POST["num"])) && (!empty($_POST["fec"])) && (!empty($_POST["emp"])) && (!empty($_POST["priority"])) && (!empty($_POST["tl"])))
        {
            $num = $_POST['num'];//el número del trabajador
            $fecha=$_POST['fec'];//la fecha de inicio
            $empresa=$_POST['emp'];
            $prioridad=$_POST['priority'];//la prioridad de la comisión
            $tipocomision=$_POST['tl'];
            $validez=0;
            if($tipocomision=="csi" || $tipocomision=="cse")
            {
                if((!empty($_POST["he"])) && (!empty($_POST["hs"])) && (!empty($_POST["fecf"])))
                {
                    $fechaf=$_POST['fecf'];//la fecha de fin
                    $hora_e=$_POST['he'];
                    $hora_s=$_POST['hs'];
                    //24 H de anticipación, solo base
                    $clave_especial="CS";
                    if($tipocomision=="csi")
                    {
                        $empresa=utf8_encode("A".$empresa);
                    }
                    else
                    {
                        if($tipocomision=="cse")
                        {
                            $empresa=utf8_encode("D".$empresa);
                        }
                    }
                }
                else
                {
                    //Obligar al usuario a que meta todos los campos que se solicitan en aprobaciones.php
                    echo "<script> imprime('NO debe dejar NINGÚN campo VACÍO de la Comisión. Verifique...'); </script>";
                }
            }
            else
            {
                if($tipocomision=="com1")
                {
                    if((!empty($_POST["he"])) && (!empty($_POST["hs"])) && (!empty($_POST["fecf"])))
                    {
                        $fechaf=$_POST['fecf'];//la fecha de fin
                        $hora_e=$_POST['he'];
                        $hora_s=$_POST['hs'];
                        //24 H de anticipación, solo base
                        //guardar la empresa normal
                        //Su horario de entrada y salida deben ser 00:00
                        $clave_especial="61";
                    }  
                    else
                    {
                        //Obligar al usuario a que meta todos los campos que se solicitan en aprobaciones.php
                        echo "<script> imprime('NO debe dejar NINGÚN campo VACÍO de la Comisión. Verifique...'); </script>";
                    } 
                }
                else
                {
                    if($tipocomision=="co1")
                    {
                        $fechaf=$fecha;//la fecha de fin
                        $hora_e="00:00:00";
                        $hora_s="00:00:00";
                        //guardar empresa normal
                        //todo tipo de personal
                        $clave_especial="17";
                    }
                }
            }
            
            $date1= new DateTime($fecha);
            $date2= new DateTime($fechaf);
            /*Ver si ese empleado ya posee una comisión activa*/
            $sql7="SELECT idespecial from especial where trabajador_trabajador=$num and validez=1 and (clave_especial_clave_especial='CS' or clave_especial_clave_especial='61' or clave_especial_clave_especial='17')";
            $query7=mysqli_query($con, $sql7) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
            $resul7=mysqli_fetch_array($query7);
            $filasval1= mysqli_num_rows($query7);

            $sql7="SELECT b.idespecial from especial b where b.validez=0
            and (b.clave_especial_clave_especial='17' or b.clave_especial_clave_especial='61' or b.clave_especial_clave_especial='CS')
            and b.trabajador_trabajador = $num
            and  b.fecha_inicio>=now()";
            $query7=mysqli_query($con, $sql7) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
            $resul7=mysqli_fetch_array($query7);
            $filasval0= mysqli_num_rows($query7);

            /*Si el total de filas es 0 significa que el empleado no posee una comisión activa*/
            if(($filasval1==0 && $filasval0==0) || $prioridad=="a")
            {
                /*antes se debe verificar si se tuvo una comisión en los últimos 6 meses*/
                //obtener la fecha de hoy
                $hoy=date("Y-m-d"); 
                $fecha_ac = strtotime($hoy);
                $fecha_in = strtotime($fecha);//la fecha de inicio de la comisión
                if($fecha_ac < $fecha_in)
                {
                    //La comisión aún no empieza, insertar la comisión
                    $interval = $date1->diff($date2);
                    $totDias=$interval->format('%a');//los días que durará la comisión
                    //si el periodo de comisión es superior a 165 días (5 meses y medio) y la prioridad es normal
                    if($totDias>165 && $prioridad=="n")
                    {
                        mysqli_close($con);
                        echo "<script> noMaxComision('$fecha','$fechaf'); </script>";
                    }
                    else
                    {
                        //Insertar la comisión
                        $sql8=" INSERT INTO especial VALUES (null, '$fecha', '$fechaf', '$hora_e', '$hora_s', '0', '$num', '$clave_especial','$empresa','$totDias')";
                        if((mysqli_query($con, $sql8) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)))))
                        {
                            //Agregar a la bitacora más la prioridad
                            mysqli_close($con);
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
                    if($fecha_ac==$fecha_in)
                    {
                        mysqli_close($con);
                        echo "<script> imprime('La comisión empieza hoy y no puede registrarse debido a que SE REQUIERE MÍNIMO UN DÍA DE ANTICIPACIÓN'); </script>";
                    }
                    else
                    {
                        mysqli_close($con);
                        echo "<script> imprime('La fecha de inicio de la comisión ya pasó, NO ES POSIBLE REGISTRAR UNA COMISIÓN QUE YA INICIÓ'); </script>";
                    }
                }
            }
            else
            {
                //El empleado ya posee una comisión activa y no puede tener 2 comisiones a la vez
                mysqli_close($con);
                echo "<script> noComision($num); </script>";
            }

        }//Fin del if valida POST
        else
        {
            //Obligar al usuario a que meta todos los campos que se solicitan en aprobaciones.php
            echo "<script> imprime('NO debe dejar NINGÚN campo VACÍO de la Comisión. Verifique...'); </script>";
        }
    }//FIN DEL IF COMISIÓN

    if($operacion=="licencia")
    {
        if ((!empty($_POST["num"])) && (!empty($_POST["fec"])) && (!empty($_POST["lic"])))
        {
            $num = $_POST['num'];//el número del trabajador
            $TipoEmp=tipoEmpleado($num);
            $fecha=$_POST['fec'];//la fecha de inicio
            $ClaveLicencia=$_POST['lic'];//la clave de licencia que se eligió en aprobación
            $TuvoONoBecaAntes=0;/*1=si tuvo, 2=no ha tenido*/

            /*
            Reglamento de Becas del ISSSTE
            CICA 51 solo que no está en CICA XD
            las becas (normal) con goce o sin goce son hasta por meses (se renueva cada año si dura más de un año)
            Reglamento de Becas ISSSTE


            Artículo 24. Las becas cuya duración sea mayor de 12 meses requerirán ser ratificadas
            anualmente por la Subcomisión. El Becario deberá rendir informes satisfactorios sobre el
            cumplimiento del objeto de la Beca que disfruta en los términos a los que se hubiere
            comprometido.
            */

            if($ClaveLicencia=="51")
            {
                if (!empty($_POST["fecf"]))
                {
                    $fechaf=$_POST['fecf'];//la fecha de fin
                    $sql="SELECT t.descripcion from tipo t inner join trabajador tra on tra.tipo_tipo=t.idtipo 
                    where tra.numero_trabajador='$num'";
                    $query=mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
                    $resul=mysqli_fetch_array($query);
                    //Comprobar que sea basificado
                    if($resul[0]=="BASE")
                    {
                      //Ver si posee beca ya activa
                      $sql="SELECT idespecial FROM especial where clave_especial_clave_especial='51' 
                      and trabajador_trabajador='$num'
                      and validez=1";
                      $query=mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
                      $totfilas1=mysqli_num_rows($query);
    
                        //Ver si posee beca que aún no se activa pero falta que se active
                        $sql="SELECT duracion FROM especial where clave_especial_clave_especial='51' 
                        and trabajador_trabajador='$num'
                        and validez=0
                        and fecha_inicio>=now()";
                        $query=mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
                        $totfilas2=mysqli_num_rows($query);
    
                        if($totfilas1+$totfilas2>0)//Si posee comision activa o proxima a activarse
                        {
                            echo "<script> imprime('El empleado con número $num ya posee una beca activa actualmente. NO es posible tener 2 becas al mismo tiempo'); </script>";
                        }//Fin if checar ya tiene beca activa o próxima a activarse
                        else
                        {
                            /*
                                Artículo 21. El trabajador que disfrute de una Beca, sólo podrá ser beneficiario de otra,
                                siempre y cuando cubra el doble del tiempo de duración de la Beca otorgada.
                                Revisar este caso...
                            */ 
                            $sql="SELECT fecha_fin, duracion FROM especial where clave_especial_clave_especial='51' 
                            and trabajador_trabajador='$num'
                            and validez=0
                            and fecha_fin<=now()
                            order by idespecial";
                            $query=mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
                            $filas=mysqli_num_rows($query);
                            if($filas>0)
                            {
                                //Ya hizo una beca o varias con anterioridad, es decir, ya pasaron y no son válidas ahora
                                //Se deben obtener los datos de la última beca que solicitó
                                while($resul=mysqli_fetch_array($query))
                                {
                                    $UltimoDiaBecaPasada=$resul[0];
                                    $diasQueDuroLaBecaPasada=($resul[1]);
                                    //la última vez que pase por el while se guardarán los datos de la última beca que tuvo
                                }
                                $diasQueDuroLaBecaPasada=$diasQueDuroLaBecaPasada*2;//Los días que debe cubrir a fuerza para solicitar otra beca
                                $TuvoONoBecaAntes=1;//si
                            }
                            else
                            {
                                //Jamás ha realizado una Beca
                                $TuvoONoBecaAntes=2;//no
                            }
    
                            if($TuvoONoBecaAntes==1)
                            {
                                //Validar el Art. 21
                                //obtener la fecha de hoy
                                $today=date("Y-m-d"); 
                                $date1= new DateTime($today);
                                $date2= new DateTime($UltimoDiaBecaPasada);
                                $interval = $date1->diff($date2);
                                $totDias=$interval->format('%a');//los días que han pasado desde que se acabó su última Beca
                                if($totDias>$diasQueDuroLaBecaPasada)
                                {
                                    //Insertar la Beca
                                    $validafechas=fechaMayorMenor($fecha, $fechaf);
                                    $duracion=calcularDuracionEntreDosFechas(0,$fecha,$fechaf);
                                    ejecutaFinalBeca($validafechas,$fecha,$fechaf,$num,$ClaveLicencia,$duracion);
                                }
                                else
                                {
                                    //Faltan días por cubrir
                                    $porcubrir=$diasQueDuroLaBecaPasada;
                                    echo "<script> imprime('El empleado con número $num debe cubrir un total de $porcubrir días después de su última Beca. Solo ha cubierto $totDias días de los $porcubrir que debe. Sustento: Art. 21 del Reglamento de Becas del ISSSTE.'); </script>";    
                                }
                            }
                            else
                            {
                                if($TuvoONoBecaAntes==2)
                                {
                                    //insertar la beca
                                    $validafechas=fechaMayorMenor($fecha, $fechaf);
                                    $duracion=calcularDuracionEntreDosFechas(0,$fecha,$fechaf);
                                    ejecutaFinalBeca($validafechas,$fecha,$fechaf,$num,$ClaveLicencia,$duracion);
                                }
                            }
                        }
                    }
                    else
                    {
                        $resul[0]=utf8_encode($resul[0]);
                        echo "<script> imprime('El empleado con número $num es de tipo: $resul[0]. Se necesita ser de BASE para solicitar una beca. Sustento: Artículo 15, fracción III del Reglamento de Becas del ISSSTE.'); </script>";
                    }//Fin else comprobar si es basificado
                }
                else
                {
                    echo "<script> imprime('Falta la fecha de fin. NO DEBE dejarla vacía.'); </script>";
                } //Fin else comprobar que exista fecha de fin 
            }//Fin licencia 51

            /*
                Tolerancia lactancia CICA 92, es una licencia
                ARTÍCULO 47 CGT . Las trabajadoras cuyas hijas o hijos se encuentren en etapa de lactancia, tendrán derecho, 
                a su elección, a dos períodos de descanso diario de treinta minutos cada uno, o uno de una hora 
                para alimentar a sus hijas o hijos, por el lapso de seis meses contados a partir de la terminación de 
                su licencia por maternidad.
                
                Según CICA: solo para base y confianza
                Se da por un periodo de seis meses calendario a partir de la fecha en que se dé por concluida su incapacidad 
                por gravidez (Debe haber solicitado esta última para solicitar esta).
                30 minutos al inicio y 30 al final de la jornada O 1h al inicio o al final
                Debe ser resgistrado mínimo 12 H antes de la fecha de inicio
            */
            if($ClaveLicencia=="92")
            {
                //Solo para base o confianza
                //Ver si es mujer
                $genero=obtenerSexo($num);
                if($genero=="F")
                {
                    //VER SI POSEE ESTA LICENCIA ACTIVA
                    $sql="SELECT idespecial from especial where trabajador_trabajador='$num'
                    and clave_especial_clave_especial='92'
                    and validez='1'";
                    $filas=obtenerFilas($sql);
                    if($filas==0)
                    {
                        if($TipoEmp=="BASE" || $TipoEmp=="CONFIANZA")
                        {
                            //Debió haber solicitado una licencia por gravidez antes (y ya debió haberse terminado); buscar dicha licencia
                            $sql="SELECT idespecial,fecha_fin FROM especial where clave_especial_clave_especial='53' 
                            and trabajador_trabajador='$num'
                            and validez=0
                            and fecha_fin<now()
                            order by idespecial";
                            //recuerda que debes checar las fechas pues puede que si haya tenido esa licencia pero ya hace tiempo XD.
                            $query=mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
                            $filas=mysqli_num_rows($query);
                            if($filas>0)
                            {
                                while($resul=mysqli_fetch_array($query))
                                {
                                    $fechaUltimaLicGravidez=($resul[1]);
                                    //la última vez que pase por el while se guardará la fecha final de su última beca licencia por gravidez
                                }
        
                                $duracion=calcularDuracionEntreDosFechas(1,$fechaUltimaLicGravidez,"");
                                if($duracion<7)
                                {
                                    //insertar la tolerancia de lactancia
                                    $OpcionElegida=$_POST['to-la'];
                                    $horario=obtenerHorario($num);
                                    $horaE=$horario[0];
                                    $horaS=$horario[1];
                                    if($OpcionElegida==1)
                                    {
                                        $horaE=SumResMinutosHoras(1,$horaE,"30");
                                        $horaS=SumResMinutosHoras(1,$horaS,"30");
                                    }
                                    if($OpcionElegida==2)
                                    {
                                        $horaE=SumResMinutosHoras(1,$horaE,"60");
                                    }
                                    if($OpcionElegida==3)
                                    {
                                        $horaS=SumResMinutosHoras(1,$horaS,"60");
                                    }
            
                                    $FechaFin=SumRestDiasMesAnio(1,$fechaUltimaLicGravidez,"6 months");
                                    $sql="INSERT INTO especial VALUES (null, '$fechaUltimaLicGravidez', '$FechaFin', '$horaE', '$horaS', '1', '$num', '$ClaveLicencia','Ninguna','180')";
                                    $ok= "<script> imprime('Tolerancia de lactancia agregada correctamente'); </script>";
                                    $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                                    insertaEnBD($sql,$ok,$error);
                                }
                                else
                                {
                                    echo "<script> imprime('Han pasado $duracion días desde que terminó la licencia por gravidez. La tolerancia de lactancia debió solicitarse justo después de que terminó dicha licencia por gravidez. NO es posible registrar esta tolerancia de lactancia'); </script>";
                                }//fin if duración<7
                            }
                            else
                            {
                                echo "<script> imprime('La empleada con número $num NUNCA solicitó una licencia por gravidez hace un més como mínimo. NO se puede registrar esta licencia sin antes haber solicitado y terminado su licencia por gravidez.'); </script>";
                            }//fin de if filas>0
                        }
                        else
                        {
                            echo "<script> imprime('La empleada con número $num es de tipo: $TipoEmp. Se necesita ser de BASE o CONFIANZA para solicitar tolerancia de lactancia. Sustento: Clave 92: cobertura, del CICA.'); </script>";
                        }//fin if tipo empleado  
                    }
                    else
                    {
                        echo "<script> imprime('Esta empleada YA posee una tolerancia de lactancia activa. NO es posible tener 2 de estas licencias al mismo tiempo.'); </script>";
                    }//fin el se filas==0
                }
                else
                {
                    echo "<script> imprime('El empleado que eligió es hombre. Esta licencia es SOLO para sexo femenino.'); </script>";
                }
            }//FIN clave LICENCIA 92 Tolerancia lactancia

            /*
                Tolerancia estancia CICA 93
                Para todo tipo de personal
                30 minutos al inicio O 30 minutos al final
            */
            if($ClaveLicencia=="93")
            {
                if (!empty($_POST["fecf"]))
                {
                    //Ver si no posee una tolerancia de estancia activa aún
                    $sql="SELECT idespecial from especial where trabajador_trabajador='$num'
                    and clave_especial_clave_especial='93'
                    and (validez='1' or (validez='0' and fecha_inicio>now()))";
                    $filas=obtenerFilas($sql);
                    if($filas==0) //no posee una tol. estancia activa o por activarse
                    {
                        $fechaf=$_POST['fecf'];//la fecha de fin
                        $validarfechas=RevisarFechas(1,$fecha,$fechaf,"de la tolerancia de estancia","una tolerancia de estancia","",0);
                        if($validarfechas==4)//fechas correctas
                        {
                            $OpcionElegida=$_POST['to-es'];
                            $horario=obtenerHorario($num);
                            $horaE=$horario[0];
                            $horaS=$horario[1];
    
                            if($OpcionElegida==1)
                            {
                                $horaE=SumResMinutosHoras(1,$horaE,"30");
                            }
                            if($OpcionElegida==2)
                            {
                                $horaS=SumResMinutosHoras(1,$horaS,"30");
                            }
                            //Insertar la tolerancia de estancia
                            $duracion=calcularDuracionEntreDosFechas(0,$fecha,$fechaf);
                            $sql="INSERT INTO especial VALUES (null, '$fecha', '$fechaf', '$horaE', '$horaS', '0', '$num', '$ClaveLicencia','Ninguna','$duracion')";
                            $ok= "<script> imprime('Tolerancia de estancia agregada correctamente'); </script>";
                            $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                            insertaEnBD($sql,$ok,$error);
                        }
                    }
                    else
                    {
                        echo "<script> imprime('El empleado $num ya posee una Tolerancia de estancia activa. NO es posible tener dos de estas licencias al mismo tiempo.'); </script>";
                    }//Fin else validar filas
                }
                else
                {
                    echo "<script> imprime('Falta la fecha de fin. NO DEBE dejarla vacía.'); </script>";
                }//Fin else empty fechaf
            }//Fin tolerancia estancia CICA 93

            /*
                    LSG 
                CGT
                sin goce (no las mamnejan)
                Aplica para: Base y confianza

                Arti.52 CGT Si pasa de Base a Confianza se puede pedir una licencia sin goce superior a un año, pero se renueva anualmente
                En el sistema actualmente se utiliza la clave 92 pero en CICA esa clave es para tolerancia de lactancia 
                ¿Que clave se utiliza? (no aplica) 

                LSG
                Arti.53 CGT. Los trabajadores disfrutarán de licencias sin goce en forma total o fraccionada por una vez al año, el tiempo
                depende de su antiguedad. 
                I. Por treinta días, para quienes tengan de seis meses a un año;
                II. Por noventa días, para quienes tengan de uno a tres años; y
                III. Por ciento ochenta días para quienes tengan más de tres años.

                agotado este derecho deberá transcurrir un período mínimo de seis meses para que se le autorice otra licencia

                Para los casos, en que la trabajadora o el trabajador haya disfrutado de licencias sin goce de sueldo, durante 
                el periodo que corresponda se disminuirá 1 DÍA DE VACACIONES por cada quince días de licencia
                y será disminuida la aportación comprendida en el Artículo 87 fracción VIII (ayuda por la muerte de un 
                familiar en primer grado).
                ¿Que clave se utiliza?

                LSGSS
                Arti.54 CGT trabajadores que deban practicar servicio social o pasantía en alguna otra dependencia o entidad de gobierno 
                federal, estatal o municipal se le da licencia sin goce por el tiempo que dure el servicio.
                Debe durar como mínimo 6 meses, se puede alargar hasta 2 años, pero se deben presentar documentos que avalen
                la prorroga por parte de la dependencia en donde se realiza la pasantía.
            */
            if($ClaveLicencia=="LSG" || $ClaveLicencia=="LSGSS")
            {
                if (!empty($_POST["fecf"]))
                {
                    $tipo=tipoEmpleado($num);
                    if($tipo=="BASE" || $tipo=="CONFIANZA")
                    {
                        $fechaf=$_POST['fecf'];//la fecha de fin
                        $validarfechas=RevisarFechas(1,$fecha,$fechaf,"de la licencia sin goce","una licencia sin goce","",0);

                        $separa1=explode("-",$fecha);
                        $anioInicia=$separa1[0];

                        $separa2=explode("-",$fechaf);
                        $anioFin=$separa2[0];

                        if($ClaveLicencia=="LSG")
                        { 
                            
                            if(isset($_POST['licUnAnio'])) //solo si es licencia por enfermedad no profesional
                            {
                                $diasPermitidos=365;
                            }
                            else
                            {
                                if($anioInicia!=$anio || $anioFin!=$anio)
                                {
                                    echo "<script> imprime('Las fechas de inicio y de fin requieren que sean DEL AÑO ACTUAL $anio. Verifique...'); </script>";
                                    exit();
                                }

                                //calcular la antiguedad del empleado
                                $antiguedad=calculaAntiguedad($num);
                                if($antiguedad<0.5)
                                {
                                    echo "<script> imprime('El empleado con número $num cuenta con menos de 6 meses de antiguedad. Se requieren 6 meses o más de antiguedad para solicitar una licencia sin goce. Sustento: Artículo 53 de las CGT.'); </script>";
                                    exit();
                                }
                                if($antiguedad>=0.5 && $antiguedad<=1)
                                {
                                    $diasPermitidos=30;
                                }
                                else
                                {
                                    if($antiguedad>=1 && $antiguedad<=3)
                                    {
                                        $diasPermitidos=90;
                                    }
                                    else
                                    {
                                        if($antiguedad>3)
                                        {
                                            $diasPermitidos=180;
                                        }
                                    }
                                }
                            }

                            $sql="SELECT duracion FROM especial where trabajador_trabajador='$num' and fecha_inicio like '$anio%' and clave_especial_clave_especial='LSG'";
                            $filas=obtenerFilas($sql);
                            if($filas==0)
                            {
                                //No ha solicitado una LSG en el año
                                $diasQueSolicita=calcularDuracionEntreDosFechas(0,$fecha,$fechaf);
                                if($diasQueSolicita<=$diasPermitidos)
                                {
                                    //Insertar la licencia sin goce
                                    $sql="INSERT INTO especial VALUES (null, '$fecha', '$fechaf', '00:00:00', '00:00:00', '0', '$num', '$ClaveLicencia','Ninguna','$diasQueSolicita')";
                                    $ok= "<script> imprime('Licencia sin goce agregada correctamente'); </script>";
                                    $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                                    insertaEnBD($sql,$ok,$error);
                                } 
                                else
                                {
                                    echo "<script> imprime('El empleado con número $num está solicitando una licencia sin goce por $diasQueSolicita días, pero por su antiguedad solo puede solicitar licencias sin goce por $diasPermitidos días. Sustento: Artículo 53 de las CGT.'); </script>";
                                }//Fin if dias que solicita
                            }
                            else
                            {
                                //Ya ha solicitado una LSG en el año, se debe calcular cuantos días de licencia sin goce le queda
                                $diasYaUsados=0;
                                $query=mysqli_query($con, $sql) or die();
                                while($resul=mysqli_fetch_array($query))
                                {
                                    $diasYaUsados=$diasYaUsados+$resul[0];
                                }

                                if(isset($_POST['licUnAnio'])) //solo en el caso de que sea una licencia sin goce por riesgo o enfermedad no profesional
                                {
                                    $diasYaUsados=0;
                                }

                                if($diasYaUsados<=$diasPermitidos)
                                {
                                    //aún le quedan días
                                    $diasRestantes=$diasPermitidos-$diasYaUsados;//ver cuántos días le quedan
                                    $duracion=calcularDuracionEntreDosFechas(0,$fecha,$fechaf);
                                    if($duracion<=$diasRestantes)
                                    {
                                        //insertar la licencia sin goce
                                        $sql="INSERT INTO especial VALUES (null, '$fecha', '$fechaf', '00:00:00', '00:00:00', '0', '$num', '$ClaveLicencia','Ninguna','$duracion')";
                                        $ok= "<script> imprime('Licencia sin goce agregada correctamente'); </script>";
                                        $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                                        insertaEnBD($sql,$ok,$error);
                                    }
                                    else
                                    {
                                        echo "<script> imprime('El empleado con número $num intenta solicitar una licencia sin goce con duración de $duracion días. Sin embargo, este empleado solo tiene derecho a $diasRestantes días de licencias sin goce de los $diasPermitidos días permitidos por su antiguedad. NO es posible agregar esta licencia debido a lo anterior.'); </script>";
                                    }//fin if dias ya usados
                                }
                                else
                                {
                                    echo "<script> imprime('El empleado con número $num ha agotado sus $diasPermitidos días en el año disponibles por su antiguedad para una licencia sin goce. Debe esperar al año siguiente para solicitar una licencia sin goce.'); </script>";
                                }//fin if dias ya usados
                            }//Fin if filas ==0 
                        }//Fin clave LSG
                        else
                        {
                            if($ClaveLicencia=="LSGSS")
                            {
                                if($anioInicia!=$anio || $anioFin!=$anio)
                                {
                                    echo "<script> imprime('Las fechas de inicio y de fin requieren que sean DEL AÑO ACTUAL $anio. Verifique...'); </script>";
                                    exit();
                                }
                                //ver que no tenga una LSGSS ACTIVA O POR ACTIVARSE
                                $sql="SELECT idespecial FROM especial where trabajador_trabajador='$num' 
                                and (clave_especial_clave_especial='LSGSS' or clave_especial_clave_especial='LSG')
                                and (validez='1' or (validez='0' and fecha_inicio>now()))";
                                $filas=obtenerFilas($sql);
                                if($filas==0)
                                {
                                    //Insertar la LSGSS
                                    $duracion=calcularDuracionEntreDosFechas(0,$fecha,$fechaf);
                                    $min6Meses=$duracion/365;
                                    if($min6Meses<0.333)//< a 4 meses
                                    {
                                        echo "<script> imprime('Toda LSGSS debe durar mínimo 4 meses. La licencia que intenta agregar no cumple el requisito anterior. Verifique'); </script>";
                                        exit();
                                    }
                                    $sql="INSERT INTO especial VALUES (null, '$fecha', '$fechaf', '00:00:00', '00:00:00', '0', '$num', '$ClaveLicencia','*Ver documento*','$duracion')";
                                    $ok= "<script> imprime('Licencia sin goce para servicio social agregada correctamente'); </script>";
                                    $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                                    insertaEnBD($sql,$ok,$error);
                                }
                                else
                                {
                                    echo "<script> imprime('El empleado con número $num Ya posee una licencia sin goce activa. No es posible tener dos de estas licencias al mismo tiempo'); </script>";
                                }
                            }
                        }//Fin clave licencia LSGSS
                    }
                    else
                    {
                        echo "<script> imprime('El empleado con número $num es de tipo $tipo. Se necesita ser empleado de BASE o CONFIANZA para solicitar una licencia sin goce'); </script>";
                    }//Fin if tipo
                }
                else
                {
                    echo "<script> imprime('Falta la fecha de fin. NO DEBE dejarla vacía.'); </script>";
                }//Fin if fecha fin
            }//Fin LSG y LSGSS

            /*
                CON GOCE (permiso con goce por antiguedad CICA 41, es la misma para estos artículos.)
                Art. 55 CGT: podrán disfrutar de licencias con goce de sueldo:
                
                CICA 41, especificar detalle: Tramites para obtener pensión por jubilación, de retiro por edad, cesantía en edad avanzada
                los basificados que tengan necesidad de iniciar los trámites para obtener su pensión ya sea por jubilación, 
                de retiro por edad y tiempo de servicio, por cesantía en edad avanzada o bien bajo el régimen de cuentas 
                individuales, de retiro, cesantía en edad avanzada y vejez, el Instituto le concederá licencia 
                con goce de sueldo por un término de tres meses.

                CICA 41 con goce de sueldo por fuerza mayor (en base a su antiguedad)
                ARTÍCULO 57. El Instituto concederá a su personal licencias con goce de sueldo por motivos de fuerza mayor, 
                distintas a las referidas en las fracciones I a IV del Artículo anterior (artículo 56). Dichas licencias 
                serán descontadas de los estímulos adicionales referidos en el ARTÍCULO 87, fracción VII de estas Condiciones, 
                a partir del primer día. Se conceden a solicitud del trabajador mediante la debida comprobación del motivo, 
                en un plazo que no deberá exceder a las 48 horas posteriores al suceso.  

                Para los efectos de los Artículos 56 (CICA 40 permiso haste por 3 días) y 57 (CICA 41 LICENCIA POR FUERZA mayor) 
                la trabajadora o el trabajador podrá disfrutar de estas licencias 
                hasta por el número de días de sueldo en los términos del Artículo 87 (estímulos por antiguedad), 
                fracción VII de estas Condiciones. El tiempo se debe basar en la antiguedad del empleado.
            */
            if($ClaveLicencia=="41")
            {
                if ((!empty($_POST["per-go"])))
                {
                    $tipo_permiso_goce=$_POST['per-go'];

                    if($tipo_permiso_goce==2) //tramites pension jubilacion
                    {
                        $validarfechas=RevisarFechas(2,$fecha,"","de la licencia por pensión","una licencia por pensión","",0);
                        $fechaf=SumRestDiasMesAnio(1,$fecha,"3 months");
                        $duracion=calcularDuracionEntreDosFechas(0,$fecha,$fechaf);
                        //Ver que no tenga una licencia de este tipo; solo es una en la vida
                        $sql="SELECT idespecial FROM especial where trabajador_trabajador='$num' 
                        and (clave_especial_clave_especial='41')
                        and (validez='1' or (validez='0' and fecha_inicio>now()) or (validez='0' and fecha_fin<now()))
                        and empresa='pension'";
                        $filas=obtenerFilas($sql);
                        if($filas==0)
                        {
                            //Registrar en BD
                            $sql="INSERT INTO especial VALUES (null, '$fecha', '$fechaf', '00:00:00', '00:00:00', '0', '$num', '$ClaveLicencia','pension','$duracion')";
                            $ok= "<script> imprime('Licencia por pensión agregada correctamente'); </script>";
                            $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                            insertaEnBD($sql,$ok,$error);
                        }
                        else
                        {
                            echo "<script> imprime('El empleado con número $num está solicitando una licencia con goce por pensión; sin embargo, dicha licencia ya fue solicicitada antes y no es posible registrar 2 veces esta licencia. SOLO SE PERMITE ESTA LICENCIA UNA VEZ EN LA VIDA.'); </script>";
                        }//fin if filas==0
                    }
                    else
                    {
                        if($tipo_permiso_goce==1)//fuerza mayor ¿?Una vez que se inserte supongo que se tiene que revisar las incidencias de ese rango de fechas y quitarlas
                        {
                            if((!empty($_POST["fecf"])))
                            {
                                $fechaf=$_POST['fecf'];
                                $validarfechas=RevisarFechas(1,$fecha, $fechaf,"de la licencia por fuerza mayor","una licencia por fuerza mayor","",1);
                                $duracion=calcularDuracionEntreDosFechas(0,$fecha,$fechaf);
                                //¿?cada cuanto tiempo se pueden pedir el CICA 40 y el CICA 41
                                //obtener la antiguedad
                                $diasPermitidos;
                                $antiguedad=calculaAntiguedad($num);
                                //calcular dias permitidos en base al artículo 87 fracción 7
                                $diasPermitidos=diasAntiguedad87V11($antiguedad);
                                if($duracion>$diasPermitidos)
                                {
                                    echo "<script> imprime('El empleado con número $num está solicitando una licencia con goce por fuerza mayor por un total de $duracion días; solo puede, por su antiguedad solicitar licencias con goce por $diasPermitidos días. Sustento: Artículos 57 y 87 Fracción 7 de las CGT.'); </script>";
                                    exit();
                                }
                                $sql="SELECT duracion FROM especial where 
                                ((clave_especial_clave_especial='41' and empresa!='pension') or clave_especial_clave_especial='40') 
                                and trabajador_trabajador='$num'
                                and fecha_inicio like '$anio%'";
                                //Obtener los 40 y 41 de este año y sumar los días solicitados
                                $diasGastados=sumaRegistrosDeConsulta($sql);
                                //Hacer una resta para obtener los días que le quedan
                                $diasSobrantes=$diasPermitidos-$diasGastados;
                                if($duracion<=$diasSobrantes) //aún le quedan días
                                {

                                    //insertar la licencia
                                    $sql="INSERT INTO especial VALUES (null, '$fecha', '$fechaf', '00:00:00', '00:00:00', '1', '$num', '$ClaveLicencia','','$duracion')";
                                    $ok= "<script> imprime('Licencia por fuerza mayor agregada correctamente'); </script>";
                                    $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                                    insertaEnBD($sql,$ok,$error);
                                }
                                else//fin if dias permitidos < dias gastados
                                {
                                    if($diasSobrantes==0)
                                    {
                                        echo "<script> imprime('El empleado con número $num está solicitando una licencia con goce por fuerza mayor por un total de $duracion días; sin embargo, este empleado ha agotado sus días disponibles por antiguedad para solicitar licencias de este tipo. Sustento: Artículos 57 y 87 Fracción 7 de las CGT.'); </script>";
                                    }
                                    else
                                    {
                                        echo "<script> imprime('El empleado con número $num está solicitando una licencia con goce por fuerza mayor por un total de $duracion días; solo puede, por su antiguedad solicitar licencias con goce por $diasSobrantes días; esto debido a que ya ha solicitado licencias de este tipo con anterioridad y los días que merece por antiguedad disminuyeron. Sustento: Artículos 57 y 87 Fracción 7 de las CGT.'); </script>";
                                    }
                                }
                            }
                            else
                            {
                                echo "<script> imprime('Falta la fecha de fin. NO DEBE dejarla vacía.'); </script>";
                            }//fin if fecha final
                        }
                    }//Fin else tipo permiso
                }
                else
                {
                    echo "<script> imprime('Falta el motivo. NO DEBE dejar ningún campo vacío.'); </script>";
                }//fin if empty
            }

            /*
                al que contraiga matrimonio se le concederán diez días hábiles de licencia con goce de sueldo por una sola vez, 
                comprometiéndose a entregar, dentro de los sesenta días posteriores a la terminación de la licencia, su acta 
                de matrimonio; (ya no otorga: puntualidad (79), asistencia (80), desempeño (83), mérito relevante (84), ni el art. 85:notas buenas)
                **noCICA 48
            */
            if($ClaveLicencia=="48")
            {
                $validarfechas=RevisarFechas(2,$fecha,"","de la licencia por matrimonio","una licencia por matrimonio","",0);
                //ver que no tenga una licencia por matrimonio pasada o por activarse, solo es una en la vida
                $sql="SELECT idespecial FROM especial where trabajador_trabajador='$num' 
                and (clave_especial_clave_especial='48')
                and (validez='1' or (validez='0' and fecha_inicio>now()) or (validez='0' and fecha_fin<now()))";
                $filas=obtenerFilas($sql);
                if($filas==0)
                {
                    $fechaf=feriadoConArray($fecha,10);
                    $duracion=calcularDuracionEntreDosFechas(0,$fecha,$fechaf);
                    //insertar la licencia
                    $sql="INSERT INTO especial VALUES (null, '$fecha', '$fechaf', '00:00:00', '00:00:00', '0', '$num', '$ClaveLicencia','*Ver documento*','$duracion')";
                    $ok= "<script> imprime('Licencia por contraer matrimonio agregada correctamente. ESTE EMPLEADO TENDRÁ 60 DÍAS PARA ENTREGAR EL ACTA DE MATRIMONIO EN ESTE LUGAR A PARTIR DEL $fechaf.'); </script>";
                    $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                    insertaEnBD($sql,$ok,$error);
                }
                else
                {
                    echo "<script> imprime('El empleado con número $num ya solicitó antes una licencia por contraer matrimonio. Esta licencia se puede pedir solo UNA vez por trabajador. Sustento: Artículo 55 fracción 2 de las CGT.'); </script>";
                }
            }

            /*
                por fallecimiento de un familiar en primer grado, con parentesco por consanguinidad, afinidad o su cónyuge 
                se le concederán cinco días hábiles de licencia con goce de sueldo. anexando copia del acta de defunción o
                comprometiéndose, en su caso, a entregarla dentro de los quince días posteriores a la terminación de la 
                licencia. (ya no otorga: puntualidad (79), asistencia (80), desempeño (83), mérito relevante (84), ni el art. 85: notas buenas)
                **noCICA 49
            */
            if($ClaveLicencia=="49")
            {
                $validarfechas=RevisarFechas(2,$fecha,"","de la licencia por fallecimiento","una licencia por fallecimiento","",0);
                //ver que no tenga una licencia por fallecimiento activa o por activarse
                $sql="SELECT idespecial FROM especial where trabajador_trabajador='$num' 
                and (clave_especial_clave_especial='49')
                and (validez='1' or (validez='0' and fecha_inicio>now()))";
                
                $filas=obtenerFilas($sql);
                if($filas==0)
                {
                    $fechaf=feriadoConArray($fecha,5);
                    $duracion=calcularDuracionEntreDosFechas(0,$fecha,$fechaf);
                    //insertar la licencia
                    $sql="INSERT INTO especial VALUES (null, '$fecha', '$fechaf', '00:00:00', '00:00:00', '0', '$num', '$ClaveLicencia','*Ver documento*','$duracion')";
                    $ok= "<script> imprime('Licencia por fallecimiento de un familiar agregada correctamente. ESTE EMPLEADO TENDRÁ 15 DÍAS PARA ENTREGAR EL ACTA DE DEFUNCIÓN EN ESTE LUGAR A PARTIR DEL $fechaf.'); </script>";
                    $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                    insertaEnBD($sql,$ok,$error);
                }
                else
                {
                    echo "<script> imprime('El empleado con número $num ya tiene una licencia por fallecimiento de un familiar activa. No se puede tener 2 de estas licencias al mismo tiempo.'); </script>";
                }
            }

            /*
                CICA 53
                Las trabajadoras en estado de gravidez disfrutarán de licencias con goce de sueldo, treinta días antes de 
                la fecha probable de parto y sesenta días después de éste.
            */
            if($ClaveLicencia=="53")
            {
                if((!empty($_POST["fecf"])))
                {
                    $fechaf=$_POST["fecf"];
                    $genero=obtenerSexo($num);
                    if($genero=="F")
                    {
                        $validarfechas=RevisarFechas(1,$fecha,$fechaf,"de la incapacidad por gravidez","Una incapacidad por gravidez","",0);
                        //ver que no tenga una licencia por gravidez activa o por activarse
                        $sql="SELECT idespecial FROM especial where trabajador_trabajador='$num' 
                        and (clave_especial_clave_especial='53')
                        and (validez='1' or (validez='0' and fecha_inicio>now()))";
                        
                        $filas=obtenerFilas($sql);
                        if($filas==0)
                        {
                            $duracion=calcularDuracionEntreDosFechas(0,$fecha,$fechaf);
                            if($duracion>=88 && $duracion<90)
                            {
                                //insertar la licencia
                                $sql="INSERT INTO especial VALUES (null, '$fecha', '$fechaf', '00:00:00', '00:00:00', '0', '$num', '$ClaveLicencia','*Ver documento*','$duracion')";
                                $ok= "<script> imprime('Incapacidad por gravidez agregada correctamente.'); </script>";
                                $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                                insertaEnBD($sql,$ok,$error);
                            }
                            else//fin if duracion entre 88 y 90
                            {
                                echo "<script> imprime('La duración de esta licencia que está solicitando es de $duracion días. Esta licencia debe ser obligatoriamente de 90 días de duración. Sustento: Artículo 55 fracción IV de las CGT'); </script>";
                            }
                        }
                        else//fin if filas
                        {
                            echo "<script> imprime('El empleado con número $num ya tiene una licencia por fallecimiento de un familiar activa. No se puede tener 2 de estas licencias al mismo tiempo.'); </script>";
                        }
                    }
                    else//fin if sexo
                    {
                        echo "<script> imprime('El empleado que eligió es hombre. Esta licencia es SOLO para sexo femenino.'); </script>";
                    }
                }
                else//fin if empty fechaf
                {
                    echo "<script> imprime('Falta la fecha final, no debe dejarla vacía...'); </script>";
                }                 
            }//Fin CICA 53 gravidez


            /*
                CICA 47
                licencia con goce de sueldo hasta por ocho días, cuando sus hijas o hijos menores de seis años requieran de 
                cuidados por enfermedad aguda; así como también para el caso de las hijas y los hijos con discapacidad física o 
                psíquica, sin importar la edad que tengan, basta que el médico tratante del Instituto certifique la 
                gravedad del caso y los días de cuidado; debiendo presentar el original del documento que acredite este 
                supuesto. Empleados varones deberán además comprobar con documento fehaciente, tener la custodia de la 
                menor o del menor y que no cuentan con el auxilio de su cónyuge.
                (ya no otorga: puntualidad (79), asistencia (80), desempeño (83), mérito relevante (84), ni el art. 85; 
                cuando en el término de un mes los días otorgados por uno u otro concepto o por ambos sumen solos o individual
                mente 3 días).  
            */
            if($ClaveLicencia=="47")
            { 
                if((!empty($_POST["fecf"])))
                {
                    $fechaf=$_POST["fecf"];
                    $validarfechas=RevisarFechas(1,$fecha,$fechaf,"de la licencia por cuidados maternos","una licencia por cuidados maternos","",1);
                    //ver que no tenga una licencia de este tipo activa o por activarse
                    $sql="SELECT idespecial FROM especial where trabajador_trabajador='$num' 
                    and (clave_especial_clave_especial='47')
                    and (validez='1' or (validez='0' and fecha_inicio>now()))";
                    
                    $filas=obtenerFilas($sql);
                    if($filas==0)
                    {
                        $duracion=calcularDuracionEntreDosFechas(0,$fecha,$fechaf);
                        if($duracion>0 && $duracion<=8)
                        {
                            //insertar la licencia
                            $sql="INSERT INTO especial VALUES (null, '$fecha', '$fechaf', '00:00:00', '00:00:00', '0', '$num', '$ClaveLicencia','*Ver documento*','$duracion')";
                            $ok= "<script> imprime('Licencia por cuidados maternos agregada correctamente.'); </script>";
                            $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                            insertaEnBD($sql,$ok,$error);
                        }
                        else
                        {
                            echo "<script> imprime('La duración de esta licencia que está solicitando es de $duracion días. Esta licencia debe ser obligatoriamente de 1 hasta 8 días de duración. Sustento: Artículo 55 fracción V de las CGT'); </script>";
                        }
                    }
                    else
                    {
                        echo "<script> imprime('El empleado con número $num ya tiene una licencia por cuidados maternos activa. No se puede tener 2 de estas licencias al mismo tiempo.'); </script>";
                    }
                }
                else//fin if empty fechaf
                {
                    echo "<script> imprime('Falta la fecha final, no debe dejarla vacía...'); </script>";
                }  
            }//Fin CICA 47 cuidados maternos


            /*         
                CICA 62(Claves de servicio autorizadas para este concepto dentro del Instituto son: 09200 Radiología, 
                09210 Medicina Nuclear, 20600 Radio Diagnóstico, 17824 Radiología, 7910 Radio Terapia)
            */
            if($ClaveLicencia=="62")
            {
                echo "Licencia Radio";
            }//Fin CICA 62 Radio ¿?

            /*
                CICA 54
                En caso de riesgo de trabajo, la trabajadora o el trabajador tendrá derecho a disfrutar sus licencias con 
                goce de sueldo en los términos de los Artículos 110 de la Ley y el aplicable de la Ley del ISSSTE .
                el Artículo 62 fracción I de la Ley del ISSSTE y clave 54: Observaciones del CICA NOS DICE QUE
                ESTA LICENCIA DURA MÁXIMO 1 AÑO
                
                además:
                art.60 Ley ISSSTE: El Trabajador o sus Familiares Derechohabientes deberán solicitar al Instituto la 
                calificación del probable riesgo de trabajo dentro de los treinta días hábiles siguientes a que haya ocurrido, 
                en los términos que señale el reglamento respectivo y demás disposiciones aplicables. No procederá la 
                solicitud de calificación, ni se reconocerá un riesgo del trabajo, si éste no hubiere sido notificado 
                al Instituto en los términos de este artículo.)
                ; y
            */
            if($ClaveLicencia=="54")
            {
                //¿? Se puede pedir dias antes de la fecha de hoy?
                
                /*
                    Todo tipo de empleado
                */ 
                if((!empty($_POST["fecf"])))
                {
                    $fechaf=$_POST["fecf"];
                    $validarfechas=RevisarFechas(1,$fecha,$fechaf,"de la licencia por incapacidad por accidente o riesgo profesional","una licencia por incapacidad por accidente o riesgo profesional","",1);
                    //ver que no tenga una licencia de este tipo activa o por activarse
                    $sql="SELECT idespecial FROM especial where trabajador_trabajador='$num' 
                    and (clave_especial_clave_especial='54')
                    and (validez='1' or (validez='0' and fecha_inicio>now()))";
                    
                    $filas=obtenerFilas($sql);
                    if($filas==0)
                    {
                        $duracion=calcularDuracionEntreDosFechas(0,$fecha,$fechaf);
                        if($duracion>0 && $duracion<=365)
                        {
                            //insertar la licencia
                            $sql="INSERT INTO especial VALUES (null, '$fecha', '$fechaf', '00:00:00', '00:00:00', '0', '$num', '$ClaveLicencia','*Ver documento*','$duracion')";
                            $ok= "<script> imprime('licencia por incapacidad por accidente o riesgo profesional agregada correctamente.'); </script>";
                            $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                            insertaEnBD($sql,$ok,$error);
                        }
                        else
                        {
                            echo "<script> imprime('La duración de esta licencia que está solicitando es de $duracion días. Esta licencia debe ser obligatoriamente de 1 hasta 365 días de duración. Sustento: Artículo 62 fracción I de la Ley del ISSSTE'); </script>";
                        }
                    }
                    else
                    {
                        echo "<script> imprime('El empleado con número $num ya tiene una licencia por incapacidad por accidente o riesgo profesional activa. No se puede tener 2 de estas licencias al mismo tiempo.'); </script>";
                    }
                }
                else//fin if empty fechaf
                {
                    echo "<script> imprime('Falta la fecha final, no debe dejarla vacía...'); </script>";
                }
            }//Fin CICA 62 Radio


            /*
                CICA 55 .
                licencia Incapacidad médica por enfermedad no profesional. 
                En caso de enfermedades no profesionales se aplicará lo previsto en los Artículos 111 de la Ley y el 
                aplicable de la Ley del ISSSTE. 
                Clave 55: Observaciones, del CICA

                Las licencias y permisos a que se refieren los Artículos anteriores podrán ser solicitadas por las trabajadoras 
                o los trabajadores o la representación sindical, con la debida anticipación a la fecha que se señale como inicio 
                de la misma salvo causa de fuerza mayor.

            */
            if($ClaveLicencia=="55")
            {
                /*
                    Todo tipo de empleado
                    ¿?
                */ 
                $antiguedad=calculaAntiguedad($num);
                $diasPermitidos;
                if($antiguedad<1)
                {
                    $diasPermitidos=15;
                }
                else
                {
                    if($antiguedad>1 && $antiguedad<5)
                    {
                        $diasPermitidos=30;
                    }
                    else
                    {
                        if($antiguedad>5 && $antiguedad<10)
                        {
                            $diasPermitidos=45;
                        }
                        else
                        {
                            if($antiguedad>10)
                            {
                                $diasPermitidos=60;
                            }
                        }
                    }
                }//fin ifs antiguedad

            }//Fin CICA 62 Radio
            
            /*incapacidad médica
                revisar la incapacidad registrada en (el sistema que el ISSSTE tiene para licencias médicas, la emite cualquier doctor)
                cual es el doctor que da más incapacidades en un periodo de tiempo.
            */
        }
        else
        {
            $error="Faltan los siguientes datos:"."<br>";
            if (empty($_POST["num"])){$error.="Número de trabajador que exista."."<br>";}
            if (empty($_POST["fec"])){$error.="La fecha de inicio de lo que está solicitando."."<br>";} 
            if (empty($_POST["lic"])){$error.="El tipo de licencia que está solicitando."."<br>";}
            echo "<script> imprime('$error'); </script>";
        }//FIN IF post vacios
    }//FIN DE IF LICENCIA

    if($operacion=="permiso")
    {
        if ((!empty($_POST["num"])) && (!empty($_POST["fec"])) && (!empty($_POST["fecf"])))
        {
            $num = $_POST['num'];//el número del trabajador
            $fecha=$_POST['fec'];//la fecha de inicio
            $fechaf=$_POST['fecf'];
           
            /*
            SOLO DE BASE
            CICA 40
            Art.56 permisos con goce de sueldo, de entre uno y hasta por tres días cada uno de ellos, por los siguientes motivos:
                I. Por intervención quirúrgica o internamiento en instituciones hospitalarias, del cónyuge, hijas o hijos, 
                padres; 
                II. En caso de siniestro en el hogar de la trabajadora o del trabajador;
                III. Por privación de la libertad o accidente del cónyuge, hijas o hijos, o padres de la trabajadora 
                o del trabajador; y
                IV. Por sustentar examen profesional;
                Este tipo de permisos se otorgarán a solicitud de la trabajadora o del trabajador, mediante la debida comprobación 
                del motivo.
                A la trabajadora o al trabajador que en el término de un semestre, exceda de tres días de permiso con goce 
                de sueldo, se le descontarán los días excedentes de las prestaciones económicas referidas en el Artículo 87,
                fracción VII de las presentes Condiciones (Art. 87: Como ayuda por la muerte de un familiar en primer grado, 
                la cantidad de $2,800.00 para los gastos del funeral).

                En el caso de que el trabajador/ra requiera por las 4 causales mencionadas en las CGT de más días, se le concederán 
                descontándolos de los días que por antigüedad tiene derecho, anulando automáticamente el pago de estímulos.
            */
            $tipo=tipoEmpleado($num);
            if($tipo=="BASE")
            {
                $validarfechas=RevisarFechas(1,$fecha,$fechaf,"del permiso con goce de sueldo","una permiso con goce de sueldo","",0);
                $duracion=calcularDuracionEntreDosFechas(0,$fecha,$fechaf);
                $sql="SELECT duracion FROM especial where 
                ((clave_especial_clave_especial='41' and empresa!='pension') or clave_especial_clave_especial='40') 
                and trabajador_trabajador='$num'
                and fecha_inicio like '$anio%'";
                //Obtener los 40 y 41 de este año y sumar los días solicitados
                $diasGastados=sumaRegistrosDeConsulta($sql);//Hacer una resta para obtener los días que le quedan

                $antiguedad=calculaAntiguedad($num);
                //calcular dias permitidos en base al artículo 87 fracción 7
                $diasPermitidos=diasAntiguedad87V11($antiguedad);
                $diasSobrantes=$diasPermitidos-$diasGastados;
                if($duracion<=$diasSobrantes)//si aún le quedan días
                {
                    $Clave=40;
                    //insertar el permiso
                    $sql="INSERT INTO especial VALUES (null, '$fecha', '$fechaf', '00:00:00', '00:00:00', '0', '$num', '$Clave','*Ver documento*','$duracion')";
                    $ok= "<script> imprime('Permiso con goce de sueldo agregado correctamente.'); </script>";
                    $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                    insertaEnBD($sql,$ok,$error);
                }
                else//fin if duracion<=dias sobrantes
                {
                    echo "<script> imprime('El empleado con número $num está solicitando un permiso con goce hasta por 3 días por un total de $duracion días; solo puede, por su antiguedad solicitar permisos pagados de este tipo por $diasPermitidos días. Sustento: Artículos 57 y 87 Fracción 7 de las CGT.'); </script>";
                    exit();
                }
            }
            else
            {
                $tipo=utf8_encode($tipo);
                echo "<script> imprime('El empleado con número $num es de tipo $tipo. Se requiere ser de BASE para solicitar este permiso.'); </script>";
            }//fin if tipo==BASE
        }
        else//Fin if validar campos
        {
            $error="Faltan los siguientes datos:"."<br>";
            if (empty($_POST["num"])){$error.="Número de trabajador que exista."."<br>";}
            if (empty($_POST["fec"])){$error.="La fecha de inicio de lo que está solicitando."."<br>";} 
            if (empty($_POST["fecf"])){$error.="La fecha de fin de lo que está solicitando."."<br>";}
            echo "<script> imprime('$error'); </script>";
        }
    }//FIN DE IF PERMISO

    if($operacion=="guardia")
    {
        /*
            Pedir:
            fecha actual: $fec_act
            fecha de guardia: $fec
            que los trabajadores no sean los mismos y que sean del mismo tipo de empleado y tengan el mismo departamento
            obtener la hora de entrada y de salida del trabajador solicitante
        */

        if ((!empty($_POST["num"])) && (!empty($_POST["numSup"])) && (!empty($_POST["fec"])))
        {
            $num=$_POST["num"];
            $suplente=$_POST["numSup"];
            $fechaGuardia=$_POST["fec"];
            //Ver que los trabajadores no sean los mismos
            if($num!=$suplente)
            {
                //Ver que sean de la misma especialidad o categoría o área

                //Obtener hora de entrada y salida del trabajador
            }
            else
            {
                echo "<script> imprime('Los empleados son los mismos, POR FAVOR. Elija con cuidado.'); </script>";
            }
        }
        else
        {
            $error="Faltan los siguientes datos:"."<br>";
            if (empty($_POST["num"])){$error.="Número de trabajador solicitante que exista."."<br>";}
            if (empty($_POST["numSup"])){$error.="Número de trabajador suplente que exista."."<br>";} 
            if (empty($_POST["fec"])){$error.="La fecha de la guardia"."<br>";}
            echo "<script> imprime('$error'); </script>";
        }
    }//FIN DE IF GUARDIA

    if($operacion=="pt")
    {
        //Guardar con la clave PS, ojo :D
        $Clave="PS"; 

    }//FIN DE IF PT

    if($operacion=="curso")
    {
        /*las becas capacitacion son 12 días máximo al semestre
            CICA 29*/
    }//FIN DE IF CURSO

    /*Articulo 60 CGT vacaciones*/

    function insertaBeca($f_inicio, $f_final, $numero_emp, $Clavelic,$duracion)
    {
        global $nombre;
        global $contra;
        require("../../Acceso/global.php");
        $sql="INSERT INTO especial (fecha_inicio, fecha_fin, hora_entrada, hora_salida, validez, trabajador_trabajador, clave_especial_clave_especial, empresa, duracion) VALUES ('$f_inicio', '$f_final', '00:00:00', '00:00:00', '0', '$numero_emp', '$Clavelic', '*Ver documento*', '$duracion');";
        if((mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)))))
        {
            //Agregar a la bitacora más la prioridad
            mysqli_close($con);
            echo "<script> todoCorrecto('Licencia por Beca Agregada Correctamente');</script>";
        }
        else
        {
            die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
        }
    }// fin de insertaBeca

    function calcularDuracionEntreDosFechas($tipo, $fecha_inicio, $fecha_final)
    {
        /*Tipo=0 Se compararán las dos fechas elegidas
          Tipo=1 Se comparará la fecha de inicio con el día de hoy
        */
        if($tipo==0)
        {
            $date1= new DateTime($fecha_inicio);
            $date2= new DateTime($fecha_final);
            $interval = $date1->diff($date2);
            $totDias=$interval->format('%a');
            return $totDias+1;
        }
        else
        {
            if($tipo==1)
            {
                $today=date("Y-m-d"); 
                $date1= new DateTime($today);
                $date2= new DateTime($fecha_inicio);
                $interval = $date1->diff($date2);
                $totDias=$interval->format('%a');
                return $totDias;
            }
            else
            {
                echo "Parámetro *tipo* no válido en función calcularDuracionEntreDosFechas";
            }
        }
    }//fin de calcularDuracionEntreDosFechas

    function fechaMayorMenor($fechaInicio, $fechaFinal)
    {
        $today=date("Y-m-d");
        $fecha_hoy=strtotime($today);

        $fecha_in = strtotime($fechaInicio);
        $fecha_fi = strtotime($fechaFinal);

        if($fecha_in<$fecha_hoy)
        {
            return 1;//La fecha inicial es menor a hoy
            //echo "La fecha inicial es menor que hoy";
        }
        else
        {
            if($fecha_fi<$fecha_hoy)
            {
                return 2;//La fecha final es menor que hoy
                //echo "La fecha final es menor que hoy";
            }
            else
            {
                if($fecha_in==$fecha_hoy)
                {
                    return 3;//La fecha inicial es igual que hoy
                    //echo "La fecha inicial es igual que hoy";
                }
                else
                {
                    if($fecha_in<$fecha_fi)
                    {
                        return 4;//Correcto
                        //echo "Correcto";
                    }
                    else
                    {
                        if($fecha_in==$fecha_fi)
                        {
                            return 5;//La fecha inicial y final es la misma
                            //echo "La fecha inicial y final es la misma";
                        }
                        else
                        {
                            if($fecha_fi<$fecha_in)
                            {
                                return 6;//La fecha final es menor a la fecha de inicio
                                //echo "La fecha final es menor a la fecha de inicio";
                            }
                        }
                    }
                }
            }
        }
    }//fin de fechaMayorMenor

    function RevisarFechas($opcion,$fechaInicio, $fechaFinal,$comentario1,$comentario2,$diasAntelacion,$omitirFechaMenoraYfechaigualA)
    {
        //Forma de uso
        //$validarfechas=RevisarFechas(1,$fecha,$fechaf,"de la tolerancia de estancia","una tolerancia de estancia","",0);
        //$validarfechas=RevisarFechas(2,$fecha,"","de la licencia por matrimonio","una licencia por matrimonio","",0);
        /*
            opcion=1->comparar dos fechas con el día de hoy
            opcion=2->comparar una fecha (fechaInicio)  con el día de hoy

            $omitirFechaMenoraYfechaigualA-> pongala a 0 cuando deseee que esta función valide si la fecha inicio es menor a hoy
            y si la fecha de inicio es igual a hoy; pongala a 1 cuando quiere omitir lo antes mencionado
        */
        if($diasAntelacion=="")
        {
            $diasAntelacion="al menos 1 día";
        }
        $today=date("Y-m-d");
        $fecha_hoy=strtotime($today);
        $fecha_in = strtotime($fechaInicio);
        $fecha_fi = strtotime($fechaFinal);

        if($opcion==1)
        {
            if(($fecha_in<$fecha_hoy) && ($omitirFechaMenoraYfechaigualA==0))
            {
                echo "<script> imprime('La fecha inicial $comentario1 es menor a la fecha actual. No se puede registrar $comentario2 que empieza antes que hoy.'); </script>"; 
                exit(); 
            }
            else
            {
                if(($fecha_fi<$fecha_hoy) && ($omitirFechaMenoraYfechaigualA==0))
                {
                    echo "<script> imprime('La fecha final $comentario1 es menor a la fecha actual. $comentario2 no debe terminar antes que hoy, verifique.'); </script>";
                    exit();
                }
                else
                {
                    if(($fecha_in==$fecha_hoy) && ($omitirFechaMenoraYfechaigualA==0))
                    {
                        echo "<script> imprime('La fecha $comentario1 inicia hoy. Debió registrar $comentario2 con $diasAntelacion de antelación. NO es posible registrar $comentario2 que inicia hoy.'); </script>";
                        exit();
                    }
                    else
                    {
                        if($fecha_in<$fecha_fi)
                        {
                            return 4;//Correcto
                        }
                        else
                        {
                            if($fecha_in==$fecha_fi)
                            {
                                echo "<script> imprime('La fecha de inicio $comentario1 es igual a la fecha final. No se permite $comentario2 de 1 día.'); </script>";
                                exit();
                            }
                            else
                            {
                                if($fecha_fi<$fecha_in)
                                {
                                    echo "<script> imprime('La fecha de fin $comentario1 es menor a su fecha de inicio. ¿Está seguro de que no escribió las fechas al revés?'); </script>";
                                    exit();
                                }
                            }
                        }
                    }
                }
            }
        }//Fin if opcion1
        else
        {
            if($opcion==2)
            {
                if($fecha_in<$fecha_hoy)
                {
                    echo "<script> imprime('La fecha inicial $comentario1 es menor a la fecha actual. No se puede registrar $comentario2 que empieza antes que hoy.'); </script>"; 
                    exit();
                }
                else
                {
                    if($fecha_in==$fecha_hoy)
                    {
                        echo "<script> imprime('La fecha inicial $comentario1 es igual a la fecha actual. No se puede registrar $comentario2 que empieza hoy.'); </script>"; 
                        exit();
                    }
                    else
                    {
                        if($fecha_in>$fecha_hoy)
                        {
                            return 2;//correcto
                        }
                    }
                }
            }
        }
    }//fin de RevisarFechas

    function ejecutaFinalBeca($validafechas,$fecha,$fechaf,$num,$ClaveLicencia,$duracion)
    {
        if($validafechas==1)
        {
            echo "<script> imprime('La fecha inicial de la beca es menor a la fecha actual. No se puede registrar una beca que empieza antes que hoy.'); </script>";  
        }
        else
        {
            if($validafechas==2)
            {
                echo "<script> imprime('La fecha final de la beca es menor a la fecha actual. Esta beca no debe terminar antes que hoy, verifique.'); </script>";    
            }
            else
            {
                if($validafechas==3)
                {
                    echo "<script> imprime('La fecha de la beca inicia hoy. Debió registrar esta beca con al menos 1 día de antelación. NO es posible registrar una beca que inicia hoy.'); </script>";  
                }
                else
                {
                    if($validafechas==4)
                    {
                        if($duracion>=90)
                        {
                            insertaBeca($fecha,$fechaf,$num,$ClaveLicencia,$duracion);
                        }
                        else
                        {
                            echo "<script> imprime('La beca tiene $duracion días de duración. Se necesitan mínimo 90 días de duración para cualquier beca. Art. 2 del Reglamento de Becas del ISSSTE.'); </script>";
                        }
                    }
                    else
                    {
                        if($validafechas==5)
                        {
                            echo "<script> imprime('La fecha de inicio de la beca es igual a la fecha final. No se permiten becas de 1 día.'); </script>";  
                        }
                        else
                        {
                            if($validafechas==6)
                            {
                                echo "<script> imprime('La fecha de fin de la beca es menor a la fecha de inicio de la misma. ¿Está seguro de que no escribió las fechas al revés?'); </script>";  
                            }
                        }
                    }
                }
            }
        }
    }// Fin de ejecutaFinalBeca

    function insertaEnBD($elQuery, $mensajeOk, $mensajeErr)
    {
        global $con;
        $sql=$elQuery;
        if($query=mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con))))
        {
            echo $mensajeOk;
        }
        else
        {
            echo $mensajeErr;
        }
    }

    function tipoEmpleado($numeroEmpleado)
    {
        global $con;
        //obtener el tipo de empleado
        $sql="SELECT t.descripcion from tipo t inner join trabajador tra on tra.tipo_tipo=t.idtipo 
        where tra.numero_trabajador='$numeroEmpleado'";
        $query=mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
        $resul=mysqli_fetch_array($query);
        return $resul[0];
    }//fin de tipoEmpleado

    function obtenerHorario($numero_de_empleado)
    {
        global $con;
        //obtener la hora entrada y hora salida del empleado
        $sql="SELECT t.entrada,t.salida from turno t 
        inner join acceso a on a.turno_turno=t.idturno
        and a.trabajador_trabajador='15321030'";
        $query=mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
        $resul=mysqli_fetch_array($query);
        return [$resul[0],$resul[1]];
    }//fin de obtenerHorario

    function SumResMinutosHoras($operacion,$horario, $minutosASumaroRestar)
    {
        /*Operacion=1->Sumar
            Operacion=2->Restar
        */
        if($operacion==1)
        {
            $date = new DateTime($horario);
            $date->modify("+$minutosASumaroRestar minute");
            $horaFormateada=$date->format('H:i:s');
            return $horaFormateada;
        }
        else
        {
            if($operacion==2)
            {
                $date = new DateTime($horario);
                $date->modify("-$minutosASumaroRestar minute");
                $horaFormateada=$date->format('H:i:s');
                return $horaFormateada;
            }
            else
            {
                echo "Parametro *operacion=$operacion* de la función SumarMinutosHoras no admitido";
                exit();
            }
        }
    }//fin de SumResMinutosHoras

    function SumRestDiasMesAnio($operacion,$fecha,$diasOMesesASumar)
    {
        /*Operacion=1->Sumar
            Operacion=2->Restar

            months
            days
            years
        */
        //$dia = date("Y-m-d");
        $dia=$fecha;
        if($operacion==1)
        {
            $mod_dia = strtotime($dia."+ $diasOMesesASumar");
            $diaFormateado= date("Y-m-d",$mod_dia);
            return $diaFormateado;
        }
        else
        {
            if($operacion==2)
            {
                $mod_dia = strtotime($dia."- $diasOMesesASumar");
                $diaFormateado= date("Y-m-d",$mod_dia);
                return $diaFormateado;
            }
            else
            {
                echo "Parametro *operacion=$operacion* de la función SumRestDiasMesAnio no admitido";
                exit();
            }
        }
    }//fin de SumRestDiasMesAnio

    function obtenerFilas($Elquery)
    {
        global $con;
        //obtener la hora entrada y hora salida del empleado
        $sql=$Elquery;
        $query=mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
        $Totfilas=mysqli_num_rows($query);
        return $Totfilas;
    }

    function obtenerSexo($numeroEmpleado)
    {
        global $con;
        $sql="Select genero from trabajador where numero_trabajador='$numeroEmpleado'";
        $query=mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
        $resul=mysqli_fetch_array($query);
        return $resul[0];
    }

    function calculaAntiguedad($numeroEmpleado)
    {
        /*Calcular la antiguedad en años*/
        global $con;
        $sql="SELECT fecha_alta FROM tiempo_servicio where trabajador_trabajador='$numeroEmpleado'";
        $query=mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
        $resul=mysqli_fetch_array($query);
        $fecha_alta=$resul[0];
        $antiguedad=calcularDuracionEntreDosFechas(1,$fecha_alta,"");
        $antiguedad=$antiguedad/365;
        return $antiguedad;
    }

    function sumaRegistrosDeConsulta($elQuery)
    {
        global $con;
        $diasUsados=0;
        $sql=$elQuery;
        $query=mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
        $filas=mysqli_num_rows($query);
        if($filas>0)
        {
            while($resul=mysqli_fetch_array($query))
            {
                $diasUsados=$diasUsados+$resul[0];
            }
        }
        return $diasUsados;
    }

    function diasAntiguedad87V11($antiguedad)
    {
        global $num;
        $diasPermitidos=0;
        if($antiguedad<0.5)
        {
            echo "<script> imprime('El empleado con número $num cuenta con menos de 6 meses de antiguedad. Se requieren 6 meses o más de antiguedad para solicitar una licencia con goce'); </script>";
            exit();
        }
        if($antiguedad>=0.5 && $antiguedad<=5)
        {
            $diasPermitidos=21;
        }
        else
        {
            if($antiguedad>=5 && $antiguedad<=10)
            {
                $diasPermitidos=26;
            }
            else
            {
                if($antiguedad>=10 && $antiguedad<=15)
                {
                    $diasPermitidos=31;
                }
                else
                {
                    if($antiguedad>=15 && $antiguedad<=20)
                    {
                        $diasPermitidos=36;
                    }
                    else
                    {
                        if($antiguedad>=41)
                        {
                            $diasPermitidos=36;
                        }
                    }
                }
            }
        }
        return $diasPermitidos;
    }

    function feriadoConArray($fecha,$diasAAgregar)
    {
        global $con;
        $feriado=false;
        
        $FechaFinal;
        $diasASumar=$diasAAgregar-1;
        $contador=0;
        $dia=$fecha;
        $mod_dia=$fecha;

        $diasFeriados=array();//para guardar los días feriados de mi BD
        $pos=0;
        $sql="SELECT fecha from dia_festivo";
        $query= mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
        $filas = mysqli_num_rows($query);//obtener las filas del query
        //Si el query no está vacío
        if($filas>0)
        {
            while($resul=mysqli_fetch_array($query))
            { 
                $diasFeriados[$pos]=$resul[0];//Guardar el día feriado correspondiente en el array
                $pos++;//aumentar la posición del array
            }
        }
        /*El tamaño final del array es lo que hay en la variable pos; cabe resaltar que el array debe tener 
            como mínimo una fecha o esta función dará error
        */

        for($i=0;$i<50;$i++)
        {
            $mod_dia = strtotime($mod_dia."+ 1 days");//sumar 1 día

            $diaIngles= date("l",$mod_dia);//El día en inglés que cae al sumarle 1 día a la fecha de inicio
            $fechaCompleta= date("Y-m-d",$mod_dia);//El día completo que cae al sumarle 1 día a la fecha de inicio

            $feriado=false;
            //Buscar si fechaCompleta está en el array o no
            for($j=0;$j<$pos;$j++)
            {
                if($fechaCompleta==$diasFeriados[$j])
                {
                    $feriado=true;
                    $j=$pos-1;//romper y salir el cucle
                }//fin if for j<pos
            }//fin del for que evalua el array

            if(($diaIngles=="Monday" || $diaIngles=="Tuesday" || $diaIngles=="Wednesday" || $diaIngles=="Thursday" || $diaIngles=="Friday") && $feriado==false)
            {
                $contador++;//aumentamos el contador, lo que indica que sí se sumó un día habil
                if($contador==$diasASumar) //si contador vale 10, en este caso
                {
                    //romper el bucle
                    $i=49;
                }
            }//fin del if
            $mod_dia=date("Y-m-d",$mod_dia);
        }//fin del for i<50
        return $fechaCompleta;
    }
?>