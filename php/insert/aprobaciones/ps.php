<?php
    if ((!empty($_POST["num"])) && (!empty($_POST["fec"])))
    {
        $num=$_POST["num"];
        $fecha=$_POST["fec"];
        $tipoEmpleado=tipoEmpleado($num);
        $validarfechas=RevisarFechas(2,$fecha,"","del pase de salida","un pase de salidas","",0); 
        //Guardar con la clave PS, ojo :D
        $Clave="PS";
        if($tipoEmpleado=="BASE")
        {
            //ver que no tenga una PS en la quincena
            $sql="SELECT idpase_salida from pase_salida where trabajador_trabajador='$num' and quincena_quincena='$quincena'";
            $filas=obtenerFilas($sql);
            if($filas==0)
            {
                //ingresar el pase
                $sql="INSERT INTO pase_salida (fecha_uso, trabajador_trabajador, quincena_quincena) VALUES ('$fecha', '$num', '$quincena')";
                $ok= "<script> imprime('Pase de salida agregado correctamente.'); </script>";
                $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                $correcto=insertaEnBD($sql,$ok,$error,0);
                //agregar a la bitacora pase de salida
                insertaEnBitacoraPS($ok,"Guardado",$fecha,$num,$quincena,$correcto);
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
        if (empty($_POST["fec"])){$error.="La fecha del pase de salida"."<br>";}
        echo "<script> imprime('$error'); </script>";
    }
?>