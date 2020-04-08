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
        header("Location: ../index.html");
        die();
    }
?>
<script type="text/javascript">
    function No_Existe(numero,fecha)
    {
        alert("No hay una incidencia en la fecha "+fecha+" para el número de trabajador "+numero);
        history.back();
    }

    function Ya(numero,fecha)
    {
        alert("Esta incidencia ya fue justificada antes");
        history.back();
    }

    function Correcto()
    {
        alert("Justificacion agregada Correctamente");
        location.href="../../ht/aprobaciones.php";
    }

    function Error()
    {
        alert("Algo salió mal");
        history.back();
    }

    function no()
    {
        alert("Ya posee 2 justificaciones o  2 omisiones o 1 omisión+ 1 justificación. Sustento: Art. 46 CGT");
        history.back();
        //window.close();
    }

    function noMaxComision(fecha1, fecha2)
    {
        alert("El periodo entre las fechas "+fecha1+" y "+fecha2+" es superior a 5 meses y medio. NO ES POSIBLE TENER UNA COMISIÓN QUE DURE ESE TIEMPO.");
        history.back();
    }

    function noComision(numero)
    {
        alert("El trabajador con número "+numero+ " Ya posee una comisión activa. NO ES POSIBLE TENER 2 COMISIONES A LA VEZ");
        history.back();
    }

    function siComision()
    {
        alert("la comisión se agregó correctamente");
        location.href="../../ht/aprobaciones.php";
    }

    function noOmision()
    {
        alert("Ya posee 2 omisiones o 2 faltas o 1 omisión + 1 justifiación");
        history.back();
    }

    function antesOmision()
    {
        alert("Esta omisión ya fue justificada antes.");
        history.back();
    }

    function omisionNoExiste(numero,fecha)
    {
        alert("No hay una omisión en la fecha "+fecha+" para el número de trabajador "+numero);
        history.back();
    }

    function omisionCorrecta()
    {
        alert("Omision justificada correctamente.");
        location.href="../../ht/aprobaciones.php";
    }

    function imprime(texto)
    {
        alert(texto);
        history.back();
    }
</script>

<?php
    //******formatear a la zona horaria de la ciudad de México**********
    date_default_timezone_set('America/Mexico_City');
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
    }//FIN DEL IF OMISIÓN

    if($operacion=="comision")
    {//comisión es la clave 17
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
        $empresa=$_POST['empresa'];
        $clave_especial=45;
        $validez=0;

        $date1= new DateTime($fecha);
        $date2= new DateTime($fechaf);
        /*Ver si ese empleado ya posee una comisión activa*/
        $sql7="SELECT * from especial where trabajador_trabajador=$num and validez=1 and clave_especial_clave_especial=89";
        $query7=mysqli_query($con, $sql7) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
        $resul7=mysqli_fetch_array($query7);
        $filas7= mysqli_num_rows($query7);
        /*Si el total de filas es 0 significa que el empleado no posee una comisión activa*/
        if($filas7==0)
        {
            /*antes se debe verificar si se tuvo una comisión en en los últimos 6 meses*/
            //obtener la fecha de hoy
            $hoy=date("Y-m-d"); 
            $fecha_ac = strtotime($hoy);
            $fecha_in = strtotime($fecha);//la fecha de inicio de la comisión
            if($fecha_ac < $fecha_in)
            {
                //La comisión aún no empieza
                //insertar la comisión
                $interval = $date1->diff($date2);
                $totDias=$interval->format('%a');//los días que durará la comisión
                //si el periodo de comisión es superior a 165 días (5 meses y medio)
                if($totDias>165)
                {
                    mysqli_close($con);
                    echo "<script> noMaxComision('$fecha','$fechaf'); </script>";
                }
                else
                {
                    //verificar si el día actual es inferior a la fecha de inicio de la comisión

                    //Insertar la comisión
                    $sql8=" INSERT INTO especial VALUES (null, '$fecha', '$fechaf', '$hora_e', '$hora_s', '1', '$num', '89','','$totDias')";
                    if((mysqli_query($con, $sql8) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)))))
                    {
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
                    echo "<script> imprime('La fecha de inicio de la comisión ya pasó, NO ES POSIBLE REGISTRAR UNA COMISIÓN QUE INICIÓ ANTES DE HOY'); </script>";
                }
            }
        }
        else
        {
            //El empleado ya posee una comisión activa y no puede tener 2 comisiones a la vez
            mysqli_close($con);
            echo "<script> noComision($num); </script>";
        }

    }//FIN DEL IF COMISIÓN

    if($operacion=="licencia")
    {
        /*sin goce

        *Arti.52 CGT Si pasa de Base a Confianza se puede pedir una licencia sin goce superior a un año, pero se renueva anualmente
        En el sistema actualmente se utiliza la clave 92 pero en CICA esa clave es para tolerancia de lactancia 
        ¿Que clave se utiliza? (no) 

        *Arti.53 CGT. Los trabajadores disfrutarán de licencias sin goce en forma total o fraccionada por una vez al año, el tiempo
                     depende de su antiguedad. 
                    Para los casos, en que la trabajadora o el trabajador haya disfrutado de licencias sin goce de sueldo, durante 
                    el periodo que corresponda se disminuirá 1 DÍA DE VACACIONES por cada quince días de licencia.
                     ¿Que clave se utiliza?

        Arti.54 CGT trabajadores que deban practicar servicio social o pasantía en alguna otra dependencia o entidad de gobierno 
                    federal, estatal o municipal se le da licencia sin goce por el tiempo que dure el servicio 
                    CICA 51

        con goce (permiso con goce 41)
        *Art. 55 CGT los basificados que tengan necesidad de iniciar los trámites para obtener su pensión ya sea por jubilación, 
                    de retiro por edad y tiempo de servicio, por cesantía en edad avanzada o bien bajo el régimen de cuentas 
                    individuales, de retiro, cesantía en edad avanzada y vejez, el Instituto le concederá licencia 
                    con goce de sueldo por un término de tres meses.
                    ¿Es la clave 85 de CICA?

                    al que contraiga matrimonio se le concederán diez días hábiles de licencia con goce de sueldo por una sola vez, 
                    comprometiéndose a entregar, dentro de los sesenta días posteriores a la terminación de la licencia, su acta 
                    de matrimonio; (ya no otorga: puntualidad (79), asistencia (80), desempeño (83), mérito relevante (84), ni el art. 85)
                    CICA 48

                    por fallecimiento de un familiar en primer grado, con parentesco por consanguinidad, afinidad o su cónyuge 
                    se le concederán cinco días hábiles de licencia con goce de sueldo. anexando copia del acta de defunción o
                    comprometiéndose, en su caso, a entregarla dentro de los quince días posteriores a la terminación de la 
                    licencia. (ya no otorga: puntualidad (79), asistencia (80), desempeño (83), mérito relevante (84), ni el art. 85)
                    CICA 49

                    Las trabajadoras en estado de gravidez disfrutarán de licencias con goce de sueldo, treinta días antes de 
                    la fecha probable de parto y sesenta días después de éste.
                    CICA 53

                    licencia con goce de sueldo hasta por ocho días, cuando sus hijas o hijos menores de seis años requieran de 
                    cuidados por enfermedad aguda; así como también para el caso de las hijas y los hijos con discapacidad física o 
                    psíquica, sin importar la edad que tengan, basta que el médico tratante del Instituto certifique la 
                    gravedad del caso y los días de cuidado; debiendo presentar el original del documento que acredite este 
                    supuesto. Empleados varones deberán además comprobar con documento fehaciente, tener la custodia de la 
                    menor o del menor y que no cuentan con el auxilio de su cónyuge.
                    (ya no otorga: puntualidad (79), asistencia (80), desempeño (83), mérito relevante (84), ni el art. 85; 
                    cuando en el término de un mes los días otorgados por uno u otro concepto o por ambos sumen solos o individual
                    mente 3 días)
                    CICA 47
                    
                    CICA 62(Claves de servicio autorizadas para este concepto dentro del Instituto son: 09200 Radiología, 
                    09210 Medicina Nuclear, 20600 Radio Diagnóstico, 17824 Radiología, 7910 Radio Terapia)
                    En caso de riesgo de trabajo (radio,), la trabajadora o el trabajador tendrá derecho a disfrutar sus licencias con 
                    goce de sueldo en los términos de los Artículos 110 de la Ley y el aplicable de la Ley del ISSSTE (además:
                    art.60 Ley ISSSTE: El Trabajador o sus Familiares Derechohabientes deberán solicitar al Instituto la 
                    calificación del probable riesgo de trabajo dentro de los treinta días hábiles siguientes a que haya ocurrido, 
                    en los términos que señale el reglamento respectivo y demás disposiciones aplicables. No procederá la 
                    solicitud de calificación, ni se reconocerá un riesgo del trabajo, si éste no hubiere sido notificado 
                    al Instituto en los términos de este artículo.)
                    ; y

                    En caso de enfermedades no profesionales se aplicará lo previsto en los Artículos 111 de la Ley y el 
                    aplicable de la Ley del ISSSTE. CLAVE 55 CICA.

            *ARTÍCULO 57. El Instituto concederá a su personal licencias con goce de sueldo por motivos de fuerza mayor, 
                    distintas a las referidas en las fracciones I a IV del Artículo anterior (artículo 56). Dichas licencias 
                    serán descontadas de los estímulos adicionales referidos en el ARTÍCULO 87, fracción VII de estas Condiciones, 
                    a partir del primer día.  
                    ¿Qué clave se utiliza? cica 41
                    
                    Para los efectos de los Artículos 56 y 57 la trabajadora o el trabajador podrá disfrutar de estas licencias 
                    hasta por el número de días de sueldo en los términos del Artículo 87 (estímulos por antiguedad), 
                    fracción VII de estas Condiciones. CICA 50.

            Las licencias y permisos a que se refieren los Artículos anteriores podrán ser solicitadas por las trabajadoras 
            o los trabajadores o la representación sindical, con la debida anticipación a la fecha que se señale como inicio 
            de la misma salvo causa de fuerza mayor.

        */
        $num = $_POST['num'];//el número del trabajador
        $fecha=$_POST['fec'];//la fecha de inicio
        $fechaf=$_POST['fecf'];//la fecha de fin
        $tipoLicencia=$_POST['lic'];//la clave de licencia que se eligió en aprobación
        
        /*incapacidad médica
            revisar la incapacidad registrada en (el sistema que el ISSSTE tiene para licencias médicas, la emite cualquier doctor)
            cual es el doctor que da más incapacidades en un periodo de tiempo.
        */
    }

    if($operacion=="permiso")
    {
        //especial
        //40 con goce de sueldo hasta por 3 días
        //41 con goce de sueldo por antiguedad
        /*
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
            fracción VII de las presentes Condiciones.
        */
    }
    
    /*Articulo 60 CGT vacaciones*/
?>