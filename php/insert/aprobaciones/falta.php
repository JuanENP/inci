<?php
    if((!empty($_POST["num"])) && (!empty($_POST["fec"])) && (!empty($_FILES["archivo"]) && $_FILES["archivo"]["name"][0]))
    {
        $num = $_POST['num'];
        $fecha=$_POST['fec'];
        //¿?Cuántas faltas puede justificar el empleado en una quincena
        //Validar las fechas
        $validarfechas=RevisarFechas(3,$fecha,"","de la falta","una falta","",1);
        //Ver si esa falta existe
        $sql="SELECT idfalta from falta where fecha='$fecha' and trabajador_trabajador='$num' 
        and quincena='$quincena'";
        $filas=obtenerFilas($sql);
        if($filas>0)//posee al menos 1 falta en ese día
        {
            //Obtener la última falta
            if($filas==1){$idfalta=retornaAlgoDeBD(0, $sql);}
            else
            {
                $faltas=retornaAlgoDeBD(1, $sql); 
                $tot=count($faltas);
                $idfalta=$faltas[$tot-1];
            }
            //ver si la falta ya está justificada
            $sql="SELECT idjustificar_falta from justificar_falta where falta_falta='$idfalta'";
            $filas=obtenerFilas($sql);
            if($filas==0)
            {
                //no se ha justificado la falta, la podemos justificar
                //para que se evalue la imagen
                $laImagen=$_FILES["archivo"]["name"][0];
                $extension=$_FILES["archivo"]["type"][0];
                $origen=$_FILES["archivo"]["tmp_name"][0];
                $destino=$carpetaDestino.$laImagen;

                $sql="INSERT INTO justificar_falta (fecha, falta_falta, quincena) VALUES ('$fec_act', '$idfalta', '$quincena');";
                $ok= "<script> imprime('Falta justificada correctamente.'); </script>";
                $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                $correcto=insertaEnBD($sql,$ok,$error,0);

                //correcto obtiene el último ID que se insertó
                //f.$correcto indica el nombre de la imagen asociada a dicha falta
                $SubeImagen=analizaYCargaImagen($origen,$destino,$laImagen,"f".$correcto,$extension,$ok,1);
                //insertar en bitácora
                insertaEnBitacoraJustificarFalta($ok,"Guardado",$correcto,$fec_act,$idfalta,"",
                "","");
            }
            else
            {
                echo "<script> imprime('Esta falta ya ha sido justificada antes.'); </script>";
            }
        }
        else
        {
            echo "<script> imprime('No una falta en la fecha $fecha para el trabajador $num.'); </script>";
        }
    }
    else //fin if posts vacíos
    {
        //Obligar al usuario a que meta todos los campos que se solicitan en aprobaciones.php
        $error="Faltan los siguientes datos:"."<br>";
        if (empty($_POST["num"])){$error.="Número de trabajador que exista."."<br>";}
        if (empty($_POST["fec"])){$error.="La fecha de inicio de la falta a justificar."."<br>";} 
        if (empty($_FILES["archivo"]["name"][0])){$error.="El archivo escaneado"."<br>";}
        echo "<script> imprime('$error'); </script>";
    }
?>