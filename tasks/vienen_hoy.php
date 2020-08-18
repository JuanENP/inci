<?php
    date_default_timezone_set('America/Mexico_City'); 
    set_time_limit(600);//Indica que son 600 segundos, es decir 10 minutos máximo para ejecutar todo el script
    $deben_hoy_ultimo=0;
    $deben_hoy=[];
    $observar_e=-1;//-1 Significa que el empleado debe venir
    $observar_s=-1;
    $arregloPendientes=array();//arreglo para guardar a los pendientes

    //OBTENER QUE DÍA ES HOY
    $dias = array("domingo","lunes","martes","miercoles","jueves","viernes","sabado");
    //echo "HOY ES ".$dias[date("w")] . "<br>";//esto es solo para ver si el día es correcto
    $diaactual=$dias[date("w")];//guardar el día actual para su posterior uso
    // $diaactual='sabado';
    $f_hoy=date("Y-m-d");//guardar la fecha actual
    $nombre="biometric";
    $contra="5_w**/pQxcmk.";
    require("../Acceso/global.php");

    verSiHoyEsDiaFestivo();
    function verSiHoyEsDiaFestivo()
    {
        global $con;
        global $f_hoy;
        global $diaactual;
        global $deben_hoy;
        global $deben_hoy_ultimo;                   
        global $observar_e;
        global $observar_s;
        require('funciones_vienen_hoy.php');
        //Ver si el día de hoy está registrado en la tabla día festivo
        $sql="Select iddia_festivo from dia_festivo where fecha='$f_hoy';";
        $query= mysqli_query($con, $sql);
        $fila=mysqli_num_rows($query);
        if($fila==1) //Si hoy es día festivo
        {
            //Seleccionar a todos los que trabajan en día festivo
            $sql="select t.numero_trabajador,c.entrada,c.salida,c.t_horas from trabajador t 
            inner join acceso a on a.trabajador_trabajador = t.numero_trabajador 
            inner join turno c on c.idturno=a.turno_turno 
            and dia_festivo=1 and a.t_dias=-1;";
            $query= mysqli_query($con, $sql);
            $fila=mysqli_num_rows($query);
            if($fila>0)
            {
                while($resul=mysqli_fetch_array($query))
                {
                    $numero=$resul[0];
                    $t_horas=$resul[3];
                    $DiasAcceso=verDiasAcceso($numero);
                    if($diaactual=='lunes')
                    {
                        //Revisar si no trabaja domingo y festivo (00000011). 
                        if($DiasAcceso !== '00000011')
                        {
                            //Revisar si trabaja sábado, domingo y festivo  existe en la tabla af, obtener su id (idaf)
                            $idaf=existeEnAF($numero);
                            if($idaf !== null)
                            {  
                                //Actualizar a 1 domingo de la tabla af
                                actualizaAFa1('domingo',$idaf);
                            }
                            //Guardar en vienen hoy
                            $deben_hoy[$deben_hoy_ultimo][0]=$resul[0];    
                            $deben_hoy[$deben_hoy_ultimo][1]=$resul[1]; 
                            $deben_hoy[$deben_hoy_ultimo][2]=$resul[2]; 
                            $deben_hoy[$deben_hoy_ultimo][3]=$observar_e;
                            $deben_hoy[$deben_hoy_ultimo][4]=$observar_s;
                            $deben_hoy_ultimo++;
                        }
                    }
                    else
                    {
                        if($diaactual=='viernes')
                        { 
                            //Revisar si no trabaja sábado y festivo (00000101). 
                            if($DiasAcceso !== '00000101')
                            {                            
                                //Revisar si trabaja sábado, domingo y festivo  existe en la tabla af, obtener su id (idaf)
                                $idaf=existeEnAF($numero);
                                if($idaf !== null)
                                {  
                                    //Guardar 1 en sábado de la tabla af.
                                    actualizaAFa1('sabado',$idaf);
                                }
                                //Guardar en vienen hoy
                                $deben_hoy[$deben_hoy_ultimo][0]=$resul[0];    
                                $deben_hoy[$deben_hoy_ultimo][1]=$resul[1]; 
                                $deben_hoy[$deben_hoy_ultimo][2]=$resul[2]; 
                                $deben_hoy[$deben_hoy_ultimo][3]=$observar_e;
                                $deben_hoy[$deben_hoy_ultimo][4]=$observar_s;
                                $deben_hoy_ultimo++;
                            }
                        }
                        else
                        {
                            //Revisar si trabaja sábado, domingo y festivo  existe en la tabla af, obtener su id (idaf)
                            $idaf=existeEnAF($numero);
                            if($idaf !== null)
                            {
                                //Actualizar a 0 en sábado de la tabla af.
                                actualizaAFa0('sabado',$idaf);
                                actualizaAFa0('domingo',$idaf);
                            }
                            if($diaactual=='sabado')
                            {
                                // Revisar si no trabaja domingo y festivo (00000011).
                                if($DiasAcceso !== '00000011')
                                {
                                    //Guardar en vienen hoy
                                    $deben_hoy[$deben_hoy_ultimo][0]=$resul[0];    
                                    $deben_hoy[$deben_hoy_ultimo][1]=$resul[1]; 
                                    $deben_hoy[$deben_hoy_ultimo][2]=$resul[2]; 
                                    $deben_hoy[$deben_hoy_ultimo][3]=$observar_e;
                                    $deben_hoy[$deben_hoy_ultimo][4]=$observar_s;
                                    $deben_hoy_ultimo++;
                                }   
                            }
                            else
                            {
                                if($diaactual=='domingo')
                                {
                                    // Revisar si no trabaja sábado y festivo (00000101).
                                    if($DiasAcceso !== '00000101')
                                    {
                                        //Guardar en vienen hoy
                                        $deben_hoy[$deben_hoy_ultimo][0]=$resul[0];    
                                        $deben_hoy[$deben_hoy_ultimo][1]=$resul[1]; 
                                        $deben_hoy[$deben_hoy_ultimo][2]=$resul[2]; 
                                        $deben_hoy[$deben_hoy_ultimo][3]=$observar_e;
                                        $deben_hoy[$deben_hoy_ultimo][4]=$observar_s;
                                        $deben_hoy_ultimo++;
                                    }   
                                }
                                else
                                {
                                    //Guardar en vienen hoy
                                    $deben_hoy[$deben_hoy_ultimo][0]=$resul[0];    
                                    $deben_hoy[$deben_hoy_ultimo][1]=$resul[1]; 
                                    $deben_hoy[$deben_hoy_ultimo][2]=$resul[2]; 
                                    $deben_hoy[$deben_hoy_ultimo][3]=$observar_e;
                                    $deben_hoy[$deben_hoy_ultimo][4]=$observar_s;
                                    $deben_hoy_ultimo++;
                                }
                            }
                        }
                    }
                }
                checar_todo();
                guarda_deben_hoy();
            }
        }
        else //Sino es día festivo
        {
            orden_acceso_sexta();
            vienen_hoy();
            vienen_hoy_si_trabajan_sabado_domingo_festivo();
            checar_todo();
            guarda_deben_hoy();
        }
    }
?>