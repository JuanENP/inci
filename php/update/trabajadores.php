<?php
session_start(); 
    date_default_timezone_set('America/Mexico_City');
    if (($_SESSION["name"]) && ($_SESSION["con"]))
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        require("../../Acceso/global.php"); 
        $nombre_host= gethostname();
        require('../buscar_info_trabajador.php');
    }
    else
    {
        header("Location: ../index.php");
        die();
    }
?>
<script type="text/javascript">
    function Alerta()
    {
        alert("Correcto");
        history.back();
    }

    function error(cadena)
    {
        alert(cadena);
        history.back();
        exit();
    }
</script>

<?php
    $salida="";
    if(empty($_SESSION['anterior_num']))
    {
        $salida.="No se recibió el número de trabajador seleccionado. ";
    }
    if(empty($_POST['num']))
    {
        $salida.="Debe escribir un número de trabajador. ";
    }
    if(empty($_POST['nom']))
    {
        $salida.="Debe escribir el nombre. ";
    }
    if(empty($_POST['a_pat']))
    {
        $salida.="Debe escribir el apellido paterno. ";
    }
    if(empty($_POST['a_mat']))
    {
        $salida.="Debe escribir el apellido materno. ";
    }
    if(empty($_POST['cat']))
    {
        $salida.="Debe seleccionar una categoria. ";
    }
    if(empty($_POST['depto']))
    {
        $salida.="Debe seleccionar un departamento. ";
    }
    if(empty($_POST['tipo']))
    {
        $salida.="Debe seleccionar un tipo de trabajador. ";
    }
    if(empty($_POST['turno']))
    {
        $salida.="Debe seleccionar un turno. ";
    }
    if(empty($_POST['cumple']))
    {
        $salida.="Debe seleccionar una fecha de cumpleaños ";
    }
    if(empty($_POST['fecha_alta']))
    {
        $salida.="Debe seleccionar la fecha de alta del trabajador ";
    }

    $anterior_num=$_SESSION['anterior_num'];//anterior numero de empleado
    $numero=$_POST['num'];
    //Validar si el número de empleado sigue siendo el mismo o se si se actualizará
    if($numero !== $anterior_num)
    {
        //Aqui consulto si existe ese nuevo numero de trabajador 
        $ejecu="select * from trabajador where numero_trabajador = '$numero'";
        $codigo=mysqli_query($con,$ejecu);
        $consultar=mysqli_num_rows($codigo);
        //si el trabajador existe avisame que ya existe
        if($consultar==1)
        { 
            $salida="El número de trabajador ya existe, debe registrar otro número ";
            echo "<script> error('$salida');</script>";
        } 
        else
        {
            $numero=$numero;
        }  
    }
    
    $nombre=$_POST['nom'];
    $a_pat=$_POST['a_pat'];
    $a_mat=$_POST['a_mat'];
    $cat=$_POST['cat'];
    $depto=$_POST['depto'];
    $tipo=$_POST['tipo'];
    $existeSexta=0;

    $todoturno=$_POST['turno'];
    $separa=explode(' ',$todoturno);
    $turno=$separa[0];
    $t_horas_turno=$separa[1];

    $cumple=$_POST['cumple'];
    $valores=explode('-',$cumple);
    $respuesta=esMayorEdad($cumple);
    if($respuesta==false)
    { 
        $salida.="El trabajador debe ser mayor de edad. ";
    }

    $fecha_alta=$_POST['fecha_alta'];
    $valores2=explode('-',$fecha_alta);
    $resp=compararAnio($valores2[0]);
    if($resp==false)
    {
        $salida.="El año de alta del trabajador debe ser menor o igual al año actual. ";
    }

    if (empty($_POST['dia']))
    {
        $salida.="Debe seleccionar al menos un dia de trabajo. ";

    }
    else
    {
        $dias=$_POST['dia'];
    }

    if($_POST['ono'])
    {
       $ono=$_POST['ono'];
       $valores3=explode('-',$ono);
       if(strlen($valores3[0])>4)
        {
            $salida.="El año de la fecha del onomástico es incorrecto.";
        }
    }
    else
    {
       $ono="";
    }

    if (empty($_POST['genero']))
    {
       $salida.="Debe seleccionar un género. ";
    }
    else
    {
       $genero=$_POST['genero'];
    }

    if(strlen($valores[0])>4)
    {
        $salida.="El año de nacimiento es incorrecto.";
    }
    if(strlen($valores2[0])>4)
    {
        $salida.="El año de la fecha de alta es incorrecto.";
    }
           //Si es foráneo
    if($tipo==4)
    {
        //Consultar los datos de la comision el trabajador desde la bd
        $fila=consultaEspecial($anterior_num);
        $idespecial=$fila[0];
        $f_ini_anterior=$fila[1];
        $f_fin_anterior=$fila[2];
        $clave_especial_anterior=$fila[7];
        $empresa_anterior=$fila[8];
        $duracion_anterior=$fila[9]; 

        //datos recibidos desde editar-trabajadores.php
        $empresa=$_POST['emp'];
        $f_ini=$_POST['f_ini'];
        $f_fin=$_POST['f_fin'];
        $empresa = trim($empresa);//Evitar guardar espacios en el input
        if(empty($empresa))
        {
            $salida.="No ha escrito el nombre de la empresa.";
        }

        if(empty($f_ini))
        {
            $salida.="No ha escrito la fecha de inicio de la comisión.";
        }
        if(empty($f_fin))
        {
            $salida.="No ha escrito la fecha de fin de la comisión.";
        }

        $valores_ini=explode('-',$f_ini);
        if(strlen($valores_ini[0])>4)
        {
            $salida.="El año de inicio es incorrecto.";
        }

        $valores_fin=explode('-',$f_fin);
        if(strlen($valores_fin[0])>4)
        {
            $salida.="El año de fin es incorrecto.";
        }

        $date1= new DateTime($f_ini);
        $date2= new DateTime($f_fin);
        $interval = $date1->diff($date2);
        $totDias=$interval->format('%a');//los días que durará la comisión
        //si el periodo de comisión es superior a 165 días (5 meses y medio)
        if($totDias>165)
        {
            $salida.="No se puede comisionar más de 5 meses y medio.";
        }

        $fecha_hoy=date("Y-m-d");//la fecha de hoy
        $fecha_ac = strtotime($fecha_hoy);
        $fecha_in = strtotime($f_ini);
        if(($f_ini !== $f_ini_anterior))
        {
            //Si la fecha actual es mayor que la fecha inicial de la comisión entonces
            if($fecha_ac > $fecha_in)
            {
                $salida.="La fecha de inicio de la comisión ya pasó, no es posible registrar una comisión que inició antes de hoy .";
            }
            if($fecha_ac==$fecha_in)
            {
                $salida.="La comisión empieza hoy y no puede registrarse debido a que se requiere mínimo un día de anticipación.";
            }
        }
    }//fin if-si es comisionado foráneo

    //Seleccionar los días de sexta en caso de que el trabajador tenga sexta
    if (!(empty($_POST['diaS'])))
    {  
        $diasSexta=$_POST['diaS'];
        $existeSexta=1;
        $semana2 = array(0,0,0,0,0,0,0,0);
        $num2=count($diasSexta);
        for($n=0;$n<$num2;$n++)
        {
            if($diasSexta[$n]=="lunes")
            {  
                $semana2[0]=1;
            }
            if($diasSexta[$n]=="martes")
            {  
                $semana2[1]=1;
            }
            if($diasSexta[$n]=="miercoles")
            {  
                $semana2[2]=1;
            }
            if($diasSexta[$n]=="jueves")
            {  
                $semana2[3]=1;
            }
            if($diasSexta[$n]=="viernes")
            {  
                $semana2[4]=1;
            }
            if($diasSexta[$n]=="sabado")
            {  
                $semana2[5]=1;
            }
            if($diasSexta[$n]=="domingo")
            {  
                $semana2[6]=1;
            }
            if($diasSexta[$n]=="dias_festivos")
            {  
                $semana2[7]=1;
            }
        }
    }

    //Si salida está vacio significa que no ocurrió algún error
    if(empty($salida))
    {
        //significa que no ocurrio ningun error y prosigues
        $semana = array(0,0,0,0,0,0,0,0);
        $num=count($dias);
        for($n=0;$n<$num;$n++)
        {
            if($dias[$n]=="lunes")
            {  
                $semana[0]=1;
            }
            if($dias[$n]=="martes")
            {  
                $semana[1]=1;
            }
            if($dias[$n]=="miercoles")
            {  
                $semana[2]=1;
            }
            if($dias[$n]=="jueves")
            {  
                $semana[3]=1;
            }
            if($dias[$n]=="viernes")
            {  
                $semana[4]=1;
            }
            if($dias[$n]=="sabado")
            {  
                $semana[5]=1;
            }
            if($dias[$n]=="domingo")
            {  
                $semana[6]=1;
            }
            if($dias[$n]=="dias_festivos")
            {  
                $semana[7]=1;
            }
        }
        //Datos necesarios para guardar en las bitácoras//
        $fila=consultaTrabajador($anterior_num);
        $nombre_anterior=$fila[1];
        $a_paterno_anterior=$fila[2];
        $a_materno_anterior=$fila[3];
        $depto_anterior=$fila[4];
        $categoria_anterior=$fila[5];
        $tipo_anterior=$fila[6];
        $genero_anterior=$fila[7];
    
        $fila=consultaCumple($anterior_num);
        $cumple_anterior=$fila[0];
        $ono_anterior=$fila[1];
        $idcumple_ono=$fila[2];

        $fila=consultaAcceso($anterior_num);
        $lunes_anterior=$fila[0];
        $martes_anterior=$fila[1];
        $miercoles_anterior=$fila[2];
        $jueves_anterior=$fila[3];
        $viernes_anterior=$fila[4];
        $sabado_anterior=$fila[5];
        $domingo_anterior=$fila[6];
        $dias_festivos_anterior=$fila[7];
        $turno_anterior=$fila[8];
 
        $fila=consultaTServicio($anterior_num);
        $idtiempo_servicio=$fila[1];
        $fecha_alta_anterior=$fila[0];

        $fila=describeTipoTrabajador($anterior_num,$tipo_anterior);
        $descripcion_tipo=$fila;
                  
        //Si el tipo de empleado es comisionado foráneo 
        if($tipo==4)
        { 
            mysqli_autocommit($con, FALSE);
            if(!(mysqli_query($con,"Update trabajador SET numero_trabajador='$numero', nombre='$nombre',apellido_paterno='$a_pat',apellido_materno='$a_mat',depto_depto='$depto',categoria_categoria='$cat',tipo_tipo=$tipo, genero='$genero' WHERE numero_trabajador='$anterior_num'")))
            {
                mysqli_rollback($con);
                mysqli_autocommit($con, TRUE); 
                echo "<script type=\"text/javascript\">alert('Error al actualizar los datos del trabajador, línea 363, verifique con el administrador de sistemas.'); history.back();</script>";
            }
            else
            {   
                if(!(mysqli_query($con,"Update acceso SET lunes=$semana[0],martes=$semana[1],miercoles=$semana[2],jueves=$semana[3],viernes=$semana[4],sabado=$semana[5],domingo=$semana[6],dia_festivo=$semana[7],turno_turno='$turno' WHERE trabajador_trabajador='$numero'")))
                {
                    mysqli_rollback($con);
                    mysqli_autocommit($con, TRUE); 
                    echo "<script type=\"text/javascript\">alert('Error al actualizar los días de trabajo o el turno, línea 371, verifique con el administrador de sistemas.'); history.back();</script>";    
                }
                else
                { 
                    if(!(mysqli_query($con,"Update cumple_ono SET fecha_cumple='$cumple',fecha_ono='$ono' where (idcumple_ono=$idcumple_ono)")))
                    {
                        mysqli_rollback($con);
                        mysqli_autocommit($con, TRUE); 
                        echo "<script type=\"text/javascript\">alert('Error al actualizar el cumpleaños u onomástico del trabajador, línea 379, verifique con el administrador de sistemas.'); history.back();</script>";            
                    }
                    else
                    {
                        if(!(mysqli_query($con,"Update tiempo_servicio SET fecha_alta='$fecha_alta' where idtiempo_servicio=$idtiempo_servicio")))
                        {
                            mysqli_rollback($con);
                            mysqli_autocommit($con, TRUE); 
                            echo "<script type=\"text/javascript\">alert('Error al actualizar el tiempo de servicio, línea 387, verifique con el administrador de sistemas. '); history.back(); </script>";
                        }
                        else
                        {   
                            if(!(mysqli_query($con,"Update especial SET fecha_inicio='$f_ini', fecha_fin='$f_fin',empresa='$empresa', duracion=$totDias where (idespecial=$idespecial)")))
                            {
                                mysqli_rollback($con);
                                mysqli_autocommit($con, TRUE); 
                                echo "<script type=\"text/javascript\">alert('Error al actualizar la comisión del trabajador, línea 395, verifique con el administrador de sistemas.'); history.back();</script>";
                            }
                            else
                            {     
                                //GUARDAR EN LA BITACORA DE TRABAJADOR
                                if(!(mysqli_query($con,"call inserta_bitacora_trabajador('Actualizado','$numero','$nombre','$a_pat','$a_mat','$depto','$cat','$descripcion_tipo','$genero','$anterior_num','$nombre_anterior','$a_paterno_anterior','$a_materno_anterior','$depto_anterior','$categoria_anterior','$descripcion_tipo','$genero_anterior','$nombre_host')")))
                                {
                                    mysqli_rollback($con);
                                    mysqli_autocommit($con, TRUE); 
                                    echo "<script type=\"text/javascript\">alert('Error al guardar en bitácora trabajador, línea 404, verifique con el administrador de sistemas.'); history.back();</script>";
                                }
                                else
                                {                                       
                                    //GUARDAR EN LA BITACORA DE CUMPLE_ONO
                                    if(!(mysqli_query($con,"call inserta_bitacora_cumple_ono('Actualizado','$cumple','$ono','$cumple_anterior','$ono_anterior','$numero', '$nombre_host')")))
                                    {
                                        mysqli_rollback($con);
                                        mysqli_autocommit($con, TRUE); 
                                        echo "<script type=\"text/javascript\">alert('Error al guardar en bitácora cumpleaños u onomástico, línea 413, verifique con el administrador de sistemas.'); history.back();</script>";
                                    }
                                    else
                                    {  
                                        //GUARDAR EN LA BITACORA DE ACCESO
                                        if(!(mysqli_query($con,"call inserta_bitacora_acceso('Actualizado','$semana[0]','$semana[1]','$semana[2]','$semana[3]','$semana[4]','$semana[5]',$semana[6],$semana[7],'$turno','$lunes_anterior','$martes_anterior','$miercoles_anterior','$jueves_anterior','$viernes_anterior','$sabado_anterior','$domingo_anterior','$dias_festivos_anterior','$turno','$numero','$nombre_host')")))
                                        {
                                            mysqli_rollback($con);
                                            mysqli_autocommit($con, TRUE); 
                                            echo "<script type=\"text/javascript\">alert('Error al guardar en bitácora acceso, línea 422,verifique con el administrador de sistemas.'); history.back();</script>";
                                        }
                                        else
                                        {
                                            //GUARDAR EN LA BITACORA DE TIEMPO SERVICIO
                                            if(!(mysqli_query($con,"call inserta_bitacora_tiempo_servicio('Actualizado','$fecha_alta','$fecha_alta_anterior','$numero', '$nombre_host')")))
                                            {
                                                mysqli_rollback($con);
                                                mysqli_autocommit($con, TRUE); 
                                                echo "<script type=\"text/javascript\">alert('Error al guardar en bitácora tiempo de servicio, línea 431, verifique con el administrador de sistemas.'); history.back();</script>";
                                            }
                                            else
                                            {
                                                //GUARDAR EN LA BITACORA DE ESPECIAL
                                                if(!(mysqli_query($con,"call inserta_bitacora_especial('Actualizado', '$f_ini', '$f_fin', '-', '-', 'CS', '$empresa', '$totDias', '$f_ini_anterior', '$f_fin_anterior', '-', '-', '$clave_especial_anterior', '$empresa_anterior', '$duracion_anterior', '$numero', '$nombre_host', '-1')")))
                                                { 
                                                    //echo "<br>" . "Error, línea 440: " . mysqli_errno($con) . " : " . mysqli_error($con)." verifique con el administrador de sistemas";
                                                    mysqli_rollback($con);
                                                    mysqli_autocommit($con, TRUE); 
                                                    echo "<script type=\"text/javascript\">alert('Error al guardar en bitácora especial, línea 440,verifique con el administrador de sistemas.'); history.back();</script>";  
                                                }
                                                else
                                                {   //Si el trabajador tiene selecionado un turno con sexta
                                                    if($existeSexta==1)
                                                    {
                                                        $result=tieneSexta($numero);
                                                        if($result==false) //Si el trabajador no tiene una sexta registrada, será necesario guardar la sexta
                                                        {
                                                            //Guardar la sexta 
                                                            if(!(mysqli_query($con,"Insert into sexta values ('',$semana2[0],$semana2[1],$semana2[2],$semana2[3],$semana2[4],$semana2[5],$semana2[6],$semana2[7],0,0,'$turno','$numero')")))
                                                            {
                                                                mysqli_rollback($con);
                                                                mysqli_autocommit($con, TRUE); 
                                                                echo "<script type=\"text/javascript\">alert('Error al guardar la sexta del trabajador, línea 455,verifique con el administrador de sistemas.'); history.back();</script>";  
                                                            }
                                                            else
                                                            {   //Guardar en bitacora sexta
                                                                if(!(mysqli_query($con,"call inserta_bitacora_sexta('Guardado','$semana2[0]','$semana2[1]','$semana2[2]','$semana2[3]','$semana2[4]','$semana2[5]','$semana2[6]','$semana2[7]','$turno','0','0','-', '-', '-', '-', '-', '-', '-', '-', '-','$numero','-','-','$nombre_host')")))
                                                                {
                                                                    mysqli_rollback($con);
                                                                    mysqli_autocommit($con, TRUE); 
                                                                    echo "<script type=\"text/javascript\">alert('Error al guardar en bitácora especial, línea 463,verifique con el administrador de sistemas.'); history.back();</script>";  
                                                                }
                                                                else
                                                                {  //Si todo está correcto, guardar todo
                                                                    mysqli_commit($con);
                                                                    mysqli_autocommit($con, TRUE);
                                                                    echo "<script type=\"text/javascript\">alert(\"Empleado comisionado guardado correctamente\"); location.href='../../ht/trabajadores.php';</script>";

                                                                }
                                                            }
                                                        }
                                                        else
                                                        { //Si el trabajador ya tiene registrada una sexta, solo se actualizará

                                                            if(!(mysqli_query($con,"Update sexta SET lunes=$semana2[0],martes=$semana2[1],miercoles=$semana2[2],jueves=$semana2[3],viernes=$semana2[4],sabado=$semana2[5],domingo=$semana2[6],dia_festivo=$semana2[7],validez=0,t_dias=0,turno_turno='$turno' WHERE trabajador_trabajador='$numero'")))
                                                            {
                                                                mysqli_rollback($con);
                                                                mysqli_autocommit($con, TRUE); 
                                                                echo "<script type=\"text/javascript\">alert('Error al actualizar los datos de la sexta del trabajador, línea 481, verifique con el administrador de sistemas.'); history.back();</script>";    
                                                            }
                                                            else
                                                            {
                                                                if(!(mysqli_query($con,"call inserta_bitacora_sexta('Actualizado','$semana2[0]','$semana2[1]','$semana2[2]','$semana2[3]','$semana2[4]','$semana2[5]','$semana2[6]','$semana2[7]','$turno','0','0','$result[1]', '$result[2]', '$result[3]', '$result[4]', '$result[5]', '$result[6]', '$result[7]', '$result[8]', '$result[11]','$numero','$result[9]','$result[10]','$nombre_host')")))
                                                                {
                                                                    mysqli_rollback($con);
                                                                    mysqli_autocommit($con, TRUE); 
                                                                    echo "<script type=\"text/javascript\">alert('Error al guardar los datos en bitácora sexta, línea 489, verifique con el administrador de sistemas.'); history.back();</script>";    
                                                                }
                                                                else
                                                                {
                                                                    mysqli_commit($con);
                                                                    mysqli_autocommit($con, TRUE);
                                                                    echo "<script type=\"text/javascript\">alert(\"Empleado comisionado actualizado correctamente\"); location.href='../../ht/trabajadores.php';</script>";
                                                                }
                                                            }
                                                        }
                                                    }
                                                    else//Si el trabajador no tiene seleccionado un turno con sexta
                                                    {
                                                       //Pero si el trabajador ya tiene registrado un turno con sexta en la tabla sexta y va a cambiar a otro turno que no tiene sexta, será necesario eliminar su sexta
                                                        $result=tieneSexta($numero);
                                                        if(($result!==false) && ($t_horas_turno !=="06:00:00" || $t_horas_turno !=="06:30:00") && ($tipo==2 || $tipo==4))
                                                        { 
                                                            if(!(mysqli_query($con,"DELETE FROM sexta WHERE idsexta = '$result[0]';")))
                                                            {
                                                                $error="Error al eliminar la sexta del trabajador, línea 510, verifique con el administrador de sistemas.";
                                                                echo "<script> alert('$error'); </script>";
                                                                mysqli_rollback($con);
                                                                mysqli_autocommit($con, TRUE); 
                                                            
                                                            }
                                                            else
                                                            { 
                                                                if(!(mysqli_query($con,"call inserta_bitacora_sexta('Eliminado','-','-','-', '-', '-', '-', '-', '-','-','-','-','$result[1]', '$result[2]', '$result[3]', '$result[4]', '$result[5]', '$result[6]', '$result[7]', '$result[8]','$turno','$numero','$result[9]','$result[10]','$nombre_host')")))
                                                                {
                    
                                                                    $error="Error al insertar en la bitácora sexta, línea 520, verifique con el administrador de sistemas.";
                                                                    //echo "Error, línea 669: " . mysqli_errno($con) . " : " . mysqli_error($con).", verifique con el administrador de sistemas";
                                                                    echo "<script> alert('$error'); </script>";
                                                                    mysqli_rollback($con);
                                                                    mysqli_autocommit($con, TRUE); 
                                            
                                                                }
                                                                else
                                                                {
                                                                    mysqli_commit($con);
                                                                    mysqli_autocommit($con, TRUE);
                                                                    echo "<script type=\"text/javascript\">alert(\"Empleado comisionado actualizado correctamente\"); location.href='../../ht/trabajadores.php';</script>"; 
                                                                
                                                                }
                                                            }
                                                        }
                                                        else
                                                        { 
                                                            mysqli_commit($con);
                                                            mysqli_autocommit($con, TRUE);
                                                            echo "<script type=\"text/javascript\">alert(\"Empleado comisionado guardado correctamente\"); location.href='../../ht/trabajadores.php';</script>";
                                                        }    
                                                    }
                                                }
                                            }
                                        }   
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }//fin-if comisionado foráneo
        else //Sino el tipo de empleado es diferente de comisionado foráneo
        {                
            mysqli_autocommit($con, FALSE);
            if(!(mysqli_query($con,"Update trabajador SET numero_trabajador='$numero', nombre='$nombre',apellido_paterno='$a_pat',apellido_materno='$a_mat',depto_depto='$depto',categoria_categoria='$cat',tipo_tipo=$tipo WHERE (numero_trabajador='$anterior_num')")))
            {
                mysqli_rollback($con);
                mysqli_autocommit($con, TRUE); 
                echo "<script type=\"text/javascript\">alert('Error al actualizar los datos del trabajador, línea 377, verifique con el administrador de sistemas.'); history.back();</script>";
            }
            else
            {   
                if(!(mysqli_query($con,"Update acceso SET lunes=$semana[0],martes=$semana[1],miercoles=$semana[2],jueves=$semana[3],viernes=$semana[4],sabado=$semana[5],domingo=$semana[6],dia_festivo=$semana[7],turno_turno='$turno' WHERE trabajador_trabajador='$numero'")))
                {
                    mysqli_rollback($con);
                    mysqli_autocommit($con, TRUE); 
                    echo "<script type=\"text/javascript\">alert('Error al actualizar los días de trabajo o el turno, línea 385, verifique con el administrador de sistemas'); history.back();</script>";    
                }
                else
                { 
                    if(!(mysqli_query($con,"Update cumple_ono SET fecha_cumple='$cumple',fecha_ono='$ono' where (idcumple_ono=$idcumple_ono)")))
                    {
                        mysqli_rollback($con);
                        mysqli_autocommit($con, TRUE); 
                        echo "<script type=\"text/javascript\">alert('Error al actualizar el cumpleaños u onomástico del trabajador, línea 393, verifique con el administrador de sistemas'); history.back();</script>";                        }
                    else
                    {
                        if(!(mysqli_query($con,"Update tiempo_servicio SET fecha_alta='$fecha_alta' where (idtiempo_servicio=$idtiempo_servicio)")))
                        {
                            mysqli_rollback($con);
                            mysqli_autocommit($con, TRUE); 
                            echo "<script type=\"text/javascript\">alert('Error al actualizar el tiempo de servicio, línea 400, verifique con el administrador de sistemas. '); history.back(); </script>";
                        }
                        else
                        {  
                            //GUARDAR EN LA BITACORA DE TRABAJADOR
                            if(!(mysqli_query($con,"call inserta_bitacora_trabajador('Actualizado','$numero','$nombre','$a_pat','$a_mat','$depto','$cat','$descripcion_tipo','$genero','$anterior_num','$nombre_anterior','$a_paterno_anterior','$a_materno_anterior','$depto_anterior','$categoria_anterior','$descripcion_tipo','$genero_anterior', '$nombre_host')")))
                            {
                                $er1=mysqli_errno($con);
                                $er2=mysqli_error($con);
                                echo "<script type=\"text/javascript\">alert('Error al guardar en bitácora trabajador, línea 594, $er1: $er2'); history.back();</script>";
                                mysqli_rollback($con);
                                mysqli_autocommit($con, TRUE); 
                            }
                            else
                            {
                                //GUARDAR EN LA BITACORA DE CUMPLE_ONO
                                if(!(mysqli_query($con,"call inserta_bitacora_cumple_ono('Actualizado','$cumple','$ono','$cumple_anterior','$ono_anterior','$numero', '$nombre_host')")))
                                {
                                    mysqli_rollback($con);
                                    mysqli_autocommit($con, TRUE); 
                                    echo "<script type=\"text/javascript\">alert('Error al guardar en bitácora cumpleaños u onomástico, línea 419, verifique con el administrador de sistemas.'); history.back();</script>";
                                }
                                else
                                {  
                                    //GUARDAR EN LA BITACORA DE ACCESO
                                    if(!(mysqli_query($con,"call inserta_bitacora_acceso('Actualizado','$semana[0]','$semana[1]','$semana[2]','$semana[3]','$semana[4]','$semana[5]',$semana[6],$semana[7],'$turno','$lunes_anterior','$martes_anterior','$miercoles_anterior','$jueves_anterior','$viernes_anterior','$sabado_anterior','$domingo_anterior','$dias_festivos_anterior','$turno','$numero','$nombre_host')")))
                                    {
                                        mysqli_rollback($con);
                                        mysqli_autocommit($con, TRUE); 
                                        echo "<script type=\"text/javascript\">alert('Error al guardar en bitácora acceso, línea 428, verifique con el administrador de sistemas.'); history.back();</script>";
                                    }
                                    else
                                    {
                                        //GUARDAR EN LA BITACORA DE TIEMPO SERVICIO
                                        if(!(mysqli_query($con,"call inserta_bitacora_tiempo_servicio('Actualizado','$fecha_alta','$fecha_alta_anterior','$numero', '$nombre_host')")))
                                        {
                                            mysqli_rollback($con);
                                            mysqli_autocommit($con, TRUE); 
                                            echo "<script type=\"text/javascript\">alert('Error al guardar en tiempo de servicio, línea 585, verifique con el administrador de sistemas.'); history.back();</script>";
                                        }
                                        else
                                        {   //Si el trabajador tiene selecionado un turno con sexta
                                            if($existeSexta==1)
                                            {
                                                $result=tieneSexta($numero);
                                                if($result==false) //Si el trabajador no tiene una sexta registrada, será necesario guardar la sexta
                                                {
                                                    //Guardar la sexta 
                                                    if(!(mysqli_query($con,"Insert into sexta values ('',$semana2[0],$semana2[1],$semana2[2],$semana2[3],$semana2[4],$semana2[5],$semana2[6],$semana2[7],0,0,'$turno','$numero')")))
                                                    {
                                                        mysqli_rollback($con);
                                                        mysqli_autocommit($con, TRUE); 
                                                        echo "<script type=\"text/javascript\">alert('Error al guardar la sexta del trabajdor, línea 599, verifique con el administrador de sistemas.'); history.back();</script>";
                                                    }
                                                    else
                                                    {   //Guardar en bitacora sexta
                                                        if(!(mysqli_query($con,"call inserta_bitacora_sexta('Guardado','$semana2[0]','$semana2[1]','$semana2[2]','$semana2[3]','$semana2[4]','$semana2[5]','$semana2[6]','$semana2[7]','$turno','0','0','-', '-', '-', '-', '-', '-', '-', '-', '-','$numero','-','-','$nombre_host')")))
                                                        {
                                                            mysqli_rollback($con);
                                                            mysqli_autocommit($con, TRUE); 
                                                            echo "<script type=\"text/javascript\">alert('Error al guardar en bitácora sexta, línea 607, verifique con el administrador de sistemas.'); history.back();</script>";
                                                        }
                                                        else
                                                        {  //Si todo está correcto, guardar todo
                                                            mysqli_commit($con);
                                                            mysqli_autocommit($con, TRUE);
                                                            echo "<script type=\"text/javascript\">alert(\"Empleado actualizado correctamente\"); location.href='../../ht/trabajadores.php';</script>";

                                                        }
                                                    }
                                                }
                                                else
                                                { //Si el trabajador ya tiene registrada una sexta, solo se actualizará
                                                    
                                                    if(!(mysqli_query($con,"Update sexta SET lunes=$semana2[0],martes=$semana2[1],miercoles=$semana2[2],jueves=$semana2[3],viernes=$semana2[4],sabado=$semana2[5],domingo=$semana2[6],dia_festivo=$semana2[7],validez=0,t_dias=0,turno_turno='$turno' WHERE trabajador_trabajador='$numero'")))
                                                    {
                                                        mysqli_rollback($con);
                                                        mysqli_autocommit($con, TRUE); 
                                                        echo "<script type=\"text/javascript\">alert('Error al actualizar los datos de la sexta del trabajador, línea 625, verifique con el administrador de sistemas'); history.back();</script>";    
                                                    }
                                                    else
                                                    {
                                                        if(!(mysqli_query($con,"call inserta_bitacora_sexta('Actualizado','$semana2[0]','$semana2[1]','$semana2[2]','$semana2[3]','$semana2[4]','$semana2[5]','$semana2[6]','$semana2[7]','$turno','0','0','$result[1]', '$result[2]', '$result[3]', '$result[4]', '$result[5]', '$result[6]', '$result[7]', '$result[8]', '$result[11]','$numero','$result[9]','$result[10]','$nombre_host')")))
                                                        {
                                                            mysqli_rollback($con);
                                                            mysqli_autocommit($con, TRUE); 
                                                            echo "<script type=\"text/javascript\">alert('Error al guardar los datos en la bitácora sexta, línea 633, verifique con el administrador de sistemas'); history.back();</script>";    
                                                        }
                                                        else
                                                        { 

                                                            mysqli_commit($con);
                                                            mysqli_autocommit($con, TRUE);
                                                            echo "<script type=\"text/javascript\">alert(\"Empleado actualizado correctamente\"); location.href='../../ht/trabajadores.php';</script>";
                                                        }
                                                    }
                                                }
                                            }
                                            else //Si el trabajador no tiene un turno con sexta
                                            {
                                                //Pero trabajador ya tiene un turno con sexta registrada y va a cambiar a otro turno que no tiene sexta, será necesario eliminar su sexta
                                                $result=tieneSexta($numero);
                                                if(($result!==false) && ($t_horas_turno !=="06:00:00" || $t_horas_turno !=="06:30:00") && ($tipo==2 || $tipo==4))
                                                { 
                                                    if(!(mysqli_query($con,"DELETE FROM sexta WHERE idsexta = '$result[0]';")))
                                                    {
                                                        $error="Error al eliminar la sexta del trabajador, línea 285, verifique con el aministrador de sistemas";
                                                        echo "<script> alert('$error'); </script>";
                                                        mysqli_rollback($con);
                                                        mysqli_autocommit($con, TRUE); 
                                                      
                                                    }
                                                    else
                                                    { 
                                                        if(!(mysqli_query($con,"call inserta_bitacora_sexta('Eliminado','-','-','-', '-', '-', '-', '-', '-','-','-','-','$result[1]', '$result[2]', '$result[3]', '$result[4]', '$result[5]', '$result[6]', '$result[7]', '$result[8]','$turno','$numero','$result[9]','$result[10]','$nombre_host')")))
                                                        {
                                                           
                                                            $error="Error al insertar en la bitácora sexta, línea 298, verifique con el administrador de sistemas.";
                                                            //echo "Error, línea 669: " . mysqli_errno($con) . " : " . mysqli_error($con).", verifique con el administrador de sistemas";
                                                            echo "<script> alert('$error'); </script>";
                                                            mysqli_rollback($con);
                                                            mysqli_autocommit($con, TRUE); 
                                    
                                                        }
                                                        else
                                                        {
                                                           mysqli_commit($con);
                                                           mysqli_autocommit($con, TRUE);
                                                           echo "<script type=\"text/javascript\">alert(\"Empleado actualizado correctamente\"); location.href='../../ht/trabajadores.php';</script>"; 
                                                        
                                                        }
                                                    }
                                                }
                                                else
                                                { 
                                                    mysqli_commit($con);
                                                    mysqli_autocommit($con, TRUE);
                                                    echo "<script type=\"text/javascript\">alert(\"Empleado actualizado correctamente\"); location.href='../../ht/trabajadores.php';</script>"; 
                                                
                                                }
                                            }
                                        }
                                    }   
                                }
                            }
                        }
                    }
                }
            }
        }//fin de else
    }//fin de if (empty(salida))
    else
    {
        echo "<script> error('$salida'); history.back();</script>";
    }

?>

