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
    $nuevoNumeroEmpleado='';
    $sextaRegistrada='no'; //Sirve para validar que el trabajador tenga o no tenga una sexta registrada en la bd
    $existeSexta=0;//Verificar que el usuario haya seleccionado un turno con sexta

    //Validar si el número de empleado sigue siendo el mismo o se si se actualizará
    if($numero !== $anterior_num)
    {   
        $nuevoNumeroEmpleado='si';
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
    $t_dias=-1; //Servirá para guardar el total de días en acceso si el empleado no tiene sexta
    
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
            $salida.="El año de la fecha del onomástico es incorrecto. ";
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
        $salida.="El año de nacimiento es incorrecto. ";
    }
    if(strlen($valores2[0])>4)
    {
        $salida.="El año de la fecha de alta es incorrecto. ";
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
            $salida.="No ha escrito el nombre de la empresa. ";
        }

        if(empty($f_ini))
        {
            $salida.="No ha escrito la fecha de inicio de la comisión. ";
        }
        if(empty($f_fin))
        {
            $salida.="No ha escrito la fecha de fin de la comisión. ";
        }

        $valores_ini=explode('-',$f_ini);
        if(strlen($valores_ini[0])>4)
        {
            $salida.="El año de inicio es incorrecto. ";
        }

        $valores_fin=explode('-',$f_fin);
        if(strlen($valores_fin[0])>4)
        {
            $salida.="El año de fin es incorrecto. ";
        }

        $date1= new DateTime($f_ini);
        $date2= new DateTime($f_fin);
        $interval = $date1->diff($date2);
        $totDias=$interval->format('%a');//los días que durará la comisión
        //si el periodo de comisión es superior a 165 días (5 meses y medio)
        if($totDias>165)
        {
            $salida.="No se puede comisionar más de 5 meses y medio. ";
        }

        $fecha_hoy=date("Y-m-d");//la fecha de hoy
        $fecha_ac = strtotime($fecha_hoy);
        $fecha_in = strtotime($f_ini);
        $fecha_fin = strtotime($f_fin);
        if(($f_ini !== $f_ini_anterior))
        {
            //Si la fecha actual es mayor que la fecha inicial de la comisión entonces
            if($fecha_ac > $fecha_in)
            {
                $salida.="La fecha de inicio de la comisión ya pasó, no es posible registrar una comisión que inició antes de hoy. ";
            }
            if($fecha_ac==$fecha_in)
            {
                $salida.="La comisión empieza hoy y no puede registrarse debido a que se requiere mínimo un día de anticipación. ";
            }
        }
        if(($f_fin !== $f_fin_anterior))
        {
            //Si la fecha de fin es menor que la fecha inicial de la comisión entonces
            if($fecha_fin < $fecha_in)
            {
                $salida.="La fecha de fin de la comisión debe ser mayor o igual a la fecha de inicio, no es posible actualizar la fecha fin de la comisión. ";
            }
        }
    }//fin if-si es comisionado foráneo

    //-------------------Datos necesarios para guardar en las bitácoras-----------------------//
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
    $validezCumpleOno_anterior=$fila[3];

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

    $fila=describeTipoEmpleado($tipo_anterior);
    $descripcion_tipo_anterior=$fila;

    $fila=describeTipoEmpleado($tipo);
    $descripcion_tipo_nueva=$fila;

    $result=tieneSexta($numero);
    if($result !== false ) //Si el trabajador tiene una sexta registrada
    {  
        $sextaRegistrada='si'; 
        $idSexta= $result[0];
        $lunes_anteriorS = $result[1];
        $martes_anteriorS=$result[2];
        $miercoles_anteriorS=$result[3];
        $jueves_anteriorS=$result[4];
        $viernes_anteriorS=$result[5];
        $sabado_anteriorS=$result[6];
        $domingo_anteriorS=$result[7];
        $dias_festivos_anteriorS=$result[8];
        $validez_anteriorS=$result[9];
        $t_dias_anteriorS=$result[10];
        $turno_anteriorS=$result[11];
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

        //Si el trabajador es comisionado foráneo
        if($tipo==4)
        {
            mysqli_autocommit($con, FALSE);
            $actualizarTrabajador=actualizarTrabajador($anterior_num,$numero, $nombre, $a_pat, $a_mat, $depto, $cat, $tipo, $genero);
            $actualizarComisionEnEspecial=actualizarComisionEnEspecial($f_ini,$f_fin,$empresa,$totDias,$idespecial);
            //Si el trabajador seleccionó un turno con sexta
            if($existeSexta==1)
            {   //Ver si tiene una sexta registrada en la tabla sexta
                if($sextaRegistrada=='no') //si no tiene una sexta registrada será necesario guardar la sexta
                {
                    $InsertarSexta=Sexta('1',$semana2[0],$semana2[1],$semana2[2],$semana2[3],$semana2[4],$semana2[5],$semana2[6],$semana2[7],$turno,$numero,'');
                    $t_dias=0;// El total de días se guadará en acceso a los trabajadores que tengan sexta
                }
                else
                { //$sextaRegistrada=='si'
                    $t_dias=0;// El total de días se guadará en acceso a los trabajadores que tengan sexta
                    $ActualizarSexta=Sexta('2',$semana2[0],$semana2[1],$semana2[2],$semana2[3],$semana2[4],$semana2[5],$semana2[6],$semana2[7],$turno,$numero,'');
                }
            }
            else//Si el trabajador no tiene seleccionado un turno con sexta
            {
                //Pero ya estaba registrado en la tabla sexta y va a cambiar a otro turno que no tiene sexta, su sexta será eliminada

                if(($sextaRegistrada=='si') && ($t_horas_turno !=="06:00:00" || $t_horas_turno !=="06:30:00") && ($tipo==2 || $tipo==4))
                {
                    $EliminarSexta=Sexta('3','-','-','-','-','-','-','-','-','-','-',$idSexta);
                }                                        

            }
            
            $actualizarAcceso=actualizarAcceso($semana[0],$semana[1],$semana[2],$semana[3],$semana[4],$semana[5],$semana[6],$semana[7],$turno,$numero,$t_dias);
            $actualizarCumpleOno=actualizarCumpleOno($cumple,$ono,$validezCumpleOno,$idcumple_ono);
            $actualizarTiempoServicio=actualizarTiempoServicio($fecha_alta,$idtiempo_servicio);
            //Si el número de trabajador es actualizado
            if($nuevoNumeroEmpleado =='si')
            { 
                //Será necesario actualizar su cuenta de mysql.user y su mail
                $correcto=actualizarUserSiActualizaNumeroEmp($anterior_num);
                $res=insertaUsuario($numero);   
                $numeroActual = $numero;
            }
            else
            {
                $numeroActual='-';
            } 
               
            if($nombre == $nombre_anterior){$nombre='-';}    
            if($a_pat == $a_paterno_anterior){$a_pat='-';}  
            if($a_mat == $a_materno_anterior){$a_mat='-';}   
            if($depto == $depto_anterior){$depto='-';} 
            if($cat == $categoria_anterior){$cat='-';}   
            if($tipo == $tipo_anterior){$descripcion_tipo_nueva='-';} //en caso de ser diferentes en la bitacora se debe guardar $descripcion_tipo_anterior
            if($genero == $genero_anterior){$genero='-';}

            //Guardar en bitácoras
            $bitacoraTrabajador=bitacoraTrabajador($numeroActual, $nombre, $a_pat, $a_mat, $depto, $cat, $descripcion_tipo_nueva, $genero, $anterior_num, $nombre_anterior, $a_paterno_anterior, $a_materno_anterior, $depto_anterior, $categoria_anterior, $descripcion_tipo_anterior, $genero_anterior, $nombre_host);
            $bitacoraCumpleOno=bitacoraCumpleOno($cumple,$ono,$cumple_anterior,$ono_anterior,$numero,$nombre_host);
            $bitacoraAcceso=bitacoraAcceso($semana[0],$semana[1],$semana[2],$semana[3],$semana[4],$semana[5],$semana[6],$semana[7],$turno,$lunes_anterior,$martes_anterior,$miercoles_anterior,$jueves_anterior,$viernes_anterior,$sabado_anterior,$domingo_anterior,$dias_festivos_anterior,$turno_anterior,$numero,$nombre_host);
            $bitacoraTiempoServicio=bitacoraTiempoServicio($fecha_alta,$fecha_alta_anterior,$numero,$nombre_host);
            $bitacoraEspecial=bitacoraEspecial($f_ini, $f_fin, $empresa, $totDias, $f_ini_anterior, $f_fin_anterior, $clave_especial_anterior, $empresa_anterior, $duracion_anterior, $numero, $nombre_host);
           
            //Si trabajador seleccionó un turno con sexta
            if($existeSexta==1)
            {
                if($sextaRegistrada =='no') //pero el trabajador no tiene una sexta registrada, será necesario guardar la sexta tambien en la bitacora sexta
                {
                    $bitacoraSexta=bitacoraSexta('Guardado',$semana2[0],$semana2[1],$semana2[2],$semana2[3],$semana2[4],$semana2[5],$semana2[6],$semana2[7],$turno,'-','-','-','-','-','-','-','-','-',$numero,$nombre_host);
                   
                }
                else
                {
                    $bitacoraSexta=bitacoraSexta('Actualizado',$semana2[0],$semana2[1],$semana2[2],$semana2[3],$semana2[4],$semana2[5],$semana2[6],$semana2[7],$turno,$lunes_anteriorS,$martes_anteriorS,$miercoles_anteriorS,$jueves_anteriorS,$viernes_anteriorS,$sabado_anteriorS,$domingo_anteriorS,$dias_festivos_anteriorS,$turno_anteriorS,$numero,$nombre_host);
                }
            }
            else//Si el trabajador no tiene seleccionado un turno con sexta
            {
                //Pero si el trabajador ya estaba registrado en la tabla sexta y va a cambiar a otro turno que no tiene sexta, su sexta será eliminada
                if(($sextaRegistrada=='si') && ($t_horas_turno !=="06:00:00" || $t_horas_turno !=="06:30:00") && ($tipo==2 || $tipo==4))
                {
                    $bitacoraSexta=bitacoraSexta('Eliminado','-','-','-','-','-','-','-','-','-',$lunes_anteriorS,$martes_anteriorS,$miercoles_anteriorS,$jueves_anteriorS,$viernes_anteriorS,$sabado_anteriorS,$domingo_anteriorS,$dias_festivos_anteriorS,$turno_anteriorS,$numero,$nombre_host);
                }                                        
            }
            mysqli_commit($con);
            mysqli_autocommit($con, TRUE);
            echo "<script type=\"text/javascript\">alert(\"Empleado comisionado actualizado correctamente\"); location.href='../../ht/trabajadores.php';</script>";
        }
        else //Empleado Base, confianza o eventual 
        {
            mysqli_autocommit($con, FALSE);
            $actualizarTrabajador=actualizarTrabajador($anterior_num,$numero, $nombre, $a_pat, $a_mat, $depto, $cat, $tipo, $genero);
            //Si el trabajador seleccionó un turno con sexta
            if($existeSexta==1)
            {   //Ver si tiene una sexta registrada en la tabla sexta
                if($sextaRegistrada=='no') //si no tiene una sexta registrada será necesario guardar la sexta
                {
                    $InsertarSexta=Sexta('1',$semana2[0],$semana2[1],$semana2[2],$semana2[3],$semana2[4],$semana2[5],$semana2[6],$semana2[7],$turno,$numero,'');
                    $t_dias=0;// El total de días se guadará en acceso a los trabajadores que tengan sexta
                }
                else
                {   //$sextaRegistrada=='si'
                    $t_dias=0;// El total de días se guadará en acceso a los trabajadores que tengan sexta
                    $ActualizarSexta=Sexta('2',$semana2[0],$semana2[1],$semana2[2],$semana2[3],$semana2[4],$semana2[5],$semana2[6],$semana2[7],$turno,$numero,'');
                }
            }
            else//Si el trabajador no tiene seleccionado un turno con sexta
            {
                //Pero ya estaba registrado en la tabla sexta y va a cambiar a otro turno que no tiene sexta, su sexta será eliminada

                if(($sextaRegistrada=='si') && ($t_horas_turno !=="06:00:00" || $t_horas_turno !=="06:30:00") && ($tipo==2 || $tipo==4))
                {
                    $EliminarSexta=Sexta('3','-','-','-','-','-','-','-','-','-','-',$idSexta);
                }                                        

            }

            $actualizarAcceso=actualizarAcceso($semana[0],$semana[1],$semana[2],$semana[3],$semana[4],$semana[5],$semana[6],$semana[7],$turno,$numero,$t_dias);
            $actualizarCumpleOno=actualizarCumpleOno($cumple,$ono,$validezCumpleOno,$idcumple_ono);
            $actualizarTiempoServicio=actualizarTiempoServicio($fecha_alta,$idtiempo_servicio);
            //Si el número de trabajador es actualizado
            if($nuevoNumeroEmpleado =='si')
            { 
                //Será necesario actualizar su cuenta de mysql.user y su mail
                $correcto=actualizarUserSiActualizaNumeroEmp($anterior_num);
                $res=insertaUsuario($numero);   
                $numeroActual = $numero;
            }
            else
            {
                $numeroActual='-';
            } 
                
            if($nombre == $nombre_anterior){$nombre='-';}    
            if($a_pat == $a_paterno_anterior){$a_pat='-';}  
            if($a_mat == $a_materno_anterior){$a_mat='-';}   
            if($depto == $depto_anterior){$depto='-';} 
            if($cat == $categoria_anterior){$cat='-';}   
            if($tipo == $tipo_anterior){$descripcion_tipo_nueva='-';} //en caso de ser diferentes en la bitacora se debe guardar $descripcion_tipo_anterior
            if($genero == $genero_anterior){$genero='-';}

            //Guardar en bitácoras
            $bitacoraTrabajador=bitacoraTrabajador($numeroActual, $nombre, $a_pat, $a_mat, $depto, $cat, $descripcion_tipo_nueva, $genero, $anterior_num, $nombre_anterior, $a_paterno_anterior, $a_materno_anterior, $depto_anterior, $categoria_anterior, $descripcion_tipo_anterior, $genero_anterior, $nombre_host);
            $bitacoraCumpleOno=bitacoraCumpleOno($cumple,$ono,$cumple_anterior,$ono_anterior,$numero,$nombre_host);
            $bitacoraAcceso=bitacoraAcceso($semana[0],$semana[1],$semana[2],$semana[3],$semana[4],$semana[5],$semana[6],$semana[7],$turno,$lunes_anterior,$martes_anterior,$miercoles_anterior,$jueves_anterior,$viernes_anterior,$sabado_anterior,$domingo_anterior,$dias_festivos_anterior,$turno_anterior,$numero,$nombre_host);
            $bitacoraTiempoServicio=bitacoraTiempoServicio($fecha_alta,$fecha_alta_anterior,$numero,$nombre_host);
        
            //Si trabajador seleccionó un turno con sexta
            if($existeSexta==1)
            {
                if($sextaRegistrada =='no') //pero el trabajador no tiene una sexta registrada, será necesario guardar la sexta tambien en la bitacora sexta
                {
                    $bitacoraSexta=bitacoraSexta('Guardado',$semana2[0],$semana2[1],$semana2[2],$semana2[3],$semana2[4],$semana2[5],$semana2[6],$semana2[7],$turno,'-','-','-','-','-','-','-','-','-',$numero,$nombre_host);
                }
                else //en caso de que si tenga solo se actualizará la sexta
                {
                    $bitacoraSexta=bitacoraSexta('Actualizado',$semana2[0],$semana2[1],$semana2[2],$semana2[3],$semana2[4],$semana2[5],$semana2[6],$semana2[7],$turno,$lunes_anteriorS,$martes_anteriorS,$miercoles_anteriorS,$jueves_anteriorS,$viernes_anteriorS,$sabado_anteriorS,$domingo_anteriorS,$dias_festivos_anteriorS,$turno_anteriorS,$numero,$nombre_host);
                }
            }
            else//Si el trabajador no tiene seleccionado un turno con sexta
            {
                //Pero si el trabajador ya estaba registrado en la tabla sexta y va a cambiar a otro turno que no tiene sexta, su sexta será eliminada
                if(($sextaRegistrada=='si') && ($t_horas_turno !=="06:00:00" || $t_horas_turno !=="06:30:00") && ($tipo==2 || $tipo==4))
                {
                    $bitacoraSexta=bitacoraSexta('Eliminado','-','-','-','-','-','-','-','-','-',$lunes_anteriorS,$martes_anteriorS,$miercoles_anteriorS,$jueves_anteriorS,$viernes_anteriorS,$sabado_anteriorS,$domingo_anteriorS,$dias_festivos_anteriorS,$turno_anteriorS,$numero,$nombre_host);
                }                                        
            }
            mysqli_commit($con);
            mysqli_autocommit($con, TRUE);
            echo "<script type=\"text/javascript\">alert(\"Empleado actualizado correctamente\"); location.href='../../ht/trabajadores.php';</script>";
        }
    }//fin de if (empty(salida))
    else
    {
        echo "<script> error('$salida'); history.back();</script>";
    }

    function actualizarTrabajador($anterior_num,$numero, $nombre, $a_pat, $a_mat, $depto, $cat, $tipo, $genero)
    {
        global $con;
        if(!(mysqli_query($con,"Update trabajador SET numero_trabajador='$numero', nombre='$nombre',apellido_paterno='$a_pat', apellido_materno='$a_mat',depto_depto='$depto',categoria_categoria='$cat',tipo_tipo=$tipo, genero='$genero' WHERE numero_trabajador='$anterior_num'")))
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $línea='412';
            error($er1,$er2,$línea);
            mysqli_rollback($con);
            mysqli_autocommit($con, TRUE);            
        }
        else
        {
            return true;   
        } 
    }

    function actualizarAcceso($lun,$mar,$mie,$jue,$vie,$sab,$dom,$dia_fes,$turno,$numero,$t_dias)
    {
        global $con;
        if(!(mysqli_query($con,"Update acceso SET lunes=$lun,martes=$mar,miercoles=$mie,jueves=$jue,viernes=$vie,sabado=$sab,domingo=$dom,dia_festivo=$dia_fes,turno_turno='$turno',t_dias=$t_dias WHERE trabajador_trabajador='$numero'")))
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $línea='402';
            error($er1,$er2,$línea);
            mysqli_rollback($con);
            mysqli_autocommit($con, TRUE);
        }
        else
        {
            return true;   
        } 
    }

    function actualizarCumpleOno($cumple,$ono,$validezCumpleOno,$idcumple_ono)
    {
        global $con;
        if(!(mysqli_query($con,"Update cumple_ono SET fecha_cumple='$cumple',fecha_ono='$ono', validez=$validezCumpleOno where (idcumple_ono=$idcumple_ono)")))
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $línea='414';
            error($er1,$er2,$línea);
            mysqli_rollback($con);
            mysqli_autocommit($con, TRUE);            
        }
        else
        {
            return true;   
        } 
    }

    function actualizarTiempoServicio($fecha_alta,$idtiempo_servicio)
    {
        global $con;
        if(!(mysqli_query($con,"Update tiempo_servicio SET fecha_alta='$fecha_alta' where idtiempo_servicio=$idtiempo_servicio")))
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $línea='425';
            error($er1,$er2,$línea);
            mysqli_rollback($con);
            mysqli_autocommit($con, TRUE);
        }
        else
        {
            return true;   
        } 
    }

    function actualizarComisionEnEspecial($f_ini,$f_fin,$empresa,$totDias,$idespecial)
    {
        global $con;
        if(!(mysqli_query($con,"Update especial SET fecha_inicio='$f_ini', fecha_fin='$f_fin',empresa='$empresa', duracion=$totDias where idespecial=$idespecial;")))
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
            return true;   
        } 
    }

    function Sexta($opcion,$lun,$mar,$mie,$jue,$vie,$sab,$dom,$dia_fes,$turno,$numero,$idsexta)
    {
        global $con;
        if($opcion == 1)
        { 
            if(!(mysqli_query($con,"Insert into sexta values ('',$lun,$mar,$mie,$jue,$vie,$sab,$dom,$dia_fes,0,0,'$turno','$numero')")))
            {
                $er1=mysqli_errno($con);
                $er2=mysqli_error($con);
                $línea='513';
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
            if($opcion==2)
            {
                if(!(mysqli_query($con,"Update sexta SET lunes=$lun,martes=$mar,miercoles=$mie,jueves=$jue,viernes=$vie,sabado=$sab,domingo=$dom,dia_festivo=$dia_fes,validez=0,t_dias=0,turno_turno='$turno' WHERE trabajador_trabajador='$numero'")))
                {
                    $er1=mysqli_errno($con);
                    $er2=mysqli_error($con);
                    $línea='402';
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
                if($opcion==3)
                {
                    if(!(mysqli_query($con,"DELETE FROM sexta WHERE idsexta = $idsexta;")))
                    {
                        $er1=mysqli_errno($con);
                        $er2=mysqli_error($con);
                        $línea='608';
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
        }
    }

    function bitacoraTrabajador($numero, $nombre, $a_pat, $a_mat, $depto, $cat, $tipo, $genero, $numero_anterior, $nombre_anterior, $a_pat_anterior, $a_mat_anterior, $depto_anterior, $cat_anterior, $tipo_anterior, $genero_anterior, $nombre_host)
    {
      global $con;
      $sql="call inserta_bitacora_trabajador('Actualizado','$numero', '$nombre', '$a_pat', '$a_mat', '$depto', '$cat', '$tipo', '$genero', '$numero_anterior', '$nombre_anterior', '$a_pat_anterior', '$a_mat_anterior', '$depto_anterior', '$cat_anterior', '$tipo_anterior', '$genero_anterior', '$nombre_host');"; 
      mysqli_autocommit($con, FALSE);
      if(!(mysqli_query($con,$sql)))
      {
        $er1=mysqli_errno($con);
        $er2=mysqli_error($con);
        $línea='704';
        error($er1,$er2,$línea);
        mysqli_rollback($con);
        mysqli_autocommit($con, TRUE); 
      }
      else
      {
        return true;
      } 
    }

    function bitacoraCumpleOno($cumple,$ono,$cumple_anterior,$ono_anterior,$numero,$nombre_host)
    {
        global $con;
        if(!(mysqli_query($con,"call inserta_bitacora_cumple_ono('Actualizado','$cumple','$ono','$cumple_anterior','$ono_anterior','$numero', '$nombre_host')")))
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $línea='547';
            error($er1,$er2,$línea);
            mysqli_rollback($con);
            mysqli_autocommit($con, TRUE);
        }
        else
        {
            return true;   
        } 
    }

    function bitacoraAcceso($lun,$mar,$mie,$jue,$vie,$sab,$dom,$dia_fes,$turno,$lunes_anterior,$martes_anterior,$miercoles_anterior,$jueves_anterior,$viernes_anterior,$sabado_anterior,$domingo_anterior,$dias_festivos_anterior,$turno_anterior,$numero,$nombre_host)
    {
        global $con;
        //GUARDAR EN LA BITACORA DE ACCESO
        if(!(mysqli_query($con,"call inserta_bitacora_acceso('Actualizado','$lun','$mar','$mie','$jue','$vie','$sab','$dom',$dia_fes,'$turno','$lunes_anterior','$martes_anterior','$miercoles_anterior','$jueves_anterior','$viernes_anterior','$sabado_anterior','$domingo_anterior','$dias_festivos_anterior','$turno_anterior','$numero','$nombre_host')")))
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $línea='566';
            error($er1,$er2,$línea);
            mysqli_rollback($con);
            mysqli_autocommit($con, TRUE);
        }
        else
        {
            return true;   
        } 
    }

    function bitacoraTiempoServicio($fecha_alta,$fecha_alta_anterior,$numero,$nombre_host)
    {
        global $con;
        if(!(mysqli_query($con,"call inserta_bitacora_tiempo_servicio('Actualizado','$fecha_alta','$fecha_alta_anterior','$numero', '$nombre_host')")))
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $línea='583';
            error($er1,$er2,$línea);
            mysqli_rollback($con);
            mysqli_autocommit($con, TRUE);
        }
        else
        {
            return true;   
        } 
    }

    function bitacoraEspecial($f_ini, $f_fin, $empresa, $totDias, $f_ini_anterior, $f_fin_anterior, $clave_especial_anterior, $empresa_anterior, $duracion_anterior, $numero, $nombre_host)
    {
        global $con;
        if(!(mysqli_query($con,"call inserta_bitacora_especial('Actualizado', '$f_ini', '$f_fin', ' ', '', 'CS', '$empresa', '$totDias', '$f_ini_anterior', '$f_fin_anterior', '', '', '$clave_especial_anterior', '$empresa_anterior', '$duracion_anterior', '$numero', '$nombre_host', '1')")))
        { 
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $línea='496';
            error($er1,$er2,$línea);
            mysqli_rollback($con);
            mysqli_autocommit($con, TRUE);
        }
        else
        {
            return true;   
        } 
    }

    function bitacoraSexta($operacion,$lun,$mar,$mie,$jue,$vie,$sab,$dom,$dia_fes,$turno,$lunes_anterior,$martes_anterior,$miercoles_anterior,$jueves_anterior,$viernes_anterior,$sabado_anterior,$domingo_anterior,$dias_festivos_anterior,$turno_anterior,$numero,$nombre_host)
    {
        global $con;
        if(!(mysqli_query($con,"call inserta_bitacora_sexta('$operacion','$lun','$mar','$mie','$jue','$vie','$sab','$dom','$dia_fes','$turno','0','0','$lunes_anterior','$martes_anterior','$miercoles_anterior','$jueves_anterior','$viernes_anterior','$sabado_anterior','$domingo_anterior','$dias_festivos_anterior','$turno_anterior','$numero','0','0','$nombre_host');")))
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $línea='566';
            error($er1,$er2,$línea);
            mysqli_rollback($con);
            mysqli_autocommit($con, TRUE);
        }
        else
        {
            return true;   
        } 
    }

    function actualizarUserSiActualizaNumeroEmp($numero)
    {
        global $con;

        if(!(mysqli_query($con,"drop user '$numero'@'localhost';")))
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $línea='653';
            error($er1,$er2,$línea);
            mysqli_rollback($con);
            mysqli_autocommit($con, TRUE); 
        }
        else
        {
            $mail=consultaMail($numero);
            if($mail==true)
            {
                $idmail=$mail;
                
                if(!(mysqli_query($con,"DELETE FROM mail WHERE (idmail =  $idmail);")))
                {
                    $er1=mysqli_errno($con);
                    $er2=mysqli_error($con);
                    $línea='653';
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