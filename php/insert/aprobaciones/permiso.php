<?php
    if ((!empty($_POST["num"])) && (!empty($_POST["fec"])) && (!empty($_POST["fecf"])) && (!empty($_FILES["archivo"]) && $_FILES["archivo"]["name"][0]))
    {
        $num = $_POST['num'];//el número del trabajador
        $fecha=$_POST['fec'];//la fecha de inicio
        $fechaf=$_POST['fecf'];
       
        /*
        SOLO DE BASE
        CICA 40
        Art.56 permisos con goce de sueldo, de entre uno y hasta por tres días cada uno de ellos, por los siguientes motivos:
            I. Por intervención quirúrgica o internamiento en instituciones hospitalarias, del cónyuge, hijas o hijos, 
            padres; 
            II. En caso de siniestro en el hogar de la trabajadora o del trabajador;
            III. Por privación de la libertad o accidente del cónyuge, hijas o hijos, o padres de la trabajadora 
            o del trabajador; y
            IV. Por sustentar examen profesional;
            Este tipo de permisos se otorgarán a solicitud de la trabajadora o del trabajador, mediante la debida comprobación 
            del motivo.
            A la trabajadora o al trabajador que en el término de un semestre, exceda de tres días de permiso con goce 
            de sueldo, se le descontarán los días excedentes de las prestaciones económicas referidas en el Artículo 87,
            fracción VII de las presentes Condiciones (Art. 87: Como ayuda por la muerte de un familiar en primer grado, 
            la cantidad de $2,800.00 para los gastos del funeral).
            En el caso de que el trabajador/ra requiera por las 4 causales mencionadas en las CGT de más días, se le concederán 
            descontándolos de los días que por antigüedad tiene derecho, anulando automáticamente el pago de estímulos.
        */
        $tipo=tipoEmpleado($num);
        if($tipo=="BASE")
        {
            $validarfechas=RevisarFechas(1,$fecha,$fechaf,"del permiso con goce de sueldo","una permiso con goce de sueldo","",0);
            $duracion=calcularDuracionEntreDosFechas(0,$fecha,$fechaf);
            $sql="SELECT duracion FROM especial where 
            ((clave_especial_clave_especial='41' and empresa!='pension') or clave_especial_clave_especial='40') 
            and trabajador_trabajador='$num'
            and fecha_inicio like '$anio%'";
            //Obtener los 40 y 41 de este año y sumar los días solicitados
            $diasGastados=sumaRegistrosDeConsulta($sql);//Hacer una resta para obtener los días que le quedan
            $antiguedad=calculaAntiguedad($num);
            //calcular dias permitidos en base al artículo 87 fracción 7
            $diasPermitidos=diasAntiguedad87V11($antiguedad);
            $diasSobrantes=$diasPermitidos-$diasGastados;
            if($duracion<=$diasSobrantes)//si aún le quedan días
            {
                $Clave=40;
                //para que se evalue la imagen
                $laImagen=$_FILES["archivo"]["name"][0];
                $extension=$_FILES["archivo"]["type"][0];
                $origen=$_FILES["archivo"]["tmp_name"][0];
                $destino=$carpetaDestino.$laImagen;
                //insertar el permiso
                $sql="INSERT INTO especial VALUES (null, '$fecha', '$fechaf', '00:00:00', '00:00:00', '0', '$num', '$Clave','*Ver documento*','$duracion')";
                $ok= "<script> imprime('Permiso con goce de sueldo agregado correctamente.'); </script>";
                $error= "<script> imprime('Algo salió Mal. Reintente...'); </script>";
                $correcto=insertaEnBD($sql,$ok,$error,0);
                //correcto obtiene el último ID que se insertó
                $SubeImagen=analizaYCargaImagen($origen,$destino,$laImagen,$correcto,$extension,$ok,1);
                insertaEnBitacoraEspecial($ok,"Guardado",$fecha,$fechaf,"-","-",
                $Clave,"-","$duracion","-","-","-","-",
                "-","-","-",$num,$correcto);
            }
            else//fin if duracion<=dias sobrantes
            {
                echo "<script> imprime('El empleado con número $num está solicitando un permiso con goce hasta por 3 días por' + 
                ' un total de $duracion días; solo puede, por su antiguedad solicitar permisos pagados de este tipo' + 
                ' por $diasPermitidos días. Sustento: Artículos 57 y 87 Fracción 7 de las CGT.'); </script>";
                exit();
            }
        }
        else
        {
            $tipo=utf8_encode($tipo);
            echo "<script> imprime('El empleado con número $num es de tipo $tipo. Se requiere ser de BASE para solicitar este permiso.' +
            ' Sustento: clave 40, cobertura del CICA.'); </script>";
        }//fin if tipo==BASE
    }
    else//Fin if validar campos
    {
        $error="Faltan los siguientes datos:"."<br>";
        if (empty($_POST["num"])){$error.="Número de trabajador que exista."."<br>";}
        if (empty($_POST["fec"])){$error.="La fecha de inicio de lo que está solicitando."."<br>";} 
        if (empty($_POST["fecf"])){$error.="La fecha de fin de lo que está solicitando."."<br>";}
        if (empty($_FILES["archivo"]["name"][0])){$error.="El archivo escaneado"."<br>";}
        echo "<script> imprime('$error'); </script>";
    }
?>