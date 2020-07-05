<?php
session_start();
    if (($_SESSION["name"]) && ($_SESSION["con"]))
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con']; 
        require("../Acceso/global.php");

        if($nombre!="AdministradorGod")
        {
            echo "No posee permisos para esta sección.";
            exit();
        }
    }
    else
    {
        header("Location: ../index.php");
        die();
    }

    if(!empty($_POST['opcion']))
    {
        $opcion=$_POST['opcion'];
        //para imprimir la tabla al final
        $tabla="";

        //acceso
        if($opcion="acc")
        {
            $query="";
            exit();
        }

        //
    }
?>