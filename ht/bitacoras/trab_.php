<?php
/*Aquí se encuentra el coódigo para generar la tabla que se reutiliza 4 veces*/

    $tabla.="<table border=1 style='text-align: center;'>
    <thead>
        <tr>
        <th colspan='3'></th>
        <th colspan='8' style='background: #D03E3E;'>Actualizado/borrado/Guardado</th>
        <th colspan='8' style='background: #D0D26E;'>Anterior</th>
        <th colspan='2'></th>
        </tr>
        
        <tr>
            <td>Usuario</td>
            <td>Host Origen</td>
            <td>Operación</td>
            <td>Trabajador</td>
            <td>Nombre</td>
            <td>A. Paterno</td>
            <td>A. Materno</td>
            <td>Depto.</td>
            <td>Categoría</td>
            <td>Tipo empleado</td>
            <td>Género</td>
            <td>Trabajador</td>
            <td>Nombre</td>
            <td>A. Paterno</td>
            <td>A. Materno</td>
            <td>Depto.</td>
            <td>Categoría</td>
            <td>Tipo empleado</td>
            <td>Género</td>
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
                </tr>";
    }
    $tabla.="</tbody></table>";
    echo $tabla;
?>