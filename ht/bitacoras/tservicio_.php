<?php
/*Aquí se encuentra el código para generar la tabla que se reutiliza 4 veces*/

    $tabla.="<table border=1 style='text-align: center;'>
    <thead>
        <tr>
        <th colspan='3'></th>
        <th colspan='1' style='background: #D03E3E;'>Actualizado/borrado/Guardado</th>
        <th colspan='1' style='background: #D0D26E;'>Anterior</th>
        <th colspan='3'></th>
        </tr>
        
        <tr>
            <td>Usuario</td>
            <td>Host Origen</td>
            <td>Operación</td>
            <td>Fecha alta</td>
            <td>Fecha alta</td>
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
                    </tr>";
    }
    $tabla.="</tbody></table>";
    echo $tabla;
?>