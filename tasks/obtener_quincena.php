<?php
date_default_timezone_set('America/Mexico_City');
set_time_limit(600);//Indica que son 600 segundos, es decir 10 minutos máximo para ejecutar todo el script 
  $nombre="biometric";
  $contra="5_w**/pQxcmk.";
  require("../Acceso/global.php");
  require("contador_faltas_quincenal.php");
  $contador=0;//para moverse a las filas de arrayIni;
  $porcion_fecha_ini;
  $fecha_ini_formateada;
  $porcion_fecha_fin;
  $fecha_fin_formateada;
  $arrayIni = [];
  $fecha_de_hoy = date("Y-m-d" ) ; //sacamos la fecha de hoy
  $anio_actual = date("Y") ; //sacamos el año actual
  //obtener todos los datos de la tabla
  $sql="select * from quincena"; 
  $query= mysqli_query($con, $sql);
  if($query)
  {
    $filas=mysqli_num_rows($query);//obtener el número de filas
    if($filas==24)//24 quincenas
    {
      while($resul=mysqli_fetch_array($query))
      {
        $fecha_ini=$resul[1];//la fecha de inicio de la quincena
        $porcion_fecha_ini=explode("-",$fecha_ini);//genera un array de 3 posiciones: [0]=0000 [1]=12 [2]=25
        $fecha_ini_formateada=$anio_actual . "-" . $porcion_fecha_ini[1] . "-" . $porcion_fecha_ini[2];//agregar el año actual
        $fecha_fin=$resul[2];//la fecha de fin de la quincena
        $porcion_fecha_fin=explode("-",$fecha_fin);
        $fecha_fin_formateada=$anio_actual . "-" . $porcion_fecha_fin[1] . "-" . $porcion_fecha_fin[2];//agregar el año actual
        //Guardar en un arreglo para su posterior uso
        $arrayIni[$contador][0]=$resul[0];//el id
        $arrayIni[$contador][1]=$fecha_ini_formateada;//fecha inicio
        $arrayIni[$contador][2]=$fecha_fin_formateada;//fecha fin
        $arrayIni[$contador][3]=$resul[3];//validez
        $contador++;//aumentar para que los siguientes datos se guarden el la siguiente fila
      }
      //recorrer todas las filas de arrayIni. Son 24 filas SIEMPRE (24 quincenas)
      for($i=0;$i<24;$i++)
      {
        //Si la fecha actual está dentro del rango de la quincena
        if(($fecha_de_hoy>=$arrayIni[$i][1])&&($fecha_de_hoy<=$arrayIni[$i][2]))
        {
          $id=$arrayIni[$i][0];//guardar el id de la quincena actual
          $sql="select validez from quincena where idquincena = $id";
          $query= mysqli_query($con, $sql);
          $resul=mysqli_fetch_array($query);
          $filas=mysqli_num_rows($query);//obtener el número de filas
          if($filas==1)
          {
            $validez_actual = $resul[0];//guardar la validez de la quincena actual
            //si la validez de la quincena actual es 0
            if($validez_actual==0)
            {
              //guardar el id de la quincena que posee validez 1
              $sql="select idquincena from quincena where validez=1";
              $query= mysqli_query($con, $sql);
              $filas=mysqli_num_rows($query);//obtener el número de filas
              if($filas==1)
              {
                $resul=mysqli_fetch_array($query);
                $id_validez_1 = $resul[0];//el id de la quincena con validez 1
                //actualizar la validez a 0 de la quincena que tenía validez 1
                $sql="UPDATE quincena SET validez = 0 WHERE idquincena = $id_validez_1";
                $query= mysqli_query($con, $sql);
                if(!$query)
                {
                  $er1=mysqli_errno($con);
                  $er2=mysqli_error($con);
                  $línea='65';
                  errorQuincena($er1,$er2,$línea);
                }
                //actualizar la validez a 1 de la quincena que tenía validez 0, es decir, la quincena actual
                $sql="UPDATE quincena SET validez = 1 WHERE idquincena = $id";
                $query= mysqli_query($con, $sql);
                if(!$query)
                {
                  $er1=mysqli_errno($con);
                  $er2=mysqli_error($con);
                  $línea='75';
                  errorQuincena($er1,$er2,$línea);
                }
                //romper el bucle, pues ya no es necesario seguir comparando si ya se encontró en que quincena está la fecha actual
                $i=24;
              }
            }
          }
        }
      }
    }
  } 
  else
  {
    $er1=mysqli_errno($con);
    $er2=mysqli_error($con);
    $línea='17';
    errorQuincena($er1,$er2,$línea);
  }

  function errorQuincena($er1,$er2,$numLinea)
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
    $error="Error: . $err1 : $err2. Línea de error: $numLinea. Tarea obtener_quincena.";
    echo"<script> console.error('$error'); </script>";
    exit();
  }
?>