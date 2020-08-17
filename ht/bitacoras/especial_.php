<?php
/*Aquí se encuentra el código para generar la tabla que se reutiliza 4 veces*/

    $tabla.="<table border=1 style='text-align: center;'>
    <thead>
        <tr>
        <th colspan='3'></th>
        <th colspan='7' style='background: #D03E3E;'>Actualizado/borrado</th>
        <th colspan='7' style='background: #D0D26E;'>Anterior</th>
        <th colspan='4'></th>
        </tr>
        
        <tr>
            <td>Usuario</td>
            <td>Host Origen</td>
            <td>Operación</td>
            <td>Fecha inicio</td>
            <td>Fecha fin</td>
            <td>Hora entrada</td>
            <td>Hora salida</td>
            <td>Clave especial</td>
            <td>Empresa</td>
            <td>Duración</td>
            <td>Fecha inicio</td>
            <td>Fecha fin</td>
            <td>Hora entrada</td>
            <td>Hora salida</td>
            <td>Clave especial</td>
            <td>Empresa</td>
            <td>Duración</td>
            <td>Trabajador</td>
            <td>Nombre máquina</td>
            <td>Doc. Respaldo</td>
            <td>Fecha</td>
        </tr>
    </thead>     
    <tbody>";
    while ($fila = mysqli_fetch_array($query)) 
    {
        //guardar el nombre de la imagen, que se encuentra en la posición 20 del array fila
        $img=$fila[20];
        require("_existeIMG.php");
        //meter la opción de ver la imagen, fila[8] es la clave especial
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
                    <td>".$fila[19]."</td>";
                    if($existe==1)
                    {
                        if($fila[8]=="55")
                        {
                            $idClave55=$fila[20];
                            $doctor=retornaAlgoDeBD(0, "SELECT empresa from especial where idespecial=$idClave55");
                            $tabla.="<td><a href='../documents/".$fila[20].$extension."' target='_blank'>ver Doctor: [$doctor]</a></td>";
                        }
                        else
                        {
                            $tabla.="<td><a href='../documents/".$fila[20].$extension."' target='_blank'>ver</a></td>";
                        }
                    }
                    else
                    {
                        $tabla.="<td>N/A</td>";
                    }
                    $tabla.="<td>".$fila[21]."</td>
                </tr>";
    }
    $tabla.="</tbody></table>";
    echo $tabla;
?>