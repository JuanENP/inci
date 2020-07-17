<?php
    if($eventNoComunes=="no" && $okFechas=="no")
    {
        //Mostrar TODA la bitácora
        $sql="SELECT * FROM bitacora_categoria";
        $query=mysqli_query($con, $sql);
        if(!$query)
        {
            echo "Error al recuperar los datos de la bitácora.";
        }
        else
        {
            if(mysqli_num_rows($query)>0)
            {
                $tabla.="<table border=1 style='text-align: center;'>
                <thead>
                    <tr>
                    <th colspan='3'></th>
                    <th colspan='2' style='background: #D03E3E;'>Actualizado/borrado</th>
                    <th colspan='2' style='background: #D0D26E;'>Anterior</th>
                    <th colspan='2'></th>
                    </tr>
                    
                    <tr>
                        <td>Usuario</td>
                        <td>Host Origen</td>
                        <td>Operación</td>
                        <td>id Categoría</td>
                        <td>Nombre</td>
                        <td>id Categoría</td>
                        <td>Nombre</td>
                        <td>Nombre máquina</td>
                        <td>Fecha</td>
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
                                <td>".$fila[6]."</td>
                                <td>".$fila[7]."</td>
                                <td>".$fila[8]."</td>
                                <td>".$fila[9]."</td>
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
        exit();//evitar seguir con los demás if
    }//fin if eventos comunes=no fechas=no
        
    if($eventNoComunes=="no" && $okFechas=="si")
    {
        //Mostrar TODA la bitácora
        $sql="SELECT * FROM bitacora_categoria where fecha between '$finicio' AND '$ffin'";
        $query=mysqli_query($con, $sql);
        if(!$query)
        {
            echo "Error al recuperar los datos de la bitácora.";
        }
        else
        {
            if(mysqli_num_rows($query)>0)
            {
                $tabla.="<table border=1 style='text-align: center;'>
                <thead>
                    <tr>
                    <th colspan='3'></th>
                    <th colspan='2' style='background: #D03E3E;'>Actualizado/borrado</th>
                    <th colspan='2' style='background: #D0D26E;'>Anterior</th>
                    <th colspan='2'></th>
                    </tr>
                    
                    <tr>
                        <td>Usuario</td>
                        <td>Host Origen</td>
                        <td>Operación</td>
                        <td>id Categoría</td>
                        <td>Nombre</td>
                        <td>id Categoría</td>
                        <td>Nombre</td>
                        <td>Nombre máquina</td>
                        <td>Fecha</td>
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
                                <td>".$fila[6]."</td>
                                <td>".$fila[7]."</td>
                                <td>".$fila[8]."</td>
                                <td>".$fila[9]."</td>
                            </tr>";
                }
                $tabla.="</tbody></table>";
                echo $tabla;
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
        $sql="SELECT * FROM bitacora_categoria where operacion='Actualizado' or operacion='Eliminado'";
        $query=mysqli_query($con, $sql);
        if(!$query)
        {
            echo "Error al recuperar los datos de la bitácora.";
        }
        else
        {
            if(mysqli_num_rows($query)>0)
            {
                $tabla.="<table border=1 style='text-align: center;'>
                <thead>
                    <tr>
                    <th colspan='3'></th>
                    <th colspan='2' style='background: #D03E3E;'>Actualizado/borrado</th>
                    <th colspan='2' style='background: #D0D26E;'>Anterior</th>
                    <th colspan='2'></th>
                    </tr>
                    
                    <tr>
                        <td>Usuario</td>
                        <td>Host Origen</td>
                        <td>Operación</td>
                        <td>id Categoría</td>
                        <td>Nombre</td>
                        <td>id Categoría</td>
                        <td>Nombre</td>
                        <td>Nombre máquina</td>
                        <td>Fecha</td>
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
                                <td>".$fila[6]."</td>
                                <td>".$fila[7]."</td>
                                <td>".$fila[8]."</td>
                                <td>".$fila[9]."</td>
                            </tr>";
                }
                $tabla.="</tbody></table>";
                echo $tabla;
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
        $sql="SELECT * FROM bitacora_categoria where (operacion='Actualizado' or operacion='Eliminado')
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
                $tabla.="<table border=1 style='text-align: center;'>
                <thead>
                    <tr>
                    <th colspan='3'></th>
                    <th colspan='2' style='background: #D03E3E;'>Actualizado/borrado</th>
                    <th colspan='2' style='background: #D0D26E;'>Anterior</th>
                    <th colspan='2'></th>
                    </tr>
                    
                    <tr>
                        <td>Usuario</td>
                        <td>Host Origen</td>
                        <td>Operación</td>
                        <td>id Categoría</td>
                        <td>Nombre</td>
                        <td>id Categoría</td>
                        <td>Nombre</td>
                        <td>Nombre máquina</td>
                        <td>Fecha</td>
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
                                <td>".$fila[6]."</td>
                                <td>".$fila[7]."</td>
                                <td>".$fila[8]."</td>
                                <td>".$fila[9]."</td>
                            </tr>";
                }
                $tabla.="</tbody></table>";
                echo $tabla;
            }//Fin if num rows>0
            else
            {
                echo "No hay datos (eventos comunes) con ese rango de fechas";
            }
        }
        exit();
    }//fin if eventos comunes=si fechas=si
?>