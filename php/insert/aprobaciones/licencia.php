<?php
    if ((!empty($_POST["num"])) && (!empty($_POST["lic"])))
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
            if (!empty($_POST["fecf"]) && (!empty($_FILES["archivo"]) && $_FILES["archivo"]["name"][0]))
            {
                $fechaf=$_POST['fecf'];//la fecha de fin
                $sql="SELECT t.descripcion from tipo t inner join trabajador tra on tra.tipo_tipo=t.idtipo 
                where tra.numero_trabajador='$num'";
                $query=mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
                $resul=mysqli_fetch_array($query);
                //Comprobar que sea basificado
                if($resul[0]=="BASE")
                {
                  //Validar las fechas
                  $validarfechas=RevisarFechas(1,$fecha,$fechaf,"de la beca","una beca","",0);
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
                                //para que se evalue la imagen
                                $laImagen=$_FILES["archivo"]["name"][0];
                                $extension=$_FILES["archivo"]["type"][0];
                                $origen=$_FILES["archivo"]["tmp_name"][0];
                                $destino=$carpetaDestino.$laImagen;
                                //Insertar la Beca
                                $duracion=calcularDuracionEntreDosFechas(0,$fecha,$fechaf);
                                $sql="INSERT INTO especial (fecha_inicio, fecha_fin, hora_entrada, hora_salida, validez, trabajador_trabajador, clave_especial_clave_especial, empresa, duracion) VALUES ('$fecha', '$fechaf', '00:00:00', '00:00:00', '0', '$num', '$ClaveLicencia', '*Ver documento*', '$duracion');";
                                $ok= "<script> imprime('Licencia por beca agregada correctamente'); </script>";
                                $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                                $correcto=insertaEnBD($sql,$ok,$error,0);
                                //correcto obtiene el último ID que se insertó
                                $SubeImagen=analizaYCargaImagen($origen,$destino,$laImagen,$correcto,$extension,$ok,1);
                                insertaEnBitacoraEspecial($ok,"Guardado",$fecha,$fechaf,"-","-",
                                $ClaveLicencia,"-","$duracion","-","-","-","-",
                                "-","-","-",$num,$correcto);
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
                                $duracion=calcularDuracionEntreDosFechas(0,$fecha,$fechaf);
                                //para que se evalue la imagen
                                $laImagen=$_FILES["archivo"]["name"][0];
                                $extension=$_FILES["archivo"]["type"][0];
                                $origen=$_FILES["archivo"]["tmp_name"][0];
                                $destino=$carpetaDestino.$laImagen;
                                //Insertar la Beca
                                $duracion=calcularDuracionEntreDosFechas(0,$fecha,$fechaf);
                                $sql="INSERT INTO especial (fecha_inicio, fecha_fin, hora_entrada, hora_salida, validez, trabajador_trabajador, clave_especial_clave_especial, empresa, duracion) VALUES ('$fecha', '$fechaf', '00:00:00', '00:00:00', '0', '$num', '$ClaveLicencia', '*Ver documento*', '$duracion');";
                                $ok= "<script> imprime('Licencia por beca agregada correctamente'); </script>";
                                $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                                $correcto=insertaEnBD($sql,$ok,$error,0);
                                //correcto obtiene el último ID que se insertó
                                $SubeImagen=analizaYCargaImagen($origen,$destino,$laImagen,$correcto,$extension,$ok,1);
                                insertaEnBitacoraEspecial($ok,"Guardado",$fecha,$fechaf,"-","-",
                                $ClaveLicencia,"-","$duracion","-","-","-","-",
                                "-","-","-",$num,$correcto);
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
                $error="Faltan los siguientes datos:"."<br>";
                if (empty($_POST["fecf"])){$error.="La fecha de fin de lo que está solicitando."."<br>";} 
                if (empty($_FILES["archivo"]["name"][0])){$error.="El archivo escaneado"."<br>";}
                echo "<script> imprime('$error'); </script>";
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
                //VER SI POSEE ESTA LICENCIA ACTIVA o por activarse, si es así no dejar que se guarde otra
                $sql="SELECT idespecial FROM especial where trabajador_trabajador='$num' 
                and (clave_especial_clave_especial='92')
                and (validez='1' or (validez='0' and fecha_inicio>now()))";
                $filas=obtenerFilas($sql);
                if($filas==0)
                {
                    if($TipoEmp=="BASE" || $TipoEmp=="CONFIANZA")
                    {
                        //Debió haber solicitado una licencia por gravidez, buscar dicha licencia
                        $sql="SELECT idespecial,fecha_fin FROM especial where clave_especial_clave_especial='53' 
                        and trabajador_trabajador='$num'
                        and (validez='1' or validez='0')
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
                            //ojo ¿?
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
                                $correcto=insertaEnBD($sql,$ok,$error,0);
                                insertaEnBitacoraEspecial($ok,"Guardado",$fechaUltimaLicGravidez,$FechaFin,$horaE,$horaS,
                                $ClaveLicencia,"-","180","-","-","-","-",
                                "-","-","-",$num,$correcto);
                            }
                            else
                            {
                                //¿?
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
            if (!empty($_POST["fecf"]) && !empty($_POST["fec"]))
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
                        $correcto=insertaEnBD($sql,$ok,$error,0);
                        insertaEnBitacoraEspecial($ok,"Guardado",$fecha,$fechaf,$horaE,$horaS,
                        $ClaveLicencia,"-",$duracion,"-","-","-","-",
                        "-","-","-",$num,$correcto);
                    }
                }
                else
                {
                    echo "<script> imprime('El empleado $num ya posee una Tolerancia de estancia activa. NO es posible tener dos de estas licencias al mismo tiempo.'); </script>";
                }//Fin else validar filas
            }
            else
            {
                $error="Faltan los siguientes datos:"."<br>";
                if (empty($_POST["fec"])){$error.="La fecha de inicio de lo que está solicitando."."<br>";}
                if (empty($_POST["fecf"])){$error.="La fecha de fin de lo que está solicitando."."<br>";}
                echo "<script> imprime('$error'); </script>"; 
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
            if ((!empty($_POST["fec"])) && (!empty($_POST["fecf"])) && (!empty($_FILES["archivo"]) && $_FILES["archivo"]["name"][0]))
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
                                //para que se evalue la imagen
                                $laImagen=$_FILES["archivo"]["name"][0];
                                $extension=$_FILES["archivo"]["type"][0];
                                $origen=$_FILES["archivo"]["tmp_name"][0];
                                $destino=$carpetaDestino.$laImagen;
                                //Insertar la licencia sin goce
                                $sql="INSERT INTO especial VALUES (null, '$fecha', '$fechaf', '00:00:00', '00:00:00', '0', '$num', '$ClaveLicencia','Ver documento','$diasQueSolicita')";
                                $ok= "<script> imprime('Licencia sin goce agregada correctamente'); </script>";
                                $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                                $correcto=insertaEnBD($sql,$ok,$error,0);
                                //correcto obtiene el último ID que se insertó
                                $SubeImagen=analizaYCargaImagen($origen,$destino,$laImagen,$correcto,$extension,$ok,1);
                                insertaEnBitacoraEspecial($ok,"Guardado",$fecha,$fechaf,"-","-",
                                $ClaveLicencia,"-","$diasQueSolicita","-","-","-","-",
                                "-","-","-",$num,$correcto);
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
                                    //para que se evalue la imagen
                                    $laImagen=$_FILES["archivo"]["name"][0];
                                    $extension=$_FILES["archivo"]["type"][0];
                                    $origen=$_FILES["archivo"]["tmp_name"][0];
                                    $destino=$carpetaDestino.$laImagen;
                                    //insertar la licencia sin goce
                                    $sql="INSERT INTO especial VALUES (null, '$fecha', '$fechaf', '00:00:00', '00:00:00', '0', '$num', '$ClaveLicencia','*Ver documento*','$duracion')";
                                    $ok= "<script> imprime('Licencia sin goce agregada correctamente'); </script>";
                                    $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                                    $correcto=insertaEnBD($sql,$ok,$error,0);
                                    //correcto obtiene el último ID que se insertó
                                    $SubeImagen=analizaYCargaImagen($origen,$destino,$laImagen,$correcto,$extension,$ok,1);
                                    insertaEnBitacoraEspecial($ok,"Guardado",$fecha,$fechaf,"-","-",
                                    $ClaveLicencia,"-","$duracion","-","-","-","-",
                                    "-","-","-",$num,$correcto);
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
                                    echo "<script> imprime('Toda LSGSS debe durar mínimo 4 meses. La licencia que intenta agregar no cumple el requisito anterior. Verifique.'); </script>";
                                    exit();
                                }
                                //para que se evalue la imagen
                                $laImagen=$_FILES["archivo"]["name"][0];
                                $extension=$_FILES["archivo"]["type"][0];
                                $origen=$_FILES["archivo"]["tmp_name"][0];
                                $destino=$carpetaDestino.$laImagen;
                                $sql="INSERT INTO especial VALUES (null, '$fecha', '$fechaf', '00:00:00', '00:00:00', '0', '$num', '$ClaveLicencia','*Ver documento*','$duracion')";
                                $ok= "<script> imprime('Licencia sin goce para servicio social agregada correctamente'); </script>";
                                $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                                $correcto=insertaEnBD($sql,$ok,$error,0);
                                //correcto obtiene el último ID que se insertó
                                $SubeImagen=analizaYCargaImagen($origen,$destino,$laImagen,$correcto,$extension,$ok,1);
                                insertaEnBitacoraEspecial($ok,"Guardado",$fecha,$fechaf,"-","-",
                                $ClaveLicencia,"-","$duracion","-","-","-","-",
                                "-","-","-",$num,$correcto);
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
                $error="Faltan los siguientes datos:"."<br>";
                if (empty($_POST["fec"])){$error.="La fecha de inicio de lo que está solicitando."."<br>";}
                if (empty($_POST["fecf"])){$error.="La fecha de fin de lo que está solicitando."."<br>";} 
                if (empty($_FILES["archivo"]["name"][0])){$error.="El archivo escaneado"."<br>";}
                echo "<script> imprime('$error'); </script>";
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
            if ((!empty($_POST["per-go"])) && (!empty($_POST["fec"])))
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
                        $correcto=insertaEnBD($sql,$ok,$error,0);
                        insertaEnBitacoraEspecial($ok,"Guardado",$fecha,$fechaf,"-","-",
                        $ClaveLicencia,"-","$duracion","-","-","-","-",
                        "-","-","-",$num,$correcto);
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
                                $correcto=insertaEnBD($sql,$ok,$error,0);
                                insertaEnBitacoraEspecial($ok,"Guardado",$fecha,$fechaf,"-","-",
                                $ClaveLicencia,"-","$duracion","-","-","-","-",
                                "-","-","-",$num,$correcto);
                            }
                            else//fin if dias permitidos < dias gastados
                            {
                                if($diasSobrantes==0)
                                {
                                    echo "<script> imprime('El empleado con número $num está solicitando una licencia con goce' + 
                                    ' por fuerza mayor por un total de $duracion días; sin embargo, este empleado ha agotado' + 
                                    ' sus días disponibles por antiguedad para solicitar licencias de este tipo.' + 
                                    ' Sustento: Artículos 57 y 87 Fracción 7 de las CGT.'); </script>";
                                }
                                else
                                {
                                    echo "<script> imprime('El empleado con número $num está solicitando una licencia con goce por' + 
                                    ' fuerza mayor por un total de $duracion días; solo puede, por su antiguedad solicitar licencias' + 
                                    ' con goce por $diasSobrantes días; esto debido a que ya ha solicitado licencias de este tipo' + 
                                    ' con anterioridad y los días que merece por antiguedad disminuyeron.' + 
                                    ' Sustento: Artículos 57 y 87 Fracción 7 de las CGT.'); </script>";
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
                $error="Faltan los siguientes datos:"."<br>";
                if (empty($_POST["fec"])){$error.="La fecha de inicio de lo que está solicitando."."<br>";}
                if (empty($_POST["per-go"])){$error.="El motivo del permiso."."<br>";}
                echo "<script> imprime('$error'); </script>"; 
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
            if(!empty($_POST["fec"]))
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
                    $correcto=insertaEnBD($sql,$ok,$error,0);

                    insertaEnBitacoraEspecial($ok,"Guardado",$fecha,$fechaf,"-","-",
                    $ClaveLicencia,"-","$duracion","-","-","-","-",
                    "-","-","-",$num,$correcto);
                }
                else
                {
                    echo "<script> imprime('El empleado con número $num ya solicitó antes una licencia por contraer matrimonio. Esta licencia se puede pedir solo UNA vez por trabajador. Sustento: Artículo 55 fracción 2 de las CGT.'); </script>";
                }
            }
            else
            {
                $error="Faltan los siguientes datos:"."<br>";
                if (empty($_POST["fec"])){$error.="La fecha de inicio de lo que está solicitando."."<br>";}
                echo "<script> imprime('$error'); </script>";
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
            if(!empty($_POST["fec"]))
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
                    $correcto=insertaEnBD($sql,$ok,$error,0);
                    insertaEnBitacoraEspecial($ok,"Guardado",$fecha,$fechaf,"-","-",
                    $ClaveLicencia,"-","$duracion","-","-","-","-",
                    "-","-","-",$num,$correcto);
                }
                else
                {
                    echo "<script> imprime('El empleado con número $num ya tiene una licencia por fallecimiento de un familiar activa. No se puede tener 2 de estas licencias al mismo tiempo.'); </script>";
                }
            }
            else
            {
                $error="Faltan los siguientes datos:"."<br>";
                if (empty($_POST["fec"])){$error.="La fecha de inicio de lo que está solicitando."."<br>";}
                echo "<script> imprime('$error'); </script>";
            }
        }
        /*
            CICA 53
            Las trabajadoras en estado de gravidez disfrutarán de licencias con goce de sueldo, treinta días antes de 
            la fecha probable de parto y sesenta días después de éste.
        */
        if($ClaveLicencia=="53")
        {
            if((!empty($_POST["fec"])) && (!empty($_POST["fecf"])) && (!empty($_FILES["archivo"]) && $_FILES["archivo"]["name"][0]))
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
                        $duracion=calcularDuracionEntreDosFechas(2,$fecha,$fechaf);
                        if($duracion==90)
                        {
                            //para que se evalue la imagen
                            $laImagen=$_FILES["archivo"]["name"][0];
                            $extension=$_FILES["archivo"]["type"][0];
                            $origen=$_FILES["archivo"]["tmp_name"][0];
                            $destino=$carpetaDestino.$laImagen;
                            //insertar la licencia
                            $sql="INSERT INTO especial VALUES (null, '$fecha', '$fechaf', '00:00:00', '00:00:00', '0', '$num', '$ClaveLicencia','*Ver documento*','$duracion')";
                            $ok= "<script> imprime('Incapacidad por gravidez agregada correctamente.'); </script>";
                            $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                            $correcto=insertaEnBD($sql,$ok,$error,0);
                            $SubeImagen=analizaYCargaImagen($origen,$destino,$laImagen,$correcto,$extension,$ok,1);
                            
                            insertaEnBitacoraEspecial($ok,"Guardado",$fecha,$fechaf,"-","-",
                            $ClaveLicencia,"-","$duracion","-","-","-","-",
                            "-","-","-",$num,$correcto);
                        }
                        else//fin if duracion entre 88 y 90
                        {
                            echo "<script> imprime('La duración de esta licencia que está solicitando es de $duracion días.' + 
                            ' Esta licencia debe ser obligatoriamente de 90 días de duración.' + 
                            ' Sustento: Artículo 55 fracción IV de las CGT'); </script>";
                        }
                    }
                    else//fin if filas
                    {
                        echo "<script> imprime('El empleado con número $num ya tiene una licencia por gravidez activa. No se puede tener 2 de estas licencias al mismo tiempo.'); </script>";
                    }
                }
                else//fin if sexo
                {
                    echo "<script> imprime('El empleado que eligió es hombre. Esta licencia es SOLO para sexo femenino.'); </script>";
                }
            }
            else//fin if empty fechaf
            {
                $error="Faltan los siguientes datos:"."<br>";
                if (empty($_POST["fec"])){$error.="La fecha de inicio de lo que está solicitando."."<br>";}
                if (empty($_POST["fecf"])){$error.="La fecha de fin de lo que está solicitando."."<br>";} 
                if (empty($_FILES["archivo"]["name"][0])){$error.="El archivo escaneado"."<br>";}
                echo "<script> imprime('$error'); </script>";
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
            mente 3 días) NO OTORGA ESTÍMULOS SI EXCEDE 3 DÍAS POR SÍ SOLA O COMBINADA CON LA 55.  
        */
        if($ClaveLicencia=="47")
        { 
            if((!empty($_POST["fec"])) && (!empty($_POST["fecf"])) && (!empty($_FILES["archivo"]) && $_FILES["archivo"]["name"][0]))
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
                        //para que se evalue la imagen
                        $laImagen=$_FILES["archivo"]["name"][0];
                        $extension=$_FILES["archivo"]["type"][0];
                        $origen=$_FILES["archivo"]["tmp_name"][0];
                        $destino=$carpetaDestino.$laImagen;
                        //insertar la licencia
                        $sql="INSERT INTO especial VALUES (null, '$fecha', '$fechaf', '00:00:00', '00:00:00', '0', '$num', '$ClaveLicencia','*Ver documento*','$duracion')";
                        $ok= "<script> imprime('Licencia por cuidados maternos agregada correctamente.'); </script>";
                        $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                        $correcto=insertaEnBD($sql,$ok,$error,0);
                        //correcto obtiene el último ID que se insertó
                        $SubeImagen=analizaYCargaImagen($origen,$destino,$laImagen,$correcto,$extension,$ok,1);
                        insertaEnBitacoraEspecial($ok,"Guardado",$fecha,$fechaf,"-","-",
                        $ClaveLicencia,"-","$duracion","-","-","-","-",
                        "-","-","-",$num,$correcto);
                    }
                    else
                    {
                        echo "<script> imprime('La duración de esta licencia que está solicitando es de $duracion días.' + 
                        ' Esta licencia debe ser obligatoriamente de 1 hasta 8 días de duración.' +
                        ' Sustento: Artículo 55 fracción V de las CGT'); </script>";
                    }
                }
                else
                {
                    echo "<script> imprime('El empleado con número $num ya tiene una licencia por cuidados maternos activa. No se puede tener 2 de estas licencias al mismo tiempo.'); </script>";
                }
            }
            else//fin if empty fechaf
            {
                $error="Faltan los siguientes datos:"."<br>";
                if (empty($_POST["fec"])){$error.="La fecha de inicio de lo que está solicitando."."<br>";}
                if (empty($_POST["fecf"])){$error.="La fecha de fin de lo que está solicitando."."<br>";} 
                if (empty($_FILES["archivo"]["name"][0])){$error.="El archivo escaneado"."<br>";}
                echo "<script> imprime('$error'); </script>";
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
            if((!empty($_POST["fec"])) && (!empty($_POST["fecf"])) && (!empty($_FILES["archivo"]) && $_FILES["archivo"]["name"][0]))
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
                        //para que se evalue la imagen
                        $laImagen=$_FILES["archivo"]["name"][0];
                        $extension=$_FILES["archivo"]["type"][0];
                        $origen=$_FILES["archivo"]["tmp_name"][0];
                        $destino=$carpetaDestino.$laImagen;
                        //insertar la licencia
                        $sql="INSERT INTO especial VALUES (null, '$fecha', '$fechaf', '00:00:00', '00:00:00', '0', '$num', '$ClaveLicencia','*Ver documento*','$duracion')";
                        $ok= "<script> imprime('licencia por incapacidad por accidente o riesgo profesional agregada correctamente.'); </script>";
                        $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                        $correcto=insertaEnBD($sql,$ok,$error,0);
                        //correcto obtiene el último ID que se insertó
                        $SubeImagen=analizaYCargaImagen($origen,$destino,$laImagen,$correcto,$extension,$ok,1);
                        insertaEnBitacoraEspecial($ok,"Guardado",$fecha,$fechaf,"-","-",
                        $ClaveLicencia,"-","$duracion","-","-","-","-",
                        "-","-","-",$num,$correcto);
                    }
                    else
                    {
                        echo "<script> imprime('La duración de esta licencia que está solicitando es de $duracion días.' + 
                        ' Esta licencia debe ser obligatoriamente de 1 hasta 365 días de duración.' + 
                        ' Sustento: Artículo 62 fracción I de la Ley del ISSSTE'); </script>";
                    }
                }
                else
                {
                    echo "<script> imprime('El empleado con número $num ya tiene una licencia por incapacidad por accidente o riesgo profesional activa. No se puede tener 2 de estas licencias al mismo tiempo.'); </script>";
                }
            }
            else//fin if empty fechaf
            {
                $error="Faltan los siguientes datos:"."<br>";
                if (empty($_POST["fec"])){$error.="La fecha de inicio de lo que está solicitando."."<br>";}
                if (empty($_POST["fecf"])){$error.="La fecha de fin de lo que está solicitando."."<br>";} 
                if (empty($_FILES["archivo"]["name"][0])){$error.="El archivo escaneado"."<br>";}
                echo "<script> imprime('$error'); </script>";
            }
        }//Fin CICA 54 
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
            if((!empty($_POST["fec"])) && (!empty($_POST["fecf"])) && (!empty($_POST["doctor"])) && (!empty($_FILES["archivo"]) && $_FILES["archivo"]["name"][0]))
            {
                $fecha=$_POST["fec"];
                $fechaf=$_POST["fecf"];
                $doctor=$_POST["doctor"];
                $antiguedad=calculaAntiguedad($num);
                $diasPermitidos;
                if($antiguedad<1)
                {
                    $diasPermitidos=15*2;
                }
                else
                {
                    if($antiguedad>1 && $antiguedad<5)
                    {
                        $diasPermitidos=30*2;
                    }
                    else
                    {
                        if($antiguedad>5 && $antiguedad<10)
                        {
                            $diasPermitidos=45*2;
                        }
                        else
                        {
                            if($antiguedad>10)
                            {
                                $diasPermitidos=60*2;
                            }
                        }
                    }
                }//fin ifs antiguedad

                //Ver cuántos días ha usado esta persona en la tabla especial con la clave 55
                $sql="SELECT duracion from especial where (trabajador_trabajador='$num' and clave_especial_clave_especial='$ClaveLicencia' and fecha_inicio like '$anio%')";
                $diasUsados=sumaRegistrosDeConsulta($sql);
                $diasRestantes=$diasPermitidos-$diasUsados;

                $totDias=calcularDuracionEntreDosFechas(0, $fecha, $fechaf);
                
                //Ver que las fechas de inicio y final no sean menores a hoy
                $today=date("Y-m-d");
                $fecha_hoy=strtotime($today);
                $fecha_in = strtotime($fecha);
                $fecha_fi = strtotime($fechaf);
                $ok=1;

                if($fecha_in<=$fecha_hoy && $fecha_fi<$fecha_hoy)
                {
                    $ok=0;
                }

                if($ok==1)
                {
                    if($totDias<=$diasRestantes)
                    {
                        //le quedan días, ver cuántos días han pasado hasta hoy desde el inicio de esta licencia
                        $diasAhoy= obtenDiasDeRango($fecha, $today);

                        //Ver si tiene faltas en cada día desde la fecha de inicio de la licencia hasta hoy, si es así, quitarlas.
                        $tot=count($diasAhoy);
                        for($i=0;$i<$tot;$i++)
                        {
                            $diaIndiv=$diasAhoy[$i];
                            $sql="SELECT idfalta from falta where (trabajador_trabajador='$num' and fecha='$diaIndiv')";
                            $obtenIdFalta=retornaAlgoSiExiste($sql);
                            if($obtenIdFalta!=0)
                            {
                                $borrar="DELETE from falta where idfalta='$obtenIdFalta'";
                                hazAlgoEnBDSinRetornarAlgo($borrar);
                            }
                        }

                        //Insertar la licencia
                        //para que se evalue la imagen
                        $laImagen=$_FILES["archivo"]["name"][0];
                        $extension=$_FILES["archivo"]["type"][0];
                        $origen=$_FILES["archivo"]["tmp_name"][0];
                        $destino=$carpetaDestino.$laImagen;
                        //insertar la licencia
                        $validez=0;
                        if($fecha_in<=$fecha_hoy && $fecha_fi>=$fecha_hoy)
                        {
                            $validez=1;
                        }
                        $sql="INSERT INTO especial VALUES (null, '$fecha', '$fechaf', '00:00:00', '00:00:00', '$validez', '$num', '$ClaveLicencia','$doctor','$totDias')";
                        $ok= "<script> imprime('Licencia por incapacidad médica no profesional agregada correctamente.'); </script>";
                        $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                        $correcto=insertaEnBD($sql,$ok,$error,0);
                        //correcto obtiene el último ID que se insertó
                        $SubeImagen=analizaYCargaImagen($origen,$destino,$laImagen,$correcto,$extension,$ok,1);
                        insertaEnBitacoraEspecial($ok,"Guardado",$fecha,$fechaf,"-","-",
                        $ClaveLicencia,"-","$totDias","-","-","-","-",
                        "-","-","-",$num,$correcto);

                    }//fin if totdias<diasrestantes
                    else
                    {
                        echo "<script> imprime('El trabajador con número $num solicita una licencia por $totDias días' +
                        ', sin embargo, a este empleado solo le quedan $diasRestantes días de licencia este año. No es' +
                        ' posible guardar esta licencia debido a lo anterior. Sustento: CICA 55. Observaciones.'); </script>";
                    }
                }
                else
                {
                    echo "<script> imprime('Las fechas de inicio y de fin ya pasaron. No es posible guardar esta licencia.'); </script>";
                }
            }
            else//fin if empty fechaf
            {
                $error="Faltan los siguientes datos:"."<br>";
                if (empty($_POST["fec"])){$error.="La fecha de inicio de lo que está solicitando."."<br>";}
                if (empty($_POST["fecf"])){$error.="La fecha de fin de lo que está solicitando."."<br>";} 
                if (empty($_FILES["archivo"]["name"][0])){$error.="El archivo escaneado"."<br>";}
                if (empty($_POST["doctor"])){$error.="El doctor que aprobó esta licencia."."<br>";}
                echo "<script> imprime('$error'); </script>";
            }

        }//Fin CICA 55
        
        /*incapacidad médica
            revisar la incapacidad registrada en (el sistema que el ISSSTE tiene para licencias médicas, la emite cualquier doctor)
            cual es el doctor que da más incapacidades en un periodo de tiempo.
        */
    }
    else
    {
        $error="Faltan los siguientes datos:"."<br>";
        if (empty($_POST["num"])){$error.="Número de trabajador que exista."."<br>";}
        if (empty($_POST["lic"])){$error.="El tipo de licencia que está solicitando."."<br>";}
        echo "<script> imprime('$error'); </script>";
    }//FIN IF post vacios
?>