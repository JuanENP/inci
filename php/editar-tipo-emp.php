<?php
session_start();
  if (($_SESSION["name"]) && ($_SESSION["con"]))
  {
    $nombre=$_SESSION['name'];
    $contra=$_SESSION['con'];
  }
  else
  {
    header("Location: ../index.html");
    die();
  }
  //obtener el id que se mandó acá
  $descripcion=$_GET['id'];
  //Función que busca la categoría con el ID
  function consulta($myid)
  {
    $nombre=$_SESSION['name'];
    $contra=$_SESSION['con'];
    require("../Acceso/global.php");
    $sql="select * from tipo where descripcion = '".$myid."'";
    $query= mysqli_query($con, $sql);
    if(!$query)
    {
      die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
    }
    else
    { 
      $resul=mysqli_fetch_array($query);
      //retornar este array
      return
      [
        $resul[0],$resul[1]  
      ];
 
    }
  }
  //guardar el array que retornó la función consulta
  $id2=consulta($descripcion);
?>

<!DOCTYPE html>
  <html lang="es">
  <head>
    <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Editar tipo de empleado</title>
  </head>

  <body>
    <form method="POST" action="../php/update/tipo-emp.php">
      <label for="">Descripción </label>
      <input type="hidden" name="id" value="<?php echo $id2[0]?>">
      <input type="text"   name="descripcion" value="<?php echo $id2[1]?>">
      <button type="submit" class="btn btn-success">Guardar</button>
    </form>
  </body>

</html>