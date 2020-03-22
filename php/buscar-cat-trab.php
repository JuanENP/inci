<?php
    require("../Acceso/global.php");
    $salida="";
    $query= "select categoria as Categoría, CONCAT(nombre) as Categoría from trabajador";

    if(isset($_POST['consulta']))
    {
        //escapar caracteres especiales
        $q=$_POST['consulta'];
        $query= "select * from categoria
        where categoria like '".$q."%' or categoria like '%".$q."%'";
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
            $salida.="No existe esa categoria";
        }
        echo $salida;
        mysqli_close($con);
    }   
?>