<?php
   date_default_timezone_set('America/Mexico_City'); 
   $deben_hoy_ultimo=0;
   $deben_hoy=[];

   //OBTENER QUE DÍA ES HOY
   $dias = array("domingo","lunes","martes","miercoles","jueves","viernes","sabado");
   //echo "HOY ES ".$dias[date("w")] . "<br>";//esto es solo para ver si el día es correcto
   $diaactual=$dias[date("w")];//guardar el día actual para su posterior uso
   $f_hoy=date("Y-m-d");//guardar la fecha actual


   //POSICIÓN ACTUAL DE LOS REGISTROS DE ASISTENCIA
   $nombre="biometric";
   $contra="5_w**/pQxcmk.";
   require("../Acceso/global.php");
   $sql="SELECT Valor FROM _posicion where idposicion=4;";
   $query= mysqli_query($con, $sql);
   $resul=mysqli_fetch_array($query);
   $pos=$resul[0]; 

  //SI EL EMPLEADO VIENE HOY EN ACCESO Y TIENE SEXTA
   function viene_hoy_y_tiene_sexta($numero_empleado)
   {
       global $diaactual;
       $nombre="biometric";
       $contra="5_w**/pQxcmk.";
       require("../Acceso/global.php");
       $sql1="select a.numero_trabajador,
       b.lunes,b.martes,b.miercoles,b.jueves,b.viernes,b.sabado,b.domingo,
       c.lunes,c.martes,c.miercoles,c.jueves,c.viernes,c.sabado,c.domingo,
       b.t_dias,c.t_dias,c.validez,d.entrada,b.idacceso,c.idsexta,d.salida
       from trabajador a 
       inner join acceso b on a.numero_trabajador=b.trabajador_trabajador
       inner join sexta c on a.numero_trabajador=c.trabajador_trabajador
       inner join turno d on b.turno_turno=d.idturno
       and b.$diaactual = 1
       and a.numero_trabajador='$numero_empleado'";
       $query1= mysqli_query($con, $sql1);
       $filas=mysqli_num_rows($query1);
       if($filas>0)
       {
           $resul=mysqli_fetch_array($query1);
           $num_empleado=$resul[0]; 
           $acceso_sexta=$resul[1].$resul[2].$resul[3].$resul[4].$resul[5].$resul[6].$resul[7].$resul[8].$resul[9].$resul[10].$resul[11].$resul[12].$resul[13].$resul[14];  
           $t_dias_acceso=$resul[15];
           $t_dias_sexta=$resul[16];
           $validez=$resul[17];
           $entrada=$resul[18];
           $idacceso=$resul[19];
           $idsexta=$resul[20];
           $salida=$resul[21];
           return[$num_empleado,$acceso_sexta,$t_dias_acceso,$t_dias_sexta,$validez,$entrada,$idacceso,$idsexta,$salida];
       }
       else
       {
           return null;
       }
   }//FIN SI EL EMPLEADO VIENE HOY EN ACCESO Y TIENE SEXTA
?>