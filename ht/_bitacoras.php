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

    function retornaAlgoDeBD($tipoDatoADevolver, $elQuery)
    {
        global $con;
        /* 
            $elQuery             : es la consulta que se desea ejecutar 
            $tipoDatoADevolver=0 : Devolverá 1 solo dato de la consulta dada
            $tipoDatoADevolver=1 : Devolverá 1 array de la consulta dada

            Ejemplo de uso
            $devuelve=retornaAlgoDeBD(0, $sql)
        */
        $sql=$elQuery;
        if($tipoDatoADevolver==0)
        {
            $query=mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
            $filas=mysqli_num_rows($query);
            if($filas==1)
            {
                $resul=mysqli_fetch_array($query);
                return $resul[0];//Devolver un solo dato
            }
            else
            {
                echo "Esta consulta arrojó un conjunto vacío o un array. Verifique con el administrador del sistema para obtener más información. No es posible proceder.";
                exit();
            }  
        }
        else//fin if devolver ==0
        {
            if($tipoDatoADevolver==1)
            {
                $datos=array();//para guardar los datos
                $pos=0;//para controlar las posiciones del array
                $query=mysqli_query($con, $sql) or die("<br>" . "Error: " . utf8_encode(mysqli_errno($con)) . " : " . utf8_encode(mysqli_error($con)));
                $filas=mysqli_num_rows($query);
                if($filas>0)
                {
                    while($resul=mysqli_fetch_array($query))
                    { 
                        $datos[$pos]=$resul[0];//Guardar el día feriado correspondiente en el array
                        $pos++;//aumentar la posición del array
                    }
                    return $datos;//devolver un array con los datos
                }  
                else
                {
                    echo "Esta consulta arrojó un conjunto vacío. Verifique con el administrador del sistema para obtener más información. No es posible proceder.";
                    exit();
                }
            }
            else//fin if devolver==1
            {
                echo "Parametro *tipoDatoADevolver=$tipoDatoADevolver* de la función retornaAlgoDeBD no admitido";
                exit();
            }
        } 
    }//fin de retornaAlgoBD
?>