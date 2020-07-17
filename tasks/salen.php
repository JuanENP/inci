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


    //QUIENES ASISTIERON HOY (turno_salida,fecha salida, su numero empleado e id)
    function quienes_asistieron_hoy()
    {
        global $f_hoy;
        global $nombre;
        global $contra;
        require("../Acceso/global.php");
        $asisten_hoy=[];
        $aumenta=0;
        //seleccionar el valor del ultimo id seleccionado
        $sql=" SELECT Valor FROM _posicion where idposicion=6";
        $query= mysqli_query($con, $sql);
        $resul=mysqli_fetch_array($query);
        $valor=$resul[0];
        
        //Seleccionar a todos los que checaron su salida
        $sql1="select d.fecha_salida,a.numero_trabajador,d.id from trabajador a
        inner join acceso b on a.numero_trabajador=b.trabajador_trabajador 
        inner join turno c on b.turno_turno=c.idturno
        inner join asistencia d on a.numero_trabajador=d.trabajador_trabajador 
        and d.id >$valor
        and Cast(d.fecha_salida As Date)='$f_hoy'
        group by id;";
        $query1= mysqli_query($con, $sql1);
        $filas1=mysqli_num_rows($query1);
        if($filas1>0)
        {    
            while($resul1=mysqli_fetch_array($query1))
            {

                $asisten_hoy[$aumenta][0]=$resul1[0];//fecha_salida
                $asisten_hoy[$aumenta][1]=$resul1[1];//numero de trabajador
                $asisten_hoy[$aumenta][2]=$resul1[2];//id de asistencia
                $posicion=$resul1[2];
                $aumenta++;
                //echo $ultimo_id=$asisten_hoy[$aumenta][3];
            } 
            //Actualizar en posicion el ultimo id seleccionado
            $sql2="UPDATE _posicion SET Valor = $posicion  WHERE (idposicion = 6)";
            $query2= mysqli_query($con, $sql2);
            if(!$query2)
            {
                $er1=mysqli_errno($con);
                $er2=mysqli_error($con);
                $hacer='actualizar';
                $tabla='_posicion';
                $línea='53';
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
        $cantidad=0;     
        if($datos!=null)
        { 
            //Calcula la cantidad de filas que hay en $datos=quienes_asistieron_hoy();
            foreach($datos as $fila)
            {
                $cantidad++;
            }
            //Seleccionar a todos los empleados que vienen hoy
            $sql="SELECT idvienen_hoy,trabajador_trabajador,salida FROM vienen_hoy where observar_s=-1";
            $query= mysqli_query($con, $sql);
            $filas=mysqli_num_rows($query);
            if($filas>0)
            {  
                while($resul=mysqli_fetch_array($query))
                {
                    $idvienen=$resul[0];
                    $numero=$resul[1];//Numero de empleado
                    $salida=$resul[2];//Hora de salida de su horario
                    for($j=0;$j<$cantidad;$j++)
                    {  
                        $salida_asistencia=$datos[$j][0];//hora en la que checo su salida (en asistencia)
                        $numero_emp=$datos[$j][1];
                        $idasistencia=$datos[$j][2];
                      
                        if ($numero==$numero_emp)
                        {  
                            $sql2="UPDATE vienen_hoy SET observar_s = 0 WHERE (idvienen_hoy = $idvienen);";
                            $query2= mysqli_query($con, $sql2);
                            if(!$query2)
                            {
                                $er1=mysqli_errno($con);
                                $er2=mysqli_error($con);
                                $hacer='actualizar';
                                $tabla='vienen hoy';
                                $línea='107';
                                error($er1,$er2,$hacer,$tabla,$línea);
                            }
                            
                            //Concatener la fecha de hoy con su hora de salida 
                            $hora_salida=$f_hoy . ' ' . $salida;
                        
                            //Calcular si la persona tiene una incidencia en la salida
                            minA_minD($hora_salida, $salida_asistencia, $idasistencia,$numero);
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
    
        
    /*Inserta datos en la tabla incidencia y la bitacora incidencias*/ 
    function inserta($mt, $ma_d, $inc,$id_asis)
    { 
        global $nombre;
        global $contra;
        require("../Acceso/global.php"); 
        //Si mt está vacio no se debe insertar algo en descripcion
        if($mt=='')
        {
            $mt="-";
            $query=mysqli_query($con,"insert into incidencia values(' ', '$mt', '$inc', $id_asis);");
            if(!$query)
            {
                $er1=mysqli_errno($con);
                $er2=mysqli_error($con);
                $hacer='insertar';
                $tabla='incidencia';
                $línea='154';
                error($er1,$er2,$hacer,$tabla,$línea);
            }
        }
        else
        {   //ma_d guarda la palabra antes o despues
            $mt=$mt . " minutos " . $ma_d;
            mysqli_query($con,"insert into incidencia values(' ', '$mt', '$inc', $id_asis);");
            if(!$query)
            {
                $er1=mysqli_errno($con);
                $er2=mysqli_error($con);
                $hacer='insertar';
                $tabla='incidencia';
                $línea='168';
                error($er1,$er2,$hacer,$tabla,$línea);
            }
        }    
    }
    
        
    /*Calcula los minutos antes o despues de la hora de checar entrada*/
    function minA_minD ($fechaO, $fechaS, $id_asis,$numero)
    {

        global $nombre;
        global $contra;
        require("../Acceso/global.php");  
        //formatear fecha de Salida y salida para ver cuál es mayor
        $fechaOriginal=strtotime($fechaO);
        $fechaSale=strtotime($fechaS);
        //Ver si salió justo en su hora de salida o despues
        if($fechaSale>=$fechaOriginal)
        {
            //No hacer algo 
        }
        else
        {
            
            //Marcar incidencias
            $mt=minutosTranscurridos($fechaO, $fechaS);
            //'25', 'REGISTRAR ANTES DE LA HORA DE SALIDA EN LA JORNADA LABORAL. '
            //'27', 'REGISTRAR ANTES DE LA HORA DE SALIDA DEL TURNO OPCIONAL O PERCEPCIÓN ADICIONAL.  '
            //PENDIENTE SABER QUIENES SON EL TURNO OPCIONAL//
            //Si el tiempo es mayor a igual a 1 minuto y menor o igual que 120 minutos (2hrs) 
            if(($mt >=1) && ($mt<=120))
            {
               //buscar si el empleado tiene pase de salida
                $tiene=pase_salida($numero);
                if($tiene==false)
                {
                    inserta($mt,'antes','25',$id_asis);
                }
            }
            else
            {
                inserta($mt,'antes','25',$id_asis);
            }
        }

    }

  
    function pase_salida($num)
    {
       
        global $f_hoy;
        global $nombre;
        global $contra;
        require("../Acceso/global.php");  
        //Buscar si el empleado tiene un pase de salida
        $sql1="SELECT idpase_salida FROM pase_salida where trabajador_trabajador='$num' and fecha_uso='$f_hoy'";
        $query1= mysqli_query($con, $sql1);
        $filas1=mysqli_num_rows($query1);
        //Si el empleado no tiene un pase de salida se marcará una incidencia
        if($filas1==0)
        {  
            return false;
        }
    }
    falta();
    function falta()
    {
        global $f_hoy;
        global $nombre;
        global $contra;
        require("../Acceso/global.php");  
        //Ver en que quincena estamos
        $sql="SELECT idquincena FROM quincena where validez =1";
        $query= mysqli_query($con, $sql);
        $filas=mysqli_num_rows($query);
        if($filas>0)
        {   
            $resul=mysqli_fetch_array($query);
            $idquincena=$resul[0];
        }
 
        //Seleccionar a todos los empleados de vienen hoy que siguen con -1 y -1
        $sql2="SELECT trabajador_trabajador FROM vienen_hoy where observar_e=-1 and observar_s=-1";
        $query2= mysqli_query($con, $sql2);
        $filas2=mysqli_num_rows($query2);
        if($filas2>0)
        {   
            while($resul2=mysqli_fetch_array($query2))
            {
                $numero_empleado=$resul2[0];
                //Insertar una falta
                $sql3="INSERT INTO falta VALUES ('','$f_hoy', '$idquincena', '$numero_empleado','10')";
                $query3= mysqli_query($con, $sql3);
                if(!$query3)
                {
                    $er1=mysqli_errno($con);
                    $er2=mysqli_error($con);
                    $hacer='insertar';
                    $tabla='falta';
                    $línea='149';
                    error($er1,$er2,$hacer,$tabla,$línea);
                }
            }
        }
    }
    omision_entrada();
    function omision_entrada()
    {
        global $f_hoy;
        global $nombre;
        global $contra;
        require("../Acceso/global.php"); 
        //Seleccionar a todos los empleados de vienen hoy que siguen con -1
        $sql="SELECT b.id, a.trabajador_trabajador FROM vienen_hoy a
        inner join asistencia b on a.trabajador_trabajador=b.trabajador_trabajador
		and Cast(b.fecha_salida As Date)='$f_hoy' 
		and observar_e=-1 and observar_s=0;";
        $query= mysqli_query($con, $sql);
        $filas=mysqli_num_rows($query);
        if($filas>0)
        {   
            while($resul=mysqli_fetch_array($query))
            {
                $idasistencia=$resul[0];
                $numero_empleado=$resul[1];
                //'18', 'OMISIÓN DE ENTRADA EN EL REGISTRO DE ASISTENCIA A LA JORNADA LABORAL'
                // '20', 'OMISIÓN DE ENTRADA Y/O SALIDA AL TURNO OPCIONAL O PERCEPCIÓN ADICIONAL EN EL REGISTRO DE ASISTENCIA.'
                inserta('','','18',$idasistencia);
            }
        }
    }
    omision_salida();
    function omision_salida()
    {   global $f_hoy;
        global $nombre;
        global $contra;
        require("../Acceso/global.php");   
        //Seleccionar a todos los empleados de vienen hoy que siguen con -1
        $sql="SELECT b.id, a.trabajador_trabajador FROM vienen_hoy a
        inner join asistencia b on a.trabajador_trabajador=b.trabajador_trabajador
		and Cast(b.fecha_entrada As Date)='$f_hoy' 
		and observar_e=0 and observar_s=-1;";
        $query= mysqli_query($con, $sql);
        $filas=mysqli_num_rows($query);
        if($filas>0)
        {   
            while($resul=mysqli_fetch_array($query))
            {
                $idasistencia=$resul[0];
                $numero_empleado=$resul[1];
	            //19	OMISIÓN DE SALIDA EN EL REGISTRO DE ASISTENCIA A LA JORNADA LABORAL CONTINUA. 
                //	20	OMISIÓN DE ENTRADA Y/O SALIDA AL TURNO OPCIONAL O PERCEPCIÓN ADICIONAL EN EL REGISTRO DE ASISTENCIA.
                inserta('','','19',$idasistencia);
                
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
