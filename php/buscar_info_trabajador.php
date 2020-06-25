<?php
    function consultaTrabajador($myid)
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        require("../Acceso/global.php");
        $sql="select * from trabajador where numero_trabajador = '".$myid."'";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
            die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
        }
        else
        { 
            $resul=mysqli_fetch_array($query);
            //retornar este array
            return
            [
                $resul[0],$resul[1],$resul[2],$resul[3],$resul[4],$resul[5],$resul[6]
            ];
        }
    }

    function consultaCumple($myid)
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        require("../Acceso/global.php");
        //Consultar todo de la tabla cumpleaños de tal trabajador
        $sql="select * from cumple_ono where trabajador_trabajador = '".$myid."'";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
             die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
        }
        else
        { 
            $resul=mysqli_fetch_array($query);
            //retornar este array
            return
            [ $resul[1],$resul[2] ];
        }
    
    }

    function consultaAcceso($myid)
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        require("../Acceso/global.php");
        //Consultar todo de la tabla acceso
        $sql="select * from acceso where trabajador_trabajador = '".$myid."'";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
             die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
        }
        else
        { 
            $resul=mysqli_fetch_array($query);
            //retornar este array
            return
            [ $resul[1],$resul[2],$resul[3],$resul[4],$resul[5],$resul[6],$resul[7],$resul[8],$resul[9] ];
        }
    }

    function consultaTServicio($myid)
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        require("../Acceso/global.php");
        //Consultar todo de la tabla acceso
        $sql="select * from tiempo_servicio where trabajador_trabajador = '".$myid."'";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
             die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
        }
        else
        { 
            $resul=mysqli_fetch_array($query);
            //retornar este array
            return
            [ $resul[1] ];
        }
    }

    function consultaGenero($myid)
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        require("../Acceso/global.php");
        //Consultar todo de la tabla acceso
        $sql="select genero from trabajador where numero_trabajador = '".$myid."'";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
             die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
        }
        else
        { 
            $resul=mysqli_fetch_array($query);
            //retornar este array
            return
            [ $resul[0]];
            
        }
    }
?>