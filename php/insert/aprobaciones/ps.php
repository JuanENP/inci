<?php
    if (!empty($_POST["num"]))
    {
        $num=$_POST["num"];
        $fecha=date("Y-m-d"); //la fecha actual
        $tipoEmpleado=tipoEmpleado($num);
        //Guardar con la clave PS, ojo :D
        $Clave="PS";
        if($tipoEmpleado=="BASE")
        {
            //ver que no tenga una PS en la quincena
            $sql="SELECT idpase_salida from pase_salida where trabajador_trabajador='$num' and year(fecha_uso)=$anio and quincena_quincena='$quincena'";
            $filas=obtenerFilas($sql);
            if($filas==0)
            {
                $sqlV="SELECT idvienen_hoy,salida FROM vienen_hoy WHERE trabajador_trabajador='$num' and observar_s=-1";
                $queryV=mysqli_query($con, $sqlV) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
                $filasV=mysqli_num_rows($queryV);
                if($filasV==1)
                {
                    $datos=mysqli_fetch_array($queryV);
                    $idViene=$datos[0];
                    $salida=$datos[1];
                    $salidaFinal=SumResMinutosHoras(2,$salida, 120);
                    $modificarSalida="UPDATE vienen_hoy SET salida='$salidaFinal' WHERE idvienen_hoy=$idViene";

                    //insertar el pase
                    $sql="INSERT INTO pase_salida (fecha_uso, trabajador_trabajador, quincena_quincena) VALUES ('$fecha', '$num', '$quincena')";
                    $ok= "<script> imprime('Pase de salida agregado correctamente.'); </script>";
                    $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                    $correcto=insertaEnBD($sql,$ok,$error,0);
                    //modificar su hora de salida en la tabla vienen_hoy
                    hazAlgoEnBDSinRetornarAlgo($modificarSalida);
                    //agregar a la bitacora pase de salida
                    insertaEnBitacoraPS($ok,"Guardado",$fecha,$num,$quincena,$correcto);
                }
                else
                {
                    echo "<script> imprime('Al empleado con número $num NO se le puede agregar un pase de salida porque la persona ya chechó su salida hoy o no debe venir hoy, verifique.'); </script>";
                }
            }
            else//fin filas==0
            {
                echo "<script> imprime('El empleado con número $num ya posee un pase de salida en esta quincena. Solo se permite un' +
                ' pase de salida por quincena. NO ES POSIBLE guardar este PS por el motivo anterior.'); </script>";
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
        echo "<script> imprime('$error'); </script>";
    }
?>