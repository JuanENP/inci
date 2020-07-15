<?php
   date_default_timezone_set('America/Mexico_City'); 
   set_time_limit(600);//Indica que son 600 segundos, es decir 10 minutos máximo para ejecutar todo el script

   //OBTENER QUE DÍA ES HOY
   $dias = array("domingo","lunes","martes","miercoles","jueves","viernes","sabado");
   //echo "HOY ES ".$dias[date("w")] . "<br>";//esto es solo para ver si el día es correcto
   $diaactual=$dias[date("w")];//guardar el día actual para su posterior uso
   $f_hoy=date("Y-m-d");//guardar la fecha actual


   //Pendiente saber si se guardará la posicion
   $nombre="biometric";
   $contra="5_w**/pQxcmk.";
   require("../Acceso/global.php");
   $sql="SELECT Valor FROM _posicion where idposicion=4;";
   $query= mysqli_query($con, $sql);
   $resul=mysqli_fetch_array($query);
   $pos=$resul[0]; 

  //Consultar si vino algún trabajador diferente que no esté registrado en la tabla vienen hoy
   function vino_no_debia()
   {
        global $f_hoy;
        global $con;
        $sql1="Select b.trabajador_trabajador from  asistencia b 
        where (CAST(b.fecha_entrada AS DATE) = '$f_hoy' 
        or CAST(b.fecha_salida AS DATE) = '$f_hoy')
        and not exists(select trabajador_trabajador from vienen_hoy c 
        where b.trabajador_trabajador= c.trabajador_trabajador);";
        $query1= mysqli_query($con, $sql1);
        $filas=mysqli_num_rows($query1);
        if($filas>0)
        {
            while($resul=mysqli_fetch_array($query1))
            {
                $NumeroDeEmpleado=$resul[1];
                $resultadoVacaciones=tieneVacaciones($NumeroDeEmpleado);
                $resultadoCumpleOno=tieneCumpleUono($NumeroDeEmpleado);
                $resulSexta=debiaVenirEnSextaOAcceso($NumeroDeEmpleado);
                $resulAcceso=debiaVenirEnSextaOAcceso($NumeroDeEmpleado);

                //Si el trabajador vino porque en acceso o sexta debía venir pero hoy tiene vacaciones
                if($resultadoVacaciones!==null && ($resulAcceso!==null || $resulSexta!==null))
                {   $periodo=$resultadoVacaciones[1];
                    if($periodo==2)
                    {
                        //mandar una alerta de que será necesario cambiar las fechas pendientes de las vacaciones de los trabajadores
                    }
                }
                //Si el trabajador vino porque en acceso o sexta debía venir pero hoy su cumpleaños u onomastico
                if($resultadoCumpleOno!==null && ($resulAcceso!==null || $resulSexta!==null))
                {
                    //Marcar un PT
                }
            }
        }
    }

    function tieneVacaciones($numEmpleado)
    {
        // Seleccionar si el trabajador debe tener vacaciones el día de hoy y tomado = 0, y de que periodo es 
        global $f_hoy;
        global $con;
        $sql1="Select b.iddia,b.periodo,b.dia  from vacaciones a
        inner join dias_vacaciones b 
        where a.idvacaciones=b.vacaciones_vacaciones
        and a.trabajador_trabajador='$numEmpleado'
        and b.dia='$f_hoy'
        and b.tomado=0;";
        $query1= mysqli_query($con, $sql1);
        $filas=mysqli_num_rows($query1);
        if($filas==1)
        {
            $resul=mysqli_fetch_array($query1);
            return[ $resul[0],$resul[1],$resul[2]];
        }
        else
        {
            return null;
        }
    }

    function tieneCumpleUono($numEmpleado)
    {
        // Seleccionar si el trabajador debe tener vacaciones el día de hoy y tomado = 0, y de que periodo es 
        global $f_hoy;
        global $con;
        $sql1="select * from cumple_ono
        where (fecha_cumple='$f_hoy' and validez=0 ) or (fecha_ono= '$f_hoy'and validez=1)
        and trabajador_trabajador='$numEmpleado';";
        $query1= mysqli_query($con, $sql1);
        $filas=mysqli_num_rows($query1);
        if($filas==1)
        {
            return true;
        }
        else
        {
            return null;
        }
    }
    function debiaVenirEnAcceso($numEmpleado)
    {
        // Seleccionar si el trabajador debe tener vacaciones el día de hoy y tomado = 0, y de que periodo es 
        global $f_hoy;
        global $con;
        global $diaactual;
        $sql1="select t.numero_trabajador,c.entrada,c.salida from trabajador t 
        inner join acceso a on a.trabajador_trabajador = t.numero_trabajador 
        inner join turno c on c.idturno=a.turno_turno 
        and a.$diaactual=1 and a.t_dias=-1
        where a.trabajador_trabajador='$numEmpleado';";
        $query1= mysqli_query($con, $sql1);
        $filas=mysqli_num_rows($query1);
        if($filas==1)
        {
            return true;
        }
        else
        {
            return null;
        }
    }
    function debiaVenirEnSextaOAcceso($numEmpleado)
    {
        // Seleccionar si el trabajador debe tener vacaciones el día de hoy y tomado = 0, y de que periodo es 
        global $f_hoy;
        global $con;
        global $diaactual;
        $sql1="select b.trabajador_trabajador
        from trabajador a 
        inner join acceso b on a.numero_trabajador=b.trabajador_trabajador
        inner join sexta c on a.numero_trabajador=c.trabajador_trabajador
        inner join turno d on b.turno_turno=d.idturno
        and ((b.$diaactual = 1 and b.t_dias<3) or (c.$diaactual = 1 and c.validez=1 and c.t_dias<2))
        and a.numero_trabajador='$numEmpleado';";
        $query1= mysqli_query($con, $sql1);
        $filas=mysqli_num_rows($query1);
        if($filas==1)
        {
            return true;
        }
        else
        {
            return null;
        }
    }
?>