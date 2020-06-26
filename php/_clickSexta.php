<?php
session_start();
    if (($_SESSION["name"]) && ($_SESSION["con"]))
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con']; 
        require("../Acceso/global.php");
    }
    else
    {
        header("Location: ../index.html");
        die();
    }
    $valorTurno=$_POST['consulta'];
    $numero=$_POST['consulta2'];

    $salida="";
    $separa=explode(' ',$valorTurno);
    $t_horas=$separa[1];
    $sexta=consultaSexta($numero);  //revisar si el trabajador tiene sexta
    $semana = array('lunes','martes','miercoles','jueves','viernes','sabado','domingo','dias_festivos');//campos de la bd
    $semana2 = array('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo','Días festivos'); //dias de la semana

        if(($t_horas=="06:00:00")||($t_horas=="06:30:00"))
        {
            if($sexta!==null)
            {
                $salida.="<span> Días de sexta </span><br>";
                for ($i=0;$i<8;$i++)
                {
                    if($sexta[$i]==1)
                    {
                        $salida.="<input type='checkbox' name='diaS[]' id='$semana[$i]' value='$semana[$i]' checked/><label for='$semana[$i]'> $semana2[$i]</label><br/>";  
                    }
                    else
                    {
                        $salida.="<input type='checkbox' name='diaS[]'  id='$semana[$i]' value='$semana[$i]'/><label for='$semana[$i]'> $semana2[$i]</label><br/>";
                    }  
                }
                echo $salida;
            }
            else
            {
                $salida.="<span> Días de sexta</span><br>";
                $salida.="<input type='checkbox' name='diaS[]' value='lunes'id='lun'/> <label for='lun'>Lunes</label><br>";
                $salida.="<input type='checkbox' name='diaS[]' value='martes' id='mar'/> <label for='mar'>Martes</label><br>";
                $salida.="<input type='checkbox' name='diaS[]' value='miercoles'id='mie'/> <label for='mie'>Miércoles</label><br>";
                $salida.="<input type='checkbox' name='diaS[]' value='jueves' id='jue'/> <label for='jue'>Jueves</label><br>";
                $salida.="<input type='checkbox' name='diaS[]' value='viernes'id='vie'/> <label for='vie'>Viernes</label><br>";
                $salida.="<input type='checkbox' name='diaS[]' value='sabado' id='sab'/> <label for='sab'>Sábado</label><br>";
                $salida.="<input type='checkbox' name='diaS[]' value='domingo' id='dom'/> <label for='dom'>Domingo</label><br>";
                $salida.="<input type='checkbox' name='diaS[]' value='dias_festivos' id='dfe'/> <label for='dfe'>Días festivos</label><br>";
                echo $salida;
            }
        }
        else
        {

            $salida='';
        
        }
    
    
    function consultaSexta($myid)
    {
        global $con;
        //Consultar todo de la tabla acceso
        $sql="select idsexta, lunes, martes, miercoles, jueves, viernes, sabado,domingo, dia_festivo from sexta where trabajador_trabajador = '$myid'";
        if($query=mysqli_query($con, $sql))
        {
            $filas=mysqli_num_rows($query);
            if($filas>0)
            {
                $resul=mysqli_fetch_array($query);
                return
                [ $resul[0],$resul[1],$resul[2],$resul[3],$resul[4],$resul[5],$resul[6],$resul[7],$resul[8]];
            }
            else
            {
                return null;
            }
        }
        else
        {
            return null;
        }
    }

?>