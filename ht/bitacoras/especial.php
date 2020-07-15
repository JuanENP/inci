<?php
    if($eventNoComunes=="no" && $okFechas=="no")
    {
        //Mostrar TODA la bitácora
        $sql="SELECT * FROM bitacora_especial";
        $query=mysqli_query($con, $sql);
        if(!$query)
        {
            echo "Error al recuperar los datos de la bitácora.";
        }
        else
        {
            if(mysqli_num_rows($query)>0)
            {
                require("especial_.php");
            }//Fin if num rows>0
            else
            {
                echo "No hay datos";
            }
        }
        exit();//evitar seguir con los demás if
    }//fin if eventos comunes=no fechas=no
        
    if($eventNoComunes=="no" && $okFechas=="si")
    {
        //Mostrar TODA la bitácora
        $sql="SELECT * FROM bitacora_especial where fecha between '$finicio' AND '$ffin'";
        $query=mysqli_query($con, $sql);
        if(!$query)
        {
            echo "Error al recuperar los datos de la bitácora.";
        }
        else
        {
            if(mysqli_num_rows($query)>0)
            {
                require("especial_.php");
            }//Fin if num rows>0
            else
            {
                echo "No hay datos con ese rango de fechas";
            }
        }
        exit();
    }//fin if eventos comunes=no fechas=si
        
    if($eventNoComunes=="si" && $okFechas=="no")
    {
        $sql="SELECT * FROM bitacora_especial where operacion='Actualizado' or operacion='Eliminado' or usuario='AdministradorGod'";
        $query=mysqli_query($con, $sql);
        if(!$query)
        {
            echo "Error al recuperar los datos de la bitácora.";
        }
        else
        {
            if(mysqli_num_rows($query)>0)
            {
                require("especial_.php");
            }//Fin if num rows>0
            else
            {
                echo "No hay datos (eventos comunes)";
            }
        }
        exit();
    }//fin if eventos comunes=si fechas=no
        
    if($eventNoComunes=="si" && $okFechas=="si")
    {
        $sql="SELECT * FROM bitacora_especial where (operacion='Actualizado' or operacion='Eliminado')
        and (fecha between '$finicio' AND '$ffin')";
        $query=mysqli_query($con, $sql);
        if(!$query)
        {
            echo "Error al recuperar los datos de la bitácora.";
        }
        else
        {
            if(mysqli_num_rows($query)>0)
            {
                require("especial_.php");
            }//Fin if num rows>0
            else
            {
                echo "No hay datos (eventos comunes) con ese rango de fechas";
            }
        }
        exit();
    }//fin if eventos comunes=si fechas=si
?>