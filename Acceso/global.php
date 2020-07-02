<?php
    $local="localhost";
    $user="$nombre";
    $pws="$contra";
    $db="checada6";

    $con=mysqli_connect($local,$user,$pws,$db);
    if (!$con) 
    {
        $error=mysqli_connect_error();

        $divide=explode("'",$error);//separar el error por caracter '
        $tamDivide=count($divide);//saber el tamaño del array
        if($tamDivide>0)//si el array posee datos
        {
            $error="";
            for($i=0;$i<$tamDivide;$i++)
            {
                $error.=$divide[$i];//juntar los fragmentos del error pero ya sin el caracter '
            }
        }
        //mostrar el error en un alert
        echo "<script language= javascript type= text/javascript>alert('Error al conectar: $error'); history.back(); </script>";
        die();
    }
?>