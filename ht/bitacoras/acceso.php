<?php
    if($eventNoComunes=="no" && $okFechas=="no")
    {
        //Mostrar TODA la bitácora
        $sql="SELECT * FROM bitacora_acceso";
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
                    <th colspan='9' style='background: #D03E3E;'>Actualizado/borrado</th>
                    <th colspan='9' style='background: #D0D26E;'>Anterior</th>
                    <th colspan='3'></th>
                    </tr>
                    
                    <tr>
                        <td>Usuario</td>
                        <td>Host Origen</td>
                        <td>Operación</td>
                        <td>Lunes</td>
                        <td>Martes</td>
                        <td>Miércoles</td>
                        <td>Jueves</td>
                        <td>Viernes</td>
                        <td>Sábado</td>
                        <td>Domingo</td>
                        <td>Días Festivo</td>
                        <td>Turno</td>
                        <td>Lunes</td>
                        <td>Martes</td>
                        <td>Miércoles</td>
                        <td>Jueves</td>
                        <td>Viernes</td>
                        <td>Sábado</td>
                        <td>Domingo</td>
                        <td>Días Festivo</td>
                        <td>Turno</td>
                        <td>Trabajador</td>
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
                                <td>".$fila[10]."</td>
                                <td>".$fila[11]."</td>
                                <td>".$fila[12]."</td>
                                <td>".$fila[13]."</td>
                                <td>".$fila[14]."</td>
                                <td>".$fila[15]."</td>
                                <td>".$fila[16]."</td>
                                <td>".$fila[17]."</td>
                                <td>".$fila[18]."</td>
                                <td>".$fila[19]."</td>
                                <td>".$fila[20]."</td>
                                <td>".$fila[21]."</td>
                                <td>".$fila[22]."</td>
                                <td>".$fila[23]."</td>
                                <td>".$fila[24]."</td>
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
        $sql="SELECT * FROM bitacora_acceso where fecha between '$finicio' AND '$ffin'";
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
                    <th colspan='9' style='background: #D03E3E;'>Actualizado/borrado</th>
                    <th colspan='9' style='background: #D0D26E;'>Anterior</th>
                    <th colspan='3'></th>
                    </tr>
                    
                    <tr>
                        <td>Usuario</td>
                        <td>Host Origen</td>
                        <td>Operación</td>
                        <td>Lunes</td>
                        <td>Martes</td>
                        <td>Miércoles</td>
                        <td>Jueves</td>
                        <td>Viernes</td>
                        <td>Sábado</td>
                        <td>Domingo</td>
                        <td>Días Festivo</td>
                        <td>Turno</td>
                        <td>Lunes</td>
                        <td>Martes</td>
                        <td>Miércoles</td>
                        <td>Jueves</td>
                        <td>Viernes</td>
                        <td>Sábado</td>
                        <td>Domingo</td>
                        <td>Días Festivo</td>
                        <td>Turno</td>
                        <td>Trabajador</td>
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
                                <td>".$fila[10]."</td>
                                <td>".$fila[11]."</td>
                                <td>".$fila[12]."</td>
                                <td>".$fila[13]."</td>
                                <td>".$fila[14]."</td>
                                <td>".$fila[15]."</td>
                                <td>".$fila[16]."</td>
                                <td>".$fila[17]."</td>
                                <td>".$fila[18]."</td>
                                <td>".$fila[19]."</td>
                                <td>".$fila[20]."</td>
                                <td>".$fila[21]."</td>
                                <td>".$fila[22]."</td>
                                <td>".$fila[23]."</td>
                                <td>".$fila[24]."</td>
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
        $sql="SELECT * FROM bitacora_acceso where operacion='Actualizado' or operacion='Eliminado'";
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
                    <th colspan='9' style='background: #D03E3E;'>Actualizado/borrado</th>
                    <th colspan='9' style='background: #D0D26E;'>Anterior</th>
                    <th colspan='3'></th>
                    </tr>
                    
                    <tr>
                        <td>Usuario</td>
                        <td>Host Origen</td>
                        <td>Operación</td>
                        <td>Lunes</td>
                        <td>Martes</td>
                        <td>Miércoles</td>
                        <td>Jueves</td>
                        <td>Viernes</td>
                        <td>Sábado</td>
                        <td>Domingo</td>
                        <td>Días Festivo</td>
                        <td>Turno</td>
                        <td>Lunes</td>
                        <td>Martes</td>
                        <td>Miércoles</td>
                        <td>Jueves</td>
                        <td>Viernes</td>
                        <td>Sábado</td>
                        <td>Domingo</td>
                        <td>Días Festivo</td>
                        <td>Turno</td>
                        <td>Trabajador</td>
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
                                <td>".$fila[10]."</td>
                                <td>".$fila[11]."</td>
                                <td>".$fila[12]."</td>
                                <td>".$fila[13]."</td>
                                <td>".$fila[14]."</td>
                                <td>".$fila[15]."</td>
                                <td>".$fila[16]."</td>
                                <td>".$fila[17]."</td>
                                <td>".$fila[18]."</td>
                                <td>".$fila[19]."</td>
                                <td>".$fila[20]."</td>
                                <td>".$fila[21]."</td>
                                <td>".$fila[22]."</td>
                                <td>".$fila[23]."</td>
                                <td>".$fila[24]."</td>
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
        $sql="SELECT * FROM bitacora_acceso where (operacion='Actualizado' or operacion='Eliminado')
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
                    <th colspan='9' style='background: #D03E3E;'>Actualizado/borrado</th>
                    <th colspan='9' style='background: #D0D26E;'>Anterior</th>
                    <th colspan='3'></th>
                    </tr>
                    
                    <tr>
                        <td>Usuario</td>
                        <td>Host Origen</td>
                        <td>Operación</td>
                        <td>Lunes</td>
                        <td>Martes</td>
                        <td>Miércoles</td>
                        <td>Jueves</td>
                        <td>Viernes</td>
                        <td>Sábado</td>
                        <td>Domingo</td>
                        <td>Días Festivo</td>
                        <td>Turno</td>
                        <td>Lunes</td>
                        <td>Martes</td>
                        <td>Miércoles</td>
                        <td>Jueves</td>
                        <td>Viernes</td>
                        <td>Sábado</td>
                        <td>Domingo</td>
                        <td>Días Festivo</td>
                        <td>Turno</td>
                        <td>Trabajador</td>
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
                                <td>".$fila[10]."</td>
                                <td>".$fila[11]."</td>
                                <td>".$fila[12]."</td>
                                <td>".$fila[13]."</td>
                                <td>".$fila[14]."</td>
                                <td>".$fila[15]."</td>
                                <td>".$fila[16]."</td>
                                <td>".$fila[17]."</td>
                                <td>".$fila[18]."</td>
                                <td>".$fila[19]."</td>
                                <td>".$fila[20]."</td>
                                <td>".$fila[21]."</td>
                                <td>".$fila[22]."</td>
                                <td>".$fila[23]."</td>
                                <td>".$fila[24]."</td>
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