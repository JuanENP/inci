<?php
session_start(); 
    date_default_timezone_set('America/Mexico_City');
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
    function Alerta()
    {
        alert("Correcto");
        location.href="../../ht/categoria.php";
        //window.close();
        //Si quieres usar instrucciones php, salte del script y coloca la apertura y cierre de php, escribe dentro de ellas de forma normal
    }
    function error(cadena)
    {
        alert(cadena);
        history.back();
    }
</script>

<?php
    $anterior_num=$_SESSION['anterior_num'];//anterior numero de empleado
    $numero=$_POST['num'];
    $nombre=$_POST['nom'];
    $a_pat=$_POST['a_pat'];
    $a_mat=$_POST['a_mat'];
    $cat=$_POST['cat'];
    $depto=$_POST['depto'];
    $tipo=$_POST['tipo'];
    $todoturno=$_POST['turno'];
    $separa=explode(' ',$todoturno);
    $turno=$separa[0];
    $t_horas_turno=$separa[1];
    $cumple=$_POST['cumple'];
    $valores=explode('-',$cumple);
    $fecha_alta=$_POST['fecha_alta'];
    $valores2=explode('-',$fecha_alta);
    $salida="";
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


/////////////VALIDAR SI EL NÚMERO DE EMPLEADO ES EL MISMO ////////////////////////
    if($numero==$anterior_num)
    {
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
            $empresa=$_POST['emp'];
            $f_ini=$_POST['f_ini'];
            $f_fin=$_POST['f_fin'];
            $empresa_validar=trim($empresa);
            if(empty($empresa_validar))
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
            //si el periodo de comisión es superior a 165 días (5 meses y medio)
            if($totDias>165)
            {
                $salida.="No se puede comisionar más de 5 meses y medio.";
            }

            $fecha_hoy=date("Y-m-d");//la fecha de hoy
            $fecha_ac = strtotime($fecha_hoy);
            $fecha_in = strtotime($f_ini);
             //Si la fecha actual es mayor que la fecha inicial de la comisión entonces
            if($fecha_ac > $fecha_in)
            {
                $salida.="La fecha de inicio de la comisión ya pasó, no es posible registrar una comisión que inició antes de hoy .";
            }
            if($fecha_ac==$fecha_in)
            {
                $salida.="La comisión empieza hoy y no puede registrarse debido a que se requiere mínimo un día de anticipación.";
            }
        }//fin if-si es comisionado foráneo

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
            //Si el tipo de empleado es comisionado foráneo y es el mismo número de trabajador , relizar lo siguiente
            if($tipo==4)
            { 
                            //////////////////////////
                // OBTENER TODOS LOS DATOS DEL TRABAJADOR
                $sql="SELECT *  FROM trabajador WHERE numero_trabajador=$numero;";
                $query= mysqli_query($con, $sql) or die();
                $fila=mysqli_fetch_array($query);
                $nombre_anterior=$fila[1];
                $a_paterno_anterior=$fila[2];
                $a_materno_anterior=$fila[3];
                $depto_anterior=$fila[4];
                $categoria_anterior=$fila[5];
                $tipo_anterior=$fila[6];
                // OBTENER TODOS LOS DATOS DE CUMPLE_ONO
                $sql="SELECT *  FROM cumple_ono WHERE trabajador_trabajador=$numero;";
                $query= mysqli_query($con, $sql) or die();
                $fila=mysqli_fetch_array($query);
                $cumple_anterior=$fila[1];
                $ono_anterior=$fila[2];
                // OBTENER TODOS LOS DATOS DE ACCESO
                $sql="SELECT *  FROM acceso WHERE trabajador_trabajador=$numero;";
                $query= mysqli_query($con, $sql) or die();
                $fila=mysqli_fetch_array($query);
                $lunes_anterior=$fila[1];
                $martes_anterior=$fila[2];
                $miercoles_anterior=$fila[3];
                $jueves_anterior=$fila[4];
                $viernes_anterior=$fila[5];
                $sabado_anterior=$fila[6];
                $domingo_anterior=$fila[7];
                $dias_festivos_anterior=$fila[8];
                $turno_anterior=$fila[9];
                // OBTENER TODOS LOS DATOS DEL TIEMPO DE SERVICIO
                $sql="SELECT *  FROM tiempo_servicio WHERE trabajador_trabajador=$numero;";
                $query= mysqli_query($con, $sql) or die();
                $fila=mysqli_fetch_array($query);
                $fecha_alta_anterior=$fila[1];
                // OBTENER TODOS LOS DATOS DE ESPECIAL
                $sql="SELECT *  FROM especial WHERE trabajador_trabajador=$numero;";
                $query= mysqli_query($con, $sql) or die();
                $fila=mysqli_fetch_array($query);
                $f_ini_anterior=$fila[1];
                $f_fin_anterior=$fila[2];
                $clave_especial_anterior=$fila[7];
                $empresa_anterior=$fila[8];
                $duracion_anterior=$fila[9];    
                
                ////////////////////////// 
                
                mysqli_autocommit($con, FALSE);
                //UPDATE `checada6`.`trabajador` SET `numero_trabajador` = '101010', `nombre` = 'DANIAA', `apellido_paterno` = 'MARTINEZA', `apellido_materno` = 'FLORESA', `depto_depto` = '043202', `categoria_categoria` = 'HE00702', `tipo_tipo` = '2' WHERE (`numero_trabajador` = '1010');
                if(!(mysqli_query($con,"Update trabajador SET nombre='$nombre',apellido_paterno='$a_pat',apellido_materno='$a_mat',depto_depto='$depto',categoria_categoria='$cat',tipo_tipo=$tipo WHERE numero_trabajador='$numero'")))
                {
                    mysqli_rollback($con);
                    mysqli_autocommit($con, TRUE); 
                    echo "<script type=\"text/javascript\">alert('Datos incorrectos del trabajador'); history.back();</script>";
                }
                else
                {   
                    //UPDATE `checada6`.`acceso` SET `lunes` = '0', `martes` = '1', `miercoles` = '1', `jueves` = '1', `viernes` = '0', `sabado` = '0', `domingo` = '0', `dia_festivo` = '1', `turno_turno` = 'T2' WHERE (`idacceso` = '1');
                    if(!(mysqli_query($con,"Update acceso SET lunes=$semana[0],martes=$semana[1],miercoles=$semana[2],jueves=$semana[3],viernes=$semana[4],sabado=$semana[5],domingo=$semana[6],dia_festivo=$semana[7],turno_turno='$turno' WHERE trabajador_trabajador='$numero'")))
                    {
                        mysqli_rollback($con);
                        mysqli_autocommit($con, TRUE); 
                        echo "<script type=\"text/javascript\">alert('Datos incorrectos de los días de trabajo o el turno 2'); history.back();</script>";    
                    }
                    else
                    { //UPDATE `checada6`.`cumple_ono` SET `fecha_cumple` = '2002-02-22', `fecha_ono` = '2002-02-21' WHERE (`idcumple_ono` = '40');
                        //Obtener el id de cumple_ono del trabajador que se actualizará
                        $sql="SELECT idcumple_ono  FROM cumple_ono where trabajador_trabajador='$numero'";
                        $query= mysqli_query($con, $sql) or die();
                        $fila=mysqli_fetch_array($query);
                        $idcumple_ono=$fila[0];
                        if(!(mysqli_query($con,"Update cumple_ono SET fecha_cumple='$cumple',fecha_ono='$ono' where (idcumple_ono=$idcumple_ono)")))
                        {
                            mysqli_rollback($con);
                            mysqli_autocommit($con, TRUE); 
                            echo "<script type=\"text/javascript\">alert('Datos incorrectos del cumpleaños u onomástico'); history.back();</script>";            
                        }
                        else
                        {
                            //Actualizar la fecha de alta del trabajador
                            //UPDATE `checada6`.`tiempo_servicio` SET `fecha_alta` = '2020-04-02' WHERE (`idtiempo_servicio` = '4');
                            $sql="SELECT idtiempo_servicio  FROM tiempo_servicio where trabajador_trabajador='$numero'";
                            $query= mysqli_query($con, $sql) or die();
                            $fila=mysqli_fetch_array($query);
                            $idtiempo_servicio=$fila[0];
                            if(!(mysqli_query($con,"Update tiempo_servicio SET fecha_alta='$fecha_alta' where idtiempo_servicio=$idtiempo_servicio")))
                            {
                                mysqli_rollback($con);
                                mysqli_autocommit($con, TRUE); 
                                echo "<script type=\"text/javascript\">alert('Datos incorrectos del tiempo de servicio'); history.back();</script>";
                            }
                            else
                            {   //UPDATE `checada6`.`especial` SET `fecha_inicio` = '2020-04-22', `fecha_fin` = '2020-04-26', `empresa` = 'HOSPITALL', `duracion` = '7' WHERE (`idespecial` = '25');
                                $sql="SELECT idespecial  FROM especial where trabajador_trabajador='$numero'";
                                $query= mysqli_query($con, $sql) or die();
                                $fila=mysqli_fetch_array($query);
                                $idespecial=$fila[0];
                                $empresa = trim($empresa);//Evitar guardar espacios en el input
                                if(!(mysqli_query($con,"Update especial SET fecha_inicio='$f_ini', fecha_fin='$f_fin',empresa='$empresa', duracion=$totDias where (idespecial=$idespecial)")))
                                {
                                    mysqli_rollback($con);
                                    mysqli_autocommit($con, TRUE); 
                                    echo "<script type=\"text/javascript\">alert('Datos incorrectos de la comisión'); history.back();</script>";

                                }
                                else
                                {
                                    mysqli_commit($con);        
                                    // OBTENER LA DESCRIPCION DEL TIPO DE EMPLEADO PARA VERLO EN LA BITACORA
                                    $sql="SELECT descripcion  FROM trabajador inner join tipo on idtipo=tipo_tipo and tipo_tipo=$tipo_anterior;";
                                    $query= mysqli_query($con, $sql) or die();
                                    $fila=mysqli_fetch_array($query);
                                    $descripcion_tipo=$fila[0];
                                    
                                    $nombre_host= gethostname();
                                    //GUARDAR EN LA BITACORA DE TRABAJADOR
                                    if(!(mysqli_query($con,"call inserta_bitacora_trabajador('Actualizado','-','$nombre','$a_pat','$a_mat','$depto','$cat','$descripcion_tipo','$numero','$nombre_anterior','$a_paterno_anterior','$a_materno_anterior','$depto_anterior','$categoria_anterior','$descripcion_tipo', '$nombre_host')")))
                                    {
                                        mysqli_rollback($con);
                                        mysqli_autocommit($con, TRUE); 
                                        echo "<script type=\"text/javascript\">alert('Datos incorrectos en bitacora trabajador'); history.back();</script>";
                                    }
                                    else
                                    {                                       
                                        //GUARDAR EN LA BITACORA DE CUMPLE_ONO
                                        if(!(mysqli_query($con,"call inserta_bitacora_cumple_ono('Actualizado','$cumple','$ono','$cumple_anterior','$ono_anterior','$numero', '$nombre_host')")))
                                        {
                                            mysqli_rollback($con);
                                            mysqli_autocommit($con, TRUE); 
                                            echo "<script type=\"text/javascript\">alert('Datos incorrectos en bitacora cumple u onomástico); history.back();</script>";
                                        }
                                        else
                                        {  
                                            //GUARDAR EN LA BITACORA DE ACCESO
                                            if(!(mysqli_query($con,"call inserta_bitacora_acceso('Actualizado','$semana[0]','$semana[1]','$semana[2]','$semana[3]','$semana[4]','$semana[5]',$semana[6],$semana[7],'$turno','$lunes_anterior','$martes_anterior','$miercoles_anterior','$jueves_anterior','$viernes_anterior','$sabado_anterior','$domingo_anterior','$dias_festivos_anterior','$turno','$numero','$nombre_host')")))
                                            {
                                                mysqli_rollback($con);
                                                mysqli_autocommit($con, TRUE); 
                                                echo "<script type=\"text/javascript\">alert('Datos incorrectos en bitacora acceso); history.back();</script>";
                                            }
                                            else
                                            {
                                            
                                                //GUARDAR EN LA BITACORA DE TIEMPO SERVICIO
                                                if(!(mysqli_query($con,"call inserta_bitacora_tiempo_servicio('Actualizado','$fecha_alta','$fecha_alta_anterior','$numero', '$nombre_host')")))
                                                {
                                                    mysqli_rollback($con);
                                                    mysqli_autocommit($con, TRUE); 
                                                    echo "<script type=\"text/javascript\">alert('Datos incorrectos en bitacora tiempo de servicio); history.back();</script>";
                                                }
                                                else
                                                {
                                                    //GUARDAR EN LA BITACORA DE ESPECIAL
                                                    if(!(mysqli_query($con,"call inserta_bitacora_especial('Guardado','$f_ini','$f_fin','-','-','17','$empresa',$totDias,'$f_ini_anterior','$f_fin_anterior','','','$clave_especial_anterior','$empresa_anterior','$duracion_anterior','$numero','$nombre_host')")))
                                                    {
                                                        mysqli_rollback($con);
                                                        mysqli_autocommit($con, TRUE); 
                                                        echo "<script type=\"text/javascript\">alert('Datos incorrectos en bitacora especial); history.back();</script>";  
                                                    }
                                                    else
                                                    {   mysqli_commit($con);
                                                        mysqli_autocommit($con, TRUE);
                                                        echo "<script type=\"text/javascript\">alert(\"Empleado comisionado guardado correctamente\"); location.href='../ht/trabajadores.php';</script>";
                                                    
                                                    }// else especial
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
            else //El tipo de empleado es diferente de comisionado foráneo y tiene el mismo número de empleado
            {
                //////////////////////////
                // OBTENER TODOS LOS DATOS DEL TRABAJADOR
                $sql="SELECT *  FROM trabajador WHERE numero_trabajador=$numero;";
                $query= mysqli_query($con, $sql) or die();
                $fila=mysqli_fetch_array($query);
                $nombre_anterior=$fila[1];
                $a_paterno_anterior=$fila[2];
                $a_materno_anterior=$fila[3];
                $depto_anterior=$fila[4];
                $categoria_anterior=$fila[5];
                $tipo_anterior=$fila[6];
                // OBTENER TODOS LOS DATOS DE CUMPLE_ONO
                $sql="SELECT *  FROM cumple_ono WHERE trabajador_trabajador=$numero;";
                $query= mysqli_query($con, $sql) or die();
                $fila=mysqli_fetch_array($query);
                $cumple_anterior=$fila[1];
                $ono_anterior=$fila[2];
                // OBTENER TODOS LOS DATOS DE ACCESO
                $sql="SELECT *  FROM acceso WHERE trabajador_trabajador=$numero;";
                $query= mysqli_query($con, $sql) or die();
                $fila=mysqli_fetch_array($query);
                $lunes_anterior=$fila[1];
                $martes_anterior=$fila[2];
                $miercoles_anterior=$fila[3];
                $jueves_anterior=$fila[4];
                $viernes_anterior=$fila[5];
                $sabado_anterior=$fila[6];
                $domingo_anterior=$fila[7];
                $dias_festivos_anterior=$fila[8];
                $turno_anterior=$fila[9];
                // OBTENER TODOS LOS DATOS DEL TIEMPO DE SERVICIO
                $sql="SELECT *  FROM tiempo_servicio WHERE trabajador_trabajador=$numero;";
                $query= mysqli_query($con, $sql) or die();
                $fila=mysqli_fetch_array($query);
                $fecha_alta_anterior=$fila[1];
                //////////////////////////
                
                mysqli_autocommit($con, FALSE);
                //UPDATE `checada6`.`trabajador` SET `numero_trabajador` = '101010', `nombre` = 'DANIAA', `apellido_paterno` = 'MARTINEZA', `apellido_materno` = 'FLORESA', `depto_depto` = '043202', `categoria_categoria` = 'HE00702', `tipo_tipo` = '2' WHERE (`numero_trabajador` = '1010');
                if(!(mysqli_query($con,"Update trabajador SET nombre='$nombre',apellido_paterno='$a_pat',apellido_materno='$a_mat',depto_depto='$depto',categoria_categoria='$cat',tipo_tipo=$tipo WHERE (numero_trabajador='$numero')")))
                {
                    mysqli_rollback($con);
                    mysqli_autocommit($con, TRUE); 
                    echo "<script type=\"text/javascript\">alert('Datos incorrectos del trabajador'); history.back();</script>";
                }
                else
                {   
                    //UPDATE `checada6`.`acceso` SET `lunes` = '0', `martes` = '1', `miercoles` = '1', `jueves` = '1', `viernes` = '0', `sabado` = '0', `domingo` = '0', `dia_festivo` = '1', `turno_turno` = 'T2' WHERE (`idacceso` = '1');
                    // if(!(mysqli_query($con,"Update acceso SET lunes=$semana[0],martes=$semana[1],miercoles=$semana[2],jueves=$semana[3],viernes=$semana[4],sabado=$semana[5],domingo=$semana[6],dia_festivo=$semana[7],turno_turno='$turno' WHERE trabajador_trabajador='$numero'")))
                    if(!(mysqli_query($con,"Update acceso SET lunes=0,martes=0,miercoles=1,jueves=1,viernes=1,sabado=1,domingo=1,dia_festivo=1,turno_turno='$turno' WHERE trabajador_trabajador='$numero'")))
                    {
                        mysqli_rollback($con);
                        mysqli_autocommit($con, TRUE); 
                        echo "<script type=\"text/javascript\">alert('Datos incorrectos de los días de trabajo o el turno 3 $turno'); history.back();</script>";    
                    }
                    else
                    { //UPDATE `checada6`.`cumple_ono` SET `fecha_cumple` = '2002-02-22', `fecha_ono` = '2002-02-21' WHERE (`idcumple_ono` = '40');
                        //Obtener el id de cumple_ono del trabajador que se actualizará
                        $sql="SELECT idcumple_ono  FROM cumple_ono where trabajador_trabajador='$numero'";
                        $query= mysqli_query($con, $sql) or die();
                        $fila=mysqli_fetch_array($query);
                        $idcumple_ono=$fila[0];
                        if(!(mysqli_query($con,"Update cumple_ono SET fecha_cumple='$cumple',fecha_ono='$ono' where (idcumple_ono=$idcumple_ono)")))
                        {
                            mysqli_rollback($con);
                            mysqli_autocommit($con, TRUE); 
                            echo "<script type=\"text/javascript\">alert('Datos incorrectos del cumpleaños u onomástico'); history.back();</script>";            
                        }
                        else
                        {
                            //Actualizar la fecha de alta del trabajador
                            //UPDATE `checada6`.`tiempo_servicio` SET `fecha_alta` = '2020-04-02' WHERE (`idtiempo_servicio` = '4');
                            $sql="SELECT idtiempo_servicio  FROM tiempo_servicio where trabajador_trabajador='$numero'";
                            $query= mysqli_query($con, $sql) or die();
                            $fila=mysqli_fetch_array($query);
                            $idtiempo_servicio=$fila[0];
                            if(!(mysqli_query($con,"Update tiempo_servicio SET fecha_alta='$fecha_alta' where (idtiempo_servicio=$idtiempo_servicio)")))
                            {
                                mysqli_rollback($con);
                                mysqli_autocommit($con, TRUE); 
                                echo "<script type=\"text/javascript\">alert('Datos incorrectos del tiempo de servicio'); history.back();</script>";
                            }
                            else
                            {  
                                mysqli_commit($con);
                                // OBTENER LA DESCRIPCION DEL TIPO DE EMPLEADO PARA VERLO EN LA BITACORA
                                $sql="SELECT descripcion  FROM trabajador inner join tipo on idtipo=tipo_tipo and tipo_tipo=$tipo_anterior;";
                                $query= mysqli_query($con, $sql) or die();
                                $fila=mysqli_fetch_array($query);
                                $descripcion_tipo=$fila[0];
                                
                                $nombre_host= gethostname();
                                //GUARDAR EN LA BITACORA DE TRABAJADOR
                                if(!(mysqli_query($con,"call inserta_bitacora_trabajador('Actualizado','-','$nombre','$a_pat','$a_mat','$depto','$cat','$descripcion_tipo','$numero','$nombre_anterior','$a_paterno_anterior','$a_materno_anterior','$depto_anterior','$categoria_anterior','$descripcion_tipo', '$nombre_host')")))
                                {
                                    mysqli_rollback($con);
                                    mysqli_autocommit($con, TRUE); 
                                    echo "<script type=\"text/javascript\">alert('Datos incorrectos en bitacora trabajador'); history.back();</script>";
                                }
                                else
                                {
                                    //GUARDAR EN LA BITACORA DE CUMPLE_ONO
                                    if(!(mysqli_query($con,"call inserta_bitacora_cumple_ono('Actualizado','$cumple','$ono','$cumple_anterior','$ono_anterior','$numero', '$nombre_host')")))
                                    {
                                        mysqli_rollback($con);
                                        mysqli_autocommit($con, TRUE); 
                                        echo "<script type=\"text/javascript\">alert('Datos incorrectos en bitacora cumple u onomástico); history.back();</script>";
                                    }
                                else
                                    {  
                                        //GUARDAR EN LA BITACORA DE ACCESO
                                        if(!(mysqli_query($con,"call inserta_bitacora_acceso('Actualizado','$semana[0]','$semana[1]','$semana[2]','$semana[3]','$semana[4]','$semana[5]',$semana[6],$semana[7],'$turno','$lunes_anterior','$martes_anterior','$miercoles_anterior','$jueves_anterior','$viernes_anterior','$sabado_anterior','$domingo_anterior','$dias_festivos_anterior','$turno','$numero','$nombre_host')")))
                                        {
                                            mysqli_rollback($con);
                                            mysqli_autocommit($con, TRUE); 
                                            echo "<script type=\"text/javascript\">alert('Datos incorrectos en bitacora acceso); history.back();</script>";
                                        }
                                        else
                                        {
                                            //GUARDAR EN LA BITACORA DE TIEMPO SERVICIO
                                            if(!(mysqli_query($con,"call inserta_bitacora_tiempo_servicio('Actualizado','$fecha_alta','$fecha_alta_anterior','$numero', '$nombre_host')")))
                                            {
                                                mysqli_rollback($con);
                                                mysqli_autocommit($con, TRUE); 
                                                echo "<script type=\"text/javascript\">alert('Datos incorrectos en bitacora tiempo de servicio); history.back();</script>";
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
            }//fin de else
        }//fin de if (empty(salida))
        else
        {
            echo "<script> error('$salida'); history.back();</script>";
        }
    }
/////////////SI EL NÚMERO DE EMPLEADO SE VA A  ACTUALIZAR ////////////////////////
    else
    {
        //Aqui consulto si existe ese numero de trabajador 
        $ejecu="select * from trabajador where numero_trabajador = '$numero'";
        $codigo=mysqli_query($con,$ejecu);
        $consultar=mysqli_num_rows($codigo);
        //si el trabajador existe avisame que ya existe
        if($consultar>0)
        {
            $salida.="El número de trabajador ya existe, debe registrar otro número ";
            echo "<script> error('$salida'); history.back();</script>";
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
        
            //Si es foráneo
            if($tipo==4)
            {
                $empresa=$_POST['emp'];
                $f_ini=$_POST['f_ini'];
                $f_fin=$_POST['f_fin'];
                $empresa_validar=trim($empresa);
                if(empty($empresa_validar))
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
                //si el periodo de comisión es superior a 165 días (5 meses y medio)
                if($totDias>165)
                {
                    $salida.="No se puede comisionar más de 5 meses y medio.";
                }

                $fecha_hoy=date("Y-m-d");//la fecha de hoy
                $fecha_ac = strtotime($fecha_hoy);
                $fecha_in = strtotime($f_ini);
                //Si la fecha actual es mayor que la fecha inicial de la comisión entonces
                if($fecha_ac > $fecha_in)
                {
                    $salida.="La fecha de inicio de la comisión ya pasó, no es posible registrar una comisión que inició antes de hoy .";
                }
                if($fecha_ac==$fecha_in)
                {
                    $salida.="La comisión empieza hoy y no puede registrarse debido a que se requiere mínimo un día de anticipación.";
                }
            }//fin if-si es comisionado foráneo
            
            //Si salida está vacia 
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

                //Si el tipo de empleado es comisionado foráneo y el número de empleado es otro, realizar lo siguiente
                if($tipo==4)
                {  
                    mysqli_autocommit($con, FALSE);
                    //UPDATE `checada6`.`trabajador` SET `numero_trabajador` = '101010', `nombre` = 'DANIAA', `apellido_paterno` = 'MARTINEZA', `apellido_materno` = 'FLORESA', `depto_depto` = '043202', `categoria_categoria` = 'HE00702', `tipo_tipo` = '2' WHERE (`numero_trabajador` = '1010');
                    if(!(mysqli_query($con,"Update trabajador SET numero_trabajador='$numero', nombre='$nombre',apellido_paterno='$a_pat',apellido_materno='$a_mat',depto_depto='$depto',categoria_categoria='$cat',tipo_tipo=$tipo WHERE (numero_trabajador='$anterior_num')")))
                    {
                        mysqli_rollback($con);
                        mysqli_autocommit($con, TRUE); 
                        echo "<script type=\"text/javascript\">alert('Datos incorrectos del trabajador'); history.back();</script>";
                    }
                    else
                    { 
                        //////////////////////////
                        // OBTENER TODOS LOS DATOS DEL TRABAJADOR
                        $sql="SELECT * FROM trabajador WHERE numero_trabajador=$anterior_num;";
                        $query= mysqli_query($con, $sql) or die();
                        $fila=mysqli_fetch_array($query);
                        $nombre_anterior=$fila[1];
                        $a_paterno_anterior=$fila[2];
                        $a_materno_anterior=$fila[3];
                        $depto_anterior=$fila[4];
                        $categoria_anterior=$fila[5];
                        $tipo_anterior=$fila[6];
                        // OBTENER TODOS LOS DATOS DE CUMPLE_ONO
                        $sql="SELECT *  FROM cumple_ono WHERE trabajador_trabajador=$anterior_num;";
                        $query= mysqli_query($con, $sql) or die();
                        $fila=mysqli_fetch_array($query);
                        $cumple_anterior=$fila[1];
                        $ono_anterior=$fila[2];
                        // OBTENER TODOS LOS DATOS DE ACCESO
                        $sql="SELECT *  FROM acceso WHERE trabajador_trabajador=$anterior_num;";
                        $query= mysqli_query($con, $sql) or die();
                        $fila=mysqli_fetch_array($query);
                        $lunes_anterior=$fila[1];
                        $martes_anterior=$fila[2];
                        $miercoles_anterior=$fila[3];
                        $jueves_anterior=$fila[4];
                        $viernes_anterior=$fila[5];
                        $sabado_anterior=$fila[6];
                        $domingo_anterior=$fila[7];
                        $dias_festivos_anterior=$fila[8];
                        $turno_anterior=$fila[9];
                        // OBTENER TODOS LOS DATOS DEL TIEMPO DE SERVICIO
                        $sql="SELECT *  FROM tiempo_servicio WHERE trabajador_trabajador=$anterior_num;";
                        $query= mysqli_query($con, $sql) or die();
                        $fila=mysqli_fetch_array($query);
                        $fecha_alta_anterior=$fila[1];
                        // OBTENER TODOS LOS DATOS DE ESPECIAL
                        $sql="SELECT *  FROM especial WHERE trabajador_trabajador=$anterior_num;";
                        $query= mysqli_query($con, $sql) or die();
                        $fila=mysqli_fetch_array($query);
                        $f_ini_anterior=$fila[1];
                        $f_fin_anterior=$fila[2];
                        $clave_especial_anterior=$fila[7];
                        $empresa_anterior=$fila[8];
                        $duracion_anterior=$fila[9];        
                        //////////////////////////  
                        mysqli_commit($con);
                        //UPDATE `checada6`.`acceso` SET `lunes` = '0', `martes` = '1', `miercoles` = '1', `jueves` = '1', `viernes` = '0', `sabado` = '0', `domingo` = '0', `dia_festivo` = '1', `turno_turno` = 'T2' WHERE (`idacceso` = '1');
                        if(!(mysqli_query($con,"Update acceso SET lunes=$semana[0],martes=$semana[1],miercoles=$semana[2],jueves=$semana[3],viernes='$semana[4]',sabado=$semana[5],domingo=$semana[6],dia_festivo=$semana[7],turno_turno='$turno' WHERE trabajador_trabajador='$numero'")))
                        {
                            mysqli_rollback($con);
                            mysqli_autocommit($con, TRUE); 
                            echo "<script type=\"text/javascript\">alert('Datos incorrectos de los días de trabajo o el turno 4'); history.back();</script>";    
                        }
                        else
                        { //UPDATE `checada6`.`cumple_ono` SET `fecha_cumple` = '2002-02-22', `fecha_ono` = '2002-02-21' WHERE (`idcumple_ono` = '40');
                            //Obtener el id de cumple_ono del trabajador que se actualizará
                            $sql="SELECT idcumple_ono  FROM cumple_ono where trabajador_trabajador='$numero'";
                            $query= mysqli_query($con, $sql) or die();
                            $fila=mysqli_fetch_array($query);
                            $idcumple_ono=$fila[0];
                            if(!(mysqli_query($con,"Update cumple_ono SET fecha_cumple='$cumple',fecha_ono='$ono' where (idcumple_ono=$idcumple_ono)")))
                            {
                                mysqli_rollback($con);
                                mysqli_autocommit($con, TRUE); 
                                echo "<script type=\"text/javascript\">alert('Datos incorrectos del cumpleaños u onomástico'); history.back();</script>";            
                            }
                            else
                            {
                                //Actualizar la fecha de alta del trabajador
                                //UPDATE `checada6`.`tiempo_servicio` SET `fecha_alta` = '2020-04-02' WHERE (`idtiempo_servicio` = '4');
                                $sql="SELECT idtiempo_servicio  FROM tiempo_servicio where trabajador_trabajador='$numero'";
                                $query= mysqli_query($con, $sql) or die();
                                $fila=mysqli_fetch_array($query);
                                $idtiempo_servicio=$fila[0];
                                if(!(mysqli_query($con,"Update tiempo_servicio SET fecha_alta='$fecha_alta' where (idtiempo_servicio=$idtiempo_servicio)")))
                                {
                                    mysqli_rollback($con);
                                    mysqli_autocommit($con, TRUE); 
                                    echo "<script type=\"text/javascript\">alert('Datos incorrectos del tiempo de servicio'); history.back();</script>";
                                }
                                else
                                {   //UPDATE `checada6`.`especial` SET `fecha_inicio` = '2020-04-22', `fecha_fin` = '2020-04-26', `empresa` = 'HOSPITALL', `duracion` = '7' WHERE (`idespecial` = '25');
                                    $sql="SELECT idespecial  FROM especial where trabajador_trabajador='$numero'";
                                    $query= mysqli_query($con, $sql) or die();
                                    $fila=mysqli_fetch_array($query);
                                    $idespecial=$fila[0];
                                    $empresa = trim($empresa);//Evitar guardar espacios en el input
                                    if(!(mysqli_query($con,"Update especial SET fecha_inicio='$f_ini', fecha_fin='$f_fin',empresa='$empresa', duracion=$totDias where (idespecial=$idespecial)")))
                                    {
                                        mysqli_rollback($con);
                                        mysqli_autocommit($con, TRUE); 
                                        echo "<script type=\"text/javascript\">alert('Datos incorrectos de la comisión'); history.back();</script>";

                                    }
                                    else
                                    {
                                        mysqli_commit($con);
            
                                        // OBTENER LA DESCRIPCION DEL TIPO DE EMPLEADO PARA VERLO EN LA BITACORA
                                        $sql="SELECT descripcion  FROM trabajador inner join tipo on idtipo=tipo_tipo and tipo_tipo=$tipo_anterior;";
                                        $query= mysqli_query($con, $sql) or die();
                                        $fila=mysqli_fetch_array($query);
                                        $descripcion_tipo=$fila[0];
                                        
                                        $nombre_host= gethostname();
                                        //GUARDAR EN LA BITACORA DE TRABAJADOR
                                        if(!(mysqli_query($con,"call inserta_bitacora_trabajador('Actualizado',$numero,'$nombre','$a_pat','$a_mat','$depto','$cat','$descripcion_tipo','$anterior_num','$nombre_anterior','$a_paterno_anterior','$a_materno_anterior','$depto_anterior','$categoria_anterior','$descripcion_tipo', '$nombre_host')")))
                                        {
                                            mysqli_rollback($con);
                                            mysqli_autocommit($con, TRUE); 
                                            echo "<script type=\"text/javascript\">alert('Datos incorrectos en bitacora trabajador'); history.back();</script>";
                                        }
                                        else
                                        {
                                            //GUARDAR EN LA BITACORA DE CUMPLE_ONO
                                            if(!(mysqli_query($con,"call inserta_bitacora_cumple_ono('Actualizado','$cumple','$ono','$cumple_anterior','$ono_anterior','$numero', '$nombre_host')")))
                                            {
                                                mysqli_rollback($con);
                                                mysqli_autocommit($con, TRUE); 
                                                echo "<script type=\"text/javascript\">alert('Datos incorrectos en bitacora cumple u onomástico'); history.back();</script>";
                                            }
                                            else
                                            {  
                                                //GUARDAR EN LA BITACORA DE ACCESO
                                                if(!(mysqli_query($con,"call inserta_bitacora_acceso('Actualizado','$semana[0]','$semana[1]','$semana[2]','$semana[3]','$semana[4]','$semana[5]',$semana[6],$semana[7],'$turno','$lunes_anterior','$martes_anterior','$miercoles_anterior','$jueves_anterior','$viernes_anterior','$sabado_anterior','$domingo_anterior','$dias_festivos_anterior','$turno','$numero','$nombre_host')")))
                                                {
                                                    mysqli_rollback($con);
                                                    mysqli_autocommit($con, TRUE); 
                                                    echo "<script type=\"text/javascript\">alert('Datos incorrectos en bitacora acceso'); history.back();</script>";
                                                }
                                                else
                                                {                                                
                                                    //GUARDAR EN LA BITACORA DE TIEMPO SERVICIO
                                                    if(!(mysqli_query($con,"call inserta_bitacora_tiempo_servicio('Actualizado','$fecha_alta','$fecha_alta_anterior','$numero', '$nombre_host')")))
                                                    {
                                                        mysqli_rollback($con);
                                                        mysqli_autocommit($con, TRUE); 
                                                        echo "<script type=\"text/javascript\">alert('Datos incorrectos en bitacora tiempo de servicio'); history.back();</script>";
                                                    }
                                                    else
                                                    {            
                                                        //GUARDAR EN LA BITACORA DE ESPECIAL
                                                        if(!(mysqli_query($con,"call inserta_bitacora_especial('Guardado','$f_ini','$f_fin','-','-','17','$empresa',$totDias,'$f_ini_anterior','$f_fin_anterior','','','$clave_especial_anterior','$empresa_anterior','$duracion_anterior','$numero','$nombre_host',-1)")))
                                                        {
                                                            mysqli_rollback($con);
                                                            mysqli_autocommit($con, TRUE); 
                                                            echo "<script type=\"text/javascript\">alert('Datos incorrectos en bitacora especial'); history.back();</script>";  
                                                        }
                                                        else
                                                        {   mysqli_commit($con);
                                                            mysqli_autocommit($con, TRUE);
                                                            echo "<script type=\"text/javascript\">alert(\"Empleado comisionado actualizado correctamente\"); location.href='../../ht/trabajadores.php';</script>";
                                                        
                                                        }// else especial
                                                    }
                                                }   
                                            }
                                        }
                                    }
                                }
                            }
                        }     
                    }//fin de else
                }//fin-if comisionado foráneo
            else //El tipo de empleado es diferente de comisionado foráneo
                {
                    //////////////////////////
                    // OBTENER TODOS LOS DATOS DEL TRABAJADOR
                    $sql="SELECT * FROM trabajador WHERE numero_trabajador=$anterior_num;";
                    $query= mysqli_query($con, $sql) or die();
                    $fila=mysqli_fetch_array($query);
                    $nombre_anterior=$fila[1];
                    $a_paterno_anterior=$fila[2];
                    $a_materno_anterior=$fila[3];
                    $depto_anterior=$fila[4];
                    $categoria_anterior=$fila[5];
                    $tipo_anterior=$fila[6];
                    // OBTENER TODOS LOS DATOS DE CUMPLE_ONO
                    $sql="SELECT *  FROM cumple_ono WHERE trabajador_trabajador=$anterior_num;";
                    $query= mysqli_query($con, $sql) or die();
                    $fila=mysqli_fetch_array($query);
                    $cumple_anterior=$fila[1];
                    $ono_anterior=$fila[2];
                    // OBTENER TODOS LOS DATOS DE ACCESO
                    $sql="SELECT *  FROM acceso WHERE trabajador_trabajador=$anterior_num;";
                    $query= mysqli_query($con, $sql) or die();
                    $fila=mysqli_fetch_array($query);
                    $lunes_anterior=$fila[1];
                    $martes_anterior=$fila[2];
                    $miercoles_anterior=$fila[3];
                    $jueves_anterior=$fila[4];
                    $viernes_anterior=$fila[5];
                    $sabado_anterior=$fila[6];
                    $domingo_anterior=$fila[7];
                    $dias_festivos_anterior=$fila[8];
                    $turno_anterior=$fila[9];
                    // OBTENER TODOS LOS DATOS DEL TIEMPO DE SERVICIO
                    $sql="SELECT *  FROM tiempo_servicio WHERE trabajador_trabajador=$anterior_num;";
                    $query= mysqli_query($con, $sql) or die();
                    $fila=mysqli_fetch_array($query);
                    $fecha_alta_anterior=$fila[1];
                    ////////////////////////////////////////////////////////
                     mysqli_autocommit($con, FALSE);
                    //UPDATE `checada6`.`trabajador` SET `numero_trabajador` = '101010', `nombre` = 'DANIAA', `apellido_paterno` = 'MARTINEZA', `apellido_materno` = 'FLORESA', `depto_depto` = '043202', `categoria_categoria` = 'HE00702', `tipo_tipo` = '2' WHERE (`numero_trabajador` = '1010');
                    if(!(mysqli_query($con,"Update trabajador SET numero_trabajador=$numero, nombre='$nombre',apellido_paterno='$a_pat',apellido_materno='$a_mat',depto_depto='$depto',categoria_categoria='$cat',tipo_tipo=$tipo WHERE (numero_trabajador='$anterior_num')")))
                    {
                        mysqli_rollback($con);
                        mysqli_autocommit($con, TRUE); 
                        echo "<script type=\"text/javascript\">alert('Datos incorrectos del trabajador'); history.back();</script>";
                    }
                    else
                    {   
                        mysqli_commit($con);
                        //UPDATE `checada6`.`acceso` SET `lunes` = '0', `martes` = '1', `miercoles` = '1', `jueves` = '1', `viernes` = '0', `sabado` = '0', `domingo` = '0', `dia_festivo` = '1', `turno_turno` = 'T2' WHERE (`idacceso` = '1');
                        if(!(mysqli_query($con,"Update acceso SET lunes=$semana[0],martes=$semana[1],miercoles=$semana[2],jueves=$semana[3],viernes=$semana[4],sabado=$semana[5],domingo=$semana[6],dia_festivo=$semana[7],turno_turno='$turno' WHERE trabajador_trabajador='$numero'")))
                        {
                            mysqli_rollback($con);
                            mysqli_autocommit($con, TRUE); 
                            echo "<script type=\"text/javascript\">alert('Datos incorrectos de los días de trabajo o el turno 1'); history.back();</script>";    
                        }
                        else
                        { //UPDATE `checada6`.`cumple_ono` SET `fecha_cumple` = '2002-02-22', `fecha_ono` = '2002-02-21' WHERE (`idcumple_ono` = '40');
                            //Obtener el id de cumple_ono del trabajador que se actualizará
                            $sql="SELECT idcumple_ono  FROM cumple_ono where trabajador_trabajador='$numero'";
                            $query= mysqli_query($con, $sql) or die();
                            $fila=mysqli_fetch_array($query);
                            $idcumple_ono=$fila[0];
                            if(!(mysqli_query($con,"Update cumple_ono SET fecha_cumple='$cumple',fecha_ono='$ono' where (idcumple_ono=$idcumple_ono)")))
                            {
                                mysqli_rollback($con);
                                mysqli_autocommit($con, TRUE); 
                                echo "<script type=\"text/javascript\">alert('Datos incorrectos del cumpleaños u onomástico'); history.back();</script>";            
                            }
                            else
                            {
                                //Actualizar la fecha de alta del trabajador
                                //UPDATE `checada6`.`tiempo_servicio` SET `fecha_alta` = '2020-04-02' WHERE (`idtiempo_servicio` = '4');
                                $sql="SELECT idtiempo_servicio  FROM tiempo_servicio where trabajador_trabajador='$numero'";
                                $query= mysqli_query($con, $sql) or die();
                                $fila=mysqli_fetch_array($query);
                                $idtiempo_servicio=$fila[0];
                                if(!(mysqli_query($con,"Update tiempo_servicio SET fecha_alta='$fecha_alta' where (idtiempo_servicio=$idtiempo_servicio)")))
                                {
                                    mysqli_rollback($con);
                                    mysqli_autocommit($con, TRUE); 
                                    echo "<script type=\"text/javascript\">alert('Datos incorrectos del tiempo de servicio'); history.back();</script>";
                                }
                                else
                                {   
                                    mysqli_commit($con);
                                    // OBTENER LA DESCRIPCION DEL TIPO DE EMPLEADO PARA VERLO EN LA BITACORA
                                    $sql="SELECT descripcion  FROM trabajador inner join tipo on idtipo=tipo_tipo and tipo_tipo=$tipo_anterior;";
                                    $query= mysqli_query($con, $sql) or die();
                                    $fila=mysqli_fetch_array($query);
                                    $descripcion_tipo=$fila[0];

                                    $nombre_host= gethostname();
                                    //GUARDAR EN LA BITACORA DE TRABAJADOR
                                    if(!(mysqli_query($con,"call inserta_bitacora_trabajador('Actualizado','$numero','$nombre','$a_pat','$a_mat','$depto','$cat','$descripcion_tipo','$anterior_num','$nombre_anterior','$a_paterno_anterior','$a_materno_anterior','$depto_anterior','$categoria_anterior','$descripcion_tipo', '$nombre_host')")))
                                    {
                                        mysqli_rollback($con);
                                        mysqli_autocommit($con, TRUE); 
                                        echo "<script type=\"text/javascript\">alert('Datos incorrectos en bitacora trabajador'); history.back();</script>";
                                    }
                                    else
                                    {
                                        //GUARDAR EN LA BITACORA DE CUMPLE_ONO
                                        if(!(mysqli_query($con,"call inserta_bitacora_cumple_ono('Actualizado','$cumple','$ono','$cumple_anterior','$ono_anterior','$numero', '$nombre_host')")))
                                        {
                                            mysqli_rollback($con);
                                            mysqli_autocommit($con, TRUE); 
                                            echo "<script type=\"text/javascript\">alert('Datos incorrectos en bitacora cumple u onomástico'); history.back();</script>";
                                        }
                                       else
                                        {                                         
                                            //GUARDAR EN LA BITACORA DE ACCESO
                                            if(!(mysqli_query($con,"call inserta_bitacora_acceso('Actualizado','$semana[0]','$semana[1]','$semana[2]','$semana[3]','$semana[4]','$semana[5]',$semana[6],$semana[7],'$turno','$lunes_anterior','$martes_anterior','$miercoles_anterior','$jueves_anterior','$viernes_anterior','$sabado_anterior','$domingo_anterior','$dias_festivos_anterior','$turno','$numero','$nombre_host')")))
                                            {
                                                mysqli_rollback($con);
                                                mysqli_autocommit($con, TRUE); 
                                                echo "<script type=\"text/javascript\">alert('Datos incorrectos en bitacora acceso'); history.back();</script>";
                                            }
                                            else
                                            {                                            
                                                //GUARDAR EN LA BITACORA DE TIEMPO SERVICIO
                                                if(!(mysqli_query($con,"call inserta_bitacora_tiempo_servicio('Actualizado','$fecha_alta','$fecha_alta_anterior','$numero', '$nombre_host')")))
                                                {
                                                    mysqli_rollback($con);
                                                    mysqli_autocommit($con, TRUE); 
                                                    echo "<script type=\"text/javascript\"><script type=\"text/javascript\">alert('Datos incorrectos en bitacora tiempo de servicio'); history.back();</script>";
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
                }//fin de else
            }//fin de if (empty(salida))
            else
            {
            echo "<script> error('$salida'); history.back();</script>";
            }
        }
    }
?>

