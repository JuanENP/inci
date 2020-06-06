<?php
    /*las becas capacitacion son 12 días máximo al semestre: según Artículo 29 fracción VIII de las CGT
        CICA 29
    */
    if ((!empty($_POST["num"])) && (!empty($_POST["fec"])) && (!empty($_POST["fecf"])) && (!empty($_POST["cucap"])) && (!empty($_FILES["archivo"]) && $_FILES["archivo"]["name"][0]))
    {
        $num=$_POST["num"];
        $fecha=$_POST["fec"];
        $fechaf=$_POST["fecf"];
        $opcionCurso=$_POST["cucap"];
        $Clave="29";
        $he="00:00:00";
        $hs="00:00:00";
        $validarfechas=RevisarFechas(1,$fecha,$fechaf,"del curso capacitación","un curso capacitación","",0);
        //Verificar que si elige la opcion de verificar con horario distinto tengamos las horas de entrada y salida
        if($opcionCurso=="d")
        {
            if ((!empty($_POST["he"])) && (!empty($_POST["hs"])))
            {
                $he=$_POST["he"];
                $hs=$_POST["hs"];
            }
            else //fin if hora de entrada y salida no vacíos
            {
                $error="Faltan los siguientes datos:"."<br>";
                if (empty($_POST["he"])){$error.="La hora nueva de entrada"."<br>";}
                if (empty($_POST["hs"])){$error.="La hora nueva de salida"."<br>";}
                echo "<script> imprime('$error'); </script>";
                exit();
            }
        }
        //fin verificacion de tipo de curso con horario distinto
        //Saber cuantos días ya ha usado de este permiso, recordemos que son 12 por semestre
        if($mes>0 && $mes<=6)//de enero a junio
        {
            $sql="SELECT duracion, idespecial, fecha_inicio, fecha_fin FROM especial where 
            clave_especial_clave_especial='29'
            and ((fecha_inicio>='2020-01-01' or fecha_inicio>='2020-06-31') 
            or (fecha_fin>='2020-01-01' or fecha_fin>='2020-06-31'));";
        }
        else
        {
            if($mes>6 && $mes<=12) //de julio a diciembre
            {
                $sql="SELECT duracion, idespecial, fecha_inicio, fecha_fin FROM especial where 
                clave_especial_clave_especial='29'
                and ((fecha_inicio>='2020-07-01' or fecha_inicio>='2020-12-31') 
                or (fecha_fin>='2020-07-01' or fecha_fin>='2020-12-31'));";
            }
        }
        $diasUsados=sumaRegistrosDeConsulta($sql);
        //Fin saber cuantos dias ha usado este permiso
        $diasRestantes=12-$diasUsados;
        $duracion=calcularDuracionEntreDosFechas(2,$fecha,$fechaf);//la duracion excluyendo los días festivos
        if($duracion<=12)
        {
            if($duracion<=$diasRestantes)
            {
                //para que se evalue la imagen
                $laImagen=$_FILES["archivo"]["name"][0];
                $extension=$_FILES["archivo"]["type"][0];
                $origen=$_FILES["archivo"]["tmp_name"][0];
                $destino=$carpetaDestino.$laImagen;
                //Agregar curso
                $sql="INSERT INTO especial VALUES (null, '$fecha', '$fechaf', '$he', '$hs', '0', $num, '$Clave','*Ver documento*','$duracion')";
                $ok= "<script> imprime('Curso capacitación agregado correctamente.'); </script>";
                $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                $correcto=insertaEnBD($sql,$ok,$error,0);
                //correcto obtiene el último ID que se insertó
                $SubeImagen=analizaYCargaImagen($origen,$destino,$laImagen,$correcto,$extension,$ok,1);
                insertaEnBitacoraEspecial($ok,"Guardado",$fecha,$fechaf,$he,$hs,
                $Clave,"-","$duracion","-","-","-","-",
                "-","-","-",$num,$correcto);
            }
            else
            {
                echo "<script> imprime('La duración del curso que está solicitando es de $duracion días, sin embargo' +
                ' el empleado con número $num ya ha gastado $diasUsados días de los 12 por semestre que tiene' +
                ' permitidos. Sustento: Artículo 29 fracción VIII de las CGT'); </script>";
            }
        }
        else//fin ifduracion<=12
        {
            echo "<script> imprime('La duración de este curso que solicita es de $duracion días. Este tipo de curso solo se puede'+
            ' solicitar por una duración de 12 días máximo por semestre. Sustento: Artículo 29 fracción VIII de las CGT'); </script>";
        }
    }
    else// fin if validar posts
    {
        $error="Faltan los siguientes datos:"."<br>";
        if (empty($_POST["num"])){$error.="Número de trabajador que exista."."<br>";}
        if (empty($_POST["fec"])){$error.="La fecha de inicio del curso"."<br>";}
        if (empty($_POST["fecf"])){$error.="La fecha de fin del curso"."<br>";}
        if (empty($_POST["cucap"])){$error.="La opción del curso"."<br>";}
        if (empty($_FILES["archivo"]["name"][0])){$error.="El archivo escaneado"."<br>";}
        echo "<script> imprime('$error'); </script>";
    }
?>