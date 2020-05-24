<?php
    date_default_timezone_set('America/Mexico_City');
    set_time_limit(600);//Indica que son 600 segundos, es decir 10 minutos máximo para ejecutar todo el script 
    $nombre="biometric";
    $contra="5_w**/pQxcmk.";
    require("../Acceso/global.php");

    //OBTENER QUE DÍA ES HOY
    $dias = array("domingo","lunes","martes","miercoles","jueves","viernes","sabado");
    //echo "HOY ES ".$dias[date("w")] . "<br>";//esto es solo para ver si el día es correcto
    $diaactual=$dias[date("w")];//guardar el día actual para su posterior uso
    $f_hoy=date("Y-m-d");//guardar la fecha actual

    
    //QUIENES ASISTIERON HOY (turno_entrada,fecha entrada, su numero empleado e id)
    function quienes_asistieron_hoy()
    {
        global $f_hoy;
        global $nombre;
        global $contra;
        require("../Acceso/global.php");
        $asisten_hoy=[];
        $aumenta=0;
        
        //seleccionar el valor del ultimo id seleccionado
        $sql=" SELECT Valor FROM _posicion where idposicion=5";
        $query= mysqli_query($con, $sql);
        $resul=mysqli_fetch_array($query);
        $valor=$resul[0];
        
        //Seleccionar a todos los que vinieron hoy
        $sql1="select d.fecha_entrada,a.numero_trabajador,d.id from trabajador a
        inner join acceso b on a.numero_trabajador=b.trabajador_trabajador 
        inner join turno c on b.turno_turno=c.idturno
        inner join asistencia d on a.numero_trabajador=d.trabajador_trabajador 
        and d.id > $valor
        and Cast(d.fecha_entrada As Date) ='$f_hoy'
        group by id;";
        $query1= mysqli_query($con, $sql1);
        $filas1=mysqli_num_rows($query1);
        if($filas1>0)
        {    
            while($resul1=mysqli_fetch_array($query1))
            {

                $asisten_hoy[$aumenta][0]=$resul1[0];//fecha_entrada
                $asisten_hoy[$aumenta][1]=$resul1[1];//numero de trabajador
                $asisten_hoy[$aumenta][2]=$resul1[2];//id de asistencia
                $posicion=$resul1[2];
                $aumenta++;
                //echo $ultimo_id=$asisten_hoy[$aumenta][3];
            } 
            //Actualizar en posicion el ultimo id seleccionado
            $sql2="UPDATE _posicion SET Valor = $posicion  WHERE (idposicion = 5)";
            $query2= mysqli_query($con, $sql2);
           
            return $asisten_hoy;
        }
        else
        {
            return null;
        }//fin else
    }//Fin quienes_asistieron_hoy()

    incidencias();
    function incidencias()
    {
        global $f_hoy;
        global $nombre;
        global $contra;
        require("../Acceso/global.php");
        $datos=quienes_asistieron_hoy();
        $cantidad=0;     
        if($datos!=null)
        {
            //Calcula la cantidad de filas que hay en $datos=quienes_asistieron_hoy();
            foreach($datos as $fila)
            {
                $cantidad++;
            }
            //Seleccionar a todos los empleados que vienen hoy
            $sql="SELECT idvienen_hoy,trabajador_trabajador,entrada FROM vienen_hoy where observar_e=-1 and observar_s=-1";
            $query= mysqli_query($con, $sql);
            $filas=mysqli_num_rows($query);
            if($filas>0)
            {   
                while($resul=mysqli_fetch_array($query))
                {
                    $idvienen=$resul[0];
                    $numero=$resul[1];//Numero de empleado
                    $entrada=$resul[2];//Hora de entrada de su horario
                    for($j=0;$j<$cantidad;$j++)
                    {  
                        $entrada_asistencia=$datos[$j][0];//hora en la que checo su entrada (en asistencia)
                        $numero_emp=$datos[$j][1];
                        $idasistencia=$datos[$j][2];
                        
                        if ($numero==$numero_emp)
                        {  
                            $sql2="UPDATE vienen_hoy SET observar_e = 0 WHERE (idvienen_hoy = $idvienen);";
                            $query2= mysqli_query($con, $sql2);

                            //Concatener la fecha de hoy con su hora de entrada 
                            $hora_entrada=$f_hoy . ' ' . $entrada;
                            //Calcular si la persona tiene una incidencia en la entrada
                            minA_minD($hora_entrada, $entrada_asistencia, $idasistencia);

                        }//fin if numero de empleados iguales
                    }//fin for
                } //fin while
            }//fin if datos null
        }//fin if
    }//fin function
    

    /*Obtiene los minutos transcurridos entre dos fechas*/
    function minutosTranscurridos($fecha_i,$fecha_f)
    {
        $minutos = (strtotime($fecha_i)-strtotime($fecha_f))/60;
        $minutos = abs($minutos); $minutos = floor($minutos);
        return $minutos;
    }

    
    /*Inserta datos en la tabla incidencia*/ 
    function inserta($mt, $ma_d, $inc,$id_asis)
    { //ma_d guarda la palabra antes o despues
        global $nombre;
        global $contra;
        require("../Acceso/global.php"); 
        $mt=$mt . " minutos " . $ma_d;
        mysqli_query($con,"insert into incidencia values('', '$mt', '$inc', $id_asis);");
        mysqli_close($con);     
    }

    
    /*Calcula los minutos antes o despues de la hora de checar entrada*/
    function minA_minD ($fechaO, $fechaLL, $id_asis)
    {
        global $nombre;
        global $contra;
        require("../Acceso/global.php");  
        //formatear fecha de llegada y salida para ver cuál es mayor
        $fechaOriginal=strtotime($fechaO);
        $fechaLlega=strtotime($fechaLL);
        //Ver si llegó antes o después de su hora
        if($fechaOriginal > $fechaLlega)
        {
            //No hacer algo si la persona llegó antes
        }
        else
        {
            //Marcar incidencias
            $mt=minutosTranscurridos($fechaO, $fechaLL);
            //echo "Llegó " . $mt . " minutos tarde";
            if(($mt >=11) && ($mt<=29))
            {   
                //echo "<br>" . "INCIDENCIA CLAVE 01";
                inserta($mt,"tarde",'01',$id_asis); 
            }
            else
            {
                if((minutosTranscurridos($fechaO, $fechaLL) >=30 && (minutosTranscurridos($fechaO, $fechaLL) <=45)))
                {
                    //echo "<br>" . "INCIDENCIA CLAVE 02";
                    inserta($mt,"tarde",'02',$id_asis);
                }
                else
                {
                    if((minutosTranscurridos($fechaO, $fechaLL) >=46 && (minutosTranscurridos($fechaO, $fechaLL) <=59)))
                    {
                    // echo "<br>" . "INCIDENCIA CLAVE 03";
                        inserta($mt," tarde",'03',$id_asis);
                    }
                    else
                    {
                        if(minutosTranscurridos($fechaO, $fechaLL) >=60)
                        {
                            //'18', 'OMISIÓN DE ENTRADA EN EL REGISTRO DE ASISTENCIA A LA JORNADA LABORAL'
                            // '20', 'OMISIÓN DE ENTRADA Y/O SALIDA AL TURNO OPCIONAL O PERCEPCIÓN ADICIONAL EN EL REGISTRO DE ASISTENCIA.'
                            /*
                            //Si el empleado tiene  turno opcional o percepcion adicional
                            {
                                //inserta($mt,"tarde",'20',$id_asis);
                            } 
                            else
                            {
                                //inserta($mt,"",'18',$id_asis);
                            } 
                            */
                            inserta($mt,"tarde",'18',$id_asis);
                        }
                    }
                }
            }
        }
    }
?>