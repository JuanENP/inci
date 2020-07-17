<?php
/*Aquí se encuentra el código para generar la tabla que se reutiliza 4 veces*/

    $tabla.="<table border=1 style='text-align: center;'>
    <thead>
        <tr>
        <th colspan='3'></th>
        <th colspan='7' style='background: #D03E3E;'>Actualizado/borrado</th>
        <th colspan='7' style='background: #D0D26E;'>Anterior</th>
        <th colspan='3'></th>
        </tr>
        
        <tr>
            <td>Usuario</td>
            <td>Host Origen</td>
            <td>Operación</td>
            <td>Fecha registro</td>
            <td>Fecha guardia</td>
            <td>Solicitante</td>
            <td>Suplente</td>
            <td>H. Entrada</td>
            <td>H. Salida</td>
            <td>Quincena</td>
            <td>Fecha registro</td>
            <td>Fecha guardia</td>
            <td>Solicitante</td>
            <td>Suplente</td>
            <td>H. Entrada</td>
            <td>H. Salida</td>
            <td>Quincena</td>
            <td>Nombre máquina</td>
            <td>Fecha</td>
            <td>Doc. Respaldo</td>
        </tr>
    </thead>     
    <tbody>";
    while ($fila = mysqli_fetch_array($query)) 
    {
        //guardar el nombre de la imagen, que se encuentra en la posición 20 del array fila
        $img="g".$fila[4];//g de guardias concatenando el id nuevo
        require("_existeIMG.php");
        //meter la opción de ver la imagen
        $tabla.="<tr>
                    <td>".$fila[1]."</td>
                    <td>".$fila[2]."</td>
                    <td>".$fila[3]."</td>
                    <td>".$fila[5]."</td>
                    <td>".$fila[6]."</td>
                    <td>".$fila[7]."</td>
                    <td>".$fila[8]."</td>
                    <td>".$fila[9]."</td>
                    <td>".$fila[10]."</td>
                    <td>".$fila[11]."</td>
                    <td>".$fila[13]."</td>
                    <td>".$fila[14]."</td>
                    <td>".$fila[15]."</td>
                    <td>".$fila[16]."</td>
                    <td>".$fila[17]."</td>
                    <td>".$fila[18]."</td>
                    <td>".$fila[19]."</td>
                    <td>".$fila[20]."</td>
                    <td>".$fila[21]."</td>";
                    if($existe==1)
                    {
                        $tabla.="<td><a href='../../documents/".$fila[4].$extension."' target='_blank'>ver</a></td>";
                    }
                    else
                    {
                        $tabla.="<td>N/A</td>";
                    }
                $tabla.="</tr>";
    }
    $tabla.="</tbody></table>";
    echo $tabla;
?>