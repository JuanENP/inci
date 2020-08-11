<?php
    if (!empty($_POST["num"]) && !empty($_POST["select-otro"]))
    {
        $num=$_POST["num"];
        $fecha=date("Y-m-d"); //la fecha actual
        $tipoEmpleado=tipoEmpleado($num);

        $Clave=$_POST["select-otro"];
        if($tipoEmpleado=="BASE")
        {
            if($Clave=="04")
            {
                //pago de media jornada laboral. Buscar una clave 03 el día de hoy
                $sql="SELECT c.idincidencia, b.id FROM trabajador a
                INNER JOIN asistencia b on a.numero_trabajador=b.trabajador_trabajador and a.numero_trabajador = '$num' and b.fecha_entrada like '$fecha%'
                INNER JOIN incidencia c on b.id = c.asistencia_asistencia and b.quincena_quincena = '$quincena'
                and c.clave_incidencia_clave_incidencia='03'";
                $query=mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
                $filas=mysqli_num_rows($query);
                if($filas==1)
                {
                    $resul=mysqli_fetch_array($query);
                    $idIncidencia03=$resul[0];
                    $idAsistencia=$resul[1];
                    //borrar la 03
                    $sql="DELETE FROM incidencia WHERE idincidencia=$idIncidencia03;";
                    hazAlgoEnBDSinRetornarAlgo($sql);
                    //insertar la 04
                    $sql16="INSERT INTO incidencia VALUES (NULL, 'Pago de media jornada', '$Clave', '$idAsistencia')";
                    $ok= "<script> imprime('Clave 04 *PAGO DE MEDIA JORNADA* agregada correctamente'); </script>";
                    $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                    $correcto=insertaEnBD($sql16,"-",$error,0);
                    echo "$ok";
                    exit();
                }
                else
                {
                    echo "<script> imprime('No se encontró una clave 03 el día de hoy. NO es posible registrar'+
                    ' una clave 04 sin que exista una 03. Sustento: CICA 04. Observaciones'); </script>";
                } 
            }
            else
            {
                if($Clave=="82")
                {
                    //un trato especial a esta clave:

                    //Ver si ya existe este trabajador con la clave 82 o cualquier otra clave
                    $sql="SELECT idbajas from bajas where trabajador_trabajador='$num' and (motivo='80'
                    or motivo='81' or motivo='82' or motivo='83' or motivo='84' or motivo='85'
                    or motivo='86' or motivo='87' or motivo='88' or motivo='89' or motivo='90')";
                    $query=mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
                    $filasBaja1=mysqli_num_rows($query);
                    if($filasBaja1==0)
                    {
                        //no tiene una 82, buscar si existe el trabajador en la tabla pero sin la clave 82
                        $sql="SELECT idbajas from bajas where trabajador_trabajador='$num' and motivo!='80'
                        and motivo!='81' and motivo!='82' and motivo!='83' and motivo!='84' and motivo!='85'
                        and motivo!='86' and motivo!='87' and motivo!='88' and motivo!='89' and motivo!='90'";
                        $query=mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
                        $filasBaja2=mysqli_num_rows($query);
                        if($filasBaja2==1)
                        {
                            //actualizar ese idbaja y poner motivo = 82
                            $resul=mysqli_fetch_array($query);
                            $idBaja=$resul[0];
                            $sql="UPDATE bajas SET motivo='$Clave', dias_para_baja='0', baja_definitiva='1' where idbajas=$idBaja";
                            $ok= "<script> imprime('Clave 82 *SUSPENSIÓN EN NÓMINA POR CESE* agregada correctamente'); </script>";
                            $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                            $correcto=insertaEnBD($sql,$ok,$error,1);
                            exit();
                        }
                        else
                        {
                            //insertar una baja con motivo=82
                            $sql="INSERT INTO bajas(fecha, t_dias, motivo, trabajador_trabajador, quincena, dias_para_baja, baja_definitiva) 
                            VALUES ('$fecha', '0', '$Clave', '$num', '$quincena', '0', '1')";
                            $ok= "<script> imprime('Clave 82 *SUSPENSIÓN EN NÓMINA POR CESE* agregada correctamente'); </script>";
                            $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                            $correcto=insertaEnBD($sql,$ok,$error,1);
                            exit();
                        }
                    }
                    else
                    {
                        echo "<script> imprime('El trabajador con número $num ya posee una clave'+
                        ' *SUSPENSIÓN EN NÓMINA POR...*. NO es posible resgistrar dos de estos tipos de clave, Verifique.'); </script>";
                    }
                    //fin if clave 82                    
                }
                else
                {
                    if($Clave=="30")
                    {
                        //Buscar una clave 30 el día de hoy
                        $sql="SELECT c.idincidencia, b.id FROM trabajador a
                        INNER JOIN asistencia b on a.numero_trabajador=b.trabajador_trabajador and a.numero_trabajador = '$num' and b.fecha_entrada like '$fecha%'
                        INNER JOIN incidencia c on b.id = c.asistencia_asistencia and b.quincena_quincena = '$quincena'
                        and c.clave_incidencia_clave_incidencia='30'";
                        $query=mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
                        $filas=mysqli_num_rows($query);
                        if($filas==0)
                        {
                            //insertar la clave, busquemos el id de asistencia de hoy
                            $sql="SELECT id from asistencia where (fecha_entrada like '$fecha%' or fecha_salida like '$fecha%') 
                            and (trabajador_trabajador='$num' and quincena_quincena='$quincena');";
                            $query=mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
                            $filasAsis=mysqli_num_rows($query);
                            if($filasAsis==1)
                            {
                                //insertar la 30 con la clave asistencia obtenida
                                $resul=mysqli_fetch_array($query);
                                $idAsistencia=$resul[0];

                                $sql1="INSERT INTO incidencia VALUES (NULL, 'Ausentarse en horas de labores', '$Clave', '$idAsistencia')";
                                $ok= "<script> imprime('Clave 30 agregada correctamente'); </script>";
                                $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                                $correcto=insertaEnBD($sql1,$ok,$error,1);
                                exit();
                            }
                            else
                            {
                                //no hay asistencia hoy
                                echo "<script> imprime('No se ha encontrado una asistencia el día de hoy para el empleado ' +
                                ' con número $num. Esto podría deberse a que no vino, verifique lo anterior' +
                                ' en la sección REPORTES-> ¿Quiénes vinieron?. Esta clave 30 NO se insertó'); </script>";
                            }
                        }
                        else
                        {
                            echo "<script> imprime('Ya hay una clave $Clave registrada HOY para el' +
                            ' trabajador con número $num'); </script>";
                        }
                    }//fin clave 30
                    else
                    {
                        if($Clave=="31")
                        {
                            //ver si el empleado tiene turno opcional, sacamos la categoría
                            $sql="SELECT categoria_categoria from trabajador where numero_trabajador='$num'";
                            $query=mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
                            $filas=mysqli_num_rows($query);
                            if($filas==1)
                            {
                                //obtenemos su categoria
                                $resul=mysqli_fetch_array($query);
                                $categoria=$resul[0];

                                $sql="SELECT id_t_opcional from t_opcional where categoria_categoria='$categoria'";
                                $query=mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
                                $filas=mysqli_num_rows($query);
                                if($filas==1)
                                {
                                    //si tiene turno opcional
                                    //Buscar una clave 31 el día de hoy
                                    $sql="SELECT c.idincidencia, b.id FROM trabajador a
                                    INNER JOIN asistencia b on a.numero_trabajador=b.trabajador_trabajador and a.numero_trabajador = '$num' and b.fecha_entrada like '$fecha%'
                                    INNER JOIN incidencia c on b.id = c.asistencia_asistencia and b.quincena_quincena = '$quincena'
                                    and c.clave_incidencia_clave_incidencia='31'";
                                    $query=mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
                                    $filas=mysqli_num_rows($query);
                                    if($filas==0)
                                    {
                                        //insertar la clave, busquemos el id de asistencia de hoy
                                        $sql="SELECT id from asistencia where (fecha_entrada like '$fecha%' or fecha_salida like '$fecha%') 
                                        and (trabajador_trabajador='$num' and quincena_quincena='$quincena');";
                                        $query=mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
                                        $filasAsis=mysqli_num_rows($query);
                                        if($filasAsis==1)
                                        {
                                            //insertar la 30 con la clave asistencia obtenida
                                            $resul=mysqli_fetch_array($query);
                                            $idAsistencia=$resul[0];

                                            $sql1="INSERT INTO incidencia VALUES (NULL, 'Ausentarse en horas de labores de T. Opcional', '$Clave', '$idAsistencia')";
                                            $ok= "<script> imprime('Clave 31 agregada correctamente'); </script>";
                                            $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                                            $correcto=insertaEnBD($sql1,$ok,$error,1);
                                            exit();
                                        }
                                        else
                                        {
                                            //no hay asistencia hoy
                                            echo "<script> imprime('No se ha encontrado una asistencia el día de hoy para el empleado ' +
                                            ' con número $num. Esto podría deberse a que no vino, verifique lo anterior' +
                                            ' en la sección REPORTES-> ¿Quiénes vinieron?. Esta clave 31 NO se insertó'); </script>";
                                        }
                                    }
                                    else
                                    {
                                        echo "<script> imprime('Ya hay una clave $Clave registrada HOY para el' +
                                        ' trabajador con número $num'); </script>";
                                    }
                                }
                                else
                                {
                                    echo "<script> imprime('El trabajador con número $num no' +
                                    ' posee un turno opcional, Verifique.'); </script>";
                                }
                            }
                            else
                            {
                                echo "<script> imprime('No se pudo obtener la categoría del trabajador con número $num. Este problema es muy grave, ' +
                                ' contacte INMEDIATAMENTE al administrador del sistema. Verifique si este empleado posee una categoría o no en la sección Personal->Personal->Buscar empleado (sección inferior).'); </script>";
                            }
                        } //fin clave 31
                        else
                        {
                            //cualquier otra clave distinta de la 82 o 04 o 30 o 31
                            $sql="SELECT idbajas from bajas where trabajador_trabajador='$num'";
                            $filas=obtenerFilas($sql);
                            if($filas==0)
                            {
                                //insertar
                                $baja="1";
                                if($Clave=="86")
                                {
                                    $baja="0";
                                }
                                $sql="INSERT INTO bajas(fecha, t_dias, motivo, trabajador_trabajador, quincena, dias_para_baja, baja_definitiva) 
                                VALUES ('$fecha', '0', '$Clave', '$num', '$quincena', '0', '$baja')";
                                $ok= "<script> imprime('Clave $Clave agregada correctamente'); </script>";
                                $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                                $correcto=insertaEnBD($sql,$ok,$error,1);
                                exit();
                            }
                            else
                            {
                                echo "<script> imprime('El trabajador con número $num ya posee una clave'+
                                ' *SUSPENSIÓN EN NÓMINA POR...*. NO es posible resgistrar dos de estos tipos de clave, Verifique.'); </script>";
                            }

                        } //fin de cualquier otra clave distinta de la 82 o 04 o 30 o 31
                    }
                }
            }
        }
        else//fin if tipo=BASE
        {
            echo "<script> imprime('El empleado con número $num es de tipo $tipoEmpleado. Este pase es SOLO' +
            ' empleados de BASE'); </script>";
        }
    }
    else//fin if post vacíos
    {
        $error="Faltan los siguientes datos:"."<br>";
        if (empty($_POST["num"])){$error.="Número de trabajador que exista."."<br>";}
        if (empty($_POST["select-otro"])){$error.="Falta la opción de OTROS en la seccion otros."."<br>";}
        echo "<script> imprime('$error'); </script>";
    }
?>