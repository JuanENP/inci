<?php
$local="localhost";
$user="root";
$pws="";
$db="checada5";

$con=mysqli_connect($local,$user,$pws,$db);
if (!$con) 
    {
        die("Conexión Fallida. Detalles del error: " . mysqli_connect_error());
    }
?>