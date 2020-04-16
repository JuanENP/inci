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
  else
  {
      //mandar el nom de usuario
      $_SESSION['name']=$nombre;
      $_SESSION['con']=$contra;
      
  }
?>
<script type="text/javascript">
    function Correcto()
    {
        alert("Eliminado correctamente");
        location.href="../ht/categoria.php";
    }
</script>
<?php
  //obtener el id que se mandó acá
  $id=$_GET['id'];
  
  $nombre=$_SESSION['name'];
  $contra=$_SESSION['con'];
  require("../Acceso/global.php");
  //SIRVE PARA SELECCIONAR EL NOMBRE LA CATEGORIA QUE SE VA A ACTUALIZAR
  $sql="select * from categoria where idcategoria='$id'";
  $query= mysqli_query($con, $sql) or die();
  while($resul=mysqli_fetch_array($query))
  {
    $nombre_cat=$resul[1];
  }

  mysqli_autocommit($con, FALSE);
  if(!(mysqli_query($con,"DELETE FROM categoria WHERE idcategoria = '".$id."'")))
  {
    die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
  }
  else
  {
    $nombre_host= gethostname();
    if(!(mysqli_query($con,"call inserta_bitacora_categoria('Eliminado','-','-','$id ', '$nombre_cat ', '$nombre_host')")))
    {
        mysqli_rollback($con);
        mysqli_autocommit($con, TRUE); 
        echo "alert('Datos incorrectos en bitacora de categoria'); history.back();";
    }
    else
    {
        mysqli_commit($con);
        mysqli_autocommit($con, TRUE);
        echo "<script> Correcto(); </script>";
    }
  }

?>
