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

    $eventNoComunes=$_POST['filtroComun'];//puede valer no (cuando no ha sido seleccionado) y si (cuando se seleccionó)
    $okFechas="";
    //para imprimir la tabla al final
    $tabla="";

    if(!empty($_POST['fini']))
    {
        //verificar si alguna de las fechas posee un formato incorrecto o no ha sido elegido
        if($_POST['fini']=="undefined/undefined/" || $_POST['ffin']=="undefined/undefined/")
        {
            $okFechas="no";
        }
        else
        {
            $okFechas="si";

            $finicio=$_POST['fini'];
            $porciones = explode("/", $finicio);
            $finicio=$porciones[2]."-".$porciones[1]."-".$porciones[0]." 00:00:00";

            $ffin=$_POST['ffin'];
            $porciones = explode("/", $ffin);
            $ffin=$porciones[2]."-".$porciones[1]."-".$porciones[0]." 23:59:59";
        }
    }
    else
    {
        $okFechas="no";
    }

    if(!empty($_POST['opcion']))
    {
        $opcion=$_POST['opcion'];
        //dependiendo de la opción se mostrará la bitácora correspondiente

        if($opcion=="acc")
        {
            require("bitacoras/acceso.php");
        }

        if($opcion=="cat")
        {
            require("bitacoras/categorias.php");
        }

        if($opcion=="cumple")
        {
            require("bitacoras/cumpleanos.php");
        }

        if($opcion=="depto")
        {
            require("bitacoras/depto.php");
        }

        if($opcion=="festivo")
        {
            require("bitacoras/festivo.php");
        }

        if($opcion=="especial")
        {
            require("bitacoras/especial.php");
        }

        if($opcion=="guard")
        {
            require("bitacoras/guardias.php");
        }

        //justificar-incidencias
        if($opcion=="just-in")
        {
            require("bitacoras/justIN.php");
        }

        //justificar-faltas
        if($opcion=="just-fal")
        {
            require("bitacoras/justFAL.php");
        }

        //pase de salida
        if($opcion=="ps")
        {
            require("bitacoras/ps.php");
        }

        if($opcion=="sexta")
        {
            require("bitacoras/sexta.php");
        }

        if($opcion=="tservicio")
        {
            require("bitacoras/tservicio.php");
        }

        /*trabajadores*/
        if($opcion=="trab")
        {
            require("bitacoras/trab.php");
        }

        if($opcion=="turno")
        {
            require("bitacoras/turno.php");
        }

        //vacaciones personal normal (que no sean de Radio)
        if($opcion=="vaca")
        {
            require("bitacoras/vacacionesN.php");
        }
    }
?>