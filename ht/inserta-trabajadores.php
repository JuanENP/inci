<?php
session_start(); 
   // date_default_timezone_set('America/Mexico_City');
    if (($_SESSION["name"]) && ($_SESSION["con"]))
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        require("../Acceso/global.php"); 
        require('../php/buscar_info_trabajador.php');
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
    $nombre=$_POST['nom'];
    $a_pat=$_POST['a_pat'];
    $a_mat=$_POST['a_mat'];
    $cat=$_POST['cat'];
    $depto=$_POST['depto'];
    $tipo=$_POST['tipo'];
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
    $fecha_alta=$_POST['fecha_alta'];
    $valores2=explode('-',$fecha_alta);
    $resp=compararAnio($valores2[0]);
    if($resp==false)
    {
        $salida.="El año de alta del trabajador debe ser menor o igual al año actual. ";
    }

    $existeSexta=0;
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
            $salida.="El año de la fecha de alta es incorrecto.";
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

    //Aqui consulto si existe ese numero de trabajador 
    $ejecu="select * from trabajador where numero_trabajador = '$numero'";
    $codigo=mysqli_query($con,$ejecu);
    $consultar=mysqli_num_rows($codigo);
    //si el trabajador existe avisame que ya existe
    if($consultar>0)
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

        if(empty($salida))
        {
          //Si salida vacía, significa que no ocurrio ningun error 
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
            
            

            if($tipo==4)//Si el trabajador es comisionado foráneo
            { 
                mysqli_autocommit($con, FALSE);
                if(!(mysqli_query($con,"Insert into trabajador values ('$numero','$nombre','$a_pat','$a_mat','$depto','$cat',$tipo,'$genero')")))
                {
                    mysqli_rollback($con);
                    mysqli_autocommit($con, TRUE); 
                    $tabla='trabajador, línea 296';
                    echo "<script> errordato('$tabla');</script>";
                }
                else
                { 
                    if(!(mysqli_query($con,"Insert into acceso values ('','$semana[0]','$semana[1]','$semana[2]','$semana[3]','$semana[4]','$semana[5]',$semana[6],$semana[7],'$turno','$numero',-1)")))
                    {
                        mysqli_rollback($con);
                        mysqli_autocommit($con, TRUE); 
                        $tabla='acceso, línea 305';
                        echo "<script> errordato('$tabla');</script>";
                    }
                    else
                    {
                        //Nota: la validez de cumple_ono será siempre 1 porque siempre está valido el cumpleaños del empleado
                        if(!(mysqli_query($con,"Insert into cumple_ono values ('','$cumple','$ono',1,'$numero')")))
                        { 
                            mysqli_rollback($con);
                            mysqli_autocommit($con, TRUE); 
                            $tabla='cumpleaños u onomástico, línea 315';
                            echo "<script> errordato('$tabla');</script>";
                        }
                        else
                        {
                            if(!(mysqli_query($con,"Insert into tiempo_servicio values ('','$fecha_alta','$numero')")))
                            {
                                mysqli_rollback($con);
                                mysqli_autocommit($con, TRUE); 
                                $tabla='tiempo de servicio, línea 324';
                                echo "<script> errordato('$tabla');</script>";
                            }
                            else
                            {
                                if(!(mysqli_query($con,"Insert into especial values ('','$f_ini','$f_fin','','',0,'$numero','CS','$empresa',$totDias)")))
                                {
                                    mysqli_rollback($con);
                                    mysqli_autocommit($con, TRUE); 
                                    $tabla='especial, línea 334';
                                    echo "<script> errordato('$tabla');</script>";
                                }
                                else
                                {
                                   // mysqli_commit($con);
                                    // OBTENER LA DESCRIPCION DEL TIPO DE EMPLEADO PARA VERLO EN LA BITACORA
                                    $sql="SELECT descripcion  FROM trabajador inner join tipo on idtipo=tipo_tipo and tipo_tipo=$tipo";
                                    $query= mysqli_query($con, $sql) or die('Error al seleciconar la descriipcion del tipo de trabjador, línea 345, verifique con el administrador de sistemas.');
                                    $fila=mysqli_fetch_array($query);
                                    $descripcion_tipo=$fila[0];
                                    //Nombre del equipo
                                    $nombre_host= gethostname();
                                    //GUARDAR EN LA BITACORA DE TRABAJADOR
                                    if(!(mysqli_query($con,"call inserta_bitacora_trabajador('Guardado', '$numero', '$nombre', '$a_pat', '$a_mat', '$depto', '$cat', '$descripcion_tipo', '$genero', '-', '-', '-', '-', '-', '-', '-', '-', '$nombre_host')")))
                                    {
                                        mysqli_rollback($con);
                                        mysqli_autocommit($con, TRUE); 
                                        $tabla='bitácora de trabajador, línea 352';
                                        echo "<script> errordato('$tabla');</script>";
                                    }
                                    else
                                    {
                                        //GUARDAR EN LA BITACORA DE CUMPLE_ONO
                                        if(!(mysqli_query($con,"call inserta_bitacora_cumple_ono('Guardado','$cumple','$ono','-','-','$numero', '$nombre_host')")))
                                        {
                                            mysqli_rollback($con);
                                            mysqli_autocommit($con, TRUE); 
                                            $tabla='bitácora de cumpleaños u onomástico, línea 362';
                                            echo "<script> errordato('$tabla');</script>";
                                        }
                                        else
                                        {    //GUARDAR EN LA BITACORA DE ACCESO
                                            if(!(mysqli_query($con,"call inserta_bitacora_acceso('Guardado','$semana[0]','$semana[1]','$semana[2]','$semana[3]','$semana[4]','$semana[5]',$semana[6],$semana[7],'$turno','-','-','-','-','-','-','-','-','-','$numero','$nombre_host')")))
                                            {
                                                mysqli_rollback($con);
                                                mysqli_autocommit($con, TRUE); 
                                                $tabla='bitácora de acceso, línea 371';
                                                echo "<script> errordato('$tabla');</script>";
                                            }
                                            else
                                            {
                                                if($existeSexta==1)
                                                {
                                                    //GUARDAR LA SEXTA
                                                    if(!(mysqli_query($con,"Insert into sexta values ('',$semana2[0],$semana2[1],$semana2[2],$semana2[3],$semana2[4],$semana2[5],$semana2[6],$semana2[7],0,0,'$turno','$numero')")))
                                                    {
                                                        mysqli_rollback($con);
                                                        mysqli_autocommit($con, TRUE); 
                                                        $tabla='sexta, línea 383';
                                                        echo "<script> errordato('$tabla');</script>";
                                                    }
                                                    else
                                                    {
                                                        //GUARDAR EN LA BITACORA DE TIEMPO SERVICIO
                                                        if(!(mysqli_query($con,"call inserta_bitacora_tiempo_servicio('Guardado','$fecha_alta','-','$numero', '$nombre_host')")))
                                                        {
                                                            mysqli_rollback($con);
                                                            mysqli_autocommit($con, TRUE); 
                                                            $tabla='bitácora de tiempo de servicio, línea 393';
                                                            echo "<script> errordato('$tabla');</script>";
                                                        }
                                                        else
                                                        {
                                                            //GUARDAR EN LA BITACORA DE ESPECIAL
                                                            if(!(mysqli_query($con,"call inserta_bitacora_especial('Guardado','$f_ini','$f_fin','-','-','CS','$empresa',$totDias,'-','-','-','-','-','-','-','$numero','$nombre_host',-1)")))
                                                            {
                                                                mysqli_rollback($con);
                                                                mysqli_autocommit($con, TRUE); 
                                                                $tabla='bitácora de especial, línea 403';
                                                                echo "<script> errordato('$tabla');</script>"; 
                                                            }
                                                            else
                                                            {   //GUARDAR EN LA BITACORA SEXTA
                                                                if(!(mysqli_query($con,"call inserta_bitacora_sexta('Guardado','$semana2[0]','$semana2[1]','$semana2[2]','$semana2[3]','$semana2[4]','$semana2[5]','$semana2[6]','$semana2[7]','$turno','0','0','-', '-', '-', '-', '-', '-', '-', '-', '-','$numero','-','-','$nombre_host')")))
                                                                {
                                                                    mysqli_rollback($con);
                                                                    mysqli_autocommit($con, TRUE); 
                                                                    $tabla='bitácora de sexta, línea 412';
                                                                    echo "<script> errordato('$tabla');</script>";
                                                                }
                                                                else
                                                                {   $res=insertaUsuario($numero);
                                                                    if($res==true)
                                                                    {
                                                                        mysqli_commit($con);
                                                                        mysqli_autocommit($con, TRUE);
                                                                        $tipo='comisionado';
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
                                                        mysqli_rollback($con);
                                                        mysqli_autocommit($con, TRUE); 
                                                        $tabla='bitácora de tiempo de servicio, línea 436';
                                                        echo "<script> errordato('$tabla');</script>";
                                                    }
                                                    else
                                                    {
                                                        //GUARDAR EN LA BITACORA DE ESPECIAL
                                                        if(!(mysqli_query($con,"call inserta_bitacora_especial('Guardado','$f_ini','$f_fin','-','-','CS','$empresa',$totDias,'-','-','-','-','-','-','-','$numero','$nombre_host',-1)")))
                                                        {
                                                            mysqli_rollback($con);
                                                            mysqli_autocommit($con, TRUE); 
                                                            $tabla='bitácora de especial, línea 446';
                                                            echo "<script> errordato('$tabla');</script>"; 
                                                        }
                                                        else
                                                        {   $res=insertaUsuario($numero);
                                                            if($res==true)
                                                            {
                                                                mysqli_commit($con);
                                                                mysqli_autocommit($con, TRUE);
                                                                $tipo='comisionado';
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
            }//fin-if comisionado foráneo
            else
            {  //Si el empleado es de base, confianza o eventual, guardar los siguientes datos:
                mysqli_autocommit($con, FALSE);
                if(!(mysqli_query($con,"Insert into trabajador values ('$numero','$nombre','$a_pat','$a_mat','$depto','$cat',$tipo,'$genero')")))
                {
                    mysqli_rollback($con);
                    mysqli_autocommit($con, TRUE);
                    $tabla='trabajador, línea 477';
                    echo "<script> errordato('$tabla');</script>";
                }
                else
                { 
                    if(!(mysqli_query($con,"Insert into acceso values ('','$semana[0]','$semana[1]','$semana[2]','$semana[3]','$semana[4]','$semana[5]',$semana[6],$semana[7],'$turno','$numero',-1)")))
                    {
                        mysqli_rollback($con);
                        mysqli_autocommit($con, TRUE); 
                        $tabla='acceso, línea 486';
                        echo "<script> errordato('$tabla');</script>";
                    }
                    else
                    {
                        if(!(mysqli_query($con,"Insert into cumple_ono values ('','$cumple','$ono',1,'$numero')")))
                        {
                            mysqli_rollback($con);
                            mysqli_autocommit($con, TRUE); 
                            $tabla='cumpleaños u onomástico, línea 495';
                            echo "<script> errordato('$tabla');</script>";
                        }
                        else
                        {
                            if(!(mysqli_query($con,"Insert into tiempo_servicio values ('','$fecha_alta','$numero')")))
                            {
                                mysqli_rollback($con);
                                mysqli_autocommit($con, TRUE); 
                                $tabla='tiempo de servicio, línea 504';
                                echo "<script> errordato('$tabla');</script>";
                            }                            
                            else
                            {  
                               // mysqli_commit($con);
                                // OBTENER LA DESCRIPCION DEL TIPO DE EMPLEADO PARA VERLO EN LA BITACORA
                                $sql="SELECT descripcion  FROM trabajador inner join tipo on idtipo=tipo_tipo and tipo_tipo=$tipo";
                                $query= mysqli_query($con, $sql) or die('Error al obtener la descripción del tipo de trabajador, línea 508, verifique con el administrador de sistemas.');
                                $fila=mysqli_fetch_array($query);
                                $descripcion_tipo=$fila[0];
                                //Nombre del equipo
                                $nombre_host= gethostname();
                                //GUARDAR EN LA BITACORA DE TRABAJADOR
                                if(!(mysqli_query($con,"call inserta_bitacora_trabajador('Guardado', '$numero', '$nombre', '$a_pat', '$a_mat', '$depto', '$cat', '$descripcion_tipo', '$genero', '-', '-', '-', '-', '-', '-', '-', '-', '$nombre_host')")))
                                {
                                    mysqli_rollback($con);
                                    mysqli_autocommit($con, TRUE); 
                                    $tabla='bitácora de trabajador, línea 522';
                                    echo "<script> errordato('$tabla');</script>";
                                }
                                else
                                {
                                    //GUARDAR EN LA BITACORA DE CUMPLE_ONO
                                    if(!(mysqli_query($con,"call inserta_bitacora_cumple_ono('Guardado','$cumple','$ono','-','-','$numero', '$nombre_host')")))
                                    {
                                        mysqli_rollback($con);
                                        mysqli_autocommit($con, TRUE); 
                                        $tabla='bitácora cumple u onomástico, línea 532';
                                        echo "<script> errordato('$tabla');</script>";
                                    }
                                    else
                                    {    //GUARDAR EN LA BITACORA DE ACCESO
                                        if(!(mysqli_query($con,"call inserta_bitacora_acceso('Guardado','$semana[0]','$semana[1]','$semana[2]','$semana[3]','$semana[4]','$semana[5]',$semana[6],$semana[7],'$turno','-','-','-','-','-','-','-','-','-','$numero','$nombre_host')")))
                                        {
                                            mysqli_rollback($con);
                                            mysqli_autocommit($con, TRUE); 
                                            $tabla='bitácora de acceso, línea 541';
                                            echo "<script> errordato('$tabla');</script>";
                                        }
                                        else
                                        {
                                            //Si el empleado tiene sexta 
                                            if($existeSexta==1)
                                            {    //Guardar la sexta
                                                if(!(mysqli_query($con,"Insert into sexta values ('','$semana2[0]','$semana2[1]','$semana2[2]','$semana2[3]','$semana2[4]','$semana2[5]',$semana2[6],$semana2[7],0,0,'$turno','$numero')")))
                                                {
                                                    mysqli_rollback($con);
                                                    mysqli_autocommit($con, TRUE); 
                                                    $tabla='sexta, línea 553';
                                                    echo "<script> errordato('$tabla');</script>";
                                                }
                                                else
                                                {
                                                    //GUARDAR EN LA BITACORA DE TIEMPO SERVICIO
                                                    if(!(mysqli_query($con,"call inserta_bitacora_tiempo_servicio('Guardado','$fecha_alta','-','$numero', '$nombre_host')")))
                                                    {
                                                        mysqli_rollback($con);
                                                        mysqli_autocommit($con, TRUE); 
                                                        $tabla='bitácora de tiempo de servicio, línea 563';
                                                        echo "<script> errordato('$tabla');</script>";
                                                    }
                                                    else
                                                    {    //GUARDAR EN BITACORA SEXTA                                                              
                                                        if(!(mysqli_query($con,"call inserta_bitacora_sexta('Guardado','$semana2[0]','$semana2[1]','$semana2[2]','$semana2[3]','$semana2[4]','$semana2[5]','$semana2[6]','$semana2[7]','$turno','0','0','-', '-', '-', '-', '-', '-', '-', '-', '-','$numero','-','-','$nombre_host')")))
                                                        {
                                                            mysqli_rollback($con);
                                                            mysqli_autocommit($con, TRUE); 
                                                            $tabla='bitácora de sexta, línea 572';
                                                            echo "<script> errordato('$tabla');</script>";
                                                        }
                                                        else
                                                        {   $res=insertaUsuario($numero);
                                                            if($res==true)
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
                                                    mysqli_rollback($con);
                                                    mysqli_autocommit($con, TRUE); 
                                                    $tabla='bitácora de tiempo de servicio, línea 595';
                                                    echo "<script> errordato('$tabla');</script>";
                                                }
                                                else
                                                {   $res=insertaUsuario($numero);
                                                    if($res==true)
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
        }//fin de if (empty(salida))
        else
        {
          echo "<script> error($salida); </script>";
        }
    } 
?>

