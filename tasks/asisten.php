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
    $posUltimoRegistroAsistencia=5;
    
    //QUIENES ASISTIERON HOY (turno_entrada,fecha entrada, su numero empleado e id)
    function quienes_asistieron_hoy()
    {
        global $con;
        global $f_hoy;
        global $posUltimoRegistroAsistencia;
        $asisten_hoy=[];
        $aumenta=0;

        //seleccionar el valor del ultimo id seleccionado
        $sql=" SELECT Valor FROM _posicion where idposicion=$posUltimoRegistroAsistencia";
        $query= mysqli_query($con, $sql);
        $fila=mysqli_num_rows($query);
        if($query)
        {
            $resul=mysqli_fetch_array($query);
            $valor=$resul[0];
        }
        else
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $hacer='seleccionar el valor de la posición'.$posUltimoRegistroAsistencia;
            $tabla='_posicion';
            $línea='25';
            error($er1,$er2,$hacer,$tabla,$línea);
        }
        
        //Seleccionar a todos los que vinieron hoy
        $sql1="select d.fecha_entrada,a.numero_trabajador,d.id from trabajador a
        inner join acceso b on a.numero_trabajador=b.trabajador_trabajador 
        inner join turno c on b.turno_turno=c.idturno
        inner join asistencia d on a.numero_trabajador=d.trabajador_trabajador 
        and d.id > $valor
        and d.fecha_entrada like '$f_hoy%'
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
            $sql2="UPDATE _posicion SET Valor = $posicion  WHERE (idposicion = $posUltimoRegistroAsistencia)";
            $query2= mysqli_query($con, $sql2); 
            if(!$query2)
            {
                $er1=mysqli_errno($con);
                $er2=mysqli_error($con);
                $hacer='actualizar';
                $tabla='_posicion';
                $línea='66';
                error($er1,$er2,$hacer,$tabla,$línea);
            }
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
        $cantidad=count($datos);     
        if($datos!=null)
        {
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
                            if(!$query2)
                            {
                                $er1=mysqli_errno($con);
                                $er2=mysqli_error($con);
                                $hacer='actualizar';
                                $tabla='vienen_hoy';
                                $línea='122';
                                error($er1,$er2,$hacer,$tabla,$línea);
                            }  
                            //Concatenar la fecha de hoy con su hora de entrada 
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
        $query=mysqli_query($con,"insert into incidencia values('', '$mt', '$inc', $id_asis);");
        if(!$query)
        {
            $er1=mysqli_errno($con);
            $er2=mysqli_error($con);
            $hacer='insertar';
            $tabla='incidencia';
            $línea='159';
            error($er1,$er2,$hacer,$tabla,$línea);
        }   
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
                        { //CLAVE 24--> Registro extemporáneo de tolerancia a la entrada de la jornada ordinaria.
                            inserta($mt,"tarde",'24',$id_asis);
                        }
                    }
                }
            }
        }
    }

    function error($er1,$er2,$accion,$nomTabla,$numLinea)
    {
        $error="";
        $err1="$er1";
        $err2="$er2";
        //Hacer UN EXPLODE DE ERR2
        $divide=explode("'",$err2);
        $tamDivide=count($divide);//saber el tamaño del array
        if($tamDivide>0)//si el array posee datos
        {
            $err2="";
            for($i=0;$i<$tamDivide;$i++)
            {
                $err2.=$divide[$i];
            }
        }

        $error="Error al $accion en la tabla $nomTabla. $err1 : $err2. Línea de error: $numLinea. Tarea asisten.";
        echo"<script> console.error('$error'); </script>";
        exit();
    }
?>