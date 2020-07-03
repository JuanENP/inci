<?php 
session_start();
  if (($_SESSION["name"]) && ($_SESSION["con"]))
  {
    $nombre=$_SESSION['name'];
    $contra=$_SESSION['con'];
    require("../Acceso/global.php"); 
  }
  else
  {
      header("Location: ../index.php");
      die();
  }
  //obtener el id que se mandó acá
  $encriptado=$_GET['jhgtp09'];
  $id=base64_decode($encriptado);
  $sql="DELETE FROM trabajador WHERE numero_trabajador = '".$id."'";
  $query= mysqli_query($con, $sql);
  if(!$query)
  {
    die("<br>" . "Error: " . mysqli_errno($con) . " : " . mysqli_error($con));
  }
  else
  {
    mysqli_close($con); 
    echo"<script>alert('Eliminado correctamente'); location.href='../ht/trabajadores.php';</script>";
  } 
?>