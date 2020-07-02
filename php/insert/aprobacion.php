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
        header("Location: ../../index.php");
        die();
    }
?>
<?php
    require("../../assets/js/alerts-justificacion.php");
    //******formatear a la zona horaria de la ciudad de México**********
    date_default_timezone_set('America/Mexico_City');
    set_time_limit(300);//5 minutos máximo para la ejecución de un script
    //obtener la fecha de hoy
    $fec_act=date("Y-m-d H:i:s"); //la fecha actual
    $anio=date("Y");//solo el año actual
    $mes=date("m");//solo el mes actual  
    $carpetaDestino="../../documents/";//carpeta destino para las imagenes
    
    /*OBTENER LA QUINCENA ACTUAL EN LA QUE ESTAMOS
     Si la quincena está vacía dará el error: esta consulta arrojó... que se encuentra en la  función retornaAlgoDeBD*/
    $sql5="SELECT idquincena from quincena where validez=1";
    $quincena=retornaAlgoDeBD(0,$sql5);
    /*FIN DE OBTENER QUINCENA ACTUAL*/
    if(empty($_POST["opcion"]))
    {
        echo "Por favor, diríjase a la sección Aprobaciones para que esta página se ejecute normalmente: " . "<a href='/ht/aprobaciones.php'>IR AHORA</a>";
        exit();//terminar el script
    }
    $operacion=$_POST['opcion'];

    if($operacion=="justificar")
    {
       require ("aprobaciones/justificacion.php");
       exit();
    }//FIN DEL IF JUSTIFICAR

    if($operacion=="omision")
    {
        require ("aprobaciones/omision.php");
        exit();
    }//FIN DEL IF OMISIÓN

    if($operacion=="falta")
    {
        //inconcluso
        require ("aprobaciones/falta.php");
        exit();
    }//FIN DEL IF FALTA

    if($operacion=="comision")
    {
        require ("aprobaciones/comision.php");
        exit();
    }//FIN DEL IF COMISIÓN

    if($operacion=="licencia")
    {
        //inconcluso
        require ("aprobaciones/licencia.php");
        exit();
    }//FIN DE IF LICENCIA

    if($operacion=="permiso")
    {
        require ("aprobaciones/permiso.php");
        exit();
    }//FIN DE IF PERMISO

    if($operacion=="guardia")
    {
        require ("aprobaciones/guardia.php");
        exit();
    }//FIN DE IF GUARDIA

    if($operacion=="pt") //debería se PS pero surgió un error de sintaxis. Disculpe :).
    {        
        require ("aprobaciones/ps.php");
        exit();
    }//FIN DE IF PT

    if($operacion=="curso")
    {
        require ("aprobaciones/curso.php");
        exit();
    }//FIN DE IF CURSO

    /*Articulo 60 CGT vacaciones*/

    function calcularDuracionEntreDosFechas($tipo, $fecha_inicio, $fecha_final)
    {
        /*Tipo=0 Se compararán las dos fechas elegidas
          Tipo=1 Se comparará la fecha de inicio con el día de hoy
          Tipo=2 Se compararán las dos fechas elegidas y se le restarán los días feriados si es que se encuentran en el rango
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
                return $totDias+1;
            }
            else
            {
                if($tipo==2)
                {
                    $date1= new DateTime($fecha_inicio);
                    $date2= new DateTime($fecha_final);
                    $interval = $date1->diff($date2);
                    $totDias=$interval->format('%a');

                    //saber la duración normal de las fechas y guardar todas esas fechas en un array
                    $rangoFechas=array();//para guardar los datos
                    $tDias=calcularDuracionEntreDosFechas(0,$fecha_inicio,$fecha_final);
                    $rangoFechas[0]=$fecha_inicio;//la fecha de inicio va primero
                    for($i=1;$i<$tDias;$i++)
                    {
                        $fecha_inicio=SumRestDiasMesAnio(1,$fecha_inicio,"1 days");
                        $rangoFechas[$i]=$fecha_inicio;
                    }

                    //restar los días festivos
                    $sql="SELECT fecha from dia_festivo";
                    $diasFeriados=retornaAlgoDeBD(1,$sql);
                    $tamanioFeriado = count($diasFeriados);//tamanio del array
                    $tamanioFechas = count($rangoFechas);//tamanio del array

                    //recorrer ambos arrays en busca de fechas coincidentes a los feriados
                    for($i=0;$i<$tamanioFechas;$i++)
                    {
                        $fecha=$rangoFechas[$i];
                        for($j=0;$j<$tamanioFeriado;$j++)
                        {
                            if($fecha==$diasFeriados[$j])
                            {
                                /*
                                    Si la fecha es igual a un dia feriado significa 
                                    que se deberá restar 1 día al total de dias originales
                                */
                                $totDias--;
                            }
                        }
                    }
                    return $totDias+1;
                }
                else
                {
                    echo "Parámetro *tipo=$tipo* no válido en función calcularDuracionEntreDosFechas";
                }
            }
        }
    }//fin de calcularDuracionEntreDosFechas

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

    function insertaEnBD($elQuery, $mensajeOk, $mensajeErr,$AgregarImagen)
    {
        /*
            $AgregarImagen=0-->retornará el id para agregar la imagen o hacer otra cosa
            $AgregarImagen=1-->No devolverá nada
        */
        global $con;
        $sql=$elQuery;
        if($query=mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con))))
        {
            $ultimo_id = mysqli_insert_id($con);
            if($AgregarImagen==0)//para la imagen
            {
                return $ultimo_id;//regresar y agregar la imagen
            }
            else
            {
                echo $mensajeOk;
            }
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
        and a.trabajador_trabajador='$numero_de_empleado'";
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
        /*
            Suma las filas que se obtiene de una consulta que arroja UNA sola columna
        */
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
    }//Fin de feriadoConArray

    function retornaAlgoDeBD($tipoDatoADevolver, $elQuery)
    {
        global $con;
        /* 
            $elQuery             : es la consulta que se desea ejecutar 
            $tipoDatoADevolver=0 : Devolverá 1 solo dato de la consulta dada
            $tipoDatoADevolver=1 : Devolverá 1 array de la consulta dada

            Ejemplo de uso
            $devuelve=retornaAlgoDeBD(0, $sql)
        */
        $sql=$elQuery;
        if($tipoDatoADevolver==0)
        {
            $query=mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
            $filas=mysqli_num_rows($query);
            if($filas==1)
            {
                $resul=mysqli_fetch_array($query);
                return $resul[0];//Devolver un solo dato
            }
            else
            {
                echo "Esta consulta arrojó un conjunto vacío o un array. Verifique con el administrador del sistema para obtener más información. No es posible proceder.";
                exit();
            }  
        }
        else//fin if devolver ==0
        {
            if($tipoDatoADevolver==1)
            {
                $datos=array();//para guardar los datos
                $pos=0;//para controlar las posiciones del array
                $query=mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
                $filas=mysqli_num_rows($query);
                if($filas>0)
                {
                    while($resul=mysqli_fetch_array($query))
                    { 
                        $datos[$pos]=$resul[0];//Guardar el día feriado correspondiente en el array
                        $pos++;//aumentar la posición del array
                    }
                    return $datos;//devolver un array con los datos
                }  
                else
                {
                    echo "Esta consulta arrojó un conjunto vacío. Verifique con el administrador del sistema para obtener más información. No es posible proceder.";
                    exit();
                }
            }
            else//fin if devolver==1
            {
                echo "Parametro *tipoDatoADevolver=$tipoDatoADevolver* de la función retornaAlgoDeBD no admitido";
                exit();
            }
        } 
    }//fin de retornaAlgoBD

    function hazAlgoEnBDSinRetornarAlgo($script)
    {
        global $con;
        $sql=$script;
        $query=mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
    }

    function analizaYCargaImagen($origen,$destino,$laImagen,$name,$extension,$MensajeOK,$retornaSioNo)
    {
        /*
            $retornaSioNo=0 ->No retornará nada
            $retornaSioNo=1 ->Retornará un 1 cuando se logre mover la imagen
        */
        # si es un formato de imagen
        if($extension=="image/jpeg" || $extension=="image/pjpeg" || $extension=="image/png")
        {
            if($extension=="image/jpeg")
            {
                $tipo=".jpeg";
            }
            else
            {
                if($extension=="image/pjpeg")
                {
                    $tipo=".pjpeg";
                }
                else
                {
                    if($extension=="image/png")
                    {
                        $tipo=".png";
                    }
                }
            }
            # movemos el archivo
            if(@move_uploaded_file($origen, $destino))
            {
                rename ("../../documents"."/".$laImagen, "../../documents"."/".$name.$tipo);
                if($retornaSioNo==1)
                {
                    return 1;
                }
                else
                {
                    if($retorna==0)
                    {
                        echo $MensajeOK;
                        exit();
                    }
                    else
                    {
                        echo "Parametro *retornaSioNo=$retornaSioNo* de la función analizaYCargaImagen no admitido";
                        exit();
                    }
                }
            }else
            {
                $sql="DELETE FROM especial WHERE (idespecial = '$name')";
                hazAlgoEnBDSinRetornarAlgo($sql);
                echo "<script> imprime('NO SE PUDO CARGAR LA IMAGEN SELECCIONADA; LO QUE SOLICITÓ NO SE GUARDÓ, REINTENTE.'); </script>";
                exit();
            }
        }else
        {
            //echo "<br>".$laImagen." - NO es imagen jpg, png o gif";
            //return 2;//No es imagen
            $sql="DELETE FROM especial WHERE (idespecial = '$name')";
            hazAlgoEnBDSinRetornarAlgo($sql);
            echo "<script> imprime('FORMATO DE ARCHIVO NO ACEPTADO. ASEGÚRESE DE ELEGIR UNA IMAGEN, REINTENTE.'); </script>";
            exit();
        }
    }//Fin de función analizaYCargaImagen

    function insertaEnBitacoraEspecial($ok,$operacion,$f_inicio_new,$f_fin_new,$he_new,$hs_new,$clave_especial_new,$empresa_new,
    $duracion_new,$f_inicio_old,$f_fin_old,$he_old,$hs_old,$clave_especial_old,$empresa_old,$duracion_old,$num,$id)
    {
        global $con;
        $nombre_host=gethostname();
        //GUARDAR EN LA BITACORA DE ESPECIAL
        if((mysqli_query($con,"call inserta_bitacora_especial('$operacion','$f_inicio_new','$f_fin_new','$he_new','$hs_new',
        '$clave_especial_new','$empresa_new','$duracion_new','$f_inicio_old','$f_fin_old','$he_old','$hs_old',
        '$clave_especial_old','$empresa_old','$duracion_old','$num','$nombre_host','$id')")))
        {
            echo $ok;
        }
        else
        {
            echo mysqli_errno($con) . ": " . mysql_error($con) . "\n";
            $sql="DELETE FROM especial WHERE (idespecial = '$id')";
            hazAlgoEnBDSinRetornarAlgo($sql);
            echo "<script> imprime('Surgió un error al guardar en la bitácora.' +
            ' Esta operación NO se ha guardado, REINTENTE.'); </script>";
            exit();
        }

    }//FIN de insertaEnBitacoraEspecial

    function insertaEnBitacoraGuardia($ok,$operacion,$id_new,$fechaRegistro_new,$fechaGuardia_new,$solicitante_new,$suplente_new,
    $he_new,$hs_new,$quincena_new,$id_old,$fechaRegistro_old,$fechaGuardia_old,$solicitante_old,$suplente_old,
    $he_old,$hs_old,$quincena_old)
    {
        global $con;
        $nombre_host=gethostname();
        //GUARDAR EN LA BITACORA DE Guardia
        if((mysqli_query($con,"call inserta_bitacora_guardias('$operacion','$id_new','$fechaRegistro_new','$fechaGuardia_new',
        '$solicitante_new','$suplente_new','$he_new','$hs_new','$quincena_new','$id_old','$fechaRegistro_old','$fechaGuardia_old',
        '$solicitante_old','$suplente_old','$he_old','$hs_old','$quincena_old','$nombre_host')")))
        {
            echo $ok;
        }
        else
        {
            $error="";
            $er1=mysqli_errno($con);
            $err1="$er1";
            $er2=mysqli_error($con);
            $err2="$er2";

            //Hacer UN EXPLODE DE ERR2
            $divide=explode("'",$err2);
            $tamDivide=count($divide);//saber el tamaño del array
            if($tamDivide>0)//si el array posee datos
            {
                $err2="";
                for($i=0;$i<$tamDivide;$i++)
                {
                    $err2.=$divide[$i];
                }
            }

            $sql="DELETE FROM guardias WHERE (idguardias = '$id_new')";
            hazAlgoEnBDSinRetornarAlgo($sql);

            echo "<script> imprime('Surgió un error al guardar en la bitácora.' +
            ' Esta operación NO se ha guardado, REINTENTE. $err1 : $err2'); </script>";
            exit();
        }
    }//FIN de insertaEnBitacoraGuardia

    function insertaEnBitacoraPS($ok,$operacion,$fecha_uso,$num,$quincena,$id)
    {
        global $con;
        $host=gethostname();
        //GUARDAR EN LA BITACORA DE Guardia
        if((mysqli_query($con,"call inserta_bitacora_pase('$operacion','$fecha_uso','$num','$quincena','$host')")))
        {
            echo $ok;
        }
        else
        {
            echo mysqli_errno($con) . ": " . mysql_error($con) . "\n";
            $sql="DELETE FROM pase_salida WHERE (idpase_salida = '$id')";
            hazAlgoEnBDSinRetornarAlgo($sql);

            echo "<script> imprime('Surgió un error al guardar en la bitácora.' +
            ' Esta operación NO se ha guardado, REINTENTE.'); </script>";
            exit();
        }
    }//FIN de insertaEnBitacoraPS

    function insertaEnBitacoraJustificarFalta($ok,$operacion,$idjustFalta_new,$fecha_new,
    $falta_falta_new,$idjustFalta_old,$fecha_old,$falta_falta_old)
    {
        global $con;
        $host=gethostname();
        //GUARDAR EN LA BITACORA DE justificar Falta
        if((mysqli_query($con,"call inserta_bitacora_justificar_falta('$operacion','$idjustFalta_new','$fecha_new',
        '$falta_falta_new','$idjustFalta_old','$fecha_old','$falta_falta_old','$host')")))
        {
            echo $ok;
        }
        else
        {
            echo mysqli_errno($con) . ": " . mysql_error($con) . "\n";
            $sql="DELETE FROM justificar_falta WHERE (idjustificar_falta = '$idjustFalta_new')";
            hazAlgoEnBDSinRetornarAlgo($sql);
            echo "<script> imprime('Surgió un error al guardar en la bitácora.' +
            ' Esta operación NO se ha guardado, REINTENTE.'); </script>";
            exit();
        }
    }//FIN de insertaEnBitacoraJustificarFalta
?>