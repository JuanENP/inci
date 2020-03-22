<?php
    require("../Acceso/global.php"); 
    $id=consulta($_GET['id']);

    function consulta($myid)
    {
        $sql="select * from categoria where categoria = '".$myid."'";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
          die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
        }
        else
        {
          $resul=mysqli_fetch_array($query);
          return[
          $resul[0],$resul[1]
          ];
        }
    }
?>

    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Editar Categoría</title>
    </head>

    <body>
        <form action="#">
            <label for="">Categoría</label>
            <input type="text" id="id-cat">

            <label for="">Nombre</label>
            <input type="text" id="nom-cat">
        </form>
    </body>

    </html>