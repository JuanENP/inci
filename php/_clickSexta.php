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
        header("Location: ../index.php");
        die();
    }
    $valorTurno=$_POST['consulta'];
    $numero=$_POST['consulta2'];
    $tipo=$_POST['consulta3'];

    $salida="";
    $separa=explode(' ',$valorTurno);
    $t_horas=$separa[1];

    $sexta=consultaSexta($numero);  //revisar si el trabajador tiene sexta
    $semana = array('lunes','martes','miercoles','jueves','viernes','sabado','domingo','dias_festivos');//campos de la bd
    $semana2 = array('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo','Días festivos'); //dias de la semana
        
    //Si el tipo de empleado es de base, es decir el #2, podrá tener sexta. 
    //Si el trabajador es de tipo #4 o comisionado foráneo también podrá tener sexta

    if(($t_horas!=="06:00:00") && ($t_horas!=="06:30:00")) 
    {
        echo "";
    }
    else
    {
        if((($t_horas=="06:00:00")||($t_horas=="06:30:00")) && ($sexta !== null) && ($tipo==2 || $tipo==4))
        {
            $salida.="<span> Semana 2 de trabajo</span><br>";
            for ($i=0;$i<7;$i++)
            {
                if($sexta[$i]==1)
                {
                    $salida.="<input type='checkbox' name='diaS[]' id='$semana[$i]' value='$semana[$i].'s'.' checked/> <label for='$semana[$i].'s'.'> $semana2[$i]</label><br/>";  //.'s'. sirve para diferenciar el id del día de acceso con el de sexta y que no se seleccionen los dias de acceso en lugar de los de sexta
                }
                else
                {
                    $salida.="<input type='checkbox' name='diaS[]'  id='$semana[$i]' value='$semana[$i].'s'.'/> <label for='$semana[$i].'s'.'> $semana2[$i]</label><br/>";
                }  
            }
            echo $salida;
        }
        else
        {
            if($tipo==2 || $tipo==4)
            {
                $salida.="<span> Semana 2 de trabajo</span><br>";
                $salida.="<input type='checkbox' name='diaS[]' value='lunes'id='lun'/> <label for='lun'> Lunes</label><br>";
                $salida.="<input type='checkbox' name='diaS[]' value='martes' id='mar'/> <label for='mar'> Martes</label><br>";
                $salida.="<input type='checkbox' name='diaS[]' value='miercoles'id='mie'/> <label for='mie'> Miércoles</label><br>";
                $salida.="<input type='checkbox' name='diaS[]' value='jueves' id='jue'/> <label for='jue'> Jueves</label><br>";
                $salida.="<input type='checkbox' name='diaS[]' value='viernes'id='vie'/> <label for='vie'> Viernes</label><br>";
                $salida.="<input type='checkbox' name='diaS[]' value='sabado' id='sab'/> <label for='sab'> Sábado</label><br>";
                $salida.="<input type='checkbox' name='diaS[]' value='domingo' id='dom'/> <label for='dom'> Domingo</label><br>";
                // $salida.="<input type='checkbox' name='diaS[]' value='dias_festivos' id='dfe'/> <label for='dfe'> Días festivos</label><br>";
                echo $salida;
            }
            else
            {
                echo "";
            }
        }
    }
    
    
    function consultaSexta($myid)
    {
        global $con;
        //Consultar todo de la tabla acceso
        $sql="select lunes, martes, miercoles, jueves, viernes, sabado,domingo, dia_festivo from sexta where trabajador_trabajador = '$myid'";
        $query=mysqli_query($con, $sql);
        $filas=mysqli_num_rows($query);
        if($filas==1)
        {
            $resul=mysqli_fetch_array($query);
            return
            [ $resul[0],$resul[1],$resul[2],$resul[3],$resul[4],$resul[5],$resul[6],$resul[7]];
        }
        else
        {
            return null;
        }

    }

?>