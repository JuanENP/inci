<?php
session_start();
    //obtener el id que se mandó acá
    $id=$_GET['id'];
    //Función que busca la categoría con el ID
    function consulta($myid)
    {
        require("../Acceso/global.php");
        $sql="select * from idturno where idturno = '".$myid."'";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
          die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
        }
        else
        { 
          $resul=mysqli_fetch_array($query);
          //retornar este array
          return[
          $resul[0],$resul[1],$resul[2],$resul[3]
          ];
        }
    }
    //guardar el array que retornó la función consulta
    $id2=consulta($id);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Editar Turno</title>
    </head>

    <body>
        <form method="POST" action="../php/update/turno.php">
            <input type="hidden" name="old_id" value="<?php echo $id2[0]?>">
            <label for="">Turno</label>
            <input type="text" id="id-cat" name="idcat" value="<?php echo $id2[1]?>">
            <label for="">Hora entrada</label>
            <input type="text" id="nom-cat" name="nomcat" value="<?php echo $id2[2]?>">
            <label for="">Hora salida</label>
            <input type="text" id="nom-cat2" name="nomcat2" value="<?php echo $id2[3]?>">
            <button type="submit" class="btn btn-success">Guardar</button>
        </form>
    </body>
    </html>

    