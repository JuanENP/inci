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
    $salida2="";
    $query2= "select numero_trabajador as Número, CONCAT(nombre, ' ', apellido_paterno, ' ', apellido_materno) as Nombre from trabajador";

    if(isset($_POST['consulta2']))
    {
        //escapar caracteres especiales
        $q2=$_POST['consulta2'];
        $query2= "select numero_trabajador, nombre, apellido_paterno, apellido_materno from trabajador
        where numero_trabajador like '%".$q2."%' or nombre like '%".$q2."%' or apellido_paterno like '%".$q2."%' or apellido_materno like '%".$q2."%'";
    }
    if($resultado2=mysqli_query($con, $query2))
    {
        $filas2=mysqli_num_rows($resultado2);
        if($filas2>0)
        {
            $salida2.="<select name='numSup' id='MainContent_DropDownListTrabajadores2' class='form-control' style='background-color: rgb(231, 237, 255); font-weight: bold; color: black;'>";
            while($resul2=mysqli_fetch_array($resultado2))
            {
                $salida2.="<option value='".$resul2[0]."' style='background-color: rgb(231, 237, 255); font-weight: bold; color: black;'>". $resul2[0] . " " . $resul2[1]. " " . $resul2[2] . " ". $resul2[3] . " " ."</option>";
            }
            $salida2.="</select>";
        }
        else
        {
            $salida2.="No hay datos";
        }
        echo $salida2;
        mysqli_close($con);
    }   
?>