<?php
    $local="localhost";
    $user="$nombre";
    $pws="$contra";
    $db="checada6";

    $con=mysqli_connect($local,$user,$pws,$db);
    if (!$con) 
    {
        echo"<script language= javascript type= text/javascript>alert('Faltan datos para iniciar la sesión o su Usuario y Contraseña son incorrectos.');location.href='../index.html';</script>";
        die();
    }
?>