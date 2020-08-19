<?php
session_start(); 
date_default_timezone_set('America/Mexico_City'); 
set_time_limit(600);//Indica que son 600 segundos, es decir 10 minutos máximo para ejecutar todo el script
    if (($_SESSION["name"]) && ($_SESSION["con"]))
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        require("../Acceso/global.php"); 
        require('../php/buscar_info_trabajador.php');
        $fecha_hoy=date("Y-m-d");//la fecha de hoy
        $fecha_ac = strtotime($fecha_hoy);
    }
    else
    {
        header("Location: ../index.php");
        die();
    }
?>
<script type="text/javascript">
    function error(cadena)
    {
        alert(cadena);
        history.back();
        exit();
    }
    function errordato(x)
    {
        mensaje='Error al insertar datos en la tabla '+x+', verifique con el administrador de sistemas.';
        alert(mensaje);
        history.back();
        exit();
    }
    function correcto(tipo)
    {
        mensaje='Empleado '+tipo+' guardado correctamente';
        alert(mensaje);
        location.href='../ht/trabajadores.php';
        exit();
    }
</script>

<?php
    $salida="";
    if(empty($_POST['num']))
    {
        $salida.="Debe escribir un número de trabajador. ";
    }
    if(empty($_POST['nom']))
    {
        $salida.="Debe escribir el NIP. ";
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

    $numero=$_POST['num'];
    $nip=$_POST['nip'];
    $buscarSiExisteNip=buscarSiExisteNip($nip);
    if($buscarSiExisteNip == true)
    {
        $salida.="El NIP ya existe, debe escribir uno diferente. ";
    }
    
    $nombre=$_POST['nom'];
    $nombre=strtoupper($nombre);

    $a_pat=$_POST['a_pat'];
    $a_pat=strtoupper($a_pat);

    $a_mat=$_POST['a_mat'];
    $a_mat=strtoupper($a_mat);

    $cat=$_POST['cat'];
    $depto=$_POST['depto'];
    $tipo=$_POST['tipo'];
    if($tipo == 3)//Si el tipo de empleado es eventual, no se deberá guardar una cuenta para repositorio
    {
        $guardarUserRepositorio='no';
    }
    else
    {
        $guardarUserRepositorio='si';
    }
    $existeSexta=0;

    $t_turno=$_POST['turno'];
    $separa=explode(" ",$t_turno);
    $turno=$separa[0];
    $t_horas=$separa[1];

    $cumple=$_POST['cumple'];
    $valores=explode('-',$cumple);
    $respuesta=esMayorEdad($cumple);
    if($respuesta==false)
    { 
        $salida.="El trabajador debe ser mayor de edad. ";
    }

    if (empty($_POST['t_opc']))
    {
        $salida.="Debe seleccionar si el empleado tiene o no tiene turno opcional. ";
    }
    else
    {
        $t_opc=$_POST['t_opc'];
    }

    $fecha_alta=$_POST['fecha_alta'];
    $valores2=explode('-',$fecha_alta);
    $resp=compararAnio($valores2[0]);
    if($resp==false)
    {
        $salida.="El año de alta del trabajador debe ser menor o igual al año actual. ";
    }
    //Validar el tiempo de antiguedad del trabajador si es menor de 6 meses y 1 día no pude ser de base, comisionado foraneo o confianza
    if($tipo == 1 || $tipo == 2 || $tipo == 4 )
    {
        $f_alta= new DateTime($fecha_alta);
        $fHoy= new DateTime( $fecha_hoy);
        $antiguedad= $f_alta->diff($fHoy);
        $totDiasTrabajando=$antiguedad->format('%a');
        $totDiasTrabajando=$totDiasTrabajando/365;
        if($totDiasTrabajando <= 0.5)
        {
            $salida.="La antiguedad o tiempo de alta del trabajador debe ser mayor de 6 meses";
        }
    }

    if (empty($_POST['dia']))
    {
        $salida.="Debe seleccionar al menos un día de trabajo. ";
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
            $salida.="El año de la fecha de onomástico es incorrecto.";
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

    if (empty($_POST['cumpleOno']))
    {
        $salida.="Debe seleccionar el día de descanso que desea el trabajador. ";
    }
    else
    {
        $validezCumpleOno=$_POST['cumpleOno'];
        if ($validezCumpleOno=='cum') //cumpleaños
        {
            $validezCumpleOno='0';
        }
        else
        { //onomástico
            $validezCumpleOno='1';
        }
    }
    
    if (!(empty($_POST['diaS'])))
    {
        $diasSexta=$_POST['diaS'];
        $existeSexta=1;
        $semana2 = array(0,0,0,0,0,0,0,0);
        $num2=count($diasSexta);
        for($n=0;$n<$num2;$n++)
        {
            if($diasSexta[$n]=="lunes"){$semana2[0]=1;}
            if($diasSexta[$n]=="martes"){$semana2[1]=1;}
            if($diasSexta[$n]=="miercoles"){$semana2[2]=1;}
            if($diasSexta[$n]=="jueves"){$semana2[3]=1;}
            if($diasSexta[$n]=="viernes"){$semana2[4]=1;}
            if($diasSexta[$n]=="sabado"){$semana2[5]=1;}
            if($diasSexta[$n]=="domingo"){$semana2[6]=1;}
            if($diasSexta[$n]=="dias_festivos"){$semana2[7]=1;}
        }
    }

    //Aqui consulto si existe ese numero de trabajador 
    $ejecu="select * from trabajador where numero_trabajador = '$numero'";
    $codigo=mysqli_query($con,$ejecu);
    $consultar=mysqli_num_rows($codigo);
    //si el trabajador ya está registrado, avisame que ya existe
    if($consultar==1)
    {
        $salida.="El empleado ya existe";
        echo "<script> error('$salida');</script>";
    }   
    else
    {
        if(strlen($valores[0])>4)
        {
            $salida.="El año de nacimiento es incorrecto.";
        }
        if(strlen($valores2[0])>4)
        {
            $salida.="El año de la fecha de alta es incorrecto.";
        }
        //Si es comisionado foráneo
        if($tipo==4)
        {
            $empresa=$_POST['emp'];
            $f_ini=$_POST['f_ini'];
            $f_fin=$_POST['f_fin'];
            $empresa = trim($empresa);
            if(empty($empresa))
            {
                $salida.="No ha escrito el nombre de la empresa.";
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
            $totDias=$totDias+1;
            //si el periodo de comisión es superior a 165 días (5 meses y medio)
            if($totDias>165)
            {
                $salida.="No se puede comisionar más de 5 meses y medio.";
            }

            $fecha_in = strtotime($f_ini);
            $fecha_fin = strtotime($f_fin);
             //Si la fecha actual es mayor que la fecha inicial de la comisión entonces
            if($fecha_ac > $fecha_in)
            {
                $salida.="La fecha de inicio de la comisión ya pasó, no es posible registrar una comisión que inició antes de hoy .";
            }
            if($fecha_ac==$fecha_in)
            {
                $salida.="La comisión empieza hoy y no puede registrarse debido a que se requiere mínimo un día de anticipación.";
            }
            //Si la fecha de fin es menor que la fecha inicial de la comisión entonces
            if($fecha_fin < $fecha_in)
            {
                $salida.="La fecha de fin de la comisión debe ser mayor o igual a la fecha de inicio de la comisión. ";
            }
        }//fin if-si es comisionado foráneo

        //Si salida está vacía, significa que no ocurrio algún error 
        $semana = array(0,0,0,0,0,0,0,0);
        $num=count($dias);
        for($n=0;$n<$num;$n++)
        {
            if($dias[$n]=="lunes"){$semana[0]=1;}
            if($dias[$n]=="martes"){$semana[1]=1;}
            if($dias[$n]=="miercoles"){$semana[2]=1;}
            if($dias[$n]=="jueves"){$semana[3]=1;}
            if($dias[$n]=="viernes"){$semana[4]=1;}
            if($dias[$n]=="sabado"){$semana[5]=1;}
            if($dias[$n]=="domingo"){$semana[6]=1;}
            if($dias[$n]=="dias_festivos"){$semana[7]=1;}
        }
        $acceso=$semana[0].$semana[1].$semana[2].$semana[3].$semana[4].$semana[5].$semana[6].$semana[7];
        $siguardar="";//Sirve para saber si se guardará el turno opcional
        $siguardarEnAF="";//Sirve para saber si el empleado se guardará en la tabla af (acceso del día festivo solo si trabajan sabado, domingo y festivo con un turno de 12:00:00 horas)
        if($t_opc=="si")
        {
            $validaTurnoOpcional=validaTurnoOpcional($numero, $cat, $t_horas, $acceso);
            if($validaTurnoOpcional == true)
            {
                $siguardar="si";
            }
        }
        else
        {
            if($t_horas=="12:00:00" && $acceso=="00000111")
            {
                $siguardarEnAF="si";
            }
        }
        //Si todo está correcto, comenzar a guardar todo en la bd
        if(empty($salida))
        {
            if($tipo==4)//Si el trabajador es comisionado foráneo
            { 
                mysqli_autocommit($con, FALSE);  
                if(!(mysqli_query($con,"Insert into trabajador values ('$numero','$nombre','$a_pat','$a_mat','$depto','$cat',$tipo,'$genero',$nip)")))
                {
                    $er1=mysqli_errno($con);
                    $er2=mysqli_error($con);
                    $línea='331';
                    error($er1,$er2,$línea);
                    mysqli_rollback($con);
                    mysqli_autocommit($con, TRUE); 
                }
                else
                { 
                    if(!(mysqli_query($con,"Insert into acceso values ('','$semana[0]','$semana[1]','$semana[2]','$semana[3]','$semana[4]','$semana[5]',$semana[6],$semana[7],'$turno','$numero',-1)")))
                    {
                        $er1=mysqli_errno($con);
                        $er2=mysqli_error($con);
                        $línea='342';
                        error($er1,$er2,$línea);
                        mysqli_rollback($con);
                        mysqli_autocommit($con, TRUE); 
                    }
                    else
                    {   $idacceso=mysqli_insert_id($con);//Sirve para saber el ultimo id guardado en la tabla acceso
                        //Nota: la validez de cumple_ono será siempre 0 porque siempre está valido el cumpleaños del empleado
                        //El 0 indica que el cumpleaños u onomástico no ha sido tomado
                        if(!(mysqli_query($con,"Insert into cumple_ono values ('','$cumple','$ono',$validezCumpleOno,'$numero','0')")))
                        { 
                            $er1=mysqli_errno($con);
                            $er2=mysqli_error($con);
                            $línea='354';
                            error($er1,$er2,$línea);
                            mysqli_rollback($con);
                            mysqli_autocommit($con, TRUE); 
                        }
                        else
                        {
                            if(!(mysqli_query($con,"Insert into tiempo_servicio values ('','$fecha_alta','$numero')")))
                            {
                                $er1=mysqli_errno($con);
                                $er2=mysqli_error($con);
                                $línea='365';
                                error($er1,$er2,$línea);
                                mysqli_rollback($con);
                                mysqli_autocommit($con, TRUE); 
                            }
                            else
                            {
                                if(!(mysqli_query($con,"Insert into especial values ('','$f_ini','$f_fin','','',0,'$numero','CS','$empresa',$totDias)")))
                                {
                                    $er1=mysqli_errno($con);
                                    $er2=mysqli_error($con);
                                    $línea='376';
                                    error($er1,$er2,$línea);
                                    mysqli_rollback($con);
                                    mysqli_autocommit($con, TRUE); 
                                }
                                else
                                {
                                    // OBTENER LA DESCRIPCION DEL TIPO DE EMPLEADO PARA VERLO EN LA BITACORA
                                    $descripcion=describeTipoTrabajador($numero,$tipo);
                                    $descripcion_tipo=$descripcion;
                                    //Nombre del equipo
                                    $nombre_host= gethostname();
                                    //GUARDAR EN LA BITACORA DE TRABAJADOR
                                    $guardadoBitacoraTrabajador=bitacoraTrabajador($numero, $nombre, $a_pat, $a_mat, $depto, $cat, $descripcion_tipo, $genero, $nombre_host);
                                    if($guardadoBitacoraTrabajador == true)
                                    {
                                        //GUARDAR EN LA BITACORA DE CUMPLE_ONO
                                        if(!(mysqli_query($con,"call inserta_bitacora_cumple_ono('Guardado','$cumple','$ono','-','-','$numero', '$nombre_host')")))
                                        {
                                            $er1=mysqli_errno($con);
                                            $er2=mysqli_error($con);
                                            $línea='399';
                                            error($er1,$er2,$línea);
                                            mysqli_rollback($con);
                                            mysqli_autocommit($con, TRUE); 
                                        }
                                        else
                                        {    //GUARDAR EN LA BITACORA DE ACCESO
                                            if(!(mysqli_query($con,"call inserta_bitacora_acceso('Guardado','$semana[0]','$semana[1]','$semana[2]','$semana[3]','$semana[4]','$semana[5]',$semana[6],$semana[7],'$turno','-','-','-','-','-','-','-','-','-','$numero','$nombre_host')")))
                                            {
                                                $er1=mysqli_errno($con);
                                                $er2=mysqli_error($con);
                                                $línea='410';
                                                error($er1,$er2,$línea);
                                                mysqli_rollback($con);
                                                mysqli_autocommit($con, TRUE); 
                                            }
                                            else
                                            { 
                                                if($existeSexta==1)
                                                {
                                                    //GUARDAR LA SEXTA
                                                    if(!(mysqli_query($con,"Insert into sexta values ('',$semana2[0],$semana2[1],$semana2[2],$semana2[3],$semana2[4],$semana2[5],$semana2[6],$semana2[7],0,0,'$turno','$numero')")))
                                                    {
                                                        $er1=mysqli_errno($con);
                                                        $er2=mysqli_error($con);
                                                        $línea='424';
                                                        error($er1,$er2,$línea);
                                                        mysqli_rollback($con);
                                                        mysqli_autocommit($con, TRUE); 
                                                    }
                                                    else
                                                    {
                                                        //GUARDAR EN LA BITACORA DE TIEMPO SERVICIO
                                                        if(!(mysqli_query($con,"call inserta_bitacora_tiempo_servicio('Guardado','$fecha_alta','-','$numero', '$nombre_host')")))
                                                        {
                                                            $er1=mysqli_errno($con);
                                                            $er2=mysqli_error($con);
                                                            $línea='436';
                                                            error($er1,$er2,$línea);
                                                            mysqli_rollback($con);
                                                            mysqli_autocommit($con, TRUE); 
                                                        }
                                                        else
                                                        {
                                                            //GUARDAR EN LA BITACORA DE ESPECIAL
                                                            if(!(mysqli_query($con,"call inserta_bitacora_especial('Guardado','$f_ini','$f_fin','-','-','CS','$empresa',$totDias,'-','-','-','-','-','-','-','$numero','$nombre_host',-1)")))
                                                            {
                                                                $er1=mysqli_errno($con);
                                                                $er2=mysqli_error($con);
                                                                $línea='448';
                                                                error($er1,$er2,$línea);
                                                                mysqli_rollback($con);
                                                                mysqli_autocommit($con, TRUE); 
                                                            }
                                                            else
                                                            {   //GUARDAR EN LA BITACORA SEXTA
                                                                if(!(mysqli_query($con,"call inserta_bitacora_sexta('Guardado','$semana2[0]','$semana2[1]','$semana2[2]','$semana2[3]','$semana2[4]','$semana2[5]','$semana2[6]','$semana2[7]','$turno','0','0','-', '-', '-', '-', '-', '-', '-', '-', '-','$numero','-','-','$nombre_host')")))
                                                                {
                                                                    $er1=mysqli_errno($con);
                                                                    $er2=mysqli_error($con);
                                                                    $línea='459';
                                                                    error($er1,$er2,$línea);
                                                                    mysqli_rollback($con);
                                                                    mysqli_autocommit($con, TRUE); 
                                                                }
                                                                else
                                                                {   
                                                                    $res=insertaUsuario($numero);
                                                                    if($res==true)
                                                                    {
                                                                        $correcto=insertaPeriodoVacaciones($numero,$depto);
                                                                        mysqli_commit($con);
                                                                        mysqli_autocommit($con, TRUE);
                                                                        $tipo='comisionado foráneo';
                                                                        echo "<script> correcto('$tipo');</script>";
                                                                    }
                                                                }   
                                                            }
                                                        } 
                                                    }
                                                }
                                                else //Guardar si no tiene sexta 
                                                {
                                                    //GUARDAR EN LA BITACORA DE TIEMPO SERVICIO
                                                    if(!(mysqli_query($con,"call inserta_bitacora_tiempo_servicio('Guardado','$fecha_alta','-','$numero', '$nombre_host')")))
                                                    {
                                                        $er1=mysqli_errno($con);
                                                        $er2=mysqli_error($con);
                                                        $línea='489';
                                                        error($er1,$er2,$línea);
                                                        mysqli_rollback($con);
                                                        mysqli_autocommit($con, TRUE); 
                                                    }
                                                    else
                                                    {
                                                        //GUARDAR EN LA BITACORA DE ESPECIAL
                                                        if(!(mysqli_query($con,"call inserta_bitacora_especial('Guardado','$f_ini','$f_fin','-','-','CS','$empresa',$totDias,'-','-','-','-','-','-','-','$numero','$nombre_host',-1)")))
                                                        {
                                                            $er1=mysqli_errno($con);
                                                            $er2=mysqli_error($con);
                                                            $línea='497';
                                                            error($er1,$er2,$línea);
                                                            mysqli_rollback($con);
                                                            mysqli_autocommit($con, TRUE); 
                                                        }
                                                        else
                                                        {   
                                                            $res=insertaUsuario($numero);
                                                            if($res==true)
                                                            {
                                                                if($t_opc=="si" && $siguardar=="si")
                                                                {
                                                                    $guardado=insertaTurnoOpcional($numero);
                                                                    $correcto=insertaPeriodoVacaciones($numero,$depto);
                                                                    mysqli_commit($con);
                                                                    mysqli_autocommit($con, TRUE);
                                                                    $tipo='comisionado foráneo';
                                                                    echo "<script> correcto('$tipo');</script>";
                                                                }
                                                                else
                                                                {
                                                                    $correcto=insertaPeriodoVacaciones($numero,$depto);
                                                                    if( $siguardarEnAF=="si")
                                                                    {   $guardado=insertaEnAF($idacceso);
                                                                        mysqli_commit($con);
                                                                        mysqli_autocommit($con, TRUE);
                                                                        $tipo='comisionado foráneo';
                                                                        echo "<script> correcto('$tipo');</script>";
                                                                    }
                                                                    else
                                                                    {
                                                                        mysqli_commit($con);
                                                                        mysqli_autocommit($con, TRUE);
                                                                        $tipo='comisionado foráneo';
                                                                        echo "<script> correcto('$tipo');</script>";
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
                    }
                }
            }//fin-if comisionado foráneo
            else
            {   mysqli_autocommit($con, FALSE);
                //Si el empleado es de base, confianza o eventual, guardar los siguientes datos:
                if(!(mysqli_query($con,"Insert into trabajador values ('$numero','$nombre','$a_pat','$a_mat','$depto','$cat',$tipo,'$genero',$nip)")))
                {
                    $er1=mysqli_errno($con);
                    $er2=mysqli_error($con);
                    $línea='530';
                    error($er1,$er2,$línea);
                    mysqli_rollback($con);
                    mysqli_autocommit($con, TRUE);
                }
                else
                { 
                    if(!(mysqli_query($con,"Insert into acceso values ('','$semana[0]','$semana[1]','$semana[2]','$semana[3]','$semana[4]','$semana[5]',$semana[6],$semana[7],'$turno','$numero',-1)")))
                    {
                        $er1=mysqli_errno($con);
                        $er2=mysqli_error($con);
                        $línea='541';
                        error($er1,$er2,$línea);
                        mysqli_rollback($con);
                        mysqli_autocommit($con, TRUE);
                    }
                    else
                    {
                        $idacceso=mysqli_insert_id($con);
                        if(!(mysqli_query($con,"Insert into cumple_ono values ('','$cumple','$ono',1,'$numero')")))
                        {
                            $er1=mysqli_errno($con);
                            $er2=mysqli_error($con);
                            $línea='552';
                            error($er1,$er2,$línea);
                            mysqli_rollback($con);
                            mysqli_autocommit($con, TRUE);
                        }
                        else
                        {
                            if(!(mysqli_query($con,"Insert into tiempo_servicio values ('','$fecha_alta','$numero')")))
                            {
                                $er1=mysqli_errno($con);
                                $er2=mysqli_error($con);
                                $línea='563';
                                error($er1,$er2,$línea);
                                mysqli_rollback($con);
                                mysqli_autocommit($con, TRUE);
                            }                            
                            else
                            {  
                                // OBTENER LA DESCRIPCION DEL TIPO DE EMPLEADO PARA VERLO EN LA BITACORA
                                $descripcion=describeTipoTrabajador($numero,$tipo);
                                $descripcion_tipo=$descripcion;

                                //Nombre del equipo
                                $nombre_host= gethostname();

                                //GUARDAR EN LA BITACORA DE TRABAJADOR
                                $guardadoBitacoraTrabajador=bitacoraTrabajador($numero, $nombre, $a_pat, $a_mat, $depto, $cat, $descripcion_tipo, $genero, $nombre_host);
                                if($guardadoBitacoraTrabajador == true)
                                {
                                    //GUARDAR EN LA BITACORA DE CUMPLE_ONO
                                    if(!(mysqli_query($con,"call inserta_bitacora_cumple_ono('Guardado','$cumple','$ono','-','-','$numero', '$nombre_host')")))
                                    {
                                        $er1=mysqli_errno($con);
                                        $er2=mysqli_error($con);
                                        $línea='586';
                                        error($er1,$er2,$línea);
                                        mysqli_rollback($con);
                                        mysqli_autocommit($con, TRUE);
                                    }
                                    else
                                    {    //GUARDAR EN LA BITACORA DE ACCESO
                                        if(!(mysqli_query($con,"call inserta_bitacora_acceso('Guardado','$semana[0]','$semana[1]','$semana[2]','$semana[3]','$semana[4]','$semana[5]',$semana[6],$semana[7],'$turno','-','-','-','-','-','-','-','-','-','$numero','$nombre_host')")))
                                        {
                                            $er1=mysqli_errno($con);
                                            $er2=mysqli_error($con);
                                            $línea='597';
                                            error($er1,$er2,$línea);
                                            mysqli_rollback($con);
                                            mysqli_autocommit($con, TRUE);
                                        }
                                        else
                                        {
                                            //Si el empleado tiene sexta 
                                            if($existeSexta==1)
                                            {    //Guardar la sexta
                                                if(!(mysqli_query($con,"Insert into sexta values ('','$semana2[0]','$semana2[1]','$semana2[2]','$semana2[3]','$semana2[4]','$semana2[5]',$semana2[6],$semana2[7],0,0,'$turno','$numero')")))
                                                {
                                                    $er1=mysqli_errno($con);
                                                    $er2=mysqli_error($con);
                                                    $línea='615';
                                                    error($er1,$er2,$línea);
                                                    mysqli_rollback($con);
                                                    mysqli_autocommit($con, TRUE);
                                                }
                                                else
                                                {
                                                    //GUARDAR EN LA BITACORA DE TIEMPO SERVICIO
                                                    if(!(mysqli_query($con,"call inserta_bitacora_tiempo_servicio('Guardado','$fecha_alta','-','$numero', '$nombre_host')")))
                                                    {
                                                        $er1=mysqli_errno($con);
                                                        $er2=mysqli_error($con);
                                                        $línea='623';
                                                        error($er1,$er2,$línea);
                                                        mysqli_rollback($con);
                                                        mysqli_autocommit($con, TRUE);
                                                    }
                                                    else
                                                    {    //GUARDAR EN BITACORA SEXTA                                                              
                                                        if(!(mysqli_query($con,"call inserta_bitacora_sexta('Guardado','$semana2[0]','$semana2[1]','$semana2[2]','$semana2[3]','$semana2[4]','$semana2[5]','$semana2[6]','$semana2[7]','$turno','0','0','-', '-', '-', '-', '-', '-', '-', '-', '-','$numero','-','-','$nombre_host')")))
                                                        {
                                                            $er1=mysqli_errno($con);
                                                            $er2=mysqli_error($con);
                                                            $línea='634';
                                                            error($er1,$er2,$línea);
                                                            mysqli_rollback($con);
                                                            mysqli_autocommit($con, TRUE);
                                                        }
                                                        else
                                                        {  //Si el usuario es de confianza, base, comisionado foráneo guardar su usuario para el repositorio y sus días de vacaciones
                                                            if($guardarUserRepositorio=='si')
                                                            {
                                                                $res=insertaUsuario($numero);
                                                                if($res==true)
                                                                {
                                                                    $correcto=insertaPeriodoVacaciones($numero,$depto);
                                                                    mysqli_commit($con);
                                                                    mysqli_autocommit($con, TRUE);
                                                                    $tipo='';
                                                                    echo "<script> correcto('$tipo');</script>";
                                                                }
                                                            }
                                                            else
                                                            { 
                                                                mysqli_commit($con);
                                                                mysqli_autocommit($con, TRUE);
                                                                $tipo='';
                                                                echo "<script> correcto('$tipo');</script>";
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            else //SI EL EMPLEADO NO TIENE SEXTA
                                            {
                                                //GUARDAR EN LA BITACORA DE TIEMPO SERVICIO
                                                if(!(mysqli_query($con,"call inserta_bitacora_tiempo_servicio('Guardado','$fecha_alta','-','$numero', '$nombre_host')")))
                                                {
                                                    $er1=mysqli_errno($con);
                                                    $er2=mysqli_error($con);
                                                    $línea='659';
                                                    error($er1,$er2,$línea);
                                                    mysqli_rollback($con);
                                                    mysqli_autocommit($con, TRUE);
                                                }
                                                else
                                                {   
                                                    $res=insertaUsuario($numero);
                                                    if($res==true)
                                                    {
                                                        if($t_opc=="si" && $siguardar=="si")
                                                        {
                                                            $guardado=insertaTurnoOpcional($numero);
                                                            $correcto=insertaPeriodoVacaciones($numero,$depto);
                                                            mysqli_commit($con);
                                                            mysqli_autocommit($con, TRUE);
                                                            $tipo='';
                                                            echo "<script> correcto('$tipo');</script>";
                                                        }
                                                        else
                                                        {
                                                            $correcto=insertaPeriodoVacaciones($numero,$depto);
                                                            if( $siguardarEnAF=="si")
                                                            {   
                                                                $guardado=insertaEnAF($idacceso);
                                                                mysqli_commit($con);
                                                                mysqli_autocommit($con, TRUE);
                                                                $tipo='';
                                                                echo "<script> correcto('$tipo');</script>";
                                                            }
                                                            else
                                                            {
                                                                mysqli_commit($con);
                                                                mysqli_autocommit($con, TRUE);
                                                                $tipo='';
                                                                echo "<script> correcto('$tipo');</script>";
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
            }
        }//fin de if (empty(salida))
        else
        {
          echo "<script> error('$salida'); </script>";
        }
    } 

    function bitacoraTrabajador($numero, $nombre, $a_pat, $a_mat, $depto, $cat, $tipo, $genero, $nombre_host)
    {
        global $con;
        $sql="call inserta_bitacora_trabajador('Guardar','$numero', '$nombre', '$a_pat', '$a_mat', '$depto', '$cat', '$tipo', '$genero', '-', '-', '-', '-', '-', '-', '-', '-', '$nombre_host');"; 
        mysqli_autocommit($con, FALSE);//quitar el autocommit hasta que todo haya resultado correcto
        if(!(mysqli_query($con,$sql)))
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $línea='789';
            error($er1,$er2,$línea);
            mysqli_rollback($con);
            mysqli_autocommit($con, TRUE); 
        }
        else
        {
            return true;
        } 
    }

    function insertaPeriodoVacaciones($numero,$depto)
    {
        global $con;
        $p1=0;
        $p2=0;
        $fecha_hoy=date("Y-m-d");//la fecha de hoy
        $anio=date("Y");
        $f_ini_p1=$anio.'-01-01';//fecha de inicio del periodo 1
        $f_fin_p1=$anio.'-06-30';//fecha de fin del periodo 1
        $fecha_ac = strtotime($fecha_hoy);
        $fecha_in_p1 = strtotime($f_ini_p1);
        $fecha_fin_p1 = strtotime($f_fin_p1);
        /*
            Si la fecha actual está entre enero y junio, el periodo 1 estará activo (es decir, en 1). 
            Sino estará en 0 y periodo 2 estará en 1
        */
        if($fecha_ac >= $fecha_in_p1 && $fecha_ac <= $fecha_fin_p1 )
        {   
            $p1=1; 
        }
        else 
        { 
            $p2=1; 
        }
                
        if($depto=="09211" || $depto=="09200" || $depto=="51521")
        {
            /*  Departamentos de radio
                '09200', 'RADIOLOGIA'
                '51521', 'RADIOLOGIA E IMAGEN'
                '09211', 'RADIOTERAPIA'
            */
            $sql="INSERT INTO vacaciones_radio (val_p1, val_p2, val_p3, val_p4, trabajador_trabajador) VALUES ($p1, $p2, '0', '0', '$numero');"; 
            if(!(mysqli_query($con,$sql)))
            {
                $er1=mysqli_errno($con);
                $er2=mysqli_error($con);
                $línea='838';
                error($er1,$er2,$línea);
                mysqli_rollback($con);
                mysqli_autocommit($con, TRUE); 
            }
            else
            {
                return true;
            } 
        }   
        else
        {
            $sql="INSERT INTO vacaciones (val_p1, val_p2, trabajador_trabajador) VALUES ($p1, $p2,'$numero');"; 
            if(!(mysqli_query($con,$sql)))
            {
                $er1=mysqli_errno($con);
                $er2=mysqli_error($con);
                $línea='855';
                error($er1,$er2,$línea);
                mysqli_rollback($con);
                mysqli_autocommit($con, TRUE); 
            }
            else
            {
                return true;
            } 
        }  
    }

    function error($er1,$er2,$numLinea)
    {
        $error="";
        $err1="$er1";
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

        $error="$err1 : $err2. Línea de error: $numLinea. Verifique con el administrador de sistemas";
        echo"<script>error('$error'); </script>";
        exit();
    }
?>

