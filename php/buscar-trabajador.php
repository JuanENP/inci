<?php
    require("../Acceso/global.php");
    $salida="";
    $query= "select numero_trabajador as Número, CONCAT(nombre, ' ', apellido_paterno, ' ', apellido_materno) as Nombre from trabajador";

    if(isset($_POST['consulta']))
    {
        //escapar caracteres especiales
        $q=$_POST['consulta'];
        $query= "select numero_trabajador, nombre, apellido_paterno, apellido_materno from trabajador
        where numero_trabajador like '%".$q."%' or nombre like '%".$q."%' or apellido_paterno like '%".$q."%' or apellido_materno like '%".$q."%'";
    }
    if($resultado=mysqli_query($con, $query))
    {
        $filas=mysqli_num_rows($resultado);
        if($filas>0)
        {
            $salida.="<select name='sel-trab' id='MainContent_DropDownListTrabajadores' class='form-control select2'>";
            while($resul=mysqli_fetch_array($resultado))
            {
                $salida.="<option value='".$resul[0]."'>". $resul[0] . " " . utf8_encode($resul[1]). " " . utf8_encode($resul[2]) . " ". utf8_encode($resul[3]) . " " ."</option>";
            }
            $salida.="</select>";
        }
        else
        {
            $salida.="No hay datos";
        }
        echo $salida;
        mysqli_close($con);
    }   
?>