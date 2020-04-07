<?php
session_start(); 
date_default_timezone_set('America/Mexico_City');
if (($_SESSION["name"]) && ($_SESSION["con"]))
{
    $nombre=$_SESSION['name'];
    $contra=$_SESSION['con'];
    require("../Acceso/global.php"); 
}
else
{
    header("Location: ../index.html");
    die();
}
?>
 <script type="text/javascript">
  function error(cadena)
  {
    alert(cadena);
    history.back();
  }
</script>

<?php
    $numero=$_POST['num'];
    $nombre=$_POST['nom'];
    $a_pat=$_POST['a_pat'];
    $a_mat=$_POST['a_mat'];
    $cat=$_POST['cat'];
    $depto=$_POST['depto'];
    $tipo=$_POST['tipo'];
    $turno=$_POST['turno'];
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

    //Aqui consulto si existe ese numero de trabajador 
    $ejecu="select * from trabajador where numero_trabajador = '$numero'";
    $codigo=mysqli_query($con,$ejecu);
    $consultar=mysqli_num_rows($codigo);
    //si el trabajador existe avisame que ya existe
    if($consultar>0)
    {
        $salida.="El empleado ya existe";
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
            if($tipo==4)
            { 
                if(!(mysqli_query($con,"Insert into trabajador values ('$numero','$nombre','$a_pat','$a_mat','$depto','$cat',$tipo)")))
                {
                    //Ocurrió algún error
                    echo "<script type=\"text/javascript\">alert(\"Error\");</script>";
                    die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
                }
                else
                {
                    if(!(mysqli_query($con,"Insert into cumple_ono values ('','$cumple','',1,'$numero')")))
                    {
                        //Ocurrió algún error
                        echo "<script type=\"text/javascript\">alert(\"Error\");</script>";
                        die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
                    }
                    else
                    {
                        $empresa = trim($empresa);
                        if(!(mysqli_query($con,"Insert into especial values ('','$f_ini','$f_fin','','',0,'$numero','17','$empresa',$totDias)")))
                        {
                            //Ocurrió algún error
                            echo "<script type=\"text/javascript\">alert(\"Error\");</script>";
                            die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
                        }
                        else
                        {
                            echo "<script type=\"text/javascript\">alert(\"Empleado comisionado guardado correctamente\"); location.href='../ht/trabajadores.php';</script>";
                        }
                    }
                }
            }//fin-if comisionado foráneo
          else
            {
                //Cualquier otro distinto a comisionado foráneo
                if(!(mysqli_query($con,"Insert into trabajador values ('$numero','$nombre','$a_pat','$a_mat','$depto','$cat',$tipo)")))
                {
                    //Ocurrió algún error
                    echo "<script type=\"text/javascript\">alert(\"Error\");</script>";
                    die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
                }
                else
                {
                   if(!(mysqli_query($con,"Insert into acceso values ('','$semana[0]','$semana[1]','$semana[2]','$semana[3]','$semana[4]','$semana[5]',$semana[6],$semana[7],'$turno','$numero')")))
                    {
                        //Ocurrió algún error
                        echo "<script type=\"text/javascript\">alert(\"Error\");</script>";
                        die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
                    }
                    else
                    {
                        if(!(mysqli_query($con,"Insert into cumple_ono values ('','$cumple','',1,'$numero')")))
                        {
                            //Ocurrió algún error
                            echo "<script type=\"text/javascript\">alert(\"Error\");</script>";
                            die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
                        }
                        else
                        {
                            if(!(mysqli_query($con,"Insert into tiempo_servicio values ('','$fecha_alta','$numero')")))
                            {
                                //Ocurrió algún error
                                echo "<script type=\"text/javascript\">alert(\"Error\");</script>";
                                die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
                            }
                            else
                            {
                                    echo "<script type=\"text/javascript\">alert(\"Empleado guardado correctamente\"); location.href='../ht/trabajadores.php';</script>";
                            }
                        }
                    }
                }
            }

        }//fin de if (empty(salida))
        else
        {
          echo "<script> error('$salida'); history.back();</script>";
        }
    }
?>

