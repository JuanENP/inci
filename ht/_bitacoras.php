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
            $sql="SELECT * FROM bitacora_acceso";
            $query=mysqli_query($con, $sql);
            if(!$query)
            {
              echo "error";
            }
            else
            {
                if(mysqli_num_rows($query)>0)
                {
                    $tabla.="<table border=1>
                    <thead>
                        <tr>
                            <td>Usuario</td>
                            <td>Host Origen</td>
                            <td>Operación</td>
                            <td>Lunes</td>
                            <td>Martes</td>
                        </tr>
                    </thead>     
                    <tbody>";
            
                    while ($fila = mysqli_fetch_array($query)) 
                    {
                        $tabla.="<tr>
                                    <td>".$fila[1]."</td>
                                    <td>".$fila[2]."</td>
                                    <td>".$fila[3]."</td>
                                    <td>".$fila[4]."</td>
                                    <td>".$fila[5]."</td>
                                </tr>";
                    }
                    $tabla.="</tbody></table>";
                    echo $tabla;
                }//Fin if num rows>0
                else
                {
                    echo "No hay datos";
                }

            }
            exit();
        }

        //
    }
?>