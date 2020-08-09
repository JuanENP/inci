<?php
session_start();
    if (($_SESSION["name"]) && ($_SESSION["con"]))
    {
        $nombre=$_SESSION['name'];
        $contra=$_SESSION['con'];
        require("../../Acceso/global.php"); 
    }
    else
    {
        header("Location: ../index.php");
        die();
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <!-- Estas rutas son correctas, pues recordemos que este archivo se usa en el archivo justificacion que se 
        encuentra en la carpeta php
    -->
    <link rel="stylesheet" href="../../assets/css/alertify.core.css" />
    <link rel="stylesheet" href="../../assets/css/alertify.default.css" />
    <script src="../../assets/js/alertify.min.js"></script>

    <script type="text/javascript">
        function imprime(texto)
        {
            alertify.alert(texto, function(e)
            {
                if(e)
                {
                    window.location.href="../../ht/usuarios.php";
                }
            });
        }
    </script>
</head>
<body>
</body>
</html>
<?php 
    if(empty($_GET['5dF0_sp']))
    {
        echo "<script> imprime('Use el apartado Usuario para ejecutar correctamente este script.' +
        ' No es posible proceder.'); </script>";
        exit();
    }
    //obtener el id que se mandó acá
  $obtener_name=$_GET['5dF0_sp'];
  $nombreUser = base64_decode($obtener_name); // Decode

  $sql="DROP USER $nombreUser@localhost;";
  $query= mysqli_query($con, $sql);
  if(!$query)
  {
    echo "<script> imprime('Algo salió mal, reintente...'); </script>";
    die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
  }
  else
  {
    $sql="select idmail from mail where (trabajador_trabajador = '$nombreUser');";
    $query= mysqli_query($con, $sql);
    $filas=mysqli_num_rows($query);
    if($filas==1)
    {
        $resul=mysqli_fetch_array($query);
        $id=$resul[0];

        $sql="DELETE FROM mail WHERE (idmail = '$id');";
        $query= mysqli_query($con, $sql);
        if(!$query)
        {
            echo "<script> imprime('Algo salió mal, reintente...'); </script>";
            die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
        }
        else
        {
            //Agregar a la bitacora
            echo "<script> imprime('Usuario $nombreUser eliminado correctamente'); </script>";
        }
    }
    else
    {
        //Aquí se va cuando no tenía un correo registrado
        //Agregar a la bitacora
        echo "<script> imprime('Usuario $nombreUser eliminado correctamente'); </script>";
    }
  }
?>