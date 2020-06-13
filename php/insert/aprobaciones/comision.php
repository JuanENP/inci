<?php
    //es interna (guardar AnombreEmpresa) o externa (DnombreEmpresa)
    //oficial menor a 1 dia (clave 61)
    //comisión equiv. a un día es la clave 17; 
    //la comision de mayor tiempo se maneja como comision sindical se le podría poner CS: audio 69 minuto 13, 
    /*numero
        fecha inicio
        fecha de fin
        validez
        falta CICA 61 Comisión oficial con o sin viáticos o que comprenda menos de un día.
    */
    if ((!empty($_POST["num"])) && (!empty($_POST["fec"])) && (!empty($_POST["emp"])) && (!empty($_POST["priority"])) && (!empty($_POST["tl"])))
    {
        $num = $_POST['num'];//el número del trabajador
        $fecha=$_POST['fec'];//la fecha de inicio
        $empresa=$_POST['emp'];
        $prioridad=$_POST['priority'];//la prioridad de la comisión
        $tipocomision=$_POST['tl'];
        $validez=0;
        if($prioridad=="a")
        {
            if((!empty($_FILES["archivo"]) && $_FILES["archivo"]["name"][0]))
            {
                //para que se evalue la imagen
                $laImagen=$_FILES["archivo"]["name"][0];
                $extension=$_FILES["archivo"]["type"][0];
                $origen=$_FILES["archivo"]["tmp_name"][0];
                $destino=$carpetaDestino.$laImagen;
            }
            else
            {
                echo "<script> imprime('Falta el documento escaneado de esta licencia de prioridad ALTA.' + 
                ' Debe subirlo para futuras aclaraciones posibles.'); </script>";
                exit();
            }
        }
        if($tipocomision=="csi" || $tipocomision=="cse")
        {
            if((!empty($_POST["he"])) && (!empty($_POST["hs"])) && (!empty($_POST["fecf"])))
            {
                $fechaf=$_POST['fecf'];//la fecha de fin
                $hora_e=$_POST['he'];
                $hora_s=$_POST['hs'];
                //24 H de anticipación, solo base
                $clave_especial="CS";
                if($tipocomision=="csi")
                {
                    $empresa="A".$empresa;
                }
                else
                {
                    if($tipocomision=="cse")
                    {
                        $empresa="D".$empresa;
                    }
                }
            }//fin if post vacios
            else
            {
                //Obligar al usuario a que meta todos los campos que se solicitan en aprobaciones.php
                $error="Faltan los siguientes datos:"."<br>";
                if (empty($_POST["he"])){$error.="La hora de entrada de la comisión."."<br>";}
                if (empty($_POST["hs"])){$error.="La hora de salida de la comisión."."<br>";} 
                if (empty($_POST["fecf"])){$error.="La fecha de fin de la comisión."."<br>";}
                echo "<script> imprime('$error'); </script>";
            }
        }//fin comision csi o cse
        else
        {
            if($tipocomision=="com1")
            {
                if((!empty($_POST["he"])) && (!empty($_POST["hs"])) && (!empty($_POST["fecf"])))
                {
                    $fechaf=$_POST['fecf'];//la fecha de fin
                    $hora_e=$_POST['he'];
                    $hora_s=$_POST['hs'];
                    //24 H de anticipación, solo base
                    //guardar la empresa normal
                    //Su horario de entrada y salida deben ser 00:00
                    $clave_especial="61";
                }  
                else
                {
                    //Obligar al usuario a que meta todos los campos que se solicitan en aprobaciones.php
                    $error="Faltan los siguientes datos:"."<br>";
                    if (empty($_POST["he"])){$error.="La hora de entrada de la comisión."."<br>";}
                    if (empty($_POST["hs"])){$error.="La hora de salida de la comisión."."<br>";} 
                    if (empty($_POST["fecf"])){$error.="La fecha de fin de la comisión."."<br>";}
                    echo "<script> imprime('$error'); </script>";
                } 
            }
            else
            {
                if($tipocomision=="co1")
                {
                    $fechaf=$fecha;//la fecha de fin
                    $hora_e="00:00:00";
                    $hora_s="00:00:00";
                    //guardar empresa normal
                    //todo tipo de personal
                    $clave_especial="17";
                }
            }
        }//fin else commision csi o cse
        
        /*Ver si ese empleado ya posee una comisión activa*/
        $sql7="SELECT idespecial from especial where trabajador_trabajador=$num and validez=1 and (clave_especial_clave_especial='CS' or clave_especial_clave_especial='61' or clave_especial_clave_especial='17')";
        $query7=mysqli_query($con, $sql7) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
        $resul7=mysqli_fetch_array($query7);
        $filasval1= mysqli_num_rows($query7);
        $sql7="SELECT b.idespecial from especial b where b.validez=0
        and (b.clave_especial_clave_especial='17' or b.clave_especial_clave_especial='61' or b.clave_especial_clave_especial='CS')
        and b.trabajador_trabajador = $num
        and  b.fecha_inicio>=now()";
        $query7=mysqli_query($con, $sql7) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
        $resul7=mysqli_fetch_array($query7);
        $filasval0= mysqli_num_rows($query7);
        /*Si el total de filas es 0 significa que el empleado no posee una comisión activa*/
        if(($filasval1==0 && $filasval0==0) || $prioridad=="a")
        {
            /*antes se debe verificar si se tuvo una comisión en los últimos 6 meses*/
            //obtener la fecha de hoy
            $hoy=date("Y-m-d"); 
            $fecha_ac = strtotime($hoy);
            $fecha_in = strtotime($fecha);//la fecha de inicio de la comisión
            if($fecha_ac < $fecha_in)
            {
                //La comisión aún no empieza, insertar la comisión
                $totDias=calcularDuracionEntreDosFechas(0,$fecha,$fechaf);//los días que durará la comisión
                //si el periodo de comisión es superior a 165 días (5 meses y medio) y la prioridad es normal
                if($totDias>165 && $prioridad=="n")
                {
                    mysqli_close($con);
                    echo "<script> imprime('El periodo entre las fechas $fecha y $fechaf es superior a 5 meses y medio. NO ES POSIBLE TENER UNA COMISIÓN QUE DURE ESE TIEMPO.'); </script>";
                }
                else
                {
                    //Insertar la comisión
                    if($clave_especial=="17"){$totDias=1;}
                    $sql8=" INSERT INTO especial VALUES (null, '$fecha', '$fechaf', '$hora_e', '$hora_s', '0', '$num', '$clave_especial','$empresa','$totDias')";
                    $ok= "<script> imprime('Comisión agregada correctamente'); </script>";
                    $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                    $correcto=insertaEnBD($sql8,$ok,$error,0);
                    //correcto obtiene el último ID que se insertó
                    if($prioridad=="a")
                    {
                        $SubeImagen=analizaYCargaImagen($origen,$destino,$laImagen,$correcto,$extension,$ok,1);
                        insertaEnBitacoraEspecial($ok,"Guardado",$fecha,$fechaf,"-","-",
                        $clave_especial,"$empresa","$totDias","-","-","-","-",
                        "-","-","-",$num,$correcto);
                    }
                    else
                    {
                        insertaEnBitacoraEspecial($ok,"Guardado",$fecha,$fechaf,"$hora_e","$hora_s",
                        $clave_especial,"$empresa","$totDias","-","-","-","-",
                        "-","-","-",$num,$correcto);
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
                    echo "<script> imprime('La fecha de inicio de la comisión ya pasó, NO ES POSIBLE REGISTRAR UNA COMISIÓN QUE YA INICIÓ'); </script>";
                }
            }
        }
        else
        {
            //El empleado ya posee una comisión activa y no puede tener 2 comisiones a la vez
            mysqli_close($con);
            echo "<script> imprime('El trabajador con número $num Ya posee una comisión activa. NO ES POSIBLE TENER 2 COMISIONES A LA VEZ'); </script>"; 
        }
    }//Fin del if valida POST
    else
    {
        //Obligar al usuario a que meta todos los campos que se solicitan en aprobaciones.php
        $error="Faltan los siguientes datos:"."<br>";
        if (empty($_POST["num"])){$error.="Número de trabajador que exista."."<br>";}
        if (empty($_POST["fec"])){$error.="La fecha de inicio de la comisión."."<br>";} 
        if (empty($_POST["emp"])){$error.="La empresa."."<br>";}
        if (empty($_POST["tl"])){$error.="El tipo de comisión."."<br>";}
        if (empty($_POST["priority"])){$error.="La proridad."."<br>";}
        echo "<script> imprime('$error'); </script>";
    }
?>