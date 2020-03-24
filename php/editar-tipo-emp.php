<?php
session_start();
    //obtener el id que se mandó acá
    $id=$_GET['id'];
    //Función que busca la categoría con el ID
    function consulta($myid)
    {
        require("../Acceso/global.php");
        $sql="select * from tipo where nombre = '".$myid."'";
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
          $resul[1]
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
        <title>Editar Departamento</title>
    </head>

    <body>
   
        <form method="POST" action="../php/update/tipo-emp.php">
            <input type="hidden" name="old_id" value="<?php echo $id2[0]?>">

            <label for="">Nombre</label>
            <input type="text" id="nom-cat" name="nomcat" value="<?php echo $id2[1]?>">
            <button type="submit" class="btn btn-success">Guardar</button>
        </form>
    </body>
    </html>

    