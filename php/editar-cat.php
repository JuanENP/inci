<?php
session_start();
    $nombre=$_SESSION['name'];
    $contra=$_SESSION['con'];
    
    //si la variable de sesión no existe, entonces no es posible entrar al panel. 
    //Lo redirigimos al index.html para que inicie sesión
    if($nombre==null || $nombre=='')
    {
        header("Location: ../index.html");
        die();
    }
    //obtener el id que se mandó acá
    $id=$_GET['id'];
    //Función que busca la categoría con el ID
    function consulta($myid)
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        require("../Acceso/global.php");
        $sql="select * from categoria where idcategoria = '".$myid."'";
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
          $resul[0],$resul[1]
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
        <title>Editar Categoría</title>
    </head>

    <body>
        <form method="POST" action="../php/update/categoria.php">
            <input type="hidden" name="old_id" value="<?php echo $id2[0]?>">

            <label for="">Categoría</label>
            <input type="text" id="id-cat" name="idcat" value="<?php echo $id2[0]?>">

            <label for="">Nombre</label>
            <input type="text" id="nom-cat" name="nomcat" value="<?php echo $id2[1]?>">
            <button type="submit" class="btn btn-success">Guardar</button>
        </form>
    </body>
    </html>

    