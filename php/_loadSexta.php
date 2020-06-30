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

    $numero=$_POST['consulta'];//numero del empleado seleccionado
    $semana = array('lunes','martes','miercoles','jueves','viernes','sabado','domingo','dias_festivos');//campos de la bd
    $semana2 = array('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo','Días festivos'); //dias de la semana
    $sexta=consultaSexta($numero);  
    if($sexta!==null)
    {
        $salida="";
        $salida.="<span> Días de sexta </span><br>";
        for ($i=0;$i<8;$i++)
        {
            if($sexta[$i]==1)
            {
                $salida.="<input type='checkbox' name='diaS[]' id='$semana[$i].'s'.' value='$semana[$i]' checked/> <label for='$semana[$i].'s'.'> $semana2[$i]</label><br/>";  
            }
            else
            {
                $salida.="<input type='checkbox' name='diaS[]'  id='$semana[$i].'s'.' value='$semana[$i]'/> <label for='$semana[$i].'s'.'> $semana2[$i]</label><br/>";
            }  
        }
        echo $salida;
    }
    else
    {
        echo "";
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