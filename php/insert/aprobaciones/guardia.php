<?php
    /*
        Pedir:
        fecha actual: $fec_act
        fecha de guardia: $fec
        que los trabajadores no sean los mismos y que sean del mismo tipo de empleado y tengan el mismo departamento
        obtener la hora de entrada y de salida del trabajador solicitante
    */
    if ((!empty($_POST["num"])) && (!empty($_POST["numSup"])) && (!empty($_POST["fec"])))
    {
        $num=$_POST["num"];
        $suplente=$_POST["numSup"];
        $fechaGuardia=$_POST["fec"];
        //verificar que la fecha de guardia no sea hoy o un día anterior a hoy
        $validarfechas=RevisarFechas(2,$fechaGuardia,"","de la guardia","una guardia","",0);
        //ver que el solicitante no tenga ya registrada una guardia en esta quincena
        $sql="select idguardias from guardias where trabajador_solicitante='$num' and quincena='$quincena'";
        $filas=obtenerFilas($sql);
        if($filas==0)//no posee una guardia en esta quincena
        {
            //Ver que los trabajadores no sean los mismos
            if($num!=$suplente)
            {
                //Ver que sean de la misma categoría
                $sql="SELECT categoria_categoria from trabajador where numero_trabajador='$num'";
                $categoriaSolicitante=retornaAlgoDeBD(0, $sql);
                $sql="SELECT categoria_categoria from trabajador where numero_trabajador='$suplente'";
                $categoriaSuplente=retornaAlgoDeBD(0, $sql);
                if($categoriaSolicitante==$categoriaSuplente) //misma categoria
                {
                    //Obtener hora de entrada y salida del trabajador solicitante
                    $horario=obtenerHorario($num);
                    $horaE=$horario[0];
                    $horaS=$horario[1];
                    //insertar la guardia
                    $sql="INSERT INTO guardias (fecha_registro, fecha_guardia, trabajador_solicitante, trabajador_suplente, hora_entrada, hora_salida, quincena) 
                    VALUES ('$fec_act', '$fechaGuardia', $num, $suplente, '$horaE', '$horaS', '$quincena')";
                    $ok= "<script> imprime('Guardia agregada correctamente.'); </script>";
                    $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                    $correcto=insertaEnBD($sql,$ok,$error,0);
                    insertaEnBitacoraGuardia($ok,"Guardado",$correcto,$fec_act,$fechaGuardia,$num,
                    $suplente, $horaE,$horaS,$quincena,"-","-","-",
                    "-","-","-","-","-");
                    /*
                    insertaEnBitacoraGuardia($ok,$operacion,$id_new,$fechaRegistro_new,$fechaGuardia_new,$solicitante_new,
                    $suplente_new,$he_new,$hs_new,$quincena_new,$id_old,$fechaRegistro_old,$fechaGuardia_old,$solicitante_old,
                    $suplente_old,$he_old,$hs_old,$quincena_old);
                    */
                }
                else//fin comparar categoria
                {
                    echo "<script> imprime('Los empleados NO POSEEN LA MISMA CATEGORÍA. Debido a lo anterior no es' +
                    'posible guardar esta guardia.'); </script>";
                }
            }
            else//fin if num distinto de suplente
            {
                echo "<script> imprime('Los empleados son los mismos, POR FAVOR. Elija con cuidado.'); </script>";
            }
        }//fin if filas==0
        else
        {
            echo "<script> imprime('El empleado con número $num ya posee una guardia en esta quincena. Solo se permite una' +
            ' guardia por quincena. NO ES POSIBLE guardar esta guardia por el motivo anterior.'); </script>";
        }
    }
    else
    {
        $error="Faltan los siguientes datos:"."<br>";
        if (empty($_POST["num"])){$error.="Número de trabajador solicitante que exista."."<br>";}
        if (empty($_POST["numSup"])){$error.="Número de trabajador suplente que exista."."<br>";} 
        if (empty($_POST["fec"])){$error.="La fecha de la guardia"."<br>";}
        echo "<script> imprime('$error'); </script>";
    }
?>
