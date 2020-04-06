<?php
    $local="localhost";
    $user="$nombre";
    $pws="$contra";
    $db="checada6";

    $con=mysqli_connect($local,$user,$pws,$db);
    if (!$con) 
    {
        die("Conexión Fallida. Detalles del error: " . mysqli_connect_error());
    }
?>