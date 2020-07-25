<?php
session_start();
    if (($_SESSION["name"]) && ($_SESSION["con"]))
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        require("../../Acceso/global.php"); 
    }
    else
    {
        header("Location: ../../index.php");
        die();
    }

    require("../../assets/js/alerts-justificacion.php");
    //******formatear a la zona horaria de la ciudad de México**********
    date_default_timezone_set('America/Mexico_City');
    set_time_limit(600);//10 minutos máximo para la ejecución de un script

    if(empty($_POST["opcion"]))
    {
        echo "Por favor, diríjase a la sección Vacaciones para que esta página se ejecute normalmente: " . "<a href='../../ht/vacaciones.php'>IR AHORA</a>";
        exit();//terminar el script
    }

    if($_POST["opcion"]=="lote")
    {
        if((!empty($_FILES["archivo"]) && $_FILES["archivo"]["name"][0]))
        {
            //echo "<script> imprime('ok lote'); </script>";
            $fec_act=date("Y-m-d"); //la fecha actual
            $carpetaDestino="../../vacaciones/";
            $name="vacaciones-".$fec_act;
            $elArchivo=$_FILES["archivo"]["name"][0];
            $extension=$_FILES["archivo"]["type"][0];
            $origen=$_FILES["archivo"]["tmp_name"][0];
            $destino=$carpetaDestino.$elArchivo;

            require '../../excel/carga_excel.php';
            $rutaArchivo=analizaYCargaExcel($origen,$destino,$elArchivo,$name,$extension);

            require '../../excel/PHPExcel/IOFactory.php';
            $objPHPExcel = PHPEXCEL_IOFactory::load($rutaArchivo);//cargar en memoria el archivo
            $objPHPExcel->setActiveSheetIndex(0);//seleccionar la hoja 1
            cargaDeExcelABD();

        }
        else
        {
            echo "<script> imprime('No ha elegido el archivo excel'); </script>";
        }
    }//fin opcion lote
    else
    {
        if($_POST["opcion"]=="indiv")
        {
            if (!empty($_POST["num"]))
            {
                echo "<script> imprime('indi' +
                'vidual'); </script>";
            }
            else
            {
                echo "<script> imprime('No ha elegido un número de trabajador ' +
                'válido'); </script>";
            }
        }
        //fin opcion individual
    }

    function cargaDeExcelABD()
    {
        global $con;
        global $objPHPExcel;
        $errores=array();
        $centinela=0;
        $numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();//número de columnas
        
        /*Recorrer cada fila del archivo e insertarla en la BD*/
        for($i=3;$i<=$numRows;$i++)
        {
            $numero = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
            //si el número es distinto de vacío
            if($numero!="")
            {
                $diaI = $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getFormattedValue();
                $diaF = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getFormattedValue();
                $diaSuel = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getFormattedValue();
                $diaI2 = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getFormattedValue();
                $diaF2 = $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getFormattedValue();
                $diaSuel2 = $objPHPExcel->getActiveSheet()->getCell('G'.$i)->getFormattedValue();

                $idVac=obtenerID_Vac($numero);
                if($idVac!=0)
                {
                    $resultado=insertBD($numero,$diaI,$diaF,$diaSuel,$diaI2,$diaF2,$diaSuel2);
                    if($resultado!=0)
                    {
                        //no se pudo insertar este trabajador
                        array_push($errores,"No se insertaron los datos del trabajador con número $numero hágalo manualmente.");
                    }
                }
                else
                {
                    //si arrojó 0 filas con ese trabajador, indica que no existe dicho empleado
                    array_push($errores,"El trabajador con número $numero no existe, verifique.");
                }
            }
            else
            {
                $centinela++;
            }

            if($centinela==2){$i=$numRows+1;/*salir de este bucle si hallamos dos números de trab. vacíos*/}

            //echo $numero."***".$diaI."***".$diaF."***".$diaSuel."***".$diaI2."***".$diaF2."***".$diaSuel2."<br>";
        }

        if(count($errores)>0)
        {
            $salida="";
            for($i=0;$i<count($errores);$i++)
            {
                $salida.="<br>".$errores[$i];
            }
            $salida.="<br>";
            echo "<script> imprime('Ocurrieron los siguientes errores: $salida. Los demás días de vacaciones de los demás trabajadores se guardaron de forma correcta.'); </script>";
        }
        else
        {
            echo "<script> imprime('Datos subidos de forma correcta.'); </script>";
        }
        exit;
    }

    function obtenerID_Vac($num)
    {
        global $con;
        $sql="SELECT idvacaciones from vacaciones where trabajador_trabajador='$num'";
        $query=mysqli_query($con, $sql) or die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
        $filas=mysqli_num_rows($query);
        if($filas==1)
        {
            $resul=mysqli_fetch_array($query);
            return $resul[0];//Devolver un solo dato
        }
        else
        {
            if($filas==0)
            {
                return 0;
            }
        }
    }

    function insertBD($numero, $diaInicial, $diaFinal,$diaSuelto,$diaInicial2, $diaFinal2,$diaSuelto2)
    {
        global $con;
        $rangoP1=0;
        $rangoP2=0;
        $suelto1=0;
        $suelto2=0;

        if($diaInicial!="" && $diaFinal!="")
        {
            //sí hay rango inicial del periodo 1
            $rangoP1=1;
            //array que me devuelve la función
            $fechasAInsertar1=obtenDiasDeRango($diaInicial, $diaFinal);
        }

        if($diaSuelto!="")
        {
            //si hay días sueltos del periodo 1
            $suelto1=1;
            //si hay un rango P1
            if($rangoP1==1)
            {
                //tamaño del array, ese tamaño es la sig. posición en la cual insertaremos los sig. días
                $tam1=count($fechasAInsertar1);
                
                //dividir los días sueltos
                $divide=explode(",",$diaSuelto);
                $tamDivide=count($divide);//saber el tamaño del array
                if($tamDivide>0)//si el array posee datos
                {
                    for($i=0;$i<$tamDivide;$i++)
                    {
                        //formatear a yyyy-mm-dd
                        $fecha=str_replace(' ', '', $divide[$i]);
                        $separa=explode("/",$fecha);
                        $fecha=$separa[2]."/".$separa[1]."/".$separa[0];

                        $fechasAInsertar1[$tam1]=$fecha;
                        $tam1++;
                    }
                }
            }
            else
            {
                //si no hay rangoP1 creamos el array
                $fechasAInsertar1=array();
                $divide=explode(",",$diaSuelto);
                $tamDivide=count($divide);//saber el tamaño del array
                if($tamDivide>0)//si el array posee datos
                {
                    for($i=0;$i<$tamDivide;$i++)
                    {
                        $fecha=str_replace(' ', '', $divide[$i]);
                        $separa=explode("/",$fecha);
                        $fecha=$separa[2]."/".$separa[1]."/".$separa[0];
                        
                        $fechasAInsertar1[$i]=$fecha;
                    }
                }
            }
        }

        //Para el periodo 2       
        if($diaInicial2!="" && $diaFinal2!="")
        {
            //sí hay rango inicial del periodo 2
            $rangoP2=1;
            //array que me devuelve la función
            $fechasAInsertar2=obtenDiasDeRango($diaInicial2, $diaFinal2);
        }

        if($diaSuelto2!="")
        {
            //si hay días sueltos del periodo 2
            $suelto2=1;
            //si hay un rango P2
            if($rangoP2==1)
            {
                //tamaño del array, ese tamaño es la sig. posición en la cual insertaremos los sig. días
                $tam2=count($fechasAInsertar2);
                
                //dividir los días sueltos
                $divide=explode(",",$diaSuelto2);
                $tamDivide=count($divide);//saber el tamaño del array
                if($tamDivide>0)//si el array posee datos
                {
                    for($i=0;$i<$tamDivide;$i++)
                    {
                        //formatear a yyyy-mm-dd
                        $fecha=str_replace(' ', '', $divide[$i]);
                        $separa=explode("/",$fecha);
                        $fecha=$separa[2]."/".$separa[1]."/".$separa[0];

                        $fechasAInsertar2[$tam2]=$fecha;
                        $tam2++;
                    }
                }
            }
            else
            {
                //si no hay rangoP2 creamos el array
                $fechasAInsertar2=array();
                $divide=explode(",",$diaSuelto2);
                $tamDivide=count($divide);//saber el tamaño del array
                if($tamDivide>0)//si el array posee datos
                {
                    for($i=0;$i<$tamDivide;$i++)
                    {
                        $fecha=str_replace(' ', '', $divide[$i]);
                        $separa=explode("/",$fecha);
                        $fecha=$separa[2]."/".$separa[1]."/".$separa[0];
                        
                        $fechasAInsertar2[$i]=$fecha;
                    }
                }
            }
        }
        
        echo "primer array:<br>";
        for($i=0;$i<count($fechasAInsertar1);$i++)
        {
            echo $fechasAInsertar1[$i]."....";
        }
        echo "<br>";

        echo "segundo array:<br>";
        for($i=0;$i<count($fechasAInsertar2);$i++)
        {
            echo $fechasAInsertar2[$i]."....";
        }
        echo "<br>";echo "<br>";

        //Insertar en la BD, pero antes verificar cuántos días de vacaciones merece y retornar un 0 si todo es correcto

    }
    //fin función insertBD

    function obtenDiasDeRango($rango1, $rango2)
    {
        //Formatear los parámetros a dd-mm-yyy
        $divide=explode("/",$rango1);
        $rango1=$divide[0]."-".$divide[1]."-".$divide[2];
        $divide=explode("/",$rango2);
        $rango2=$divide[0]."-".$divide[1]."-".$divide[2];

        //array a retornar
        $devolverFechas=array();
        //insertar la primera fecha
        $divide=explode("-",$rango1);
        $devolverFechas[0]=$divide[2]."/".$divide[1]."/".$divide[0];

        $mod_dia=$rango1;

        $diaFinaldeRango=strtotime($rango2);

        for($i=1;$i<10;$i++)
        {
            $mod_dia = strtotime($mod_dia."+ 1 days");//sumar 1 día
            if($mod_dia==$diaFinaldeRango)
            {
                //guardar el último día del rango
                $divide=explode("-",$rango2);
                $devolverFechas[$i]=$divide[2]."/".$divide[1]."/".$divide[0];
                $i=12;//romper el bucle
            }
            else
            {
                $devolverFechas[$i]=date("Y/m/d",$mod_dia);
                //para que se le sume 1 día
                $mod_dia=date("d-m-Y",$mod_dia);
            }
        }
        return $devolverFechas;
    }
    //fin de obtenDiasDeRango
?>